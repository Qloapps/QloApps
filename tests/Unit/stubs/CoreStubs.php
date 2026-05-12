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

// ── Exceptions ──────────────────────────────────────────────────────���─────────
// Real PrestaShopException writes log files and renders HTML backtraces.
if (!class_exists('PrestaShopException')) {
    class PrestaShopException extends RuntimeException {}
}
if (!class_exists('PrestaShopDatabaseException')) {
    class PrestaShopDatabaseException extends PrestaShopException {}
}

// ── ObjectModel ───────────────────────────────────────────────────────────────
// Real ObjectModelCore is abstract and requires live DB for add/update/delete.
if (!class_exists('ObjectModel')) {
    class ObjectModel
    {
        const TYPE_INT     = 1;
        const TYPE_BOOL    = 2;
        const TYPE_STRING  = 3;
        const TYPE_FLOAT   = 4;
        const TYPE_DATE    = 5;
        const TYPE_HTML    = 6;
        const TYPE_NOTHING = 7;

        public static $definition = [];
        public $id = null;
        public static bool $updateResult = true;

        public function __construct($id = null) { $this->id = $id; }
        public function add($autodate = true, $null_values = true) { $this->id = (int) Db::getInstance()->Insert_ID(); return true; }
        public function update($null_values = false) { return static::$updateResult; }
        public function delete() { return true; }
        public function save() { return true; }
        public function validateFields($die = true, $error_return = false) { return true; }
        public function toggleStatus() { return true; }
        public function getWebserviceObjectList($sql_join, $sql_filter, $sql_sort, $sql_limit) { return []; }
    }
}

// ── Db ────────────────────────────────────────────────────────────────��───────
// Real DbCore is abstract and requires a live MySQL connection.
// Tests inject a PHPUnit mock via ReflectionProperty into Db::$instance.
if (!class_exists('Db')) {
    class Db
    {
        const INSERT                  = 1;
        const INSERT_IGNORE           = 2;
        const REPLACE                 = 3;
        const ON_DUPLICATE_KEY        = 4;
        const INSERT_ON_DUPLICATE_KEY = 4;

        private static ?self $instance = null;

        public static function getInstance($slave = 0): static
        {
            if (!static::$instance) {
                static::$instance = new static();
            }
            return static::$instance;
        }

        public function getRow($sql): array|false { return false; }
        public function getValue($sql): string|false { return false; }
        public function executeS($sql): array|false { return []; }
        public function execute($sql): bool { return true; }
        public function insert(string $table, array $data, bool $null_values = false, bool $use_cache = true, $type = null): bool { return true; }
        public function delete(string $table, string $where = '', int $limit = 0): bool { return true; }
        public function Insert_ID(): int { return 0; }
        public function escape($str, $htmlOK = false): string { return addslashes((string) $str); }
    }
}

// ── DbQuery ───────────────────────────────────────────────────────────────────
// Real DbQuery builds SQL strings via a fluent interface. This stub returns a
// fixed query string from build() so getValue() can be called normally in tests.
if (!class_exists('DbQuery')) {
    class DbQuery
    {
        public function select(string $fields): static { return $this; }
        public function from(string $table, ?string $alias = null): static { return $this; }
        public function leftJoin(string $table, ?string $alias = null, ?string $on = null): static { return $this; }
        public function where(string $restriction): static { return $this; }
        public function groupBy(string $fields): static { return $this; }
        public function orderBy(string $fields): static { return $this; }
        public function build(): string { return 'SELECT 1'; }
    }
}

// ── Context ────────────────────────────────────────────────────────────────��──
// Real ContextCore requires mobile_detect lib, cookie, shop, cart objects.
if (!class_exists('Context')) {
    class Context
    {
        private static ?self $instance = null;
        public $shop;
        public $language;
        public $cookie;
        public $controller;
        public $cart;

        public static function getContext(): static
        {
            if (!static::$instance) {
                static::$instance = new static();
            }
            return static::$instance;
        }
    }
}

// ── Configuration ─────────────────────────────���───────────────────────────────
// Real ConfigurationCore::get() runs SQL. Tests use set()/resetAll() to control values.
if (!class_exists('Configuration')) {
    class Configuration
    {
        private static array $values = [];

        public static function get(string $key, $id_lang = null, $id_shop_group = null, $id_shop = null, $default = false): mixed
        {
            return static::$values[$key] ?? $default;
        }

        public static function set(string $key, $value): void
        {
            static::$values[$key] = $value;
        }

        public static function getMultiple(array $keys, $id_lang = null, $id_shop_group = null, $id_shop = null): array
        {
            $result = [];
            foreach ($keys as $key) {
                if (array_key_exists($key, static::$values)) {
                    $result[$key] = static::$values[$key];
                }
            }
            return $result;
        }

        public static function updateValue(string $key, $value, bool $html = false, $id_shop_group = null, $id_shop = null): bool
        {
            static::$values[$key] = $value;
            return true;
        }

        public static function deleteByName(string $key): bool
        {
            unset(static::$values[$key]);
            return true;
        }

        public static function resetAll(): void
        {
            static::$values = [];
        }
    }
}

// ── Tools ────────────────────────────���────────────────────────────────────────
// Real ToolsCore::encrypt() needs _COOKIE_KEY_ secret (from settings.inc.php).
// Real ToolsCore::displayError() needs Context->language->iso_code.
if (!class_exists('Tools')) {
    class Tools
    {
        public static function displayError(string $message = ''): string { return $message; }
        public static function encrypt(string $passwd): string { return md5($passwd); }
        public static function passwdGen(int $length = 8, string $type = 'RANDOM'): string { return str_repeat('a', $length); }
        public static function displayAsDeprecated(): void {}
        public static function cleanNonUnicodeSupport($pattern) { return $pattern; }
        public static function strlen($str, $encoding = 'UTF-8')
        {
            if (null === $str || is_array($str)) { return false; }
            return function_exists('mb_strlen') ? mb_strlen(html_entity_decode($str, ENT_COMPAT, 'UTF-8'), $encoding) : strlen($str);
        }

        public static function getValue(string $key, $default = false): mixed { return $default; }
        public static function isSubmit(string $key): bool { return false; }
        public static function getShopDomainSsl(bool $http = false, bool $entities = false): string { return 'https://example.com'; }
        public static function getAdminTokenLite(string $tab): string { return 'testtoken'; }
        public static function displayPrice(float $price, $currency = null, bool $no_utf8 = true): string { return number_format($price, 2); }
        public static function nl2br(string $str): string { return nl2br($str); }
    }
}

// ── Shop ──────────────────────────────────────────────────────────────────���───
// Real ShopCore extends ObjectModel and uses Db.
if (!class_exists('Shop')) {
    class Shop
    {
        const SHARE_CUSTOMER = 1;
        public static function addSqlRestriction($type = null, $alias = null): string { return ''; }
        public static function addSqlAssociation(string $table, string $alias): string { return ''; }
    }
}

// ── Cache ───────────────────────────��─────────────────────────────────────────
// Real CacheCore is abstract and may write to disk/memory backends.
// Tests need resetAll() for isolation between test cases.
if (!class_exists('Cache')) {
    class Cache
    {
        private static array $store = [];
        public static function isStored(string $key): bool { return array_key_exists($key, static::$store); }
        public static function store(string $key, $value): void { static::$store[$key] = $value; }
        public static function retrieve(string $key): mixed { return static::$store[$key] ?? null; }
        public static function clean(string $key): void { unset(static::$store[$key]); }
        public static function resetAll(): void { static::$store = []; }
    }
}

// ── Domain stubs ─────────────────────────────────��────────────────────────────
if (!class_exists('Group')) {
    class Group
    {
        private static bool $featureActive = true;
        public static function setFeatureActive(bool $value): void { static::$featureActive = $value; }
        public static function isFeatureActive(): bool { return static::$featureActive; }
    }
}

if (!class_exists('Order')) {
    class Order
    {
        public static function getCustomerOrders(int $id_customer): array { return []; }
    }
}

if (!class_exists('Address')) {
    class Address
    {
        public function __construct(int $id = 0) {}
        public function delete(): bool { return true; }
        public static function getCountryAndState(int $id_address): array { return []; }
    }
}

if (!class_exists('CartRule')) {
    class CartRule
    {
        public static function deleteByIdCustomer(int $id_customer): bool { return true; }
    }
}

if (!class_exists('HotelCartBookingData')) {
    class HotelCartBookingData
    {
        public function deleteCartBookingData($id_cart, $a, $b, $c, $d, $e): bool { return true; }
    }
}

if (!class_exists('CustomerGuestDetail')) {
    class CustomerGuestDetail
    {
        public function deleteCustomerGuestByIdCustomer(int $id_customer): bool { return true; }
    }
}

if (!class_exists('Cart')) {
    class Cart
    {
        public int $id = 0;
        public int $id_currency = 0;
        public function __construct(int $id = 0) { $this->id = $id; }
        public static function getCustomerCarts(int $id_customer, bool $with_order = true): array { return []; }
        public function nbProducts(): int { return 0; }
    }
}

if (!class_exists('Mail')) {
    class Mail
    {
        public static function Send($id_lang, $template, $subject, $vars, $to, $to_name, ...$rest): bool { return true; }
        public static function l(string $string, int $id_lang = null): string { return $string; }
    }
}

if (!class_exists('Hook')) {
    class Hook
    {
        public static string $hookReturn = '';

        public static function exec(string $hook_name, array $hook_args = []): string
        {
            return static::$hookReturn;
        }
    }
}

// ── Module / PaymentModule ────────────────────────────────────────────────────
// Real ModuleCore pulls DB, translation files, Smarty, Context, and HTTP.
// This stub supplies the minimum surface area for unit-testing module logic:
// install/uninstall branching, hook handlers, getContent, and configuration.
if (!class_exists('Module')) {
    class Module
    {
        public ?int $id = null;
        public string $name = '';
        public string $tab = '';
        public string $version = '';
        public string $author = '';
        public array $controllers = [];
        public bool $bootstrap = false;
        public bool $active = true;
        public string $displayName = '';
        public string $description = '';
        public string $confirmUninstall = '';
        public string $warning = '';
        public string $_path = '/modules/test/';
        public string $local_path = '/modules/test/';
        public string $identifier = '';
        public string $table = '';
        public $smarty = null;
        public $context = null;

        public function __construct()
        {
            $this->context = Context::getContext();
        }

        public function install(): bool { return true; }
        public function uninstall(): bool { return true; }
        public function registerHook(string $hook_name): bool { return true; }
        public function unregisterHook(string $hook_name): bool { return true; }
        public function l(string $string, string $specific = ''): string { return $string; }
        public function display(string $file, string $template): string { return ''; }
        public function displayError(string $message): string { return '<p class="error">'.$message.'</p>'; }
        public function displayConfirmation(string $message): string { return '<p class="conf">'.$message.'</p>'; }
        public function getCurrency(int $id_currency): array|false { return []; }
        public function addTab(array $tab): bool { return true; }
        public function removeTab(string $class_name): bool { return true; }
    }
}

if (!class_exists('PaymentModule')) {
    class PaymentModule extends Module
    {
        public bool $currencies = true;
        public string $currencies_mode = 'checkbox';
        public int $payment_type = 0;

        public function checkCurrency($cart): bool { return true; }
        public function execPayment($cart): void {}
    }
}

// ── Currency ──────────────────────────────────────────────────────────────────
if (!class_exists('Currency')) {
    class Currency
    {
        public int $id = 1;
        public string $iso_code = 'USD';
        public string $sign = '$';
        public function __construct(int $id = 1) { $this->id = $id; }
        public static function checkPaymentCurrencies(int $id_module): array { return [['id_currency' => 1]]; }
    }
}

// ── Language ──────────────────────────────────────────────────────────────────
if (!class_exists('Language')) {
    class Language
    {
        public int $id = 1;
        public string $iso_code = 'en';
        public function __construct(int $id = 1) { $this->id = $id; }
    }
}

// ── Media ─────────────────────────────────────────────────────────────────────
if (!class_exists('Media')) {
    class Media
    {
        public static function getMediaPath(string $path): string { return $path; }
    }
}

// ── OrderPayment ──────────────────────────────────────────────────────────────
if (!class_exists('OrderPayment')) {
    class OrderPayment
    {
        const PAYMENT_TYPE_REMOTE_PAYMENT = 1;
    }
}

// ── OrderState ────────────────────────────────────────────────────────────────
if (!class_exists('OrderState')) {
    class OrderState
    {
        public bool $logable = false;
        public function __construct(int $id = 0) {}
    }
}

// ── HelperForm ────────────────────────────────────────────────────────────────
if (!class_exists('HelperForm')) {
    class HelperForm
    {
        public bool $show_toolbar = false;
        public string $table = '';
        public int $default_form_language = 1;
        public int $allow_employee_form_lang = 0;
        public int $id = 0;
        public string $identifier = '';
        public string $submit_action = '';
        public string $currentIndex = '';
        public string $token = '';
        public array $tpl_vars = [];
        public function generateForm(array $fields_form): string { return '<form></form>'; }
    }
}
