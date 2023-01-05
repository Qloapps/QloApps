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

class HotelImage extends ObjectModel
{
    public $id;
    public $id_hotel;
    public $cover;

    public $image_format = 'jpg';

    protected static $access_rights = 0755;

    public static $definition = array(
        'table' => 'htl_image',
        'primary' => 'id',
        'fields' => array(
            'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'cover' => array('type' => self::TYPE_BOOL,'validate' => 'isBool')
        ),
    );


    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);

        $this->source_index = _PS_HOTEL_IMG_DIR_.'index.php';

        $this->image_dir = _PS_HOTEL_IMG_.$this->getImgFolder();
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
     * [getImagesByHotelId :: To get paginated hotel images data]
     * @param  [int] $id_hotel [id_hotel to get images of]
     * @param  [int] $p [page number of the paginated images data]
     * @param  [int] $n [number of images per page for paginated images data]
     * @return [array|boolean] [if data found returns array containing information of the images of the hotel which id is passed]
     */
    public function getImagesByHotelId($id_hotel, $p = 1, $n = null)
    {
        $p = (int) $p;
        $n = $n !== null ? (int) $n : $n; // n = null for no pagination
        if ($p <= 1) {
            $p = 1;
        }

        $sql = 'SELECT *
        FROM `'._DB_PREFIX_.'htl_image`
        WHERE `id_hotel` = '.(int) $id_hotel.
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

    public function uploadHotelImages($images, $idHotel)
    {
        if (isset($images) && $idHotel) {
            $objHotelHelper = new HotelHelper();
            $hotelImages  = $images['tmp_name'];
            if (is_array($images['tmp_name'])) {
                foreach ($hotelImages as $image) {
                    $objHtlImage = new HotelImage();
                    $objHtlImage->id_hotel = $idHotel;
                    if ($coverImgExist = HotelImage::getCover($idHotel)) {
                        $objHtlImage->cover = 0;
                    } else {
                        $objHtlImage->cover = 1;
                    }
                    if ($objHtlImage->save()) {
                        if ($path = $objHtlImage->getPathForCreation()) {
                            if (ImageManager::resize(
                                $image,
                                $path.$objHtlImage->id.'.'.$objHtlImage->image_format
                                )) {
                                // add hotel images in all required sizes
                                $imagesTypes = ImageType::getImagesTypes('hotels');
                                $generate_hight_dpi_images = (bool)Configuration::get('PS_HIGHT_DPI');

                                foreach ($imagesTypes as $imageType) {
                                    if (!ImageManager::resize(
                                        $image,
                                        $path.$objHtlImage->id.'-'.stripslashes($imageType['name']).'.'.$objHtlImage->image_format,
                                        $imageType['width'],
                                        $imageType['height']
                                    )) {
                                        continue;
                                    }

                                    if ($generate_hight_dpi_images) {
                                        if (!ImageManager::resize(
                                            $image,
                                            $path.$objHtlImage->id.'-'.stripslashes($imageType['name']).'.'.$objHtlImage->image_format,
                                            (int)$imageType['width']*2,
                                            (int)$imageType['height']*2
                                        )) {
                                            continue;
                                        }
                                    }
                                }


                            }
                        }
                    }
                }
            } else {
                $objHtlImage = new HotelImage();
                $objHtlImage->id_hotel = $idHotel;
                if ($coverImgExist = HotelImage::getCover($idHotel)) {
                    $objHtlImage->cover = 0;
                } else {
                    $objHtlImage->cover = 1;
                }
                if ($objHtlImage->save()) {
                    if ($path = $objHtlImage->getPathForCreation()) {
                        if (ImageManager::resize(
                            $hotelImages,
                            $path.$objHtlImage->id.'.'.$objHtlImage->image_format
                        )) {
                            $imagesTypes = ImageType::getImagesTypes('hotels');
                            $generate_hight_dpi_images = (bool)Configuration::get('PS_HIGHT_DPI');
                            foreach ($imagesTypes as $imageType) {
                                if (!ImageManager::resize(
                                    $hotelImages,
                                    $path.$objHtlImage->id.'-'.stripslashes($imageType['name']).'.'.$objHtlImage->image_format,
                                    $imageType['width'],
                                    $imageType['height']
                                )) {
                                    continue;
                                }

                                if ($generate_hight_dpi_images) {
                                    if (!ImageManager::resize(
                                        $hotelImages,
                                        $path.$objHtlImage->id.'-'.stripslashes($imageType['name']).'.'.$objHtlImage->image_format,
                                        (int)$imageType['width']*2,
                                        (int)$imageType['height']*2
                                    )) {
                                        continue;
                                    }
                                }
                            }
                            $addedImage = array(
                                'id_image' => $objHtlImage->id,
                                'cover' => $objHtlImage->cover,
                                'image_url' => Context::getContext()->link->getMediaLink($objHtlImage->getImageLink($objHtlImage->id)),
                            );
                            return $addedImage;
                        }
                    }
                }
            }
            return true;
        }
        return false;
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
