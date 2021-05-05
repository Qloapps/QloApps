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
    public $active_refund;
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
            'active_refund' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
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
        'objectNodeName' => 'hotel',
        'objectMethods' => array(
            'add' => 'addWs',
            'delete' => 'deleteWs',
            'update' => 'updateWs',
        ),

        'fields' => array(
            'id_default_image' => array(
                'getter' => 'getCoverWs',
                'xlink_resource' => array(
                    'resourceName' => 'hotel_images',
                )
            ),
            'max_order_date' => array(
                'getter' => 'getWsMaxOrderDate',
                'setter' => 'setWsMaxOrderDate',
            ),
        ),

        'associations' => array(
            'room_types' => array(
                'setter' => false,
                'resource' => 'room_types',
                'fields' => array('id' => array())
            ),
            'hotel_images' => array(
                'setter' => false,
                'resource' => 'image',
                'fields' => array('id' => array())
            ),
            'hotel_features' => array(
                'resource' => 'hotel_feature',
                'fields' => array('id' => array('required' => true))
            ),
            'hotel_refund_rules' => array(
                'resource' => 'hotel_refund_rule',
                'fields' => array('id' => array('required' => true))
            ),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        $this->moduleInstance = Module::getInstanceByName('hotelreservationsystem');
        parent::__construct($id, $id_lang, $id_shop);
    }

    public function add($autodate = true, $null_values = false)
    {
        // Add tab
        if (parent::add($autodate, $null_values)) {
            // insert accesses of the hotel
            return self::initAccess($this->id);
        }
        return false;
    }

    /** When creating a new hotel $idHotel, this add default rights to the table access
     * @param int $idHotel
     * @param Context $context
     * @return bool true if succeed
     */
    protected function initAccess($idHotel, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        /* Profile selection */
        $profiles = Db::getInstance()->executeS('SELECT `id_profile` FROM '._DB_PREFIX_.'profile WHERE `id_profile` != 1');
        if (!$profiles || empty($profiles)) {
            return true;
        }
        /* Query definition */
        $query = 'REPLACE INTO `'._DB_PREFIX_.'htl_access` (`id_profile`, `id_hotel`, `access`)';
        $query .= ' VALUES '.'(1, '.(int)$idHotel.', 1),';

        foreach ($profiles as $profile) {
            $access = 0;
            if (isset($context->employee->id_profile)) {
                $access = ($profile['id_profile'] == $context->employee->id_profile) ? 1 : 0;
            }

            $query .= ' ('.(int)$profile['id_profile'].', '.(int)$idHotel.', '.(int)$access.'),';
        }
        $query = trim($query, ', ');
        return Db::getInstance()->execute($query);
    }

    // Add profile hotel access while profile is added
    public function addHotelsAccessToProfile($idProfile)
    {
        return Db::getInstance()->execute(
            'INSERT INTO '._DB_PREFIX_.'htl_access (SELECT '.(int)$idProfile.', id, 0 FROM '
            ._DB_PREFIX_.'htl_branch_info)'
        );
    }

    // Add profile hotel access while profile is deleted
    public function deleteProfileHotelsAccess($idProfile)
    {
        return Db::getInstance()->execute(
            'DELETE FROM `'._DB_PREFIX_.'htl_access` WHERE `id_profile` = '.(int)$idProfile
        );
    }

    /**
     * get profile accessed hotels
     * @param [type] $idProfile
     * @param integer $access send 1 for allowed, 0 for not allowed and 2 form all
     * @param integer $onlyhotelIds  send 1 if only hotel ids needed
     * @return [hotelaccessInfo or id_hotel array]
     */
    public static function getProfileAccessedHotels($idProfile, $access = 2, $onlyhotelIds = 0)
    {
        $sql = 'SELECT `id_hotel` FROM `'._DB_PREFIX_.'htl_access` WHERE `id_profile` = '.(int)$idProfile;
        if ($access != 2) {
            $sql .= ' AND access = 1';
        }
        if ($hotelAccessInfo =  Db::getInstance()->executeS($sql)) {
            if ($onlyhotelIds) {
                $hotels = array();
                foreach ($hotelAccessInfo as $hotel) {
                    $hotels[] = $hotel['id_hotel'];
                }
                return $hotels;
            }
        }
        return $hotelAccessInfo;
    }

    // Add profile hotel access while profile is deleted
    public function getHotelAccess($idHotel)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'htl_access` WHERE `id_hotel` = '.(int)$idHotel
        );
    }

    /**
     * Filter associative array containing id_hotel or id prodcut as per the hotels access
     * @param [array] $data : associative array containing id_hotel or id prodcut in its elements
     * @param [int] $idProfile : id profile as pe which we have to filter data of hotel access
     * @param [int] $idAsIdHotel : consider id key in the array as id_hotel
     * @param [int] $idAsIdProduct : consider id key in the array as id_product
     * @param [int] $addAccessKey : Dont unset but set a key which define the access by hotels
     * @return [array or false]
     */
    public static function filterDataByHotelAccess(
        $dataArray,
        $idProfile,
        $idAsIdHotel = 0,
        $idAsIdProduct = 0,
        $addAccessKey = 0
    ) {
        if ($hotelAccessInfo =  Db::getInstance()->executeS(
            'SELECT `id_hotel` FROM `'._DB_PREFIX_.'htl_access` WHERE  access = 1 AND `id_profile` = '.(int)$idProfile
        )) {
            $hotels = array();
            foreach ($hotelAccessInfo as $hotel) {
                $hotels[] = $hotel['id_hotel'];
            }
            if ($hotels) {
                foreach ($dataArray as $key => $row) {
                    if (isset($row['id_hotel'])) {
                        if ($addAccessKey) {
                            $dataArray[$key]['htl_access'] = 0;
                            if (in_array($row['id_hotel'], $hotels)) {
                                $dataArray[$key]['htl_access'] = 1;
                            }
                        } else {
                            if (!in_array($row['id_hotel'], $hotels)) {
                                unset($dataArray[$key]);
                            }
                        }
                    } elseif ($idAsIdHotel && isset($row['id'])) {
                        if ($addAccessKey) {
                            $dataArray[$key]['htl_access'] = 0;
                            if (in_array($row['id'], $hotels)) {
                                $dataArray[$key]['htl_access'] = 1;
                            }
                        } else {
                            if (!in_array($row['id'], $hotels)) {
                                unset($dataArray[$key]);
                            }
                        }
                    } elseif ($idAsIdProduct && isset($row['id'])) {
                        $objRoomType = new HotelRoomType();
                        if ($roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($row['id'])) {
                            if ($addAccessKey) {
                                $dataArray[$key]['htl_access'] = 0;
                                if (in_array($roomTypeInfo['id_hotel'], $hotels)) {
                                    $dataArray[$key]['htl_access'] = 1;
                                }
                            } else {
                                if (!in_array($roomTypeInfo['id_hotel'], $hotels)) {
                                    unset($dataArray[$key]);
                                }
                            }
                        } else {
                            if ($addAccessKey) {
                                $dataArray[$key]['htl_access'] = 0;
                            } else {
                                unset($dataArray[$key]);
                            }
                        }
                    } elseif (isset($row['id_product'])) {
                        $objRoomType = new HotelRoomType();
                        if ($roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($row['id_product'])) {
                            if ($addAccessKey) {
                                $dataArray[$key]['htl_access'] = 0;
                                if (in_array($roomTypeInfo['id_hotel'], $hotels)) {
                                    $dataArray[$key]['htl_access'] = 1;
                                }
                            } else {
                                if (!in_array($roomTypeInfo['id_hotel'], $hotels)) {
                                    unset($dataArray[$key]);
                                }
                            }
                        } else {
                            if ($addAccessKey) {
                                $dataArray[$key]['htl_access'] = 0;
                            } else {
                                unset($dataArray[$key]);
                            }
                        }
                    }
                }
                return $dataArray;
            }
        }
        return array();
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
                    if (Validate::isLoadedObject($objHotelImage = new HotelImage((int) $value_img['id']))) {
                        $objHotelImage->deleteImage();
                    }
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
            // delete accesses of the hotel
            if (Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'htl_access WHERE `id_hotel` = '.(int)$this->id)
                && parent::delete()
            ) {
                return true;
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

    public function isRefundable()
    {
        return (Configuration::get('WK_ORDER_REFUND_ALLOWED') && $this->active_refund);
    }

    // Webservice getter : get virtual field id_default_image
    public function getCoverWs()
    {
        if ($result = HotelImage::getCover($this->id)) {
            // we are sending id_hotel/id_image as per the url set for the hotel images
            return $result['id_hotel'].'/'.$result['id'];
        }
        return false;
    }

    /**
    * Webservice setter : set virtual field id_default_image in hotel images
    * @return bool
    */
    public function setCoverWs($id_image)
    {
        // first unset the cover
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'htl_image` SET `cover` = NULL
			WHERE `id_hotel` = '.(int)$this->id);

        // set the sent id of the image to the cover of the hotel
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'htl_image` SET `cover` = 1 WHERE `id_image` = '.(int)$id_image);

        return true;
    }

    // Webservice:: function to prepare id parameter for hotel images in a hotel api
    public function getWsHotelImages()
    {
        $ids = array();
        if ($hotelImages =  Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'htl_image` WHERE `id_hotel` = '.(int)$this->id)) {
            foreach ($hotelImages as $key => $image) {
                // we are sending id_hotel/id_image as per the url set for the hotel images
                $ids[$key]['id'] = $image['id_hotel'].'/'.$image['id'];
            }
        }
        return $ids;
    }

    // Webservice:: function to prepare id parameter for hotel features in a hotel api
    public function getWsHotelFeatures()
    {
        return Db::getInstance()->executeS(
            'SELECT `feature_id` as `id` FROM `'._DB_PREFIX_.'htl_branch_features` WHERE `id_hotel` = '.(int)$this->id.
            ' ORDER BY `feature_id` ASC'
        );
    }

    // Webservice:: function to prepare id parameter for hotel features in a hotel api
    public function setWsHotelFeatures($branchFeatures)
    {
        Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'htl_branch_features`
			WHERE `id_hotel` = '.(int)$this->id
        );

        foreach ($branchFeatures as $feature) {
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'htl_branch_features` (`id_hotel`, `feature_id`) VALUES ('.(int)$this->id.', '.(int)$feature['id'].')');
        }

        return true;
    }

    // Webservice:: function to get hotel refund rules in a hotel api
    public function getWsHotelRefundRules()
    {
        return Db::getInstance()->executeS(
            'SELECT `id_refund_rule` as `id` FROM `'._DB_PREFIX_.'htl_branch_refund_rules` WHERE `id_hotel` = '.(int)$this->id.' ORDER BY `id_refund_rule` ASC'
        );
    }

    // Webservice:: function to set hotel refund rules in a hotel api
    public function setWsHotelRefundRules($refundRules)
    {
        Db::getInstance()->execute('
            DELETE FROM `'._DB_PREFIX_.'htl_branch_refund_rules`
            WHERE `id_hotel` = '.(int)$this->id
        );

        foreach ($refundRules as $rule) {
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'htl_branch_refund_rules` (`id_hotel`, `id_refund_rule`) VALUES ('.(int)$this->id.', '.(int)$rule['id'].')');
        }

        return true;
    }

    // Webservice :: get room types of the hotel
    public function getWsRoomTypes()
    {
        return Db::getInstance()->executeS(
            'SELECT `id_product` as `id` FROM `'._DB_PREFIX_.'htl_room_type` WHERE `id_hotel` = '.(int)$this->id.' ORDER BY `id` ASC'
        );
    }

    // Webservice :: get max order date of the hotel
    public function getWsMaxOrderDate()
    {
        return Db::getInstance()->getValue(
            'SELECT `max_order_date` FROM `'._DB_PREFIX_.'htl_order_restrict_date` WHERE `id_hotel` = '.(int)$this->id.' ORDER BY `id` ASC'
        );
    }

    // Webservice :: set max order date of the hotel
    public function setWsMaxOrderDate($maxOrderDate)
    {
        if ($this->id) {
            // delete previous
            Db::getInstance()->execute('
                DELETE FROM `'._DB_PREFIX_.'htl_order_restrict_date`
                WHERE `id_hotel` = '.(int)$this->id
            );

            // set max_order_date for the hotel from request
            return Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'htl_order_restrict_date` (`id_hotel`, `max_order_date`) VALUES ('.(int)$this->id.', \''.pSQL($maxOrderDate).'\')');
        }
    }

    // Webservice :: function will run when hotel added from API
    public function addWs($autodate = true, $null_values = false)
    {
        if ($this->add($autodate, $null_values)) {
            // set categories of the hotel
            $this->setWsHotelCategories();

            $postData = trim(file_get_contents('php://input'));
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string(utf8_decode($postData));

            $hotelData = json_decode(json_encode($xml));
            if (isset($hotelData->hotel->max_order_date)) {
                if ($maxOrderDate = $hotelData->hotel->max_order_date) {
                    $this->setWsMaxOrderDate($maxOrderDate);
                }
            }

            return true;
        }
        return false;
    }

    // Webservice :: function will run when hotel updated from API
    public function updateWs($null_values = false)
    {
        if ($this->update($null_values)) {
            // set categories of the hotel
            $this->setWsHotelCategories();

            $postData = trim(file_get_contents('php://input'));
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string(utf8_decode($postData));

            $hotelData = json_decode(json_encode($xml));
            if (isset($hotelData->hotel->max_order_date)) {
                if ($maxOrderDate = $hotelData->hotel->max_order_date) {
                    $this->setWsMaxOrderDate($maxOrderDate);
                }
            }

            return true;
        }
        return false;
    }

    // Webservice :: function will run when hotel deleted from API
    public function deleteWs()
    {
        if ($this->delete()) {
            return true;
        }
        return false;
    }

    // Webservice:: create country, state, city, hotel categories after hotel creation
    public function setWsHotelCategories()
    {
        if ($this->id) {
            // hotel categories before save categories
            $categsBeforeUpd = $this->getAllHotelCategories();


            $idLang = Configuration::get('PS_LANG_DEFAULT');
            $groupIds = array();
            if ($dataGroupIds = Group::getGroups($idLang)) {
                foreach ($dataGroupIds as $key => $value) {
                    $groupIds[] = $value['id_group'];
                }
            }
            $objCountry = new Country();
            $countryName = $objCountry->getNameById($idLang, $this->country_id);
            if ($catCountry = $this->addCategory($countryName, false, $groupIds)) {
                if ($this->state_id) {
                    $objState = new State();
                    $stateName = $objState->getNameById($this->state_id);
                    $catState = $this->addCategory($stateName, $catCountry, $groupIds);
                } else {
                    $catState = $this->addCategory($this->city, $catCountry, $groupIds);
                }
                if ($catState) {
                    if ($catCity = $this->addCategory($this->city, $catState, $groupIds)) {
                        $hotelCatName = $this->hotel_name;
                        if ($catHotel = $this->addCategory(
                            $hotelCatName, $catCity, $groupIds, 1, $this->id
                        )) {
                            $this->id_category = $catHotel;
                            $this->save();
                        }
                    }
                }
            }

            // hotel categories after save categories
            $categsAfterUpd = $this->getAllHotelCategories();

            // delete categories which not in hotel categories and also unused
            if ($unusedCategs = array_diff($categsBeforeUpd, $categsAfterUpd)) {
                if ($hotelCategories = $this->getAllHotelCategories()) {
                    foreach ($unusedCategs as $idCategory) {
                        if (!in_array($idCategory, $hotelCategories)
                            && $idCategory != Configuration::get('PS_HOME_CATEGORY')
                        ) {
                            $objCategory = new Category($idCategory);
                            $objCategory->delete();
                        }
                    }
                }
            }

            return true;
        }

        return false;
    }
}
