<?php
/**
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2017 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class WebserviceSpecificManagementImagesCore implements WebserviceSpecificManagementInterface
{
    /** @var WebserviceOutputBuilder */
    protected $objOutput;
    protected $output;

    /** @var WebserviceRequest */
    protected $wsObject;

    /**
     * @var string The extension of the image to display
     */
    protected $imgExtension;

    /**
     * @var array The type of images (general, categories, manufacturers, suppliers, stores...)
     */
    protected $imageTypes = array(
        'general' => array(
            'header' => array(),
            'mail' => array(),
            'invoice' => array(),
            'store_icon' => array(),
        ),
        'products' => array(),
        'hotels' => array(),
        'categories' => array(),
        'manufacturers' => array(),
        'suppliers' => array(),
        'stores' => array(),
        'customizations' => array(),
    );

    /**
     * @var string The image type (product, category, general,...)
     */
    protected $imageType = null;

    /**
     * @var int The maximum size supported when uploading images, in bytes
     */
    protected $imgMaxUploadSize = 3000000;

    /**
     * @var array The list of supported mime types
     */
    protected $acceptedImgMimeTypes = array('image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');

    /**
     * @var string The product image declination id
     */
    protected $productImageDeclinationId = null;

    /**
     * @var bool If the current image management has to manage a "default" image (i.e. "No product available")
     */
    protected $defaultImage = false;

    /**
     * @var string The file path of the image to display. If not null, the image will be displayed, even if the XML output was not empty
     */
    public $imgToDisplay = null;
    public $imageResource = null;

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
        /*
         * available cases api/... :
         *
         *   images ("types_list") (N-1)
         *   	GET    (xml)
         *   images/general ("general_list") (N-2)
         *   	GET    (xml)
         *   images/general/[header,+] ("general") (N-3)
         *   	GET    (bin)
         *   	PUT    (bin)
         *
         *
         *   images/[categories,+] ("normal_list") (N-2) ([categories,+] = categories, manufacturers, ...)
         *   	GET    (xml)
         *   images/[categories,+]/[1,+] ("normal") (N-3)
         *   	GET    (bin)
         *   	PUT    (bin)
         *   	DELETE
         *   	POST   (bin) (if image does not exists)
         *   images/[categories,+]/[1,+]/[small,+] ("normal_resized") (N-4)
         *   	GET    (bin)
         *   images/[categories,+]/default ("display_list_of_langs") (N-3)
         *   	GET    (xml)
         *   images/[categories,+]/default/[en,+] ("normal_default_i18n")  (N-4)
         *   	GET    (bin)
         *   	POST   (bin) (if image does not exists)
         *      PUT    (bin)
         *      DELETE
         *   images/[categories,+]/default/[en,+]/[small,+] ("normal_default_i18n_resized")  (N-5)
         *   	GET    (bin)
         *
         *   images/product ("product_list")  (N-2)
         *   	GET    (xml) (list of image)
         *   images/product/[1,+] ("product_description")  (N-3)
         *   	GET    (xml) (legend, declinations, xlink to images/product/[1,+]/bin)
         *   images/product/[1,+]/bin ("product_bin")  (N-4)
         *   	GET    (bin)
         *      POST   (bin) (if image does not exists)
         *   images/product/[1,+]/[1,+] ("product_declination")  (N-4)
         *   	GET    (bin)
         *   	POST   (xml) (legend)
         *   	PUT    (xml) (legend)
         *      DELETE
         *   images/product/[1,+]/[1,+]/bin ("product_declination_bin") (N-5)
         *   	POST   (bin) (if image does not exists)
         *   	GET    (bin)
         *   	PUT    (bin)
         *   images/product/[1,+]/[1,+]/[small,+] ("product_declination_resized") (N-5)
         *   	GET    (bin)
         *   images/product/default ("product_default") (N-3)
         *   	GET    (bin)
         *   images/product/default/[en,+] ("product_default_i18n") (N-4)
         *   	GET    (bin)
         *      POST   (bin)
         *      PUT   (bin)
         *      DELETE
         *   images/product/default/[en,+]/[small,+] ("product_default_i18n_resized") (N-5)
         * 		GET    (bin)
         *
         */

        /* Declinated
         *ok    GET    (bin)
         *ok images/product ("product_list")  (N-2)
         *ok	GET    (xml) (list of image)
         *ok images/product/[1,+] ("product_description")  (N-3)
         *   	GET    (xml) (legend, declinations, xlink to images/product/[1,+]/bin)
         *ok images/product/[1,+]/bin ("product_bin")  (N-4)
         *ok 	GET    (bin)
         *      POST   (bin) (if image does not exists)
         *ok images/product/[1,+]/[1,+] ("product_declination")  (N-4)
         *ok 	GET    (bin)
         *   	POST   (xml) (legend)
         *   	PUT    (xml) (legend)
         *      DELETE
         *ok images/product/[1,+]/[1,+]/bin ("product_declination_bin") (N-5)
         *   	POST   (bin) (if image does not exists)
         *ok 	GET    (bin)
         *   	PUT    (bin)
         *   images/product/[1,+]/[1,+]/[small,+] ("product_declination_resized") (N-5)
         *ok 	GET    (bin)
         *ok images/product/default ("product_default") (N-3)
         *ok 	GET    (bin)
         *ok images/product/default/[en,+] ("product_default_i18n") (N-4)
         *ok 	GET    (bin)
         *      POST   (bin)
         *      PUT   (bin)
         *      DELETE
         *ok images/product/default/[en,+]/[small,+] ("product_default_i18n_resized") (N-5)
         *ok	GET    (bin)
         *
         * */

        // Pre configuration...
        if (isset($this->wsObject->urlSegment)) {
            for ($i = 1; $i < 6; $i++) {
                if (count($this->wsObject->urlSegment) == $i) {
                    $this->wsObject->urlSegment[$i] = '';
                }
            }
        }

        $this->imageType = $this->wsObject->urlSegment[1];
        // if image type requested is room_types then process products images
        if ($this->imageType == 'room_types') {
            $this->imageType = 'products';
        }

        switch ($this->wsObject->urlSegment[1]) {
            // general images management : like header's logo, invoice logo, etc...
            case 'general':
                return $this->manageGeneralImages();
                break;
            // normal images management : like the most entity images (categories, manufacturers..)...
            case 'categories':
            case 'manufacturers':
            case 'suppliers':
            case 'stores':
            case 'features':
                switch ($this->wsObject->urlSegment[1]) {
                    case 'categories':
                        $directory = _PS_CAT_IMG_DIR_;
                        break;
                    case 'manufacturers':
                        $directory = _PS_MANU_IMG_DIR_;
                        break;
                    case 'suppliers':
                        $directory = _PS_SUPP_IMG_DIR_;
                        break;
                    case 'stores':
                        $directory = _PS_STORE_IMG_DIR_;
                        break;
                    case 'features':
                        $directory = _PS_IMG_DIR_.'rf/';
                        break;
                }
                return $this->manageDeclinatedImages($directory);
                break;

            // product image management : many image for one entity (product)
            case 'room_types':
                return $this->manageProductImages();
                break;
            case 'hotels':
                return $this->manageHotelImages();
                break;
            case 'customizations':
                return $this->manageCustomizationImages();
                break;
            // images root node management : many image for one entity (product)
            case '':
                $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('image_types', array());
                foreach (array_keys($this->imageTypes) as $image_type_name) {
                    $more_attr = array(
                        'xlink_resource' => $this->wsObject->wsUrl.$this->wsObject->urlSegment[0].'/'.$image_type_name,
                        'get' => 'true', 'put' => 'false', 'post' => 'false', 'delete' => 'false', 'head' => 'true',
                        'upload_allowed_mimetypes' => implode(', ', $this->acceptedImgMimeTypes)
                    );
                    $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader($image_type_name, array(), $more_attr, false);
                }
                $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('image_types', array());
                return true;
                break;
            default:
                $exception = new WebserviceException(sprintf('Image of type "%s" does not exist', $this->wsObject->urlSegment[1]), array(48, 400));
                throw $exception->setDidYouMean($this->wsObject->urlSegment[1], array_keys($this->imageTypes));
        }
    }

    /**
     * Management of general images
     *
     * @return bool
     *
     * @throws WebserviceException
     */
    protected function manageGeneralImages()
    {
        $path = '';
        $alternative_path = '';
        switch ($this->wsObject->urlSegment[2]) {
            // Set the image path on display in relation to the header image
            case 'header':
                if (in_array($this->wsObject->method, array('GET', 'HEAD', 'PUT'))) {
                    $path = _PS_IMG_DIR_.Configuration::get('PS_LOGO');
                } else {
                    throw new WebserviceException('This method is not allowed with general image resources.', array(49, 405));
                }
                break;

            // Set the image path on display in relation to the mail image
            case 'mail':
                if (in_array($this->wsObject->method, array('GET', 'HEAD', 'PUT'))) {
                    $path = _PS_IMG_DIR_.Configuration::get('PS_LOGO_MAIL');
                    $alternative_path = _PS_IMG_DIR_.Configuration::get('PS_LOGO');
                } else {
                    throw new WebserviceException('This method is not allowed with general image resources.', array(50, 405));
                }
                break;

            // Set the image path on display in relation to the invoice image
            case 'invoice':
                if (in_array($this->wsObject->method, array('GET', 'HEAD', 'PUT'))) {
                    $path = _PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE');
                    $alternative_path = _PS_IMG_DIR_.Configuration::get('PS_LOGO');
                } else {
                    throw new WebserviceException('This method is not allowed with general image resources.', array(51, 405));
                }
                break;

            // Set the image path on display in relation to the icon store image
            case 'store_icon':
                if (in_array($this->wsObject->method, array('GET', 'HEAD', 'PUT'))) {
                    $path = _PS_IMG_DIR_.Configuration::get('PS_STORES_ICON');
                    $this->imgExtension = 'gif';
                } else {
                    throw new WebserviceException('This method is not allowed with general image resources.', array(52, 405));
                }
                break;

            // List the general image types
            case '':
                $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('general_image_types', array());
                foreach (array_keys($this->imageTypes['general']) as $general_image_type_name) {
                    $more_attr = array(
                        'xlink_resource' => $this->wsObject->wsUrl.$this->wsObject->urlSegment[0].'/'.$this->wsObject->urlSegment[1].'/'.$general_image_type_name,
                        'get' => 'true', 'put' => 'true', 'post' => 'false', 'delete' => 'false', 'head' => 'true',
                        'upload_allowed_mimetypes' => implode(', ', $this->acceptedImgMimeTypes)
                    );
                    $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader($general_image_type_name, array(), $more_attr, false);
                }
                $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('general_image_types', array());
                return true;
                break;

            // If the image type does not exist...
            default:
                $exception = new WebserviceException(sprintf('General image of type "%s" does not exist', $this->wsObject->urlSegment[2]), array(53, 400));
                throw $exception->setDidYouMean($this->wsObject->urlSegment[2], array_keys($this->imageTypes['general']));
        }
        // The general image type is valid, now we try to do action in relation to the method
        switch ($this->wsObject->method) {
            case 'GET':
            case 'HEAD':
                $this->imgToDisplay = ($path != '' && file_exists($path) && is_file($path)) ? $path : $alternative_path;
                return true;
                break;
            case 'PUT':
                if ($this->writePostedImageOnDisk($path, null, null)) {
                    if ($this->wsObject->urlSegment[2] == 'header') {
                        $logo_name = Configuration::get('PS_LOGO') ? Configuration::get('PS_LOGO') : 'logo.png';
                        list($width, $height, $type, $attr) = getimagesize(_PS_IMG_DIR_.$logo_name);
                        Configuration::updateValue('SHOP_LOGO_WIDTH', (int)round($width));
                        Configuration::updateValue('SHOP_LOGO_HEIGHT', (int)round($height));
                    }
                    $this->imgToDisplay = $path;
                    return true;
                } else {
                    throw new WebserviceException('Error while copying image to the directory', array(54, 400));
                }
                break;
        }
    }

    protected function manageDefaultDeclinatedImages($directory, $normal_image_sizes)
    {
        $this->defaultImage = true;
        // Get the language iso code list
        $lang_list = Language::getIsoIds(true);
        $langs = array();
        $default_lang = Configuration::get('PS_LANG_DEFAULT');
        foreach ($lang_list as $lang) {
            if ($lang['id_lang'] == $default_lang) {
                $default_lang = $lang['iso_code'];
            }
            $langs[] = $lang['iso_code'];
        }

        // Display list of languages
        if ($this->wsObject->urlSegment[3] == '' && $this->wsObject->method == 'GET') {
            $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('languages', array());
            foreach ($lang_list as $lang) {
                $more_attr = array(
                    'xlink_resource' => $this->wsObject->wsUrl.'images/'.$this->imageType.'/default/'.$lang['iso_code'],
                    'get' => 'true', 'put' => 'true', 'post' => 'true', 'delete' => 'true', 'head' => 'true',
                    'upload_allowed_mimetypes' => implode(', ', $this->acceptedImgMimeTypes),
                    'iso'=>$lang['iso_code']
                );
                $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('language', array(), $more_attr, false);
            }

            $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('languages', array());
            return true;
        } else {
            $lang_iso = $this->wsObject->urlSegment[3];
            $image_size = $this->wsObject->urlSegment[4];
            if ($image_size != '') {
                $filename = $directory.$lang_iso.'-default-'.$image_size.'.jpg';
            } else {
                $filename = $directory.$lang_iso.'.jpg';
            }
            $filename_exists = file_exists($filename);
            return $this->manageDeclinatedImagesCRUD($filename_exists, $filename, $normal_image_sizes, $directory);// @todo : [feature] @see todo#1
        }
    }

    protected function manageListDeclinatedImages($directory, $normal_image_sizes)
    {
        // Check if method is allowed
        if ($this->wsObject->method != 'GET') {
            throw new WebserviceException('This method is not allowed for listing images.', array(55, 405));
        }
        $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('image_types', array());
        foreach ($normal_image_sizes as $image_size) {
            $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('image_type', array(), array('id' => $image_size['id_image_type'], 'name' => $image_size['name'], 'xlink_resource'=>$this->wsObject->wsUrl.'image_types/'.$image_size['id_image_type']), false);
        }
        $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('image_types', array());
        $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('images', array());

        if ($this->imageType == 'products') {
            $ids = array();
            $images = Image::getAllImages();
            foreach ($images as $image) {
                $ids[] = $image['id_product'];
            }
            $ids = array_unique($ids, SORT_NUMERIC);
            asort($ids);
            foreach ($ids as $id) {
                $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('image', array(), array('id' => $id, 'xlink_resource'=>$this->wsObject->wsUrl.'images/'.'room_types'.'/'.$id), false);
            }
        } else if ($this->imageType == 'hotels') {
            $ids = array();
            $objHotelImage = new HotelImage();
            $images = $objHotelImage->getAllImages();
            foreach ($images as $image) {
                $ids[] = $image['id_hotel'];
            }
            $ids = array_unique($ids, SORT_NUMERIC);
            foreach ($ids as $id) {
                $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('image', array(), array('id' => $id, 'xlink_resource'=>$this->wsObject->wsUrl.'images/'.'hotels'.'/'.$id), false);
            }
        } else {
            $nodes = scandir($directory);
            foreach ($nodes as $node) {
                // avoid too much preg_match...
                if ($node != '.' && $node != '..' && $node != '.svn') {
                    if ($this->imageType != 'products') {
                        preg_match('/^(\d+)\.jpg*$/Ui', $node, $matches);
                        if (isset($matches[1])) {
                            $id = $matches[1];
                            $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('image', array(), array('id' => $id, 'xlink_resource'=>$this->wsObject->wsUrl.'images/'.$this->imageType.'/'.$id), false);
                        }
                    }
                }
            }
        }
        $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('images', array());
        return true;
    }

    protected function manageEntityDeclinatedImages($directory, $normal_image_sizes)
    {
        $normal_image_size_names = array();
        foreach ($normal_image_sizes as $normal_image_size) {
            $normal_image_size_names[] = $normal_image_size['name'];
        }
        // If id is detected
        $object_id = $this->wsObject->urlSegment[2];
        if (!Validate::isUnsignedId($object_id)) {
            throw new WebserviceException('The image id is invalid. Please set a valid id or the "default" value', array(60, 400));
        }

        // For the product case
        if ($this->imageType == 'products') {
            // Get available image ids
            $available_image_ids = array();

            // New Behavior
            foreach (Language::getIDs() as $id_lang) {
                foreach (Image::getImages($id_lang, $object_id) as $image) {
                    $available_image_ids[] = $image['id_image'];
                }
            }
            $available_image_ids = array_unique($available_image_ids, SORT_NUMERIC);

            // If an image id is specified
            if ($this->wsObject->urlSegment[3] != '') {
                if ($this->wsObject->urlSegment[3] == 'bin') {
                    $current_product = new Product($object_id);
                    $this->wsObject->urlSegment[3] = $current_product->getCoverWs();
                }
                if (!Validate::isUnsignedId($object_id) || !in_array($this->wsObject->urlSegment[3], $available_image_ids)) {
                    throw new WebserviceException('This image id does not exist', array(57, 400));
                } else {

                    // Check for new image system
                    $image_id = $this->wsObject->urlSegment[3];
                    $path = implode('/', str_split((string)$image_id));
                    $image_size = $this->wsObject->urlSegment[4];

                    if (file_exists($directory.$path.'/'.$image_id.(strlen($this->wsObject->urlSegment[4]) > 0 ? '-'.$this->wsObject->urlSegment[4] : '').'.jpg')) {
                        $filename = $directory.$path.'/'.$image_id.(strlen($this->wsObject->urlSegment[4]) > 0 ? '-'.$this->wsObject->urlSegment[4] : '').'.jpg';
                        $orig_filename = $directory.$path.'/'.$image_id.'.jpg';
                    } else {
                        // else old system or not exists

                        $orig_filename = $directory.$object_id.'-'.$image_id.'.jpg';
                        $filename = $directory.$object_id.'-'.$image_id.'-'.$image_size.'.jpg';
                    }
                }
            }
            // display the list of declinated images
            elseif ($this->wsObject->method == 'GET' || $this->wsObject->method == 'HEAD') {
                if ($available_image_ids) {
                    $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('image', array(), array('id'=>$object_id));
                    foreach ($available_image_ids as $available_image_id) {
                        $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('declination', array(), array('id'=>$available_image_id, 'xlink_resource'=>$this->wsObject->wsUrl.'images/'.'room_types'.'/'.$object_id.'/'.$available_image_id), false);
                    }
                    $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('image', array());
                } else {
                    $this->objOutput->setStatus(404);
                    $this->wsObject->setOutputEnabled(false);
                }
            }
        } elseif ($this->imageType == 'hotels') {
            // Get available image ids
            $available_image_ids = array();
            $objHotelImage = new HotelImage();

            foreach (Language::getIDs() as $id_lang) {
                foreach ($objHotelImage->getImagesByHotelId($object_id) as $image) {
                    $available_image_ids[] = $image['id'];
                }
            }
            $available_image_ids = array_unique($available_image_ids, SORT_NUMERIC);

            if ($this->wsObject->urlSegment[3] != '') {
                if ($this->wsObject->urlSegment[3] == 'bin') {
                    $hotelCoverImage = HotelImage::getCover($object_id);
                    $this->wsObject->urlSegment[3] = $hotelCoverImage['id'];
                }
                if (!Validate::isUnsignedId($object_id) || !in_array($this->wsObject->urlSegment[3], $available_image_ids)) {
                    throw new WebserviceException('This image id does not exist', array(57, 400));
                } else {
                    $image_id = $this->wsObject->urlSegment[3];
                    $path = $object_id;
                    $image_size = $this->wsObject->urlSegment[4];
                    if (file_exists($directory.$path.'/'.$image_id.(strlen($this->wsObject->urlSegment[4]) > 0 ? '-'.$this->wsObject->urlSegment[4] : '').'.jpg')) {
                        $filename = $directory.$path.'/'.$image_id.(strlen($this->wsObject->urlSegment[4]) > 0 ? '-'.$this->wsObject->urlSegment[4] : '').'.jpg';

                        $orig_filename = $directory.$path.'/'.$image_id.'.jpg';
                    }
                }
            } elseif ($this->wsObject->method == 'GET' || $this->wsObject->method == 'HEAD') {
                if ($available_image_ids) {
                    $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('image', array(), array('id'=>$object_id));
                    foreach ($available_image_ids as $available_image_id) {
                        $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('declination', array(), array('id'=>$available_image_id, 'xlink_resource'=>$this->wsObject->wsUrl.'images/'.'hotels'.'/'.$object_id.'/'.$available_image_id), false);
                    }
                    $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('image', array());
                } else {
                    $this->objOutput->setStatus(404);
                    $this->wsObject->setOutputEnabled(false);
                }
            }
        }

        // for all other cases
        else {
            $orig_filename = $directory.$object_id.'.jpg';
            if ($this->imageType == 'features') {
                $filename = $orig_filename;
            } else {
                $image_size = $this->wsObject->urlSegment[3];
                $filename = $directory.$object_id.'-'.$image_size.'.jpg';
            }
        }
        // in case of declinated images list of a product is get
        if ($this->output != '') {
            return true;
        }

        // If a size was given try to display it
        elseif (isset($image_size) && $image_size != '') {
            // Check the given size
            if ($this->imageType == 'products' && $image_size == 'bin') {
                $filename = $directory.$object_id.'-'.$image_id.'.jpg';
            } elseif (!in_array($image_size, $normal_image_size_names)) {
                $exception = new WebserviceException('This image size does not exist', array(58, 400));
                throw $exception->setDidYouMean($image_size, $normal_image_size_names);
            }

            if (!file_exists($filename)) {
                throw new WebserviceException('This image does not exist on disk', array(59, 500));
            }

            // Display the resized specific image
            $this->imgToDisplay = $filename;
            return true;
        }

        // Management of the original image (GET, PUT, POST, DELETE)
        elseif (isset($orig_filename)) {
            $orig_filename_exists = file_exists($orig_filename);
            return $this->manageDeclinatedImagesCRUD($orig_filename_exists, $orig_filename, $normal_image_sizes, $directory);
        } else {
            return $this->manageDeclinatedImagesCRUD(false, '', $normal_image_sizes, $directory);
        }
    }

    /**
     * Management of normal images (as categories, suppliers, manufacturers and stores)
     *
     * @param string $directory the file path of the root of the images folder type
     * @return bool
     */
    protected function manageDeclinatedImages($directory)
    {
        // Get available image sizes for the current image type
        $normal_image_sizes = array();
        if ($this->imageType != 'features') {
            $normal_image_sizes = ImageType::getImagesTypes($this->imageType);
        }
        switch ($this->wsObject->urlSegment[2]) {
            // Match the default images
            case 'default':
                return $this->manageDefaultDeclinatedImages($directory, $normal_image_sizes);
                break;
            // Display the list of images
            case '':
                return $this->manageListDeclinatedImages($directory, $normal_image_sizes);
                break;
            default:
                return $this->manageEntityDeclinatedImages($directory, $normal_image_sizes);
                break;
        }
    }

    protected function manageProductImages()
    {
        $this->manageDeclinatedImages(_PS_PROD_IMG_DIR_);
    }

    protected function manageHotelImages()
    {
        $this->manageDeclinatedImages(_PS_HOTEL_IMG_DIR_);
    }

    protected function getCustomizations()
    {
        $customizations = array();
        if (!$results = Db::getInstance()->executeS('
			SELECT DISTINCT c.`id_customization`
			FROM `'._DB_PREFIX_.'customization` c
			NATURAL JOIN `'._DB_PREFIX_.'customization_field` cf
			WHERE c.`id_cart` = '.(int)$this->wsObject->urlSegment[2].'
			AND type = 0')) {
            return array();
        }
        foreach ($results as $result) {
            $customizations[] = $result['id_customization'];
        }
        return $customizations;
    }

    protected function manageCustomizationImages()
    {
        $normal_image_sizes = ImageType::getImagesTypes($this->imageType);
        if (empty($this->wsObject->urlSegment[2])) {
            $results = Db::getInstance()->executeS('SELECT DISTINCT `id_cart` FROM `'._DB_PREFIX_.'customization`');
            foreach ($results as $result) {
                $ids[] = $result['id_cart'];
            }
            asort($ids);
            $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('carts', array());
            foreach ($ids as $id) {
                $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('cart', array(), array('id' => $id, 'xlink_resource'=>$this->wsObject->wsUrl.'images/'.$this->imageType.'/'.$id), false);
            }
            $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('carts', array());
            return true;
        } elseif (empty($this->wsObject->urlSegment[3])) {
            $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('customizations', array());
            $customizations = $this->getCustomizations();
            foreach ($customizations as $id) {
                $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('customization', array(), array('id' => $id, 'xlink_resource'=>$this->wsObject->wsUrl.'images/'.$this->imageType.'/'.$id), false);
            }
            $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('customizations', array());
            return true;
        } elseif (empty($this->wsObject->urlSegment[4])) {
            if ($this->wsObject->method == 'GET') {
                $results = Db::getInstance()->executeS(
                    'SELECT *
					FROM `'._DB_PREFIX_.'customized_data`
					WHERE id_customization = '.(int)$this->wsObject->urlSegment[3].' AND type = 0');

                $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('images', array());
                foreach ($results as $result) {
                    $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('image', array(), array('id' => $result['index'], 'xlink_resource'=>$this->wsObject->wsUrl.'images/'.$this->imageType.'/'.$result['index']), false);
                }
                $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('images', array());
                return true;
            }
        } else {
            if ($this->wsObject->method == 'GET') {
                $results = Db::getInstance()->executeS(
                    'SELECT *
					FROM `'._DB_PREFIX_.'customized_data`
					WHERE id_customization = '.(int)$this->wsObject->urlSegment[3].'
					AND `index` = '.(int)$this->wsObject->urlSegment[4]);
                if (empty($results[0]) || empty($results[0]['value'])) {
                    throw new WebserviceException('This image does not exist on disk', array(61, 500));
                }
                $this->imgToDisplay = _PS_UPLOAD_DIR_.$results[0]['value'];
                return true;
            }
            if ($this->wsObject->method == 'POST') {
                $customizations = $this->getCustomizations();
                if (!in_array((int)$this->wsObject->urlSegment[3], $customizations)) {
                    throw new WebserviceException('Customization does not exist', array(61, 500));
                }
                $results = Db::getInstance()->executeS(
                    'SELECT id_customization_field
					FROM `'._DB_PREFIX_.'customization_field`
					WHERE id_customization_field = '.(int)$this->wsObject->urlSegment[4].'
					AND type = 0');
                if (empty($results)) {
                    throw new WebserviceException('Customization field does not exist.', array(61, 500));
                }
                $results = Db::getInstance()->executeS(
                    'SELECT *
					FROM `'._DB_PREFIX_.'customized_data`
					WHERE id_customization = '.(int)$this->wsObject->urlSegment[3].'
					AND `index` = '.(int)$this->wsObject->urlSegment[4].'
					AND type = 0');
                if (!empty($results)) { // customization field exists and has no value
                    throw new WebserviceException('Customization field already have a value, please use PUT method.', array(61, 500));
                }
                return $this->manageDeclinatedImagesCRUD(false, '', $normal_image_sizes, _PS_UPLOAD_DIR_);
            }
            $results = Db::getInstance()->executeS(
                'SELECT *
				FROM `'._DB_PREFIX_.'customized_data`
				WHERE id_customization = '.(int)$this->wsObject->urlSegment[3].'
				AND `index` = '.(int)$this->wsObject->urlSegment[4]);
            if (empty($results[0]) || empty($results[0]['value'])) {
                throw new WebserviceException('This image does not exist on disk', array(61, 500));
            }
            $this->imgToDisplay = _PS_UPLOAD_DIR_.$results[0]['value'];
            $filename_exists = file_exists($this->imgToDisplay);

            return $this->manageDeclinatedImagesCRUD($filename_exists, $this->imgToDisplay, $normal_image_sizes, _PS_UPLOAD_DIR_);
        }
    }

    /**
     * Management of normal images CRUD
     *
     * @param bool $filename_exists if the filename exists
     * @param string $filename the image path
     * @param array $image_sizes The
     * @param string $directory
     * @return bool
     *
     * @throws WebserviceException
     */
    protected function manageDeclinatedImagesCRUD($filename_exists, $filename, $image_sizes, $directory)
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
                    if ($this->writePostedImageOnDisk($filename, null, null, $image_sizes, $directory)) {
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
                if ($this->imageType == 'products') {
                    $image = new Image((int)$this->wsObject->urlSegment[3]);
                    return $image->delete();
                } elseif ($filename_exists) {
                    if (in_array($this->imageType, array('categories', 'manufacturers', 'suppliers', 'stores'))) {
                        /** @var ObjectModel $object */
                        $image_class = $this->wsObject->resourceList[$this->imageType]['class'];
                        $object = new $image_class((int)$this->wsObject->urlSegment[2]);
                        return $object->deleteImage(true);
                    } else {
                        return $this->deleteImageOnDisk($filename, $image_sizes, $directory);
                    }
                } else {
                    throw new WebserviceException('This image does not exist on disk', array(64, 500));
                }
                break;
            // Add the image
            case 'POST':

                if ($filename_exists) {
                    throw new WebserviceException('This image already exists. To modify it, please use the PUT method', array(65, 400));
                } else {
                    if ($this->writePostedImageOnDisk($filename, null, null, $image_sizes, $directory)) {
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

    /**
     * 	Delete the image on disk
     *
     * @param string $file_path the image file path
     * @param array $image_types The different sizes
     * @param string $parent_path The parent path
     * @return bool
     */
    protected function deleteImageOnDisk($file_path, $image_types = null, $parent_path = null)
    {
        $this->wsObject->setOutputEnabled(false);
        if (file_exists($file_path)) {
            // delete image on disk
            @unlink($file_path);
            // Delete declinated image if needed
            if ($image_types) {
                foreach ($image_types as $image_type) {
                    if ($this->defaultImage) { // @todo products images too !!
                        $declination_path = $parent_path.$this->wsObject->urlSegment[3].'-default-'.$image_type['name'].'.jpg';
                    } else {
                        $declination_path = $parent_path.$this->wsObject->urlSegment[2].'-'.$image_type['name'].'.jpg';
                    }
                    if (!@unlink($declination_path)) {
                        $this->objOutput->setStatus(204);
                        return false;
                    }
                }
            }
            return true;
        } else {
            $this->objOutput->setStatus(204);
            return false;
        }
    }

    /**
     * Write the image on disk
     *
     * @param string $base_path
     * @param string $new_path
     * @param int $dest_width
     * @param int $dest_height
     * @param array $image_types
     * @param string $parent_path
     * @return string
     *
     * @throws WebserviceException
     */
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
                if ($this->wsObject->urlSegment[1] == 'customizations') {
                    // write smaller image in case of customization image
                    $product_picture_width = (int)Configuration::get('PS_PRODUCT_PICTURE_WIDTH');
                    $product_picture_height = (int)Configuration::get('PS_PRODUCT_PICTURE_HEIGHT');
                    if (!ImageManager::resize($new_path, $new_path.'_small', $product_picture_width, $product_picture_height)) {
                        $this->errors[] = Tools::displayError('An error occurred during the image upload process.');
                    }
                }
                break;
        }
        imagedestroy($dest_image);
        if (!$imaged) {
            throw new WebserviceException(sprintf('Unable to write the image "%s".', str_replace(_PS_ROOT_DIR_, '[SHOP_ROOT_DIR]', $new_path)), array(70, 500));
        }

        // Write image declinations if present
        if ($image_types) {
            foreach ($image_types as $image_type) {
                if ($this->defaultImage) {
                    $declination_path = $parent_path.$this->wsObject->urlSegment[3].'-default-'.$image_type['name'].'.jpg';
                } else {
                    if ($this->imageType == 'products') {
                        $declination_path = $parent_path.chunk_split($this->wsObject->urlSegment[3], 1, '/').$this->wsObject->urlSegment[3].'-'.$image_type['name'].'.jpg';
                    } else {
                        $declination_path = $parent_path.$this->wsObject->urlSegment[2].'-'.$image_type['name'].'.jpg';
                    }
                }
                if (!$this->writeImageOnDisk($base_path, $declination_path, $image_type['width'], $image_type['height'])) {
                    throw new WebserviceException(sprintf('Unable to save the declination "%s" of this image.', $image_type['name']), array(71, 500));
                }
            }
        }

        Hook::exec('actionWatermark', array('id_image' => $this->wsObject->urlSegment[3], 'id_product' => $this->wsObject->urlSegment[2]));
        return $new_path;
    }

    /**
     * Write the posted image on disk
     *
     * @param string $reception_path
     * @param int $dest_width
     * @param int $dest_height
     * @param array $image_types
     * @param string $parent_path
     * @return bool
     *
     * @throws WebserviceException
     */
    protected function writePostedImageOnDisk($reception_path, $dest_width = null, $dest_height = null, $image_types = null, $parent_path = null)
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
                    throw new WebserviceException('This type of image format is not recognized, allowed formats are: '.implode('", "', $this->acceptedImgMimeTypes), array(73, 400));
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
                    $result = $this->writeImageOnDisk($tmp_name, $reception_path, $dest_width, $dest_height, $image_types, $parent_path);
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
                    if ($this->imageType == 'products') {
                        $product = new Product((int)$this->wsObject->urlSegment[2]);
                        if (!Validate::isLoadedObject($product)) {
                            throw new WebserviceException('Product '.(int)$this->wsObject->urlSegment[2].' does not exist', array(76, 400));
                        }
                        $image = new Image();
                        $image->id_product = (int)($product->id);
                        $image->position = Image::getHighestPosition($product->id) + 1;

                        if (!Image::getCover((int)$product->id)) {
                            $image->cover = 1;
                        } else {
                            $image->cover = 0;
                        }

                        if (!$image->add()) {
                            throw new WebserviceException('Error while creating image', array(76, 400));
                        }
                        if (!Validate::isLoadedObject($product)) {
                            throw new WebserviceException('Product '.(int)$this->wsObject->urlSegment[2].' does not exist', array(76, 400));
                        }
                        Hook::exec('updateProduct', array('id_product' => (int)$this->wsObject->urlSegment[2]));
                    }
                    // copy image
                    if (!isset($file['tmp_name'])) {
                        return false;
                    }
                    if ($error = ImageManager::validateUpload($file, $this->imgMaxUploadSize)) {
                        throw new WebserviceException('Bad image : '.$error, array(76, 400));
                    }

                    if ($this->imageType == 'products') {
                        $image = new Image($image->id);
                        if (!(Configuration::get('PS_OLD_FILESYSTEM') && file_exists(_PS_PROD_IMG_DIR_.$product->id.'-'.$image->id.'.jpg'))) {
                            $image->createImgFolder();
                        }

                        if (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($file['tmp_name'], $tmp_name)) {
                            throw new WebserviceException('An error occurred during the image upload', array(76, 400));
                        } elseif (!ImageManager::resize($tmp_name, _PS_PROD_IMG_DIR_.$image->getExistingImgPath().'.'.$image->image_format)) {
                            throw new WebserviceException('An error occurred while copying image', array(76, 400));
                        } else {
                            $images_types = ImageType::getImagesTypes('products');
                            foreach ($images_types as $imageType) {
                                if (!ImageManager::resize($tmp_name, _PS_PROD_IMG_DIR_.$image->getExistingImgPath().'-'.stripslashes($imageType['name']).'.'.$image->image_format, $imageType['width'], $imageType['height'], $image->image_format)) {
                                    $this->_errors[] = Tools::displayError('An error occurred while copying image:').' '.stripslashes($imageType['name']);
                                }
                            }
                        }
                        @unlink($tmp_name);
                        $this->imgToDisplay = _PS_PROD_IMG_DIR_.$image->getExistingImgPath().'.'.$image->image_format;
                        $this->objOutput->setFieldsToDisplay('full');
                        $this->output = $this->objOutput->renderEntity($image, 1);
                        $image_content = array('sqlId' => 'content', 'value' => base64_encode(file_get_contents($this->imgToDisplay)), 'encode' => 'base64');
                        $this->output .= $this->objOutput->objectRender->renderField($image_content);
                    } elseif (in_array($this->imageType, array('categories', 'manufacturers', 'suppliers', 'stores'))) {
                        if (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($file['tmp_name'], $tmp_name)) {
                            throw new WebserviceException('An error occurred during the image upload', array(76, 400));
                        } elseif (!ImageManager::resize($tmp_name, $reception_path)) {
                            throw new WebserviceException('An error occurred while copying image', array(76, 400));
                        }
                        $images_types = ImageType::getImagesTypes($this->imageType);
                        foreach ($images_types as $imageType) {
                            if (!ImageManager::resize($tmp_name, $parent_path.$this->wsObject->urlSegment[2].'-'.stripslashes($imageType['name']).'.jpg', $imageType['width'], $imageType['height'])) {
                                $this->_errors[] = Tools::displayError('An error occurred while copying image:').' '.stripslashes($imageType['name']);
                            }
                        }
                        @unlink(_PS_TMP_IMG_DIR_.$tmp_name);
                        $this->imgToDisplay = $reception_path;
                    } elseif ($this->imageType == 'customizations') {
                        $filename = md5(uniqid(rand(), true));
                        $this->imgToDisplay = _PS_UPLOAD_DIR_.$filename;
                        if (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($file['tmp_name'], $tmp_name)) {
                            throw new WebserviceException('An error occurred during the image upload', array(76, 400));
                        } elseif (!ImageManager::resize($tmp_name, $this->imgToDisplay)) {
                            throw new WebserviceException('An error occurred while copying image', array(76, 400));
                        }
                        $product_picture_width = (int)Configuration::get('PS_PRODUCT_PICTURE_WIDTH');
                        $product_picture_height = (int)Configuration::get('PS_PRODUCT_PICTURE_HEIGHT');
                        if (!ImageManager::resize($this->imgToDisplay, $this->imgToDisplay.'_small', $product_picture_width, $product_picture_height)) {
                            throw new WebserviceException('An error occurred while resizing image', array(76, 400));
                        }
                        @unlink(_PS_TMP_IMG_DIR_.$tmp_name);

                        $query = 'INSERT INTO `'._DB_PREFIX_.'customized_data` (`id_customization`, `type`, `index`, `value`)
							VALUES ('.(int)$this->wsObject->urlSegment[3].', 0, '.(int)$this->wsObject->urlSegment[4].', \''.$filename.'\')';

                        if (!Db::getInstance()->execute($query)) {
                            return false;
                        }
                    }
                    return true;
                }
            }
        } else {
            throw new WebserviceException('Method '.$this->wsObject->method.' is not allowed for an image resource', array(77, 405));
        }
    }
}
