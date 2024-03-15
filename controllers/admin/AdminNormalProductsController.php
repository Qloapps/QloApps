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
class AdminNormalProductsControllerCore extends AdminController
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
        // $this->access_select = ' SELECT a.`id_product` FROM '._DB_PREFIX_.'product a';
        // $this->access_join = ' LEFT JOIN '._DB_PREFIX_.'htl_room_type hrt ON (hrt.id_product = a.id_product)';
        // if ($acsHtls = HotelBranchInformation::getProfileAccessedHotels($this->context->employee->id_profile, 1, 1)) {
        //     $this->access_where = ' WHERE (hrt.id_hotel IN ('.implode(',', $acsHtls).') OR hrt.id_hotel IS NULL)';
        // }

        parent::__construct();

        $this->imageType = 'jpg';
        $this->_defaultOrderBy = 'id_product';
        $this->max_file_size = (int)(Configuration::get('PS_LIMIT_UPLOAD_FILE_VALUE') * 1000000);
        $this->max_image_size = (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE');
        $this->allow_export = true;

        // @since 1.5 : translations for tabs
        $this->available_tabs_lang = array(
            'Informations' => $this->l('Information'),
            'Prices' => $this->l('Prices'),
            // 'Seo' => $this->l('SEO'),
            'Images' => $this->l('Images'),
            'Associations' => $this->l('Associations'),
        );

        if ($this->context->shop->getContext() != Shop::CONTEXT_GROUP) {
            $this->available_tabs = array_merge($this->available_tabs, array(
                'Informations' => 0,
                'Prices' => 1,
                // 'Seo' => 2,
                'Associations' => 3,
                'Images' => 4,
            ));
        }

        // Sort the tabs that need to be preloaded by their priority number
        asort($this->available_tabs, SORT_NUMERIC);

        /* Adding tab if modules are hooked */
        $modules_list = Hook::getHookModuleExecList('displayAdminNormalProductsExtra');
        if (is_array($modules_list) && count($modules_list) > 0) {
            foreach ($modules_list as $m) {
                // if module is setting name of the tab at the product edit page
                if (Validate::isLoadedObject($objModule = Module::getInstanceById($m['id_module']))) {
                    if (method_exists($objModule, 'moduleProductsExtraTabName')) {
                        $this->available_tabs_lang['Module'.ucfirst($m['module'])] = $objModule->moduleProductsExtraTabName();
                    }
                }
                // else set the display name of the product name as tab name
                if (!isset($this->available_tabs_lang['Module'.ucfirst($m['module'])])) {
                    $this->available_tabs_lang['Module'.ucfirst($m['module'])] = Module::getModuleName($m['module']);
                }
                $this->available_tabs['Module'.ucfirst($m['module'])] = 23;
            }
        }

        if (Tools::getValue('reset_filter_category')) {
            $this->context->cookie->id_category_products_filter = false;
        }
        if (Shop::isFeatureActive() && $this->context->cookie->id_category_products_filter) {
            $category = new Category((int)$this->context->cookie->id_category_products_filter);
            if (!$category->inShop()) {
                $this->context->cookie->id_category_products_filter = false;
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminNormalProducts'));
            }
        }
        /* Join categories table */
        if ($id_category = (int)Tools::getValue('productFilter_cl!name')) {
            $this->_category = new Category((int)$id_category);
            $_POST['productFilter_cl!name'] = $this->_category->name[$this->context->language->id];
        } else {
            if ($id_category = (int)Tools::getValue('id_category')) {
                $this->id_current_category = $id_category;
                $this->context->cookie->id_category_products_filter = $id_category;
            } elseif ($id_category = $this->context->cookie->id_category_products_filter) {
                $this->id_current_category = $id_category;
            }
            if ($this->id_current_category) {
                $this->_category = new Category((int)$this->id_current_category);
            } else {
                $this->_category = new Category();
            }
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
                LEFT JOIN `'._DB_PREFIX_.'htl_room_type_service_product` rsp ON (rsp.`id_product` = a.`id_product`)
				LEFT JOIN `'._DB_PREFIX_.'address` aa ON (aa.`id_hotel` = hb.`id`)';

        $this->_select .= ' IF(a.`auto_add_to_cart`, "'.$this->l('Yes').'", "'.$this->l('No').'") as auto_added, IF(a.`auto_add_to_cart`, 1, 0) as badge_success, (SELECT COUNT(hri.`id`) FROM `'._DB_PREFIX_.'htl_room_information` hri WHERE hri.`id_product` = a.`id_product`) as num_rooms, ';
        $this->_select .= ' COUNT(rsp.`id_product`) as products_associated, hrt.`adults`, hrt.`children`, hb.`id` as id_hotel, aa.`city`, hbl.`hotel_name`, ';
        $this->_select .= 'shop.`name` AS `shopname`, a.`id_shop_default`, ';
        $this->_select .= $alias_image.'.`id_image` AS `id_image`, cl.`name` AS `name_category`, '.$alias.'.`price`, 0 AS `price_final`, a.`is_virtual`, pd.`nb_downloadable`, sav.`quantity` AS `sav_quantity`, '.$alias.'.`active`, IF(sav.`quantity`<=0, 1, 0) AS `badge_danger`';

        if ($join_category) {
            $this->_join .= ' INNER JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_product` = a.`id_product` AND cp.`id_category` = '.(int)$this->_category->id.') ';
            $this->_select .= ' , cp.`position`, ';
        }

        // show the list of the product according to the booking or service products
        $this->_where .= ' AND a.`booking_product` = 0';

        $this->_use_found_rows = false;
        $this->_group = 'GROUP BY a.`id_product`';

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
        $this->fields_list['products_associated'] = array(
            'title' => $this->l('Associated Rooms types'),
            'class' => 'fixed-width-sm',
            'havingFilter' => true,
        );
        $this->fields_list['auto_added'] = array(
            'title' => $this->l('Auto Added'),
            'filter_key' => 'a!auto_add_to_cart',
            'align' => 'text-center',
            'type' => 'bool',
            'badge_success' => true
        );
        // $serviceProductType = array(
        //     Product::SERVICE_PRODUCT_WITH_ROOMTYPE => $this->l('Bought with room type'),
        //     Product::SERVICE_PRODUCT_WITHOUT_ROOMTYPE => $this->l('Bought without room type')
        // );
        // $this->fields_list['service_product_type'] = array(
        //     'type' => 'select',
        //     'list' => $serviceProductType,
        //     'title' => $this->l('Buying option'),
        //     'filter_key' => 'a!service_product_type',
        //     'callback' => 'getBuyingOption'
        // );
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->fields_list['shopname'] = array(
                'title' => $this->l('Default shop'),
                'filter_key' => 'shop!name',
            );
        }

        $this->fields_list['price'] = array(
            'title' => $this->l('Base price'),
            'type' => 'price',
            'align' => 'text-left',
            'filter_key' => 'a!price'
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
            'type' => 'bool',
            'class' => 'fixed-width-sm',
            'orderby' => false
        );

        if ($join_category && (int)$this->id_current_category) {
            $this->fields_list['position'] = array(
                'title' => $this->l('Position'),
                'filter_key' => 'cp!position',
                'align' => 'center',
                'position' => 'position'
            );
        }
    }

    public function getBuyingOption($service_product_type, $row)
    {
        if ($service_product_type == Product::SERVICE_PRODUCT_WITH_ROOMTYPE) {
            return $this->l('With room type');
        } else if ($service_product_type == Product::SERVICE_PRODUCT_WITHOUT_ROOMTYPE) {
            return $this->l('Without room type');
        }

        return '--';
    }

    public function getHotelName($hotelName, $row)
    {
        if ($hotelName && isset($row['city'])) {
            return $hotelName.' - '.$row['city'];
        }

        return '--';
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
        $category = Tools::getValue('category', Category::getRootCategory()->id);
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
            if ($product->add()
            && Category::duplicateProductCategories($id_product_old, $product->id)
            && ($combination_images = Product::duplicateAttributes($id_product_old, $product->id)) !== false
            && GroupReduction::duplicateReduction($id_product_old, $product->id)
            // && Product::duplicateFeatures($id_product_old, $product->id)
            && Product::duplicateTags($id_product_old, $product->id)) {
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

    public function processProductAttribute()
    {
        // Don't process if the combination fields have not been submitted
        if (!Combination::isFeatureActive() || !Tools::getValue('attribute_combination_list')) {
            return;
        }

        if (Validate::isLoadedObject($product = $this->object)) {
            if ($this->isProductFieldUpdated('attribute_price') && (!Tools::getIsset('attribute_price') || Tools::getIsset('attribute_price') == null)) {
                $this->errors[] = Tools::displayError('The price attribute is required.');
            }
            if (!Tools::getIsset('attribute_combination_list') || Tools::isEmpty(Tools::getValue('attribute_combination_list'))) {
                $this->errors[] = Tools::displayError('You must add at least one attribute.');
            }

            $array_checks = array(
                'reference' => 'isReference',
                'supplier_reference' => 'isReference',
                'location' => 'isReference',
                'ean13' => 'isEan13',
                'upc' => 'isUpc',
                'wholesale_price' => 'isPrice',
                'price' => 'isPrice',
                'ecotax' => 'isPrice',
                'quantity' => 'isInt',
                'weight' => 'isUnsignedFloat',
                'unit_price_impact' => 'isPrice',
                'default_on' => 'isBool',
                'minimal_quantity' => 'isUnsignedInt',
                'available_date' => 'isDateFormat'
            );
            foreach ($array_checks as $property => $check) {
                if (Tools::getValue('attribute_'.$property) !== false && !call_user_func(array('Validate', $check), Tools::getValue('attribute_'.$property))) {
                    $this->errors[] = sprintf(Tools::displayError('Field %s is not valid'), $property);
                }
            }

            if (!count($this->errors)) {
                if (!isset($_POST['attribute_wholesale_price'])) {
                    $_POST['attribute_wholesale_price'] = 0;
                }
                if (!isset($_POST['attribute_price_impact'])) {
                    $_POST['attribute_price_impact'] = 0;
                }
                if (!isset($_POST['attribute_weight_impact'])) {
                    $_POST['attribute_weight_impact'] = 0;
                }
                if (!isset($_POST['attribute_ecotax'])) {
                    $_POST['attribute_ecotax'] = 0;
                }
                if (Tools::getValue('attribute_default')) {
                    $product->deleteDefaultAttributes();
                }

                // Change existing one
                if (($id_product_attribute = (int)Tools::getValue('id_product_attribute')) || ($id_product_attribute = $product->productAttributeExists(Tools::getValue('attribute_combination_list'), false, null, true, true))) {
                    if ($this->tabAccess['edit'] === '1') {
                        if ($this->isProductFieldUpdated('available_date_attribute') && (Tools::getValue('available_date_attribute') != '' &&!Validate::isDateFormat(Tools::getValue('available_date_attribute')))) {
                            $this->errors[] = Tools::displayError('Invalid date format.');
                        } else {
                            $product->updateAttribute((int)$id_product_attribute,
                                $this->isProductFieldUpdated('attribute_wholesale_price') ? Tools::getValue('attribute_wholesale_price') : null,
                                $this->isProductFieldUpdated('attribute_price_impact') ? Tools::getValue('attribute_price') * Tools::getValue('attribute_price_impact') : null,
                                $this->isProductFieldUpdated('attribute_weight_impact') ? Tools::getValue('attribute_weight') * Tools::getValue('attribute_weight_impact') : null,
                                $this->isProductFieldUpdated('attribute_unit_impact') ? Tools::getValue('attribute_unity') * Tools::getValue('attribute_unit_impact') : null,
                                $this->isProductFieldUpdated('attribute_ecotax') ? Tools::getValue('attribute_ecotax') : null,
                                Tools::getValue('id_image_attr'),
                                Tools::getValue('attribute_reference'),
                                Tools::getValue('attribute_ean13'),
                                $this->isProductFieldUpdated('attribute_default') ? Tools::getValue('attribute_default') : null,
                                Tools::getValue('attribute_location'),
                                Tools::getValue('attribute_upc'),
                                $this->isProductFieldUpdated('attribute_minimal_quantity') ? Tools::getValue('attribute_minimal_quantity') : null,
                                $this->isProductFieldUpdated('available_date_attribute') ? Tools::getValue('available_date_attribute') : null, false);
                            StockAvailable::setProductDependsOnStock((int)$product->id, $product->depends_on_stock, null, (int)$id_product_attribute);
                            StockAvailable::setProductOutOfStock((int)$product->id, $product->out_of_stock, null, (int)$id_product_attribute);
                        }
                    } else {
                        $this->errors[] = Tools::displayError('You do not have permission to add this.');
                    }
                }
                // Add new
                else {
                    if ($this->tabAccess['add'] === '1') {
                        if ($product->productAttributeExists(Tools::getValue('attribute_combination_list'))) {
                            $this->errors[] = Tools::displayError('This combination already exists.');
                        } else {
                            $id_product_attribute = $product->addCombinationEntity(
                                Tools::getValue('attribute_wholesale_price'),
                                Tools::getValue('attribute_price') * Tools::getValue('attribute_price_impact'),
                                Tools::getValue('attribute_weight') * Tools::getValue('attribute_weight_impact'),
                                Tools::getValue('attribute_unity') * Tools::getValue('attribute_unit_impact'),
                                Tools::getValue('attribute_ecotax'),
                                0,
                                Tools::getValue('id_image_attr'),
                                Tools::getValue('attribute_reference'),
                                null,
                                Tools::getValue('attribute_ean13'),
                                Tools::getValue('attribute_default'),
                                Tools::getValue('attribute_location'),
                                Tools::getValue('attribute_upc'),
                                Tools::getValue('attribute_minimal_quantity'),
                                array(),
                                Tools::getValue('available_date_attribute')
                            );
                            StockAvailable::setProductDependsOnStock((int)$product->id, $product->depends_on_stock, null, (int)$id_product_attribute);
                            StockAvailable::setProductOutOfStock((int)$product->id, $product->out_of_stock, null, (int)$id_product_attribute);
                        }
                    } else {
                        $this->errors[] = Tools::displayError('You do not have permission to').'<hr>'.Tools::displayError('edit here.');
                    }
                }
                if (!count($this->errors)) {
                    $combination = new Combination((int)$id_product_attribute);
                    $combination->setAttributes(Tools::getValue('attribute_combination_list'));

                    // images could be deleted before
                    $id_images = Tools::getValue('id_image_attr');
                    if (!empty($id_images)) {
                        $combination->setImages($id_images);
                    }

                    $product->checkDefaultAttributes();
                    if (Tools::getValue('attribute_default')) {
                        Product::updateDefaultAttribute((int)$product->id);
                        if (isset($id_product_attribute)) {
                            $product->cache_default_attribute = (int)$id_product_attribute;
                        }

                        if ($available_date = Tools::getValue('available_date_attribute')) {
                            $product->setAvailableDate($available_date);
                        } else {
                            $product->setAvailableDate();
                        }
                    }
                }
            }
        }
    }

    // public function processFeatures()
    // {
    //     if (!Feature::isFeatureActive()) {
    //         return;
    //     }

    //     if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product')))) {
    //         // delete all objects
    //         $product->deleteFeatures();

    //         // add new objects
    //         $languages = Language::getLanguages(false);
    //         foreach ($_POST as $key => $val) {
    //             if (preg_match('/^feature_([0-9]+)_check/i', $key, $match)) {
    //                 if ($val) {
    //                     $product->addFeaturesToDB($match[1], $val);
    //                 } else {
    //                     if ($default_value = $this->checkFeatures($languages, $match[1])) {
    //                         $id_value = $product->addFeaturesToDB($match[1], 0, 1);
    //                         foreach ($languages as $language) {
    //                             if ($cust = Tools::getValue('custom_'.$match[1].'_'.(int)$language['id_lang'])) {
    //                                 $product->addFeaturesCustomToDB($id_value, (int)$language['id_lang'], $cust);
    //                             } else {
    //                                 $product->addFeaturesCustomToDB($id_value, (int)$language['id_lang'], $default_value);
    //                             }
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     } else {
    //         $this->errors[] = Tools::displayError('A room type must be created before adding features.');
    //     }
    // }

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
        $from_quantities = 1;
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
        $from_quantity = Tools::getValue('sp_from_quantity', 1);
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

    public function processProductCustomization()
    {
        if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product')))) {
            foreach ($_POST as $field => $value) {
                if (strncmp($field, 'label_', 6) == 0 && !Validate::isLabel($value)) {
                    $this->errors[] = Tools::displayError('The label fields defined are invalid.');
                }
            }
            if (empty($this->errors) && !$product->updateLabels()) {
                $this->errors[] = Tools::displayError('An error occurred while updating customization fields.');
            }
            if (empty($this->errors)) {
                $this->confirmations[] = $this->l('Update successful');
            }
        } else {
            $this->errors[] = Tools::displayError('A room type must be created before adding customization.');
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
            $this->redirect_after = self::$currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&action=Customization&conf=5'.(($id_category = (Tools::getIsset('id_category') ? (int)Tools::getValue('id_category') : '')) ? ('&id_category='.$id_category) : '').'&token='.Tools::getAdminTokenLite('AdminNormalProducts');
        }
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

        // Product preview
        elseif (Tools::isSubmit('submitAddProductAndPreview')) {
            $this->display = 'edit';
            $this->action = 'save';
            if (Tools::getValue('id_product')) {
                $this->id_object = Tools::getValue('id_product');
                $this->object = new Product((int)Tools::getValue('id_product'));
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
        // elseif (Tools::isSubmit('submitFeatures') || Tools::isSubmit('submitFeaturesAndStay')) {
        //     if ($this->tabAccess['edit'] === '1') {
        //         $this->action = 'features';
        //     } else {
        //         $this->errors[] = Tools::displayError('You do not have permission to edit this.');
        //     }
        // }
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

        if ($this->display == 'edit' || $this->display == 'add') {
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
                _PS_JS_DIR_.'admin/normal_products.js',
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

            $this->addCSS(array(
                _PS_JS_DIR_.'jquery/plugins/timepicker/jquery-ui-timepicker-addon.css',
                _PS_CSS_DIR_.'ps-hotel-reservation.css',
            ));
        }
    }

    public function ajaxProcessDeleteProductAttribute()
    {
        if (!Combination::isFeatureActive()) {
            return;
        }

        if ($this->tabAccess['delete'] === '1') {
            $id_product = (int)Tools::getValue('id_product');
            $id_product_attribute = (int)Tools::getValue('id_product_attribute');

            if ($id_product && Validate::isUnsignedId($id_product) && Validate::isLoadedObject($product = new Product($id_product))) {
                if (($depends_on_stock = StockAvailable::dependsOnStock($id_product)) && StockAvailable::getQuantityAvailableByProduct($id_product, $id_product_attribute)) {
                    $json = array(
                        'status' => 'error',
                        'message'=> $this->l('It is not possible to delete a combination while it still has some quantities in the Advanced Stock Management. You must delete its stock first.')
                    );
                } else {
                    $product->deleteAttributeCombination((int)$id_product_attribute);
                    $product->checkDefaultAttributes();
                    Tools::clearColorListCache((int)$product->id);
                    if (!$product->hasAttributes()) {
                        $product->cache_default_attribute = 0;
                        $product->update();
                    } else {
                        Product::updateDefaultAttribute($id_product);
                    }

                    if ($depends_on_stock && !Stock::deleteStockByIds($id_product, $id_product_attribute)) {
                        $json = array(
                            'status' => 'error',
                            'message'=> $this->l('Error while deleting the stock')
                        );
                    } else {
                        $json = array(
                            'status' => 'ok',
                            'message'=> $this->_conf[1],
                            'id_product_attribute' => (int)$id_product_attribute
                        );
                    }
                }
            } else {
                $json = array(
                    'status' => 'error',
                    'message'=> $this->l('You cannot delete this attribute.')
                );
            }
        } else {
            $json = array(
                'status' => 'error',
                'message'=> $this->l('You do not have permission to delete this.')
            );
        }

        die(json_encode($json));
    }

    public function ajaxProcessDefaultProductAttribute()
    {
        if ($this->tabAccess['edit'] === '1') {
            if (!Combination::isFeatureActive()) {
                return;
            }

            if (Validate::isLoadedObject($product = new Product((int)Tools::getValue('id_product')))) {
                $product->deleteDefaultAttributes();
                $product->setDefaultAttribute((int)Tools::getValue('id_product_attribute'));
                $json = array(
                    'status' => 'ok',
                    'message'=> $this->_conf[4]
                );
            } else {
                $json = array(
                    'status' => 'error',
                    'message'=> $this->l('You cannot make this the default attribute.')
                );
            }

            die(json_encode($json));
        }
    }

    public function ajaxProcessEditProductAttribute()
    {
        if ($this->tabAccess['edit'] === '1') {
            $id_product = (int)Tools::getValue('id_product');
            $id_product_attribute = (int)Tools::getValue('id_product_attribute');
            if ($id_product && Validate::isUnsignedId($id_product) && Validate::isLoadedObject($product = new Product((int)$id_product))) {
                $combinations = $product->getAttributeCombinationsById($id_product_attribute, $this->context->language->id);
                foreach ($combinations as $key => $combination) {
                    $combinations[$key]['attributes'][] = array($combination['group_name'], $combination['attribute_name'], $combination['id_attribute']);
                }

                die(json_encode($combinations));
            }
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
            $this->jsonError(Tools::displayError('An error occurred while attempting to delete the product image.'));
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
    // protected function checkFeatures($languages, $feature_id)
    // {
    //     $rules = call_user_func(array('FeatureValue', 'getValidationRules'), 'FeatureValue');
    //     $feature = Feature::getFeature((int)Configuration::get('PS_LANG_DEFAULT'), $feature_id);

    //     foreach ($languages as $language) {
    //         if ($val = Tools::getValue('custom_'.$feature_id.'_'.$language['id_lang'])) {
    //             $current_language = new Language($language['id_lang']);
    //             if (Tools::strlen($val) > $rules['sizeLang']['value']) {
    //                 $this->errors[] = sprintf(
    //                     Tools::displayError('The name for feature %1$s is too long in %2$s.'),
    //                     ' <b>'.$feature['name'].'</b>',
    //                     $current_language->name
    //                 );
    //             } elseif (!call_user_func(array('Validate', $rules['validateLang']['value']), $val)) {
    //                 $this->errors[] = sprintf(
    //                     Tools::displayError('A valid name required for feature. %1$s in %2$s.'),
    //                     ' <b>'.$feature['name'].'</b>',
    //                     $current_language->name
    //                 );
    //             }
    //             if (count($this->errors)) {
    //                 return 0;
    //             }
    //             // Getting default language
    //             if ($language['id_lang'] == Configuration::get('PS_LANG_DEFAULT')) {
    //                 return $val;
    //             }
    //         }
    //     }
    //     return 0;
    // }

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
        $this->object->service_product_type = Product::SERVICE_PRODUCT_WITH_ROOMTYPE;


        // set product visibility to none for current flow.
        $this->object->visibility = 'none';

        if ($this->object->add()) {
            PrestaShopLogger::addLog(sprintf($this->l('%s addition', 'AdminTab', false, false), $this->className), 1, null, $this->className, (int)$this->object->id, true, (int)$this->context->employee->id);
            // $this->addCarriers($this->object);
            // $this->updateAccessories($this->object);
            $this->updateLinkedHotelsAndRooms($this->object);
            // $this->updateDownloadProduct($this->object);

            if (Configuration::get('PS_FORCE_ASM_NEW_PRODUCT') && Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $this->object->getType() != Product::PTYPE_VIRTUAL) {
                $this->object->advanced_stock_management = 1;
                $this->object->save();
                $id_shops = Shop::getContextListShopID();
                foreach ($id_shops as $id_shop) {
                    StockAvailable::setProductDependsOnStock($this->object->id, true, (int)$id_shop, 0);
                }
            }

            StockAvailable::setQuantity($this->object->id, 0, 999999999);


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

                // set product visibility to none for current flow.
                $object->visibility = 'none';

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
                    // If the product doesn't exist in the current shop but exists in another shop
                    if (Shop::getContext() == Shop::CONTEXT_SHOP && !$existing_product->isAssociatedToShop($this->context->shop->id)) {
                        $out_of_stock = StockAvailable::outOfStock($existing_product->id, $existing_product->id_shop_default);
                        $depends_on_stock = StockAvailable::dependsOnStock($existing_product->id, $existing_product->id_shop_default);
                        StockAvailable::setProductOutOfStock((int)$this->object->id, $out_of_stock, $this->context->shop->id);
                        StockAvailable::setProductDependsOnStock((int)$this->object->id, $depends_on_stock, $this->context->shop->id);
                    }

                    PrestaShopLogger::addLog(sprintf($this->l('%s modification', 'AdminTab', false, false), $this->className), 1, null, $this->className, (int)$this->object->id, true, (int)$this->context->employee->id);
                    if (in_array($this->context->shop->getContext(), array(Shop::CONTEXT_SHOP, Shop::CONTEXT_ALL))) {
                        if ($this->isTabSubmitted('Associations')) {
                            $this->updateAccessories($object);
                        }
                        // if ($this->isTabSubmitted('Features')) {
                        //     $this->processFeatures();
                        // }
                        if ($this->isTabSubmitted('Prices')) {
                            // $this->processAdvancedPayment();
                            $this->processPriceAddition();
                            $this->processSpecificPricePriorities();
                        }
                        if ($this->isTabSubmitted('Images')) {
                            $this->processImageLegends();
                        }
                        if ($this->isTabSubmitted('Occupancy')) {
                            $this->processOccupancy();
                        }

                        $this->updateLinkedHotelsAndRooms($this->object);
                        // Disallow avanced stock management if the product become a pack
                        if ($product_type_before == Product::PTYPE_SIMPLE && $object->getType() == Product::PTYPE_PACK) {
                            StockAvailable::setProductDependsOnStock((int)$object->id, false);
                        }
                        StockAvailable::setQuantity($this->object->id, 0, 999999999);

                        $this->updateDownloadProduct($object, 1);
                        $this->updateTags(Language::getLanguages(false), $object);

                        if ($this->isProductFieldUpdated('category_box') && !$object->updateCategories(Tools::getValue('categoryBox'))) {
                            $this->errors[] = Tools::displayError('An error occurred while linking the object.').' <b>'.$this->table.'</b> '.Tools::displayError('To categories');
                        }
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


        // we have removed seo tab as it is currently not required, but in product object requires link_rewrite
        // so adding link_rewrite in $_POST
        if (Configuration::get('PS_FORCE_FRIENDLY_PRODUCT') || !Validate::isLoadedObject($this->object)) {
            foreach ($languages as $lang) {
                $_POST['link_rewrite_'.$lang['id_lang']] = Tools::link_rewrite(Tools::getValue('name_'.$lang['id_lang']));
            }
        } else {
            foreach ($languages as $lang) {
                $_POST['link_rewrite_'.$lang['id_lang']] = $this->object->link_rewrite[$lang['id_lang']];
            }
        }
        // end

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

        // $rootCategory = Category::getRootCategory();
        // $_POST['categoryBox'][] = $rootCategory->id_category;
        // if(!Tools::getValue('id_category_default')) {
        //     $_POST['id_category_default'] = $rootCategory->id_category;
        // }

        // Categories
        if ($this->isProductFieldUpdated('id_category_default') && (!Tools::isSubmit('categoryBox') || !count(Tools::getValue('categoryBox')))) {
            $this->errors[] = $this->l('This product must be in at least one category.');
        }

        if ($this->isProductFieldUpdated('id_category_default') && (!is_array(Tools::getValue('categoryBox')) || !in_array(Tools::getValue('id_category_default'), Tools::getValue('categoryBox')))) {
            $this->errors[] = $this->l('This product must be in the default category.');
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
     * Update product download
     *
     * @param Product $product
     * @param int     $edit
     *
     * @return bool
     */
    public function updateDownloadProduct($product, $edit = 0)
    {
        if ((int)Tools::getValue('is_virtual_file') == 1) {
            if (isset($_FILES['virtual_product_file_uploader']) && $_FILES['virtual_product_file_uploader']['size'] > 0) {
                $virtual_product_filename = ProductDownload::getNewFilename();
                $helper = new HelperUploader('virtual_product_file_uploader');
                $helper->setPostMaxSize(Tools::getOctets(ini_get('upload_max_filesize')))
                    ->setSavePath(_PS_DOWNLOAD_DIR_)->upload($_FILES['virtual_product_file_uploader'], $virtual_product_filename);
            } else {
                $virtual_product_filename = Tools::getValue('virtual_product_filename', ProductDownload::getNewFilename());
            }

            $product->setDefaultAttribute(0);//reset cache_default_attribute
            if (Tools::getValue('virtual_product_expiration_date') && !Validate::isDate(Tools::getValue('virtual_product_expiration_date'))) {
                if (!Tools::getValue('virtual_product_expiration_date')) {
                    $this->errors[] = Tools::displayError('The expiration-date attribute is required.');
                    return false;
                }
            }

            // Trick's
            if ($edit == 1) {
                $id_product_download = (int)ProductDownload::getIdFromIdProduct((int)$product->id, false);
                if (!$id_product_download) {
                    $id_product_download = (int)Tools::getValue('virtual_product_id');
                }
            } else {
                $id_product_download = Tools::getValue('virtual_product_id');
            }

            $is_shareable = Tools::getValue('virtual_product_is_shareable');
            $virtual_product_name = Tools::getValue('virtual_product_name');
            $virtual_product_nb_days = Tools::getValue('virtual_product_nb_days');
            $virtual_product_nb_downloable = Tools::getValue('virtual_product_nb_downloable');
            $virtual_product_expiration_date = Tools::getValue('virtual_product_expiration_date');

            $download = new ProductDownload((int)$id_product_download);
            $download->id_product = (int)$product->id;
            $download->display_filename = $virtual_product_name;
            $download->filename = $virtual_product_filename;
            $download->date_add = date('Y-m-d H:i:s');
            $download->date_expiration = $virtual_product_expiration_date ? $virtual_product_expiration_date.' 23:59:59' : '';
            $download->nb_days_accessible = (int)$virtual_product_nb_days;
            $download->nb_downloadable = (int)$virtual_product_nb_downloable;
            $download->active = 1;
            $download->is_shareable = (int)$is_shareable;
            if ($download->save()) {
                return true;
            }
        } else {
            /* unactive download product if checkbox not checked */
            if ($edit == 1) {
                $id_product_download = (int)ProductDownload::getIdFromIdProduct((int)$product->id);
                if (!$id_product_download) {
                    $id_product_download = (int)Tools::getValue('virtual_product_id');
                }
            } else {
                $id_product_download = ProductDownload::getIdFromIdProduct($product->id);
            }

            if (!empty($id_product_download)) {
                $product_download = new ProductDownload((int)$id_product_download);
                $product_download->date_expiration = date('Y-m-d H:i:s', time() - 1);
                $product_download->active = 0;

                return $product_download->save();
            }
        }

        return false;
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
                    if ($object->booking_product) {
                        $this->errors[] = $this->l('Service not found.');
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
                        'href' => $this->context->link->getAdminLink('AdminNormalProducts').'&id_product='.(int)Tools::getValue('id_product').'&action='.$product_tab,
                    );
                }
                $this->tpl_form_vars['product_tabs'] = $product_tabs;
            }
        } else {
            if ($id_category = (int)$this->id_current_category) {
                self::$currentIndex .= '&id_category='.(int)$this->id_current_category;
            }

            // If products from all categories are displayed, we don't want to use sorting by position
            if (!$id_category) {
                $this->_defaultOrderBy = $this->identifier;
                if ($this->context->cookie->{$this->table.'Orderby'} == 'position') {
                    unset($this->context->cookie->{$this->table.'Orderby'});
                    unset($this->context->cookie->{$this->table.'Orderway'});
                }
            }
            if (!$id_category) {
                $id_category = Configuration::get('PS_ROOT_CATEGORY');
            }
            $this->tpl_list_vars['is_category_filter'] = (bool)$this->id_current_category;

            // Generate category selection tree
            $catFilter = $this->l('Filter by category');
            $tree = new HelperTreeCategories('categories-tree', $catFilter);
            $tree->setAttribute('is_category_filter', (bool)$this->id_current_category)
                ->setAttribute('base_url', preg_replace('#&id_category=[0-9]*#', '', self::$currentIndex).'&token='.$this->token)
                ->setInputName('id-category')
                ->setRootCategory((int)Configuration::get('PS_SERVICE_CATEGORY'))
                ->setSelectedCategories(array((int)$id_category));
            $this->tpl_list_vars['category_tree'] = $tree->render();

            // used to build the new url when changing category
            $this->tpl_list_vars['base_url'] = preg_replace('#&id_category=[0-9]*#', '', self::$currentIndex).'&token='.$this->token;
        }
        // @todo module free
        $this->tpl_form_vars['vat_number'] = file_exists(_PS_MODULE_DIR_.'vatnumber/ajax.php');

        parent::initContent();
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('duplicate');
        $this->addRowAction('delete');
        return parent::renderList();
    }

    public function ajaxProcessProductManufacturers()
    {
        $manufacturers = Manufacturer::getManufacturers(false, 0, true, false, false, false, true);
        $jsonArray = array();

        if ($manufacturers) {
            foreach ($manufacturers as $manufacturer) {
                $tmp = array("optionValue" => $manufacturer['id_manufacturer'], "optionDisplay" => htmlspecialchars(trim($manufacturer['name'])));
                $jsonArray[] = json_encode($tmp);
            }
        }

        die('['.implode(',', $jsonArray).']');
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
                    $content .= AdminNormalProductsController::recurseCategoryForInclude($id_obj, $indexedCategories, $categories, $categories[$id_category][$key], $key, $id_category_default, $has_suite);
                }
            }
        }
        return $content;
    }

    protected function _displayDraftWarning($active)
    {
        $content = '<div class="warn draft" style="'.($active ? 'display:none' : '').'">
				<span>'.$this->l('Your product will be saved as a draft.').'</span>
				<a href="#" class="btn btn-default pull-right" onclick="submitAddProductAndPreview()" ><i class="icon-external-link-sign"></i> '.$this->l('Save').'</a>
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
                    'desc' => $this->l('Add new product', null, null, false),
                    'icon' => 'process-icon-new'
                );
        }
        if ($this->display == 'edit') {
            if (($product = $this->loadObject(true)) && $product->isAssociatedToShop()) {
                // adding button for preview this product
                // if (($url_preview = $this->getPreviewUrl($product))) {
                //     $this->page_header_toolbar_btn['preview'] = array(
                //         'short' => $this->l('Preview', null, null, false),
                //         'href' => $url_preview,
                //         'desc' => $this->l('Preview', null, null, false),
                //         'target' => true,
                //         'class' => 'previewUrl'
                //     );
                // }

                $js = (bool)Image::getImages($this->context->language->id, (int)$product->id) ?
                'confirm_link(\'\', \''.$this->l('This will copy the images too. If you wish to proceed, click "Yes". If not, click "No".', null, true, false).'\', \''.$this->l('Yes', null, true, false).'\', \''.$this->l('No', null, true, false).'\', \''.$this->context->link->getAdminLink('AdminNormalProducts', true).'&id_product='.(int)$product->id.'&duplicateproduct'.'\', \''.$this->context->link->getAdminLink('AdminNormalProducts', true).'&id_product='.(int)$product->id.'&duplicateproduct&noimage=1'.'\')'
                :
                'document.location = \''.$this->context->link->getAdminLink('AdminNormalProducts', true).'&id_product='.(int)$product->id.'&duplicateproduct&noimage=1'.'\'';

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
                // if (file_exists(_PS_MODULE_DIR_.'statsproduct/statsproduct.php')) {
                //     $this->page_header_toolbar_btn['stats'] = array(
                //     'short' => $this->l('Statistics', null, null, false),
                //     'href' => $this->context->link->getAdminLink('AdminStats').'&module=statsproduct&id_product='.(int)$product->id,
                //     'desc' => $this->l('Room Type Sales', null, null, false),
                // );
                // }

                // adding button for delete this product
                if ($this->tabAccess['delete']) {
                    $this->page_header_toolbar_btn['delete'] = array(
                        'short' => $this->l('Delete', null, null, false),
                        'href' => $this->context->link->getAdminLink('AdminNormalProducts').'&id_product='.(int)$product->id.'&deleteproduct',
                        'desc' => $this->l('Delete this product', null, null, false),
                        'confirm' => 1,
                        'js' => 'if (confirm(\''.$this->l('Delete product?', null, true, false).'\')){return true;}else{event.preventDefault();}'
                    );
                }
            }
        }
        parent::initPageHeaderToolbar();
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
        // $this->tpl_form_vars['with_room_type_txt'] = array(
        //     'autoadd_label'=> $this->l('Automatically apply to Room type'),
        // );
        // $this->tpl_form_vars['without_room_type_txt'] = array(
        //     'autoadd_label'=> $this->l('Automatically added to order'),
        // );

        // Media::addJsDef(array(
        //     'with_room_type_txt' => array(
        //         'autoadd_label'=> $this->l('Automatically apply to Room type'),
        //     ),
        //     'without_room_type_txt' => array(
        //         'autoadd_label'=> $this->l('Automatically added to order'),
        //     )
        // ));

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

        $this->tpl_form_vars['form_action'] = $this->context->link->getAdminLink('AdminNormalProducts').'&'.($id_product ? 'updateproduct&id_product='.(int)$id_product : 'addproduct').($page > 1 ? '&page='.(int)$page : '');
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

    public function updateLinkedHotelsAndRooms($product)
    {
        if (Validate::isLoadedObject($product)) {
            $objRoomTypeServiceProduct = new RoomTypeServiceProduct();
            $associatedRoomTypes = $objRoomTypeServiceProduct->getAssociatedHotelsAndRoomType($product->id)['room_types'];
            if (Product::SERVICE_PRODUCT_WITH_ROOMTYPE == $product->service_product_type) {
                $selectedRoomTypes = Tools::getValue('roomTypeBox');

                // Generate list of new associations
                $newRoomTypes = array();
                foreach ($selectedRoomTypes as $selectedRoomType) {
                    if (!in_array($selectedRoomType, $associatedRoomTypes)) {
                        $newRoomTypes[] = $selectedRoomType;
                    }
                }

                // Generate list of associations to remove
                $removedRoomTypes = array();
                foreach ($associatedRoomTypes as $associatedRoomType) {
                    if (!in_array($associatedRoomType, $selectedRoomTypes)) {
                        $removedRoomTypes[] = $associatedRoomType;
                    }
                }

                // Remove associations
                foreach ($removedRoomTypes as $removedRoomType) {
                    RoomTypeServiceProduct::deleteRoomProductLink(
                        $product->id,
                        RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE,
                        $removedRoomType
                    );
                }

                // Save new associations
                if ($newRoomTypes) {
                    $objRoomTypeServiceProduct->addRoomProductLink(
                        $product->id,
                        $newRoomTypes,
                        RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE
                    );
                }
            } else {
                // Remove associations
                foreach ($associatedRoomTypes as $associatedRoomType) {
                    RoomTypeServiceProduct::deleteRoomProductLink(
                        $product->id,
                        RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE,
                        $associatedRoomType
                    );
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

        // if ($obj->id) {
            $product = $obj;
            // Prepare Categories tree for display in Associations tab
            // $root = Category::getRootCategory();
            // $default_category = $this->context->cookie->id_category_products_filter ? $this->context->cookie->id_category_products_filter : Context::getContext()->shop->id_category;
            // default category for normal products
            $default_category = Configuration::get('PS_SERVICE_CATEGORY');
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
                ->setRootCategory((int)Configuration::get('PS_SERVICE_CATEGORY'))
                ->setUseCheckBox(true)
                ->setUseSearch(false)
                ->setFullTree(0)
                ->setSelectedCategories($categories)
                ->setUseBulkActions(false);

            $data->assign(array('default_category' => $default_category,
                        'selected_cat_ids' => implode(',', array_keys($selected_cat)),
                        'selected_cat' => $selected_cat,
                        'id_category_default' => $product->getDefaultCategory(),
                        'category_tree' => $tree->render(),
                        'product' => $product,
                        'link' => $this->context->link,
                        'is_shop_context' => Shop::getContext() == Shop::CONTEXT_SHOP
            ));
        // } else {
        //     $this->displayWarning($this->l('You must save this room type before updating associations.'));
        // }

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
            $this->displayWarning($this->l('You must save this product before adding specific pricing'));
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
            $address->zipcode = $address_infos['postcode'];
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
        // if ($obj->id) {
        //     $htlFeaturePrices = new HotelRoomTypeFeaturePricing();
        //     $productFeaturePrices = $htlFeaturePrices->getFeaturePricesbyIdProduct($product->id);
        //     $data->assign('productFeaturePrices', $productFeaturePrices);
        // }

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
				<a href="'.$this->context->link->getAdminLink('AdminNormalProducts').($page > 1 ? '&submitFilter'.$this->table.'='.(int)$page : '').'" class="btn btn-default"><i class="process-icon-cancel"></i> '.$this->l('Cancel').'</a>
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
				<a href="'.$this->context->link->getAdminLink('AdminNormalProducts').($page > 1 ? '&submitFilter'.$this->table.'='.(int)$page : '').'" class="btn btn-default"><i class="process-icon-cancel"></i> '.$this->l('Cancel').'</a>
				<button id="product_form_submit_btn"  type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> '.$this->l('Save') .'</button>
				<button id="product_form_submit_btn"  type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> '.$this->l('Save and stay') .'</button>
			</div>
		</div>
		';
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

        $objRoomTypeServiceProduct = new RoomTypeServiceProduct();
        $selectedElements = $objRoomTypeServiceProduct->getAssociatedHotelsAndRoomType($product->id);
        $tree = new HelperTreeHotels('associated-hotels-tree', 'Associated hotels');
        $tree->setTemplate('tree_associated_hotels.tpl')
            ->setHeaderTemplate('tree_associated_header.tpl')
            ->setRootCategory((int)Configuration::get('PS_LOCATIONS_CATEGORY'))
            ->setUseCheckBox(true)
            ->setFullTree(true)
            ->setRoomsOnly(false)
            ->setSelectedHotels($selectedElements['hotels'])
            ->setSelectedRoomTypes($selectedElements['room_types'])
            ->setUseBulkActions(false)
            ->setAccessedHotels(HotelBranchInformation::getProfileAccessedHotels($this->context->employee->id_profile, 1, 0));

        $data->assign('hotel_tree', $tree->render());

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
            'PS_PRODUCT_SHORT_DESC_LIMIT' => Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT') ? Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT') : 400
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

    protected function getCarrierList()
    {
        $carrier_list = Carrier::getCarriers($this->context->language->id, false, false, false, null, Carrier::ALL_CARRIERS);

        if ($product = $this->loadObject(true)) {
            /** @var Product $product */
            $carrier_selected_list = $product->getCarriers();
            foreach ($carrier_list as &$carrier) {
                foreach ($carrier_selected_list as $carrier_selected) {
                    if ($carrier_selected['id_reference'] == $carrier['id_reference']) {
                        $carrier['selected'] = true;
                        continue;
                    }
                }
            }
        }
        return $carrier_list;
    }

    protected function addCarriers($product = null)
    {
        if (!isset($product)) {
            $product = new Product((int)Tools::getValue('id_product'));
        }

        if (Validate::isLoadedObject($product)) {
            $carriers = array();

            if (Tools::getValue('selectedCarriers')) {
                $carriers = Tools::getValue('selectedCarriers');
            }

            $product->setCarriers($carriers);
        }
    }

    public function ajaxProcessaddProductImage()
    {
        self::$currentIndex = 'index.php?tab=AdminNormalProducts';
        $product = new Product((int)Tools::getValue('id_product'));
        $legends = Tools::getValue('legend');

        if (!is_array($legends)) {
            $legends = (array)$legends;
        }

        if (!Validate::isLoadedObject($product)) {
            $files = array();
            $files[0]['error'] = Tools::displayError('Cannot add image because product creation failed.');
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
                    Context::getContext()->link->getAdminLink('AdminNormalProducts').'&ajax=1&id_product='.(int)$obj->id
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
                $this->displayWarning($this->l('You must save the product in this shop before adding images.'));
            }
        } else {
            $this->displayWarning($this->l('You must save this product before adding images.'));
        }

        $this->tpl_form_vars['custom_form'] = $data->fetch();
    }

    /**
     * @param Product $obj
     * @throws Exception
     * @throws SmartyException
     */
    // public function initFormFeatures($obj)
    // {
    //     if (!$this->default_form_language) {
    //         $this->getLanguages();
    //     }

    //     $data = $this->createTemplate($this->tpl_form);
    //     $data->assign('default_form_language', $this->default_form_language);
    //     $data->assign('languages', $this->_languages);

    //     if (!Feature::isFeatureActive()) {
    //         $this->displayWarning($this->l('This feature has been disabled. ').' <a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'#featuresDetachables">'.$this->l('Performances').'</a>');
    //     } else {
    //         if ($obj->id) {
    //             if ($this->product_exists_in_shop) {
    //                 $features = Feature::getFeatures($this->context->language->id, (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP));

    //                 foreach ($features as $k => $tab_features) {
    //                     $features[$k]['current_item'] = false;
    //                     $features[$k]['val'] = array();

    //                     $custom = true;
    //                     foreach ($obj->getFeatures() as $tab_products) {
    //                         if ($tab_products['id_feature'] == $tab_features['id_feature']) {
    //                             $features[$k]['current_item'] = $tab_products['id_feature_value'];
    //                         }
    //                     }

    //                     $features[$k]['featureValues'] = FeatureValue::getFeatureValuesWithLang($this->context->language->id, (int)$tab_features['id_feature']);
    //                     if (count($features[$k]['featureValues'])) {
    //                         foreach ($features[$k]['featureValues'] as $value) {
    //                             if ($features[$k]['current_item'] == $value['id_feature_value']) {
    //                                 $custom = false;
    //                             }
    //                         }
    //                     }

    //                     if ($custom) {
    //                         $feature_values_lang = FeatureValue::getFeatureValueLang($features[$k]['current_item']);
    //                         foreach ($feature_values_lang as $feature_value) {
    //                             $features[$k]['val'][$feature_value['id_lang']] = $feature_value;
    //                         }
    //                     }
    //                 }

    //                 $data->assign('available_features', $features);
    //                 $data->assign('product', $obj);
    //                 $data->assign('link', $this->context->link);
    //                 $data->assign('default_form_language', $this->default_form_language);
    //             } else {
    //                 $this->displayWarning($this->l('You must save the room type in this shop before adding features.'));
    //             }
    //         } else {
    //             $this->displayWarning($this->l('You must save this room type before adding features.'));
    //         }
    //     }
    //     $this->tpl_form_vars['custom_form'] = $data->fetch();
    // }

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

    public function haveThisAccessory($accessory_id, $accessories)
    {
        foreach ($accessories as $accessory) {
            if ((int)$accessory['id_product'] == (int)$accessory_id) {
                return true;
            }
        }
        return false;
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
        $this->tpl_form_vars['custom_form'] = Hook::exec('displayAdminNormalProductsExtra', array(), (int)$id_module);
    }

    public function getL($key)
    {
        $trad = array(
            'Default category:' => $this->l('Default category'),
            'Catalog:' => $this->l('Catalog'),
            'Consider changing the default category.' => $this->l('Consider changing the default category.'),
            'ID' => $this->l('ID'),
            'Name' => $this->l('Name'),
            'Mark all checkbox(es) of categories in which product is to appear' => $this->l('Mark the checkbox of each categories in which this product will appear.')
        );
        return $trad[$key];
    }

    protected function _displayUnavailableProductWarning()
    {
        $content = '<div class="alert">
            <span>'.$this->l('Your product will be saved as a draft.').'</span>
                <a href="#" class="btn btn-default pull-right" onclick="submitAddProductAndPreview()" ><i class="icon-external-link-sign"></i> '.$this->l('Save').'</a>
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
            $id_category = (int)Tools::getValue('id_category');
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
                            if (isset($position) && $product->updatePosition($way, $position)) {
                                $category = new Category((int)$id_category);
                                if (Validate::isLoadedObject($category)) {
                                    hook::Exec('categoryUpdate', array('category' => $category));
                                }
                                echo 'ok position '.(int)$position.' for product '.(int)$pos[2]."\r\n";
                            } else {
                                echo '{"hasError" : true, "errors" : "Can not update product '.(int)$id_product.' to position '.(int)$position.' "}';
                            }
                        } else {
                            echo '{"hasError" : true, "errors" : "This product ('.(int)$id_product.') can t be loaded"}';
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
                $bo_product_url = dirname($_SERVER['PHP_SELF']).'/index.php?tab=AdminNormalProducts&id_product='.$id_product.'&updateproduct&token='.$this->token;

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
