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

class HotelImage extends ObjectModel
{
    public $id;
    public $id_hotel;
    public $id_htl_image_category;
    public $cover;
    public $source_index;
    public $image_dir;
    public $image_format = 'jpg';

    protected static $access_rights = 0755;

    public static $definition = array(
        'table' => 'htl_image',
        'primary' => 'id',
        'fields' => array(
            'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_htl_image_category' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'allow_null' => true),
            'cover' => array('type' => self::TYPE_BOOL,'validate' => 'isBool')
        ),
    );


    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);

        $this->source_index = _PS_HOTEL_IMG_DIR_.'index.php';

        $this->image_dir = _PS_HOTEL_IMG_DIR_.$this->getImgFolder();
    }

    /**
     * Deletes current hotel image from the database
     * @return bool `true` if delete was successful
     */
    public function delete()
    {
        if (!parent::delete()
            || !$this->deleteImage(true)
        ) {
            return false;
        }
        return true;
    }

    /**
     * Delete the hotel image from disk and remove the containing folder if empty
     * Handles both legacy and new image filesystems
     */
    public function deleteImage($force_delete = false)
    {
        parent::deleteImage();
        // Can we delete the image folder?
        if (is_dir($this->image_dir)) {
            $delete_folder = true;
            foreach (scandir($this->image_dir) as $file) {
                if (($file != '.' && $file != '..' && $file != 'index.php')) {
                    $delete_folder = false;
                    break;
                }
            }
        }

        if (isset($delete_folder) && $delete_folder) {
            // delete index image before deleting folder
            unlink($this->image_dir.'index.php');
            @rmdir($this->image_dir);
        }

        return true;
    }

    /**
     * [getImagesByHotelId :: To get paginated hotel images data]
     * @param  [int] $id_hotel [id_hotel to get images of]
     * @param  [int] $p [page number of the paginated images data]
     * @param  [int] $n [number of images per page for paginated images data]
     * @return [array|boolean] [if data found returns array containing information of the images of the hotel which id is passed]
     */
    public function getImagesByHotelId($id_hotel, $p = 1, $n = null, $idLang = null)
    {
        $p = (int) $p;
        $n = $n !== null ? (int) $n : $n; // n = null for no pagination
        if ($p <= 1) {
            $p = 1;
        }

        if (!$idLang) {
            $idLang = (int) Context::getContext()->language->id;
        }

        $sql = 'SELECT hi.*, hicl.`name` AS `category_name`
        FROM `'._DB_PREFIX_.'htl_image` hi
        LEFT JOIN `'._DB_PREFIX_.'htl_image_category_lang` hicl
            ON (hicl.`id_htl_image_category` = hi.`id_htl_image_category`
            AND hicl.`id_lang` = '.(int) $idLang.')
        WHERE hi.`id_hotel` = '.(int) $id_hotel.
        ($n ? ' LIMIT '.(int) (($p - 1) * $n).', '.(int) ($n) : '');

        return Db::getInstance()->executeS($sql);
    }

    // for backward compatibility, use getImagesByHotelId() instead
    public function getAllImagesByHotelId($id_hotel)
    {
        return $this->getImagesByHotelId($id_hotel);
    }

    /**
     * [deleteByHotelId :: To delete hotel's images data of a hotel by its hotel Id]
     * @param  [int] $htl_id [Id of the hotel which images data you want to delete]
     * @return [boolean]         [Returns true if deleted successfully else returns false]
     */
    public function deleteByHotelId($htl_id)
    {
        $delete = Db::getInstance()->delete('htl_image', '`id_hotel`='.(int)$htl_id);
        return $delete;
    }

    public static function getCover($idHotel)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'htl_image` WHERE `id_hotel` = '.(int)$idHotel.' AND `cover`=1'
        );
    }

    public function getImageLink($id, $type = null)
    {
        return _PS_HOTEL_IMG_.self::getImageHotelId($id).'/'.$id.($type ? '-'.$type : '' ).'.'.$this->image_format;
    }

    public function getImagePath()
    {
        if (!$this->id) {
            return false;
        }

        $path = $this->getImgFolder().$this->id;
        return $path;
    }

    public function getImgFolder()
    {
        if ($this->id) {
            if ($idHotel = self::getImageHotelId($this->id)) {
                return $idHotel.'/';
            }
        }

        return false;
    }

    public static function getImageHotelId($id)
    {
        return Db::getInstance()->getValue('
            SELECT `id_hotel`
            FROM `'._DB_PREFIX_.'htl_image`
            WHERE  `id` = '. (int)$id
        );
    }

    public function uploadHotelImages($images, $idHotel, $idHtlImageCategory = 0, $objCategory = null)
    {
        if (!isset($images['tmp_name']) || !$idHotel) {
            return false;
        }

        $isMultiple = is_array($images['tmp_name']);
        $tmpFiles = $isMultiple ? $images['tmp_name'] : array($images['tmp_name']);

        $generateHighDpiImages = (bool) Configuration::get('PS_HIGHT_DPI');
        $imagesTypes = ImageType::getImagesTypes('hotels');

        $addedImage = false;
        $allSucceeded = true;

        foreach ($tmpFiles as $tmpFile) {
            if (!is_string($tmpFile) || $tmpFile === '') {
                $allSucceeded = false;
                continue;
            }

            $objHtlImage = new HotelImage();
            $objHtlImage->id_hotel = $idHotel;
            $objHtlImage->id_htl_image_category = $idHtlImageCategory ?: null;
            $objHtlImage->cover = self::getCover($idHotel) ? 0 : 1;

            if (!$objHtlImage->save()
                || !($path = $objHtlImage->getPathForCreation())
                || !ImageManager::resize($tmpFile, $path.$objHtlImage->id.'.'.$objHtlImage->image_format)
            ) {
                $allSucceeded = false;
                continue;
            }

            foreach ($imagesTypes as $imageType) {
                if (!ImageManager::resize(
                    $tmpFile,
                    $path.$objHtlImage->id.'-'.stripslashes($imageType['name']).'.'.$objHtlImage->image_format,
                    $imageType['width'],
                    $imageType['height']
                )) {
                    continue;
                }

                if ($generateHighDpiImages) {
                    ImageManager::resize(
                        $tmpFile,
                        $path.$objHtlImage->id.'-'.stripslashes($imageType['name']).'.'.$objHtlImage->image_format,
                        (int) $imageType['width'] * 2,
                        (int) $imageType['height'] * 2
                    );
                }
            }

            if (!$isMultiple) {
                if (!$objCategory || (int) $objCategory->id !== $idHtlImageCategory) {
                    $objCategory = new HotelImageCategory($idHtlImageCategory, Context::getContext()->language->id);
                }

                $addedImage = array(
                    'id' => $objHtlImage->id,
                    'cover' => $objHtlImage->cover,
                    'id_htl_image_category' => $idHtlImageCategory,
                    'category_name' => (string) $objCategory->name,
                    'image_link' => Context::getContext()->link->getMediaLink($objHtlImage->getImageLink($objHtlImage->id)),
                );
            }
        }

        if ($isMultiple) {
            return $allSucceeded;
        }

        return $allSucceeded ? $addedImage : false;
    }

    public function getPathForCreation()
    {
        if (!$this->id) {
            return false;
        }
        $path = $this->getImgFolder();
        $this->createImgFolder();
        return _PS_HOTEL_IMG_DIR_.$path;
    }

    public function createImgFolder()
    {
        if (!$this->id) {
            return false;
        }

        if (!file_exists(_PS_HOTEL_IMG_DIR_.$this->getImgFolder())) {
            // Apparently sometimes mkdir cannot set the rights, and sometimes chmod can't. Trying both.
            $success = @mkdir(_PS_HOTEL_IMG_DIR_.$this->getImgFolder(), self::$access_rights, true);
            $chmod = @chmod(_PS_HOTEL_IMG_DIR_.$this->getImgFolder(), self::$access_rights);

            // Create an index.php file in the new folder
            if (($success || $chmod)
                && !file_exists(_PS_HOTEL_IMG_DIR_.$this->getImgFolder().'index.php')
                && file_exists($this->source_index)) {
                return @copy($this->source_index, _PS_HOTEL_IMG_DIR_.$this->getImgFolder().'index.php');
            }
        }
        return true;
    }

    public function getAllImages()
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'htl_image`');
    }
}
