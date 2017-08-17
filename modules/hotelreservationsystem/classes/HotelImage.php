<?php
    class HotelImage extends ObjectModel
    {
        public $id;
        public $hotel_id;
        public $hotel_image_id;
        public $active;

        public static $definition = array(
            'table' => 'htl_image',
            'primary' => 'id',
            'fields' => array(
                'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
                'hotel_image_id' => array('type' => self::TYPE_STRING),
                'active' => array('type' => self::TYPE_BOOL,'validate' => 'isBool')
            ),
        );

    /**
     * [getAllImagesByHotelId :: To get all images data of a hotel by hotel id]
     * @param  [int] $htl_id [Id of the hotel which images data you want]
     * @return [array|boolean] [if data found returns array containing information of the images of the hotel which id is passed]
     */
    public function getAllImagesByHotelId($htl_id)
    {
        $sql = "SELECT * FROM `"._DB_PREFIX_."htl_image` WHERE `id_hotel` = ".(int)$htl_id;

        $htl_images = Db::getInstance()->executeS($sql);

        if ($htl_images) {
            return $htl_images;
        } else {
            return false;
        }
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

    /**
     * [uploadOtherImages :: To upload the images of the hotel]
     * @param  [array] $image          [array containing images of the hotel]
     * @param  [int] $hotel_id       [Id of the hotel to which the images belong]
     * @param  [string] $hotel_img_path [Path where the images to be stored]
     * @return [boolean]                 [true]
     */
    public function uploadHotelImages($images, $id_hotel, $dest_path)
    {
        if (isset($images)) {
            $objHotelHelper = new HotelHelper();
            $hotelImages  = $images['tmp_name'];
            foreach ($hotelImages as $image) {
                $randName = $objHotelHelper->generateRandomCode(8);
                $imageName = $randName.'.jpg';
                if (ImageManager::resize($image, $dest_path.$imageName)) {
                    $hotelImage = new HotelImage();
                    $hotelImage->id_hotel = $id_hotel;
                    $hotelImage->hotel_image_id = $randName;
                    $hotelImage->save();
                }
            }
        }
        return true;
    }
    }
