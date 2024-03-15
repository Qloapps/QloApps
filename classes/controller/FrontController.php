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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class FrontControllerCore extends Controller
{
    /**
     * @deprecated Deprecated shortcuts as of 1.5.0.1 - Use $context->smarty instead
     * @var $smarty Smarty
     */
    protected static $smarty;

    /**
     * @deprecated Deprecated shortcuts as of 1.5.0.1 - Use $context->cookie instead
     * @var $cookie Cookie
     */
    protected static $cookie;

    /**
     * @deprecated Deprecated shortcuts as of 1.5.0.1 - Use $context->link instead
     * @var $link Link
     */
    protected static $link;

    /**
     * @deprecated Deprecated shortcuts as of 1.5.0.1 - Use $context->cart instead
     * @var $cart Cart
     */
    protected static $cart;

    /** @var array Controller errors */
    public $errors = array();

    /** @var string Language ISO code */
    public $iso;

    /** @var string ORDER BY field */
    public $orderBy;

    /** @var string Order way string ('ASC', 'DESC') */
    public $orderWay;

    /** @var int Current page number */
    public $p;

    /** @var int Items (products) per page */
    public $n;

    /** @var bool If set to true, will redirected user to login page during init function. */
    public $auth = false;

    /**
     * If set to true, user can be logged in as guest when checking if logged in.
     *
     * @see $auth
     * @var bool
     */
    public $guestAllowed = false;

    /**
     * Route of PrestaShop page to redirect to after forced login.
     *
     * @see $auth
     * @var bool
     */
    public $authRedirection = false;

    /** @var bool SSL connection flag */
    public $ssl = false;

    /** @var bool If true, switches display to restricted country page during init. */
    protected $restrictedCountry = false;

    /** @var bool If true, forces display to maintenance page. */
    protected $maintenance = false;

    /** @var bool If false, does not build left page column content and hides it. */
    public $display_column_left = true;

    /** @var bool If false, does not build right page column content and hides it. */
    public $display_column_right = true;

    /** @var bool If false, breadcump will not display. */
    public $show_breadcrump = false;                    // By webkul

    /**
     * True if controller has already been initialized.
     * Prevents initializing controller more than once.
     *
     * @var bool
     */
    public static $initialized = false;

    /**
     * @var array Holds current customer's groups.
     */
    protected static $currentCustomerGroups;

    /**
     * @var int
     */
    public $nb_items_per_page;

    /**
     * Controller constructor
     *
     * @global bool $useSSL SSL connection flag
     */
    public function __construct()
    {
        $this->controller_type = 'front';

        global $useSSL;

        parent::__construct();

        if (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) {
            $this->ssl = true;
        }

        if (isset($useSSL)) {
            $this->ssl = $useSSL;
        } else {
            $useSSL = $this->ssl;
        }

        if (isset($this->php_self) && is_object(Context::getContext()->theme)) {
            $columns = Context::getContext()->theme->hasColumns($this->php_self);

            // Don't use theme tables if not configured in DB
            if ($columns) {
                $this->display_column_left  = $columns['left_column'];
                $this->display_column_right = $columns['right_column'];
            }
        }
    }

    /**
     * Check if the controller is available for the current user/visitor
     *
     * @see Controller::checkAccess()
     * @return bool
     */
    public function checkAccess()
    {
        return true;
    }

    /**
     * Check if the current user/visitor has valid view permissions
     *
     * @see Controller::viewAccess
     * @return bool
     */
    public function viewAccess()
    {
        return true;
    }

    /**
     * Initializes front controller: sets smarty variables,
     * class properties, redirects depending on context, etc.
     *
     * @global bool     $useSSL           SSL connection flag
     * @global Cookie   $cookie           Visitor's cookie
     * @global Smarty   $smarty
     * @global Cart     $cart             Visitor's cart
     * @global string   $iso              Language ISO
     * @global Country  $defaultCountry   Visitor's country object
     * @global string   $protocol_link
     * @global string   $protocol_content
     * @global Link     $link
     * @global array    $css_files
     * @global array    $js_files
     * @global Currency $currency         Visitor's selected currency
     *
     * @throws PrestaShopException
     */
    public function init()
    {
        /**
         * Globals are DEPRECATED as of version 1.5.0.1
         * Use the Context object to access objects instead.
         * Example: $this->context->cart
         */
        global $useSSL, $cookie, $smarty, $cart, $iso, $defaultCountry, $protocol_link, $protocol_content, $link, $css_files, $js_files, $currency;

        if (self::$initialized) {
            return;
        }

        self::$initialized = true;

        parent::init();
        $this->context->smarty->assign([
            'css_dir' => _THEME_CSS_DIR_,
            'img_ps_dir' => _PS_IMG_,
            'tpl_dir' => _PS_THEME_DIR_,
            'js_dir' => _THEME_JS_DIR_,
            'base_dir' => __PS_BASE_URI__,
            'language_code'       => $this->context->language->language_code ? $this->context->language->language_code : $this->context->language->iso_code,
            'language_is_rtl' => $this->context->language->is_rtl,
        ]);

        /* get page name to display it in body id */

        // Are we in a payment module
        $module_name = '';
        if (Validate::isModuleName(Tools::getValue('module'))) {
            $module_name = Tools::getValue('module');
        }

        if (!empty($this->page_name)) {
            $page_name = $this->page_name;
        } elseif (!empty($this->php_self)) {
            $page_name = $this->php_self;
        } elseif (Tools::getValue('fc') == 'module' && $module_name != '' && (Module::getInstanceByName($module_name) instanceof PaymentModule)) {
            $page_name = 'module-payment-submit';
        }
        // @retrocompatibility Are we in a module ?
        elseif (preg_match('#^'.preg_quote($this->context->shop->physical_uri, '#').'modules/([a-zA-Z0-9_-]+?)/(.*)$#', $_SERVER['REQUEST_URI'], $m)) {
            $page_name = 'module-'.$m[1].'-'.str_replace(array('.php', '/'), array('', '-'), $m[2]);
        } else {
            $page_name = Dispatcher::getInstance()->getController();
            $page_name = (preg_match('/^[0-9]/', $page_name) ? 'page_'.$page_name : $page_name);
        }

        $this->context->smarty->assign(Meta::getMetaTags($this->context->language->id, $page_name));
        $this->context->smarty->assign('request_uri', Tools::safeOutput(urldecode($_SERVER['REQUEST_URI'])));

        Tools::setCookieLanguage($this->context->cookie);
        $this->displayMaintenancePage();
        // If current URL use SSL, set it true (used a lot for module redirect)
        if (Tools::usingSecureMode()) {
            $useSSL = true;
        }

        // For compatibility with globals, DEPRECATED as of version 1.5.0.1
        $css_files = $this->css_files;
        $js_files = $this->js_files;

        $this->sslRedirection();

        if ($this->ajax) {
            $this->display_header = false;
            $this->display_footer = false;
        }

        // If account created with the 2 steps register process, remove 'account_created' from cookie
        if (isset($this->context->cookie->account_created)) {
            $this->context->smarty->assign('account_created', 1);
            unset($this->context->cookie->account_created);
        }
        ob_start();

        // Init cookie language
        // @TODO This method must be moved into switchLanguage

        $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
        $useSSL = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
        $protocol_content = ($useSSL) ? 'https://' : 'http://';
        $link = new Link($protocol_link, $protocol_content);
        $this->context->link = $link;

        if ($id_cart = (int)$this->recoverCart()) {
            $this->context->cookie->id_cart = (int)$id_cart;
        }

        if ($this->auth && !$this->context->customer->isLogged($this->guestAllowed)) {
            Tools::redirect('index.php?controller=authentication'.($this->authRedirection ? '&back='.$this->authRedirection : ''));
        }

        /* Theme is missing */
        if (!is_dir(_PS_THEME_DIR_)) {
            throw new PrestaShopException((sprintf(Tools::displayError('Current theme unavailable "%s". Please check your theme directory name and permissions.'), basename(rtrim(_PS_THEME_DIR_, '/\\')))));
        }

        if (Configuration::get('PS_GEOLOCATION_ENABLED')) {
            if (($new_default = $this->geolocationManagement($this->context->country)) && Validate::isLoadedObject($new_default)) {
                $this->context->country = $new_default;
            }
        } elseif (Configuration::get('PS_DETECT_COUNTRY')) {
            $has_currency = isset($this->context->cookie->id_currency) && (int)$this->context->cookie->id_currency;
            $has_country = isset($this->context->cookie->iso_code_country) && $this->context->cookie->iso_code_country;
            $has_address_type = false;

            if ((int)$this->context->cookie->id_cart && ($cart = new Cart($this->context->cookie->id_cart)) && Validate::isLoadedObject($cart)) {
                $has_address_type = isset($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) && $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
            }

            if ((!$has_currency || $has_country) && !$has_address_type) {
                $id_country = $has_country && !Validate::isLanguageIsoCode($this->context->cookie->iso_code_country) ?
                    (int)Country::getByIso(strtoupper($this->context->cookie->iso_code_country)) : (int)Tools::getCountry();

                $country = new Country($id_country, (int)$this->context->cookie->id_lang);

                if (!$has_currency && validate::isLoadedObject($country) && $this->context->country->id !== $country->id) {
                    $this->context->country = $country;
                    $this->context->cookie->id_currency = (int)Currency::getCurrencyInstance($country->id_currency ? (int)$country->id_currency : (int)Configuration::get('PS_CURRENCY_DEFAULT'))->id;
                    $this->context->cookie->iso_code_country = strtoupper($country->iso_code);
                }
            }
        }

        $currency = Tools::setCurrency($this->context->cookie);

        if (isset($_GET['logout']) || ($this->context->customer->logged && Customer::isBanned($this->context->customer->id))) {
            $this->context->customer->logout();

            Tools::redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);
        } elseif (isset($_GET['mylogout'])) {
            $this->context->customer->mylogout();
            Tools::redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);
        }

        /* Cart already exists */
        if ((int)$this->context->cookie->id_cart) {
            if (!isset($cart)) {
                $cart = new Cart($this->context->cookie->id_cart);
            }

            if (Validate::isLoadedObject($cart) && $cart->OrderExists()) {
                PrestaShopLogger::addLog('Frontcontroller::init - Cart cannot be loaded or a booking has already been created using this cart', 1, null, 'Cart', (int)$this->context->cookie->id_cart, true);
                unset($this->context->cookie->id_cart, $cart, $this->context->cookie->checkedTOS);
                $this->context->cookie->check_cgv = false;
            }
            /* Delete product of cart, if user can't make an order from his country */
            elseif (intval(Configuration::get('PS_GEOLOCATION_ENABLED')) &&
                    !in_array(strtoupper($this->context->cookie->iso_code_country), explode(';', Configuration::get('PS_ALLOWED_COUNTRIES'))) &&
                    $cart->nbProducts() && intval(Configuration::get('PS_GEOLOCATION_NA_BEHAVIOR')) != -1 &&
                    !FrontController::isInWhitelistForGeolocation() &&
                    !in_array($_SERVER['SERVER_NAME'], array('localhost', '127.0.0.1'))) {
                PrestaShopLogger::addLog('Frontcontroller::init - GEOLOCATION is deleting a cart', 1, null, 'Cart', (int)$this->context->cookie->id_cart, true);
                unset($this->context->cookie->id_cart, $cart);
            }
            // update cart values
            elseif ($this->context->cookie->id_customer != $cart->id_customer || $this->context->cookie->id_lang != $cart->id_lang || $currency->id != $cart->id_currency) {
                if ($this->context->cookie->id_customer) {
                    $cart->id_customer = (int)$this->context->cookie->id_customer;
                }
                $cart->id_lang = (int)$this->context->cookie->id_lang;
                $cart->id_currency = (int)$currency->id;
                $cart->update();
            }
            /* Select an address if not set */
            if (isset($cart) && (!isset($cart->id_address_delivery) || $cart->id_address_delivery == 0 ||
                !isset($cart->id_address_invoice) || $cart->id_address_invoice == 0) && $this->context->cookie->id_customer) {
                $to_update = false;
                if (!isset($cart->id_address_delivery) || $cart->id_address_delivery == 0) {
                    $to_update = true;
                    $cart->id_address_delivery = (int)Address::getFirstCustomerAddressId($cart->id_customer);
                }
                if (!isset($cart->id_address_invoice) || $cart->id_address_invoice == 0) {
                    $to_update = true;
                    $cart->id_address_invoice = (int)Address::getFirstCustomerAddressId($cart->id_customer);
                }
                if ($to_update) {
                    $cart->update();
                }
            }
        }

        if (!isset($cart) || !$cart->id) {
            $cart = new Cart();
            $cart->id_lang = (int)$this->context->cookie->id_lang;
            $cart->id_currency = (int)$this->context->cookie->id_currency;
            $cart->id_guest = (int)$this->context->cookie->id_guest;
            $cart->id_shop_group = (int)$this->context->shop->id_shop_group;
            $cart->id_shop = $this->context->shop->id;
            if ($this->context->cookie->id_customer) {
                $cart->id_customer = (int)$this->context->cookie->id_customer;
                $cart->id_address_delivery = (int)Address::getFirstCustomerAddressId($cart->id_customer);
                $cart->id_address_invoice = (int)$cart->id_address_delivery;
            } else {
                $cart->id_address_delivery = 0;
                $cart->id_address_invoice = 0;
            }

            // Needed if the merchant want to give a free product to every visitors
            $this->context->cart = $cart;
            CartRule::autoAddToCart($this->context);
        } else {
            $this->context->cart = $cart;
        }

        /* Breadcrumb */
        $navigation_pipe = (Configuration::get('PS_NAVIGATION_PIPE') ? Configuration::get('PS_NAVIGATION_PIPE') : '>');
        $this->context->smarty->assign('navigationPipe', $navigation_pipe);

        // Automatically redirect to the canonical URL if needed
        if (!empty($this->php_self) && !Tools::getValue('ajax')) {
            $this->canonicalRedirection($this->context->link->getPageLink($this->php_self, $this->ssl, $this->context->language->id));
        }
        Product::initPricesComputation();

        $display_tax_label = $this->context->country->display_tax_label;
        if (isset($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) && $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) {
            $infos = Address::getCountryAndState((int)$cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
            $country = new Country((int)$infos['id_country']);
            $this->context->country = $country;
            if (Validate::isLoadedObject($country)) {
                $display_tax_label = $country->display_tax_label;
            }
        }

        $languages = Language::getLanguages(true, $this->context->shop->id);
        $meta_language = array();
        foreach ($languages as $lang) {
            $meta_language[] = $lang['iso_code'];
        }

        $compared_products = array();
        if (Configuration::get('PS_COMPARATOR_MAX_ITEM') && isset($this->context->cookie->id_compare)) {
            $compared_products = CompareProduct::getCompareProducts($this->context->cookie->id_compare);
        }

        $isOccupancyWiseSearch = false;
        if (Configuration::get('PS_FRONT_SEARCH_TYPE') == HotelBookingDetail::SEARCH_TYPE_OWS) {
            $isOccupancyWiseSearch = true;
        }

        $occupancyRequiredForBooking = false;
        if (Configuration::get('PS_FRONT_ROOM_UNIT_SELECTION_TYPE') == HotelBookingDetail::PS_ROOM_UNIT_SELECTION_TYPE_OCCUPANCY) {
            $occupancyRequiredForBooking = true;
        }
        $this->context->smarty->assign(array(
            // Useful for layout.tpl
            'mobile_device'       => $this->context->getMobileDevice(),
            'link'                => $link,
            'cart'                => $cart,
            'currency'            => $currency,
            'currencyRate'        => (float)$currency->getConversationRate(),
            'cookie'              => $this->context->cookie,
            'page_name'           => $page_name,
            'hide_left_column'    => !$this->display_column_left,
            'hide_right_column'   => !$this->display_column_right,
            'base_dir'            => _PS_BASE_URL_.__PS_BASE_URI__,
            'base_dir_ssl'        => $protocol_link.Tools::getShopDomainSsl().__PS_BASE_URI__,
            'force_ssl'           => Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'),
            'content_dir'         => $protocol_content.Tools::getHttpHost().__PS_BASE_URI__,
            'base_uri'            => $protocol_content.Tools::getHttpHost().__PS_BASE_URI__.(!Configuration::get('PS_REWRITING_SETTINGS') ? 'index.php' : ''),
            'tpl_uri'             => _THEME_DIR_,
            'modules_dir'         => _MODULE_DIR_,
            'mail_dir'            => _MAIL_DIR_,
            'lang_iso'            => $this->context->language->iso_code,
            'lang_id'             => (int)$this->context->language->id,
            'come_from'           => Tools::getHttpHost(true, true).Tools::htmlentitiesUTF8(str_replace(array('\'', '\\'), '', urldecode($_SERVER['REQUEST_URI']))),
            'cart_qties'          => (int)$cart->nbProducts(),
            'currencies'          => Currency::getCurrencies(),
            'languages'           => $languages,
            'meta_language'       => implode(',', $meta_language),
            'priceDisplay'        => Product::getTaxCalculationMethod((int)$this->context->cookie->id_customer),
            'is_logged'           => (bool)$this->context->customer->isLogged(),
            'is_guest'            => (bool)$this->context->customer->isGuest(),
            'add_prod_display'    => (int)Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
            'shop_name'           => Configuration::get('PS_SHOP_NAME'),
            'roundMode'           => (int)Configuration::get('PS_PRICE_ROUND_MODE'),
            'use_taxes'           => (int)Configuration::get('PS_TAX'),
            'show_taxes'          => (int)(Configuration::get('PS_TAX_DISPLAY') == 1 && (int)Configuration::get('PS_TAX')),
            'display_tax_label'   => (bool)$display_tax_label,
            'opc'                 => (bool)Configuration::get('PS_ORDER_PROCESS_TYPE'),
            'PS_CATALOG_MODE'     => (bool)Configuration::get('PS_CATALOG_MODE') || (Group::isFeatureActive() && !(bool)Group::getCurrent()->show_prices),
            'b2b_enable'          => (bool)Configuration::get('PS_B2B_ENABLE'),
            'request'             => $link->getPaginationLink(false, false, false, true),
            'PS_STOCK_MANAGEMENT' => Configuration::get('PS_STOCK_MANAGEMENT'),
            'quick_view'          => (bool)Configuration::get('PS_QUICK_VIEW'),
            'shop_phone'          => Configuration::get('PS_SHOP_PHONE'),
            'compared_products'   => is_array($compared_products) ? $compared_products : array(),
            'comparator_max_item' => (int)Configuration::get('PS_COMPARATOR_MAX_ITEM'),
            'currencySign'        => $currency->sign, // backward compat, see global.tpl
            'currencyFormat'      => $currency->format, // backward compat
            'currencyBlank'       => $currency->blank, // backward compat
            'is_occupancy_wise_search'    => $isOccupancyWiseSearch,
            'occupancy_required_for_booking' => $occupancyRequiredForBooking,
            'max_child_age' => Configuration::get('WK_GLOBAL_CHILD_MAX_AGE'),
            'max_child_in_room' => Configuration::get('WK_GLOBAL_MAX_CHILD_IN_ROOM'),
        ));

        // Add the tpl files directory for mobile
        if ($this->useMobileTheme()) {
            $this->context->smarty->assign(array(
                'tpl_mobile_uri' => _PS_THEME_MOBILE_DIR_,
            ));
        }

        // Deprecated
        $this->context->smarty->assign(array(
            'id_currency_cookie' => (int)$currency->id,
            'logged'             => $this->context->customer->isLogged(),
            'customerName'       => ($this->context->customer->logged ? $this->context->cookie->customer_firstname.' '.$this->context->cookie->customer_lastname : false)
        ));

        $assign_array = array(
            'img_cat_dir'   => _THEME_CAT_DIR_,
            'img_lang_dir'  => _THEME_LANG_DIR_,
            'img_prod_dir'  => _THEME_PROD_DIR_,
            'img_manu_dir'  => _THEME_MANU_DIR_,
            'img_sup_dir'   => _THEME_SUP_DIR_,
            'img_ship_dir'  => _THEME_SHIP_DIR_,
            'img_store_dir' => _THEME_STORE_DIR_,
            'img_col_dir'   => _THEME_COL_DIR_,
            'img_dir'       => _THEME_IMG_DIR_,
            'pic_dir'       => _THEME_PROD_PIC_DIR_
        );

        // Add the images directory for mobile
        if ($this->useMobileTheme()) {
            $assign_array['img_mobile_dir'] = _THEME_MOBILE_IMG_DIR_;
        }

        // Add the CSS directory for mobile
        if ($this->useMobileTheme()) {
            $assign_array['css_mobile_dir'] = _THEME_MOBILE_CSS_DIR_;
        }

        foreach ($assign_array as $assign_key => $assign_value) {
            if (substr($assign_value, 0, 1) == '/' || $protocol_content == 'https://') {
                $this->context->smarty->assign($assign_key, $protocol_content.Tools::getMediaServer($assign_value).$assign_value);
            } else {
                $this->context->smarty->assign($assign_key, $assign_value);
            }
        }

        /**
         * These shortcuts are DEPRECATED as of version 1.5.0.1
         * Use the Context to access objects instead.
         * Example: $this->context->cart
         */
        self::$cookie = $this->context->cookie;
        self::$cart   = $cart;
        self::$smarty = $this->context->smarty;
        self::$link   = $link;
        $defaultCountry = $this->context->country;

        if ($this->restrictedCountry) {
            $this->displayRestrictedCountryPage();
        }

        if (Tools::isSubmit('live_edit') && !$this->checkLiveEditAccess()) {
            Tools::redirect('index.php');
        }

        $this->iso               = $iso;
        $this->context->cart     = $cart;
        $this->context->currency = $currency;
    }

    /**
     * Method that is executed after init() and checkAccess().
     * Used to process user input.
     *
     * @see Controller::run()
     */
    public function postProcess()
    {
    }

    /**
     * Initializes common front page content: header, footer and side columns
     */
    public function initContent()
    {
        $this->process();

        if (!isset($this->context->cart)) {
            $this->context->cart = new Cart();
        }

        if (!$this->useMobileTheme()) {
            // These hooks aren't used for the mobile theme.
            // Needed hooks are called in the tpl files.
            $this->context->smarty->assign(array(
                'HOOK_HEADER'       => Hook::exec('displayHeader'),
                'HOOK_TOP'          => Hook::exec('displayTop'),
                'HOOK_LEFT_COLUMN'  => ($this->display_column_left  ? Hook::exec('displayLeftColumn') : ''),
                'HOOK_RIGHT_COLUMN' => ($this->display_column_right ? Hook::exec('displayRightColumn', array('cart' => $this->context->cart)) : ''),
                'show_breadcrump'   => $this->show_breadcrump,              // By webkul
            ));
        } else {
            $this->context->smarty->assign('HOOK_MOBILE_HEADER', Hook::exec('displayMobileHeader'));
        }
    }

    /**
     * Compiles and outputs page header section (including HTML <head>)
     *
     * @param bool $display If true, renders visual page header section
     * @deprecated 1.5.0.1
     */
    public function displayHeader($display = true)
    {
        Tools::displayAsDeprecated();

        $this->initHeader();
        $hook_header = Hook::exec('displayHeader');
        if ((Configuration::get('PS_CSS_THEME_CACHE') || Configuration::get('PS_JS_THEME_CACHE')) && is_writable(_PS_THEME_DIR_.'cache')) {
            // CSS compressor management
            if (Configuration::get('PS_CSS_THEME_CACHE')) {
                $this->css_files = Media::cccCss($this->css_files);
            }

            //JS compressor management
            if (Configuration::get('PS_JS_THEME_CACHE')) {
                $this->js_files = Media::cccJs($this->js_files);
            }
        }

        // Call hook before assign of css_files and js_files in order to include correctly all css and javascript files
        $this->context->smarty->assign(array(
            'HOOK_HEADER'       => $hook_header,
            'HOOK_TOP'          => Hook::exec('displayTop'),
            'HOOK_LEFT_COLUMN'  => ($this->display_column_left  ? Hook::exec('displayLeftColumn') : ''),
            'HOOK_RIGHT_COLUMN' => ($this->display_column_right ? Hook::exec('displayRightColumn', array('cart' => $this->context->cart)) : ''),
            'HOOK_FOOTER'       => Hook::exec('displayFooter')
        ));

        $this->context->smarty->assign(array(
            'css_files' => $this->css_files,
            'js_files'  => ($this->getLayout() && (bool)Configuration::get('PS_JS_DEFER')) ? array() : $this->js_files
        ));

        $this->display_header = $display;
        $this->smartyOutputContent(_PS_THEME_DIR_.'header.tpl');
    }

    /**
     * Compiles and outputs page footer section
     *
     * @deprecated 1.5.0.1
     */
    public function displayFooter($display = true)
    {
        Tools::displayAsDeprecated();
        $this->smartyOutputContent(_PS_THEME_DIR_.'footer.tpl');
    }

    /**
     * Renders and outputs maintenance page and ends controller process.
     */
    public function initCursedPage()
    {
        $this->displayMaintenancePage();
    }

    /**
     * Called before compiling common page sections (header, footer, columns).
     * Good place to modify smarty variables.
     *
     * @see FrontController::initContent()
     */
    public function process()
    {
    }

    /**
     * Redirects to redirect_after link
     *
     * @see $redirect_after
     */
    protected function redirect()
    {
        Tools::redirectLink($this->redirect_after);
    }

    /**
     * Renders page content.
     * Used for retrocompatibility with PS 1.4
     */
    public function displayContent()
    {
    }

    /**
     * Compiles and outputs full page content
     *
     * @return bool
     * @throws Exception
     * @throws SmartyException
     */
    public function display()
    {
        Tools::safePostVars();

        // assign css_files and js_files at the very last time
        if ((Configuration::get('PS_CSS_THEME_CACHE') || Configuration::get('PS_JS_THEME_CACHE')) && is_writable(_PS_THEME_DIR_.'cache')) {
            // CSS compressor management
            if (Configuration::get('PS_CSS_THEME_CACHE')) {
                $this->css_files = Media::cccCss($this->css_files);
            }
            //JS compressor management
            if (Configuration::get('PS_JS_THEME_CACHE') && !$this->useMobileTheme()) {
                $this->js_files = Media::cccJs($this->js_files);
            }
        }

        $this->context->smarty->assign(array(
            'css_files'      => $this->css_files,
            'js_files'       => ($this->getLayout() && (bool)Configuration::get('PS_JS_DEFER')) ? array() : $this->js_files,
            'js_defer'       => (bool)Configuration::get('PS_JS_DEFER'),
            'errors'         => $this->errors,
            'display_header' => $this->display_header,
            'display_footer' => $this->display_footer,
        ));

        $layout = $this->getLayout();
        if ($layout) {
            if ($this->template) {
                $template = $this->context->smarty->fetch($this->template);
            } else {
                // For retrocompatibility with 1.4 controller

                ob_start();
                $this->displayContent();
                $template = ob_get_contents();
                ob_clean();
            }
            $this->context->smarty->assign('template', $template);
            $this->smartyOutputContent($layout);
        } else {
            Tools::displayAsDeprecated('layout.tpl is missing in your theme directory');
            if ($this->display_header) {
                $this->smartyOutputContent(_PS_THEME_DIR_.'header.tpl');
            }

            if ($this->template) {
                $this->smartyOutputContent($this->template);
            } else { // For retrocompatibility with 1.4 controller
                $this->displayContent();
            }

            if ($this->display_footer) {
                $this->smartyOutputContent(_PS_THEME_DIR_.'footer.tpl');
            }
        }

        return true;
    }

    /**
     * Displays maintenance page if shop is closed.
     */
    protected function displayMaintenancePage()
    {
        if (isset(Context::getContext()->cookie->remote_addr)
            && Context::getContext()->cookie->remote_addr != ip2long(Tools::getRemoteAddr())
        ) {
            $cookie = Context::getContext()->cookie;
            if (isset($cookie->remote_addr)) {
                $cookie->enable_maintenance_view = false;
                $cookie->remote_addr = false;
                $cookie->write();
            }
        }

        if (Tools::isSubmit('SubmitLogin')
            && Configuration::get('PS_ALLOW_EMP')
            && !Context::getContext()->cookie->enable_maintenance_view
        ) {
            $email = trim(Tools::getValue('email'));
            $passwd = trim(Tools::getValue('passwd'));
            $this->processLogin($email, $passwd);
        }

        if (!Context::getContext()->cookie->enable_maintenance_view || !Configuration::get('PS_ALLOW_EMP')) {
            Context::getContext()->cookie->__unset('enable_maintenance_view');
            Context::getContext()->cookie->write();
            if ($this->maintenance == true || !(int)Configuration::get('PS_SHOP_ENABLE')) {
                $this->maintenance = true;
                if (!in_array(Tools::getRemoteAddr(), explode(',', Configuration::get('PS_MAINTENANCE_IP')))) {
                    header('HTTP/1.1 503 temporarily overloaded');

                    $this->context->smarty->assign($this->initLogoAndFavicon());
                    $this->context->smarty->assign(array(
                        'HOOK_MAINTENANCE' => Hook::exec('displayMaintenance', array()),
                        'allowEmployee' => Configuration::get('PS_ALLOW_EMP'),
                        'maintenance' => $this->context->link->getAdminLink('AdminMaintenance', false),
                        'languages' => Language::getLanguages(true, $this->context->shop->id),
                        'lang_iso' => $this->context->language->iso_code,
                        'link' => $this->context->link,
                    ));
                    // If the controller is a module, then getTemplatePath will try to find the template in the modules, so we need to instanciate a real frontcontroller
                    $front_controller = preg_match('/ModuleFrontController$/', get_class($this)) ? new FrontController() : $this;
                    $this->smartyOutputContent($front_controller->getTemplatePath($this->getThemeDir().'maintenance.tpl'));
                    exit;
                }
            }
        }
    }

    protected function processLogin($email, $passwd)
    {
        $result = false;
        $objMaintenanceAccess = new MaintenanceAccess();

        /* Check fields validity */
        if (empty($email)) {
            $this->errors[] = Tools::displayError('Email is empty.');
        } elseif (!Validate::isEmail($email)) {
            $this->errors[] = Tools::displayError('Invalid email address.');
        }

        if (empty($passwd)) {
            $this->errors[] = Tools::displayError('The password field is blank.');
        } elseif (!Validate::isPasswd($passwd)) {
            $this->errors[] = Tools::displayError('Invalid password.');
        }

        if ($objMaintenanceAccess->checkLimit($email)) {
            $this->errors[] =Tools::displayError('You have exceeded the limit of login attempts, please try after some time');
        }

        if (!count($this->errors)) {
            // Find employee
            $this->context->employee = new Employee();
            $is_employee_loaded = $this->context->employee->getByEmail($email, $passwd);
            $employee_associated_shop = $this->context->employee->getAssociatedShops();
            if (!$is_employee_loaded) {
                $this->errors[] = Tools::displayError('The Employee does not exist, or the password provided is incorrect.');
                $this->context->employee->logout();
            } elseif (empty($employee_associated_shop) && !$this->context->employee->isSuperAdmin()) {
                $this->errors[] = Tools::displayError('This employee does not manage the shop anymore (Either the shop has been deleted or permissions have been revoked).');
                $this->context->employee->logout();
            } else {
                // Update cookie
                $cookie = Context::getContext()->cookie;
                $cookie->enable_maintenance_view = $this->context->employee->id;
                $cookie->remote_addr = ip2long(Tools::getRemoteAddr());
                $cookie->write();
            }
            if (count($this->errors)) {
                $this->context->smarty->assign('errors', $this->errors);
                $objMaintenanceAccess->email = $email;
                $objMaintenanceAccess->ip_address = Tools::getRemoteAddr();
                $objMaintenanceAccess->save();
            } else {
                Tools::redirect('index.php');
            }
        } else {
            $this->context->smarty->assign('errors', $this->errors);
        }
        return $result;
    }

    /**
     * Displays 'country restricted' page if user's country is not allowed.
     */
    protected function displayRestrictedCountryPage()
    {
        header('HTTP/1.1 503 temporarily overloaded');
        $this->context->smarty->assign(array(
            'shop_name'   => $this->context->shop->name,
            'favicon_url' => $this->context->link->getMediaLink(_PS_IMG_.Configuration::get('PS_FAVICON')),
            'logo_url'    => $this->context->link->getMediaLink(_PS_IMG_.Configuration::get('PS_LOGO'))
        ));
        $this->smartyOutputContent($this->getTemplatePath($this->getThemeDir().'restricted-country.tpl'));
        exit;
    }

    /**
     * Redirects to correct protocol if settings and request methods don't match.
     */
    protected function sslRedirection()
    {
        // If we call a SSL controller without SSL or a non SSL controller with SSL, we redirect with the right protocol
        if (Configuration::get('PS_SSL_ENABLED') && $_SERVER['REQUEST_METHOD'] != 'POST' && $this->ssl != Tools::usingSecureMode()) {
            $this->context->cookie->disallowWriting();
            header('HTTP/1.1 301 Moved Permanently');
            header('Cache-Control: no-cache');
            if ($this->ssl) {
                header('Location: '.Tools::getShopDomainSsl(true).$_SERVER['REQUEST_URI']);
            } else {
                header('Location: '.Tools::getShopDomain(true).$_SERVER['REQUEST_URI']);
            }
            exit();
        }
    }

    /**
     * Redirects to canonical URL
     *
     * @param string $canonical_url
     */
    protected function canonicalRedirection($canonical_url = '')
    {
        if (!$canonical_url || !Configuration::get('PS_CANONICAL_REDIRECT') || strtoupper($_SERVER['REQUEST_METHOD']) != 'GET' || Tools::getValue('live_edit')) {
            return;
        }

        $match_url = rawurldecode(Tools::getCurrentUrlProtocolPrefix().$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        if (!preg_match('/^'.Tools::pRegexp(rawurldecode($canonical_url), '/').'([&?].*)?$/', $match_url)) {
            $params = array();
            $url_details = parse_url($canonical_url);

            if (!empty($url_details['query'])) {
                parse_str($url_details['query'], $query);
                foreach ($query as $key => $value) {
                    $params[Tools::safeOutput($key)] = Tools::safeOutput($value);
                }
            }
            $excluded_key = array('isolang', 'id_lang', 'controller', 'fc', 'id_product', 'id_category', 'id_manufacturer', 'id_supplier', 'id_cms');
            foreach ($_GET as $key => $value) {
                if (!in_array($key, $excluded_key)) {
                    if (is_array($value)) {
                        if (Validate::isUrl($key)) {
                            $params[Tools::safeOutput($key)] = $this->sanitizeQueryOutput($value);
                        }
                    } else {
                        if (Validate::isUrl($key) && Validate::isUrl($value)) {
                            $params[Tools::safeOutput($key)] = Tools::safeOutput($value);
                        }
                    }
                }
            }

            $str_params = http_build_query($params, '', '&');
            if (!empty($str_params)) {
                $final_url = preg_replace('/^([^?]*)?.*$/', '$1', $canonical_url).'?'.$str_params;
            } else {
                $final_url = preg_replace('/^([^?]*)?.*$/', '$1', $canonical_url);
            }

            // Don't send any cookie
            Context::getContext()->cookie->disallowWriting();

            if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_ && $_SERVER['REQUEST_URI'] != __PS_BASE_URI__) {
                die('[Debug] This page has moved<br />Please use the following URL instead: <a href="'.$final_url.'">'.$final_url.'</a>');
            }

            $redirect_type = Configuration::get('PS_CANONICAL_REDIRECT') == 2 ? '301' : '302';
            header('HTTP/1.0 '.$redirect_type.' Moved');
            header('Cache-Control: no-cache');
            Tools::redirectLink($final_url);
        }
    }

    protected function sanitizeQueryOutput($query)
    {
        $params = array();
        foreach ($query as $key => $value) {
            if (Validate::isUrl($key)) {
                if (is_array($value)) {
                    $params[Tools::safeOutput($key)] = $this->sanitizeQueryOutput($value);
                } else {
                    if (Validate::isUrl($value)) {
                        $params[Tools::safeOutput($key)] = Tools::safeOutput($value);
                    }
                }
            }
        }

        return $params;
    }

    /**
     * Geolocation management
     *
     * @param Country $default_country
     * @return Country|false
     */
    protected function geolocationManagement($default_country)
    {
        if (!in_array($_SERVER['SERVER_NAME'], array('localhost', '127.0.0.1'))) {
            /* Check if Maxmind Database exists */
            if (@filemtime(_PS_GEOIP_DIR_._PS_GEOIP_CITY_FILE_)) {
                if (!isset($this->context->cookie->iso_code_country) || (isset($this->context->cookie->iso_code_country) && !in_array(strtoupper($this->context->cookie->iso_code_country), explode(';', Configuration::get('PS_ALLOWED_COUNTRIES'))))) {
                    include_once(_PS_GEOIP_DIR_.'geoipcity.inc');

                    $gi = geoip_open(realpath(_PS_GEOIP_DIR_._PS_GEOIP_CITY_FILE_), GEOIP_STANDARD);
                    $record = geoip_record_by_addr($gi, Tools::getRemoteAddr());

                    if (is_object($record)) {
                        if (!in_array(strtoupper($record->country_code), explode(';', Configuration::get('PS_ALLOWED_COUNTRIES'))) && !FrontController::isInWhitelistForGeolocation()) {
                            if (Configuration::get('PS_GEOLOCATION_BEHAVIOR') == _PS_GEOLOCATION_NO_CATALOG_) {
                                $this->restrictedCountry = true;
                            } elseif (Configuration::get('PS_GEOLOCATION_BEHAVIOR') == _PS_GEOLOCATION_NO_ORDER_) {
                                $this->context->smarty->assign(array(
                                    'restricted_country_mode' => true,
                                    'geolocation_country'     => $record->country_name
                                ));
                            }
                        } else {
                            $has_been_set = !isset($this->context->cookie->iso_code_country);
                            $this->context->cookie->iso_code_country = strtoupper($record->country_code);
                        }
                    }
                }

                if (isset($this->context->cookie->iso_code_country) && $this->context->cookie->iso_code_country && !Validate::isLanguageIsoCode($this->context->cookie->iso_code_country)) {
                    $this->context->cookie->iso_code_country = Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'));
                }

                if (isset($this->context->cookie->iso_code_country) && ($id_country = (int)Country::getByIso(strtoupper($this->context->cookie->iso_code_country)))) {
                    /* Update defaultCountry */
                    if ($default_country->iso_code != $this->context->cookie->iso_code_country) {
                        $default_country = new Country($id_country);
                    }
                    if (isset($has_been_set) && $has_been_set) {
                        $this->context->cookie->id_currency = (int)($default_country->id_currency ? (int)$default_country->id_currency : (int)Configuration::get('PS_CURRENCY_DEFAULT'));
                    }
                    return $default_country;
                } elseif (Configuration::get('PS_GEOLOCATION_NA_BEHAVIOR') == _PS_GEOLOCATION_NO_CATALOG_ && !FrontController::isInWhitelistForGeolocation()) {
                    $this->restrictedCountry = true;
                } elseif (Configuration::get('PS_GEOLOCATION_NA_BEHAVIOR') == _PS_GEOLOCATION_NO_ORDER_ && !FrontController::isInWhitelistForGeolocation()) {
                    $this->context->smarty->assign(array(
                        'restricted_country_mode' => true,
                        'geolocation_country'     => isset($record->country_name) && $record->country_name ? $record->country_name : 'Undefined'
                    ));
                }
            }
        }
        return false;
    }

    /**
     * Specific medias for mobile device.
     * If autoload directory is present in the mobile theme, these files will not be loaded
     */
    public function setMobileMedia()
    {
        $this->addJquery();

        if (!file_exists($this->getThemeDir().'js/autoload/')) {
            $this->addJS(_THEME_MOBILE_JS_DIR_.'jquery.mobile-1.3.0.min.js');
            $this->addJS(_THEME_MOBILE_JS_DIR_.'jqm-docs.js');
            $this->addJS(_PS_JS_DIR_.'tools.js');
            $this->addJS(_THEME_MOBILE_JS_DIR_.'global.js');
            $this->addJqueryPlugin('fancybox');
        }

        if (!file_exists($this->getThemeDir().'css/autoload/')) {
            $this->addCSS(_THEME_MOBILE_CSS_DIR_.'jquery.mobile-1.3.0.min.css', 'all');
            $this->addCSS(_THEME_MOBILE_CSS_DIR_.'jqm-docs.css', 'all');
            $this->addCSS(_THEME_MOBILE_CSS_DIR_.'global.css', 'all');
        }
    }

    /**
     * Sets controller CSS and JS files.
     *
     * @return bool
     */
    public function setMedia()
    {
        /**
         * If website is accessed by mobile device
         * @see FrontControllerCore::setMobileMedia()
         */
        if ($this->useMobileTheme()) {
            $this->setMobileMedia();
            return true;
        }

        $this->addCSS(_THEME_CSS_DIR_.'grid_prestashop.css', 'all');  // retro compat themes 1.5.0.1
        $this->addCSS(_THEME_CSS_DIR_.'global.css', 'all');
        $this->addJquery();
        $this->addJqueryPlugin('easing');
        $this->addJS(_PS_JS_DIR_.'tools.js');
        $this->addJS(_THEME_JS_DIR_.'global.js');
        /*by webkul to show datepicker/timepicker on frontEnd*/
        $this->addJqueryUI('ui.datepicker');
        $this->addJS(array(
            _PS_JS_DIR_.'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js'
        ));
        //By webkul to show notifications
        $this->addjQueryPlugin('growl', null, false);
        // Automatically add js files from js/autoload directory in the template
        if (@filemtime($this->getThemeDir().'js/autoload/')) {
            foreach (scandir($this->getThemeDir().'js/autoload/', 0) as $file) {
                if (preg_match('/^[^.].*\.js$/', $file)) {
                    $this->addJS($this->getThemeDir().'js/autoload/'.$file);
                }
            }
        }
        // Automatically add css files from css/autoload directory in the template
        if (@filemtime($this->getThemeDir().'css/autoload/')) {
            foreach (scandir($this->getThemeDir().'css/autoload', 0) as $file) {
                if (preg_match('/^[^.].*\.css$/', $file)) {
                    $this->addCSS($this->getThemeDir().'css/autoload/'.$file);
                }
            }
        }

        $this->addJS(_PS_JS_DIR_.'daterangepicker/moment.min.js');
        $this->addjqueryPlugin('daterangepicker.min', _PS_JS_DIR_.'daterangepicker/');
        $this->addJS(_PS_JS_DIR_.'daterangepicker/qlodaterangepicker.js');

        $this->addCSS(_THEME_CSS_DIR_.'daterangepicker.css');

        if (Tools::isSubmit('live_edit') && Tools::getValue('ad') && Tools::getAdminToken('AdminModulesPositions'.(int)Tab::getIdFromClassName('AdminModulesPositions').(int)Tools::getValue('id_employee'))) {
            $this->addJqueryUI('ui.sortable');
            $this->addjqueryPlugin('fancybox');
            $this->addJS(_PS_JS_DIR_.'hookLiveEdit.js');
        }
        $this->addJqueryUI('ui.tooltip', 'base', true);

        if (Configuration::get('PS_QUICK_VIEW')) {
            $this->addjqueryPlugin('fancybox');
        }

        if (Configuration::get('PS_COMPARATOR_MAX_ITEM') > 0) {
            $this->addJS(_THEME_JS_DIR_.'products-comparison.js');
        }

        // Execute Hook FrontController SetMedia
        Hook::exec('actionFrontControllerSetMedia', array());

        return true;
    }

    /**
     * Initializes page header variables
     */
    public function initHeader()
    {
        header('Powered-By: QloApps');

        // Hooks are voluntary out the initialize array (need those variables already assigned)
        $this->context->smarty->assign(array(
            'time'                  => time(),
            'img_update_time'       => Configuration::get('PS_IMG_UPDATE_TIME'),
            'static_token'          => Tools::getToken(false),
            'token'                 => Tools::getToken(),
            'priceDisplayPrecision' => _PS_PRICE_DISPLAY_PRECISION_,
            'content_only'          => (int)Tools::getValue('content_only'),
        ));

        $this->context->smarty->assign($this->initLogoAndFavicon());
    }

    /**
     * Initializes page footer variables
     */
    public function initFooter()
    {
        $this->context->smarty->assign(array(
            'HOOK_FOOTER'            => Hook::exec('displayFooter'),
            'conditions'             => Configuration::get('PS_CONDITIONS'),
            'id_cgv'                 => Configuration::get('PS_CONDITIONS_CMS_ID'),
            'PS_SHOP_NAME'           => Configuration::get('PS_SHOP_NAME'),
            'PS_ALLOW_MOBILE_DEVICE' => isset($_SERVER['HTTP_USER_AGENT']) && (bool)Configuration::get('PS_ALLOW_MOBILE_DEVICE') && @filemtime(_PS_THEME_MOBILE_DIR_)
        ));

        /**
         * RTL support
         * rtl.css overrides theme css files for RTL
         * iso_code.css overrides default font for every language (optional)
         */
        if ($this->context->language->is_rtl) {
            $this->addCSS(_THEME_CSS_DIR_.'rtl.css');
            $this->addCSS(_THEME_CSS_DIR_.$this->context->language->iso_code.'.css');
        }
    }

    /**
     * Checks if the user can use Live Edit feature
     *
     * @return bool
     */
    public function checkLiveEditAccess()
    {
        if (!Tools::isSubmit('live_edit') || !Tools::getValue('ad') || !Tools::getValue('liveToken')) {
            return false;
        }

        if (Tools::getValue('liveToken') != Tools::getAdminToken('AdminModulesPositions'.(int)Tab::getIdFromClassName('AdminModulesPositions').(int)Tools::getValue('id_employee'))) {
            return false;
        }

        return is_dir(_PS_CORE_DIR_.DIRECTORY_SEPARATOR.Tools::getValue('ad'));
    }

    /**
     * Renders Live Edit widget
     *
     * @return string HTML
     */
    public function getLiveEditFooter()
    {
        if ($this->checkLiveEditAccess()) {
            $data = $this->context->smarty->createData();
            $data->assign(array(
                'ad'        => Tools::getValue('ad'),
                'live_edit' => true,
                'hook_list' => Hook::$executed_hooks,
                'id_shop'   => $this->context->shop->id
            ));
            return $this->context->smarty->createTemplate(_PS_ALL_THEMES_DIR_.'live_edit.tpl', $data)->fetch();
        } else {
            return '';
        }
    }

    /**
     * Assigns product list page sorting variables
     */
    public function productSort()
    {
        // $this->orderBy = Tools::getProductsOrder('by', Tools::getValue('orderby'));
        // $this->orderWay = Tools::getProductsOrder('way', Tools::getValue('orderway'));
        // 'orderbydefault' => Tools::getProductsOrder('by'),
        // 'orderwayposition' => Tools::getProductsOrder('way'), // Deprecated: orderwayposition
        // 'orderwaydefault' => Tools::getProductsOrder('way'),

        $stock_management = Configuration::get('PS_STOCK_MANAGEMENT') ? true : false; // no display quantity order if stock management disabled
        $order_by_values  = array(0 => 'name', 1 => 'price', 2 => 'date_add', 3 => 'date_upd', 4 => 'position', 5 => 'manufacturer_name', 6 => 'quantity', 7 => 'reference');
        $order_way_values = array(0 => 'asc', 1 => 'desc');

        $this->orderBy  = Tools::strtolower(Tools::getValue('orderby', $order_by_values[(int)Configuration::get('PS_PRODUCTS_ORDER_BY')]));
        $this->orderWay = Tools::strtolower(Tools::getValue('orderway', $order_way_values[(int)Configuration::get('PS_PRODUCTS_ORDER_WAY')]));

        if (!in_array($this->orderBy, $order_by_values)) {
            $this->orderBy = $order_by_values[0];
        }

        if (!in_array($this->orderWay, $order_way_values)) {
            $this->orderWay = $order_way_values[0];
        }

        $this->context->smarty->assign(array(
            'orderby'          => $this->orderBy,
            'orderway'         => $this->orderWay,
            'orderbydefault'   => $order_by_values[(int)Configuration::get('PS_PRODUCTS_ORDER_BY')],
            'orderwayposition' => $order_way_values[(int)Configuration::get('PS_PRODUCTS_ORDER_WAY')], // Deprecated: orderwayposition
            'orderwaydefault'  => $order_way_values[(int)Configuration::get('PS_PRODUCTS_ORDER_WAY')],
            'stock_management' => (int)$stock_management
        ));
    }

    /**
     * Assigns product list page pagination variables
     *
     * @param int|null $total_products
     * @throws PrestaShopException
     */
    public function pagination($total_products = null)
    {
        if (!self::$initialized) {
            $this->init();
        } elseif (!$this->context) {
            $this->context = Context::getContext();
        }

        // Retrieve the default number of products per page and the other available selections
        $default_products_per_page = max(1, (int)Configuration::get('PS_PRODUCTS_PER_PAGE'));
        $n_array = array($default_products_per_page, $default_products_per_page * 2, $default_products_per_page * 5);

        if ((int)Tools::getValue('n') && (int)$total_products > 0) {
            $n_array[] = $total_products;
        }
        // Retrieve the current number of products per page (either the default, the GET parameter or the one in the cookie)
        $this->n = $default_products_per_page;
        if (isset($this->context->cookie->nb_item_per_page) && in_array($this->context->cookie->nb_item_per_page, $n_array)) {
            $this->n = (int)$this->context->cookie->nb_item_per_page;
        }

        if ((int)Tools::getValue('n') && in_array((int)Tools::getValue('n'), $n_array)) {
            $this->n = (int)Tools::getValue('n');
        }

        // Retrieve the page number (either the GET parameter or the first page)
        $this->p = (int)Tools::getValue('p', 1);
        // If the parameter is not correct then redirect (do not merge with the previous line, the redirect is required in order to avoid duplicate content)
        if (!is_numeric($this->p) || $this->p < 1) {
            Tools::redirect($this->context->link->getPaginationLink(false, false, $this->n, false, 1, false));
        }

        // Remove the page parameter in order to get a clean URL for the pagination template
        $current_url = preg_replace('/(?:(\?)|&amp;)p=\d+/', '$1', Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']));

        if ($this->n != $default_products_per_page || isset($this->context->cookie->nb_item_per_page)) {
            $this->context->cookie->nb_item_per_page = $this->n;
        }

        $pages_nb = ceil($total_products / (int)$this->n);
        if ($this->p > $pages_nb && $total_products != 0) {
            Tools::redirect($this->context->link->getPaginationLink(false, false, $this->n, false, $pages_nb, false));
        }

        $range = 2; /* how many pages around page selected */
        $start = (int)($this->p - $range);
        if ($start < 1) {
            $start = 1;
        }

        $stop = (int)($this->p + $range);
        if ($stop > $pages_nb) {
            $stop = (int)$pages_nb;
        }

        $this->context->smarty->assign(array(
            'nb_products'       => $total_products,
            'products_per_page' => $this->n,
            'pages_nb'          => $pages_nb,
            'p'                 => $this->p,
            'n'                 => $this->n,
            'nArray'            => $n_array,
            'range'             => $range,
            'start'             => $start,
            'stop'              => $stop,
            'current_url'       => $current_url,
        ));
    }

    /**
     * Sets and returns customer groups that the current customer(visitor) belongs to.
     *
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public static function getCurrentCustomerGroups()
    {
        if (!Group::isFeatureActive()) {
            return array();
        }

        $context = Context::getContext();
        if (!isset($context->customer) || !$context->customer->id) {
            return array();
        }

        if (!is_array(self::$currentCustomerGroups)) {
            self::$currentCustomerGroups = array();
            $result = Db::getInstance()->executeS('SELECT id_group FROM '._DB_PREFIX_.'customer_group WHERE id_customer = '.(int)$context->customer->id);
            foreach ($result as $row) {
                self::$currentCustomerGroups[] = $row['id_group'];
            }
        }

        return self::$currentCustomerGroups;
    }

    /**
     * Checks if user's location is whitelisted.
     *
     * @staticvar bool|null $allowed
     * @return bool
     */
    protected static function isInWhitelistForGeolocation()
    {
        static $allowed = null;

        if ($allowed !== null) {
            return $allowed;
        }

        $allowed = false;
        $user_ip = Tools::getRemoteAddr();
        $ips = array();

        // retrocompatibility
        $ips_old = explode(';', Configuration::get('PS_GEOLOCATION_WHITELIST'));
        if (is_array($ips_old) && count($ips_old)) {
            foreach ($ips_old as $ip) {
                $ips = array_merge($ips, explode("\n", $ip));
            }
        }

        $ips = array_map('trim', $ips);
        if (is_array($ips) && count($ips)) {
            foreach ($ips as $ip) {
                if (!empty($ip) && preg_match('/^'.$ip.'.*/', $user_ip)) {
                    $allowed = true;
                }
            }
        }

        return $allowed;
    }

    /**
     * Checks if token is valid
     *
     * @since 1.5.0.1
     * @return bool
     */
    public function isTokenValid()
    {
        if (!Configuration::get('PS_TOKEN_ENABLE')) {
            return true;
        }

        return (strcasecmp(Tools::getToken(false), Tools::getValue('token')) == 0);
    }

    /**
     * Adds a media file(s) (CSS, JS) to page header
     *
     * @param string|array $media_uri Path to file, or an array of paths like: array(array(uri => media_type), ...)
     * @param string|null $css_media_type CSS media type
     * @param int|null $offset
     * @param bool $remove If True, removes media files
     * @param bool $check_path If true, checks if files exists
     * @return true|void
     */
    public function addMedia($media_uri, $css_media_type = null, $offset = null, $remove = false, $check_path = true)
    {
        if (!is_array($media_uri)) {
            if ($css_media_type) {
                $media_uri = array($media_uri => $css_media_type);
            } else {
                $media_uri = array($media_uri);
            }
        }

        $list_uri = array();
        foreach ($media_uri as $file => $media) {
            if (!Validate::isAbsoluteUrl($media)) {
                $different = 0;
                $different_css = 0;
                $type = 'css';
                if (!$css_media_type) {
                    $type = 'js';
                    $file = $media;
                }
                if (strpos($file, __PS_BASE_URI__.'modules/') === 0) {
                    $override_path = str_replace(__PS_BASE_URI__.'modules/', _PS_ROOT_DIR_.'/themes/'._THEME_NAME_.'/'.$type.'/modules/', $file, $different);
                    if (strrpos($override_path, $type.'/'.basename($file)) !== false) {
                        $override_path_css = str_replace($type.'/'.basename($file), basename($file), $override_path, $different_css);
                    }

                    if ($different && @filemtime($override_path)) {
                        $file = str_replace(__PS_BASE_URI__.'modules/', __PS_BASE_URI__.'themes/'._THEME_NAME_.'/'.$type.'/modules/', $file, $different);
                    } elseif ($different_css && @filemtime($override_path_css)) {
                        $file = $override_path_css;
                    }
                    if ($css_media_type) {
                        $list_uri[$file] = $media;
                    } else {
                        $list_uri[] = $file;
                    }
                } else {
                    $list_uri[$file] = $media;
                }
            } else {
                $list_uri[$file] = $media;
            }
        }

        if ($remove) {
            if ($css_media_type) {
                return parent::removeCSS($list_uri, $css_media_type);
            }
            return parent::removeJS($list_uri);
        }

        if ($css_media_type) {
            return parent::addCSS($list_uri, $css_media_type, $offset, $check_path);
        }

        return parent::addJS($list_uri, $check_path);
    }

    /**
     * Removes media file(s) from page header
     *
     * @param string|array $media_uri Path to file, or an array paths of like: array(array(uri => media_type), ...)
     * @param string|null $css_media_type CSS media type
     * @param bool $check_path If true, checks if files exists
     */
    public function removeMedia($media_uri, $css_media_type = null, $check_path = true)
    {
        FrontController::addMedia($media_uri, $css_media_type, null, true, $check_path);
    }

    /**
     * Add one or several CSS for front, checking if css files are overridden in theme/css/modules/ directory
     * @see Controller::addCSS()
     *
     * @param array|string $css_uri $media_uri Path to file, or an array of paths like: array(array(uri => media_type), ...)
     * @param string $css_media_type CSS media type
     * @param int|null $offset
     * @param bool $check_path If true, checks if files exists
     * @return true|void
     */
    public function addCSS($css_uri, $css_media_type = 'all', $offset = null, $check_path = true)
    {
        return FrontController::addMedia($css_uri, $css_media_type, $offset = null, false, $check_path);
    }

    /**
     * Removes CSS file(s) from page header
     *
     * @param array|string $css_uri $media_uri Path to file, or an array of paths like: array(array(uri => media_type), ...)
     * @param string $css_media_type CSS media type
     * @param bool $check_path If true, checks if files exists
     */
    public function removeCSS($css_uri, $css_media_type = 'all', $check_path = true)
    {
        return FrontController::removeMedia($css_uri, $css_media_type, $check_path);
    }

    /**
     * Add one or several JS files for front, checking if js files are overridden in theme/js/modules/ directory
     * @see Controller::addJS()
     *
     * @param array|string $js_uri Path to file, or an array of paths
     * @param bool $check_path If true, checks if files exists
     * @return true|void
     */
    public function addJS($js_uri, $check_path = true)
    {
        return Frontcontroller::addMedia($js_uri, null, null, false, $check_path);
    }

    /**
     * Removes JS file(s) from page header
     *
     * @param array|string $js_uri Path to file, or an array of paths
     * @param bool $check_path If true, checks if files exists
     */
    public function removeJS($js_uri, $check_path = true)
    {
        return FrontController::removeMedia($js_uri, null, $check_path);
    }

    /**
     * Recovers cart information
     *
     * @return int|false
     */
    protected function recoverCart()
    {
        if (($id_cart = (int)Tools::getValue('recover_cart')) && Tools::getValue('token_cart') == md5(_COOKIE_KEY_.'recover_cart_'.$id_cart)) {
            $cart = new Cart((int)$id_cart);
            if (Validate::isLoadedObject($cart)) {
                $customer = new Customer((int)$cart->id_customer);
                if (Validate::isLoadedObject($customer)) {
                    $customer->logged = 1;
                    $this->context->customer = $customer;
                    $this->context->cookie->id_customer = (int)$customer->id;
                    $this->context->cookie->customer_lastname = $customer->lastname;
                    $this->context->cookie->customer_firstname = $customer->firstname;
                    $this->context->cookie->logged = 1;
                    $this->context->cookie->check_cgv = 1;
                    $this->context->cookie->is_guest = $customer->isGuest();
                    $this->context->cookie->passwd = $customer->passwd;
                    $this->context->cookie->email = $customer->email;
                    $this->context->cookie->id_guest = (int) $cart->id_guest;
                    return $id_cart;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Sets template file for page content output
     *
     * @param string $default_template
     */
    public function setTemplate($default_template)
    {
        if ($this->useMobileTheme()) {
            $this->setMobileTemplate($default_template);
        } else {
            $template = $this->getOverrideTemplate();
            if ($template) {
                parent::setTemplate($template);
            } else {
                parent::setTemplate($default_template);
            }
        }
    }

    /**
     * Returns an overridden template path (if any) for this controller.
     * If not overridden, will return false. This method can be easily overriden in a
     * specific controller.
     *
    * @since 1.5.0.13
    * @return string|bool
    */
    public function getOverrideTemplate()
    {
        return Hook::exec('DisplayOverrideTemplate', array('controller' => $this));
    }

    /**
     * Checks if mobile theme is active and in use.
     *
     * @staticvar bool|null $use_mobile_template
     * @return bool
     */
    protected function useMobileTheme()
    {
        static $use_mobile_template = null;

        // The mobile theme must have a layout to be used
        if ($use_mobile_template === null) {
            $use_mobile_template = ($this->context->getMobileDevice() && file_exists(_PS_THEME_MOBILE_DIR_.'layout.tpl'));
        }

        return $use_mobile_template;
    }

    /**
     * Returns theme directory (regular or mobile)
     *
     * @return string
     */
    protected function getThemeDir()
    {
        return $this->useMobileTheme() ? _PS_THEME_MOBILE_DIR_ : _PS_THEME_DIR_;
    }


    /**
     * Returns theme override directory (regular or mobile)
     *
     * @return string
     */
    protected function getOverrideThemeDir()
    {
        return $this->useMobileTheme() ? _PS_THEME_MOBILE_OVERRIDE_DIR_ : _PS_THEME_OVERRIDE_DIR_;
    }

    /**
     * Returns the layout corresponding to the current page by using the override system
     * Ex:
     * On the url: http://localhost/index.php?id_product=1&controller=product, this method will
     * check if the layout exists in the following files (in that order), and return the first found:
     * - /themes/default/override/layout-product-1.tpl
     * - /themes/default/override/layout-product.tpl
     * - /themes/default/layout.tpl
     *
     * @since 1.5.0.13
     * @return bool|string
     */
    public function getLayout()
    {
        $entity = $this->php_self;
        $id_item = (int)Tools::getValue('id_'.$entity);

        $layout_dir = $this->getThemeDir();
        $layout_override_dir  = $this->getOverrideThemeDir();

        $layout = false;
        if ($entity) {
            if ($id_item > 0 && file_exists($layout_override_dir.'layout-'.$entity.'-'.$id_item.'.tpl')) {
                $layout = $layout_override_dir.'layout-'.$entity.'-'.$id_item.'.tpl';
            } elseif (file_exists($layout_override_dir.'layout-'.$entity.'.tpl')) {
                $layout = $layout_override_dir.'layout-'.$entity.'.tpl';
            }
        }

        if (!$layout && file_exists($layout_dir.'layout.tpl')) {
            $layout = $layout_dir.'layout.tpl';
        }

        return $layout;
    }

    /**
     * Returns template path
     *
     * @param string $template
     * @return string
     */
    public function getTemplatePath($template)
    {
        if (!$this->useMobileTheme()) {
            return $template;
        }

        $tpl_file = basename($template);
        $dirname = dirname($template).(substr(dirname($template), -1, 1) == '/' ? '' : '/');

        if ($dirname == _PS_THEME_DIR_) {
            if (file_exists(_PS_THEME_MOBILE_DIR_.$tpl_file)) {
                $template = _PS_THEME_MOBILE_DIR_.$tpl_file;
            }
        } elseif ($dirname == _PS_THEME_MOBILE_DIR_) {
            if (!file_exists(_PS_THEME_MOBILE_DIR_.$tpl_file) && file_exists(_PS_THEME_DIR_.$tpl_file)) {
                $template = _PS_THEME_DIR_.$tpl_file;
            }
        }

        return $template;
    }

    /**
     * Checks if the template set is available for mobile themes,
     * otherwise front template is chosen.
     *
     * @param string $template
     */
    public function setMobileTemplate($template)
    {
        $this->context->smarty->assign(array(
            'categoriesTree'            => Category::getRootCategory()->recurseLiteCategTree(0),
            'categoriescmsTree'         => CMSCategory::getRecurseCategory($this->context->language->id, 1, 1, 1),
            'voucherAllowed'            => (int)CartRule::isFeatureActive(),
            'PS_DISPLAY_BEST_SELLERS'   => Configuration::get('PS_DISPLAY_BEST_SELLERS'),
            'display_store'             => Configuration::get('PS_STORES_DISPLAY_SITEMAP'),
            'conditions'                => Configuration::get('PS_CONDITIONS'),
            'id_cgv'                    => Configuration::get('PS_CONDITIONS_CMS_ID'),
            'PS_SHOP_NAME'              => Configuration::get('PS_SHOP_NAME'),
        ));

        $template = $this->getTemplatePath($template);

        $assign = array();
        $assign['tpl_file'] = basename($template, '.tpl');
        if (isset($this->php_self)) {
            $assign['controller_name'] = $this->php_self;
        }

        $this->context->smarty->assign($assign);
        $this->template = $template;
    }

    /**
     * Returns logo and favicon variables, depending
     * on active theme type (regular or mobile)
     *
     * @since 1.5.3.0
     * @return array
     */
    public function initLogoAndFavicon()
    {
        $mobile_device = $this->context->getMobileDevice();

        if ($mobile_device && Configuration::get('PS_LOGO_MOBILE')) {
            $logo = $this->context->link->getMediaLink(_PS_IMG_.Configuration::get('PS_LOGO_MOBILE').'?'.Configuration::get('PS_IMG_UPDATE_TIME'));
        } else {
            $logo = $this->context->link->getMediaLink(_PS_IMG_.Configuration::get('PS_LOGO'));
        }

        return array(
            'favicon_url'       => $this->context->link->getMediaLink(_PS_IMG_.Configuration::get('PS_FAVICON')),
            'logo_image_width'  => ($mobile_device == false ? Configuration::get('SHOP_LOGO_WIDTH')  : Configuration::get('SHOP_LOGO_MOBILE_WIDTH')),
            'logo_image_height' => ($mobile_device == false ? Configuration::get('SHOP_LOGO_HEIGHT') : Configuration::get('SHOP_LOGO_MOBILE_HEIGHT')),
            'logo_url'          => $logo
        );
    }

    /**
     * Renders and adds color list HTML for each product in a list
     *
     * @param array $products
     */
    public function addColorsToProductList(&$products)
    {
        if (!is_array($products) || !count($products) || !file_exists(_PS_THEME_DIR_.'product-list-colors.tpl')) {
            return;
        }

        $products_need_cache = array();
        foreach ($products as &$product) {
            if (!$this->isCached(_PS_THEME_DIR_.'product-list-colors.tpl', $this->getColorsListCacheId($product['id_product']))) {
                $products_need_cache[] = (int)$product['id_product'];
            }
        }

        unset($product);

        $colors = false;
        if (count($products_need_cache)) {
            $colors = Product::getAttributesColorList($products_need_cache);
        }

        Tools::enableCache();
        foreach ($products as &$product) {
            $tpl = $this->context->smarty->createTemplate(_PS_THEME_DIR_.'product-list-colors.tpl', $this->getColorsListCacheId($product['id_product']));
            if (isset($colors[$product['id_product']])) {
                $tpl->assign(array(
                    'id_product'  => $product['id_product'],
                    'colors_list' => $colors[$product['id_product']],
                    'link'        => Context::getContext()->link,
                    'img_col_dir' => _THEME_COL_DIR_,
                    'col_img_dir' => _PS_COL_IMG_DIR_
                ));
            }

            if (!in_array($product['id_product'], $products_need_cache) || isset($colors[$product['id_product']])) {
                $product['color_list'] = $tpl->fetch(_PS_THEME_DIR_.'product-list-colors.tpl', $this->getColorsListCacheId($product['id_product']));
            } else {
                $product['color_list'] = '';
            }
        }
        Tools::restoreCacheSettings();
    }

    /**
     * Returns cache ID for product color list
     *
     * @param int $id_product
     * @return string
     */
    protected function getColorsListCacheId($id_product)
    {
        return Product::getColorsListCacheId($id_product);
    }
}
