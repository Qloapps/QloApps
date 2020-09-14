<?php
/**
* 2010-2020 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WebserviceSpecificManagementHotelImages implements WebserviceSpecificManagementInterface
{
    /** @var WebserviceOutputBuilder */
    protected $objOutput;
    protected $output;

    /** @var WebserviceRequest */
    protected $wsObject;
    public $imgToDisplay = null;
    public $imageResource = null;

    /**
     * @var int The maximum size supported when uploading images, in bytes
     */
    protected $imgMaxUploadSize = 3000000;

    protected $acceptedImgMimeTypes = array('image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');

    /* ------------------------------------------------
     * GETTERS & SETTERS
     * ------------------------------------------------ */

    /**
     * @param WebserviceOutputBuilderCore $obj
     * @return WebserviceSpecificManagementInterface
     */
    public function setObjectOutput(WebserviceOutputBuilderCore $obj)
    {
        $this->objOutput = $obj;
        return $this;
    }

    public function getObjectOutput()
    {
        return $this->objOutput;
    }

    public function setWsObject(WebserviceRequestCore $obj)
    {
        $this->wsObject = $obj;
        return $this;
    }

    public function getWsObject()
    {
        return $this->wsObject;
    }

    /*
    * This method need $this->imgToDisplay to be set if output don't needs to be XML
    */
    public function getContent()
    {
        if ($this->output != '') {
            return $this->objOutput->getObjectRender()->overrideContent($this->output);
        }
        // display image content if needed
        elseif ($this->imgToDisplay) {
            if (empty($this->imgExtension)) {
                $imginfo = getimagesize($this->imgToDisplay);
                $this->imgExtension = image_type_to_extension($imginfo[2], false);
            }
            $imageResource = false;
            $types = array(
                'jpg' => array(
                    'function' => 'imagecreatefromjpeg',
                    'Content-Type' => 'image/jpeg'
                ),
                'jpeg' => array(
                    'function' => 'imagecreatefromjpeg',
                    'Content-Type' => 'image/jpeg'
                ),
                'png' => array('function' =>
                    'imagecreatefrompng',
                    'Content-Type' => 'image/png'
                ),
                'gif' => array(
                    'function' => 'imagecreatefromgif',
                    'Content-Type' => 'image/gif'
                )
            );
            if (array_key_exists($this->imgExtension, $types)) {
                $imageResource = @$types[$this->imgExtension]['function']($this->imgToDisplay);
            }

            if (!$imageResource) {
                throw new WebserviceException(sprintf('Unable to load the image "%s"', str_replace(_PS_ROOT_DIR_, '[SHOP_ROOT_DIR]', $this->imgToDisplay)), array(47, 500));
            } else {
                if (array_key_exists($this->imgExtension, $types)) {
                    $this->objOutput->setHeaderParams('Content-Type', $types[$this->imgExtension]['Content-Type']);
                }
                return file_get_contents($this->imgToDisplay);
            }
        }
    }

    public function manage()
    {
        $this->manageImages();
        return $this->wsObject->getOutputEnabled();
    }

    /**
     * Management of images URL segment
     *
     * @return bool
     *
     * @throws WebserviceException
     */
    protected function manageImages()
    {
        // Pre configuration...
        if (isset($this->wsObject->urlSegment)) {
            for ($i = 1; $i < 6; $i++) {
                if (count($this->wsObject->urlSegment) == $i) {
                    $this->wsObject->urlSegment[$i] = '';
                }
            }
        }

        $directory = _PS_MODULE_DIR_.'hotelreservationsystem/views/img/hotel_img/';
        $this->manageDeclinatedImages($directory);
    }

    protected function manageDeclinatedImages($directory)
    {
        // Get available image sizes for the current image type
        switch ($this->wsObject->urlSegment[1]) {
            // Match the default images
            case 'default':
                return $this->manageDefaultDeclinatedImages();
                break;
            // Display the list of images
            case '':
                return $this->manageListDeclinatedImages();
                break;
            default:
                return $this->manageEntityDeclinatedImages($directory);
                break;
        }
    }

    protected function manageEntityDeclinatedImages($directory)
    {
        // If id is detected
        $object_id = $this->wsObject->urlSegment[1];
        if (!Validate::isUnsignedId($object_id)) {
            throw new WebserviceException('The image id is invalid. Please set a valid id or the "default" value', array(60, 400));
        }

        // Get available image ids
        $available_image_ids = array();

        $objHotelImg = new HotelImage();
        $images = $objHotelImg->getAllImagesByHotelId($object_id);

        foreach ($images as $image) {
            $available_image_ids[] = $image['id'];
        }

        // If an image id is specified
        if ($this->wsObject->urlSegment[2] != '') {
            if (!Validate::isUnsignedId($object_id) || !in_array($this->wsObject->urlSegment[2], $available_image_ids)) {
                throw new WebserviceException('This image id does not exist', array(57, 400));
            } else {
                // Check for new image system
                $image_id = $this->wsObject->urlSegment[2];
                if (Validate::isLoadedObject($objHotelImg = new HotelImage($image_id))) {
                    if (file_exists($directory.$objHotelImg->hotel_image_id.'.jpg')) {
                        $filename = $directory.'/'.$objHotelImg->hotel_image_id.'.jpg';
                    }
                }
            }
        } else if ($this->wsObject->method == 'GET' || $this->wsObject->method == 'HEAD') {
            if ($available_image_ids) {
                $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('image', array(), array('id'=>$object_id));
                foreach ($available_image_ids as $available_image_id) {
                    $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('declination', array(), array('id'=>$available_image_id, 'xlink_resource'=>$this->wsObject->wsUrl.'hotel_images/'.$object_id.'/'.$available_image_id), false);
                }
                $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('image', array());
            } else {
                $this->objOutput->setStatus(404);
                $this->wsObject->setOutputEnabled(false);
            }
        }

        // in case of declinated images list of a product is get
        if ($this->output != '') {
            return true;
        } elseif (isset($filename)) {
            $filename_exists = file_exists($filename);
            return $this->manageDeclinatedImagesCRUD($filename_exists, $filename, $directory);
        } else {
            return $this->manageDeclinatedImagesCRUD(false, '', $directory);
        }
    }

    protected function manageListDeclinatedImages()
    {
        // Check if method is allowed
        if ($this->wsObject->method != 'GET') {
            throw new WebserviceException('This method is not allowed for listing hotel images.', array(55, 405));
        }
        $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('images', array());

        $objHotelImg = new HotelImage();
        $images = $objHotelImg->getAllImages();

        $ids = array();
        foreach ($images as $image) {
            $ids[] = $image['id_hotel'];
        }

        $ids = array_unique($ids, SORT_NUMERIC);
        asort($ids);
        foreach ($ids as $id) {
            $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('image', array(), array('id' => $id, 'xlink_resource'=>$this->wsObject->wsUrl.'hotel_images'.'/'.$id), false);
        }

        $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('images', array());
        return true;
    }

    protected function manageDeclinatedImagesCRUD($filename_exists, $filename, $directory)
    {
        switch ($this->wsObject->method) {
            // Display the image
            case 'GET':
            case 'HEAD':
                if ($filename_exists) {
                    $this->imgToDisplay = $filename;
                } else {
                    throw new WebserviceException('This image does not exist on disk', array(61, 500));
                }
                break;
            // Modify the image
            case 'PUT':
                if ($filename_exists) {
                    if ($this->writePostedImageOnDisk($filename, null, null, $directory)) {
                        $this->imgToDisplay = $filename;
                        return true;
                    } else {
                        throw new WebserviceException('Unable to save this image.', array(62, 500));
                    }
                } else {
                    throw new WebserviceException('This image does not exist on disk', array(63, 500));
                }
                break;
            // Delete the image
            case 'DELETE':
                // Delete products image in DB
                if ($filename_exists) {
                    $image = new HotelImage((int)$this->wsObject->urlSegment[2]);
                    return $image->delete();
                } else {
                    throw new WebserviceException('This image does not exist on disk', array(64, 500));
                }
                break;
            // Add the image
            case 'POST':
                if ($filename_exists) {
                    throw new WebserviceException('This image already exists. To modify it, please use the PUT method', array(65, 400));
                } else {
                    if ($this->writePostedImageOnDisk($filename, null, null, $directory)) {
                        return true;
                    } else {
                        throw new WebserviceException('Unable to save this image', array(66, 500));
                    }
                }
                break;
            default :
                throw new WebserviceException('This method is not allowed', array(67, 405));
        }
    }

    protected function writePostedImageOnDisk($reception_path, $dest_width = null, $dest_height = null, $parent_path = null)
    {
        if ($this->wsObject->method == 'PUT') {
            if (isset($_FILES['image']['tmp_name']) && $_FILES['image']['tmp_name']) {
                $file = $_FILES['image'];
                if ($file['size'] > $this->imgMaxUploadSize) {
                    throw new WebserviceException(sprintf('The image size is too large (maximum allowed is %d KB)', ($this->imgMaxUploadSize / 1000)), array(72, 400));
                }

                // Get mime content type
                $mime_type = false;
                if (Tools::isCallable('finfo_open')) {
                    $const = defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME;
                    $finfo = finfo_open($const);
                    $mime_type = finfo_file($finfo, $file['tmp_name']);
                    finfo_close($finfo);
                } elseif (Tools::isCallable('mime_content_type')) {
                    $mime_type = mime_content_type($file['tmp_name']);
                } elseif (Tools::isCallable('exec')) {
                    $mime_type = trim(exec('file -b --mime-type '.escapeshellarg($file['tmp_name'])));
                }
                if (empty($mime_type) || $mime_type == 'regular file') {
                    $mime_type = $file['type'];
                }
                if (($pos = strpos($mime_type, ';')) !== false) {
                    $mime_type = substr($mime_type, 0, $pos);
                }

                // Check mime content type
                if (!$mime_type || !in_array($mime_type, $this->acceptedImgMimeTypes)) {
                    throw new WebserviceException('This type of image format is not recognized, allowed formats are: '.implode('", "', $this->acceptedImgMiwmeTypes), array(73, 400));
                }
                // Check error while uploading
                elseif ($file['error']) {
                    throw new WebserviceException('Error while uploading image. Please change your server\'s settings', array(74, 400));
                }

                // Try to copy image file to a temporary file
                if (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['image']['tmp_name'], $tmp_name)) {
                    throw new WebserviceException('Error while copying image to the temporary directory', array(75, 400));
                }
                // Try to copy image file to the image directory
                else {
                    $result = $this->writeImageOnDisk($tmp_name, $reception_path, $dest_width, $dest_height, $parent_path);
                }

                @unlink($tmp_name);
                return $result;
            } else {
                throw new WebserviceException('Please set an "image" parameter with image data for value', array(76, 400));
            }
        } elseif ($this->wsObject->method == 'POST') {
            if (isset($_FILES['image']['tmp_name']) && $_FILES['image']['tmp_name']) {
                $file = $_FILES['image'];

                if ($file['size'] > $this->imgMaxUploadSize) {
                    throw new WebserviceException(sprintf('The image size is too large (maximum allowed is %d KB)', ($this->imgMaxUploadSize / 1000)), array(72, 400));
                }

                require_once(_PS_CORE_DIR_.'/images.inc.php');
                if ($error = ImageManager::validateUpload($file)) {
                    throw new WebserviceException('Image upload error : '.$error, array(76, 400));
                }

                if (isset($file['tmp_name']) && $file['tmp_name'] != null) {
                    // copy image
                    if (!isset($file['tmp_name'])) {
                        return false;
                    }

                    if ($error = ImageManager::validateUpload($file, $this->imgMaxUploadSize)) {
                        throw new WebserviceException('Bad image : '.$error, array(76, 400));
                    }
                    $objHotelImage = new HotelImage();
                    if ($result = $objHotelImage->uploadHotelImages($_FILES['image'], $this->wsObject->urlSegment[1], $parent_path)) {
                        $objHotelImage = new HotelImage($result['id_image']);

                        @unlink($tmp_name);

                        $this->imgToDisplay = _PS_MODULE_DIR_.'hotelreservationsystem/views/img/hotel_img/'.$objHotelImage->hotel_image_id.'.jpg';

                        $this->objOutput->setFieldsToDisplay('full');
                        $this->output = $this->objOutput->renderEntity($objHotelImage, 1);

                        $image_content = array('sqlId' => 'content', 'value' => base64_encode(file_get_contents($this->imgToDisplay)), 'encode' => 'base64');
                        $this->output .= $this->objOutput->objectRender->renderField($image_content);

                        return true;
                    }
                    return false;
                }
            }
        } else {
            throw new WebserviceException('Method '.$this->wsObject->method.' is not allowed for an image resource', array(77, 405));
        }
    }

    protected function writeImageOnDisk($base_path, $new_path, $dest_width = null, $dest_height = null, $image_types = null, $parent_path = null)
    {
        list($source_width, $source_height, $type, $attr) = getimagesize($base_path);
        if (!$source_width) {
            throw new WebserviceException('Image width was null', array(68, 400));
        }
        if ($dest_width == null) {
            $dest_width = $source_width;
        }
        if ($dest_height == null) {
            $dest_height = $source_height;
        }
        switch ($type) {
            case 1:
                $source_image = imagecreatefromgif($base_path);
                break;
            case 3:
                $source_image = imagecreatefrompng($base_path);
                break;
            case 2:
            default:
                $source_image = imagecreatefromjpeg($base_path);
                break;
        }

        $width_diff = $dest_width / $source_width;
        $height_diff = $dest_height / $source_height;

        if ($width_diff > 1 && $height_diff > 1) {
            $next_width = $source_width;
            $next_height = $source_height;
        } else {
            if ((int)(Configuration::get('PS_IMAGE_GENERATION_METHOD')) == 2 || ((int)(Configuration::get('PS_IMAGE_GENERATION_METHOD')) == 0 && $width_diff > $height_diff)) {
                $next_height = $dest_height;
                $next_width = (int)(($source_width * $next_height) / $source_height);
                $dest_width = ((int)(Configuration::get('PS_IMAGE_GENERATION_METHOD')) == 0 ? $dest_width : $next_width);
            } else {
                $next_width = $dest_width;
                $next_height = (int)($source_height * $dest_width / $source_width);
                $dest_height = ((int)(Configuration::get('PS_IMAGE_GENERATION_METHOD')) == 0 ? $dest_height : $next_height);
            }
        }

        $border_width = (int)(($dest_width - $next_width) / 2);
        $border_height = (int)(($dest_height - $next_height) / 2);

        // Build the image
        if (
            !($dest_image = imagecreatetruecolor($dest_width, $dest_height)) ||
            !($white = imagecolorallocate($dest_image, 255, 255, 255)) ||
            !imagefill($dest_image, 0, 0, $white) ||
            !imagecopyresampled($dest_image, $source_image, $border_width, $border_height, 0, 0, $next_width, $next_height, $source_width, $source_height) ||
            !imagecolortransparent($dest_image, $white)
        ) {
            throw new WebserviceException(sprintf('Unable to build the image "%s".', str_replace(_PS_ROOT_DIR_, '[SHOP_ROOT_DIR]', $new_path)), array(69, 500));
        }

        // Write it on disk

        switch ($this->imgExtension) {
            case 'gif':
                $imaged = imagegif($dest_image, $new_path);
                break;
            case 'png':
                $quality = (Configuration::get('PS_PNG_QUALITY') === false ? 7 : Configuration::get('PS_PNG_QUALITY'));
                $imaged = imagepng($dest_image, $new_path, (int)$quality);
                break;
            case 'jpeg':
            default:
                $quality = (Configuration::get('PS_JPEG_QUALITY') === false ? 90 : Configuration::get('PS_JPEG_QUALITY'));
                $imaged = imagejpeg($dest_image, $new_path, (int)$quality);
                break;
        }
        imagedestroy($dest_image);
        if (!$imaged) {
            throw new WebserviceException(sprintf('Unable to write the image "%s".', str_replace(_PS_ROOT_DIR_, '[SHOP_ROOT_DIR]', $new_path)), array(70, 500));
        }

        return $new_path;
    }
}
