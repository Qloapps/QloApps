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

class CategoryControllerCore extends FrontController
{
    /** string Internal controller name */
    public $php_self = 'category';

    /** @var Category Current category object */
    protected $category;

    /** @var bool If set to false, customer cannot view the current category. */
    public $customer_access = true;

    /** @var int Number of products in the current page. */
    protected $nbProducts;

    /** @var array Products to be displayed in the current page . */
    protected $cat_products;

    /**
     * Sets default medias for this controller
     */
    public function setMedia()
    {
        parent::setMedia();

        if (!$this->useMobileTheme()) {
            //TODO : check why cluetip css is include without js file
            $this->addCSS(array(
                // _THEME_CSS_DIR_.'scenes.css'       => 'all',
                _THEME_CSS_DIR_.'category.css' => 'all',
                _THEME_CSS_DIR_.'product_list.css' => 'all',
            ));
        }

        $this->addCSS(_THEME_CSS_DIR_.'occupancy.css');
        $this->addJS(_THEME_JS_DIR_.'occupancy.js');

        $scenes = Scene::getScenes($this->category->id, $this->context->language->id, true, false);
        if ($scenes && count($scenes)) {
            $this->addJS(_THEME_JS_DIR_.'scenes.js');
            $this->addJqueryPlugin(array('scrollTo', 'serialScroll'));
        }

        $this->addJS(_THEME_JS_DIR_.'category.js');
    }

    /**
     * Redirects to canonical or "Not Found" URL
     *
     * @param string $canonical_url
     */
    public function canonicalRedirection($canonical_url = '')
    {
        if (Tools::getValue('live_edit')) {
            return;
        }
        if (!Tools::getValue('noredirect') && Validate::isLoadedObject($this->category)) {
            parent::canonicalRedirection($this->context->link->getCategoryLink($this->category));
        }
    }

    /**
     * Initializes controller
     *
     * @see FrontController::init()
     * @throws PrestaShopException
     */
    public function init()
    {
        // Get category ID
        $id_category = (int)Tools::getValue('id_category');
        if (!$id_category || !Validate::isUnsignedId($id_category)) {
            $this->errors[] = Tools::displayError('Missing category ID');
        }

        // validate dates if available
        $dateFrom = Tools::getValue('date_from');
        $dateTo = Tools::getValue('date_to');

        $currentTimestamp = strtotime(date('Y-m-d'));
        $dateFromTimestamp = strtotime($dateFrom);
        $dateToTimestamp = strtotime($dateTo);

        if ($dateFrom != '' && ($dateFromTimestamp === false || ($dateFromTimestamp < $currentTimestamp))) {
            Tools::redirect($this->context->link->getPageLink('pagenotfound'));
        }

        if ($dateTo != '' && ($dateToTimestamp === false || ($dateToTimestamp < $currentTimestamp))) {
            Tools::redirect($this->context->link->getPageLink('pagenotfound'));
        }

        // Instantiate category
        $this->category = new Category($id_category, $this->context->language->id);

        parent::init();

        // Check if the category is active and return 404 error if is disable.
        if (!$this->category->active || !Validate::isLoadedObject($this->category) || !$this->category->inShop() || !$this->category->isAssociatedToShop() || in_array($this->category->id, array(Configuration::get('PS_HOME_CATEGORY'), Configuration::get('PS_ROOT_CATEGORY')))) {
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
            Tools::redirect($this->context->link->getPageLink('pagenotfound'));
        } else
            // Check if category can be accessible by current customer and return 403 if not
            if (!$this->category->checkAccess($this->context->customer->id)) {
                header('HTTP/1.1 403 Forbidden');
                header('Status: 403 Forbidden');
                $this->errors[] = Tools::displayError('You do not have access to this category.');
                $this->customer_access = false;
            }
    }

    /**
     * Initializes page content variables
     */
    public function initContent()
    {
        parent::initContent();

        $this->setTemplate(_PS_THEME_DIR_.'category.tpl');

        if (!$this->customer_access) {
            return;
        }

        $id_category = Tools::getValue('id_category');

        if (!($date_from = Tools::getValue('date_from'))) {
            $date_from = date('Y-m-d');
            $date_to = date('Y-m-d', strtotime($date_from) + 86400);
        }
        if (!($date_to = Tools::getValue('date_to'))) {
            $date_to = date('Y-m-d', strtotime($date_from) + 86400);
        }

        // get occupancy of the search
        $occupancy = Tools::getValue('occupancy');
        if (!Validate::isOccupancy($occupancy)) {
            $occupancy = array();
        }
        $currency = new Currency($this->context->currency->id);

        if ($id_hotel = HotelBranchInformation::getHotelIdByIdCategory($id_category)) {
            $preparationTime = (int) HotelOrderRestrictDate::getPreparationTime($id_hotel);
            if ($preparationTime
                && strtotime('+ '.$preparationTime.' day') >= strtotime($date_from)
            ) {
                $date_from = date('Y-m-d', strtotime('+ '.$preparationTime.' day'));
                if (strtotime($date_from) >= strtotime($date_to)) {
                    $date_to = date('Y-m-d', strtotime($date_from) + 86400);
                }
            }

            $id_cart = $this->context->cart->id;
            $id_guest = $this->context->cookie->id_guest;

            $objBookingDetail = new HotelBookingDetail();
            $bookingParams = array(
                'date_from' => $date_from,
                'date_to' => $date_to,
                'occupancy' => $occupancy,
                'hotel_id' => $id_hotel,
                'get_total_rooms' => 0,
                'id_cart' => $id_cart,
                'id_guest' => $id_guest,
            );

            $booking_data = $objBookingDetail->dataForFrontSearch($bookingParams);

            $obj_booking_detail = new HotelBookingDetail();
            $num_days = $obj_booking_detail->getNumberOfDays($date_from, $date_to);

            $warning_num = Configuration::get('WK_ROOM_LEFT_WARNING_NUMBER');

            /*Max date of ordering for order restrict*/
            $order_date_restrict = false;
            $max_order_date = HotelOrderRestrictDate::getMaxOrderDate($id_hotel);
            if ($max_order_date) {
                $max_order_date = date('Y-m-d', strtotime($max_order_date));
                if (strtotime('-1 day', strtotime($max_order_date)) < strtotime($date_from)
                    || strtotime($max_order_date) < strtotime($date_to)
                ) {
                    $order_date_restrict = true;
                }
            }
            /*End*/

            $this->context->smarty->assign(array(
                'warning_num' => $warning_num,
                'num_days' => $num_days,
                'booking_date_from' => $date_from,
                'booking_date_to' => $date_to,
                'booking_data' => $booking_data,
                'max_order_date' => $max_order_date,
                'order_date_restrict' => $order_date_restrict
            ));
        } else {
            Tools::redirect($this->context->link->getPageLink('pagenotfound'));
        }

        $feat_img_dir = _PS_IMG_.'rf/';
        $ratting_img = _MODULE_DIR_.'hotelreservationsystem/views/img/Slices/icons-sprite.png';

        $this->context->smarty->assign(array(
            'id_hotel' => $id_hotel,
            'currency' => $currency,
            'feat_img_dir' => $feat_img_dir,
            'ratting_img' => $ratting_img,
        ));



        /*if (isset($this->context->cookie->id_compare))
            $this->context->smarty->assign('compareProducts', CompareProduct::getCompareProducts((int)$this->context->cookie->id_compare));

        // Product sort must be called before assignProductList()
        $this->productSort();

        $this->assignScenes();
        $this->assignSubcategories();
        $this->assignProductList();

        $this->context->smarty->assign(array(
            'category'             => $this->category,
            'description_short'    => Tools::truncateString($this->category->description, 350),
            'products'             => (isset($this->cat_products) && $this->cat_products) ? $this->cat_products : null,
            'id_category'          => (int)$this->category->id,
            'id_category_parent'   => (int)$this->category->id_parent,
            'return_category_name' => Tools::safeOutput($this->category->name),
            'path'                 => Tools::getPath($this->category->id),
            'add_prod_display'     => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
            'categorySize'         => Image::getSize(ImageType::getFormatedName('category')),
            'mediumSize'           => Image::getSize(ImageType::getFormatedName('medium')),
            'thumbSceneSize'       => Image::getSize(ImageType::getFormatedName('m_scene')),
            'homeSize'             => Image::getSize(ImageType::getFormatedName('home')),
            'allow_oosp'           => (int)Configuration::get('PS_ORDER_OUT_OF_STOCK'),
            'comparator_max_item'  => (int)Configuration::get('PS_COMPARATOR_MAX_ITEM'),
            'suppliers'            => Supplier::getSuppliers(),
            'body_classes'         => array($this->php_self.'-'.$this->category->id, $this->php_self.'-'.$this->category->link_rewrite)
        ));*/
    }

    public function displayAjaxFilterResults()
    {
        $response = array('status' => false);

        $this->display_header = false;
        $this->display_footer = false;

        $date_from = Tools::getValue('date_from');
        $date_to = Tools::getValue('date_to');
        $htl_id_category = Tools::getValue('id_category');

        // occupancy of the search
        $occupancy = Tools::getValue('occupancy');
        if (!Validate::isOccupancy($occupancy)) {
            $occupancy = array();
        }
        $sort_by = Tools::getValue('sort_by');
        $sort_value = Tools::getValue('sort_value');
        $filter_data = Tools::getValue('filter_data');

        $adults = 0;
        $children = 0;
        $amenities = 0;
        $price = 0;

        if (!empty($filter_data)) {
            foreach ($filter_data as $key => $value) {
                if ($key == 'adults') {
                    $adults = min($value);
                } elseif ($key == 'children') {
                    $children = min($value);
                } elseif ($key == 'amenities') {
                    $amenities = array();
                    foreach ($value as $a_k => $a_v) {
                        $amenities[] = $a_v;
                    }
                } elseif ($key == 'price') {
                    $price = array();
                    $price['from'] = $value[0];
                    $price['to'] = $value[1];
                }
            }
        }

        if (Module::isInstalled('hotelreservationsystem')) {
            require_once _PS_MODULE_DIR_.'hotelreservationsystem/define.php';

            $id_hotel = HotelBranchInformation::getHotelIdByIdCategory($htl_id_category);

            $objBookingDetail = new HotelBookingDetail();

            $bookingParams = array(
                'date_from' => $date_from,
                'date_to' => $date_to,
                'hotel_id' => $id_hotel,
                'occupancy' => $occupancy,
                'amenities' => $amenities,
                'price' => $price,
                'get_total_rooms' => 0,
                'id_cart' => $this->context->cart->id,
                'id_guest' => $this->context->cookie->id_guest,
            );

            $booking_data = $objBookingDetail->dataForFrontSearch($bookingParams);
            // reset array keys from 0
            $booking_data['rm_data'] = array_values($booking_data['rm_data']);
            if ($sort_by && $sort_value) {
                $indi_arr = array();

                if ($sort_value == 1) {
                    $direction = SORT_ASC;
                } elseif ($sort_value == 2) {
                    $direction = SORT_DESC;
                }
                foreach ($booking_data['rm_data'] as $s_k => $s_v) {
                    $indi_arr[$s_k] = $s_v['price'];
                }

                array_multisort($indi_arr, $direction, $booking_data['rm_data']);
            }

            $this->context->smarty->assign(array('booking_data' => $booking_data));
            $html = $this->context->smarty->fetch('_partials/room_type_list.tpl');
            $response['status'] = true;
            $response['html_room_type_list'] = $html;
        }
        $this->ajaxDie(json_encode($response));
    }

    /**
     * Assigns scenes template variables
     */
    protected function assignScenes()
    {
        // Scenes (could be externalised to another controller if you need them)
        $scenes = Scene::getScenes($this->category->id, $this->context->language->id, true, false);
        $this->context->smarty->assign('scenes', $scenes);

        // Scenes images formats
        if ($scenes && ($scene_image_types = ImageType::getImagesTypes('scenes'))) {
            foreach ($scene_image_types as $scene_image_type) {
                if ($scene_image_type['name'] == ImageType::getFormatedName('m_scene')) {
                    $thumb_scene_image_type = $scene_image_type;
                } elseif ($scene_image_type['name'] == ImageType::getFormatedName('scene')) {
                    $large_scene_image_type = $scene_image_type;
                }
            }

            $this->context->smarty->assign(array(
                'thumbSceneImageType' => isset($thumb_scene_image_type) ? $thumb_scene_image_type : null,
                'largeSceneImageType' => isset($large_scene_image_type) ? $large_scene_image_type : null,
            ));
        }
    }

    /**
     * Assigns subcategory templates variables
     */
    protected function assignSubcategories()
    {
        if ($sub_categories = $this->category->getSubCategories($this->context->language->id)) {
            $this->context->smarty->assign(array(
                'subcategories'          => $sub_categories,
                'subcategories_nb_total' => count($sub_categories),
                'subcategories_nb_half'  => ceil(count($sub_categories) / 2)
            ));
        }
    }

    /**
     * Assigns product list template variables
     */
    public function assignProductList()
    {
        $hook_executed = false;
        Hook::exec('actionProductListOverride', array(
            'nbProducts'   => &$this->nbProducts,
            'catProducts'  => &$this->cat_products,
            'hookExecuted' => &$hook_executed,
        ));

        // The hook was not executed, standard working
        if (!$hook_executed) {
            $this->context->smarty->assign('categoryNameComplement', '');
            $this->nbProducts = $this->category->getProducts(null, null, null, $this->orderBy, $this->orderWay, true);
            $this->pagination((int)$this->nbProducts); // Pagination must be call after "getProducts"
            $this->cat_products = $this->category->getProducts($this->context->language->id, (int)$this->p, (int)$this->n, $this->orderBy, $this->orderWay);
        }
        // Hook executed, use the override
        else {
            // Pagination must be call after "getProducts"
            $this->pagination($this->nbProducts);
        }

        $this->addColorsToProductList($this->cat_products);

        Hook::exec('actionProductListModifier', array(
            'nb_products'  => &$this->nbProducts,
            'cat_products' => &$this->cat_products,
        ));

        foreach ($this->cat_products as &$product) {
            if (isset($product['id_product_attribute']) && $product['id_product_attribute'] && isset($product['product_attribute_minimal_quantity'])) {
                $product['minimal_quantity'] = $product['product_attribute_minimal_quantity'];
            }
        }

        $this->context->smarty->assign('nb_products', $this->nbProducts);
    }

    /**
     * Returns an instance of the current category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}
