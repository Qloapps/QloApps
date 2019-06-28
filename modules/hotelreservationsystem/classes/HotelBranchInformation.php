<?php
/**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class HotelBranchInformation extends ObjectModel
{
    public $id_category;
    public $hotel_name;
    public $phone;
    public $email;
    public $check_in;
    public $check_out;
    public $description;
    public $short_description;
    public $rating;
    public $city;
    public $state_id;
    public $country_id;
    public $zipcode;
    public $address;
    public $policies;
    public $active;
    public $latitude;
    public $longitude;
    public $map_formated_address;
    public $map_input_text;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_branch_info',
        'primary' => 'id',
        'multilang' => true,
        'fields' => array(
            'id_category' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'phone' => array('type' => self::TYPE_STRING,'validate' => 'isPhoneNumber', 'size' => 32),
            'email' => array('type' => self::TYPE_STRING,'validate' => 'isEmail', 'size' => 255),
            'rating' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'check_in' => array('type' => self::TYPE_STRING),
            'check_out' => array('type' => self::TYPE_STRING),
            'address' => array('type' => self::TYPE_STRING),
            'city' => array('type' => self::TYPE_STRING, 'validate' => 'isCityName', 'required' => true, 'size' => 64),
            'state_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'country_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'zipcode' => array('type' => self::TYPE_STRING),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'latitude' => array('type' => self::TYPE_FLOAT),
            'longitude' => array('type' => self::TYPE_FLOAT),
            'map_formated_address' => array('type' => self::TYPE_HTML, 'size' => 128),
            'map_input_text' => array('type' => self::TYPE_STRING, 'size' => 128),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            //lang fields
            'policies' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'lang' => true),
            'hotel_name' => array('type' => self::TYPE_STRING, 'lang' => true),
            'description' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'lang' => true),
            'short_description' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'lang' => true),
    ), );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'hotels',
        'objectNodeName' => 'htl_branch_info',
        'fields' => array(
            'currency' => array(
                'getter' => 'getWsDefaultCurrencyCode',
            ),
            'timezone' => array(
                'getter' => 'getWsTimezone',
            ),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        $this->moduleInstance = Module::getInstanceByName('hotelreservationsystem');
        parent::__construct($id, $id_lang, $id_shop);
    }

    // Web Services Code
    public function getWsDefaultCurrencyCode()
    {
        $id_currency = Configuration::get('PS_CURRENCY_DEFAULT');
        $currency = new Currency((int) $id_currency);
        return $currency->iso_code;
    }

    public function getWsTimezone()
    {
        return Configuration::get('PS_TIMEZONE');
    }

    public function hotelBranchesInfo($idLang = false, $active = 2, $detailedInfo = 0, $idHotel = 0)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        $sql = 'SELECT hbi.*, hbl.`policies`, hbl.`hotel_name`, hbl.`description`, hbl.`short_description`';
        if ($detailedInfo) {
            $sql .= ', hi.id as id_cover_img, s.`name` as `state_name`, cl.`name` as country_name';
        }
        $sql .= ' FROM `'._DB_PREFIX_.'htl_branch_info` hbi';
        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbl
        ON (hbl.`id` = hbi.`id` AND hbl.`id_lang` = '.(int)$idLang.')';
        if ($detailedInfo) {
            $sql .= ' LEFT JOIN `'._DB_PREFIX_.'htl_image` hi ON (hi.`id_hotel` = hbi.`id` AND hi.`cover` = 1)';
            $sql .= ' LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_state` = hbi.`state_id`)';
            $sql .= ' LEFT JOIN `'._DB_PREFIX_.
            'country_lang` cl ON (cl.`id_country` = hbi.`country_id` AND cl.`id_lang` = '.(int)$idLang.')';
        }
        $sql .= ' WHERE 1';
        if ($active == 1 || $active == 0) {
            $sql .= ' AND hbi.`active` = '.(int)$active;
        }
        if ($idHotel) {
            $sql .= ' AND hbi.`id` = '.(int)$idHotel;
            return Db::getInstance()->getRow($sql);
        } else {
            return Db::getInstance()->executeS($sql);
        }
    }

    /**
     * [getActiveHotelBranchesInfo : To get all the activated hotels information created by the admin].
     * @return [array | false] [If no hotel is created or activated then returns false otherwise returns all activated hotels information array]
     */
    public function getActiveHotelBranchesInfo()
    {
        $idLang = Context::getContext()->language->id;
        $sql = 'SELECT hbi.*, hbl.`policies`, hbl.`hotel_name`, hbl.`description`, hbl.`short_description`
            FROM `'._DB_PREFIX_.'htl_branch_info` hbi
            LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbl
            ON (hbl.`id` = hbi.`id` AND hbl.`id_lang` = '.(int)$idLang.')  WHERE hbi.`active` = 1';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Deprecated
     * [hotelBranchInfoById : Hotel's information By its id].
     *
     * @param [int] $id [id of the hotel which information is wanted]
     *
     * @return [array | false]     [If no hotel found with id sent then returns false otherwise returns all hotel's information array]
     */
    public function hotelBranchInfoById($id)
    {
        $idLang = Context::getContext()->language->id;
        $sql = 'SELECT hbi.*, hbl.`policies`, hbl.`hotel_name`, hbl.`description`, hbl.`short_description`
            FROM `'._DB_PREFIX_.'htl_branch_info` hbi
            LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbl
            ON (hbl.`id` = hbi.`id` AND hbl.`id_lang` = '.(int)$idLang.')  WHERE hbi.`id` = '.(int)$id;

        return Db::getInstance()->getRow($sql);
    }

    /**
     * Deprecated
     * [hotelsNameAndId : To get array of All created Hotels name and id].
     *
     * @return [array | false] [If no hotel found then returns false otherwise returns array of all hotels name and id]
     */
    public function hotelsNameAndId()
    {
        $idLang = Context::getContext()->language->id;
        $sql = 'SELECT hbi.`id`, hbl.`hotel_name` FROM `'._DB_PREFIX_.'htl_branch_info` hbi
            LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbl
            ON (hbl.`id` = hbi.`id` AND hbl.`id_lang` = '.(int)$idLang.')';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * [getUnassignedFeaturesHotelIds : To get array of hotels id and name To which no hotel features are assigned].
     *
     * @return [array | false] [If no hotel found then returns false otherwise returns array of all hotels name and id to which no features are assigned]
     */
    public function getUnassignedFeaturesHotelIds()
    {
        $idLang = Context::getContext()->language->id;
        $sql = 'SELECT hbi.`id`, hbl.`hotel_name` FROM `'._DB_PREFIX_.'htl_branch_info` hbi
            LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbl
            ON (hbl.`id` = hbi.`id` AND hbl.`id_lang` = '.(int)$idLang.')
            WHERE hbi.`id` NOT IN (SELECT DISTINCT id_hotel FROM `'._DB_PREFIX_.'htl_branch_features`)';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * [getFeaturesOfHotelByHotelId : To get assigned Features of a hotel by its id].
     *
     * @param [type] $id_hotel [id of the hotel]
     *
     * @return [array | false] [If no feature found then returns false otherwise returns array of all features assigned to the hotel]
     */
    public function getFeaturesOfHotelByHotelId($id_hotel)
    {
        return Db::getInstance()->executeS(
            'SELECT feature_id FROM `'._DB_PREFIX_.'htl_branch_features` WHERE id_hotel='.(int)$id_hotel
        );
    }

    /**
     * [getFeaturesOfHotelByHotelId : To get Hotel's id by its category id].
     *
     * @param [int] $id_category [id_category of the hotel]
     *
     * @return [array | int] [If no hotel found then returns false otherwise returns id of the hotel]
     */
    public static function getHotelIdByIdCategory($id_category)
    {
        return Db::getInstance()->getValue(
            'SELECT `id` FROM `'._DB_PREFIX_.'htl_branch_info` WHERE id_category = '.(int)$id_category
        );
    }

    /**
     * [getFeaturesOfHotelByHotelId : To get Category Information by its id_category].
     *
     * @param [int] $id_category [id of the category , Which innformation is wanted]
     *
     * @return [array | false] [If no Category found then returns false otherwise returns array of information of that category]
     */
    public function getCategoryDataByIdCategory($id_category)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'category_lang` WHERE id_category = '.(int)$id_category
        );
    }

    /**
     * [hotelBranchInfoByCategoryId : To get active Hotels which belongs to the passed category Id]
     * @param  [int] $cat_id [id of the category]
     * @return [array | false]         [retuns array of the information of hotels belongs to the category if found otherwise false]
     */
    public function hotelBranchInfoByCategoryId($cat_id)
    {
        $idLang = Context::getContext()->language->id;
        $sql = 'SELECT hbi.`id`, hbi.`id_category`, hbl.`hotel_name` FROM `'._DB_PREFIX_.'htl_branch_info` hbi
            LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbl
            ON (hbl.`id` = hbi.`id` AND hbl.`id_lang` = '.(int)$idLang.')
            WHERE hbi.`id_category` = '.(int)$cat_id.' AND `active` = 1';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * [getHotelCategoryTree : Select category info by name of the category]
     * @param  [string] $searchData [description]
     * @return [type]              [description]
     */
    public function getHotelCategoryTree($searchData)
    {
        $context = Context::getContext();
        return Db::getInstance()->executeS(
            'SELECT cl.`id_category` , cl.`name`
            FROM `'._DB_PREFIX_.'category_lang` AS cl
            INNER JOIN `'._DB_PREFIX_.'category` AS c ON (cl.`id_category` = c.`id_category`)
            WHERE cl.`name` LIKE \'%'.pSQL($searchData).'%\'
            AND c.`level_depth` NOT IN (0, 1, 5) and cl.`id_lang`='.(int)$context->language->id.'
            GROUP BY cl.`name`'
        );
    }

    /**
     * [getMapFormatHotelsInfo To get address ofthe hotel in the format to get the latitude and longitude of the place by using address]
     * @return [array] [description]
     */
    public function getMapFormatHotelsInfo($active = false, $idLang = false)
    {
        if (!$idLang) {
            $context = Context::getContext();
            $idLang = $context->language->id;
        }

        $sql = 'SELECT hbl.`hotel_name`, hbi.`phone`, hbi.`email`, hbi.`city`, cl.`name` AS country, hbi.`zipcode`,
            hbi.`address`, hbi.`latitude`, hbi.`longitude`, hbi.`map_formated_address`, hbi.`map_input_text`
            FROM `'._DB_PREFIX_.'htl_branch_info` AS hbi
            INNER JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbl
            ON (hbl.`id` = hbi.`id` AND hbl.`id_lang` = '.(int)$idLang.')
            INNER JOIN `'._DB_PREFIX_.'country_lang` AS cl
            ON (cl.`id_country` = hbi.`country_id` AND cl.`id_lang` = '.(int)$idLang.")
            WHERE hbi.`latitude` != 0 AND hbi.`longitude` != 0";

        if ($active !== false) {
            if ($active) {
                $sql .= " AND hbi.`active` = ".(int)$active;
            }
        }

        return Db::getInstance()->executeS($sql);
    }

    public function getAllHotels()
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'htl_branch_info`');
    }

    //Overrided ObjectModet::update() to update all the dependencies of the hotel
    public function update($null_values = false)
    {
        $objHotelInfo = new HotelBranchInformation($this->id);
        $oldStatus = $objHotelInfo->active;

        if ($return = parent::update()) {
            if ($oldStatus && !$this->active) {
                $objHotelRoomType = new HotelRoomType();
                $idsProduct = $objHotelRoomType->getIdProductByHotelId($this->id);
                if (isset($idsProduct) && $idsProduct) {
                    foreach ($idsProduct as $key_prod => $value_prod) {
                        $objProduct = new Product($value_prod['id_product']);
                        if ($objProduct->active) {
                            $objProduct->toggleStatus();
                        }
                    }
                }
            }
            if ($hotelsInfo = $this->hotelBranchesInfo(false, 1)) {
                // If more than one hotel then
                if (count($hotelsInfo) > 1) {
                    Configuration::updateValue('WK_HOTEL_NAME_ENABLE', 1);
                }
            }
        } else {
            return false;
        }
        return $return;
    }

    public function addCategory($name, $parent_cat = false, $group_ids, $ishotel = false, $idHotel = false)
    {
        $context = Context::getContext();
        if (!$parent_cat) {
            $parent_cat = Category::getRootCategory()->id;
        }
        // if ($ishotel && $idHotel) {
        //     if ($catIdHotel = Db::getInstance()->getValue(
        //         'SELECT `id_category` FROM `'._DB_PREFIX_.'htl_branch_info` WHERE id = '.(int)$idHotel
        //     )) {
        //         $category = new Category($catIdHotel);
        //         foreach (Language::getLanguages(true) as $lang) {
        //             if (is_array($name) && isset($name[$lang['id_lang']])) {
        //                 $catName = $name[$lang['id_lang']];
        //             } else {
        //                 $catName = $name;
        //             }
        //             $category->name[$lang['id_lang']] = $catName;
        //             $category->description[$lang['id_lang']] =  $this->moduleInstance->l(
        //                 'Hotel Branch Category',
        //                 'HotelBranchInformation'
        //             );
        //             $category->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($catName);
        //         }
        //         $category->id_parent = $parent_cat;
        //         $category->groupBox = $group_ids;
        //         $category->save();
        //         return $category->id;
        //     }
        // }
        if (is_array($name) && isset($name[Configuration::get('PS_LANG_DEFAULT')])) {
            $catName = $name[Configuration::get('PS_LANG_DEFAULT')];
        } else {
            $catName = $name;
        }
        if ($categoryExists = Category::searchByNameAndParentCategoryId(
            Configuration::get('PS_LANG_DEFAULT'),
            $catName,
            $parent_cat
        )) {
            return $categoryExists['id_category'];
        } else {
            $category = new Category();
            foreach (Language::getLanguages(true) as $lang) {
                if (is_array($name) && isset($name[$lang['id_lang']])) {
                    $catName = $name[$lang['id_lang']];
                } else {
                    $catName = $name;
                }
                $category->name[$lang['id_lang']] = $catName;
                $category->description[$lang['id_lang']] = $this->moduleInstance->l(
                    'Hotel Branch Category', 'HotelBranchInformation'
                );
                $category->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($catName);
            }
            $category->id_parent = $parent_cat;
            $category->groupBox = $group_ids;
            $category->add();
            return $category->id;
        }
    }

    //Overrided ObjectModet::delete() to delete all the dependencies of the hotel
    public function delete()
    {
        $contextController = Context::getContext()->controller;
        if ($idHotel = $this->id) {
            // room types of this hotel
            $objHotelRoomType = new HotelRoomType();
            $idsProduct = $objHotelRoomType->getIdProductByHotelId($idHotel);

            if (isset($idsProduct) && $idsProduct) {
                foreach ($idsProduct as $key_prod => $value_prod) {
                    $objProduct = new Product($value_prod['id_product']);
                    if (!$objProduct->delete()) {
                        $contextController->errors[] = $this->moduleInstance->l(
                            'Some error has occurred while deleting products of this hotel.',
                            'HotelBranchInformation'
                        );
                    }
                }
            }
            $objHotelfeatures = new HotelBranchFeatures();
            $objHotelImage = new HotelImage();
            if (!$objHotelfeatures->deleteBranchFeaturesByHotelId($idHotel)) {
                $contextController->errors[] = $this->moduleInstance->l(
                    'Some error has occurred while deleting hotel feature data.',
                    'HotelBranchInformation'
                );
            }
            $hotelAllImages = $objHotelImage->getAllImagesByHotelId($idHotel);
            if ($hotelAllImages) {
                foreach ($hotelAllImages as $key_img => $value_img) {
                    $path_img = _PS_MODULE_DIR_.'hotelreservationsystem/views/img/hotel_img/'.
                    $value_img['hotel_image_id'].'.jpg';
                    @unlink($path_img);
                }
            }
            if (!$objHotelImage->deleteByHotelId($idHotel)) {
                $contextController->errors[] = $this->moduleInstance->l(
                    'Some error has occurred while deleting images of hotel.',
                    'HotelBranchInformation'
                );
            }
            // delete hotel unused categories of this hotel
            if (!$this->deleteUnusedHotelCategories($idHotel)) {
                $contextController->errors[] = $this->moduleInstance->l(
                    'Some error has occurred while deleting unused hotel categories.',
                    'HotelBranchInformation'
                );
            }
        }
        if (!count($contextController->errors)) {
            if ($result = parent::delete()) {
                return $result;
            }
        } else {
            return false;
        }
    }

    /**
     * returns all the categories used by all the hotels
     * @param array $excludeIdHotels  [array of the hotels which categories you dont want]
     * @return [array] returns array of all the categories used by all the hotels
     */
    public function getAllHotelCategories($excludeIdHotels = array())
    {
        $hotelrelatedCategs = array();
        $sql = 'SELECT `id_category` FROM `'._DB_PREFIX_.'htl_branch_info`';
        if ($excludeIdHotels && count($excludeIdHotels)) {
            $sql .= ' WHERE `id` NOT IN ('.implode(',', $excludeIdHotels).')';
        }
        // get all the hotel name categories
        if ($hotelCategs = Db::getInstance()->executeS($sql)) {
            foreach ($hotelCategs as $rowCateg) {
                if (Validate::isLoadedObject($objCategory = new Category($rowCateg['id_category']))) {
                    if ($parentCategs = $objCategory->getParentsCategories(Configuration::get('PS_LANG_DEFAULT'))) {
                        foreach ($parentCategs as $categInfo) {
                            // enter only unique categories in the array
                            if (!in_array($categInfo['id_category'], $hotelrelatedCategs)) {
                                $hotelrelatedCategs[] = $categInfo['id_category'];
                            }
                        }
                    }
                }
            }
        }
        return $hotelrelatedCategs;
    }

    /**
     * Deletes all the unused categories created by hotel creation of a hotel
     * @param [int] $idHotel which categories you want to delete
     * @return true if all the categories will be deleted
     */
    public function deleteUnusedHotelCategories($idHotel)
    {
        if (Validate::isLoadedObject($objHotel = new HotelBranchInformation($idHotel))) {
            $idCategory = $objHotel->id_category;
            if (Validate::isLoadedObject($objCategory = new Category($idCategory))) {
                $hotelCategories = $this->getAllHotelCategories(array($idHotel));
                // check if category is not root or home category and not used by other hotels
                while ($idCategory
                    && !in_array($idCategory, $hotelCategories)
                    && $idCategory != Configuration::get('PS_HOME_CATEGORY')
                ) {
                    if ($objCategory->delete()) {
                        // continue deleting the unused parent hotel categories
                        $idCategory = $objCategory->id_parent;
                        $objCategory = new Category($idCategory);
                    }
                }
            }
        }
        return true;
    }
}
