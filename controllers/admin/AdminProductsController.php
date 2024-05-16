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
 * @property Product $object
 */
class AdminProductsControllerCore extends AdminController
{
    /** @var int Max image size for upload
     * As of 1.5 it is recommended to not set a limit to max image size
     */
    protected $max_file_size = null;
    protected $max_image_size = null;

    protected $_category;
    /**
     * @var string name of the tab to display
     */
    protected $tab_display;
    protected $tab_display_module;

    /**
     * The order in the array decides the order in the list of tab. If an element's value is a number, it will be preloaded.
     * The tabs are preloaded from the smallest to the highest number.
     * @var array Product tabs.
     */
    protected $available_tabs = array();

    protected $default_tab = 'Informations';

    protected $available_tabs_lang = array();

    protected $position_identifier = 'id_product';

    protected $submitted_tabs;

    protected $id_current_category;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'product';
        $this->className = 'Product';
        $this->lang = true;
        $this->explicitSelect = true;
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );
        if (!Tools::getValue('id_product')) {
            $this->multishop_context_group = false;
        }

        $this->context = Context::getContext();

        // START send access query information to the admin controller
        $this->access_select = ' SELECT a.`id_product` FROM '._DB_PREFIX_.'product a';
        $this->access_join = ' LEFT JOIN '._DB_PREFIX_.'htl_room_type hrt ON (hrt.id_product = a.id_product)';
        if ($acsHtls = HotelBranchInformation::getProfileAccessedHotels($this->context->employee->id_profile, 1, 1)) {
            $this->access_where = ' WHERE (hrt.id_hotel IN ('.implode(',', $acsHtls).') OR hrt.id_hotel IS NULL)';
        }

        parent::__construct();

        $this->imageType = 'jpg';
        $this->max_file_size = (int)(Configuration::get('PS_LIMIT_UPLOAD_FILE_VALUE') * 1000000);
        $this->max_image_size = (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
        $this->allow_export = true;

        $this->available_tabs_lang = array(
            'Informations' => $this->l('Information'),
            'Prices' => $this->l('Prices'),
            'Seo' => $this->l('SEO'),
            'Images' => $this->l('Images'),
            'ServiceProduct' => $this->l('Service Products'),
            // 'Associations' => $this->l('Associations'),
            'Features' => $this->l('Features'),
            'Configuration' => $this->l('Rooms'),
            'Occupancy' => $this->l('Occupancy'),
            'LengthOfStay' => $this->l('Length of Stay'),
            'AdditionalFacilities' => $this->l('Additional Facilities'),
        );

        if ($this->context->shop->getContext() != Shop::CONTEXT_GROUP) {
            $this->available_tabs = array_merge($this->available_tabs, array(
                'Informations' => 0,
                'Prices' => 1,
                'Seo' => 2,
                // 'Associations' => 3,
                'Images' => 4,
                'Features' => 5,
                'Configuration' => 6,
                'Occupancy' => 7,
                'ServiceProduct' => 9,
                'LengthOfStay' => 10,
                'AdditionalFacilities' => 11,
            ));
        }

        // Sort the tabs that need to be preloaded by their priority number
        asort($this->available_tabs, SORT_NUMERIC);

        /* Adding tab if modules are hooked */
        $modules_list = Hook::getHookModuleExecList('displayAdminProductsExtra');
        if (is_array($modules_list) && count($modules_list) > 0) {
            foreach ($modules_list as $m) {
                // if module is setting name of the tab at the product edit page
                if (Validate::isLoadedObject($objModule = Module::getInstanceById($m['id_module']))) {
                    if (method_exists($objModule, 'moduleRoomTypeExtraTabName')) {
                        $this->available_tabs_lang['Module'.ucfirst($m['module'])] = $objModule->moduleRoomTypeExtraTabName();
                    }
                }
                // else set the display name of the product name as tab name
                if (!isset($this->available_tabs_lang['Module'.ucfirst($m['module'])])) {
                    $this->available_tabs_lang['Module'.ucfirst($m['module'])] = Module::getModuleName($m['module']);
                }
                $this->available_tabs['Module'.ucfirst($m['module'])] = 23;
            }
        }

        if (Tools::getValue('productFilter_a!id_category_default') === '') {
            $this->context->cookie->id_category_room_types_filter = false;
        }

        if (Shop::isFeatureActive() && $this->context->cookie->id_category_room_types_filter) {
            $category = new Category((int)$this->context->cookie->id_category_room_types_filter);
            if (!$category->inShop()) {
                $this->context->cookie->id_category_room_types_filter = false;
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminProducts'));
            }
        }

        // to support filter URLs with 'id_category' parameter
        if ($id_category = (int)Tools::getValue('id_category')) {
            $_POST['submitFilter'] = '1';
            $_POST['productFilter_a!id_category_default'] = $id_category;
        }

        if (($id_category = (int)Tools::getValue('id_category'))
            || ($id_category = (int) Tools::getValue('productFilter_a!id_category_default'))
        ) {
            $this->id_current_category = $id_category;
            $this->context->cookie->id_category_room_types_filter = $id_category;
        } elseif ($id_category = $this->context->cookie->id_category_room_types_filter) {
            $this->id_current_category = $id_category;
        }
        if ($this->id_current_category) {
            $this->_category = new Category((int)$this->id_current_category);
            $this->position_group_identifier = (int) $this->id_current_category;
        } else {
            $this->_category = new Category();
        }

        $join_category = false;
        if (Validate::isLoadedObject($this->_category) && empty($this->_filter)) {
            $join_category = true;
        }

        $this->_join .= '
		LEFT JOIN `'._DB_PREFIX_.'stock_available` sav ON (sav.`id_product` = a.`id_product` AND sav.`id_product_attribute` = 0
		'.StockAvailable::addSqlShopRestriction(null, null, 'sav').') ';

        $alias = 'sa';
        $alias_image = 'image_shop';

        $id_shop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP? (int)$this->context->shop->id : 'a.id_shop_default';
        $this->_join .= ' JOIN `'._DB_PREFIX_.'product_shop` sa ON (a.`id_product` = sa.`id_product` AND sa.id_shop = '.$id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'htl_room_type` hrt ON (a.`id_product` = hrt.`id_product`)
                LEFT JOIN `'._DB_PREFIX_.'htl_branch_info` hb ON (hrt.`id_hotel` = hb.`id`)
                LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbl ON (hb.`id` = hbl.`id` AND b.`id_lang` = hbl.`id_lang`)
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON ('.$alias.'.`id_category_default` = cl.`id_category` AND b.`id_lang` = cl.`id_lang` AND cl.id_shop = '.$id_shop.')
				LEFT JOIN `'._DB_PREFIX_.'shop` shop ON (shop.id_shop = '.$id_shop.')
				LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop ON (image_shop.`id_product` = a.`id_product` AND image_shop.`cover` = 1 AND image_shop.id_shop = '.$id_shop.')
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_image` = image_shop.`id_image`)
				LEFT JOIN `'._DB_PREFIX_.'product_download` pd ON (pd.`id_product` = a.`id_product` AND pd.`active` = 1)
				LEFT JOIN `'._DB_PREFIX_.'address` aa ON (aa.`id_hotel` = hb.`id`)
				LEFT JOIN `'._DB_PREFIX_.'feature_product` fp ON (fp.`id_product` = a.`id_product`)
				LEFT JOIN `'._DB_PREFIX_.'htl_room_type_demand` hrtd ON (hrtd.`id_product` = a.`id_product`)
				LEFT JOIN `'._DB_PREFIX_.'htl_room_type_service_product` hrtsp ON ((hrtsp.`element_type` = '.(int) RoomTypeServiceProduct::WK_ELEMENT_TYPE_HOTEL.' AND hrtsp.`id_element` = hrt.`id_hotel`) OR (hrtsp.`element_type` = '.(int) RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE.' AND hrtsp.`id_element` = a.`id_product`))';

        $this->_select .= ' (SELECT COUNT(hri.`id`) FROM `'._DB_PREFIX_.'htl_room_information` hri WHERE hri.`id_product` = a.`id_product`) as num_rooms, ';
        $this->_select .= 'hrt.`adults`, hrt.`children`, hrt.`max_guests`, hb.`id` as id_hotel, aa.`city`, hbl.`hotel_name`, ';
        $this->_select .= 'shop.`name` AS `shopname`, a.`id_shop_default`, ';
        $this->_select .= $alias_image.'.`id_image` AS `id_image`, cl.`name` AS `name_category`, '.$alias.'.`price`, 0 AS `price_final`, a.`is_virtual`, pd.`nb_downloadable`, sav.`quantity` AS `sav_quantity`, '.$alias.'.`active`, IF(sav.`quantity`<=0, 1, 0) AS `badge_danger`';
        $this->_select .= ', IFNULL((SELECT hap.`active` FROM `'._DB_PREFIX_.'htl_advance_payment` hap WHERE hap.`id_product` = a.`id_product`), 0) AS advance_payment';

        if ($join_category) {
            $this->_join .= ' INNER JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_product` = a.`id_product` AND cp.`id_category` = '.(int)$this->_category->id.') ';
            $this->_select .= ' , cp.`position`, ';
        }

        // show the list of the product according to the booking or service products
        $this->_where .= ' AND a.`booking_product` = 1';

        $this->_group = ' GROUP BY a.`id_product`';

        $this->fields_list = array();
        $this->fields_list['id_product'] = array(
            'title' => $this->l('ID'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'type' => 'int'
        );
        $this->fields_list['image'] = array(
            'title' => $this->l('Image'),
            'align' => 'center',
            'image' => 'p',
            'orderby' => false,
            'filter' => false,
            'search' => false
        );
        $this->fields_list['name'] = array(
            'title' => $this->l('Name'),
            'filter_key' => 'b!name'
        );
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->fields_list['shopname'] = array(
                'title' => $this->l('Default shop'),
                'filter_key' => 'shop!name',
            );
        } else {
            $hotels = HotelBranchInformation::getProfileAccessedHotels($this->context->employee->id_profile, 1);
            foreach ($hotels as $hotel) {
                $addressInfo = HotelBranchInformation::getAddress($hotel['id_hotel']);
                $this->hotelsArray[$hotel['id_hotel']] = $hotel['hotel_name'].', '.$addressInfo['city'];
            }

            $this->fields_list['hotel_name'] = array(
                'title' => $this->l('Hotel'),
                'type' => 'select',
                'multiple' => true,
                'operator' => 'or',
                'filter_key' => 'hrt!id_hotel',
                'list' => $this->hotelsArray,
                'optional' => true,
                'class' => 'chosen',
                'visible_default' => true,
            );
        }
        $this->fields_list['adults'] = array(
            'title' => $this->l('Adults'),
            'filter_key' => 'hrt!adults',
            'type' => 'range',
            'align' => 'center',
        );
        $this->fields_list['children'] = array(
            'title' => $this->l('Children'),
            'filter_key' => 'hrt!children',
            'type' => 'range',
            'align' => 'center',
        );
        // use it for total rooms
        $this->fields_list['num_rooms'] = array(
            'title' => $this->l('Total Rooms'),
            'align' => 'center',
            'type' => 'range',
            'havingFilter' => true,
        );
        $this->fields_list['price'] = array(
            'title' => $this->l('Base Price'),
            'type' => 'range',
            'validation' => 'isFloat',
            'align' => 'text-left',
            'filter_key' => 'a!price',
            'callback' => 'displayBasePrice',
        );
        $this->fields_list['price_final'] = array(
            'title' => $this->l('Final price'),
            'type' => 'price',
            'align' => 'text-left',
            'havingFilter' => true,
            'orderby' => false,
            'search' => false
        );

        $this->fields_list['active'] = array(
            'title' => $this->l('Status'),
            'active' => 'status',
            'filter_key' => $alias.'!active',
            'align' => 'text-center',
            'type' => 'select',
            'list' => array(1 => $this->l('Yes'), 0 => $this->l('No')),
            'optional' => true,
            'visible_default' => true,
            'orderby' => false
        );

        $this->fields_list['max_guests'] = array(
            'title' => $this->l('Maximum Occupancy'),
            'filter_key' => 'hrt!max_guests',
            'type' => 'range',
            'align' => 'center',
            'optional' => true,
        );

        $this->fields_list['show_at_front'] = array(
            'title' => $this->l('Show at front'),
            'align' => 'text-center',
            'type' => 'bool',
            'active' => 'show_at_front',
            'optional' => true,
            'havingFilter' => true,
            'visible_default' => true,
            'orderby' => false,
        );

        if (Configuration::get('WK_ALLOW_ADVANCED_PAYMENT')) {
            $this->fields_list['advance_payment'] = array(
                'title' => $this->l('Advance Payment'),
                'callback' => 'getAdvancePaymentBadge',
                'align' => 'text-center',
                'type' => 'bool',
                'optional' => true,
                'havingFilter' => true,
                'visible_default' => true,
                'orderby' => false,
            );
        }

        $this->statusBadge = array(
            0 => array(
                'badge' => 'badge-danger',
                'text' => $this->l('No')
            ),
            1 => array(
                'badge' => 'badge-success',
                'text' => $this->l('Yes')
            )
        );

        $taxRulesGroups = array();
        foreach (TaxRulesGroup::getTaxRulesGroups(true) as $taxRulesGroup) {
            $taxRulesGroups[$taxRulesGroup['id_tax_rules_group']] = $taxRulesGroup['name'];
        }
        $this->fields_list['id_tax_rules_group'] = array(
            'title' => $this->l('Tax Rules'),
            'align' => 'text-center',
            'type' => 'select',
            'multiple' => true,
            'operator' => 'or',
            'filter_key' => 'a!id_tax_rules_group',
            'list' => $taxRulesGroups,
            'displayed' => false,
        );

        $features = array();
        foreach (Feature::getFeatures($this->context->language->id) as $feature) {
            $features[$feature['id_feature']] = $feature['name'];
        }
        $this->fields_list['id_feature'] = array(
            'title' => $this->l('Features'),
            'align' => 'text-center',
            'type' => 'select',
            'multiple' => true,
            'operator' => 'and',
            'filter_key' => 'fp!id_feature',
            'list' => $features,
            'displayed' => false,
        );

        $objProduct = new Product();
        $allServiceProducts = $objProduct->getServiceProducts();
        $serviceProducts = array();
        foreach ($allServiceProducts as $serviceProduct) {
            $serviceProducts[$serviceProduct['id_product']] = $serviceProduct['name'];
        }
        $this->fields_list['id_service_product'] = array(
            'title' => $this->l('Service Products'),
            'align' => 'text-center',
            'type' => 'select',
            'multiple' => true,
            'operator' => 'and',
            'filter_key' => 'hrtsp!id_product',
            'list' => $serviceProducts,
            'displayed' => false,
        );

        $additionalFacilities = array();
        $objHotelRoomTypeGlobalDemand = new HotelRoomTypeGlobalDemand();
        $demands = $objHotelRoomTypeGlobalDemand->getAllDemands();
        foreach ($demands as $demand) {
            $additionalFacilities[$demand['id_global_demand']] = $demand['name'];
        }
        $this->fields_list['id_global_demand'] = array(
            'title' => $this->l('Additional Facilities'),
            'align' => 'text-center',
            'type' => 'select',
            'multiple' => true,
            'operator' => 'and',
            'filter_key' => 'hrtd!id_global_demand',
            'list' => $additionalFacilities,
            'displayed' => false,
        );

        $this->locationsAndHotels = array();
        $idLocationsCategory = Configuration::get('PS_LOCATIONS_CATEGORY');
        $this->objLocationsCategory = new Category($idLocationsCategory, $this->context->language->id);
        $nestedCategories = Category::getNestedCategories($idLocationsCategory);
        if ($nestedCategories) {
            foreach ($nestedCategories[$idLocationsCategory]['children'] as $childCategory) {
                $this->buildCategoryOptions($childCategory);
            }
        }
        $this->fields_list['id_category_default'] = array(
            'title' => $this->l('Location/Hotel'),
            'align' => 'text-center',
            'type' => 'select',
            'list' => $this->locationsAndHotels,
            'filter_key' => 'a!id_category_default',
            'displayed' => false,
        );

        // display Position column only if it is a hotel category
        if ($join_category
            && (int) $this->id_current_category
            && HotelBranchInformation::getHotelIdByIdCategory($this->id_current_category)
        ) {
            $this->_orderBy = 'position';
            $this->_orderWay = 'ASC';

            $this->fields_list['position'] = array(
                'title' => $this->l('Position'),
                'filter_key' => 'cp!position',
                'align' => 'center',
                'position' => 'position'
            );
        }
    }

    public function getAdvancePaymentBadge($val, $row)
    {
        return '<span class="badge '.$this->statusBadge[(int)$val]['badge'].'" >'.$this->statusBadge[(int)$val]['text'].'</a>';
    }


    private function buildCategoryOptions($category)
    {
        $space = str_repeat('&nbsp;', 5 * (($category['level_depth'] - $this->objLocationsCategory->level_depth) - 1));
        $this->locationsAndHotels[$category['id_category']] = $space.$category['name'];

        if (isset($category['children']) && count($category['children'])) {
            foreach ($category['children'] as $childCategory) {
                $this->buildCategoryOptions($childCategory);
            }
        }
    }

    public static function displayBasePrice($basePrice, $tr)
    {
        return Tools::displayPrice($basePrice, (int) Configuration::get('PS_CURRENCY_DEFAULT'));
    }

    public static function getQuantities($hotelName, $tr)
    {
        if ((int)$tr['is_virtual'] == 1 && $tr['nb_downloadable'] == 0) {
            return '&infin;';
        } else {
            return $echo;
        }
    }

    public function setMedia()
    {
        parent::setMedia();

        $bo_theme = ((Validate::isLoadedObject($this->context->employee)
            && $this->context->employee->bo_theme) ? $this->context->employee->bo_theme : 'default');

        if (!file_exists(_PS_BO_ALL_THEMES_DIR_.$bo_theme.DIRECTORY_SEPARATOR
            .'template')) {
            $bo_theme = 'default';
        }

        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.iframe-transport.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-process.js');
        $this->addJs(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$bo_theme.'/js/jquery.fileupload-validate.js');
        $this->addJs(__PS_BASE_URI__.'js/vendor/spin.js');
        $this->addJs(__PS_BASE_URI__.'js/vendor/ladda.js');
        $this->addJS(_PS_JS_DIR_.'/datatable/jquery.dataTables.min.js');
        $this->addJS(_PS_JS_DIR_.'/datatable/dataTables.bootstrap.js');
    }

    protected function _cleanMetaKeywords($keywords)
    {
        if (!empty($keywords) && $keywords != '') {
            $out = array();
            $words = explode(',', $keywords);
            foreach ($words as $word_item) {
                $word_item = trim($word_item);
                if (!empty($word_item) && $word_item != '') {
                    $out[] = $word_item;
                }
            }

            return ((count($out) > 0) ? implode(',', $out) : '');
        } else {
            return '';
        }
    }

    protected function filterToField($key, $filter)
    {
        if ($key == 'a!id_category_default') {
            return false;
        }

        return parent::filterToField($key, $filter);
    }

    public function processResetFilters($list_id = null)
    {
        parent::processResetFilters($list_id);

        // reset category filter
        $this->context->cookie->id_category_room_types_filter = false;
    }

    /**
     * @param Product|ObjectModel $object
     * @param string              $table
     */
    protected function copyFromPost(&$object, $table)
    {
        parent::copyFromPost($object, $table);
        if (get_class($object) != 'Product') {
            return;
        }

        /* Additional fields */
        foreach (Language::getIDs(false) as $id_lang) {
            if (isset($_POST['meta_keywords_'.$id_lang])) {
                $_POST['meta_keywords_'.$id_lang] = $this->_cleanMetaKeywords(Tools::strtolower($_POST['meta_keywords_'.$id_lang]));
                // preg_replace('/ *,? +,* /', ',', strtolower($_POST['meta_keywords_'.$id_lang]));
                $object->meta_keywords[$id_lang] = $_POST['meta_keywords_'.$id_lang];
            }
        }
        $_POST['width'] = empty($_POST['width']) ? '0' : str_replace(',', '.', $_POST['width']);
        $_POST['height'] = empty($_POST['height']) ? '0' : str_replace(',', '.', $_POST['height']);
        $_POST['depth'] = empty($_POST['depth']) ? '0' : str_replace(',', '.', $_POST['depth']);
        $_POST['weight'] = empty($_POST['weight']) ? '0' : str_replace(',', '.', $_POST['weight']);

        if (Tools::getIsset('unit_price') != null) {
            $object->unit_price = str_replace(',', '.', Tools::getValue('unit_price'));
        }
        if (Tools::getIsset('ecotax') != null) {
            $object->ecotax = str_replace(',', '.', Tools::getValue('ecotax'));
        }

        if ($this->isTabSubmitted('Informations')) {
            if ($this->checkMultishopBox('available_for_order', $this->context)) {
                $object->available_for_order = (int)Tools::getValue('available_for_order');
            }

            if ($this->checkMultishopBox('show_price', $this->context)) {
                $object->show_price = $object->available_for_order ? 1 : (int)Tools::getValue('show_price');
            }

            if ($this->checkMultishopBox('online_only', $this->context)) {
                $object->online_only = (int)Tools::getValue('online_only');
            }
        }
        if ($this->isTabSubmitted('Prices')) {
            $object->on_sale = (int)Tools::getValue('on_sale');
        }
    }

    public function checkMultishopBox($field, $context = null)
    {
        static $checkbox = null;
        static $shop_context = null;

        if ($context == null && $shop_context == null) {
            $context = Context::getContext();
        }

        if ($shop_context == null) {
            $shop_context = $context->shop->getContext();
        }

        if ($checkbox == null) {
            $checkbox = Tools::getValue('multishop_check', array());
        }

        if ($shop_context == Shop::CONTEXT_SHOP) {
            return true;
        }

        if (isset($checkbox[$field]) && $checkbox[$field] == 1) {
            return true;
        }

        return false;
    }

    public function getList($id_lang, $orderBy = null, $orderWay = null, $start = 0, $limit = null, $id_lang_shop = null)
    {
        $orderByPriceFinal = (empty($orderBy) ? ($this->context->cookie->__get($this->table.'Orderby') ? $this->context->cookie->__get($this->table.'Orderby') : 'id_'.$this->table) : $orderBy);
        $orderWayPriceFinal = (empty($orderWay) ? ($this->context->cookie->__get($this->table.'Orderway') ? $this->context->cookie->__get($this->table.'Orderby') : 'ASC') : $orderWay);
        if ($orderByPriceFinal == 'price_final') {
            $orderBy = 'id_'.$this->table;
            $orderWay = 'ASC';
        }
        parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, $this->context->shop->id);

        /* update product quantity with attributes ...*/
        $nb = count($this->_list);
        if ($this->_list) {
            $context = $this->context->cloneContext();
            $context->shop = clone($context->shop);
            /* update product final price */
            for ($i = 0; $i < $nb; $i++) {
                if (Context::getContext()->shop->getContext() != Shop::CONTEXT_SHOP) {
                    $context->shop = new Shop((int)$this->_list[$i]['id_shop_default']);
                }

                // convert price with the currency from context
                $this->_list[$i]['price'] = Tools::convertPrice($this->_list[$i]['price'], $this->context->currency, true, $this->context);
                $this->_list[$i]['price_tmp'] = Product::getPriceStatic($this->_list[$i]['id_product'], true, null,
                    (int)Configuration::get('PS_PRICE_DISPLAY_PRECISION'), null, false, true, 1, true, null, null, null, $nothing, true, true,
                    $context);
            }
        }

        if ($orderByPriceFinal == 'price_final') {
            if (strtolower($orderWayPriceFinal) == 'desc') {
                uasort($this->_list, 'cmpPriceDesc');
            } else {
                uasort($this->_list, 'cmpPriceAsc');
            }
        }
        for ($i = 0; $this->_list && $i < $nb; $i++) {
            $this->_list[$i]['price_final'] = $this->_list[$i]['price_tmp'];
            unset($this->_list[$i]['price_tmp']);
        }
    }

    protected function loadObject($opt = false)
    {
        $result = parent::loadObject($opt);
        if ($result && Validate::isLoadedObject($this->object)) {
            if (Shop::getContext() == Shop::CONTEXT_SHOP && Shop::isFeatureActive() && !$this->object->isAssociatedToShop()) {
                $default_product = new Product((int)$this->object->id, false, null, (int)$this->object->id_shop_default);
                $def = ObjectModel::getDefinition($this->object);
                foreach ($def['fields'] as $field_name => $row) {
                    if (is_array($default_product->$field_name)) {
                        foreach ($default_product->$field_name as $key => $value) {
                            $this->object->{$field_name}[$key] = $value;
                        }
                    } else {
                        $this->object->$field_name = $default_product->$field_name;
                    }
                }
            }
            $this->object->loadStockData();
        }

        return $result;
    }

    public function ajaxProcessGetCategoryTree()
    {
        $category = Configuration::get('PS_LOCATIONS_CATEGORY');
        $full_tree = Tools::getValue('fullTree', 0);
        $use_check_box = Tools::getValue('useCheckBox', 1);
        $selected = Tools::getValue('selected', array());
        $id_tree = Tools::getValue('type');
        $input_name = str_replace(array('[', ']'), '', Tools::getValue('inputName', null));

        $tree = new HelperTreeCategories('subtree_associated_categories');
        $tree->setTemplate('subtree_associated_categories.tpl')
            ->setUseCheckBox($use_check_box)
            ->setUseSearch(false)
            ->setIdTree($id_tree)
            ->setSelectedCategories($selected)
            ->setFullTree($full_tree)
            ->setChildrenOnly(true)
            ->setNoJS(true)
            ->setRootCategory($category);

        $tree->setTemplate('tree_associated_categories.tpl')
            ->setHeaderTemplate('tree_associated_header.tpl')
            ->setRootCategory(Configuration::get('PS_LOCATIONS_CATEGORY'))
            ->setUseCheckBox(true)
            ->setUseSearch(false)
            ->setFullTree(0)
            ->setSelectedCategories($selected)
            ->setUseBulkActions(false)
            ->setDisablAllCategories(true);

        if ($input_name) {
            $tree->setInputName($input_name);
        }

        die($tree->render());
    }

    public function ajaxProcessGetCountriesOptions()
    {
        if (!$res = Country::getCountriesByIdShop((int)Tools::getValue('id_shop'), (int)$this->context->language->id)) {
            return;
        }

        $tpl = $this->createTemplate('specific_prices_shop_update.tpl');
        $tpl->assign(array(
            'option_list' => $res,
            'key_id' => 'id_country',
            'key_value' => 'name'
            )
        );

        $this->content = $tpl->fetch();
    }

    public function ajaxProcessGetCurrenciesOptions()
    {
        if (!$res = Currency::getCurrenciesByIdShop((int)Tools::getValue('id_shop'))) {
            return;
        }

        $tpl = $this->createTemplate('specific_prices_shop_update.tpl');
        $tpl->assign(array(
            'option_list' => $res,
            'key_id' => 'id_currency',
            'key_value' => 'name'
            )
        );

        $this->content = $tpl->fetch();
    }

    public function ajaxProcessGetGroupsOptions()
    {
        if (!$res = Group::getGroups((int)$this->context->language->id, (int)Tools::getValue('id_shop'))) {
            return;
        }

        $tpl = $this->createTemplate('specific_prices_shop_update.tpl');
        $tpl->assign(array(
            'option_list' => $res,
            'key_id' => 'id_group',
            'key_value' => 'name'
            )
        );

        $this->content = $tpl->fetch();
    }

    public function processDeleteVirtualProduct()
    {
        if (!($id_product_download = ProductDownload::getIdFromIdProduct((int)Tools::getValue('id_product')))) {
            $this->errors[] = Tools::displayError('Cannot retrieve file');
        } else {
            $product_download = new ProductDownload((int)$id_product_download);

            if (!$product_download->deleteFile((int)$id_product_download)) {
                $this->errors[] = Tools::displayError('Cannot delete file');
            } else {
                $this->redirect_after = self::$currentIndex.'&id_product='.(int)Tools::getValue('id_product').'&updateproduct&key_tab=VirtualProduct&conf=1&token='.$this->token;
            }
        }

        $this->display = 'edit';
        $this->tab_display = 'VirtualProduct';
    }

    public function processDuplicate()
    {
        if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product')))) {
            $id_product_old = $product->id;
            if (empty($product->price) && Shop::getContext() == Shop::CONTEXT_GROUP) {
                $shops = ShopGroup::getShopsFromGroup(Shop::getContextShopGroupID());
                foreach ($shops as $shop) {
                    if ($product->isAssociatedToShop($shop['id_shop'])) {
                        $product_price = new Product($id_product_old, false, null, $shop['id_shop']);
                        $product->price = $product_price->price;
                    }
                }
            }
            unset($product->id);
            unset($product->id_product);
            $product->indexed = 0;
            $product->active = 0;
            // suffix 'Duplicate' if same hotel
            $id_hotel_new = Tools::getValue('id_hotel');
            $obj_hotel_room_type = new HotelRoomType();
            $room_type_info = $obj_hotel_room_type->getRoomTypeInfoByIdProduct($id_product_old);
            if ($room_type_info && $room_type_info['id_hotel'] == $id_hotel_new) {
                foreach (Language::getLanguages(true) as $language) {
                    $product->name[$language['id_lang']] = $product->name[$language['id_lang']].
                    ' - '.$this->l('Duplicate');
                }
            }

            // update `id_category_default`
            $objHotelBranchInformation = new HotelBranchInformation($id_hotel_new);
            $product->id_category_default = $objHotelBranchInformation->id_category;

            // update lang fields
            foreach (Language::getLanguages(true) as $language) {
                $product->link_rewrite[$language['id_lang']] = Tools::str2url($product->name[$language['id_lang']]);
            }
            if ($product->add()
                // && Category::duplicateProductCategories($id_product_old, $product->id)
                && Product::duplicateSuppliers($id_product_old, $product->id)
                && ($combination_images = Product::duplicateAttributes($id_product_old, $product->id)) !== false
                && GroupReduction::duplicateReduction($id_product_old, $product->id)
                && Product::duplicateAccessories($id_product_old, $product->id)
                && Product::duplicateFeatures($id_product_old, $product->id)
                && Pack::duplicate($id_product_old, $product->id)
                && Product::duplicateCustomizationFields($id_product_old, $product->id)
                && Product::duplicateTags($id_product_old, $product->id)
                && Product::duplicateDownload($id_product_old, $product->id)
            ) {
                $obj_hotel_room_type = new HotelRoomType();
                $room_type_info = $obj_hotel_room_type->getRoomTypeInfoByIdProduct($id_product_old);
                $id_room_type_old = $room_type_info['id'];
                if (!$id_hotel_new) {
                    $id_hotel_new = $room_type_info['id_hotel'];
                }
                $id_room_type_new = HotelRoomType::duplicateRoomType(
                    $id_product_old,
                    $product->id,
                    $id_hotel_new,
                    true
                );
                if ($id_room_type_new) {
                    if (!HotelRoomType::duplicateRooms(
                        $id_product_old,
                        $id_room_type_new,
                        $product->id,
                        $id_hotel_new
                    )) {
                        $this->errors[] = Tools::displayError('An error occurred while duplicating rooms.');
                    }
                    if (!HotelRoomTypeDemand::duplicateRoomTypeDemands($id_product_old, $product->id)) {
                        $this->errors[] = Tools::displayError(
                            'An error occurred while duplicating additional facilities.'
                        );
                    }
                } else {
                    $this->errors[] = Tools::displayError('An error occurred while duplicating room type.');
                }
                if ($product->hasAttributes()) {
                    Product::updateDefaultAttribute($product->id);
                } else {
                    Product::duplicateSpecificPrices($id_product_old, $product->id);
                }

                if (!Tools::getValue('noimage') && !Image::duplicateProductImages($id_product_old, $product->id, $combination_images)) {
                    $this->errors[] = Tools::displayError('An error occurred while copying images.');
                } else {
                    Hook::exec('actionProductAdd', array('id_product' => (int)$product->id, 'product' => $product));
                    if (in_array($product->visibility, array('both', 'search')) && Configuration::get('PS_SEARCH_INDEXATION')) {
                        Search::indexation(false, $product->id);
                    }
                    $this->redirect_after = self::$currentIndex.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&conf=19&token='.$this->token;
                }
            } else {
                $this->errors[] = Tools::displayError('An error occurred while creating an object.');
            }
        }
    }

    public function processDelete()
    {
        if (Validate::isLoadedObject($object = $this->loadObject()) && isset($this->fieldImageSettings)) {
            /** @var Product $object */
            // check if request at least one object with noZeroObject
            if (isset($object->noZeroObject) && count($taxes = call_user_func(array($this->className, $object->noZeroObject))) <= 1) {
                $this->errors[] = Tools::displayError('You need at least one object.').' <b>'.$this->table.'</b><br />'.Tools::displayError('You cannot delete all of the items.');
            } else {
                /*
                 * @since 1.5.0
                 * It is NOT possible to delete a product if there are currently:
                 * - physical stock for this product
                 * - supply order(s) for this product
                 */
                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $object->advanced_stock_management) {
                    $stock_manager = StockManagerFactory::getManager();
                    $physical_quantity = $stock_manager->getProductPhysicalQuantities($object->id, 0);
                    $real_quantity = $stock_manager->getProductRealQuantities($object->id, 0);
                    if ($physical_quantity > 0 || $real_quantity > $physical_quantity) {
                        $this->errors[] = Tools::displayError('You cannot delete this room type because there is physical stock left.');
                    }
                }

                if (!count($this->errors)) {
                    if ($object->delete()) {
                        $id_category = (int)Tools::getValue('id_category');
                        $category_url = empty($id_category) ? '' : '&id_category='.(int)$id_category;
                        PrestaShopLogger::addLog(sprintf($this->l('%s deletion', 'AdminTab', false, false), $this->className), 1, null, $this->className, (int)$object->id, true, (int)$this->context->employee->id);
                        $this->redirect_after = self::$currentIndex.'&conf=1&token='.$this->token.$category_url;
                    } else {
                        $this->errors[] = Tools::displayError('An error occurred during deletion.');
                    }
                }
            }
        } else {
            $this->errors[] = Tools::displayError('An error occurred while deleting the object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
        }
    }

    public function processImage()
    {
        $id_image = (int)Tools::getValue('id_image');
        $image = new Image((int)$id_image);
        if (Validate::isLoadedObject($image)) {
            /* Update product image/legend */
            // @todo : move in processEditProductImage
            if (Tools::getIsset('editImage')) {
                if ($image->cover) {
                    $_POST['cover'] = 1;
                }

                $_POST['id_image'] = $image->id;
            } elseif (Tools::getIsset('coverImage')) {
                /* Choose product cover image */
                Image::deleteCover($image->id_product);
                $image->cover = 1;
                if (!$image->update()) {
                    $this->errors[] = Tools::displayError('You cannot change the product\'s cover image.');
                } else {
                    $productId = (int)Tools::getValue('id_product');
                    @unlink(_PS_TMP_IMG_DIR_.'product_'.$productId.'.jpg');
                    @unlink(_PS_TMP_IMG_DIR_.'product_mini_'.$productId.'_'.$this->context->shop->id.'.jpg');
                    $this->redirect_after = self::$currentIndex.'&id_product='.$image->id_product.'&id_category='.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&action=Images&addproduct'.'&token='.$this->token;
                }
            } elseif (Tools::getIsset('imgPosition') && Tools::getIsset('imgDirection')) {
                /* Choose product image position */
                $image->updatePosition(Tools::getValue('imgDirection'), Tools::getValue('imgPosition'));
                $this->redirect_after = self::$currentIndex.'&id_product='.$image->id_product.'&id_category='.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&add'.$this->table.'&action=Images&token='.$this->token;
            }
        } else {
            $this->errors[] = Tools::displayError('The image could not be found. ');
        }
    }

    protected function processBulkDelete()
    {
        if ($this->tabAccess['delete'] === '1') {
            if (is_array($this->boxes) && !empty($this->boxes)) {
                $object = new $this->className();

                if (isset($object->noZeroObject) &&
                    // Check if all object will be deleted
                    (count(call_user_func(array($this->className, $object->noZeroObject))) <= 1 || count($_POST[$this->table.'Box']) == count(call_user_func(array($this->className, $object->noZeroObject))))) {
                    $this->errors[] = Tools::displayError('You need at least one object.').' <b>'.$this->table.'</b><br />'.Tools::displayError('You cannot delete all of the items.');
                } else {
                    $success = 1;
                    $products = Tools::getValue($this->table.'Box');
                    if (is_array($products) && ($count = count($products))) {
                        // Deleting products can be quite long on a cheap server. Let's say 1.5 seconds by product (I've seen it!).
                        if (intval(ini_get('max_execution_time')) < round($count * 1.5)) {
                            ini_set('max_execution_time', round($count * 1.5));
                        }

                        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                            $stock_manager = StockManagerFactory::getManager();
                        }

                        foreach ($products as $id_product) {
                            $product = new Product((int)$id_product);
                            /*
                             * @since 1.5.0
                             * It is NOT possible to delete a product if there are currently:
                             * - physical stock for this product
                             * - supply order(s) for this product
                             */
                            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $product->advanced_stock_management) {
                                $physical_quantity = $stock_manager->getProductPhysicalQuantities($product->id, 0);
                                $real_quantity = $stock_manager->getProductRealQuantities($product->id, 0);
                                if ($physical_quantity > 0 || $real_quantity > $physical_quantity) {
                                    $this->errors[] = sprintf(Tools::displayError('You cannot delete the room type #%d because there is physical stock left.'), $product->id);
                                }
                            }
                            if (!count($this->errors)) {
                                if ($product->delete()) {
                                    PrestaShopLogger::addLog(sprintf($this->l('%s deletion', 'AdminTab', false, false), $this->className), 1, null, $this->className, (int)$product->id, true, (int)$this->context->employee->id);
                                } else {
                                    $success = false;
                                }
                            } else {
                                $success = 0;
                            }
                        }
                    }

                    if ($success) {
                        $id_category = (int)Tools::getValue('id_category');
                        $category_url = empty($id_category) ? '' : '&id_category='.(int)$id_category;
                        $this->redirect_after = self::$currentIndex.'&conf=2&token='.$this->token.$category_url;
                    } else {
                        $this->errors[] = Tools::displayError('An error occurred while deleting this selection.');
                    }
                }
            } else {
                $this->errors[] = Tools::displayError('You must select at least one element to delete.');
            }
        } else {
            $this->errors[] = Tools::displayError('You do not have permission to delete this.');
        }
    }

    public function processFeatures()
    {
        if (!Feature::isFeatureActive()) {
            return;
        }

        if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product')))) {
            // delete all objects
            $product->deleteFeatures();

            // add new objects
            $languages = Language::getLanguages(false);
            foreach ($_POST as $key => $val) {
                if (preg_match('/^feature_([0-9]+)_check/i', $key, $match)) {
                    if ($val) {
                        $product->addFeaturesToDB($match[1], $val);
                    } else {
                        if ($default_value = $this->checkFeatures($languages, $match[1])) {
                            $id_value = $product->addFeaturesToDB($match[1], 0, 1);
                            foreach ($languages as $language) {
                                if ($cust = Tools::getValue('custom_'.$match[1].'_'.(int)$language['id_lang'])) {
                                    $product->addFeaturesCustomToDB($id_value, (int)$language['id_lang'], $cust);
                                } else {
                                    $product->addFeaturesCustomToDB($id_value, (int)$language['id_lang'], $default_value);
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $this->errors[] = Tools::displayError('A room type must be created before adding features.');
        }
    }

    /**
     * This function is never called at the moment (specific prices cannot be edited)
     */
    public function processPricesModification()
    {
        $id_specific_prices = Tools::getValue('spm_id_specific_price');
        $id_combinations = Tools::getValue('spm_id_product_attribute');
        $id_shops = Tools::getValue('spm_id_shop');
        $id_currencies = Tools::getValue('spm_id_currency');
        $id_countries = Tools::getValue('spm_id_country');
        $id_groups = Tools::getValue('spm_id_group');
        $id_customers = Tools::getValue('spm_id_customer');
        $prices = Tools::getValue('spm_price');
        $from_quantities = Tools::getValue('spm_from_quantity');
        $reductions = Tools::getValue('spm_reduction');
        $reduction_types = Tools::getValue('spm_reduction_type');
        $froms = Tools::getValue('spm_from');
        $tos = Tools::getValue('spm_to');

        foreach ($id_specific_prices as $key => $id_specific_price) {
            if ($reduction_types[$key] == 'percentage' && ((float)$reductions[$key] <= 0 || (float)$reductions[$key] > 100)) {
                $this->errors[] = Tools::displayError('Submitted reduction value (0-100) is out-of-range');
            } elseif ($this->_validateSpecificPrice($id_shops[$key], $id_currencies[$key], $id_countries[$key], $id_groups[$key], $id_customers[$key], $prices[$key], $from_quantities[$key], $reductions[$key], $reduction_types[$key], $froms[$key], $tos[$key], $id_combinations[$key])) {
                $specific_price = new SpecificPrice((int)($id_specific_price));
                $specific_price->id_shop = (int)$id_shops[$key];
                $specific_price->id_product_attribute = (int)$id_combinations[$key];
                $specific_price->id_currency = (int)($id_currencies[$key]);
                $specific_price->id_country = (int)($id_countries[$key]);
                $specific_price->id_group = (int)($id_groups[$key]);
                $specific_price->id_customer = (int)$id_customers[$key];
                $specific_price->price = (float)($prices[$key]);
                $specific_price->from_quantity = (int)($from_quantities[$key]);
                $specific_price->reduction = (float)($reduction_types[$key] == 'percentage' ? ($reductions[$key] / 100) : $reductions[$key]);
                $specific_price->reduction_type = !$reductions[$key] ? 'amount' : $reduction_types[$key];
                $specific_price->from = !$froms[$key] ? '0000-00-00 00:00:00' : $froms[$key];
                $specific_price->to = !$tos[$key] ? '0000-00-00 00:00:00' : $tos[$key];
                if (!$specific_price->update()) {
                    $this->errors[] = Tools::displayError('An error occurred while updating the specific price.');
                }
            }
        }
        if (!count($this->errors)) {
            $this->redirect_after = self::$currentIndex.'&id_product='.(int)(Tools::getValue('id_product')).(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&update'.$this->table.'&action=Prices&token='.$this->token;
        }
    }

    public function processAdvancedPayment()
    {
        if (Configuration::get('WK_ALLOW_ADVANCED_PAYMENT')) {
            // Check if a specific price has been submitted
            if (Tools::getIsset('submitPriceAddition')) {
                return;
            }

            $id_product = Tools::getValue('id_product');
            $id_adv_pmt = Tools::getValue('id_adv_pmt');
            if ($id_adv_pmt) {
                $obj_adv_pmt = new HotelAdvancedPayment($id_adv_pmt);
            } else {
                $obj_adv_pmt = new HotelAdvancedPayment();
                if ($adv_pmt_info = $obj_adv_pmt->getIdAdvPaymentByIdProduct($id_product)) {
                    // To prevent duplication from two separate tabs
                    $obj_adv_pmt = new HotelAdvancedPayment((int) $adv_pmt_info['id']);
                }
            }

            $adv_payment_active = Tools::getValue('adv_payment_active');
            $obj_adv_pmt->id_product = $id_product;
            $obj_adv_pmt->active = $adv_payment_active;

            if ($adv_payment_active) {
                $calculate_from = Tools::getValue('cal_from');

                $payment_type = Tools::getValue('payment_type');

                if ($payment_type == 1) {
                    $adv_pay_value = Tools::getValue('adv_pay_percent');
                } elseif ($payment_type == 2) {
                    $adv_pay_value = Tools::getValue('adv_pay_amount');
                }

                $adv_tax_include = Tools::getValue('adv_tax_include');

                $obj_adv_pmt->payment_type = $payment_type;
                $obj_adv_pmt->value = $adv_pay_value;

                if ($payment_type == 2) {
                    $obj_adv_pmt->id_currency = (int) Configuration::get('PS_CURRENCY_DEFAULT');
                } else {
                    $obj_adv_pmt->id_currency = '';
                }

                $obj_adv_pmt->tax_include = $adv_tax_include;
                $obj_adv_pmt->calculate_from = $calculate_from;
            } else {
                $obj_adv_pmt->payment_type = '';
                $obj_adv_pmt->value = '';
                $obj_adv_pmt->id_currency = '';
                $obj_adv_pmt->tax_include = '';
                $obj_adv_pmt->calculate_from = 0;
            }

            $obj_adv_pmt->save();
        }

        return true;
    }

    public function processPriceAddition()
    {
        // Check if a specific price has been submitted
        if (!Tools::getIsset('submitPriceAddition')) {
            return;
        }

        $id_product = Tools::getValue('id_product');
        $id_product_attribute = Tools::getValue('sp_id_product_attribute');
        $id_shop = Tools::getValue('sp_id_shop');
        $id_currency = Tools::getValue('sp_id_currency');
        $id_country = Tools::getValue('sp_id_country');
        $id_group = Tools::getValue('sp_id_group');
        $id_customer = Tools::getValue('sp_id_customer');
        $price = Tools::getValue('leave_bprice') ? '-1' : Tools::getValue('sp_price');
        $from_quantity = 1;
        $reduction = (float)(Tools::getValue('sp_reduction'));
        $reduction_tax = Tools::getValue('sp_reduction_tax');
        $reduction_type = !$reduction ? 'amount' : Tools::getValue('sp_reduction_type');
        $reduction_type = $reduction_type == '-' ? 'amount' : $reduction_type;
        $from = Tools::getValue('sp_from');
        if (!$from) {
            $from = '0000-00-00 00:00:00';
        }
        $to = Tools::getValue('sp_to');
        if (!$to) {
            $to = '0000-00-00 00:00:00';
        }

        if (($price == '-1') && ((float)$reduction == '0')) {
            $this->errors[] = Tools::displayError('No reduction value has been submitted');
        }  elseif ($to != '0000-00-00 00:00:00' && strtotime($to) < strtotime($from)) {
            $this->errors[] = Tools::displayError('Invalid date range');
        } elseif ($reduction_type == 'percentage' && ((float)$reduction <= 0 || (float)$reduction > 100)) {
            $this->errors[] = Tools::displayError('Submitted reduction value (0-100) is out-of-range');
        } elseif ($this->_validateSpecificPrice($id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_type, $from, $to, $id_product_attribute)) {
            $specificPrice = new SpecificPrice();
            $specificPrice->id_product = (int)$id_product;
            $specificPrice->id_product_attribute = (int)$id_product_attribute;
            $specificPrice->id_shop = (int)$id_shop;
            $specificPrice->id_currency = (int)($id_currency);
            $specificPrice->id_country = (int)($id_country);
            $specificPrice->id_group = (int)($id_group);
            $specificPrice->id_customer = (int)$id_customer;
            $specificPrice->price = (float)($price);
            $specificPrice->from_quantity = (int)($from_quantity);
            $specificPrice->reduction = (float)($reduction_type == 'percentage' ? $reduction / 100 : $reduction);
            $specificPrice->reduction_tax = $reduction_tax;
            $specificPrice->reduction_type = $reduction_type;
            $specificPrice->from = $from;
            $specificPrice->to = $to;
            if (!$specificPrice->add()) {
                $this->errors[] = Tools::displayError('An error occurred while updating the specific price.');
            }
        }
    }

    public function ajaxProcessDeleteSpecificPrice()
    {
        if ($this->tabAccess['delete'] === '1') {
            $id_specific_price = (int)Tools::getValue('id_specific_price');
            if (!$id_specific_price || !Validate::isUnsignedId($id_specific_price)) {
                $error = Tools::displayError('The specific price ID is invalid.');
            } else {
                $specificPrice = new SpecificPrice((int)$id_specific_price);
                if (!$specificPrice->delete()) {
                    $error = Tools::displayError('An error occurred while attempting to delete the specific price.');
                }
            }
        } else {
            $error = Tools::displayError('You do not have permission to delete this.');
        }

        if (isset($error)) {
            $json = array(
                'status' => 'error',
                'message'=> $error
            );
        } else {
            $json = array(
                'status' => 'ok',
                'message'=> $this->_conf[1]
            );
        }

        die(json_encode($json));
    }

    public function processSpecificPricePriorities()
    {
        if (!($obj = $this->loadObject())) {
            return;
        }
        if (!$priorities = Tools::getValue('specificPricePriority')) {
            $this->errors[] = Tools::displayError('Please specify priorities.');
        } elseif (Tools::isSubmit('specificPricePriorityToAll')) {
            if (!SpecificPrice::setPriorities($priorities)) {
                $this->errors[] = Tools::displayError('An error occurred while updating priorities.');
            } else {
                $this->confirmations[] = $this->l('The price rule has successfully updated');
            }
        } elseif (!SpecificPrice::setSpecificPriority((int)$obj->id, $priorities)) {
            $this->errors[] = Tools::displayError('An error occurred while setting priorities.');
        }
    }

    /**
     * Overrides parent for custom redirect link
     */
    public function processPosition()
    {
        /** @var Product $object */
        if (!Validate::isLoadedObject($object = $this->loadObject())) {
            $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.').
                ' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
        } elseif (!$object->updatePosition((int)Tools::getValue('way'), (int)Tools::getValue('position'))) {
            $this->errors[] = Tools::displayError('Failed to update the position.');
        } else {
            $category = new Category((int)Tools::getValue('id_category'));
            if (Validate::isLoadedObject($category)) {
                Hook::exec('actionCategoryUpdate', array('category' => $category));
            }
            $this->redirect_after = self::$currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&action=Customization&conf=5'.(($id_category = (Tools::getIsset('id_category') ? (int)Tools::getValue('id_category') : '')) ? ('&id_category='.$id_category) : '').'&token='.Tools::getAdminTokenLite('AdminProducts');
        }
    }
    public function ajaxProcessChangeServicePosition()
    {
        $response = array('success' => false);
        $idElement = Tools::getValue('id_element');
        $idProduct = Tools::getValue('id_product');
        if ($idElement && $idProduct) {
            $newPosition = Tools::getValue('new_position');
            if (RoomTypeServiceProduct::updatePosition(
                $idProduct,
                $idElement,
                $newPosition,
                RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE
            )) {
                $response['msg'] = $this->l('Positions updated successfully.');
                $response['success'] = true;
            }
        }
        die(json_encode($response));
    }

    public function initProcess()
    {
        if (Tools::isSubmit('submitAddproductAndStay') || Tools::isSubmit('submitAddproduct')) {
            $this->id_object = (int)Tools::getValue('id_product');
            $this->object = new Product($this->id_object);

            if ($this->isTabSubmitted('Informations') && $this->object->is_virtual && (int)Tools::getValue('type_product') != 2) {
                if ($id_product_download = (int)ProductDownload::getIdFromIdProduct($this->id_object)) {
                    $product_download = new ProductDownload($id_product_download);
                    if (!$product_download->deleteFile($id_product_download)) {
                        $this->errors[] = Tools::displayError('Cannot delete file');
                    }
                }
            }
        }

        // Delete a product in the download folder
        if (Tools::getValue('deleteVirtualProduct')) {
            if ($this->tabAccess['delete'] === '1') {
                $this->action = 'deleteVirtualProduct';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to delete this.');
            }
        }
        // Product preview
        elseif (Tools::isSubmit('submitAddProductAndPreview')) {
            $this->display = 'edit';
            $this->action = 'save';
            if (Tools::getValue('id_product')) {
                $this->id_object = Tools::getValue('id_product');
                $this->object = new Product((int)Tools::getValue('id_product'));
            }
        } elseif (Tools::isSubmit('submitAttachments')) {
            if ($this->tabAccess['edit'] === '1') {
                $this->action = 'attachments';
                $this->tab_display = 'attachments';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        }
        // Product duplication
        elseif (Tools::getIsset('duplicate'.$this->table)) {
            if ($this->tabAccess['add'] === '1') {
                $this->action = 'duplicate';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to add this.');
            }
        }
        // Toggle Show at front
        elseif (Tools::getIsset('show_at_front'.$this->table)) {
            if ($this->tabAccess['edit'] === '1') {
                $this->action = 'toggleShowAtFront';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        }
        // Product images management
        elseif (Tools::getValue('id_image') && Tools::getValue('ajax')) {
            if ($this->tabAccess['edit'] === '1') {
                $this->action = 'image';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        }
        // Product attributes management
        elseif (Tools::isSubmit('submitProductAttribute')) {
            if ($this->tabAccess['edit'] === '1') {
                $this->action = 'productAttribute';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        }
        // Product features management
        elseif (Tools::isSubmit('submitFeatures') || Tools::isSubmit('submitFeaturesAndStay')) {
            if ($this->tabAccess['edit'] === '1') {
                $this->action = 'features';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        }
        // Product specific prices management NEVER USED
        elseif (Tools::isSubmit('submitPricesModification')) {
            if ($this->tabAccess['add'] === '1') {
                $this->action = 'pricesModification';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to add this.');
            }
        } elseif (Tools::isSubmit('deleteSpecificPrice')) {
            if ($this->tabAccess['delete'] === '1') {
                $this->action = 'deleteSpecificPrice';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to delete this.');
            }
        } elseif (Tools::isSubmit('submitSpecificPricePriorities')) {
            if ($this->tabAccess['edit'] === '1') {
                $this->action = 'specificPricePriorities';
                $this->tab_display = 'prices';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        }
        // Customization management
        elseif (Tools::isSubmit('submitCustomizationConfiguration')) {
            if ($this->tabAccess['edit'] === '1') {
                $this->action = 'customizationConfiguration';
                $this->tab_display = 'customization';
                $this->display = 'edit';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('submitProductCustomization')) {
            if ($this->tabAccess['edit'] === '1') {
                $this->action = 'productCustomization';
                $this->tab_display = 'customization';
                $this->display = 'edit';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('id_product')) {
            $post_max_size = Tools::getMaxUploadSize(Configuration::get('PS_LIMIT_UPLOAD_FILE_VALUE') * 1024 * 1024);
            if ($post_max_size && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] && $_SERVER['CONTENT_LENGTH'] > $post_max_size) {
                $this->errors[] = sprintf(Tools::displayError('The uploaded file exceeds the "Maximum size for a downloadable product" set in preferences (%1$dMB) or the post_max_size/ directive in php.ini (%2$dMB).'), number_format((Configuration::get('PS_LIMIT_UPLOAD_FILE_VALUE'))), ($post_max_size / 1024 / 1024));
            }
        }

        if (!$this->action) {
            parent::initProcess();
        } else {
            $this->id_object = (int)Tools::getValue($this->identifier);
        }

        if (isset($this->available_tabs[Tools::getValue('key_tab')])) {
            $this->tab_display = Tools::getValue('key_tab');
        }

        // Set tab to display if not decided already
        if (!$this->tab_display && $this->action) {
            if (in_array($this->action, array_keys($this->available_tabs))) {
                $this->tab_display = $this->action;
            }
        }

        // And if still not set, use default
        if (!$this->tab_display) {
            if (in_array($this->default_tab, $this->available_tabs)) {
                $this->tab_display = $this->default_tab;
            } else {
                $this->tab_display = key($this->available_tabs);
            }
        }
    }

    /**
     * postProcess handle every checks before saving products information
     *
     * @return void
     */
    public function postProcess()
    {
        if (!$this->redirect_after) {
            parent::postProcess();
        }

        $this->addJS(array(
            _PS_JS_DIR_.'admin/products.js',
        ));

        if (in_array($this->display, array('add', 'edit'))
            && $this->tabAccess[$this->display] == '1'
        ) {
            $this->addJqueryUI(array(
                'ui.core',
                'ui.widget'
            ));

            $this->addjQueryPlugin(array(
                'autocomplete',
                'tablednd',
                'thickbox',
                'ajaxfileupload',
                'date',
                'tagify',
                'select2',
                'validate'
            ));

            $this->addJS(array(
                _PS_JS_DIR_.'admin/attributes.js',
                _PS_JS_DIR_.'admin/price.js',
                _PS_JS_DIR_.'tiny_mce/tiny_mce.js',
                _PS_JS_DIR_.'admin/tinymce.inc.js',
                _PS_JS_DIR_.'admin/dnd.js',
                _PS_JS_DIR_.'jquery/ui/jquery.ui.progressbar.min.js',
                _PS_JS_DIR_.'vendor/spin.js',
                _PS_JS_DIR_.'vendor/ladda.js'
            ));

            $this->addJS(_PS_JS_DIR_.'jquery/plugins/select2/select2_locale_'.$this->context->language->iso_code.'.js');
            $this->addJS(_PS_JS_DIR_.'jquery/plugins/validate/localization/messages_'.$this->context->language->iso_code.'.js');

            $this->addCSS(_PS_JS_DIR_.'jquery/plugins/timepicker/jquery-ui-timepicker-addon.css');
        }
    }

    public function ajaxPreProcess()
    {
        if (Tools::getIsset('update'.$this->table) && Tools::getIsset('id_'.$this->table)) {
            $this->display = 'edit';
            $this->action = Tools::getValue('action');
        }
    }

    public function ajaxProcessUpdateProductImageShopAsso()
    {
        $id_product = Tools::getValue('id_product');
        if (($id_image = Tools::getValue('id_image')) && ($id_shop = (int)Tools::getValue('id_shop'))) {
            if (Tools::getValue('active') == 'true') {
                $res = Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'image_shop (`id_product`, `id_image`, `id_shop`, `cover`) VALUES('.(int)$id_product.', '.(int)$id_image.', '.(int)$id_shop.', NULL)');
            } else {
                $res = Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'image_shop WHERE `id_image` = '.(int)$id_image.' AND `id_shop` = '.(int)$id_shop);
            }
        }

        // Clean covers in image table
        $count_cover_image = Db::getInstance()->getValue('
			SELECT COUNT(*) FROM '._DB_PREFIX_.'image i
			INNER JOIN '._DB_PREFIX_.'image_shop ish ON (i.id_image = ish.id_image AND ish.id_shop = '.(int)$id_shop.')
			WHERE i.cover = 1 AND i.`id_product` = '.(int)$id_product);

        if (!$id_image) {
            $id_image = Db::getInstance()->getValue('
                SELECT i.`id_image` FROM '._DB_PREFIX_.'image i
                INNER JOIN '._DB_PREFIX_.'image_shop ish ON (i.id_image = ish.id_image AND ish.id_shop = '.(int)$id_shop.')
                WHERE i.`id_product` = '.(int)$id_product);
        }

        if ($count_cover_image < 1) {
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'image i SET i.cover = 1 WHERE i.id_image = '.(int)$id_image.' AND i.`id_product` = '.(int)$id_product.' LIMIT 1');
        }

        // Clean covers in image_shop table
        $count_cover_image_shop = Db::getInstance()->getValue('
			SELECT COUNT(*)
			FROM '._DB_PREFIX_.'image_shop ish
			WHERE ish.`id_product` = '.(int)$id_product.' AND ish.id_shop = '.(int)$id_shop.' AND ish.cover = 1');

        if ($count_cover_image_shop < 1) {
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'image_shop ish SET ish.cover = 1 WHERE ish.id_image = '.(int)$id_image.' AND ish.`id_product` = '.(int)$id_product.' AND ish.id_shop =  '.(int)$id_shop.' LIMIT 1');
        }

        if ($res) {
            $this->jsonConfirmation($this->_conf[27]);
        } else {
            $this->jsonError(Tools::displayError('An error occurred while attempting to associate this image with your shop. '));
        }
    }

    public function ajaxProcessUpdateImagePosition()
    {
        if ($this->tabAccess['edit'] === '0') {
            return die(json_encode(array('error' => $this->l('You do not have the right permission'))));
        }
        $res = false;
        if ($json = Tools::getValue('json')) {
            $res = true;
            $json = stripslashes($json);
            $images = json_decode($json, true);
            foreach ($images as $id => $position) {
                $img = new Image((int)$id);
                $img->position = (int)$position;
                $res &= $img->update();
            }
        }
        if ($res) {
            $this->jsonConfirmation($this->_conf[25]);
        } else {
            $this->jsonError(Tools::displayError('An error occurred while attempting to move this picture.'));
        }
    }

    public function ajaxProcessUpdateCover()
    {
        if ($this->tabAccess['edit'] === '0') {
            return die(json_encode(array('error' => $this->l('You do not have the right permission'))));
        }
        Image::deleteCover((int)Tools::getValue('id_product'));
        $img = new Image((int)Tools::getValue('id_image'));
        $img->cover = 1;

        @unlink(_PS_TMP_IMG_DIR_.'product_'.(int)$img->id_product.'.jpg');
        @unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$img->id_product.'_'.$this->context->shop->id.'.jpg');

        if ($img->update()) {
            $this->jsonConfirmation($this->_conf[26]);
        } else {
            $this->jsonError(Tools::displayError('An error occurred while attempting to update the cover picture.'));
        }
    }

    public function ajaxProcessDeleteProductImage()
    {
        $this->display = 'content';
        $res = true;
        /* Delete product image */
        $image = new Image((int)Tools::getValue('id_image'));
        $this->content['id'] = $image->id;
        $res &= $image->delete();
        // if deleted image was the cover, change it to the first one
        if (!Image::getCover($image->id_product)) {
            $res &= Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'image_shop` image_shop
			SET image_shop.`cover` = 1
			WHERE image_shop.`id_product` = '.(int)$image->id_product.'
			AND id_shop='.(int)$this->context->shop->id.' LIMIT 1');
        }

        if (!Image::getGlobalCover($image->id_product)) {
            $res &= Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'image` i
			SET i.`cover` = 1
			WHERE i.`id_product` = '.(int)$image->id_product.' LIMIT 1');
        }

        if (file_exists(_PS_TMP_IMG_DIR_.'product_'.$image->id_product.'.jpg')) {
            $res &= @unlink(_PS_TMP_IMG_DIR_.'product_'.$image->id_product.'.jpg');
        }
        if (file_exists(_PS_TMP_IMG_DIR_.'product_mini_'.$image->id_product.'_'.$this->context->shop->id.'.jpg')) {
            $res &= @unlink(_PS_TMP_IMG_DIR_.'product_mini_'.$image->id_product.'_'.$this->context->shop->id.'.jpg');
        }

        if ($res) {
            $this->jsonConfirmation($this->_conf[7]);
        } else {
            $this->jsonError(Tools::displayError('An error occurred while attempting to delete the room type image.'));
        }
    }

    protected function _validateSpecificPrice($id_shop, $id_currency, $id_country, $id_group, $id_customer, $price, $from_quantity, $reduction, $reduction_type, $from, $to, $id_combination = 0)
    {
        if (!Validate::isUnsignedId($id_shop) || !Validate::isUnsignedId($id_currency) || !Validate::isUnsignedId($id_country) || !Validate::isUnsignedId($id_group) || !Validate::isUnsignedId($id_customer)) {
            $this->errors[] = Tools::displayError('Wrong IDs');
        } elseif ((!isset($price) && !isset($reduction)) || (isset($price) && !Validate::isNegativePrice($price)) || (isset($reduction) && !Validate::isPrice($reduction))) {
            $this->errors[] = Tools::displayError('Invalid price/discount amount');
        } elseif (!Validate::isUnsignedInt($from_quantity)) {
            $this->errors[] = Tools::displayError('Invalid quantity');
        } elseif ($reduction && !Validate::isReductionType($reduction_type)) {
            $this->errors[] = Tools::displayError('Please select a discount type (amount or percentage).');
        } elseif ($from && $to && (!Validate::isDateFormat($from) || !Validate::isDateFormat($to))) {
            $this->errors[] = Tools::displayError('The from/to date is invalid.');
        } elseif (SpecificPrice::exists((int)$this->object->id, $id_combination, $id_shop, $id_group, $id_country, $id_currency, $id_customer, $from_quantity, $from, $to, false)) {
            $this->errors[] = Tools::displayError('A specific price already exists for these parameters.');
        } else {
            return true;
        }
        return false;
    }

    /* Checking customs feature */
    protected function checkFeatures($languages, $feature_id)
    {
        $rules = call_user_func(array('FeatureValue', 'getValidationRules'), 'FeatureValue');
        $feature = Feature::getFeature((int)Configuration::get('PS_LANG_DEFAULT'), $feature_id);

        foreach ($languages as $language) {
            if ($val = Tools::getValue('custom_'.$feature_id.'_'.$language['id_lang'])) {
                $current_language = new Language($language['id_lang']);
                if (Tools::strlen($val) > $rules['sizeLang']['value']) {
                    $this->errors[] = sprintf(
                        Tools::displayError('The name for feature %1$s is too long in %2$s.'),
                        ' <b>'.$feature['name'].'</b>',
                        $current_language->name
                    );
                } elseif (!call_user_func(array('Validate', $rules['validateLang']['value']), $val)) {
                    $this->errors[] = sprintf(
                        Tools::displayError('A valid name required for feature. %1$s in %2$s.'),
                        ' <b>'.$feature['name'].'</b>',
                        $current_language->name
                    );
                }
                if (count($this->errors)) {
                    return 0;
                }
                // Getting default language
                if ($language['id_lang'] == Configuration::get('PS_LANG_DEFAULT')) {
                    return $val;
                }
            }
        }
        return 0;
    }

    /**
     * Add or update a product image
     *
     * @param Product $product Product object to add image
     * @param string  $method
     *
     * @return int|false
     */
    public function addProductImage($product, $method = 'auto')
    {
        /* Updating an existing product image */
        if ($id_image = (int)Tools::getValue('id_image')) {
            $image = new Image((int)$id_image);
            if (!Validate::isLoadedObject($image)) {
                $this->errors[] = Tools::displayError('An error occurred while loading the object image.');
            } else {
                if (($cover = Tools::getValue('cover')) == 1) {
                    Image::deleteCover($product->id);
                }
                $image->cover = $cover;
                $this->validateRules('Image');
                $this->copyFromPost($image, 'image');
                if (count($this->errors) || !$image->update()) {
                    $this->errors[] = Tools::displayError('An error occurred while updating the image.');
                } elseif (isset($_FILES['image_product']['tmp_name']) && $_FILES['image_product']['tmp_name'] != null) {
                    $this->copyImage($product->id, $image->id, $method);
                }
            }
        }
        if (isset($image) && Validate::isLoadedObject($image) && !file_exists(_PS_PROD_IMG_DIR_.$image->getExistingImgPath().'.'.$image->image_format)) {
            $image->delete();
        }
        if (count($this->errors)) {
            return false;
        }
        @unlink(_PS_TMP_IMG_DIR_.'product_'.$product->id.'.jpg');
        @unlink(_PS_TMP_IMG_DIR_.'product_mini_'.$product->id.'_'.$this->context->shop->id.'.jpg');
        return ((isset($id_image) && is_int($id_image) && $id_image) ? $id_image : false);
    }

    /**
     * Copy a product image
     *
     * @param int    $id_product Product Id for product image filename
     * @param int    $id_image   Image Id for product image filename
     * @param string $method
     *
     * @return void|false
     * @throws PrestaShopException
     */
    public function copyImage($id_product, $id_image, $method = 'auto')
    {
        if (!isset($_FILES['image_product']['tmp_name'])) {
            return false;
        }
        if ($error = ImageManager::validateUpload($_FILES['image_product'])) {
            $this->errors[] = $error;
        } else {
            $image = new Image($id_image);

            if (!$new_path = $image->getPathForCreation()) {
                $this->errors[] = Tools::displayError('An error occurred while attempting to create a new folder.');
            }
            if (!($tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['image_product']['tmp_name'], $tmpName)) {
                $this->errors[] = Tools::displayError('An error occurred during the image upload process.');
            } elseif (!ImageManager::resize($tmpName, $new_path.'.'.$image->image_format)) {
                $this->errors[] = Tools::displayError('An error occurred while copying the image.');
            } elseif ($method == 'auto') {
                $imagesTypes = ImageType::getImagesTypes('products');
                foreach ($imagesTypes as $k => $image_type) {
                    if (!ImageManager::resize($tmpName, $new_path.'-'.stripslashes($image_type['name']).'.'.$image->image_format, $image_type['width'], $image_type['height'], $image->image_format)) {
                        $this->errors[] = Tools::displayError('An error occurred while copying this image:').' '.stripslashes($image_type['name']);
                    }
                }
            }

            @unlink($tmpName);
            Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_product));
        }
    }

    protected function updateAssoShop($id_object)
    {
        //override AdminController::updateAssoShop() specifically for products because shop association is set with the context in ObjectModel
        return;
    }

    public function processAdd()
    {
        $this->checkProduct();

        if (!empty($this->errors)) {
            $this->display = 'add';
            return false;
        }

        $this->object = new $this->className();
        $this->_removeTaxFromEcotax();
        $this->copyFromPost($this->object, $this->table);
        $this->object->booking_product = true;
        if ($this->object->add()) {

            // associateroom type to hotel
            // if ($this->object->is_virtual) {

            // }

            $this->assignRoomType($this->object);

            PrestaShopLogger::addLog(sprintf($this->l('%s addition', 'AdminTab', false, false), $this->className), 1, null, $this->className, (int)$this->object->id, true, (int)$this->context->employee->id);


            if (Configuration::get('PS_FORCE_ASM_NEW_PRODUCT') && Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $this->object->getType() != Product::PTYPE_VIRTUAL) {
                $this->object->advanced_stock_management = 1;
                $this->object->save();
                $id_shops = Shop::getContextListShopID();
                foreach ($id_shops as $id_shop) {
                    StockAvailable::setProductDependsOnStock($this->object->id, true, (int)$id_shop, 0);
                }
            }

            if (empty($this->errors)) {
                $languages = Language::getLanguages(false);
                if ($this->isProductFieldUpdated('category_box') && !$this->object->updateCategories(Tools::getValue('categoryBox'))) {
                    $this->errors[] = Tools::displayError('An error occurred while linking the object.').' <b>'.$this->table.'</b> '.Tools::displayError('To categories');
                } elseif (!$this->updateTags($languages, $this->object)) {
                    $this->errors[] = Tools::displayError('An error occurred while adding tags.');
                } else {
                    Hook::exec('actionProductAdd', array('id_product' => (int)$this->object->id, 'product' => $this->object));
                    if (in_array($this->object->visibility, array('both', 'search')) && Configuration::get('PS_SEARCH_INDEXATION')) {
                        Search::indexation(false, $this->object->id);
                    }
                }

                if (Configuration::get('PS_DEFAULT_WAREHOUSE_NEW_PRODUCT') != 0 && Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                    $warehouse_location_entity = new WarehouseProductLocation();
                    $warehouse_location_entity->id_product = $this->object->id;
                    $warehouse_location_entity->id_product_attribute = 0;
                    $warehouse_location_entity->id_warehouse = Configuration::get('PS_DEFAULT_WAREHOUSE_NEW_PRODUCT');
                    $warehouse_location_entity->location = pSQL('');
                    $warehouse_location_entity->save();
                }

                // Apply groups reductions
                $this->object->setGroupReduction();

                // Save and preview
                if (Tools::isSubmit('submitAddProductAndPreview')) {
                    $this->redirect_after = $this->getPreviewUrl($this->object);
                }

                // Save and stay on same form
                if ($this->display == 'edit') {
                    $this->redirect_after = self::$currentIndex.'&id_product='.(int)$this->object->id
                        .(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '')
                        .'&updateproduct&conf=3&key_tab='.Tools::safeOutput(Tools::getValue('key_tab')).'&token='.$this->token;
                } else {
                    // Default behavior (save and back)
                    $this->redirect_after = self::$currentIndex
                        .(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '')
                        .'&conf=3&token='.$this->token;
                }
            } else {
                $this->object->delete();
                // if errors : stay on edit page
                $this->display = 'edit';
            }
        } else {
            $this->errors[] = Tools::displayError('An error occurred while creating an object.').' <b>'.$this->table.'</b>';
        }

        return $this->object;
    }

    protected function isTabSubmitted($tab_name)
    {
        if (!is_array($this->submitted_tabs)) {
            $this->submitted_tabs = Tools::getValue('submitted_tabs');
        }

        if (is_array($this->submitted_tabs) && in_array($tab_name, $this->submitted_tabs)) {
            return true;
        }

        return false;
    }

    public function processStatus()
    {
        $this->loadObject(true);
        if (!Validate::isLoadedObject($this->object)) {
            return false;
        }
        if (($error = $this->object->validateFields(false, true)) !== true) {
            $this->errors[] = $error;
        }
        if (($error = $this->object->validateFieldsLang(false, true)) !== true) {
            $this->errors[] = $error;
        }

        if (count($this->errors)) {
            return false;
        }

        $res = parent::processStatus();

        $query = trim(Tools::getValue('bo_query'));
        $searchType = (int)Tools::getValue('bo_search_type');

        if ($query) {
            $this->redirect_after = preg_replace('/[\?|&](bo_query|bo_search_type)=([^&]*)/i', '', $this->redirect_after);
            $this->redirect_after .= '&bo_query='.$query.'&bo_search_type='.$searchType;
        }

        return $res;
    }

    public function processToggleShowAtFront()
    {
        if (!$this->loadObject()) {
            return false;
        }

        if (!Product::isBookingProduct($this->object->id)) {
            return false;
        }

        $this->object->show_at_front = !$this->object->show_at_front;
        if ($this->object->save()) {
            Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&conf=4');
        } else {
            $this->errors[] = $this->l('Something error occurred.');
        }
    }

    public function processUpdate()
    {
        $existing_product = $this->object;

        $this->checkProduct();

        if (!empty($this->errors)) {
            $this->display = 'edit';
            return false;
        }

        $id = (int)Tools::getValue('id_'.$this->table);
        /* Update an existing product */
        if (isset($id) && !empty($id)) {
            /** @var Product $object */
            $object = new $this->className((int)$id);
            $this->object = $object;

            if (Validate::isLoadedObject($object)) {
                $this->_removeTaxFromEcotax();
                $product_type_before = $object->getType();
                $this->copyFromPost($object, $this->table);
                $object->indexed = 0;

                if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
                    $object->setFieldsToUpdate((array)Tools::getValue('multishop_check', array()));
                }

                // Duplicate combinations if not associated to shop
                if ($this->context->shop->getContext() == Shop::CONTEXT_SHOP && !$object->isAssociatedToShop()) {
                    $is_associated_to_shop = false;
                    $combinations = Product::getProductAttributesIds($object->id);
                    if ($combinations) {
                        foreach ($combinations as $id_combination) {
                            $combination = new Combination((int)$id_combination['id_product_attribute']);
                            $default_combination = new Combination((int)$id_combination['id_product_attribute'], null, (int)$this->object->id_shop_default);

                            $def = ObjectModel::getDefinition($default_combination);
                            foreach ($def['fields'] as $field_name => $row) {
                                $combination->$field_name = ObjectModel::formatValue($default_combination->$field_name, $def['fields'][$field_name]['type']);
                            }

                            $combination->save();
                        }
                    }
                } else {
                    $is_associated_to_shop = true;
                }

                if ($object->update()) {
                    // update position in category
                    $object->setPositionInCategory(Tools::getValue('category_position'));

                    // If the product doesn't exist in the current shop but exists in another shop
                    if (Shop::getContext() == Shop::CONTEXT_SHOP && !$existing_product->isAssociatedToShop($this->context->shop->id)) {
                        $out_of_stock = StockAvailable::outOfStock($existing_product->id, $existing_product->id_shop_default);
                        $depends_on_stock = StockAvailable::dependsOnStock($existing_product->id, $existing_product->id_shop_default);
                        StockAvailable::setProductOutOfStock((int)$this->object->id, $out_of_stock, $this->context->shop->id);
                        StockAvailable::setProductDependsOnStock((int)$this->object->id, $depends_on_stock, $this->context->shop->id);
                    }

                    PrestaShopLogger::addLog(sprintf($this->l('%s modification', 'AdminTab', false, false), $this->className), 1, null, $this->className, (int)$this->object->id, true, (int)$this->context->employee->id);
                    if (in_array($this->context->shop->getContext(), array(Shop::CONTEXT_SHOP, Shop::CONTEXT_ALL))) {
                        if ($this->isTabSubmitted('Shipping')) {
                            // $this->addCarriers();
                        }
                        if ($this->isTabSubmitted('Associations')) {
                            // $this->updateAccessories($object);
                        }
                        if ($this->isTabSubmitted('Suppliers')) {
                            // $this->processSuppliers();
                        }
                        if ($this->isTabSubmitted('Features')) {
                            $this->processFeatures();
                        }
                        if ($this->isTabSubmitted('Combinations')) {
                            $this->processProductAttribute();
                        }
                        if ($this->isTabSubmitted('Prices')) {
                            $this->processAdvancedPayment();
                            $this->processPriceAddition();
                            $this->processSpecificPricePriorities();
                        }
                        if ($this->isTabSubmitted('Customization')) {
                            $this->processCustomizationConfiguration();
                        }
                        if ($this->isTabSubmitted('Attachments')) {
                            $this->processAttachments();
                        }
                        if ($this->isTabSubmitted('Images')) {
                            $this->processImageLegends();
                        }
                        if ($this->isTabSubmitted('Occupancy')) {
                            $this->processOccupancy();
                        }
                        if ($this->isTabSubmitted('ServiceProduct')) {
                            $this->processServiceProduct();
                        }
                        if ($this->isTabSubmitted('LengthOfStay')) {
                            $this->processLengthOfStay();
                        }
                        if ($this->isTabSubmitted('Configuration')) {
                            $this->processConfiguration();
                        }
                        if ($this->isTabSubmitted('AdditionalFacilities')) {
                            $this->processAdditionalFacilities();
                        }

                        // $this->updatePackItems($object);
                        // Disallow avanced stock management if the product become a pack
                        if ($product_type_before == Product::PTYPE_SIMPLE && $object->getType() == Product::PTYPE_PACK) {
                            StockAvailable::setProductDependsOnStock((int)$object->id, false);
                        }
                        // $this->updateDownloadProduct($object, 1);
                        $this->updateTags(Language::getLanguages(false), $object);

                        if ($this->isProductFieldUpdated('category_box') && !$object->updateCategories(Tools::getValue('categoryBox'))) {
                            $this->errors[] = Tools::displayError('An error occurred while linking the object.').' <b>'.$this->table.'</b> '.Tools::displayError('To categories');
                        }
                    }

                    if ($this->isTabSubmitted('Warehouses')) {
                        $this->processWarehouses();
                    }
                    if (empty($this->errors)) {
                        if (in_array($object->visibility, array('both', 'search')) && Configuration::get('PS_SEARCH_INDEXATION')) {
                            Search::indexation(false, $object->id);
                        }

                        // Save and preview
                        if (Tools::isSubmit('submitAddProductAndPreview')) {
                            $this->redirect_after = $this->getPreviewUrl($object);
                        } else {
                            $page = (int)Tools::getValue('page');
                            // Save and stay on same form
                            if ($this->display == 'edit') {
                                $this->confirmations[] = $this->l('Update successful');
                                $this->redirect_after = self::$currentIndex.'&id_product='.(int)$this->object->id
                                    .(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '')
                                    .'&updateproduct&conf=4&key_tab='.Tools::safeOutput(Tools::getValue('key_tab')).($page > 1 ? '&page='.(int)$page : '').'&token='.$this->token;
                            } else {
                                // Default behavior (save and back)
                                $this->redirect_after = self::$currentIndex.(Tools::getIsset('id_category') ? '&id_category='.(int)Tools::getValue('id_category') : '').'&conf=4'.($page > 1 ? '&submitFilterproduct='.(int)$page : '').'&token='.$this->token;
                            }
                        }
                    }
                    // if errors : stay on edit page
                    else {
                        $this->display = 'edit';
                    }
                } else {
                    if (!$is_associated_to_shop && $combinations) {
                        foreach ($combinations as $id_combination) {
                            $combination = new Combination((int)$id_combination['id_product_attribute']);
                            $combination->delete();
                        }
                    }
                    $this->errors[] = Tools::displayError('An error occurred while updating an object.').' <b>'.$this->table.'</b> ('.Db::getInstance()->getMsgError().')';
                }
            } else {
                $this->errors[] = Tools::displayError('An error occurred while updating an object.').' <b>'.$this->table.'</b> ('.Tools::displayError('The object cannot be loaded. ').')';
            }
            return $object;
        }
    }

    /**
     * Check that a saved product is valid
     */
    public function checkProduct()
    {
        $className = 'Product';
        // @todo : the call_user_func seems to contains only statics values (className = 'Product')
        $rules = call_user_func(array($this->className, 'getValidationRules'), $this->className);
        $default_language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages(false);

        // Check required fields
        foreach ($rules['required'] as $field) {
            if (!$this->isProductFieldUpdated($field)) {
                continue;
            }

            if (($value = Tools::getValue($field)) == false && $value != '0') {
                if (Tools::getValue('id_'.$this->table) && $field == 'passwd') {
                    continue;
                }
                $this->errors[] = sprintf(
                    Tools::displayError('The %s field is required.'),
                    call_user_func(array($className, 'displayFieldName'), $field, $className)
                );
            }
        }

        // Check multilingual required fields
        foreach ($rules['requiredLang'] as $fieldLang) {
            if ($this->isProductFieldUpdated($fieldLang, $default_language->id) && !Tools::getValue($fieldLang.'_'.$default_language->id)) {
                $this->errors[] = sprintf(
                    Tools::displayError('This %1$s field is required at least in %2$s'),
                    call_user_func(array($className, 'displayFieldName'), $fieldLang, $className),
                    $default_language->name
                );
            }
        }

        // Check fields sizes
        foreach ($rules['size'] as $field => $maxLength) {
            if ($this->isProductFieldUpdated($field) && ($value = Tools::getValue($field)) && Tools::strlen($value) > $maxLength) {
                $this->errors[] = sprintf(
                    Tools::displayError('The %1$s field is too long (%2$d chars max).'),
                    call_user_func(array($className, 'displayFieldName'), $field, $className),
                    $maxLength
                );
            }
        }

        if (Tools::getIsset('description_short') && $this->isProductFieldUpdated('description_short')) {
            $saveShort = Tools::getValue('description_short');
            $_POST['description_short'] = strip_tags(Tools::getValue('description_short'));
        }

        // Check description short size without html
        $limit = (int)Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT');
        if ($limit <= 0) {
            $limit = 400;
        }
        foreach ($languages as $language) {
            if ($this->isProductFieldUpdated('description_short', $language['id_lang']) && ($value = Tools::getValue('description_short_'.$language['id_lang']))) {
                if (Tools::strlen(strip_tags($value)) > $limit) {
                    $this->errors[] = sprintf(
                        Tools::displayError('This %1$s field (%2$s) is too long: %3$d chars max (current count %4$d).'),
                        call_user_func(array($className, 'displayFieldName'), 'description_short'),
                        $language['name'],
                        $limit,
                        Tools::strlen(strip_tags($value))
                    );
                }
            }
        }

        // Check multilingual fields sizes
        foreach ($rules['sizeLang'] as $fieldLang => $maxLength) {
            foreach ($languages as $language) {
                $value = Tools::getValue($fieldLang.'_'.$language['id_lang']);
                if ($value && Tools::strlen($value) > $maxLength) {
                    $this->errors[] = sprintf(
                        Tools::displayError('The %1$s field is too long (%2$d chars max).'),
                        call_user_func(array($className, 'displayFieldName'), $fieldLang, $className),
                        $maxLength
                    );
                }
            }
        }

        if ($this->isProductFieldUpdated('description_short') && isset($_POST['description_short'])) {
            $_POST['description_short'] = $saveShort;
        }

        // Check fields validity
        foreach ($rules['validate'] as $field => $function) {
            if ($this->isProductFieldUpdated($field) && ($value = Tools::getValue($field))) {
                $res = true;
                if (Tools::strtolower($function) == 'iscleanhtml') {
                    if (!Validate::{$function}($value, (int)Configuration::get('PS_ALLOW_HTML_IFRAME'))) {
                        $res = false;
                    }
                } elseif (!Validate::{$function}($value)) {
                    $res = false;
                }

                if (!$res) {
                    $this->errors[] = sprintf(
                        Tools::displayError('The %s field is invalid.'),
                        call_user_func(array($className, 'displayFieldName'), $field, $className)
                    );
                }
            }
        }
        // Check multilingual fields validity
        foreach ($rules['validateLang'] as $fieldLang => $function) {
            foreach ($languages as $language) {
                if ($this->isProductFieldUpdated($fieldLang, $language['id_lang']) && ($value = Tools::getValue($fieldLang.'_'.$language['id_lang']))) {
                    if (!Validate::{$function}($value, (int)Configuration::get('PS_ALLOW_HTML_IFRAME'))) {
                        $this->errors[] = sprintf(
                            Tools::displayError('The %1$s field (%2$s) is invalid.'),
                            call_user_func(array($className, 'displayFieldName'), $fieldLang, $className),
                            $language['name']
                        );
                    }
                }
            }
        }

        // if (Tools::getValue('is_virtual')) {
            $id_hotel = Tools::getValue('id_hotel');
            if (!$id_hotel || !Validate::isUnsignedInt($id_hotel)) {
                $this->errors[] = Tools::displayError('Please select a hotel');
            } else if (!Validate::isLoadedObject($objHotel = new HotelBranchInformation($id_hotel))) {
                $this->errors[] = Tools::displayError('Selected Hotel not found');
            } else {
                $hotelIdCategory = $objHotel->id_category;
                if (Validate::isLoadedObject($objCategory = new Category($hotelIdCategory))) {
                    foreach($objCategory->getParentsCategories() as $category) {
                        $_POST['categoryBox'][] = $category['id_category'];
                    }
                    if(!Tools::getValue('id_category_default')) {
                        $_POST['id_category_default'] = $hotelIdCategory;
                    }
                }
            }
        // } else {
        //     $rootCategory = Category::getRootCategory();
        //     $_POST['categoryBox'][] = $rootCategory->id_category;
        //     if(!Tools::getValue('id_category_default')) {
        //         $_POST['id_category_default'] = $rootCategory->id_category;
        //     }
        // }

        // Categories
        if ($this->isProductFieldUpdated('id_category_default') && (!Tools::isSubmit('categoryBox') || !count(Tools::getValue('categoryBox')))) {
            $this->errors[] = $this->l('This room type must be in at least one category.');
        }

        if ($this->isProductFieldUpdated('id_category_default') && (!is_array(Tools::getValue('categoryBox')) || !in_array(Tools::getValue('id_category_default'), Tools::getValue('categoryBox')))) {
            $this->errors[] = $this->l('This room type must be in the default category.');
        }

        // Tags
        foreach ($languages as $language) {
            if ($value = Tools::getValue('tags_'.$language['id_lang'])) {
                if (!Validate::isTagsList($value)) {
                    $this->errors[] = sprintf(
                        Tools::displayError('The tags list (%s) is invalid.'),
                        $language['name']
                    );
                }
            }
        }

        // Category Position
        if (Validate::isLoadedObject($this->object)) {
            $categoryPositon = Tools::getValue('category_position');
            if ($categoryPositon < 0) {
                $this->errors[] = $this->l('Position can not be less than 0.');
            }

            if (isset($objHotel) && Validate::isLoadedObject($objHotel)) {
                $maxPosition = Product::getHighestPositionInCategory($objHotel->id_category);
                if ($categoryPositon > $maxPosition) {
                    $this->errors[] = sprintf($this->l('Position can not be greater than %d.'), $maxPosition);
                }
            }
        }
    }

    /**
     * Check if a field is edited (if the checkbox is checked)
     * This method will do something only for multishop with a context all / group
     *
     * @param string $field Name of field
     * @param int $id_lang
     * @return bool
     */
    protected function isProductFieldUpdated($field, $id_lang = null)
    {
        // Cache this condition to improve performances
        static $is_activated = null;
        if (is_null($is_activated)) {
            $is_activated = Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP && $this->id_object;
        }

        if (!$is_activated) {
            return true;
        }

        $def = ObjectModel::getDefinition($this->object);
        if (!$this->object->isMultiShopField($field) && is_null($id_lang) && isset($def['fields'][$field])) {
            return true;
        }

        if (is_null($id_lang)) {
            return !empty($_POST['multishop_check'][$field]);
        } else {
            return !empty($_POST['multishop_check'][$field][$id_lang]);
        }
    }

    protected function _removeTaxFromEcotax()
    {
        if ($ecotax = Tools::getValue('ecotax')) {
            $_POST['ecotax'] = Tools::ps_round($ecotax / (1 + Tax::getProductEcotaxRate() / 100), 6);
        }
    }

    protected function _applyTaxToEcotax($product)
    {
        if ($product->ecotax) {
            $product->ecotax = Tools::ps_round($product->ecotax * (1 + Tax::getProductEcotaxRate() / 100), 2);
        }
    }

    /**
     * Update product accessories
     *
     * @param object $product Product
     */
    public function updateAccessories($product)
    {
        $product->deleteAccessories();
        if ($accessories = Tools::getValue('inputAccessories')) {
            $accessories_id = array_unique(explode('-', $accessories));
            if (count($accessories_id)) {
                array_pop($accessories_id);
                $product->changeAccessories($accessories_id);
            }
        }
    }

    /**
     * Update product tags
     *
     * @param array $languages Array languages
     * @param object $product Product
     * @return bool Update result
     */
    public function updateTags($languages, $product)
    {
        $tag_success = true;
        /* Reset all tags for THIS product */
        if (!Tag::deleteTagsForProduct((int)$product->id)) {
            $this->errors[] = Tools::displayError('An error occurred while attempting to delete previous tags.');
        }
        /* Assign tags to this product */
        foreach ($languages as $language) {
            if ($value = Tools::getValue('tags_'.$language['id_lang'])) {
                $tag_success &= Tag::addTags($language['id_lang'], (int)$product->id, $value);
            }
        }

        if (!$tag_success) {
            $this->errors[] = Tools::displayError('An error occurred while adding tags.');
        }

        return $tag_success;
    }

    public function initContent($token = null)
    {
        if ($this->display == 'edit' || $this->display == 'add') {
            $this->fields_form = array();

            // Check if Module
            if (substr($this->tab_display, 0, 6) == 'Module') {
                $this->tab_display_module = strtolower(substr($this->tab_display, 6, Tools::strlen($this->tab_display) - 6));
                $this->tab_display = 'Modules';
            }
            if (method_exists($this, 'initForm'.$this->tab_display)) {
                $this->tpl_form = strtolower($this->tab_display).'.tpl';
            }

            if ($this->ajax) {
                $this->content_only = true;
            } else {
                if (($object = $this->loadObject(true)) && $object->isAssociatedToShop()) {
                    if (!$object->booking_product) {
                        $this->errors[] = $this->l('Room type not found.');
                        return;
                    }
                }
                $product_tabs = array();

                // tab_display defines which tab to display first
                if (!method_exists($this, 'initForm'.$this->tab_display)) {
                    $this->tab_display = $this->default_tab;
                }

                $advanced_stock_management_active = Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT');
                foreach ($this->available_tabs as $product_tab => $value) {
                    // if it's the warehouses tab and advanced stock management is disabled, continue
                    if ($advanced_stock_management_active == 0 && $product_tab == 'Warehouses') {
                        continue;
                    }

                    $product_tabs[$product_tab] = array(
                        'id' => $product_tab,
                        'selected' => (strtolower($product_tab) == strtolower($this->tab_display) || (isset($this->tab_display_module) && 'module'.$this->tab_display_module == Tools::strtolower($product_tab))),
                        'name' => $this->available_tabs_lang[$product_tab],
                        'href' => $this->context->link->getAdminLink('AdminProducts').'&id_product='.(int)Tools::getValue('id_product').'&action='.$product_tab,
                    );
                }
                $this->tpl_form_vars['product_tabs'] = $product_tabs;
            }
        } else {
            // If products from all categories are displayed, we don't want to use sorting by position
            if (!(int) $this->id_current_category) {
                $this->_defaultOrderBy = $this->identifier;
                if ($this->context->cookie->{$this->table.'Orderby'} == 'position') {
                    unset($this->context->cookie->{$this->table.'Orderby'});
                    unset($this->context->cookie->{$this->table.'Orderway'});
                }
            }
        }

        parent::initContent();
    }

    public function renderKpis()
    {
        $time = time();
        $kpis = array();

        $helper = new HelperKpi();
        $helper->id = 'box-total-rooms';
        $helper->icon = 'icon-bed';
        $helper->color = 'color3';
        $helper->title = $this->l('Total Rooms', null, null, false);
        $helper->subtitle = $this->l('Today', null, null, false);
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=total_rooms';
        $helper->tooltip = $this->l('The total number of rooms in all hotels.', null, null, false);
        $kpis[] = $helper;

        $helper = new HelperKpi();
        $helper->id = 'box-occupied-rooms';
        $helper->icon = 'icon-user';
        $helper->color = 'color1';
        $helper->title = $this->l('Occupied Rooms', null, null, false);
        $helper->subtitle = $this->l('Today', null, null, false);
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=occupied_rooms';
        $helper->tooltip = $this->l('The current count of rooms that are currently occupied by guests.', null, null, false);
        $kpis[] = $helper;

        $helper = new HelperKpi();
        $helper->id = 'box-vacant-rooms';
        $helper->icon = 'icon-check-empty';
        $helper->color = 'color3';
        $helper->title = $this->l('Vacant Rooms', null, null, false);
        $helper->subtitle = $this->l('Today', null, null, false);
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=vacant_rooms';
        $helper->tooltip = $this->l('The count of rooms that are currently unoccupied and available for booking.', null, null, false);
        $kpis[] = $helper;

        $helper = new HelperKpi();
        $helper->id = 'box-reserved-rooms';
        $helper->icon = 'icon-calendar';
        $helper->color = 'color4';
        $helper->title = $this->l('Booked Rooms', null, null, false);
        $helper->subtitle = $this->l('Today', null, null, false);
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=booked_rooms';
        $helper->tooltip = $this->l('The number of rooms that are currently booked but not yet occupied.', null, null, false);
        $kpis[] = $helper;

        $helper = new HelperKpi();
        $helper->id = 'box-disabled-rooms';
        $helper->icon = 'icon-ban';
        $helper->color = 'color2';
        $helper->title = $this->l('Disabled Rooms', null, null, false);
        $helper->subtitle = $this->l('Today', null, null, false);
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=disabled_rooms';
        $helper->tooltip = $this->l('The number of rooms that are currently disabled.', null, null, false);
        $kpis[] = $helper;

        $helper = new HelperKpi();
        $helper->id = 'box-online-bookable-rooms';
        $helper->icon = 'icon-globe';
        $helper->color = 'color4';
        $helper->title = $this->l('Online Bookable Rooms', null, null, false);
        $helper->subtitle = $this->l('Today', null, null, false);
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=online_bookable_rooms';
        $helper->tooltip = $this->l('The total number of rooms that can be booked only using website.', null, null, false);
        $kpis[] = $helper;

        $helper = new HelperKpi();
        $helper->id = 'box-offline-bookable-rooms';
        $helper->icon = 'icon-building';
        $helper->color = 'color1';
        $helper->title = $this->l('Offline Bookable Rooms', null, null, false);
        $helper->subtitle = $this->l('Today', null, null, false);
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=offline_bookable_rooms';
        $helper->tooltip = $this->l('The number of rooms that can be booked either through website or offline channels (e.g., phone or in-person).', null, null, false);
        $kpis[] = $helper;

        $helper = new HelperKpi();
        $helper->id = 'box-best-selling-room-type';
        $helper->icon = 'icon-star';
        $helper->color = 'color1';
        $helper->title = $this->l('Best Selling', null, null, false);
        $nbDaysBestSelling = Validate::isUnsignedInt(Configuration::get('PS_KPI_BEST_SELLING_ROOM_TYPE_NB_DAYS')) ? Configuration::get('PS_KPI_BEST_SELLING_ROOM_TYPE_NB_DAYS') : 30;
        $helper->subtitle = sprintf($this->l('%d Days', null, null, false), (int) $nbDaysBestSelling);
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=best_selling_room_type';
        $helper->tooltip = $this->l('Displays the best selling room type based on the last 30 days of sales.', null, null, false);
        $kpis[] = $helper;

        $helper = new HelperKpi();
        $helper->id = 'box-disbled-room-types';
        $helper->icon = 'icon-ban';
        $helper->color = 'color2';
        $helper->title = $this->l('Disabled Room Types', null, null, false);
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=disabled_room_types';
        $helper->href = Context::getContext()->link->getAdminLink('AdminProducts').'&productFilter_sa!active=0&submitFilterproduct=1';
        $helper->tooltip = $this->l('The total number of room types that are currently disabled.', null, null, false);
        $kpis[] = $helper;

        Hook::exec('action'.$this->controller_name.'KPIListingModifier', array(
            'kpis' => &$kpis,
        ));

        $helper = new HelperKpiRow();
        $helper->kpis = $kpis;

        return $helper->generate();
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('preview');
        $this->addRowAction('duplicate');
        $this->addRowAction('delete');

        $this->tpl_list_vars['title'] = $this->l('Room Types');

        $this->_new_list_header_design = true;

        return parent::renderList();
    }

    public function displayDuplicateLink($token = null, $id, $name = null)
    {
        return '<a href="#" title="'.$this->l('Duplicate').'"
        onclick="initDuplicateRoomType('.(int)$id.');return false;"><i class="icon-copy"></i>'.$this->l('Duplicate').'</a>';
    }

    /**
     * Build a categories tree
     *
     * @param       $id_obj
     * @param array $indexedCategories   Array with categories where product is indexed (in order to check checkbox)
     * @param array $categories          Categories to list
     * @param       $current
     * @param null  $id_category         Current category ID
     * @param null  $id_category_default
     * @param array $has_suite
     *
     * @return string
     */
    public static function recurseCategoryForInclude($id_obj, $indexedCategories, $categories, $current, $id_category = null, $id_category_default = null, $has_suite = array())
    {
        global $done;
        static $irow;
        $content = '';

        if (!$id_category) {
            $id_category = (int)Configuration::get('PS_ROOT_CATEGORY');
        }

        if (!isset($done[$current['infos']['id_parent']])) {
            $done[$current['infos']['id_parent']] = 0;
        }
        $done[$current['infos']['id_parent']] += 1;

        $todo = count($categories[$current['infos']['id_parent']]);
        $doneC = $done[$current['infos']['id_parent']];

        $level = $current['infos']['level_depth'] + 1;

        $content .= '
		<tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
			<td>
				<input type="checkbox" name="categoryBox[]" class="categoryBox'.($id_category_default == $id_category ? ' id_category_default' : '').'" id="categoryBox_'.$id_category.'" value="'.$id_category.'"'.((in_array($id_category, $indexedCategories) || ((int)(Tools::getValue('id_category')) == $id_category && !(int)($id_obj))) ? ' checked="checked"' : '').' />
			</td>
			<td>
				'.$id_category.'
			</td>
			<td>';
        for ($i = 2; $i < $level; $i++) {
            $content .= '<img src="../img/admin/lvl_'.$has_suite[$i - 2].'.gif" alt="" />';
        }
        $content .= '<img src="../img/admin/'.($level == 1 ? 'lv1.gif' : 'lv2_'.($todo == $doneC ? 'f' : 'b').'.gif').'" alt="" /> &nbsp;
			<label for="categoryBox_'.$id_category.'" class="t">'.stripslashes($current['infos']['name']).'</label></td>
		</tr>';

        if ($level > 1) {
            $has_suite[] = ($todo == $doneC ? 0 : 1);
        }
        if (isset($categories[$id_category])) {
            foreach ($categories[$id_category] as $key => $row) {
                if ($key != 'infos') {
                    $content .= AdminProductsController::recurseCategoryForInclude($id_obj, $indexedCategories, $categories, $categories[$id_category][$key], $key, $id_category_default, $has_suite);
                }
            }
        }
        return $content;
    }

    protected function _displayDraftWarning($active)
    {
        $content = '<div class="warn draft" style="'.($active ? 'display:none' : '').'">
				<span>'.$this->l('Your room type will be saved as a draft.').'</span>
				<a href="#" class="btn btn-default pull-right" onclick="submitAddProductAndPreview()" ><i class="icon-external-link-sign"></i> '.$this->l('Save and preview').'</a>
				<input type="hidden" name="fakeSubmitAddProductAndPreview" id="fakeSubmitAddProductAndPreview" />
	 		</div>';
        $this->tpl_form_vars['draft_warning'] = $content;
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_product'] = array(
                    'href' => self::$currentIndex.'&addproduct&token='.$this->token,
                    // 'desc' => $this->l('Add new room type', null, null, false),
                    'desc' => $this->l('Add new room type', null, null, false),
                    'icon' => 'process-icon-new'
                );
        }
        if ($this->display == 'edit') {
            if (($product = $this->loadObject(true)) && $product->isAssociatedToShop()) {
                // adding button for preview this product
                if ($url_preview = $this->getPreviewUrl($product)) {
                    $this->page_header_toolbar_btn['preview'] = array(
                        'short' => $this->l('Preview', null, null, false),
                        'href' => $url_preview,
                        'desc' => $this->l('Preview', null, null, false),
                        'target' => true,
                        'class' => 'previewUrl'
                    );
                }

                $js = 'initDuplicateRoomType('.(int)$product->id.');return false;';
                // adding button for duplicate this product
                if ($this->tabAccess['add']) {
                    $this->page_header_toolbar_btn['duplicate'] = array(
                        'short' => $this->l('Duplicate', null, null, false),
                        'desc' => $this->l('Duplicate', null, null, false),
                        'confirm' => 1,
                        'js' => $js
                    );
                }

                // adding button for preview this product statistics
                if (file_exists(_PS_MODULE_DIR_.'statsproduct/statsproduct.php')) {
                    $this->page_header_toolbar_btn['stats'] = array(
                    'short' => $this->l('Statistics', null, null, false),
                    'href' => $this->context->link->getAdminLink('AdminStats').'&module=statsproduct&id_product='.(int)$product->id,
                    'desc' => $this->l('Room Type Sales', null, null, false),
                );
                }

                // adding button for delete this product
                if ($this->tabAccess['delete']) {
                    $this->page_header_toolbar_btn['delete'] = array(
                        'short' => $this->l('Delete', null, null, false),
                        'href' => $this->context->link->getAdminLink('AdminProducts').'&id_product='.(int)$product->id.'&deleteproduct',
                        'desc' => $this->l('Delete this room type', null, null, false),
                        'confirm' => 1,
                        'js' => 'if (confirm(\''.$this->l('Delete room type?', null, true, false).'\')){return true;}else{event.preventDefault();}'
                    );
                }
            }
        }
        parent::initPageHeaderToolbar();
    }

    public function initModal()
    {
        parent::initModal();
        $this->modals[] = $this->getModalDuplicateOptions();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        if ($this->display == 'edit' || $this->display == 'add') {
            $this->toolbar_btn['save'] = array(
                'short' => 'Save',
                'href' => '#',
                'desc' => $this->l('Save'),
            );

            $this->toolbar_btn['save-and-stay'] = array(
                'short' => 'SaveAndStay',
                'href' => '#',
                'desc' => $this->l('Save and stay'),
            );

            // adding button for adding a new combination in Combination tab
            $this->toolbar_btn['newCombination'] = array(
                'short' => 'New combination',
                'desc' => $this->l('New combination'),
                'class' => 'toolbar-new'
            );
        } elseif ($this->can_import) {
            $this->toolbar_btn['import'] = array(
                'href' => $this->context->link->getAdminLink('AdminImport', true).'&import_type=products',
                'desc' => $this->l('Import')
            );
        }

        $this->context->smarty->assign('toolbar_scroll', 1);
        $this->context->smarty->assign('show_toolbar', 1);
        $this->context->smarty->assign('toolbar_btn', $this->toolbar_btn);
    }

    /**
     * renderForm contains all necessary initialization needed for all tabs
     *
     * @return string|void
     * @throws PrestaShopException
     */
    public function renderForm()
    {
        // This nice code (irony) is here to store the product name, because the row after will erase product name in multishop context
        if (Validate::isLoadedObject(($this->object))) {
            $this->product_name = $this->object->name[$this->context->language->id];
        }

        if (!method_exists($this, 'initForm'.$this->tab_display)) {
            return;
        }

        $product = $this->object;

        // Product for multishop
        $this->context->smarty->assign('bullet_common_field', '');
        if (Shop::isFeatureActive() && $this->display == 'edit') {
            if (Shop::getContext() != Shop::CONTEXT_SHOP) {
                $this->context->smarty->assign(array(
                    'display_multishop_checkboxes' => true,
                    'multishop_check' => Tools::getValue('multishop_check'),
                ));
            }

            if (Shop::getContext() != Shop::CONTEXT_ALL) {
                $this->context->smarty->assign('bullet_common_field', '<i class="icon-circle text-orange"></i>');
                $this->context->smarty->assign('display_common_field', true);
            }
        }

        $this->tpl_form_vars['tabs_preloaded'] = $this->available_tabs;

        /*---- NOTE FOR WEBKUL ----*/
        $this->tpl_form_vars['product_type'] = (int) Tools::getValue('type_product', $product->getType()); // $product->getType() before changes

        $this->getLanguages();

        $this->tpl_form_vars['id_lang_default'] = Configuration::get('PS_LANG_DEFAULT');
        $this->tpl_form_vars['currency'] = $this->context->currency;

        $this->tpl_form_vars['currentIndex'] = self::$currentIndex;
        $this->tpl_form_vars['display_multishop_checkboxes'] = (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP && $this->display == 'edit');
        $this->fields_form = array('');

        $this->tpl_form_vars['token'] = $this->token;
        $this->tpl_form_vars['combinationImagesJs'] = $this->getCombinationImagesJs();
        $this->tpl_form_vars['PS_ALLOW_ACCENTED_CHARS_URL'] = (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
        $this->tpl_form_vars['post_data'] = json_encode($_POST);
        $this->tpl_form_vars['save_error'] = !empty($this->errors);
        $this->tpl_form_vars['mod_evasive'] = Tools::apacheModExists('evasive');
        $this->tpl_form_vars['mod_security'] = Tools::apacheModExists('security');
        $this->tpl_form_vars['ps_force_friendly_product'] = Configuration::get('PS_FORCE_FRIENDLY_PRODUCT');

        // add text for normal product and booking product
        $this->tpl_form_vars['booking_product_text'] = array(
            'name'=> $this->l('Room Type'),
            'name_title'=> $this->l('Write the name of the Room Type for ex. Delux, Executive etc. Invalid characters <>;=#{}')
        );
        $this->tpl_form_vars['normal_product_text'] = array(
            'name'=> $this->l('Name'),
            'name_title'=> $this->l('Write the name of the Product for ex. water bottle, etc. Invalid characters <>;=#{}')
        );

        // autoload rich text editor (tiny mce)
        $this->tpl_form_vars['tinymce'] = true;
        $iso = $this->context->language->iso_code;
        $this->tpl_form_vars['iso'] = file_exists(_PS_CORE_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en';
        $this->tpl_form_vars['path_css'] = _THEME_CSS_DIR_;
        $this->tpl_form_vars['ad'] = __PS_BASE_URI__.basename(_PS_ADMIN_DIR_);

        if (Validate::isLoadedObject(($this->object))) {
            $id_product = (int)$this->object->id;
        } else {
            $id_product = (int)Tools::getvalue('id_product');
        }

        $page = (int)Tools::getValue('page');

        $this->tpl_form_vars['form_action'] = $this->context->link->getAdminLink('AdminProducts').'&'.($id_product ? 'updateproduct&id_product='.(int)$id_product : 'addproduct').($page > 1 ? '&page='.(int)$page : '');
        $this->tpl_form_vars['id_product'] = $id_product;

        // Transform configuration option 'upload_max_filesize' in octets
        $upload_max_filesize = Tools::getOctets(ini_get('upload_max_filesize'));

        // Transform configuration option 'upload_max_filesize' in MegaOctets
        $upload_max_filesize = ($upload_max_filesize / 1024) / 1024;

        $this->tpl_form_vars['upload_max_filesize'] = $upload_max_filesize;
        $this->tpl_form_vars['country_display_tax_label'] = $this->context->country->display_tax_label;
        $this->tpl_form_vars['has_combinations'] = $this->object->hasAttributes();
        $this->product_exists_in_shop = true;

        if ($this->display == 'edit' && Validate::isLoadedObject($product) && Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP && !$product->isAssociatedToShop($this->context->shop->id)) {
            $this->product_exists_in_shop = false;
            if ($this->tab_display == 'Informations') {
                $this->displayWarning($this->l('Warning: The product does not exist in this shop'));
            }

            $default_product = new Product();
            $definition = ObjectModel::getDefinition($product);
            foreach ($definition['fields'] as $field_name => $field) {
                if (isset($field['shop']) && $field['shop']) {
                    $product->$field_name = ObjectModel::formatValue($default_product->$field_name, $field['type']);
                }
            }
        }

        // let's calculate this once for all
        if (!Validate::isLoadedObject($this->object) && Tools::getValue('id_product')) {
            $this->errors[] = 'Unable to load object';
        } else {
            $this->_displayDraftWarning($this->object->active);

            // if there was an error while saving, we don't want to lose posted data
            if (!empty($this->errors)) {
                $this->copyFromPost($this->object, $this->table);
            }

            // $this->initPack($this->object);
            $this->{'initForm'.$this->tab_display}($this->object);
            $this->tpl_form_vars['product'] = $this->object;

            if ($this->ajax) {
                if (!isset($this->tpl_form_vars['custom_form'])) {
                    throw new PrestaShopException('custom_form empty for action '.$this->tab_display);
                } else {
                    return $this->tpl_form_vars['custom_form'];
                }
            }
        }

        $parent = parent::renderForm();
        $this->addJqueryPlugin(array('autocomplete', 'fancybox', 'typewatch'));
        return $parent;
    }

    public function getPreviewUrl(Product $product)
    {
        $id_lang = Configuration::get('PS_LANG_DEFAULT', null, null, Context::getContext()->shop->id);

        if (!ShopUrl::getMainShopDomain()) {
            return false;
        }

        $is_rewrite_active = (bool)Configuration::get('PS_REWRITING_SETTINGS');
        $preview_url = $this->context->link->getProductLink(
            $product,
            $this->getFieldValue($product, 'link_rewrite', $this->context->language->id),
            Category::getLinkRewrite($this->getFieldValue($product, 'id_category_default'), $this->context->language->id),
            null,
            $id_lang,
            (int)Context::getContext()->shop->id,
            0,
            $is_rewrite_active
        );

        if (!$product->active) {
            $admin_dir = dirname($_SERVER['PHP_SELF']);
            $admin_dir = substr($admin_dir, strrpos($admin_dir, '/') + 1);
            $preview_url .= ((strpos($preview_url, '?') === false) ? '?' : '&').'adtoken='.$this->token.'&ad='.$admin_dir.'&id_employee='.(int)$this->context->employee->id;
        }

        return $preview_url;
    }


    protected function assignRoomType($product) {

        if (!isset($product)) {
            $product = new Product((int)Tools::getValue('id_product'));
        }

        if (Validate::isLoadedObject($product)) {
            if ($id_hotel = Tools::getValue('id_hotel')) {
                $objRoomType = new HotelRoomType();
                $objRoomType->id_product = $product->id;
                $objRoomType->id_hotel = $id_hotel;
                $objRoomType->save();
            }
        }
    }

    /**
     * @param Product $obj
     *
     * @throws Exception
     * @throws PrestaShopException
     * @throws SmartyException
     */
    public function initFormConfiguration($obj)
    {
        $data = $this->createTemplate($this->tpl_form);
        if ($obj->id) {
            if ($this->product_exists_in_shop) {
                $objHotelInfo = new HotelBranchInformation();
                $hotelInfo = $objHotelInfo->hotelsNameAndId();
                if ($hotelInfo) {
                    $objRoomInfo = new HotelRoomInformation();
                    $roomStatus = $objRoomInfo->getAllRoomStatus();

                    $objRoomType = new HotelRoomType();
                    if ($hotelRoomType = $objRoomType->getRoomTypeInfoByIdProduct($obj->id)) {
                        $data->assign('htl_room_type', $hotelRoomType);

                        $hotelFullInfo = $objHotelInfo->hotelBranchInfoById($hotelRoomType['id_hotel']);
                        $data->assign('htl_full_info', $hotelFullInfo);

                        $objRoomDisableDates = new HotelRoomDisableDates();
                        $hotelRoomInfo = $objRoomInfo->getHotelRoomInfo($obj->id, $hotelRoomType['id_hotel']);
                        if ($hotelRoomInfo) {
                            foreach ($hotelRoomInfo as &$room) {
                                $bookedDates = $objRoomInfo->getFutureBookings($room['id']);
                                foreach($bookedDates as &$bookedDate) {
                                    $bookedDate['date_from_formatted'] = Tools::displayDate($bookedDate['date_from']);
                                    $bookedDate['date_to_formatted'] = Tools::displayDate($bookedDate['date_to']);
                                }
                                $room['booked_dates'] = json_encode($bookedDates);

                                if ($room['id_status'] == HotelRoomInformation::STATUS_TEMPORARY_INACTIVE) {
                                    $disabledDates = $objRoomDisableDates->getRoomDisableDates($room['id']);
                                    $room['disable_dates_json'] = json_encode($disabledDates);
                                }
                            }
                            $data->assign('htl_room_info', $hotelRoomInfo);
                        }
                    }

                    $data->assign(
                        array(
                            'product' => $obj,
                            'htl_info' => $hotelInfo,
                            'rm_status' => $roomStatus,
                        )
                    );
                } else {
                    $this->displayWarning($this->l('Add Hotel Before configurate this room type.'));
                }
            } else {
                $this->displayWarning($this->l('You must save the room type in this shop before managing hotel configuration.'));
            }
        } else {
            $this->displayWarning($this->l('You must save this room type before managing hotel configuration.'));
        }

        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }

    public function initFormServiceProduct($obj)
    {
        $data = $this->createTemplate($this->tpl_form);
        if ($obj->id) {
            $address = new Address();
            $address->id_country = (int)$this->context->country->id;
            $tax_rules_groups = TaxRulesGroup::getTaxRulesGroups(true);
            $tax_rates = array(
                0 => array(
                    'id_tax_rules_group' => 0,
                    'rates' => array(0),
                    'computation_method' => 0
                )
            );

            foreach ($tax_rules_groups as $tax_rules_group) {
                $id_tax_rules_group = (int)$tax_rules_group['id_tax_rules_group'];
                $tax_calculator = TaxManagerFactory::getManager($address, $id_tax_rules_group)->getTaxCalculator();
                $tax_rates[$id_tax_rules_group] = array(
                    'id_tax_rules_group' => $id_tax_rules_group,
                    'rates' => array(),
                    'computation_method' => (int)$tax_calculator->computation_method
                );

                if (isset($tax_calculator->taxes) && count($tax_calculator->taxes)) {
                    foreach ($tax_calculator->taxes as $tax) {
                        $tax_rates[$id_tax_rules_group]['rates'][] = (float)$tax->rate;
                    }
                } else {
                    $tax_rates[$id_tax_rules_group]['rates'][] = 0;
                }
            }

            $objRoomType = new HotelRoomType();
            if ($hotelRoomType = $objRoomType->getRoomTypeInfoByIdProduct($obj->id)) {
                $allServiceProducts = $obj->getServiceProducts();

                $objRoomTypeServiceProduct = new RoomTypeServiceProduct();
                $objRoomTypeServiceProductPrice = new RoomTypeServiceProductPrice();

                // change index of array to id_product for faster access
                $tmpServiceProducts = $objRoomTypeServiceProduct->getProductsForRoomType($obj->id);
                $roomTypeServiceProducts = array();
                foreach ($tmpServiceProducts as $serviceProduct) {
                    $roomTypeServiceProducts[$serviceProduct['id_product']] = $serviceProduct;
                }

                // classify all service products into associated and unassociated groups
                $associatedServiceProducts = array();
                $unassociatedServiceProducts = array();
                $idsRoomTypeServiceProduct = array_column($roomTypeServiceProducts, 'id_product');
                foreach ($allServiceProducts as &$serviceProduct) {
                    if (in_array($serviceProduct['id_product'], $idsRoomTypeServiceProduct)) {
                        $associationInfo = array();

                        $objProduct = new Product($serviceProduct['id_product'], false, $this->context->language->id);
                        $serviceProductPriceInfo = $objRoomTypeServiceProductPrice->getProductRoomTypeLinkPriceInfo(
                            $objProduct->id,
                            $obj->id,
                            RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE
                        );

                        $associationInfo['id_room_type_service_product'] = $roomTypeServiceProducts[$serviceProduct['id_product']]['id_room_type_service_product'];
                        $associationInfo['position'] = $roomTypeServiceProducts[$serviceProduct['id_product']]['position'];
                        $associationInfo['id_product'] = $objProduct->id;
                        $associationInfo['name'] = $objProduct->name;
                        $associationInfo['auto_add_to_cart'] = $objProduct->auto_add_to_cart;
                        $associationInfo['price_addition_type'] = $objProduct->price_addition_type;
                        $associationInfo['category'] = $objProduct->category;
                        $associationInfo['default_price'] = $objProduct->price;
                        $associationInfo['id_tax_rules_group'] = $objProduct->id_tax_rules_group;

                        // assign custom price only if it is saved for service product
                        if ($serviceProductPriceInfo) {
                            $associationInfo['custom_price'] = $serviceProductPriceInfo['price'];
                            $associationInfo['id_tax_rules_group'] = $serviceProductPriceInfo['id_tax_rules_group'];
                            if ($serviceProductPriceInfo['id_tax_rules_group'] == 0) {
                                $associationInfo['tax_rules_group_name'] = $this->l('No tax');
                            } else {
                                $objTaxRuleGroup = new TaxRulesGroup(
                                    $serviceProductPriceInfo['id_tax_rules_group'],
                                    $this->context->language->id
                                );
                                $associationInfo['tax_rules_group_name'] = $objTaxRuleGroup->name;
                            }
                            $associationInfo['id_room_type_service_product_price'] = $serviceProductPriceInfo['id_room_type_service_product_price'];
                        }

                        if ($objProduct->id_tax_rules_group == 0) {
                            $associationInfo['default_tax_rules_group_name'] = $this->l('No tax');
                        } else {
                            $objTaxRuleGroup = new TaxRulesGroup(
                                $objProduct->id_tax_rules_group,
                                $this->context->language->id
                            );
                            $associationInfo['default_tax_rules_group_name'] = $objTaxRuleGroup->name;
                        }

                        $serviceProduct['is_associated'] = true;
                        $serviceProduct['association_info'] = $associationInfo;

                        $associatedServiceProducts[$associationInfo['position']] = $serviceProduct;
                    } else {
                        $serviceProduct['is_associated'] = false;

                        $objTaxRulesGroup = new TaxRulesGroup($serviceProduct['id_tax_rules_group'], $this->context->language->id);
                        $serviceProduct['tax_rules_group_name'] = $objTaxRulesGroup->name;

                        $unassociatedServiceProducts[] = $serviceProduct;
                    }
                }

                ksort($associatedServiceProducts);

                $data->assign(array(
                    'product' => $obj,
                    'currency' => $this->context->currency,
                    'associated_service_products' => $associatedServiceProducts,
                    'unassociated_service_products' => $unassociatedServiceProducts,
                    'tax_rules_groups' => $tax_rules_groups,
                    'taxesRatesByGroup' => $tax_rates,
                ));
            }
        } else {
            $this->displayWarning($this->l('You must save this room type before managing Service Products.'));
        }

        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }

    public function processServiceProduct()
    {
        $idProduct = Tools::getValue('id_product');

        if (!$idProduct || !Validate::isUnsignedInt($idProduct) || !Product::isBookingProduct($idProduct)) {
            $this->errors[] = $this->l('Something went wrong while saving service products.');
        }

        if (Validate::isLoadedObject($objProduct = new Product($idProduct))) {
            // validate submitted details
            $availableServiceProducts = Tools::getValue('available_service_products');
            $objServiceProducts = array();
            foreach ($availableServiceProducts as $idServiceProduct) {
                $prefix = 'service_product_'.$idServiceProduct.'_';

                $isAssociated = in_array(Tools::getValue($prefix.'associated'), array('on', 'true', '1'));
                $price = Tools::getValue($prefix.'price');
                $idTaxRulesGroup = Tools::getValue($prefix.'id_tax_rules_group');

                if ($isAssociated) {
                    $objServiceProduct = new Product($idServiceProduct, false, $this->context->language->id);
                    if (Validate::isLoadedObject($objServiceProduct)) {
                        // cache for faster access in next foreach loop
                        $objServiceProducts[$idServiceProduct] = $objServiceProduct;

                        if (!$price) {
                            $this->errors[] = sprintf($this->l('Price for service product \'%s\' is empty.'), $objServiceProduct->name);
                        } elseif (!Validate::isPrice($price)) {
                            $this->errors[] = sprintf($this->l('Price for service product \'%s\' is invalid.'), $objServiceProduct->name);
                        }
                    } else{
                        $this->errors[] = sprintf($this->l('Service product #%s is not available.'), $idServiceProduct);
                    }
                }
            }

            if (!count($this->errors)) {
                // save submitted details
                foreach ($availableServiceProducts as $idServiceProduct) {
                    $prefix = 'service_product_'.$idServiceProduct.'_';

                    $isAssociated = in_array(Tools::getValue($prefix.'associated'), array('on', 'true', '1'));
                    $price = Tools::getValue($prefix.'price');
                    $idTaxRulesGroup = Tools::getValue($prefix.'id_tax_rules_group');

                    if ($isAssociated) {
                        $objRoomTypeServiceProduct = new RoomTypeServiceProduct();

                        // if already associated
                        if ($objRoomTypeServiceProduct->isRoomTypeLinkedWithProduct($idProduct, $idServiceProduct)) {
                            $objRoomTypeServiceProductPrice = new RoomTypeServiceProductPrice();
                            $priceInfo = $objRoomTypeServiceProductPrice->getProductRoomTypeLinkPriceInfo(
                                $idServiceProduct,
                                $idProduct,
                                RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE
                            );

                            $objRoomTypeServiceProductPrice = null;
                            if ($priceInfo) {
                                $objRoomTypeServiceProductPrice = new RoomTypeServiceProductPrice($priceInfo['id_room_type_service_product_price']);
                            } else {
                                $objRoomTypeServiceProductPrice = new RoomTypeServiceProductPrice();
                                $objRoomTypeServiceProductPrice->id_product = $idServiceProduct;
                                $objRoomTypeServiceProductPrice->id_element = $idProduct;
                                $objRoomTypeServiceProductPrice->element_type = RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE;
                            }
                            $objRoomTypeServiceProductPrice->price = $price;
                            $objRoomTypeServiceProductPrice->id_tax_rules_group = $idTaxRulesGroup;
                            $objRoomTypeServiceProductPrice->save();
                        } else {
                            // create new association
                            $objRoomTypeServiceProduct = new RoomTypeServiceProduct();
                            $objRoomTypeServiceProduct->addRoomProductLink(
                                $idServiceProduct,
                                $idProduct,
                                RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE
                            );

                            $objRoomTypeServiceProductPrice = new RoomTypeServiceProductPrice();
                            $objRoomTypeServiceProductPrice->id_product = $idServiceProduct;
                            $objRoomTypeServiceProductPrice->id_element = $idProduct;
                            $objRoomTypeServiceProductPrice->element_type = RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE;
                            $objRoomTypeServiceProductPrice->price = $price;
                            $objRoomTypeServiceProductPrice->id_tax_rules_group = $idTaxRulesGroup;
                            $objRoomTypeServiceProductPrice->save();
                        }
                    } else {
                        // remove association
                        RoomTypeServiceProduct::deleteRoomProductLink(
                            $idServiceProduct,
                            RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE,
                            $idProduct
                        );

                        RoomTypeServiceProductPrice::deleteRoomProductPrices(
                            $idServiceProduct,
                            RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE,
                            $idProduct
                        );
                    }
                }
            }
        } else {
            $this->errors[] = $this->l('Please save room type details before saving service products.');
        }
    }

    // send information for the occupancy tab
    public function initFormOccupancy($obj)
    {
        $data = $this->createTemplate($this->tpl_form);
        if ($obj->id) {
            $smartyVars['product'] = $obj;
            if ($this->product_exists_in_shop) {
                // Check is any hotel is created or not
                $objHotelInfo = new HotelBranchInformation();
                if ($objHotelInfo->hotelsNameAndId()) {
                    $objRoomType = new HotelRoomType();
                    if ($roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($obj->id)) {
                        $smartyVars['roomTypeInfo'] = $roomTypeInfo;
                    }
                } else {
                    $this->displayWarning($this->l('Add Hotel Before configurate this room type.'));
                }
            } else {
                $this->displayWarning($this->l('You must save the room type in this shop before managing occupancy.'));
            }
        } else {
            $this->displayWarning($this->l('You must save this room type before managing occupancy.'));
        }
        $smartyVars['currency'] = $this->context->currency;
        $data->assign($smartyVars);
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }

    // save occupancy information
    public function processOccupancy()
    {
        $idProduct = Tools::getValue('id_product');
        if ((int)Tools::getValue('is_occupancy_submit')
            && Validate::isLoadedObject($product = new Product((int)$idProduct)
        )) {
            // htl_room_type id field use only in edit case
            if ($idHtlRoomType = Tools::getValue('wk_id_room_type')) {
                if (Validate::isLoadedObject($objRoomType = new HotelRoomType($idHtlRoomType))) {
                    $baseAdults = Tools::getValue('base_adults');
                    $baseChildren = Tools::getValue('base_children');
                    $maxAdults = Tools::getValue('max_adults');
                    $maxChildren = Tools::getValue('max_children');
                    $maxGuests = Tools::getValue('max_guests');

                    if (!$baseAdults || !Validate::isUnsignedInt($baseAdults)) {
                        $this->errors[] = Tools::displayError('Invalid base adults');
                    }
                    if ($baseChildren == '' || !Validate::isUnsignedInt($baseChildren)) {
                        $this->errors[] = Tools::displayError('Invalid base children');
                    } else if (Configuration::get('WK_GLOBAL_MAX_CHILD_IN_ROOM')) {
                        if ($baseChildren > Configuration::get('WK_GLOBAL_MAX_CHILD_IN_ROOM')) {
                            $this->errors[] = sprintf(Tools::displayError('Base children cannot be greater than max childern allowed on your website (Max: %s)'), Configuration::get('WK_GLOBAL_MAX_CHILD_IN_ROOM'));
                        }
                    }
                    if (!$maxAdults || !Validate::isUnsignedInt($maxAdults)) {
                        $this->errors[] = Tools::displayError('Invalid maximum number of adults');
                    } elseif ($maxAdults < $baseAdults) {
                        $this->errors[] = Tools::displayError('Maximum number of adults cannot be less than base adults');
                    }
                    if ($maxChildren == '' || !Validate::isUnsignedInt($maxChildren)) {
                        $this->errors[] = Tools::displayError('Invalid maximum number of children');
                    } else if (Configuration::get('WK_GLOBAL_MAX_CHILD_IN_ROOM')) {
                        if ($maxChildren > Configuration::get('WK_GLOBAL_MAX_CHILD_IN_ROOM')) {
                            $this->errors[] = sprintf(Tools::displayError('Maximum number of children cannot be greater than max childern allowed on your website (Max: %s)'), Configuration::get('WK_GLOBAL_MAX_CHILD_IN_ROOM'));
                        }
                    } elseif ($maxChildren < $baseChildren) {
                        $this->errors[] = Tools::displayError('Maximum number of children cannot be less than base children');
                    }
                    if (!$maxGuests || !Validate::isUnsignedInt($maxGuests)) {
                        $this->errors[] = Tools::displayError('Invalid maximum number of guests');
                    } elseif ($maxGuests < ($baseAdults + $baseChildren)) {
                        $this->errors[] = Tools::displayError('Maximum number of guests cannot be less than base occupancy of adults and children');
                    } elseif ($maxGuests > ($maxChildren + $maxAdults)) {
                        $this->errors[] = Tools::displayError('Maximum number of guests cannot be more than max occupancy of adults and children');
                    }

                    if (!count($this->errors)) {
                        $objRoomType->adults = $baseAdults;
                        $objRoomType->children = $baseChildren;
                        $objRoomType->max_adults = $maxAdults;
                        $objRoomType->max_children = $maxChildren;
                        $objRoomType->max_guests = $maxGuests;
                        $objRoomType->save();
                    }
                } else {
                    $this->errors[] = Tools::displayError('Invalid room type found.');
                }
            } else {
                $this->errors[] = Tools::displayError('Please save hotel of the room type from configuration tab.');
            }
        }
    }

    // send information for the length of stay tab
    public function initFormLengthOfStay($product)
    {
        $data = $this->createTemplate($this->tpl_form);
        if ($product->id) {
            if ($this->product_exists_in_shop) {
                // Check if any hotel is created or not
                $objRoomType = new HotelRoomType();
                if ($roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($product->id)) {
                    if ($roomTypeInfo['id_hotel']) {
                        // send length of stay date ranges for this room type
                        $objRoomTypeRestrictionDates = new HotelRoomTypeRestrictionDateRange();
                        $roomTypeInfo['restrictionDataRange'] = $objRoomTypeRestrictionDates->getRoomTypeLengthOfStayRestriction($product->id);
                        $smartyVars['roomTypeInfo'] = $roomTypeInfo;
                    } else {
                        $this->displayWarning($this->l('No hotel is attached to this room type.'));
                    }
                } else {
                    $this->displayWarning($this->l('Room type information is missing.'));
                }
            } else {
                $this->displayWarning($this->l('You must save room type before managing length of stay.'));
            }
        } else {
            $this->displayWarning($this->l('You must save room type before managing length of stay.'));
        }

        $smartyVars['product'] = $product;
        $data->assign($smartyVars);
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }

    public function processLengthOfStay()
    {
        if ($this->tabAccess['edit'] == 1) {

            $idProduct = Tools::getValue('id_product');
            if (Validate::isLoadedObject($product = new Product((int)$idProduct))) {
                $objRoomType = new HotelRoomType();
                if ($roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($idProduct)) {
                    $roomTypeMinLos = Tools::getValue('min_los');
                    $roomTypeMaxLos = Tools::getValue('max_los');

                    // validate length of stay global values for room type
                    if (!$roomTypeMinLos || $roomTypeMinLos == null) {
                        $this->errors[] = Tools::displayError('Global minimum length of stay is a required field.');
                    } elseif (!Validate::isUnsignedInt($roomTypeMinLos)) {
                        $this->errors[] = Tools::displayError('Global minimum length of stay value is invalid. Please enter integer value. Set 1 day incase  of setting no limit on global minimum length of stay.');
                    }

                    if ($roomTypeMaxLos == null) {
                        $this->errors[] = Tools::displayError('Global maximum length of stay is a required field.');
                    } elseif (!Validate::isUnsignedInt($roomTypeMaxLos)) {
                        $this->errors[] = Tools::displayError('Invalid value entered for global maximum length of stay field. Please enter integer value. Set 0 day incase of setting no limit on maximum length of stay.');
                    } elseif ($roomTypeMinLos && $roomTypeMaxLos > 0 && ($roomTypeMinLos > $roomTypeMaxLos)) {
                        $this->errors[] = Tools::displayError('Value of global maximum length of stay must be greater than global minimum length of stay.');
                    }

                    if (Tools::getValue('active_restriction_dates')) {
                        if (Tools::getValue('restriction_date_from') && Tools::getValue('restriction_date_to')) {
                            $objRoomTypeRestrictDateRange = new HotelRoomTypeRestrictionDateRange();
                            $dateFromRestriction = Tools::getValue('restriction_date_from');
                            $dateToRestriction = Tools::getValue('restriction_date_to');
                            $minLosDays = Tools::getValue('restriction_min_los');
                            $maxLosDays = Tools::getValue('restriction_max_los');

                            $this->errors = array_merge($this->errors, $objRoomTypeRestrictDateRange->validateRoomTypeLengthOfStayRestriction($dateFromRestriction, $dateToRestriction, $minLosDays, $maxLosDays));
                        } else {
                            $this->errors[] = Tools::displayError('Please add at least one Minimum & maximum length of stay restriction for date range if \'Length of stay for date ranges\' option is enable.');
                        }
                    }

                    if (!$this->errors) {
                        $objRoomType = new HotelRoomType($roomTypeInfo['id']);
                        $objRoomType->min_los = $roomTypeMinLos;
                        $objRoomType->max_los = $roomTypeMaxLos;
                        if ($objRoomType->save()) {
                            if (Tools::getValue('active_restriction_dates') && Tools::getValue('restriction_date_from')) {
                                // @ToDo: we should validate this room type restriction ids belongs to this room type
                                $idRoomTypeRestriction = Tools::getValue('id_rt_restriction');

                                foreach ($dateFromRestriction as $restrictionKey => $dateFrom) {
                                    // change into database compatible format
                                    $dateFrom = date('Y-m-d', strtotime($dateFrom));
                                    $dateTo = date('Y-m-d', strtotime($dateToRestriction[$restrictionKey]));

                                    if ($idRoomTypeRestriction[$restrictionKey]) {
                                        $objRoomTypeRestrictDateRange = new HotelRoomTypeRestrictionDateRange($idRoomTypeRestriction[$restrictionKey]);
                                    } else {
                                        $objRoomTypeRestrictDateRange = new HotelRoomTypeRestrictionDateRange();
                                    }

                                    $objRoomTypeRestrictDateRange->id_product = $idProduct;
                                    $objRoomTypeRestrictDateRange->date_from = $dateFrom;
                                    $objRoomTypeRestrictDateRange->date_to = $dateTo;
                                    $objRoomTypeRestrictDateRange->min_los = $minLosDays[$restrictionKey];
                                    $objRoomTypeRestrictDateRange->max_los = $maxLosDays[$restrictionKey];
                                    $objRoomTypeRestrictDateRange->save();
                                }
                            } else {
                                // if disabled length of stay for date ranges then delete all previously saved
                                $objRoomTypeRestrictDateRange = new HotelRoomTypeRestrictionDateRange();
                                if ($losRestrictions = $objRoomTypeRestrictDateRange->getRoomTypeLengthOfStayRestriction($idProduct)) {
                                    foreach ($losRestrictions as $losDate) {
                                        $objRoomTypeRestrictDateRange = new HotelRoomTypeRestrictionDateRange($losDate['id_rt_restriction']);
                                        $objRoomTypeRestrictDateRange->delete();
                                    }
                                }
                            }
                        } else {
                            $this->errors[] = Tools::displayError('Something went wrong while saving global minimum & maximum length of stay values. Please try again !!');
                        }
                    }
                }
            }
        } else {
            $this->errors[] = Tools::displayError('You do not have the right permission.');
        }
    }

    // delete the rows of length of stay on date range
    public function ajaxProcessDeleteRoomTypeLengthOfStayRestriction()
    {
        if ($this->tabAccess['edit'] == 1) {
            $objRoomTypeRestrictionDates = new HotelRoomTypeRestrictionDateRange(Tools::getValue('id_rt_restriction'));
            if ($objRoomTypeRestrictionDates->delete()) {
                die(json_encode(array('success' => $this->l('Successfully deleted'))));
            } else {
                die(json_encode(array('error' => $this->l('Something went wrong. Please reload the page and try again !!'))));
            }
        } else {
            die(json_encode(array('error' => $this->l('You do not have the right permission'))));
        }
    }

    public function validateDisableDateRanges($disableDates, $roomIndex, $idRoom)
    {
        if (is_array($disableDates) && count($disableDates)) {
            foreach ($disableDates as $disable_key => $disableDate) {
                if (!$disableDate['date_to'] && !$disableDate['date_from']) {
                    unset($disableDates[$disable_key]);
                } elseif (!Validate::isDate($disableDate['date_from']) || !Validate::isDate($disableDate['date_to'])) {
                    $this->errors[] = sprintf(
                        Tools::displayError('Please add valid disable dates for room %s.'),
                        $roomIndex
                    );
                } elseif (($disableDate['date_from'] && !$disableDate['date_to']) || (!$disableDate['date_from'] && $disableDate['date_to'])) {
                    $this->errors[] = sprintf(
                        Tools::displayError('Please fill date from and date to for disable dates for room %s.'),
                        $roomIndex
                    );
                } else {
                    $objHotelBookingDetail = new HotelBookingDetail();
                    foreach ($disableDates as $key => $disDate) {
                        if ($key != $disable_key) {
                            if ((($disableDate['date_from'] < $disDate['date_from']) && ($disableDate['date_to'] <= $disDate['date_from'])) || (($disableDate['date_from'] > $disDate['date_from']) && ($disableDate['date_from'] >= $disDate['date_to']))) {
                                // continue
                            } else {
                                $this->errors[] = sprintf(
                                    Tools::displayError('Disable dates are conflicting for room %s. Please add non-conflicting dates.'),
                                    $roomIndex
                                );
                            }
                        }
						// check if room has booking for current date range
						if ($objHotelBookingDetail->chechRoomBooked($idRoom, $disDate['date_from'], $disDate['date_to'])) {
							$this->errors[] = sprintf(
								Tools::displayError('The room %s already has bookings for selected disable dates. Please reselect disable dates.'),
								$roomIndex
							);
						}
                    }
                }
            }
        } else {
            $this->errors[] = sprintf(Tools::displayError('Please add disable dates for room %s.'), $roomIndex);
        }
    }

    public function processConfiguration()
    {
        // Check if save of configuration tab is submitted
        if (Tools::getValue('checkConfSubmit')) {
            $id_product = Tools::getValue('id_product');
            $id_hotel = Tools::getValue('id_hotel');

            if (!$id_product || !Validate::isUnsignedInt($id_product)) {
                $this->errors[] = Tools::displayError('There is some problem while setting room information.');
            }
            if (!$id_hotel || !Validate::isUnsignedInt($id_hotel)) {
                $this->errors[] = Tools::displayError('Please select a hotel.');
            }

            $this->validateConfigurationPostData();
            if (!count($this->errors)) {
                $roomsInfo = Tools::getValue('rooms_info');
                if (is_array($roomsInfo) && count($roomsInfo)) {
                    foreach ($roomsInfo as $roomInfo) {
                        $objHotelRoomInfo = null;
                        if (isset($roomInfo['id']) && $roomInfo['id']) {
                            $objHotelRoomInfo = new HotelRoomInformation($roomInfo['id']);
                        } else {
                            $objHotelRoomInfo = new HotelRoomInformation();
                        }
                        $objHotelRoomInfo->id_product = $id_product;
                        $objHotelRoomInfo->id_hotel = $id_hotel;
                        $objHotelRoomInfo->room_num = $roomInfo['room_num'];
                        $objHotelRoomInfo->id_status = $roomInfo['id_status'];
                        $objHotelRoomInfo->floor = $roomInfo['floor'];
                        $objHotelRoomInfo->comment = $roomInfo['comment'];
                        if ($objHotelRoomInfo->save()) {
                            $idRoom = $objHotelRoomInfo->id;
                            if ($roomInfo['id_status'] == HotelRoomInformation::STATUS_TEMPORARY_INACTIVE) {
                                $objHotelRoomDisableDates = new HotelRoomDisableDates();
                                $objHotelRoomDisableDates->deleteRoomDisableDates($idRoom);

                                $disableDates = json_decode($roomInfo['disable_dates_json'], true);
                                foreach ($disableDates as $disableDate) {
                                    $objHotelRoomDisableDates = new HotelRoomDisableDates();
                                    $objHotelRoomDisableDates->id_room_type = $id_product;
                                    $objHotelRoomDisableDates->id_room = $idRoom;
                                    $objHotelRoomDisableDates->date_from = $disableDate['date_from'];
                                    $objHotelRoomDisableDates->date_to = $disableDate['date_to'];
                                    $objHotelRoomDisableDates->reason = $disableDate['reason'];
                                    $objHotelRoomDisableDates->add();
                                }

                                Hook::exec(
                                    'actionRoomDisableDatesAddAfter',
                                    array(
                                        'room_info' => $roomInfo,
                                        'disable_dates' => $disableDates
                                    )
                                );
                            }
                        }
                    }
                }
            }
        }
    }

    public function validateConfigurationPostData()
    {
        $roomsInfo = Tools::getValue('rooms_info');
        if (is_array($roomsInfo) && count($roomsInfo)) {
            foreach ($roomsInfo as $key => $roomInfo) {
                if (!$roomInfo['room_num']) {
                    unset($_POST['rooms_info'][$key]);
                }
            }
        }

        $roomsInfo = Tools::getValue('rooms_info'); // since $_POST['rooms_info'] has changed
        if (is_array($roomsInfo) && count($roomsInfo)) {
            foreach ($roomsInfo as $key => $roomInfo) {
                $roomIndex = $key + 1;

                if ($roomInfo['id_status'] == HotelRoomInformation::STATUS_TEMPORARY_INACTIVE) {
                    if ($roomInfo['disable_dates_json'] === 0) {
                        $this->errors[] = sprintf(Tools::displayError('Please add disable dates for room %s.'), $roomIndex);
                    }
                }

                if ($roomInfo['room_num'] && !Validate::isGenericName($roomInfo['room_num'])) {
                    $this->errors[] = sprintf(Tools::displayError('Invalid room number for room %s.'), $roomIndex);
                }

                if ($roomInfo['floor'] && !Validate::isGenericName($roomInfo['floor'])) {
                    $this->errors[] = sprintf(Tools::displayError('Invalid floor for room %s.'), $roomIndex);
                }

                if ($roomInfo['id_status'] == HotelRoomInformation::STATUS_INACTIVE) {
                    $objHotelRoomInformation = new HotelRoomInformation();
                    if (count($objHotelRoomInformation->getFutureBookings($roomInfo['id']))) {
                        $this->errors[] = sprintf(Tools::displayError('Cannot change room %s status to inactive as it already has some bookings, Please check the bookings and move those bookings to another room if you want make this room inactive'), $roomInfo['room_num']);
                    }
                } elseif ($roomInfo['id_status'] == HotelRoomInformation::STATUS_TEMPORARY_INACTIVE) {
                    $disableDates = json_decode($roomInfo['disable_dates_json'], true);
                    if ($roomInfo['disable_dates_json'] !== 0) {
                        $this->validateDisableDateRanges($disableDates, $roomInfo['room_num'], $roomInfo['id']);
                    }
                }
            }
        } else {
            $this->errors[] = Tools::displayError('Please add at least one room.');
        }
    }

    public function ajaxProcessDeleteHotelRoom()
    {
        $response = array(
            'success' => false
        );
        if ($this->tabAccess['edit'] == 1) {
            $idRoom = Tools::getValue('id');
            $objRoomInfo = new HotelRoomInformation((int)$idRoom);
            $objHotelRoomInformation = new HotelRoomInformation();
            if ($objHotelRoomInformation->getFutureBookings($idRoom)) {
                $this->errors[] = $this->l('This room cannot be deleted as this room contains future booking.');
            }
            if (empty($this->errors)) {
                if ($objRoomInfo->delete()) {
                    $response['success'] = true;
                } else {
                    $this->errors[] = $this->l('Unable to delete room. Please try again!.');
                }
            }
        }
        if (!empty($this->errors)) {
            $response['errors'] = $this->errors;
        }
        die(json_encode($response));
    }

    public function initFormAdditionalFacilities($obj)
    {
        $data = $this->createTemplate($this->tpl_form);

        if ($obj->id) {
            $objGlobalDemand = new HotelRoomTypeGlobalDemand();
            $allDemands = $objGlobalDemand->getAllDemands();
            foreach($allDemands as &$demand) {
                if ($demand['id_tax_rules_group'] == 0) {
                    $demand['default_tax_rules_group_name'] = 'No tax';
                } else {
                    $objTaxRuleGroup = new TaxRulesGroup(
                        $demand['id_tax_rules_group'],
                        $this->context->language->id
                    );
                    $demand['default_tax_rules_group_name'] = $objTaxRuleGroup->name;
                }
            }
            $objCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

            // get room type additional facilities
            $objRoomDemand = new HotelRoomTypeDemand();
            $selectedDemands = $objRoomDemand->getRoomTypeDemands($obj->id, 0, 0);

            $data->assign(array(
                'product' => $obj,
                'selectedDemands' => $selectedDemands,
                'allDemands' => $allDemands,
                'defaultcurrencySign' => $objCurrency->sign,
                'idDefaultcurrency' => $objCurrency->id,
            ));
        } else {
            $this->displayWarning($this->l('You must save this room type before managing additional facilities.'));
        }

        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }

    public function processAdditionalFacilities()
    {
        if ($idProduct = Tools::getValue('id_product')) {
            $objRoomTypeDemand = new HotelRoomTypeDemand();
            $objRoomTypeDemandPrice = new HotelRoomTypeDemandPrice();
            // first delete all the previously saved prices and demands of this room type
            $objRoomTypeDemand->deleteRoomTypeDemands($idProduct);
            $objRoomTypeDemandPrice->deleteRoomTypeDemandPrices($idProduct);
            if ($selectedDemands = Tools::getValue('selected_demand')) {
                $objAdvOption = new HotelRoomTypeGlobalDemandAdvanceOption();
                foreach ($selectedDemands as $idGlobalDemand) {
                    if (Validate::isLoadedObject($objGlobalDemand = new HotelRoomTypeGlobalDemand($idGlobalDemand))) {
                        // save selected demands for this room type
                        $objRoomTypeDemand = new HotelRoomTypeDemand();
                        $objRoomTypeDemand->id_product = $idProduct;
                        $objRoomTypeDemand->id_global_demand = $idGlobalDemand;
                        $objRoomTypeDemand->save();

                        // save selected demands prices for this room type
                        $demandPrice = Tools::getValue('demand_price_'.$idGlobalDemand);
                        if (Validate::isPrice($demandPrice)) {
                            if ($objGlobalDemand->price != $demandPrice) {
                                $objRoomTypeDemandPrice = new HotelRoomTypeDemandPrice();
                                $objRoomTypeDemandPrice->id_product = $idProduct;
                                $objRoomTypeDemandPrice->id_global_demand = $idGlobalDemand;
                                $objRoomTypeDemandPrice->id_option = 0;
                                $objRoomTypeDemandPrice->price = $demandPrice;
                                $objRoomTypeDemandPrice->save();
                            }
                        } else {
                            $this->errors[] = Tools::displayError('Invalid demand price of facility.').
                            ' : '.$objGlobalDemand->name[$this->context->language->id];
                        }
                        if ($advOptions = $objAdvOption->getGlobalDemandAdvanceOptions($idGlobalDemand)) {
                            foreach ($advOptions as $option) {
                                if (Validate::isLoadedObject($objAdvOption = new HotelRoomTypeGlobalDemandAdvanceOption($option['id']))) {
                                    $optionPrice = Tools::getValue('option_price_'.$option['id']);
                                    if (Validate::isPrice($optionPrice)) {
                                        if ($optionPrice != $objAdvOption->price) {
                                            $objRoomTypeDemandPrice = new HotelRoomTypeDemandPrice();
                                            $objRoomTypeDemandPrice->id_product = $idProduct;
                                            $objRoomTypeDemandPrice->id_global_demand = $idGlobalDemand;
                                            $objRoomTypeDemandPrice->id_option = $option['id'];
                                            $objRoomTypeDemandPrice->price = $optionPrice;
                                            $objRoomTypeDemandPrice->save();
                                        }
                                    } else {
                                        $this->errors[] = Tools::displayError('Invalid price of advanced option: ').$objAdvOption->name[$this->context->language->id];
                                    }
                                }
                            }
                        }
                    }
                }
                if (count($this->errors)) {
                    $this->warnings[] = Tools::displayError('Invalid price values are not saved. Please correct them and save again.');
                }

                $objCartBookingData = new HotelCartBookingData();
                if ($cartExtraDemands = $objCartBookingData->getCartExtraDemands(0, $idProduct)) {
                    // delete the demands from cart if not available in cart
                    $objRoomDemand = new HotelRoomTypeDemand();
                    $roomTypeDemandIds = array();
                    if ($roomTypeDemands = $objRoomDemand->getRoomTypeDemands($idProduct)) {
                        $roomTypeDemandIds = array_keys($roomTypeDemands);
                    }
                    foreach ($cartExtraDemands as &$demandInfo) {
                        if (isset($demandInfo['extra_demands']) && $demandInfo['extra_demands']) {
                            $cartChanged = 0;
                            foreach ($demandInfo['extra_demands'] as $key => $demand) {
                                if (!in_array($demand['id_global_demand'], $roomTypeDemandIds)) {
                                    $cartChanged = 1;
                                    unset($demandInfo['extra_demands'][$key]);
                                }
                            }
                            if ($cartChanged) {
                                if (Validate::isLoadedObject(
                                    $objCartBooking = new HotelCartBookingData($demandInfo['id'])
                                )) {
                                    $objCartBooking->extra_demands = json_encode($demandInfo['extra_demands']);
                                    $objCartBooking->save();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param Product $obj
     *
     * @throws Exception
     * @throws PrestaShopException
     * @throws SmartyException
     */
    public function initFormAssociations($obj)
    {
        $data = $this->createTemplate($this->tpl_form);

        if ($obj->id) {
            $product = $obj;
            // Prepare Categories tree for display in Associations tab
            $root = Category::getRootCategory();
            $default_category = $this->context->cookie->id_category_room_types_filter ? $this->context->cookie->id_category_room_types_filter : Context::getContext()->shop->id_category;
            if (!$product->id || !$product->isAssociatedToShop()) {
                $selected_cat = Category::getCategoryInformations(Tools::getValue('categoryBox', array($default_category)), $this->default_form_language);
            } else {
                if (Tools::isSubmit('categoryBox')) {
                    $selected_cat = Category::getCategoryInformations(Tools::getValue('categoryBox', array($default_category)), $this->default_form_language);
                } else {
                    $selected_cat = Product::getProductCategoriesFull($product->id, $this->default_form_language);
                }
            }

            // Multishop block
            $data->assign('feature_shop_active', Shop::isFeatureActive());
            $helper = new HelperForm();
            if ($this->object && $this->object->id) {
                $helper->id = $this->object->id;
            } else {
                $helper->id = null;
            }
            $helper->table = $this->table;
            $helper->identifier = $this->identifier;

            // Accessories block
            $accessories = Product::getAccessoriesLight($this->context->language->id, $product->id);

            if ($post_accessories = Tools::getValue('inputAccessories')) {
                $post_accessories_tab = explode('-', $post_accessories);
                foreach ($post_accessories_tab as $accessory_id) {
                    if (!$this->haveThisAccessory($accessory_id, $accessories) && $accessory = Product::getAccessoryById($accessory_id)) {
                        $accessories[] = $accessory;
                    }
                }
            }
            $data->assign('accessories', $accessories);

            $product->manufacturer_name = Manufacturer::getNameById($product->id_manufacturer);

            $categories = array();
            foreach ($selected_cat as $key => $category) {
                $categories[] = $key;
            }

            $tree = new HelperTreeCategories('associated-categories-tree', 'Associated categories');
            $tree->setTemplate('tree_associated_categories.tpl')
                ->setHeaderTemplate('tree_associated_header.tpl')
                ->setRootCategory(Configuration::get('PS_LOCATIONS_CATEGORY'))
                ->setUseCheckBox(true)
                ->setUseSearch(false)
                ->setFullTree(0)
                ->setSelectedCategories($categories)
                ->setUseBulkActions(false)
                ->setDisablAllCategories(true);

            $data->assign(array('default_category' => $default_category,
                        'selected_cat_ids' => implode(',', array_keys($selected_cat)),
                        'selected_cat' => $selected_cat,
                        'id_category_default' => $product->getDefaultCategory(),
                        'category_tree' => $tree->render(),
                        'product' => $product,
                        'link' => $this->context->link,
                        'is_shop_context' => Shop::getContext() == Shop::CONTEXT_SHOP
            ));
        } else {
            $this->displayWarning($this->l('You must save this room type before updating associations.'));
        }

        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }

    /**
     * @param Product $obj
     * @throws Exception
     * @throws SmartyException
     */
    public function initFormPrices($obj)
    {
        $data = $this->createTemplate($this->tpl_form);
        $product = $obj;
        if ($obj->id) {
            $shops = Shop::getShops();
            $countries = Country::getCountries($this->context->language->id);
            $groups = Group::getGroups($this->context->language->id);
            $currencies = Currency::getCurrencies();
            $attributes = $obj->getAttributesGroups((int)$this->context->language->id);
            $combinations = array();
            foreach ($attributes as $attribute) {
                $combinations[$attribute['id_product_attribute']]['id_product_attribute'] = $attribute['id_product_attribute'];
                if (!isset($combinations[$attribute['id_product_attribute']]['attributes'])) {
                    $combinations[$attribute['id_product_attribute']]['attributes'] = '';
                }
                $combinations[$attribute['id_product_attribute']]['attributes'] .= $attribute['attribute_name'].' - ';

                $combinations[$attribute['id_product_attribute']]['price'] = Tools::displayPrice(
                    Tools::convertPrice(
                        Product::getPriceStatic((int)$obj->id, false, $attribute['id_product_attribute']),
                        $this->context->currency
                    ), $this->context->currency
                );
            }
            foreach ($combinations as &$combination) {
                $combination['attributes'] = rtrim($combination['attributes'], ' - ');
            }
            $data->assign('specificPriceModificationForm', $this->_displaySpecificPriceModificationForm(
                $this->context->currency, $shops, $currencies, $countries, $groups)
            );

            $data->assign('ecotax_tax_excl', (float)$obj->ecotax);
            $this->_applyTaxToEcotax($obj);

            $data->assign(array(
                'shops' => $shops,
                'admin_one_shop' => count($this->context->employee->getAssociatedShops()) == 1,
                'currencies' => $currencies,
                'countries' => $countries,
                'groups' => $groups,
                'combinations' => $combinations,
                'multi_shop' => Shop::isFeatureActive(),
                'link' => new Link(),
                'pack' => new Pack()
            ));

            // get hotel address for this room type
            $address_infos = Address::getCountryAndState(Cart::getIdAddressForTaxCalculation($obj->id));
        } else {
            $this->displayWarning($this->l('You must save this room type before adding specific pricing'));
            $product->id_tax_rules_group = (int)Product::getIdTaxRulesGroupMostUsed();
            $data->assign('ecotax_tax_excl', 0);
        }

        $address = new Address();
        // $address->id_country = (int)$this->context->country->id;
        if (!isset($address_infos) || !$address_infos) {
            $address->id_country = (int)$this->context->country->id;
        } else {
            $address->id_country = (int)$address_infos['id_country'];
            $address->id_state = (int)$address_infos['id_state'];
            $address->postcode = $address_infos['postcode'];
        }

        $tax_rules_groups = TaxRulesGroup::getTaxRulesGroups(true);
        $tax_rates = array(
            0 => array(
                'id_tax_rules_group' => 0,
                'rates' => array(0),
                'computation_method' => 0
            )
        );

        foreach ($tax_rules_groups as $tax_rules_group) {
            $id_tax_rules_group = (int)$tax_rules_group['id_tax_rules_group'];
            $tax_calculator = TaxManagerFactory::getManager($address, $id_tax_rules_group)->getTaxCalculator();
            $tax_rates[$id_tax_rules_group] = array(
                'id_tax_rules_group' => $id_tax_rules_group,
                'rates' => array(),
                'computation_method' => (int)$tax_calculator->computation_method
            );

            if (isset($tax_calculator->taxes) && count($tax_calculator->taxes)) {
                foreach ($tax_calculator->taxes as $tax) {
                    $tax_rates[$id_tax_rules_group]['rates'][] = (float)$tax->rate;
                }
            } else {
                $tax_rates[$id_tax_rules_group]['rates'][] = 0;
            }
        }

        // prices part
        $data->assign(array(
            'link' => $this->context->link,
            'currency' => $currency = $this->context->currency,
            'tax_rules_groups' => $tax_rules_groups,
            'taxesRatesByGroup' => $tax_rates,
            'ecotaxTaxRate' => Tax::getProductEcotaxRate(),
            'tax_exclude_taxe_option' => Tax::excludeTaxeOption(),
            'ps_use_ecotax' => Configuration::get('PS_USE_ECOTAX'),
        ));

        $product->price = Tools::convertPrice($product->price, $this->context->currency, true, $this->context);
        if ($product->unit_price_ratio != 0) {
            $data->assign('unit_price', Tools::ps_round($product->price / $product->unit_price_ratio, 6));
        } else {
            $data->assign('unit_price', 0);
        }
        $data->assign('ps_tax', Configuration::get('PS_TAX'));

        $data->assign('country_display_tax_label', $this->context->country->display_tax_label);
        $data->assign(array(
            'currency', $this->context->currency,
            'product' => $product,
            'token' => $this->token
        ));

        // by webkul
        // For Advanced Payment
        if ($obj->id) {
            $WK_ALLOW_ADVANCED_PAYMENT = Configuration::get('WK_ALLOW_ADVANCED_PAYMENT');
            $data->assign('WK_ALLOW_ADVANCED_PAYMENT', $WK_ALLOW_ADVANCED_PAYMENT);

            if ($WK_ALLOW_ADVANCED_PAYMENT) {
                $obj_adv_pmt = new HotelAdvancedPayment();
                $adv_pay_dtl = $obj_adv_pmt->getIdAdvPaymentByIdProduct($product->id);
                if ($adv_pay_dtl) {
                    $data->assign('adv_pay_dtl', $adv_pay_dtl);
                }
            }
        }
        // For Feature Price Plans
        if ($obj->id) {
            $htlFeaturePrices = new HotelRoomTypeFeaturePricing();
            $productFeaturePrices = $htlFeaturePrices->getFeaturePricesbyIdProduct($product->id);
            $data->assign('productFeaturePrices', $productFeaturePrices);
        }

        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }

    public function initFormSeo($product)
    {
        if (!$this->default_form_language) {
            $this->getLanguages();
        }

        $data = $this->createTemplate($this->tpl_form);

        $context = Context::getContext();
        $rewritten_links = array();
        foreach ($this->_languages as $language) {
            $category = Category::getLinkRewrite((int)$product->id_category_default, (int)$language['id_lang']);
            $rewritten_links[(int)$language['id_lang']] = explode(
                '[REWRITE]',
                $context->link->getProductLink($product, '[REWRITE]', $category, null, (int)$language['id_lang'])
            );
        }

        $data->assign(array(
            'product' => $product,
            'languages' => $this->_languages,
            'id_lang' => $this->context->language->id,
            'ps_ssl_enabled' => Configuration::get('PS_SSL_ENABLED'),
            'curent_shop_url' => $this->context->shop->getBaseURL(),
            'default_form_language' => $this->default_form_language,
            'rewritten_links' => $rewritten_links
        ));

        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }

    protected function _getFinalPrice($specific_price, $product_price, $tax_rate)
    {
        return $this->object->getPrice(false, $specific_price['id_product_attribute'], 2);
    }

    protected function _displaySpecificPriceModificationForm($defaultCurrency, $shops, $currencies, $countries, $groups)
    {
        /** @var Product $obj */
        if (!($obj = $this->loadObject())) {
            return;
        }

        $page = (int)Tools::getValue('page');
        $content = '';
        $specific_prices = SpecificPrice::getByProductId((int)$obj->id);
        $specific_price_priorities = SpecificPrice::getPriority((int)$obj->id);

        $tmp = array();
        foreach ($shops as $shop) {
            $tmp[$shop['id_shop']] = $shop;
        }
        $shops = $tmp;
        $tmp = array();
        foreach ($currencies as $currency) {
            $tmp[$currency['id_currency']] = $currency;
        }
        $currencies = $tmp;

        $tmp = array();
        foreach ($countries as $country) {
            $tmp[$country['id_country']] = $country;
        }
        $countries = $tmp;

        $tmp = array();
        foreach ($groups as $group) {
            $tmp[$group['id_group']] = $group;
        }
        $groups = $tmp;

        $length_before = strlen($content);
        if (is_array($specific_prices) && count($specific_prices)) {
            $i = 0;
            foreach ($specific_prices as $specific_price) {
                $id_currency = $specific_price['id_currency'] ? $specific_price['id_currency'] : $defaultCurrency->id;
                if (!isset($currencies[$id_currency])) {
                    continue;
                }

                $current_specific_currency = $currencies[$id_currency];
                if ($specific_price['reduction_type'] == 'percentage') {
                    $impact = '- '.($specific_price['reduction'] * 100).' %';
                } elseif ($specific_price['reduction'] > 0) {
                    $impact = '- '.Tools::displayPrice(Tools::ps_round($specific_price['reduction'], 2), $current_specific_currency).' ';
                    if ($specific_price['reduction_tax']) {
                        $impact .= '('.$this->l('Tax incl.').')';
                    } else {
                        $impact .= '('.$this->l('Tax excl.').')';
                    }
                } else {
                    $impact = '--';
                }

                if ($specific_price['from'] == '0000-00-00 00:00:00' && $specific_price['to'] == '0000-00-00 00:00:00') {
                    $period = $this->l('Unlimited');
                } else {
                    $period = $this->l('From').' '.($specific_price['from'] != '0000-00-00 00:00:00' ? $specific_price['from'] : '0000-00-00 00:00:00').'<br />'.$this->l('To').' '.($specific_price['to'] != '0000-00-00 00:00:00' ? $specific_price['to'] : '0000-00-00 00:00:00');
                }
                if ($specific_price['id_product_attribute']) {
                    $combination = new Combination((int)$specific_price['id_product_attribute']);
                    $attributes = $combination->getAttributesName((int)$this->context->language->id);
                    $attributes_name = '';
                    foreach ($attributes as $attribute) {
                        $attributes_name .= $attribute['name'].' - ';
                    }
                    $attributes_name = rtrim($attributes_name, ' - ');
                } else {
                    $attributes_name = $this->l('All combinations');
                }

                $rule = new SpecificPriceRule((int)$specific_price['id_specific_price_rule']);
                $rule_name = ($rule->id ? $rule->name : '--');

                if ($specific_price['id_customer']) {
                    $customer = new Customer((int)$specific_price['id_customer']);
                    if (Validate::isLoadedObject($customer)) {
                        $customer_full_name = $customer->firstname.' '.$customer->lastname;
                    }
                    unset($customer);
                }

                if (!$specific_price['id_shop'] || in_array($specific_price['id_shop'], Shop::getContextListShopID())) {
                    $content .= '
					<tr '.($i % 2 ? 'class="alt_row"' : '').'>
						<td>'.$rule_name.'</td>';
						// <td>'.$attributes_name.'</td>';

                    $can_delete_specific_prices = true;
                    if (Shop::isFeatureActive()) {
                        $id_shop_sp = $specific_price['id_shop'];
                        $can_delete_specific_prices = (count($this->context->employee->getAssociatedShops()) > 1 && !$id_shop_sp) || $id_shop_sp;
                        $content .= '
						<td>'.($id_shop_sp ? $shops[$id_shop_sp]['name'] : $this->l('All shops')).'</td>';
                    }
                    $price = Tools::ps_round($specific_price['price'], 2);
                    $fixed_price = ($price == Tools::ps_round($obj->price, 2) || $specific_price['price'] == -1) ? '--' : Tools::displayPrice($price, $current_specific_currency);
                    $content .= '
						<td>'.($specific_price['id_currency'] ? $currencies[$specific_price['id_currency']]['name'] : $this->l('All currencies')).'</td>
						<td>'.($specific_price['id_country'] ? $countries[$specific_price['id_country']]['name'] : $this->l('All countries')).'</td>
						<td>'.($specific_price['id_group'] ? $groups[$specific_price['id_group']]['name'] : $this->l('All groups')).'</td>
						<td title="'.$this->l('ID:').' '.$specific_price['id_customer'].'">'.(isset($customer_full_name) ? $customer_full_name : $this->l('All customers')).'</td>
						<td>'.$fixed_price.'</td>
						<td>'.$impact.'</td>
						<td>'.$period.'</td>
						<td>'.((!$rule->id && $can_delete_specific_prices) ? '<a class="btn btn-default" name="delete_link" href="'.self::$currentIndex.'&id_product='.(int)Tools::getValue('id_product').'&action=deleteSpecificPrice&id_specific_price='.(int)($specific_price['id_specific_price']).'&token='.Tools::getValue('token').'"><i class="icon-trash"></i></a>': '').'</td>
					</tr>';
                    $i++;
                    unset($customer_full_name);
                }
            }
        }

        if ($length_before === strlen($content)) {
            $content .= '
				<tr>
					<td class="text-center" colspan="13"><i class="icon-warning-sign"></i>&nbsp;'.$this->l('No specific prices.').'</td>
				</tr>';
        }

        $content .= '
				</tbody>
			</table>
			</div>
			<div class="panel-footer">
				<a href="'.$this->context->link->getAdminLink('AdminProducts').($page > 1 ? '&submitFilter'.$this->table.'='.(int)$page : '').'" class="btn btn-default"><i class="process-icon-cancel"></i> '.$this->l('Cancel').'</a>
				<button id="product_form_submit_btn"  type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> '.$this->l('Save') .'</button>
				<button id="product_form_submit_btn"  type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> '.$this->l('Save and stay') .'</button>
			</div>
		</div>';

        $content .= '
		<script type="text/javascript">
			var currencies = new Array();
			currencies[0] = new Array();
			currencies[0]["sign"] = "'.$defaultCurrency->sign.'";
			currencies[0]["format"] = '.intval($defaultCurrency->format).';
			';
        foreach ($currencies as $currency) {
            $content .= '
				currencies['.$currency['id_currency'].'] = new Array();
				currencies['.$currency['id_currency'].']["sign"] = "'.$currency['sign'].'";
				currencies['.$currency['id_currency'].']["format"] = '.intval($currency['format']).';
				';
        }
        $content .= '
		</script>
		';

        // Not use id_customer
        if ($specific_price_priorities[0] == 'id_customer') {
            unset($specific_price_priorities[0]);
        }
        // Reindex array starting from 0
        $specific_price_priorities = array_values($specific_price_priorities);

        $content .= '<div class="panel">
		<h3>'.$this->l('Priority management').'</h3>
		<div class="alert alert-info">
				'.$this->l('Sometimes one customer can fit into multiple price rules. Priorities allow you to define which rule applies to the customer.').'
		</div>';

        $content .= '
		<div class="form-group">
			<label class="control-label col-lg-3" for="specificPricePriority1">'.$this->l('Priorities').'</label>
			<div class="input-group col-lg-9">
				<select id="specificPricePriority1" name="specificPricePriority[]">
					<option value="id_shop"'.($specific_price_priorities[0] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
					<option value="id_currency"'.($specific_price_priorities[0] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
					<option value="id_country"'.($specific_price_priorities[0] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
					<option value="id_group"'.($specific_price_priorities[0] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
				</select>
				<span class="input-group-addon"><i class="icon-chevron-right"></i></span>
				<select name="specificPricePriority[]">
					<option value="id_shop"'.($specific_price_priorities[1] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
					<option value="id_currency"'.($specific_price_priorities[1] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
					<option value="id_country"'.($specific_price_priorities[1] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
					<option value="id_group"'.($specific_price_priorities[1] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
				</select>
				<span class="input-group-addon"><i class="icon-chevron-right"></i></span>
				<select name="specificPricePriority[]">
					<option value="id_shop"'.($specific_price_priorities[2] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
					<option value="id_currency"'.($specific_price_priorities[2] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
					<option value="id_country"'.($specific_price_priorities[2] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
					<option value="id_group"'.($specific_price_priorities[2] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
				</select>
				<span class="input-group-addon"><i class="icon-chevron-right"></i></span>
				<select name="specificPricePriority[]">
					<option value="id_shop"'.($specific_price_priorities[3] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
					<option value="id_currency"'.($specific_price_priorities[3] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
					<option value="id_country"'.($specific_price_priorities[3] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
					<option value="id_group"'.($specific_price_priorities[3] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<div class="col-lg-9 col-lg-offset-3">
				<p class="checkbox">
					<label for="specificPricePriorityToAll"><input type="checkbox" name="specificPricePriorityToAll" id="specificPricePriorityToAll" />'.$this->l('Apply to all products').'</label>
				</p>
			</div>
		</div>
		<div class="panel-footer">
				<a href="'.$this->context->link->getAdminLink('AdminProducts').($page > 1 ? '&submitFilter'.$this->table.'='.(int)$page : '').'" class="btn btn-default"><i class="process-icon-cancel"></i> '.$this->l('Cancel').'</a>
				<button id="product_form_submit_btn"  type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> '.$this->l('Save') .'</button>
				<button id="product_form_submit_btn"  type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> '.$this->l('Save and stay') .'</button>
			</div>
		</div>
		';
        return $content;
    }

    protected function _getCustomizationFieldIds($labels, $alreadyGenerated, $obj)
    {
        $customizableFieldIds = array();
        if (isset($labels[Product::CUSTOMIZE_FILE])) {
            foreach ($labels[Product::CUSTOMIZE_FILE] as $id_customization_field => $label) {
                $customizableFieldIds[] = 'label_'.Product::CUSTOMIZE_FILE.'_'.(int)($id_customization_field);
            }
        }
        if (isset($labels[Product::CUSTOMIZE_TEXTFIELD])) {
            foreach ($labels[Product::CUSTOMIZE_TEXTFIELD] as $id_customization_field => $label) {
                $customizableFieldIds[] = 'label_'.Product::CUSTOMIZE_TEXTFIELD.'_'.(int)($id_customization_field);
            }
        }
        $j = 0;
        for ($i = $alreadyGenerated[Product::CUSTOMIZE_FILE]; $i < (int)($this->getFieldValue($obj, 'uploadable_files')); $i++) {
            $customizableFieldIds[] = 'newLabel_'.Product::CUSTOMIZE_FILE.'_'.$j++;
        }
        $j = 0;
        for ($i = $alreadyGenerated[Product::CUSTOMIZE_TEXTFIELD]; $i < (int)($this->getFieldValue($obj, 'text_fields')); $i++) {
            $customizableFieldIds[] = 'newLabel_'.Product::CUSTOMIZE_TEXTFIELD.'_'.$j++;
        }
        return implode('¤', $customizableFieldIds);
    }

    protected function _displayLabelField(&$label, $languages, $default_language, $type, $fieldIds, $id_customization_field)
    {
        foreach ($languages as $language) {
            $input_value[$language['id_lang']] = (isset($label[(int)($language['id_lang'])])) ? $label[(int)($language['id_lang'])]['name'] : '';
        }

        $required = (isset($label[(int)($language['id_lang'])])) ? $label[(int)($language['id_lang'])]['required'] : false;

        $template = $this->context->smarty->createTemplate('controllers/products/input_text_lang.tpl',
            $this->context->smarty);
        return '<div class="form-group">'
            .'<div class="col-lg-6">'
            .$template->assign(array(
                'languages' => $languages,
                'input_name'  => 'label_'.$type.'_'.(int)($id_customization_field),
                'input_value' => $input_value
            ))->fetch()
            .'</div>'
            .'<div class="col-lg-6">'
            .'<div class="checkbox">'
            .'<label for="require_'.$type.'_'.(int)($id_customization_field).'">'
            .'<input type="checkbox" name="require_'.$type.'_'.(int)($id_customization_field).'" id="require_'.$type.'_'.(int)($id_customization_field).'" value="1" '.($required ? 'checked="checked"' : '').'/>'
            .$this->l('Required')
            .'</label>'
            .'</div>'
            .'</div>'
            .'</div>';
    }

    protected function _displayLabelFields(&$obj, &$labels, $languages, $default_language, $type)
    {
        $content = '';
        $type = (int)($type);
        $labelGenerated = array(Product::CUSTOMIZE_FILE => (isset($labels[Product::CUSTOMIZE_FILE]) ? count($labels[Product::CUSTOMIZE_FILE]) : 0), Product::CUSTOMIZE_TEXTFIELD => (isset($labels[Product::CUSTOMIZE_TEXTFIELD]) ? count($labels[Product::CUSTOMIZE_TEXTFIELD]) : 0));

        $fieldIds = $this->_getCustomizationFieldIds($labels, $labelGenerated, $obj);
        if (isset($labels[$type])) {
            foreach ($labels[$type] as $id_customization_field => $label) {
                $content .= $this->_displayLabelField($label, $languages, $default_language, $type, $fieldIds, (int)($id_customization_field));
            }
        }
        return $content;
    }

    /**
     * @param Product $product
     * @throws Exception
     * @throws SmartyException
     */
    public function initFormInformations($product)
    {
        if (!$this->default_form_language) {
            $this->getLanguages();
        }

        $data = $this->createTemplate($this->tpl_form);

        $currency = $this->context->currency;

        $data->assign(array(
            'languages' => $this->_languages,
            'default_form_language' => $this->default_form_language,
            'currency' => $currency
        ));
        $this->object = $product;
        //$this->display = 'edit';
        $data->assign('product_name_redirected', Product::getProductName((int)$product->id_product_redirected, null, (int)$this->context->language->id));
        /*
        * Form for adding a virtual product like software, mp3, etc...
        */
        $product_download = new ProductDownload();
        if ($id_product_download = $product_download->getIdFromIdProduct($this->getFieldValue($product, 'id'))) {
            $product_download = new ProductDownload($id_product_download);
        }

        $product->{'productDownload'} = $product_download;

        $product_props = array();
        // global informations
        array_push($product_props, 'reference', 'ean13', 'upc',
        'available_for_order', 'show_price', 'online_only',
        'id_manufacturer'
        );

        // specific / detailled information
        array_push($product_props,
        // physical product
        'width', 'height', 'weight', 'active',
        // virtual product
        'is_virtual', 'cache_default_attribute',
        // customization
        'uploadable_files', 'text_fields'
        );
        // prices
        array_push($product_props,
            'price', 'wholesale_price', 'id_tax_rules_group', 'unit_price_ratio', 'on_sale',
            'unity', 'minimum_quantity', 'additional_shipping_cost',
            'available_now', 'available_later', 'available_date'
        );

        if (Configuration::get('PS_USE_ECOTAX')) {
            array_push($product_props, 'ecotax');
        }

        foreach ($product_props as $prop) {
            $product->$prop = $this->getFieldValue($product, $prop);
        }

        $product->name['class'] = 'updateCurrentText';
        if (!$product->id || Configuration::get('PS_FORCE_FRIENDLY_PRODUCT')) {
            $product->name['class'] .= ' copy2friendlyUrl';
        }

        $images = Image::getImages($this->context->language->id, $product->id);

        if (is_array($images)) {
            foreach ($images as $k => $image) {
                $images[$k]['src'] = $this->context->link->getImageLink($product->link_rewrite[$this->context->language->id], $product->id.'-'.$image['id_image'], ImageType::getFormatedName('small'));
            }
            $data->assign('images', $images);
        }
        $data->assign('imagesTypes', ImageType::getImagesTypes('products'));

        $product->tags = Tag::getProductTags($product->id);

        $data->assign('product_type', (int)Tools::getValue('type_product', $product->getType()));
        $data->assign('is_in_pack', (int)Pack::isPacked($product->id));

        $check_product_association_ajax = false;
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL) {
            $check_product_association_ajax = true;
        }

        // TinyMCE
        $iso_tiny_mce = $this->context->language->iso_code;
        $iso_tiny_mce = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso_tiny_mce.'.js') ? $iso_tiny_mce : 'en');
        $data->assign(array(
            'ad' => dirname($_SERVER['PHP_SELF']),
            'iso_tiny_mce' => $iso_tiny_mce,
            'check_product_association_ajax' => $check_product_association_ajax,
            'id_lang' => $this->context->language->id,
            'product' => $product,
            'token' => $this->token,
            'currency' => $currency,
            'link' => $this->context->link,
            'PS_PRODUCT_SHORT_DESC_LIMIT' => Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT') ? Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT') : 400,
            'category_position' => Tools::getValue('category_position', $product->getPositionInCategory()),
        ));
        $data->assign($this->tpl_form_vars);

        $objRoomType = new HotelRoomType();
        $objHotelInfo = new HotelBranchInformation();
        $data->assign('htl_info', $objHotelInfo->hotelsNameAndId());
        if ($hotelRoomType = $objRoomType->getRoomTypeInfoByIdProduct($product->id)) {
            $data->assign('htl_room_type', $hotelRoomType);
            $hotelFullInfo = $objHotelInfo->hotelBranchInfoById($hotelRoomType['id_hotel']);
            $data->assign('htl_full_info', $hotelFullInfo);
        }

        $this->tpl_form_vars['product'] = $product;
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }

    public function ajaxProcessaddProductImage()
    {
        self::$currentIndex = 'index.php?tab=AdminProducts';
        $product = new Product((int)Tools::getValue('id_product'));
        $legends = Tools::getValue('legend');

        if (!is_array($legends)) {
            $legends = (array)$legends;
        }

        if (!Validate::isLoadedObject($product)) {
            $files = array();
            $files[0]['error'] = Tools::displayError('Cannot add image because room type creation failed.');
        }

        $image_uploader = new HelperImageUploader('file');
        $image_uploader->setAcceptTypes(array('jpeg', 'gif', 'png', 'jpg'))->setMaxSize($this->max_image_size);
        $files = $image_uploader->process();

        foreach ($files as &$file) {
            $image = new Image();
            $image->id_product = (int)($product->id);
            $image->position = Image::getHighestPosition($product->id) + 1;

            foreach ($legends as $key => $legend) {
                if (!empty($legend)) {
                    $image->legend[(int)$key] = $legend;
                }
            }

            if (!Image::getCover($image->id_product)) {
                $image->cover = 1;
            } else {
                $image->cover = 0;
            }

            if (($validate = $image->validateFieldsLang(false, true)) !== true) {
                $file['error'] = Tools::displayError($validate);
            }

            if (isset($file['error']) && (!is_numeric($file['error']) || $file['error'] != 0)) {
                continue;
            }

            if (!$image->add()) {
                $file['error'] = Tools::displayError('Error while creating additional image');
            } else {
                if (!$new_path = $image->getPathForCreation()) {
                    $file['error'] = Tools::displayError('An error occurred during new folder creation');
                    continue;
                }

                $error = 0;

                if (!ImageManager::resize($file['save_path'], $new_path.'.'.$image->image_format, null, null, 'jpg', false, $error)) {
                    switch ($error) {
                        case ImageManager::ERROR_FILE_NOT_EXIST :
                            $file['error'] = Tools::displayError('An error occurred while copying image, the file does not exist anymore.');
                            break;

                        case ImageManager::ERROR_FILE_WIDTH :
                            $file['error'] = Tools::displayError('An error occurred while copying image, the file width is 0px.');
                            break;

                        case ImageManager::ERROR_MEMORY_LIMIT :
                            $file['error'] = Tools::displayError('An error occurred while copying image, check your memory limit.');
                            break;

                        default:
                            $file['error'] = Tools::displayError('An error occurred while copying image.');
                            break;
                    }
                    continue;
                } else {
                    $imagesTypes = ImageType::getImagesTypes('products');
                    $generate_hight_dpi_images = (bool)Configuration::get('PS_HIGHT_DPI');

                    foreach ($imagesTypes as $imageType) {
                        if (!ImageManager::resize($file['save_path'], $new_path.'-'.stripslashes($imageType['name']).'.'.$image->image_format, $imageType['width'], $imageType['height'], $image->image_format)) {
                            $file['error'] = Tools::displayError('An error occurred while copying image:').' '.stripslashes($imageType['name']);
                            continue;
                        }

                        if ($generate_hight_dpi_images) {
                            if (!ImageManager::resize($file['save_path'], $new_path.'-'.stripslashes($imageType['name']).'2x.'.$image->image_format, (int)$imageType['width']*2, (int)$imageType['height']*2, $image->image_format)) {
                                $file['error'] = Tools::displayError('An error occurred while copying image:').' '.stripslashes($imageType['name']);
                                continue;
                            }
                        }
                    }
                }

                unlink($file['save_path']);
                //Necesary to prevent hacking
                unset($file['save_path']);
                Hook::exec('actionWatermark', array('id_image' => $image->id, 'id_product' => $product->id));

                if (!$image->update()) {
                    $file['error'] = Tools::displayError('Error while updating status');
                    continue;
                }

                // Associate image to shop from context
                $shops = Shop::getContextListShopID();
                $image->associateTo($shops);
                $json_shops = array();

                foreach ($shops as $id_shop) {
                    $json_shops[$id_shop] = true;
                }

                $file['status']   = 'ok';
                $file['id']       = $image->id;
                $file['position'] = $image->position;
                $file['cover']    = $image->cover;
                $file['legend']   = $image->legend;
                $file['path']     = $image->getExistingImgPath();
                $file['shops']    = $json_shops;

                @unlink(_PS_TMP_IMG_DIR_.'product_'.(int)$product->id.'.jpg');
                @unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$product->id.'_'.$this->context->shop->id.'.jpg');
            }
        }

        die(json_encode(array($image_uploader->getName() => $files)));
    }

    /**
     * @param Product $obj
     * @throws Exception
     * @throws SmartyException
     */
    public function initFormImages($obj)
    {
        $data = $this->createTemplate($this->tpl_form);

        if ((bool)$obj->id) {
            if ($this->product_exists_in_shop) {
                $data->assign('product', $this->loadObject());

                $shops = false;
                if (Shop::isFeatureActive()) {
                    $shops = Shop::getShops();
                }

                if ($shops) {
                    foreach ($shops as $key => $shop) {
                        if (!$obj->isAssociatedToShop($shop['id_shop'])) {
                            unset($shops[$key]);
                        }
                    }
                }

                $data->assign('shops', $shops);

                $count_images = Db::getInstance()->getValue('
					SELECT COUNT(id_product)
					FROM '._DB_PREFIX_.'image
					WHERE id_product = '.(int)$obj->id
                );

                $images = Image::getImages($this->context->language->id, $obj->id);
                foreach ($images as $k => $image) {
                    $images[$k] = new Image($image['id_image']);
                }

                if ($this->context->shop->getContext() == Shop::CONTEXT_SHOP) {
                    $current_shop_id = (int)$this->context->shop->id;
                } else {
                    $current_shop_id = 0;
                }

                $languages = Language::getLanguages(true);
                $image_uploader = new HelperImageUploader('file');
                $image_uploader->setMultiple(!(Tools::getUserBrowser() == 'Apple Safari' && Tools::getUserPlatform() == 'Windows'))
                    ->setUseAjax(true)->setUrl(
                    Context::getContext()->link->getAdminLink('AdminProducts').'&ajax=1&id_product='.(int)$obj->id
                    .'&action=addProductImage');

                $data->assign(array(
                        'countImages' => $count_images,
                        'id_product' => (int)Tools::getValue('id_product'),
                        'id_category_default' => (int)$this->_category->id,
                        'images' => $images,
                        'iso_lang' => $languages[0]['iso_code'],
                        'token' =>  $this->token,
                        'table' => $this->table,
                        'max_image_size' => $this->max_image_size / 1024 / 1024,
                        'up_filename' => (string)Tools::getValue('virtual_product_filename_attribute'),
                        'currency' => $this->context->currency,
                        'current_shop_id' => $current_shop_id,
                        'languages' => $this->_languages,
                        'default_language' => (int)Configuration::get('PS_LANG_DEFAULT'),
                        'image_uploader' => $image_uploader->render()
                ));

                $type = ImageType::getByNameNType('%', 'products', 'height');
                if (isset($type['name'])) {
                    $data->assign('imageType', $type['name']);
                } else {
                    $data->assign('imageType', ImageType::getFormatedName('small'));
                }
            } else {
                $this->displayWarning($this->l('You must save the room type in this shop before adding images.'));
            }
        } else {
            $this->displayWarning($this->l('You must save this room type before adding images.'));
        }

        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }

    /**
     * @param Product $obj
     * @throws Exception
     * @throws SmartyException
     */
    public function initFormFeatures($obj)
    {
        if (!$this->default_form_language) {
            $this->getLanguages();
        }

        $data = $this->createTemplate($this->tpl_form);
        $data->assign('default_form_language', $this->default_form_language);
        $data->assign('languages', $this->_languages);

        if (!Feature::isFeatureActive()) {
            $this->displayWarning($this->l('This feature has been disabled. ').' <a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'#featuresDetachables">'.$this->l('Performances').'</a>');
        } else {
            if ($obj->id) {
                if ($this->product_exists_in_shop) {
                    $features = Feature::getFeatures($this->context->language->id, (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP));

                    foreach ($features as $k => $tab_features) {
                        $features[$k]['current_item'] = false;
                        $features[$k]['val'] = array();

                        $custom = true;
                        foreach ($obj->getFeatures() as $tab_products) {
                            if ($tab_products['id_feature'] == $tab_features['id_feature']) {
                                $features[$k]['current_item'] = $tab_products['id_feature_value'];
                            }
                        }

                        $features[$k]['featureValues'] = FeatureValue::getFeatureValuesWithLang($this->context->language->id, (int)$tab_features['id_feature']);
                        if (count($features[$k]['featureValues'])) {
                            foreach ($features[$k]['featureValues'] as $value) {
                                if ($features[$k]['current_item'] == $value['id_feature_value']) {
                                    $custom = false;
                                }
                            }
                        }

                        if ($custom) {
                            $feature_values_lang = FeatureValue::getFeatureValueLang($features[$k]['current_item']);
                            foreach ($feature_values_lang as $feature_value) {
                                $features[$k]['val'][$feature_value['id_lang']] = $feature_value;
                            }
                        }
                    }

                    $data->assign('available_features', $features);
                    $data->assign('product', $obj);
                    $data->assign('link', $this->context->link);
                    $data->assign('default_form_language', $this->default_form_language);
                } else {
                    $this->displayWarning($this->l('You must save the room type in this shop before adding features.'));
                }
            } else {
                $this->displayWarning($this->l('You must save this room type before adding features.'));
            }
        }
        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }

    public function getModalDuplicateOptions()
    {
        $idsHotel = HotelBranchInformation::getProfileAccessedHotels($this->context->employee->id_profile, 1, 1);
        $hotelsInfo = array();
        foreach ($idsHotel as $idHotel) {
            $objHotelBranchInfo = new HotelBranchInformation($idHotel, $this->context->language->id);
            if (Validate::isLoadedObject($objHotelBranchInfo)) {
                $hotelAddressInfo = HotelBranchInformation::getAddress($idHotel);
                $hotelInfo = array(
                    'id_hotel' => $objHotelBranchInfo->id,
                    'hotel_name' => $objHotelBranchInfo->hotel_name,
                    'rating' => $objHotelBranchInfo->rating,
                    'city' => $hotelAddressInfo['city'],
                );
                $hotelsInfo[] = $hotelInfo;
            }
        }

        $formAction = $this->context->link->getAdminLink('AdminProducts', true).'&duplicateproduct';
        $this->context->smarty->assign(array(
            'action' => $formAction,
            'hotels_info' => $hotelsInfo,
            'duplicate_images' => 1,
        ));

        $modalContent = $this->context->smarty->fetch('controllers/products/modal-duplicate-options.tpl');
        $modalActions = array(
            array(
                'type' => 'button',
                'value' => 'submitDuplicate',
                'class' => 'btn-primary submit-duplicate',
                'label' => $this->l('Submit'),
            ),
        );

        // set modal options
        $modal = array(
            'modal_id' => 'modal-duplicate-options',
            'modal_class' => 'modal-md',
            'modal_title' => $this->l('Duplication options'),
            'modal_content' => $modalContent,
            'modal_actions' => $modalActions,
        );

        return $modal;
    }

    public function ajaxProcessProductQuantity()
    {
        if ($this->tabAccess['edit'] === '0') {
            return die(json_encode(array('error' => $this->l('You do not have the right permission'))));
        }
        if (!Tools::getValue('actionQty')) {
            return json_encode(array('error' => $this->l('Undefined action')));
        }

        $product = new Product((int)Tools::getValue('id_product'), true);
        switch (Tools::getValue('actionQty')) {
            case 'depends_on_stock':
                if (Tools::getValue('value') === false) {
                    die(json_encode(array('error' =>  $this->l('Undefined value'))));
                }
                if ((int)Tools::getValue('value') != 0 && (int)Tools::getValue('value') != 1) {
                    die(json_encode(array('error' =>  $this->l('Incorrect value'))));
                }
                if (!$product->advanced_stock_management && (int)Tools::getValue('value') == 1) {
                    die(json_encode(array('error' =>  $this->l('Not possible if advanced stock management is disabled. '))));
                }
                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && (int)Tools::getValue('value') == 1 && (Pack::isPack($product->id) && !Pack::allUsesAdvancedStockManagement($product->id)
                    && ($product->pack_stock_type == 2 || $product->pack_stock_type == 1 ||
                        ($product->pack_stock_type == 3 && (Configuration::get('PS_PACK_STOCK_TYPE') == 1 || Configuration::get('PS_PACK_STOCK_TYPE') == 2))))) {
                    die(json_encode(array('error' => $this->l('You cannot use advanced stock management for this pack because').'<br />'.
                        $this->l('- advanced stock management is not enabled for these room types').'<br />'.
                        $this->l('- you have chosen to decrement room types quantities.'))));
                }

                StockAvailable::setProductDependsOnStock($product->id, (int)Tools::getValue('value'));
                break;

            case 'pack_stock_type':
                $value = Tools::getValue('value');
                if ($value === false) {
                    die(json_encode(array('error' =>  $this->l('Undefined value'))));
                }
                if ((int)$value != 0 && (int)$value != 1
                    && (int)$value != 2 && (int)$value != 3) {
                    die(json_encode(array('error' =>  $this->l('Incorrect value'))));
                }
                if ($product->depends_on_stock && !Pack::allUsesAdvancedStockManagement($product->id) && ((int)$value == 1
                    || (int)$value == 2 || ((int)$value == 3 && (Configuration::get('PS_PACK_STOCK_TYPE') == 1 || Configuration::get('PS_PACK_STOCK_TYPE') == 2)))) {
                    die(json_encode(array('error' => $this->l('You cannot use this stock management option because:').'<br />'.
                        $this->l('- advanced stock management is not enabled for these room types').'<br />'.
                        $this->l('- advanced stock management is enabled for the pack'))));
                }

                Product::setPackStockType($product->id, $value);
                break;

            case 'out_of_stock':
                if (Tools::getValue('value') === false) {
                    die(json_encode(array('error' =>  $this->l('Undefined value'))));
                }
                if (!in_array((int)Tools::getValue('value'), array(0, 1, 2))) {
                    die(json_encode(array('error' =>  $this->l('Incorrect value'))));
                }

                StockAvailable::setProductOutOfStock($product->id, (int)Tools::getValue('value'));
                break;

            case 'set_qty':
                if (Tools::getValue('value') === false || (!is_numeric(trim(Tools::getValue('value'))))) {
                    die(json_encode(array('error' =>  $this->l('Undefined value'))));
                }
                if (Tools::getValue('id_product_attribute') === false) {
                    die(json_encode(array('error' =>  $this->l('Undefined id room type attribute'))));
                }

                StockAvailable::setQuantity($product->id, (int)Tools::getValue('id_product_attribute'), (int)Tools::getValue('value'));
                Hook::exec('actionProductUpdate', array('id_product' => (int)$product->id, 'product' => $product));

                // Catch potential echo from modules
                $error = ob_get_contents();
                if (!empty($error)) {
                    ob_end_clean();
                    die(json_encode(array('error' => $error)));
                }
                break;
            case 'advanced_stock_management' :
                if (Tools::getValue('value') === false) {
                    die(json_encode(array('error' =>  $this->l('Undefined value'))));
                }
                if ((int)Tools::getValue('value') != 1 && (int)Tools::getValue('value') != 0) {
                    die(json_encode(array('error' =>  $this->l('Incorrect value'))));
                }
                if (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && (int)Tools::getValue('value') == 1) {
                    die(json_encode(array('error' =>  $this->l('Not possible if advanced stock management is disabled. '))));
                }

                $product->setAdvancedStockManagement((int)Tools::getValue('value'));
                if (StockAvailable::dependsOnStock($product->id) == 1 && (int)Tools::getValue('value') == 0) {
                    StockAvailable::setProductDependsOnStock($product->id, 0);
                }
                break;

        }
        die(json_encode(array('error' => false)));
    }

    public function getCombinationImagesJS()
    {
        /** @var Product $obj */
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $content = 'var combination_images = new Array();';
        if (!$allCombinationImages = $obj->getCombinationImages($this->context->language->id)) {
            return $content;
        }
        foreach ($allCombinationImages as $id_product_attribute => $combination_images) {
            $i = 0;
            $content .= 'combination_images['.(int)$id_product_attribute.'] = new Array();';
            foreach ($combination_images as $combination_image) {
                $content .= 'combination_images['.(int)$id_product_attribute.']['.$i++.'] = '.(int)$combination_image['id_image'].';';
            }
        }
        return $content;
    }

    /**
     * AdminProducts display hook
     *
     * @param $obj
     *
     * @throws PrestaShopException
     */
    public function initFormModules($obj)
    {
        $id_module = Db::getInstance()->getValue('SELECT `id_module` FROM `'._DB_PREFIX_.'module` WHERE `name` = \''.pSQL($this->tab_display_module).'\'');
        $this->tpl_form_vars['custom_form'] = Hook::exec('displayAdminProductsExtra', array(), (int)$id_module);
    }

    public function getL($key)
    {
        $trad = array(
            'Default category:' => $this->l('Default category'),
            'Catalog:' => $this->l('Catalog'),
            'Consider changing the default category.' => $this->l('Consider changing the default category.'),
            'ID' => $this->l('ID'),
            'Name' => $this->l('Name'),
            'Mark all checkbox(es) of categories in which room type is to appear' => $this->l('Mark the checkbox of each categories in which this room type will appear.')
        );
        return $trad[$key];
    }

    protected function _displayUnavailableProductWarning()
    {
        $content = '<div class="alert">
            <span>'.$this->l('Your room type will be saved as a draft.').'</span>
                <a href="#" class="btn btn-default pull-right" onclick="submitAddProductAndPreview()" ><i class="icon-external-link-sign"></i> '.$this->l('Save and preview').'</a>
                <input type="hidden" name="fakeSubmitAddProductAndPreview" id="fakeSubmitAddProductAndPreview" />
            </div>';
        $this->tpl_form_vars['warning_unavailable_product'] = $content;
    }

    public function ajaxProcessCheckProductName()
    {
        if ($this->tabAccess['view'] === '1') {
            $search = Tools::getValue('q');
            $id_lang = Tools::getValue('id_lang');
            $limit = Tools::getValue('limit');
            if (Context::getContext()->shop->getContext() != Shop::CONTEXT_SHOP) {
                $result = false;
            } else {
                $result = Db::getInstance()->executeS('
					SELECT DISTINCT pl.`name`, p.`id_product`, pl.`id_shop`
					FROM `'._DB_PREFIX_.'product` p
					LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON (ps.id_product = p.id_product AND ps.id_shop ='.(int)Context::getContext()->shop->id.')
					LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
						ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = '.(int)$id_lang.')
					WHERE pl.`name` LIKE "%'.pSQL($search).'%" AND ps.id_product IS NULL
					GROUP BY pl.`id_product`
					LIMIT '.(int)$limit);
            }
            die(json_encode($result));
        }
    }

    public function ajaxProcessUpdatePositions()
    {
        if ($this->tabAccess['edit'] === '1') {
            $way = (int)(Tools::getValue('way'));
            $id_product = (int)Tools::getValue('id_product');
            $id_category = (int)Tools::getValue('id_category_hotel');
            $positions = Tools::getValue('product');
            $page = (int)Tools::getValue('page');
            $selected_pagination = (int)Tools::getValue('selected_pagination');

            if (is_array($positions)) {
                foreach ($positions as $position => $value) {
                    $pos = explode('_', $value);

                    if ((isset($pos[1]) && isset($pos[2])) && ($pos[1] == $id_category && (int)$pos[2] === $id_product)) {
                        if ($page > 1) {
                            $position = $position + (($page - 1) * $selected_pagination);
                        }

                        if ($product = new Product((int)$pos[2])) {
                            if (isset($position) && $product->updatePosition($way, $position, $id_category)) {
                                $category = new Category((int)$id_category);
                                if (Validate::isLoadedObject($category)) {
                                    hook::Exec('categoryUpdate', array('category' => $category));
                                }
                                echo 'ok position '.(int)$position.' for product '.(int)$pos[2]."\r\n";
                            } else {
                                echo '{"hasError" : true, "errors" : "Can not update product '.(int)$id_product.' to position '.(int)$position.' "}';
                            }
                        } else {
                            echo '{"hasError" : true, "errors" : "This room type ('.(int)$id_product.') can t be loaded"}';
                        }

                        break;
                    }
                }
            }
        }
    }

    public function ajaxProcessPublishProduct()
    {
        if ($this->tabAccess['edit'] === '1') {
            if ($id_product = (int)Tools::getValue('id_product')) {
                $bo_product_url = dirname($_SERVER['PHP_SELF']).'/index.php?tab=AdminProducts&id_product='.$id_product.'&updateproduct&token='.$this->token;

                if (Tools::getValue('redirect')) {
                    die($bo_product_url);
                }

                $product = new Product((int)$id_product);
                if (!Validate::isLoadedObject($product)) {
                    die('error: invalid id');
                }

                $product->active = 1;

                if ($product->save()) {
                    die($bo_product_url);
                } else {
                    die('error: saving');
                }
            }
        }
    }

    public function ajaxProcessGetIdHotelByIdProduct()
    {
        $response = array('status' => 'failed');
        $idProduct = Tools::getValue('id_product');
        $objHotelRoomType = new HotelRoomType();
        $roomTypeInfo = $objHotelRoomType->getRoomTypeInfoByIdProduct($idProduct);
        if ($roomTypeInfo) {
            $response['status'] = 'success';
            $response['id_hotel'] = (int)$roomTypeInfo['id_hotel'];
        }
        die(json_encode($response));
    }

    public function ajaxProcessValidateDisableDates()
    {
        $response = array('status' => false);

        $idRoom = (int) Tools::getValue('id_room');
        $disableDates = Tools::getValue('disable_dates');

        $rowsToHighlight = array();
        $bookedRows = array();
        if (is_array($disableDates) && count($disableDates)) {
            foreach ($disableDates as $key => $dateRange) {
                if (!Validate::isDate($dateRange['date_from']) || !Validate::isDate($dateRange['date_to'])) {
                    $this->errors[] = $this->l('Some dates are missing. Please select all the date ranges.');
                    $rowsToHighlight[] = $key;
                }
            }

            if (!count($this->errors)) {
                foreach ($disableDates as $keyOuter => $dateRangeOuter) {
                    foreach ($disableDates as $keyInner => $dateRangeInner) {
                        if ($keyInner != $keyOuter) {
                            if ((($dateRangeOuter['date_from'] >= $dateRangeInner['date_from']) && ($dateRangeOuter['date_from'] < $dateRangeInner['date_to']))
                                || (($dateRangeInner['date_from'] >= $dateRangeOuter['date_from']) && ($dateRangeInner['date_from'] < $dateRangeOuter['date_to']))
                            ) {
                                $this->errors[] = $this->l('Some dates are conflicting with each other. Please check and reselect the date ranges.');
                                $rowsToHighlight[] = $keyOuter;
                                $rowsToHighlight[] = $keyInner;
                            }
                        }
                    }
                }
            }

            if (!count($this->errors)) {
                if ($idRoom) {
                    $objHotelBookingDetail = new HotelBookingDetail();
                    foreach ($disableDates as $key => $dateRange) {
                        if ($bookingRow = $objHotelBookingDetail->chechRoomBooked($idRoom, $dateRange['date_from'], $dateRange['date_to'])) {
                            $bookedRows[] = new HotelBookingDetail($bookingRow['id']);
                            $rowsToHighlight[] = $key;
                        }
                    }
                }
            }
        }

        if (count($bookedRows)) {
            $this->context->smarty->assign(array(
                'link' => $this->context->link,
                'booked_rows_list' => $bookedRows,
            ));

            $this->errors[] = $this->context->smarty->fetch('controllers/products/booked_room_date_ranges_list.tpl');
        }

        $this->errors = array_unique($this->errors);
        $rowsToHighlight = array_values(array_unique($rowsToHighlight));

        if (!count($this->errors)) {
            $response['status'] = true;
        } else {
            $this->context->smarty->assign(array(
                'errors' => $this->errors,
            ));

            $response['errors'] = $this->context->smarty->fetch('alerts.tpl');
            $response['rows_to_highlight'] = $rowsToHighlight;
            $response['status'] = false;
        }

        $this->ajaxDie(json_encode($response));
    }

    public function processImageLegends()
    {
        if (Tools::getValue('key_tab') == 'Images' && Tools::getValue('submitAddproductAndStay') == 'update_legends' && Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product')))) {
            $id_image = (int)Tools::getValue('id_caption');
            $language_ids = Language::getIDs(false);
            foreach ($_POST as $key => $val) {
                if (preg_match('/^legend_([0-9]+)/i', $key, $match)) {
                    foreach ($language_ids as $id_lang) {
                        if ($val && $id_lang == $match[1]) {
                            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'image_lang SET legend = "'.pSQL($val).'" WHERE '.($id_image ? 'id_image = '.(int)$id_image : 'EXISTS (SELECT 1 FROM '._DB_PREFIX_.'image WHERE '._DB_PREFIX_.'image.id_image = '._DB_PREFIX_.'image_lang.id_image AND id_product = '.(int)$product->id.')').' AND id_lang = '.(int)$id_lang);
                        }
                    }
                }
            }
        }
    }

    public function displayPreviewLink($token = null, $id, $name = null)
    {
        $tpl = $this->createTemplate('helpers/list/list_action_preview.tpl');
        if (!array_key_exists('Bad SQL query', self::$cache_lang)) {
            self::$cache_lang['Preview'] = $this->l('Preview', 'Helper');
        }

        $tpl->assign(array(
            'href' => $this->getPreviewUrl(new Product((int)$id)),
            'action' => self::$cache_lang['Preview'],
        ));

        return $tpl->fetch();
    }
}
