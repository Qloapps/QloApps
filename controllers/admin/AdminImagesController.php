<?php
/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @property ImageType $object
 */
class AdminImagesControllerCore extends AdminController
{
    protected $start_time = 0;
    protected $max_execution_time = 7200;
    protected $display_move;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'image_type';
        $this->className = 'ImageType';
        $this->lang = false;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->fields_list = array(
            'id_image_type' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'align' => 'left'
            ),
            'width' => array(
                'title' => $this->l('Width'),
                'suffix' => ' px'
                ),
            'height' => array(
                'title' => $this->l('Height'),
                'suffix' => ' px'
            ),
            'products' => array(
                'title' => $this->l('Room Types/Products'),
                'align' => 'center',
                'type' => 'bool',
                'active' => 'products',
                'ajax' => true,
                'orderby' => false
            ),
            'categories' => array(
                'title' => $this->l('Categories'),
                'align' => 'center',
                'type' => 'bool',
                'active' => 'categories',
                'ajax' => true,
                'orderby' => false
            ),
            'hotels' => array(
                'title' => $this->l('Hotels'),
                'align' => 'center',
                'type' => 'bool',
                'active' => 'hotels',
                'ajax' => true,
                'orderby' => false
            ),
        );

        // Scenes tab has been removed by default from the installation, but may still exists in updates
        if (Tab::getIdFromClassName('AdminScenes')) {
            $this->fields_list['scenes'] = array('title' => $this->l('Scenes'), 'align' => 'center', 'type' => 'bool', 'orderby' => false);
        }

        // No need to display the old image system migration tool except if product images are in _PS_PROD_IMG_DIR_
        $this->display_move = false;
        $dir = _PS_PROD_IMG_DIR_;
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false && $this->display_move == false) {
                    if (!is_dir($dir.DIRECTORY_SEPARATOR.$file) && $file[0] != '.' && is_numeric($file[0])) {
                        $this->display_move = true;
                    }
                }
                closedir($dh);
            }
        }

        $this->fields_options = array(
            'images' => array(
                'title' =>    $this->l('Images generation options'),
                'icon' =>    'icon-picture',
                'top' => '',
                'bottom' => '',
                'description' => $this->l('JPEG images have a small file size and standard quality. PNG images have a larger file size, a higher quality and support transparency. Note that in all cases the image files will have the .jpg extension.').'
					<br /><br />'.$this->l('WARNING: This feature may not be compatible with your theme, or with some of your modules. In particular, PNG mode is not compatible with the Watermark module. If you encounter any issues, turn it off by selecting "Use JPEG".'),
                'fields' =>    array(
                    'PS_IMAGE_QUALITY' => array(
                        'title' => $this->l('Image format'),
                        'show' => true,
                        'required' => true,
                        'type' => 'radio',
                        'choices' => array('jpg' => $this->l('Use JPEG.'), 'png' => $this->l('Use PNG only if the base image is in PNG format.'), 'png_all' => $this->l('Use PNG for all images.'))
                    ),
                    'PS_JPEG_QUALITY' => array(
                        'title' => $this->l('JPEG compression'),
                        'hint' => $this->l('Ranges from 0 (worst quality, smallest file) to 100 (best quality, biggest file).').' '.$this->l('Recommended: 90.'),
                        'validation' => 'isUnsignedId',
                        'required' => true,
                        'cast' => 'intval',
                        'type' => 'text'
                    ),
                    'PS_PNG_QUALITY' => array(
                         'title' => $this->l('PNG compression'),
                         'hint' => $this->l('PNG compression is lossless: unlike JPG, you do not lose image quality with a high compression ratio. However, photographs will compress very badly.').' '.$this->l('Ranges from 0 (biggest file) to 9 (smallest file, slowest decompression).').' '.$this->l('Recommended: 7.'),
                         'validation' => 'isUnsignedId',
                         'required' => true,
                         'cast' => 'intval',
                         'type' => 'text'
                     ),
                    'PS_IMAGE_GENERATION_METHOD' => array(
                        'title' => $this->l('Generate images based on one side of the source image'),
                        'validation' => 'isUnsignedId',
                        'required' => false,
                        'cast' => 'intval',
                        'type' => 'select',
                        'list' => array(
                            array(
                                'id' => '0',
                                'name' => $this->l('Automatic (longest side)')
                            ),
                            array(
                                'id' => '1',
                                'name' => $this->l('Width')
                            ),
                            array(
                                'id' => '2',
                                'name' => $this->l('Height')
                            )
                        ),
                        'identifier' => 'id',
                        'visibility' => Shop::CONTEXT_ALL
                    ),
                    'PS_PRODUCT_PICTURE_MAX_SIZE' => array(
                        'title' => $this->l('Maximum file size of product customization pictures'),
                        'hint' => $this->l('The maximum file size of pictures that customers can upload to customize a product (in bytes).'),
                        'validation' => 'isUnsignedInt',
                        'required' => true,
                        'cast' => 'intval',
                        'type' => 'text',
                        'suffix' => $this->l('bytes'),
                        'visibility' => Shop::CONTEXT_ALL
                    ),
                    'PS_PRODUCT_PICTURE_WIDTH' => array(
                        'title' => $this->l('Product picture width'),
                        'hint' => $this->l('Width of product customization pictures that customers can upload (in pixels).'),
                        'validation' => 'isUnsignedInt',
                        'required' => true,
                        'cast' => 'intval',
                        'type' => 'text',
                        'width' => 'px',
                        'suffix' => $this->l('pixels'),
                        'visibility' => Shop::CONTEXT_ALL
                    ),
                    'PS_PRODUCT_PICTURE_HEIGHT' => array(
                        'title' => $this->l('Product picture height'),
                        'hint' => $this->l('Height of product customization pictures that customers can upload (in pixels).'),
                        'validation' => 'isUnsignedInt',
                        'required' => true,
                        'cast' => 'intval',
                        'type' => 'text',
                        'height' => 'px',
                        'suffix' => $this->l('pixels'),
                        'visibility' => Shop::CONTEXT_ALL
                    ),
                    'PS_HIGHT_DPI' => array(
                        'type' => 'bool',
                        'title' => $this->l('Generate high resolution images'),
                        'required' => false,
                        'is_bool' => true,
                        'hint' => $this->l('This will generate an additional file for each image (thus doubling your total amount of images). Resolution of these images will be twice higher.'),
                        'desc' => $this->l('Enable to optimize the display of your images on high pixel density screens.'),
                        'visibility' => Shop::CONTEXT_ALL,
                    ),
                ),
                'submit' => array('title' => $this->l('Save')),
            ),
        );

        if ($this->display_move) {
            $this->fields_options['product_images']['fields']['PS_LEGACY_IMAGES'] = array(
                'title' => $this->l('Use the legacy image filesystem'),
                'hint' => $this->l('This should be set to yes unless you successfully moved images in "Images" page under the "Preferences" menu.'),
                'validation' => 'isBool',
                'cast' => 'intval',
                'required' => false,
                'type' => 'bool',
                'visibility' => Shop::CONTEXT_ALL
            );
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Image type'),
                'icon' => 'icon-picture'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name for the image type'),
                    'name' => 'name',
                    'required' => true,
                    'hint' => $this->l('Letters, underscores and hyphens only (e.g. "small_custom", "cart_medium", "large", "thickbox_extra-large").')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Width'),
                    'name' => 'width',
                    'required' => true,
                    'maxlength' => 5,
                    'suffix' => $this->l('pixels'),
                    'hint' => $this->l('Maximum image width in pixels.')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Height'),
                    'name' => 'height',
                    'required' => true,
                    'maxlength' => 5,
                    'suffix' => $this->l('pixels'),
                    'hint' => $this->l('Maximum image height in pixels.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Room Types/Products'),
                    'name' => 'products',
                    'required' => false,
                    'is_bool' => true,
                    'hint' => $this->l('This type will be used for Product images.'),
                    'values' => array(
                        array(
                            'id' => 'products_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'products_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        ),
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Categories'),
                    'name' => 'categories',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'hint' => $this->l('This type will be used for Category images.'),
                    'values' => array(
                        array(
                            'id' => 'categories_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'categories_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        ),
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Hotels'),
                    'name' => 'hotels',
                    'required' => false,
                    'is_bool' => true,
                    'hint' => $this->l('This type will be used for Hotel images.'),
                    'values' => array(
                        array(
                            'id' => 'hotels_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'hotels_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        ),
                    )
                ),
                // array(
                //     'type' => 'switch',
                //     'label' => $this->l('Manufacturers'),
                //     'name' => 'manufacturers',
                //     'required' => false,
                //     'is_bool' => true,
                //     'hint' => $this->l('This type will be used for Manufacturer images.'),
                //     'values' => array(
                //         array(
                //             'id' => 'manufacturers_on',
                //             'value' => 1,
                //             'label' => $this->l('Enabled')
                //         ),
                //         array(
                //             'id' => 'manufacturers_off',
                //             'value' => 0,
                //             'label' => $this->l('Disabled')
                //         ),
                //     )
                // ),
                // array(
                //     'type' => 'switch',
                //     'label' => $this->l('Suppliers'),
                //     'name' => 'suppliers',
                //     'required' => false,
                //     'is_bool' => true,
                //     'hint' => $this->l('This type will be used for Supplier images.'),
                //     'values' => array(
                //         array(
                //             'id' => 'suppliers_on',
                //             'value' => 1,
                //             'label' => $this->l('Enabled')
                //         ),
                //         array(
                //             'id' => 'suppliers_off',
                //             'value' => 0,
                //             'label' => $this->l('Disabled')
                //         ),
                //     )
                // ),
                // array(
                //     'type' => 'switch',
                //     'label' => $this->l('Scenes'),
                //     'name' => 'scenes',
                //     'required' => false,
                //     'class' => 't',
                //     'is_bool' => true,
                //     'hint' => $this->l('This type will be used for Scene images.'),
                //     'values' => array(
                //         array(
                //             'id' => 'scenes_on',
                //             'value' => 1,
                //             'label' => $this->l('Enabled')
                //         ),
                //         array(
                //             'id' => 'scenes_off',
                //             'value' => 0,
                //             'label' => $this->l('Disabled')
                //         ),
                //     )
                // ),
                // array(
                //     'type' => 'switch',
                //     'label' => $this->l('Stores'),
                //     'name' => 'stores',
                //     'required' => false,
                //     'is_bool' => true,
                //     'hint' => $this->l('This type will be used for Store images.'),
                //     'values' => array(
                //         array(
                //             'id' => 'stores_on',
                //             'value' => 1,
                //             'label' => $this->l('Enabled')
                //         ),
                //         array(
                //             'id' => 'stores_off',
                //             'value' => 0,
                //             'label' => $this->l('Disabled')
                //         ),
                //     )
                // ),
            ),
            'submit' => array(
                'title' => $this->l('Save')
            ),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->l('Save and stay'),
                    'name' => 'submitAdd'.$this->table.'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save',
                ),
            ),
        );

        parent::__construct();
    }

    public function ajaxProcess()
    {
        $response = array('success' => false);
        if ($action = Tools::getValue('action')) {
            $action = str_replace('image_type', '', $action);
            $fields = ObjectModel::getDefinition($this->className)['fields'];
            if (array_key_exists($action, $fields)) {
                $idImageType = Tools::getValue('id_image_type');
                if (Validate::isLoadedObject($objImageType = new ImageType($idImageType))) {
                    $objImageType->$action = !$objImageType->$action;
                    if ($objImageType->save()) {
                        $response['success'] = true;
                        $response['text'] = $this->l('Updated successfully.');
                    } else {
                        $response['text'] = $this->l('An error occurred while updating image type for .'.$action);
                    }
                }
            }
        }

        $this->ajaxDie(json_encode($response));
    }

    public function postProcess()
    {
        // When moving images, if duplicate images were found they are moved to a folder named duplicates/
        if (file_exists(_PS_PROD_IMG_DIR_.'duplicates/')) {
            $this->warnings[] = sprintf($this->l('Duplicate images were found when moving the product images. This is likely caused by unused demonstration images. Please make sure that the folder %s only contains demonstration images, and then delete it.'), _PS_PROD_IMG_DIR_.'duplicates/');
        }

        if (Tools::isSubmit('submitRegenerate'.$this->table)) {
            if ($this->tabAccess['edit'] === '1') {
                if ($this->_regenerateThumbnails(Tools::getValue('type'), Tools::getValue('erase'))) {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=9'.'&token='.$this->token);
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('submitMoveImages'.$this->table)) {
            if ($this->tabAccess['edit'] === '1') {
                if ($this->_moveImagesToNewFileSystem()) {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=25'.'&token='.$this->token);
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('submitOptions'.$this->table)) {
            if ($this->tabAccess['edit'] === '1') {
                if ((int)Tools::getValue('PS_JPEG_QUALITY') < 0
                    || (int)Tools::getValue('PS_JPEG_QUALITY') > 100) {
                    $this->errors[] = Tools::displayError('Incorrect value for the selected JPEG image compression.');
                } elseif ((int)Tools::getValue('PS_PNG_QUALITY') < 0
                    || (int)Tools::getValue('PS_PNG_QUALITY') > 9) {
                    $this->errors[] = Tools::displayError('Incorrect value for the selected PNG image compression.');
                } elseif (!Configuration::updateValue('PS_IMAGE_QUALITY', Tools::getValue('PS_IMAGE_QUALITY'))
                    || !Configuration::updateValue('PS_JPEG_QUALITY', Tools::getValue('PS_JPEG_QUALITY'))
                    || !Configuration::updateValue('PS_PNG_QUALITY', Tools::getValue('PS_PNG_QUALITY'))) {
                    $this->errors[] = Tools::displayError('Unknown error.');
                } else {
                    $this->confirmations[] = $this->_conf[6];
                }
                return parent::postProcess();
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } else {
            return parent::postProcess();
        }
    }

    protected function _childValidation()
    {
        if (!Tools::getValue('id_image_type') && Validate::isImageTypeName($typeName = Tools::getValue('name')) && ImageType::typeAlreadyExists($typeName)) {
            $this->errors[] = Tools::displayError('This name already exists.');
        }
    }

    /**
      * Init display for the thumbnails regeneration block
      */
    public function initRegenerate()
    {
        $types = array(
            'categories' => $this->l('Categories'),
            'products' => $this->l('Room Types/Products'),
            'hotels' => $this->l('Hotels'),
            // 'manufacturers' => $this->l('Manufacturers'),
            // 'suppliers' => $this->l('Suppliers'),
            // 'scenes' => $this->l('Scenes'),
            // 'stores' => $this->l('Stores')
        );

        $formats = array();
        foreach ($types as $i => $type) {
            $formats[$i] = ImageType::getImagesTypes($i);
        }

        $this->context->smarty->assign(array(
            'types' => $types,
            'formats' => $formats,
        ));
    }

    /**
     * Delete resized image then regenerate new one with updated settings
     *
     * @param string $dir
     * @param array  $type
     * @param bool   $product
     *
     * @return bool
     */
    protected function _deleteOldImages($dir, $type, $product = false)
    {
        if (!is_dir($dir)) {
            return false;
        }

        // delete product images using new filesystem.
        if ($product) {
            $productsImages = Image::getAllImages();
            foreach ($productsImages as $image) {
                $imageObj = new Image($image['id_image']);
                $imageObj->id_product = $image['id_product'];
                if (file_exists($dir.$imageObj->getImgFolder())) {
                    $toDel = scandir($dir.$imageObj->getImgFolder());
                    foreach ($toDel as $d) {
                        foreach ($type as $imageType) {
                            if (preg_match('/^[0-9]+\-'.$imageType['name'].'\.jpg$/', $d) || (count($type) > 1 && preg_match('/^[0-9]+\-[_a-zA-Z0-9-]*\.jpg$/', $d))) {
                                if (file_exists($dir.$imageObj->getImgFolder().$d)) {
                                    unlink($dir.$imageObj->getImgFolder().$d);
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $toDel = scandir($dir);
            foreach ($toDel as $d) {
                if ($d === '.' || $d === '..') {
                    continue;
                }
                if (is_dir($dir.$d)) {
                    $this->_deleteOldImages($dir.$d.'/', $type, $product);
                }
                foreach ($type as $imageType) {
                    if (preg_match('/^[0-9]+\-'.($product ? '[0-9]+\-' : '').$imageType['name'].'\.jpg$/', $d)
                        || (count($type) > 1 && preg_match('/^[0-9]+\-[_a-zA-Z0-9-]*\.jpg$/', $d))
                        || preg_match('/^([[:lower:]]{2})\-default\-'.$imageType['name'].'\.jpg$/', $d)) {
                        if (file_exists($dir.$d)) {
                            unlink($dir.$d);
                        }
                    }
                }
            }
        }
    }

    /**
     * Regenerate images
     *
     * @param $dir
     * @param $type
     * @param bool $productsImages
     * @return bool|string
     */
    protected function _regenerateNewImages($dir, $type, $imageElement = false)
    {
        if (!is_dir($dir)) {
            return false;
        }

        $generate_hight_dpi_images = (bool)Configuration::get('PS_HIGHT_DPI');
        if ($imageElement == 'products') {
            foreach (Image::getAllImages() as $image) {
                $imageObj = new Image($image['id_image']);
                $existing_img = $dir.$imageObj->getExistingImgPath().'.jpg';
                if (file_exists($existing_img) && filesize($existing_img)) {
                    foreach ($type as $imageType) {
                        if (!file_exists($dir.$imageObj->getExistingImgPath().'-'.stripslashes($imageType['name']).'.jpg')) {
                            if (!ImageManager::resize($existing_img, $dir.$imageObj->getExistingImgPath().'-'.stripslashes($imageType['name']).'.jpg', (int)$imageType['width'], (int)$imageType['height'])) {
                                $this->errors[] = sprintf(Tools::displayError('Original image is corrupt (%s) for product ID %2$d or bad permission on folder'), $existing_img, (int)$imageObj->id_product);
                            }

                            if ($generate_hight_dpi_images) {
                                if (!ImageManager::resize($existing_img, $dir.$imageObj->getExistingImgPath().'-'.stripslashes($imageType['name']).'2x.jpg', (int)$imageType['width']*2, (int)$imageType['height']*2)) {
                                    $this->errors[] = sprintf(Tools::displayError('Original image is corrupt (%s) for product ID %2$d or bad permission on folder'), $existing_img, (int)$imageObj->id_product);
                                }
                            }
                        }
                    }
                } else {
                    $this->errors[] = sprintf(Tools::displayError('Original image is missing or empty (%1$s) for product ID %2$d'), $existing_img, (int)$imageObj->id_product);
                }
                if (time() - $this->start_time > $this->max_execution_time - 4) { // stop 4 seconds before the tiemout, just enough time to process the end of the page on a slow server
                    return 'timeout';
                }
            }
        } elseif ($imageElement == 'hotels') {
            $objHotelImage = new HotelImage();
            foreach($objHotelImage->getAllImages() as $image) {
                $objHotelImage = new HotelImage($image['id']);
                $existing_img = $dir.$objHotelImage->getImagePath().'.jpg';

                if (file_exists($existing_img) && filesize($existing_img)) {
                    foreach ($type as $imageType) {
                        if (!file_exists($dir.$objHotelImage->getImagePath().'-'.stripslashes($imageType['name']).'.jpg')) {
                            if (!ImageManager::resize($existing_img, $dir.$objHotelImage->getImagePath().'-'.stripslashes($imageType['name']).'.jpg', (int)$imageType['width'], (int)$imageType['height'])) {
                                $this->errors[] = sprintf(Tools::displayError('Original image is corrupt (%s) for hotel ID %2$d or bad permission on folder'), $existing_img, (int)$objHotelImage->id_hotel);
                            }

                            if ($generate_hight_dpi_images) {
                                if (!ImageManager::resize($existing_img, $dir.$objHotelImage->getImagePath().'-'.stripslashes($imageType['name']).'2x.jpg', (int)$imageType['width']*2, (int)$imageType['height']*2)) {
                                    $this->errors[] = sprintf(Tools::displayError('Original image is corrupt (%s) for hotel ID %2$d or bad permission on folder'), $existing_img, (int)$objHotelImage->id_hotel);
                                }
                            }
                        }
                    }
                } else {
                    $this->errors[] = sprintf(Tools::displayError('Original image is missing or empty (%1$s) for hotel ID %2$d'), $existing_img, (int)$objHotelImage->id_hotel);
                }
                if (time() - $this->start_time > $this->max_execution_time - 4) { // stop 4 seconds before the tiemout, just enough time to process the end of the page on a slow server
                    return 'timeout';
                }

            }
        } else {
            $formated_thumb_scene = ImageType::getFormatedName('thumb_scene');
            $formated_medium = ImageType::getFormatedName('medium');
            foreach (scandir($dir) as $image) {
                if (preg_match('/^[0-9]*\.jpg$/', $image)) {
                    foreach ($type as $k => $imageType) {
                        // Customizable writing dir
                        $newDir = $dir;
                        if ($imageType['name'] == $formated_thumb_scene) {
                            $newDir .= 'thumbs/';
                        }
                        if (!file_exists($newDir)) {
                            continue;
                        }

                        if (($dir == _PS_CAT_IMG_DIR_) && ($imageType['name'] == $formated_medium) && is_file(_PS_CAT_IMG_DIR_.str_replace('.', '_thumb.', $image))) {
                            $image = str_replace('.', '_thumb.', $image);
                        }

                        if (!file_exists($newDir.substr($image, 0, -4).'-'.stripslashes($imageType['name']).'.jpg')) {
                            if (!file_exists($dir.$image) || !filesize($dir.$image)) {
                                $this->errors[] = sprintf(Tools::displayError('Source file does not exist or is empty (%s)'), $dir.$image);
                            } elseif (!ImageManager::resize($dir.$image, $newDir.substr(str_replace('_thumb.', '.', $image), 0, -4).'-'.stripslashes($imageType['name']).'.jpg', (int)$imageType['width'], (int)$imageType['height'])) {
                                    $this->errors[] = sprintf(Tools::displayError('Failed to resize image file (%s)'), $dir.$image);
                            }

                            if ($generate_hight_dpi_images) {
                                if (!ImageManager::resize($dir.$image, $newDir.substr($image, 0, -4).'-'.stripslashes($imageType['name']).'2x.jpg', (int)$imageType['width']*2, (int)$imageType['height']*2)) {
                                    $this->errors[] = sprintf(Tools::displayError('Failed to resize image file to high resolution (%s)'), $dir.$image);
                                }
                            }
                        }
                         // stop 4 seconds before the timeout, just enough time to process the end of the page on a slow server
                        if (time() - $this->start_time > $this->max_execution_time - 4) {
                            return 'timeout';
                        }
                    }
                }
            }
        }

        return (bool)count($this->errors);
    }

    /**
     * Regenerate no-pictures images
     *
     * @param $dir
     * @param $type
     * @param $languages
     * @return bool
     */
    protected function _regenerateNoPictureImages($dir, $type, $languages)
    {
        $errors = false;
        $generate_hight_dpi_images = (bool)Configuration::get('PS_HIGHT_DPI');

        foreach ($type as $image_type) {
            foreach ($languages as $language) {
                $file = $dir.$language['iso_code'].'.jpg';
                if (!file_exists($file)) {
                    $file = _PS_PROD_IMG_DIR_.Language::getIsoById((int)Configuration::get('PS_LANG_DEFAULT')).'.jpg';
                }
                if (!file_exists($dir.$language['iso_code'].'-default-'.stripslashes($image_type['name']).'.jpg')) {
                    if (!ImageManager::resize($file, $dir.$language['iso_code'].'-default-'.stripslashes($image_type['name']).'.jpg', (int)$image_type['width'], (int)$image_type['height'])) {
                        $errors = true;
                    }

                    if ($generate_hight_dpi_images) {
                        if (!ImageManager::resize($file, $dir.$language['iso_code'].'-default-'.stripslashes($image_type['name']).'2x.jpg', (int)$image_type['width']*2, (int)$image_type['height']*2)) {
                            $errors = true;
                        }
                    }
                }
            }
        }
        return $errors;
    }

    /* Hook watermark optimization */
    protected function _regenerateWatermark($dir, $type = null)
    {
        $result = Db::getInstance()->executeS('
		SELECT m.`name` FROM `'._DB_PREFIX_.'module` m
		LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
		LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
		WHERE h.`name` = \'actionWatermark\' AND m.`active` = 1');

        if ($result && count($result)) {
            $productsImages = Image::getAllImages();
            foreach ($productsImages as $image) {
                $imageObj = new Image($image['id_image']);
                if (file_exists($dir.$imageObj->getExistingImgPath().'.jpg')) {
                    foreach ($result as $module) {
                        $moduleInstance = Module::getInstanceByName($module['name']);
                        if ($moduleInstance && is_callable(array($moduleInstance, 'hookActionWatermark'))) {
                            call_user_func(array($moduleInstance, 'hookActionWatermark'), array('id_image' => $imageObj->id, 'id_product' => $imageObj->id_product, 'image_type' => $type));
                        }

                        if (time() - $this->start_time > $this->max_execution_time - 4) { // stop 4 seconds before the tiemout, just enough time to process the end of the page on a slow server
                            return 'timeout';
                        }
                    }
                }
            }
        }
    }

    protected function _regenerateThumbnails($type = 'all', $deleteOldImages = false)
    {
        $this->start_time = time();
        ini_set('max_execution_time', $this->max_execution_time); // ini_set may be disabled, we need the real value
        $this->max_execution_time = (int)ini_get('max_execution_time');
        $languages = Language::getLanguages(false);

        $process = array(
            array('type' => 'categories', 'dir' => _PS_CAT_IMG_DIR_),
            array('type' => 'products', 'dir' => _PS_PROD_IMG_DIR_),
            array('type' => 'hotels', 'dir' => _PS_HOTEL_IMG_DIR_),
        );

        // Launching generation process
        foreach ($process as $proc) {
            if ($type != 'all' && $type != $proc['type']) {
                continue;
            }

            // Getting format generation
            $formats = ImageType::getImagesTypes($proc['type']);
            if ($type != 'all') {
                $format = strval(Tools::getValue('format_'.$type));
                if ($format != 'all') {
                    foreach ($formats as $k => $form) {
                        if ($form['id_image_type'] != $format) {
                            unset($formats[$k]);
                        }
                    }
                }
            }

            if ($deleteOldImages) {
                $this->_deleteOldImages($proc['dir'], $formats, ($proc['type'] == 'products' ? true : false));
            }
            if (($return = $this->_regenerateNewImages($proc['dir'], $formats, $proc['type'])) === true) {
                if (!count($this->errors)) {
                    $this->errors[] = sprintf(Tools::displayError('Cannot write images for this type: %s. Please check the %s folder\'s writing permissions.'), $proc['type'], $proc['dir']);
                }
            } elseif ($return == 'timeout') {
                $this->errors[] = Tools::displayError('Only part of the images have been regenerated. The server timed out before finishing.');
            } else {
                if ($proc['type'] == 'products') {
                    if ($this->_regenerateWatermark($proc['dir'], $formats) == 'timeout') {
                        $this->errors[] = Tools::displayError('Server timed out. The watermark may not have been applied to all images.');
                    }
                }
                if (!count($this->errors)) {
                    if ($this->_regenerateNoPictureImages($proc['dir'], $formats, $languages)) {
                        $this->errors[] = sprintf(Tools::displayError('Cannot write "No picture" image to (%s) images folder. Please check the folder\'s writing permissions.'), $proc['type']);
                    }
                }
            }
        }
        return (count($this->errors) > 0 ? false : true);
    }

    /**
     * Init display for move images block
     */
    public function initMoveImages()
    {
        $this->context->smarty->assign(array(
            'safe_mode' => Tools::getSafeModeStatus(),
            'link_ppreferences' => 'index.php?tab=AdminPPreferences&token='.Tools::getAdminTokenLite('AdminPPreferences').'#PS_LEGACY_IMAGES_on',
        ));
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_image_type'] = array(
                'href' => self::$currentIndex.'&addimage_type&token='.$this->token,
                'desc' => $this->l('Add new image type', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    /**
     * Move product images to the new filesystem
     */
    protected function _moveImagesToNewFileSystem()
    {
        if (!Image::testFileSystem()) {
            $this->errors[] = Tools::displayError('Error: Your server configuration is not compatible with the new image system. No images were moved.');
        } else {
            ini_set('max_execution_time', $this->max_execution_time); // ini_set may be disabled, we need the real value
            $this->max_execution_time = (int)ini_get('max_execution_time');
            $result = Image::moveToNewFileSystem($this->max_execution_time);
            if ($result === 'timeout') {
                $this->errors[] = Tools::displayError('Not all images have been moved. The server timed out before finishing. Click on "Move images" again to resume the moving process.');
            } elseif ($result === false) {
                $this->errors[] = Tools::displayError('Error: Some -- or all -- images cannot be moved.');
            }
        }
        return (count($this->errors) > 0 ? false : true);
    }

    public function initContent()
    {
        if ($this->display != 'edit' && $this->display != 'add') {
            $this->initRegenerate();
            $this->initMoveImages();

            $this->context->smarty->assign(array(
                'display_regenerate' => true,
                'display_move' => $this->display_move
            ));
        }

        if ($this->display == 'edit') {
            $this->warnings[] = $this->l('After modification, do not forget to regenerate thumbnails');
        }

        parent::initContent();
    }
}
