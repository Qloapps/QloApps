<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminHotelHeaderImageController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'htl_header_image';
        $this->className = 'HotelHeaderImage';
        $this->bootstrap = true;
        $this->identifier = 'id_header_image';
        $this->lang = true;
        parent::__construct();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected images? This cannot be undone.'),
                'icon' => 'icon-trash',
            ),
        );
    }

    public function initContent()
    {
        if (!$this->ajax) {
            $this->display = 'view';
        }
        parent::initContent();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_title = $this->l('Header Image Configuration');
        unset($this->toolbar_btn['back']);
    }

    public function renderView()
    {
        $this->meta_title = $this->l('Header Image Configuration');
        $mediaType = (int)Tools::getValue('QLO_HEADER_MEDIA_TYPE', (int)(Configuration::get('QLO_HEADER_MEDIA_TYPE') ?: HotelHeaderImage::MEDIA_TYPE_IMAGE));
        $languages = Language::getLanguages(false);
        $defaultLangId = (int)Configuration::get('PS_LANG_DEFAULT');

        $imageItems = HotelHeaderImage::getItems(null, $defaultLangId, true);
        $shopId = (int)$this->context->shop->id;
        foreach ($imageItems as &$item) {
            $item['tag_lines_json'] = json_encode((object)$item['tag_lines']);
            $srcPath = _PS_IMG_DIR_.'hotel_header_media/'.$item['name'];
            $cacheName = 'htl_header_image_mini_'.(int)$item['id_header_image'].'_'.$shopId.'.jpg';
            $item['thumb'] = ImageManager::thumbnail($srcPath, $cacheName, 45, 'jpg', false);
        }
        unset($item);

        $videoItem = HotelHeaderImage::getVideoConfig();
        $videoMimeType = 'video/mp4';
        if ($videoItem) {
            if ($videoItem['source_type'] === 'upload') {
                $ext = strtolower(pathinfo($videoItem['name'], PATHINFO_EXTENSION));
            } else {
                $urlPath = parse_url($videoItem['name'], PHP_URL_PATH);
                $ext = strtolower(pathinfo($urlPath ?: '', PATHINFO_EXTENSION));
            }
            $mimeMap = array('mp4' => 'video/mp4', 'webm' => 'video/webm', 'ogg' => 'video/ogg');
            $videoMimeType = isset($mimeMap[$ext]) ? $mimeMap[$ext] : 'video/mp4';
        }

        $sourceType = Tools::getValue('source_type', $videoItem ? $videoItem['source_type'] : 'upload');
        $videoUrlValue = Tools::getValue(
            'video_url',
            ($videoItem && $videoItem['source_type'] === 'url') ? $videoItem['name'] : ''
        );
        $showVideoUrlPreview = ($sourceType === 'url' && $videoUrlValue && Validate::isAbsoluteUrl($videoUrlValue));

        Media::addJsDef(array(
            'qloHmCurrentIndex' => self::$currentIndex,
            'qloHmToken' => $this->token,
            'qloHmMediaType' => $mediaType,
            'qloHmMediaTypeImage' => HotelHeaderImage::MEDIA_TYPE_IMAGE,
            'qloHmMediaTypeVideo' => HotelHeaderImage::MEDIA_TYPE_VIDEO,
            'qloHmMaxUpload' => Tools::getMaxUploadSize((int)Configuration::get('PS_LIMIT_UPLOAD_IMAGE_VALUE') * 1024 * 1024),
            'qloHmMaxVideoUpload' => Tools::getMaxUploadSize(),
            'qloHmDefaultLangId' => $defaultLangId,
            'qloHmI18n' => array(
                'noFileSelected' => $this->l('Please select at least one image file.'),
                'deleteFailed' => $this->l('Delete failed.'),
                'requestFailed' => $this->l('Request failed.'),
                'imageUploadedSuccess' => $this->l('Image uploaded successfully.'),
                'imageUpdatedSuccess' => $this->l('Image updated successfully.'),
                'uploadFailed' => $this->l('Upload failed.'),
                'updateFailed' => $this->l('Update failed.'),
                'editLabel' => $this->l('Edit'),
                'deleteImageLabel' => $this->l('Delete this image'),
                'fileTooLarge' => $this->l('File exceeds the maximum allowed upload size.'),
            ),
        ));

        $this->tpl_view_vars = array(
            'mediaType' => $mediaType,
            'imageItems' => $imageItems,
            'videoItem' => $videoItem,
            'videoMimeType' => $videoMimeType,
            'sourceType' => $sourceType,
            'videoUrlValue' => $videoUrlValue,
            'showVideoUrlPreview' => $showVideoUrlPreview,
            'config' => array(
                'QLO_HEADER_MEDIA_TYPE' => $mediaType,
                'QLO_HEADER_SLIDER_NAV_TYPE' => (int)Tools::getValue('QLO_HEADER_SLIDER_NAV_TYPE', (int)(Configuration::get('QLO_HEADER_SLIDER_NAV_TYPE') ?: HotelHeaderImage::NAV_TYPE_DOTS)),
                'QLO_HEADER_SLIDER_AUTO_PLAY' => (int)Tools::getValue('QLO_HEADER_SLIDER_AUTO_PLAY', (int)Configuration::get('QLO_HEADER_SLIDER_AUTO_PLAY')),
                'QLO_HEADER_SLIDER_INTERVAL' => Tools::getValue('QLO_HEADER_SLIDER_INTERVAL', (int)Configuration::get('QLO_HEADER_SLIDER_INTERVAL') ?: 5000),
                'QLO_HEADER_SLIDER_ANIM_TYPE' => (int)Tools::getValue('QLO_HEADER_SLIDER_ANIM_TYPE', (int)(Configuration::get('QLO_HEADER_SLIDER_ANIM_TYPE') ?: HotelHeaderImage::ANIMATION_TYPE_SLIDE)),
                'QLO_HEADER_CONTENT_ALIGN' => (int)Tools::getValue('QLO_HEADER_CONTENT_ALIGN', (int)(Configuration::get('QLO_HEADER_CONTENT_ALIGN') ?: HotelHeaderImage::CONTENT_ALIGN_CENTER)),
            ),
            'languages' => $languages,
            'defaultLangId' => $defaultLangId,
            'imgBaseUrl' => $this->context->link->getMediaLink(_PS_IMG_.'hotel_header_media/'),
            'maxUpload' => Tools::formatBytes(Tools::getMaxUploadSize()),
            'maxImageUpload' => Tools::formatBytes(Tools::getMaxUploadSize((int)Configuration::get('PS_LIMIT_UPLOAD_IMAGE_VALUE') * 1024 * 1024)),
        );

        return parent::renderView();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitHeaderMedia')) {
            $this->processSaveSettings();
        }
        parent::postProcess();
    }

    protected function processBulkDelete()
    {
        $ids = Tools::getValue($this->table.'Box', array());
        $activeImages = HotelHeaderImage::getItems(1);
        $activeIds = array_column($activeImages, 'id_header_image');
        $activeToDelete = array_intersect(array_map('intval', (array)$ids), array_map('intval', $activeIds));

        if (count($activeIds) - count($activeToDelete) < 1) {
            $this->errors[] = $this->l('At least one active image is required.');
            return;
        }

        parent::processBulkDelete();
    }

    protected function processSaveSettings()
    {
        $mediaType = (int)Tools::getValue('QLO_HEADER_MEDIA_TYPE', HotelHeaderImage::MEDIA_TYPE_IMAGE);
        $previousMediaType = (int)Configuration::get('QLO_HEADER_MEDIA_TYPE');

        if (!in_array($mediaType, array(HotelHeaderImage::MEDIA_TYPE_IMAGE, HotelHeaderImage::MEDIA_TYPE_VIDEO))) {
            $this->errors[] = $this->l('Invalid media type selected.');
            return;
        }

        $sourceType = Tools::getValue('source_type', 'upload');
        $existingVideo = HotelHeaderImage::getVideoConfig();
        $hasMatchingExistingVideo = ($existingVideo && $existingVideo['source_type'] === $sourceType);
        $hasNewVideoFile = isset($_FILES['header_video_file']) && !empty($_FILES['header_video_file']['size']);
        $hasNewVideoUrl = ($sourceType === 'url' && trim(Tools::getValue('video_url', '')) !== '');

        if ($mediaType === HotelHeaderImage::MEDIA_TYPE_VIDEO && !$hasMatchingExistingVideo && !$hasNewVideoFile && !$hasNewVideoUrl) {
            $this->errors[] = $this->l('Please upload or link a video before switching the header to Video mode.');
            return;
        }
        if ($mediaType === HotelHeaderImage::MEDIA_TYPE_IMAGE && !HotelHeaderImage::getItems(1)) {
            $this->errors[] = $this->l('Please add at least one active image before switching the header to Image mode.');
            return;
        }
        $autoPlay = 0;
        $interval = 0;
        $navType = HotelHeaderImage::NAV_TYPE_DOTS;
        $animType = HotelHeaderImage::ANIMATION_TYPE_SLIDE;
        $contentAlign = HotelHeaderImage::CONTENT_ALIGN_CENTER;

        if ($mediaType === HotelHeaderImage::MEDIA_TYPE_IMAGE) {
            $autoPlay = (int)(bool)Tools::getValue('QLO_HEADER_SLIDER_AUTO_PLAY', 1);
            if ($autoPlay) {
                $interval = Tools::getValue('QLO_HEADER_SLIDER_INTERVAL', 5000);
                if (!Validate::isUnsignedInt($interval) || (int)$interval < 500) {
                    $this->errors[] = $this->l('Auto Slide Interval must be at least 500 milliseconds.');
                    return;
                }
                $interval = (int)$interval;
            }

            $navType = (int)Tools::getValue('QLO_HEADER_SLIDER_NAV_TYPE', HotelHeaderImage::NAV_TYPE_DOTS);
            if (!in_array($navType, array(HotelHeaderImage::NAV_TYPE_DOTS, HotelHeaderImage::NAV_TYPE_ARROWS, HotelHeaderImage::NAV_TYPE_BOTH))) {
                $navType = HotelHeaderImage::NAV_TYPE_DOTS;
            }
            $animType = (int)Tools::getValue('QLO_HEADER_SLIDER_ANIM_TYPE', HotelHeaderImage::ANIMATION_TYPE_SLIDE);
            if (!in_array($animType, array(HotelHeaderImage::ANIMATION_TYPE_SLIDE, HotelHeaderImage::ANIMATION_TYPE_FADE, HotelHeaderImage::ANIMATION_TYPE_ZOOM, HotelHeaderImage::ANIMATION_TYPE_BLUR))) {
                $animType = HotelHeaderImage::ANIMATION_TYPE_SLIDE;
            }
            $contentAlign = (int)Tools::getValue('QLO_HEADER_CONTENT_ALIGN', HotelHeaderImage::CONTENT_ALIGN_CENTER);
            if (!in_array($contentAlign, array(HotelHeaderImage::CONTENT_ALIGN_LEFT, HotelHeaderImage::CONTENT_ALIGN_CENTER, HotelHeaderImage::CONTENT_ALIGN_RIGHT))) {
                $contentAlign = HotelHeaderImage::CONTENT_ALIGN_CENTER;
            }
        }

        if ($mediaType === HotelHeaderImage::MEDIA_TYPE_VIDEO) {
            $imagesToDrop = array();
            if ($previousMediaType === HotelHeaderImage::MEDIA_TYPE_IMAGE) {
                $imagesToDrop = HotelHeaderImage::getItems(null);
                array_shift($imagesToDrop);
            }
            if ($imagesToDrop && Tools::getValue('confirm_delete_images') !== '1') {
                $this->errors[] = $this->l('Switching to Video will delete all images except the first one. Please confirm this action.');
                return;
            }

            $this->processSaveVideo();

            if (!count($this->errors)) {
                foreach ($imagesToDrop as $imgData) {
                    $obj = new HotelHeaderImage((int)$imgData['id_header_image']);
                    if (Validate::isLoadedObject($obj)) {
                        $obj->delete();
                    }
                }
            }
        }

        if (!count($this->errors)) {
            Configuration::updateValue('QLO_HEADER_MEDIA_TYPE', $mediaType);

            if ($mediaType === HotelHeaderImage::MEDIA_TYPE_IMAGE) {
                Configuration::updateValue('QLO_HEADER_CONTENT_ALIGN', $contentAlign);
                Configuration::updateValue('QLO_HEADER_SLIDER_NAV_TYPE', $navType);
                Configuration::updateValue('QLO_HEADER_SLIDER_AUTO_PLAY', $autoPlay);
                if ($autoPlay) {
                    Configuration::updateValue('QLO_HEADER_SLIDER_INTERVAL', $interval);
                    Configuration::updateValue('QLO_HEADER_SLIDER_ANIM_TYPE', $animType);
                }
            }
            Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
        }
    }

    protected function processSaveVideo()
    {
        $sourceType = Tools::getValue('source_type', 'upload');
        $videoUrl = trim(Tools::getValue('video_url', ''));
        $file = isset($_FILES['header_video_file']) ? $_FILES['header_video_file'] : null;
        $hasNewFile = ($file && isset($file['error']) && $file['error'] === UPLOAD_ERR_OK && $file['size'] > 0);
        $hasNewUrl = ($sourceType === 'url' && $videoUrl !== '');

        if ($sourceType === 'url' && $videoUrl === '') {
            $this->errors[] = $this->l('Please enter a video URL.');
            return;
        }
        if ($hasNewUrl && !Validate::isAbsoluteUrl($videoUrl)) {
            $this->errors[] = $this->l('Please enter a valid video URL.');
            return;
        }
        if ($hasNewFile) {
            $allowedExts = array('mp4', 'webm', 'ogg');
            $allowedMimes = array('video/mp4', 'video/webm', 'video/ogg', 'video/x-matroska');
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExts)) {
                $this->errors[] = $this->l('Only .mp4, .webm, and .ogg video formats are allowed.');
                return;
            }
            if (filesize($file['tmp_name']) > Tools::getMaxUploadSize()) {
                $this->errors[] = $this->l('Video file exceeds the maximum allowed upload size.');
                return;
            }
            if (!function_exists('finfo_open')) {
                $this->errors[] = $this->l('Server cannot verify file type. Please enable the fileinfo PHP extension.');
                return;
            }
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $detectedMime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            if (!in_array($detectedMime, $allowedMimes)) {
                $this->errors[] = $this->l('Invalid video file type detected.');
                return;
            }
        }

        if ($hasNewUrl) {
            HotelHeaderImage::deleteVideoConfig();
            HotelHeaderImage::saveVideoConfig('url', $videoUrl);
        } elseif ($hasNewFile) {
            if (!HotelHeaderImage::createMediaDirectory()) {
                $this->errors[] = $this->l('Could not create the media directory.');
                return;
            }
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            do {
                $uniqueName = bin2hex(random_bytes(16)).'.'.$ext;
            } while (file_exists(_PS_IMG_DIR_.'hotel_header_media/'.$uniqueName));
            if (!move_uploaded_file($file['tmp_name'], _PS_IMG_DIR_.'hotel_header_media/'.$uniqueName)) {
                $this->errors[] = $this->l('Failed to save video file.');
                return;
            }
            HotelHeaderImage::deleteVideoConfig();
            HotelHeaderImage::saveVideoConfig('upload', $uniqueName);
        }
    }

    public function ajaxProcessUploadImage()
    {
        $response = array('errors' => array(), 'success' => false);
        $file = isset($_FILES['header_image_file']) ? $_FILES['header_image_file'] : null;

        if (!$file || !$file['size']) {
            $response['errors'][] = $this->l('No file received.');
            $this->ajaxDie(json_encode($response));
        }

        if ($error = ImageManager::validateUpload($file, Tools::getMaxUploadSize((int)Configuration::get('PS_LIMIT_UPLOAD_IMAGE_VALUE') * 1024 * 1024))) {
            $response['errors'][] = $error;
            $this->ajaxDie(json_encode($response));
        }

        if (!HotelHeaderImage::createMediaDirectory()) {
            $response['errors'][] = $this->l('Could not create the media directory.');
            $this->ajaxDie(json_encode($response));
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $isGif = ($ext === 'gif');
        $outExt = $isGif ? 'gif' : 'jpg';
        do {
            $uniqueName = bin2hex(random_bytes(16)).'.'.$outExt;
        } while (file_exists(_PS_IMG_DIR_.'hotel_header_media/'.$uniqueName));

        $destPath = _PS_IMG_DIR_.'hotel_header_media/'.$uniqueName;
        $saved = $isGif ? (bool)move_uploaded_file($file['tmp_name'], $destPath) : (bool)ImageManager::resize($file['tmp_name'], $destPath);

        if (!$saved) {
            $response['errors'][] = $this->l('Failed to save image file.');
            $this->ajaxDie(json_encode($response));
        }

        $tagLineByLang = array();
        foreach (Language::getLanguages(false) as $lang) {
            $tagLineByLang[$lang['id_lang']] = trim(Tools::getValue('tag_line_'.$lang['id_lang'], ''));
        }
        foreach ($tagLineByLang as $tagLineValue) {
            if (!Validate::isGenericName($tagLineValue)) {
                if (file_exists($destPath)) {
                    unlink($destPath);
                }
                $response['errors'][] = $this->l('Invalid tag line. Characters < > = { } are not allowed.');
                $this->ajaxDie(json_encode($response));
            }
        }

        $tagLineColor = Tools::getValue('tag_line_color', '#ffffff');
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $tagLineColor)) {
            $response['errors'][] = $this->l('Invalid tag line color. Use a 6-digit hex value (e.g. #ffffff).');
            $this->ajaxDie(json_encode($response));
        }
        $tagLineFontSize = (int)Tools::getValue('tag_line_font_size', 16);
        if ($tagLineFontSize < 8 || $tagLineFontSize > 72) {
            $response['errors'][] = $this->l('Tag line font size must be between 8 and 72 pixels.');
            $this->ajaxDie(json_encode($response));
        }
        $tagLineFontWeight = Tools::getValue('tag_line_font_weight', '400');
        if (!in_array($tagLineFontWeight, array('300', '400', '600', '700'))) {
            $response['errors'][] = $this->l('Invalid tag line font weight.');
            $this->ajaxDie(json_encode($response));
        }

        $objImage = new HotelHeaderImage();
        $objImage->name = $uniqueName;
        $objImage->position = $objImage->getHigherPosition();
        $objImage->active = (int)(bool)Tools::getValue('active', 1);
        $objImage->show_hotel_chain_name = (int)(bool)Tools::getValue('show_hotel_chain_name', 1);
        $objImage->tag_line = $tagLineByLang;
        $objImage->tag_line_color = $tagLineColor;
        $objImage->tag_line_font_size = $tagLineFontSize;
        $objImage->tag_line_font_weight = $tagLineFontWeight;

        if (!$objImage->save()) {
            if (file_exists($destPath)) {
                unlink($destPath);
            }
            $response['errors'][] = $this->l('Failed to save image record.');
            $this->ajaxDie(json_encode($response));
        }

        $defaultLangId = (int)Configuration::get('PS_LANG_DEFAULT');
        $shopId = (int)$this->context->shop->id;
        $cacheName = 'htl_header_image_mini_'.(int)$objImage->id.'_'.$shopId.'.jpg';
        $thumb = ImageManager::thumbnail($destPath, $cacheName, 45, 'jpg', true, true);
        $this->context->smarty->assign(array(
            'img' => array(
                'id_header_image' => (int)$objImage->id,
                'name' => $uniqueName,
                'thumb' => $thumb,
                'tag_line' => isset($tagLineByLang[$defaultLangId]) ? $tagLineByLang[$defaultLangId] : '',
                'tag_lines_json' => json_encode((object)$tagLineByLang),
                'tag_line_color' => $tagLineColor,
                'tag_line_font_size' => $tagLineFontSize,
                'tag_line_font_weight' => $tagLineFontWeight,
                'active' => (int)$objImage->active,
                'show_hotel_chain_name' => (int)$objImage->show_hotel_chain_name,
            ),
            'position' => count(HotelHeaderImage::getItems(null)),
            'current' => self::$currentIndex,
            'token' => $this->token,
            'imgBaseUrl' => $this->context->link->getMediaLink(_PS_IMG_.'hotel_header_media/'),
        ));

        $response['success'] = true;
        $response['data']['image_row'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/hotel_header_image/_partials/htl-header-image-row.tpl'
        );
        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessEditImage()
    {
        $response = array('errors' => array(), 'success' => false);
        $id = (int)Tools::getValue('id_header_image');

        if (!$id) {
            $response['errors'][] = $this->l('Invalid item ID.');
            $this->ajaxDie(json_encode($response));
        }

        $objImage = new HotelHeaderImage($id);
        if (!Validate::isLoadedObject($objImage)) {
            $response['errors'][] = $this->l('Image not found.');
            $this->ajaxDie(json_encode($response));
        }

        $tagLineByLang = array();
        foreach (Language::getLanguages(false) as $lang) {
            $tagLineByLang[$lang['id_lang']] = trim(Tools::getValue('tag_line_'.$lang['id_lang'], ''));
        }

        $activeVal = Tools::getValue('active');
        if ($activeVal !== false) {
            $newActive = (int)(bool)$activeVal;
            if ($newActive === 0 && (int)$objImage->active === 1) {
                $activeImages = HotelHeaderImage::getItems(1);
                if (is_array($activeImages) && count($activeImages) <= 1) {
                    $response['errors'][] = $this->l('At least one image must remain active.');
                    $this->ajaxDie(json_encode($response));
                }
            }
            $objImage->active = $newActive;
        }

        $showHotelChainNameVal = Tools::getValue('show_hotel_chain_name');
        if ($showHotelChainNameVal !== false) {
            $objImage->show_hotel_chain_name = (int)(bool)$showHotelChainNameVal;
        }

        $tagLineColor = Tools::getValue('tag_line_color', $objImage->tag_line_color);
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $tagLineColor)) {
            $response['errors'][] = $this->l('Invalid tag line color. Use a 6-digit hex value (e.g. #ffffff).');
            $this->ajaxDie(json_encode($response));
        }
        $tagLineFontSize = (int)Tools::getValue('tag_line_font_size', $objImage->tag_line_font_size);
        if ($tagLineFontSize < 8 || $tagLineFontSize > 72) {
            $response['errors'][] = $this->l('Tag line font size must be between 8 and 72 pixels.');
            $this->ajaxDie(json_encode($response));
        }
        $tagLineFontWeight = Tools::getValue('tag_line_font_weight', $objImage->tag_line_font_weight);
        if (!in_array($tagLineFontWeight, array('300', '400', '600', '700'))) {
            $response['errors'][] = $this->l('Invalid tag line font weight.');
            $this->ajaxDie(json_encode($response));
        }

        $replacementFile = isset($_FILES['header_image_file']) ? $_FILES['header_image_file'] : null;
        $hasReplacementFile = ($replacementFile && isset($replacementFile['error']) && $replacementFile['error'] === UPLOAD_ERR_OK && $replacementFile['size'] > 0);
        $newImageName = null;

        if ($hasReplacementFile) {
            if ($error = ImageManager::validateUpload($replacementFile, Tools::getMaxUploadSize((int)Configuration::get('PS_LIMIT_UPLOAD_IMAGE_VALUE') * 1024 * 1024))) {
                $response['errors'][] = $error;
                $this->ajaxDie(json_encode($response));
            }
            if (!HotelHeaderImage::createMediaDirectory()) {
                $response['errors'][] = $this->l('Could not create the media directory.');
                $this->ajaxDie(json_encode($response));
            }

            $ext = strtolower(pathinfo($replacementFile['name'], PATHINFO_EXTENSION));
            $isGif = ($ext === 'gif');
            $outExt = $isGif ? 'gif' : 'jpg';
            do {
                $newImageName = bin2hex(random_bytes(16)).'.'.$outExt;
            } while (file_exists(_PS_IMG_DIR_.'hotel_header_media/'.$newImageName));

            $destPath = _PS_IMG_DIR_.'hotel_header_media/'.$newImageName;
            $saved = $isGif ? (bool)move_uploaded_file($replacementFile['tmp_name'], $destPath) : (bool)ImageManager::resize($replacementFile['tmp_name'], $destPath);

            if (!$saved) {
                $response['errors'][] = $this->l('Failed to save image file.');
                $this->ajaxDie(json_encode($response));
            }
        }

        $oldImageName = $objImage->name;
        $objImage->tag_line = $tagLineByLang;
        $objImage->tag_line_color = $tagLineColor;
        $objImage->tag_line_font_size = $tagLineFontSize;
        $objImage->tag_line_font_weight = $tagLineFontWeight;
        if ($newImageName) {
            $objImage->name = $newImageName;
        }
        if (!$objImage->save()) {
            if ($newImageName && file_exists($destPath)) {
                unlink($destPath);
            }
            $response['errors'][] = $this->l('Failed to update image.');
            $this->ajaxDie(json_encode($response));
        }

        if ($newImageName) {
            $oldPath = _PS_IMG_DIR_.'hotel_header_media/'.$oldImageName;
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $defaultLangId = (int)Configuration::get('PS_LANG_DEFAULT');
        $response['success'] = true;
        $response['active'] = (int)$objImage->active;
        $response['show_hotel_chain_name'] = (int)$objImage->show_hotel_chain_name;
        $response['confirmations'] = $this->l('Image updated successfully.');
        $response['tag_line'] = isset($tagLineByLang[$defaultLangId]) ? $tagLineByLang[$defaultLangId] : '';
        $response['tag_lines_json'] = json_encode((object)$tagLineByLang);
        $response['tag_line_color'] = $tagLineColor;
        $response['tag_line_font_size'] = $tagLineFontSize;
        $response['tag_line_font_weight'] = $tagLineFontWeight;

        if ($newImageName) {
            $shopId = (int)$this->context->shop->id;
            $srcPath = _PS_IMG_DIR_.'hotel_header_media/'.$newImageName;
            $cacheName = 'htl_header_image_mini_'.$id.'_'.$shopId.'.jpg';
            $response['thumb'] = ImageManager::thumbnail($srcPath, $cacheName, 45, 'jpg', true, true);
            $response['thumb_src'] = $this->context->link->getMediaLink(_PS_IMG_.'hotel_header_media/'.$newImageName).'?'.time();
        }

        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessDeleteMedia()
    {
        $response = array('errors' => array(), 'success' => false);
        $id = (int)Tools::getValue('id_header_image');

        if (!$id) {
            $response['errors'][] = $this->l('Invalid item ID.');
            $this->ajaxDie(json_encode($response));
        }

        $objImage = new HotelHeaderImage($id);
        if (!Validate::isLoadedObject($objImage)) {
            $response['errors'][] = $this->l('Image not found.');
            $this->ajaxDie(json_encode($response));
        }

        if ($objImage->active) {
            $activeImages = HotelHeaderImage::getItems(1);
            if (is_array($activeImages) && count($activeImages) <= 1) {
                $response['errors'][] = $this->l('At least one active image is required');
                $this->ajaxDie(json_encode($response));
            }
        }

        if (!$objImage->delete()) {
            $response['errors'][] = $this->l('Unable to delete image.');
            $this->ajaxDie(json_encode($response));
        }

        $response['success'] = true;
        $response['confirmations'] = $this->l('Image deleted successfully.');
        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessDeleteVideo()
    {
        $response = array('errors' => array(), 'success' => false);

        if (!HotelHeaderImage::getVideoConfig()) {
            $response['errors'][] = $this->l('No video is currently set.');
            $this->ajaxDie(json_encode($response));
        }

        HotelHeaderImage::deleteVideoConfig();
        $response['success'] = true;
        $response['confirmations'] = $this->l('Video deleted successfully.');
        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessToggleImageActive()
    {
        $response = array('errors' => array(), 'success' => false);
        $id = (int)Tools::getValue('id_header_image');
        $active = (int)(bool)Tools::getValue('active');

        if (!$id) {
            $response['errors'][] = $this->l('Invalid item ID.');
            $this->ajaxDie(json_encode($response));
        }

        $objImage = new HotelHeaderImage($id);
        if (!Validate::isLoadedObject($objImage)) {
            $response['errors'][] = $this->l('Image not found.');
            $this->ajaxDie(json_encode($response));
        }

        if ($active === 0) {
            $activeImages = HotelHeaderImage::getItems(1);
            if (is_array($activeImages) && count($activeImages) <= 1) {
                $response['errors'][] = $this->l('At least one image must remain active.');
                $this->ajaxDie(json_encode($response));
            }
        }

        $objImage->active = $active;
        if (!$objImage->save()) {
            $response['errors'][] = $this->l('Unable to update active status.');
            $this->ajaxDie(json_encode($response));
        }

        $response['success'] = true;
        $response['confirmations'] = $this->l('The status has been successfully updated.');
        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessToggleImageHotelName()
    {
        $response = array('errors' => array(), 'success' => false);
        $id = (int)Tools::getValue('id_header_image');
        $showHotelChainName = (int)(bool)Tools::getValue('show_hotel_chain_name');

        if (!$id) {
            $response['errors'][] = $this->l('Invalid item ID.');
            $this->ajaxDie(json_encode($response));
        }

        $objImage = new HotelHeaderImage($id);
        if (!Validate::isLoadedObject($objImage)) {
            $response['errors'][] = $this->l('Image not found.');
            $this->ajaxDie(json_encode($response));
        }

        $objImage->show_hotel_chain_name = $showHotelChainName;
        if (!$objImage->save()) {
            $response['errors'][] = $this->l('Unable to update hotel name status.');
            $this->ajaxDie(json_encode($response));
        }

        $response['success'] = true;
        $response['confirmations'] = $this->l('The status has been successfully updated.');
        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessSaveImagePositions()
    {
        $ids = Tools::getValue('image_ids', array());
        if (!is_array($ids)) {
            $this->ajaxDie(json_encode(array('success' => false)));
        }

        foreach ($ids as $position => $id) {
            $objImage = new HotelHeaderImage((int)$id);
            if (!Validate::isLoadedObject($objImage)) {
                $this->ajaxDie(json_encode(array('success' => false, 'errors' => array($this->l('One or more image IDs are invalid.')))));
            }
            $objImage->position = (int)$position;
            $objImage->update();
        }
        $this->ajaxDie(json_encode(array(
            'success' => true,
            'confirmations' => $this->l('The selected images have successfully been moved.'),
        )));
    }

    public function ajaxProcessBulkUpdateImages()
    {
        $response = array('errors' => array(), 'success' => false);

        $ids = array_values(array_unique(array_filter(array_map('intval', (array)Tools::getValue('id_header_image', array())))));
        if (!$ids) {
            $response['errors'][] = $this->l('No images selected.');
            $this->ajaxDie(json_encode($response));
        }

        $activeVal = Tools::getValue('active', '');
        if ($activeVal !== '' && !in_array($activeVal, array('0', '1'))) {
            $response['errors'][] = $this->l('Invalid status selected.');
            $this->ajaxDie(json_encode($response));
        }

        if ($activeVal === '0') {
            $activeImages = HotelHeaderImage::getItems(1);
            $activeIds = array_map('intval', array_column($activeImages, 'id_header_image'));
            $activeToDisable = array_intersect($ids, $activeIds);
            if (count($activeIds) - count($activeToDisable) < 1) {
                $response['errors'][] = $this->l('At least one image must remain active.');
                $this->ajaxDie(json_encode($response));
            }
        }

        $showHotelChainNameVal = Tools::getValue('show_hotel_chain_name', '');
        if ($showHotelChainNameVal !== '' && !in_array($showHotelChainNameVal, array('0', '1'))) {
            $response['errors'][] = $this->l('Invalid hotel name status selected.');
            $this->ajaxDie(json_encode($response));
        }

        $updateTagline = (bool)Tools::getValue('update_tagline', false);
        $tagLineByLang = array();
        if ($updateTagline) {
            foreach (Language::getLanguages(false) as $lang) {
                $tagLineByLang[$lang['id_lang']] = trim(Tools::getValue('tag_line_'.$lang['id_lang'], ''));
            }
            foreach ($tagLineByLang as $tagLineValue) {
                if (!Validate::isGenericName($tagLineValue)) {
                    $response['errors'][] = $this->l('Invalid tag line. Characters < > = { } are not allowed.');
                    $this->ajaxDie(json_encode($response));
                }
            }
        }

        $tagLineColor = trim(Tools::getValue('tag_line_color', ''));
        if ($tagLineColor !== '' && !preg_match('/^#[0-9a-fA-F]{6}$/', $tagLineColor)) {
            $response['errors'][] = $this->l('Invalid tag line color. Use a 6-digit hex value (e.g. #ffffff).');
            $this->ajaxDie(json_encode($response));
        }

        $tagLineFontSize = (int)Tools::getValue('tag_line_font_size', 0);
        if ($tagLineFontSize !== 0 && ($tagLineFontSize < 8 || $tagLineFontSize > 72)) {
            $response['errors'][] = $this->l('Tag line font size must be between 8 and 72 pixels.');
            $this->ajaxDie(json_encode($response));
        }

        $tagLineFontWeight = Tools::getValue('tag_line_font_weight', '');
        if ($tagLineFontWeight !== '' && !in_array($tagLineFontWeight, array('300', '400', '600', '700'))) {
            $response['errors'][] = $this->l('Invalid tag line font weight.');
            $this->ajaxDie(json_encode($response));
        }

        $defaultLangId = (int)Configuration::get('PS_LANG_DEFAULT');
        $updatedRows = array();
        foreach ($ids as $id) {
            $objImage = new HotelHeaderImage($id);
            if (!Validate::isLoadedObject($objImage)) {
                continue;
            }

            if ($activeVal !== '') {
                $objImage->active = (int)$activeVal;
            }
            if ($showHotelChainNameVal !== '') {
                $objImage->show_hotel_chain_name = (int)$showHotelChainNameVal;
            }
            if ($updateTagline) {
                $objImage->tag_line = $tagLineByLang;
            }
            if ($tagLineColor !== '') {
                $objImage->tag_line_color = $tagLineColor;
            }
            if ($tagLineFontSize !== 0) {
                $objImage->tag_line_font_size = $tagLineFontSize;
            }
            if ($tagLineFontWeight !== '') {
                $objImage->tag_line_font_weight = $tagLineFontWeight;
            }
            $objImage->save();

            $currentTagLines = is_array($objImage->tag_line) ? $objImage->tag_line : array();
            $updatedRows[] = array(
                'id' => (int)$objImage->id,
                'active' => (int)$objImage->active,
                'show_hotel_chain_name' => (int)$objImage->show_hotel_chain_name,
                'tag_line' => isset($currentTagLines[$defaultLangId]) ? $currentTagLines[$defaultLangId] : '',
                'tag_lines_json' => json_encode((object)$currentTagLines),
                'tag_line_color' => $objImage->tag_line_color,
                'tag_line_font_size' => $objImage->tag_line_font_size,
                'tag_line_font_weight' => $objImage->tag_line_font_weight,
            );
        }

        if (!$updatedRows) {
            $response['errors'][] = $this->l('No valid images were updated.');
            $this->ajaxDie(json_encode($response));
        }

        $response['success'] = true;
        $response['data']['rows'] = $updatedRows;
        $response['confirmations'] = $this->l('Selected images updated successfully.');
        $this->ajaxDie(json_encode($response));
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryPlugin('tablednd');
        $this->addJqueryPlugin('colorpicker');
        $adminJsPath = _PS_MODULE_DIR_.'hotelreservationsystem/views/js/admin/qhrs_hotel_header_image_admin.js';
        $this->addJS(_MODULE_DIR_.'hotelreservationsystem/views/js/admin/qhrs_hotel_header_image_admin.js?'.@filemtime($adminJsPath));
        $this->addCSS(_MODULE_DIR_.'hotelreservationsystem/views/css/HotelReservationAdmin.css');
        $this->addCSS(_MODULE_DIR_.'hotelreservationsystem/views/css/admin/qhrs_header_media.css');
    }
}
