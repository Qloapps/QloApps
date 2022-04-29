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
require_once (_PS_TOOL_DIR_.'defuse/php-encryption/defuse-crypto.phar');

class InstallModelInstall extends InstallAbstractModel
{
    const SETTINGS_FILE = 'config/settings.inc.php';

    /**
     * @var FileLogger
     */
    public $logger;

    public function __construct()
    {
        parent::__construct();

        $this->logger = new FileLogger();
        if (is_writable(_PS_ROOT_DIR_.'/log/')) {
            $this->logger->setFilename(_PS_ROOT_DIR_.'/log/'.@date('Ymd').'_installation.log');
        }
    }

    public function setError($errors)
    {
        if (!is_array($errors)) {
            $errors = array($errors);
        }

        parent::setError($errors);

        foreach ($errors as $error) {
            $this->logger->logError($error);
        }
    }

    /**
     * Generate settings file
     */
    public function generateSettingsFile($database_server, $database_login, $database_password, $database_name, $database_prefix, $database_engine)
    {
        // Check permissions for settings file
        if (file_exists(_PS_ROOT_DIR_.'/'.self::SETTINGS_FILE) && !is_writable(_PS_ROOT_DIR_.'/'.self::SETTINGS_FILE)) {
            $this->setError($this->language->l('%s file is not writable (check permissions)', self::SETTINGS_FILE));
            return false;
        } elseif (!file_exists(_PS_ROOT_DIR_.'/'.self::SETTINGS_FILE) && !is_writable(_PS_ROOT_DIR_.'/'.dirname(self::SETTINGS_FILE))) {
            $this->setError($this->language->l('%s folder is not writable (check permissions)', dirname(self::SETTINGS_FILE)));
            return false;
        }
        $key = \Defuse\Crypto\Key::createNewRandomKey();

        // Generate settings content and write file
        $settings_constants = array(
            '_DB_SERVER_' => $database_server,
            '_DB_NAME_' => $database_name,
            '_DB_USER_' => $database_login,
            '_DB_PASSWD_' => $database_password,
            '_DB_PREFIX_' => $database_prefix,
            '_MYSQL_ENGINE_' => $database_engine,
            '_PS_CACHING_SYSTEM_' => 'CacheMemcache',
            '_PS_CACHE_ENABLED_' => '0',
            '_COOKIE_KEY_' => Tools::passwdGen(56),
            '_COOKIE_IV_' => Tools::passwdGen(8),
            '_NEW_COOKIE_KEY_' => $key->saveToAsciiSafeString(),
            '_PS_CREATION_DATE_' => date('Y-m-d'),
            '_PS_VERSION_' => _PS_INSTALL_VERSION_,
            '_QLOAPPS_VERSION_' => _QLO_INSTALL_VERSION_,
        );

        $settings_content = "<?php\n";

        foreach ($settings_constants as $constant => $value) {
            if ($constant == '_PS_VERSION_') {
                $settings_content .= 'if (!defined(\''.$constant.'\'))'."\n\t";
            }

            $settings_content .= "define('$constant', '".str_replace('\'', '\\\'', $value)."');\n";
        }

        if (!file_put_contents(_PS_ROOT_DIR_.'/'.self::SETTINGS_FILE, $settings_content)) {
            $this->setError($this->language->l('Cannot write settings file'));
            return false;
        }
        return true;
    }

    /**
     * PROCESS : installDatabase
     * Generate settings file and create database structure
     */
    public function installDatabase($clear_database = false)
    {
        // Clear database (only tables with same prefix)
        require_once _PS_ROOT_DIR_.'/'.self::SETTINGS_FILE;
        if ($clear_database) {
            $this->clearDatabase();
        }

        $allowed_collation = array('utf8_general_ci', 'utf8_unicode_ci');
        $collation_database = Db::getInstance()->getValue('SELECT @@collation_database');
        // Install database structure
        $sql_loader = new InstallSqlLoader();
        $sql_loader->setMetaData(array(
            'PREFIX_' => _DB_PREFIX_,
            'ENGINE_TYPE' => _MYSQL_ENGINE_,
            'COLLATION' => (empty($collation_database) || !in_array($collation_database, $allowed_collation)) ? '' : 'COLLATE '.$collation_database
        ));

        try {
            $sql_loader->parse_file(_PS_INSTALL_DATA_PATH_.'db_structure.sql');
        } catch (PrestashopInstallerException $e) {
            $this->setError($this->language->l('Database structure file not found'));
            return false;
        }

        if ($errors = $sql_loader->getErrors()) {
            foreach ($errors as $error) {
                $this->setError($this->language->l('SQL error on query <i>%s</i>', $error['error']));
            }
            return false;
        }

        return true;
    }

    /**
     * Clear database (only tables with same prefix)
     *
     * @param bool $truncate If true truncate the table, if false drop the table
     */
    public function clearDatabase($truncate = false)
    {
        foreach (Db::getInstance()->executeS('SHOW TABLES') as $row) {
            $table = current($row);
            if (!_DB_PREFIX_ || preg_match('#^'._DB_PREFIX_.'#i', $table)) {
                Db::getInstance()->execute((($truncate) ? 'TRUNCATE' : 'DROP TABLE').' `'.$table.'`');
            }
        }
    }

    /**
     * PROCESS : installDefaultData
     * Create default shop and languages
     */
    public function installDefaultData($shop_name, $iso_country = false, $all_languages = false, $clear_database = false)
    {
        if ($clear_database) {
            $this->clearDatabase(true);
        }

        // Install first shop
        if (!$this->createShop($shop_name)) {
            return false;
        }

        // Install languages
        try {
            if (!$all_languages) {
                $iso_codes_to_install = array($this->language->getLanguageIso());
                if ($iso_country) {
                    $version = str_replace('.', '', _PS_VERSION_);
                    $version = substr($version, 0, 2);
                    $localization_file_content = $this->getLocalizationPackContent($version, $iso_country);

                    if ($xml = @simplexml_load_string($localization_file_content)) {
                        foreach ($xml->languages->language as $language) {
                            $iso_codes_to_install[] = (string)$language->attributes()->iso_code;
                        }
                    }
                }
            } else {
                $iso_codes_to_install = null;
            }
            $iso_codes_to_install = array_flip(array_flip($iso_codes_to_install));
            $languages = $this->installLanguages($iso_codes_to_install);
        } catch (PrestashopInstallerException $e) {
            $this->setError($e->getMessage());
            return false;
        }

        $flip_languages = array_flip($languages);
        $id_lang = (!empty($flip_languages[$this->language->getLanguageIso()])) ? $flip_languages[$this->language->getLanguageIso()] : 1;
        Configuration::updateGlobalValue('PS_LANG_DEFAULT', $id_lang);
        Configuration::updateGlobalValue('PS_VERSION_DB', _PS_INSTALL_VERSION_);
        Configuration::updateGlobalValue('PS_INSTALL_VERSION', _PS_INSTALL_VERSION_);
        return true;
    }

    /**
     * PROCESS : populateDatabase
     * Populate database with default data
     */
    public function populateDatabase($entity = null)
    {
        $languages = array();
        foreach (Language::getLanguages(true) as $lang) {
            $languages[$lang['id_lang']] = $lang['iso_code'];
        }

        // Install XML data (data/xml/ folder)
        $xml_loader = new InstallXmlLoader();
        $xml_loader->setLanguages($languages);

        if (isset($this->xml_loader_ids) && $this->xml_loader_ids) {
            $xml_loader->setIds($this->xml_loader_ids);
        }

        if ($entity) {
            $xml_loader->populateEntity($entity);
        } else {
            $xml_loader->populateFromXmlFiles();
        }
        if ($errors = $xml_loader->getErrors()) {
            $this->setError($errors);
            return false;
        }

        // IDS from xmlLoader are stored in order to use them for fixtures
        $this->xml_loader_ids = $xml_loader->getIds();
        unset($xml_loader);

        // Install custom SQL data (db_data.sql file)
        if (file_exists(_PS_INSTALL_DATA_PATH_.'db_data.sql')) {
            $sql_loader = new InstallSqlLoader();
            $sql_loader->setMetaData(array(
                'PREFIX_' => _DB_PREFIX_,
                'ENGINE_TYPE' => _MYSQL_ENGINE_,
            ));

            $sql_loader->parse_file(_PS_INSTALL_DATA_PATH_.'db_data.sql', false);
            if ($errors = $sql_loader->getErrors()) {
                $this->setError($errors);
                return false;
            }
        }

        // Copy language default images (we do this action after database in populated because we need image types information)
        foreach ($languages as $iso) {
            $this->copyLanguageImages($iso);
        }

        return true;
    }

    public function createShop($shop_name)
    {
        // Create default group shop
        $shop_group = new ShopGroup();
        $shop_group->name = 'Default';
        $shop_group->active = true;
        if (!$shop_group->add()) {
            $this->setError($this->language->l('Cannot create group shop').' / '.Db::getInstance()->getMsgError());
            return false;
        }

        // Create default shop
        $shop = new Shop();
        $shop->active = true;
        $shop->id_shop_group = $shop_group->id;
        $shop->id_category = 2;
        $shop->id_theme = 1;
        $shop->name = $shop_name;
        if (!$shop->add()) {
            $this->setError($this->language->l('Cannot create shop').' / '.Db::getInstance()->getMsgError());
            return false;
        }
        Context::getContext()->shop = $shop;

        // Create default shop URL
        $shop_url = new ShopUrl();
        $shop_url->domain = Tools::getHttpHost();
        $shop_url->domain_ssl = Tools::getHttpHost();
        $shop_url->physical_uri = __PS_BASE_URI__;
        $shop_url->id_shop = $shop->id;
        $shop_url->main = true;
        $shop_url->active = true;
        if (!$shop_url->add()) {
            $this->setError($this->language->l('Cannot create shop URL').' / '.Db::getInstance()->getMsgError());
            return false;
        }

        return true;
    }

    /**
     * Install languages
     *
     * @return array Association between ID and iso array(id_lang => iso, ...)
     */
    public function installLanguages($languages_list = null)
    {
        if ($languages_list == null || !is_array($languages_list) || !count($languages_list)) {
            $languages_list = $this->language->getIsoList();
        }

        $languages_available = $this->language->getIsoList();
        $languages = array();
        foreach ($languages_list as $iso) {
            if (!in_array($iso, $languages_available)) {
                continue;
            }
            if (!file_exists(_PS_INSTALL_LANGS_PATH_.$iso.'/language.xml')) {
                throw new PrestashopInstallerException($this->language->l('File "language.xml" not found for language iso "%s"', $iso));
            }

            if (!$xml = @simplexml_load_file(_PS_INSTALL_LANGS_PATH_.$iso.'/language.xml')) {
                throw new PrestashopInstallerException($this->language->l('File "language.xml" not valid for language iso "%s"', $iso));
            }

            $params_lang = array(
                'name' => (string)$xml->name,
                'iso_code' => substr((string)$xml->language_code, 0, 2),
                'allow_accented_chars_url' => (string)$xml->allow_accented_chars_url
            );

            if (InstallSession::getInstance()->safe_mode) {
                Language::checkAndAddLanguage($iso, false, true, $params_lang);
            } else {
                Language::downloadAndInstallLanguagePack($iso, _PS_INSTALL_VERSION_, $params_lang);
            }

            Language::loadLanguages();
            Tools::clearCache();
            if (!$id_lang = Language::getIdByIso($iso, true)) {
                throw new PrestashopInstallerException($this->language->l('Cannot install language "%s"', ($xml->name) ? $xml->name : $iso));
            }
            $languages[$id_lang] = $iso;

            // Copy language flag
            if (is_writable(_PS_IMG_DIR_.'l/')) {
                if (!copy(_PS_INSTALL_LANGS_PATH_.$iso.'/flag.jpg', _PS_IMG_DIR_.'l/'.$id_lang.'.jpg')) {
                    throw new PrestashopInstallerException($this->language->l('Cannot copy flag language "%s"', _PS_INSTALL_LANGS_PATH_.$iso.'/flag.jpg => '._PS_IMG_DIR_.'l/'.$id_lang.'.jpg'));
                }
            }
        }

        return $languages;
    }

    public function copyLanguageImages($iso)
    {
        $img_path = _PS_INSTALL_LANGS_PATH_.$iso.'/img/';
        if (!is_dir($img_path)) {
            return;
        }

        $list = array(
            'products' => _PS_PROD_IMG_DIR_,
            'categories' => _PS_CAT_IMG_DIR_,
            'manufacturers' => _PS_MANU_IMG_DIR_,
            'suppliers' => _PS_SUPP_IMG_DIR_,
            'scenes' => _PS_SCENE_IMG_DIR_,
            'stores' => _PS_STORE_IMG_DIR_,
            null => _PS_IMG_DIR_.'l/', // Little trick to copy images in img/l/ path with all types
        );

        foreach ($list as $cat => $dst_path) {
            if (!is_writable($dst_path)) {
                continue;
            }

            copy($img_path.$iso.'.jpg', $dst_path.$iso.'.jpg');

            $types = ImageType::getImagesTypes($cat);
            foreach ($types as $type) {
                if (file_exists($img_path.$iso.'-default-'.$type['name'].'.jpg')) {
                    copy($img_path.$iso.'-default-'.$type['name'].'.jpg', $dst_path.$iso.'-default-'.$type['name'].'.jpg');
                } else {
                    ImageManager::resize($img_path.$iso.'.jpg', $dst_path.$iso.'-default-'.$type['name'].'.jpg', $type['width'], $type['height']);
                }
            }
        }
    }

    private static $_cache_localization_pack_content = null;
    public function getLocalizationPackContent($version, $country)
    {
        if (InstallModelInstall::$_cache_localization_pack_content === null || array_key_exists($country, InstallModelInstall::$_cache_localization_pack_content)) {
            $path_cache_file = _PS_CACHE_DIR_.'sandbox'.DIRECTORY_SEPARATOR.$version.$country.'.xml';
            if (is_file($path_cache_file)) {
                $localization_file_content = file_get_contents($path_cache_file);
            } else {
                $localization_file_content = @Tools::file_get_contents('http://api.prestashop.com/localization/'.$version.'/'.$country.'.xml');
                if (!@simplexml_load_string($localization_file_content)) {
                    $localization_file_content = false;
                }
                if (!$localization_file_content) {
                    $localization_file = _PS_ROOT_DIR_.'/localization/default.xml';
                    if (file_exists(_PS_ROOT_DIR_.'/localization/'.$country.'.xml')) {
                        $localization_file = _PS_ROOT_DIR_.'/localization/'.$country.'.xml';
                    }

                    $localization_file_content = file_get_contents($localization_file);
                }
                file_put_contents($path_cache_file, $localization_file_content);
            }
            InstallModelInstall::$_cache_localization_pack_content[$country] = $localization_file_content;
        }

        return isset(InstallModelInstall::$_cache_localization_pack_content[$country]) ? InstallModelInstall::$_cache_localization_pack_content[$country] : false;
    }

    /**
     * PROCESS : configureShop
     * Set default shop configuration
     */
    public function configureShop(array $data = array())
    {
        //clear image cache in tmp folder
        if (file_exists(_PS_TMP_IMG_DIR_)) {
            foreach (scandir(_PS_TMP_IMG_DIR_) as $file) {
                if ($file[0] != '.' && $file != 'index.php') {
                    Tools::deleteFile(_PS_TMP_IMG_DIR_.$file);
                }
            }
        }

        $default_data = array(
            'shop_name' => 'My Shop',
            'shop_activity' => '',
            'shop_country' => 'us',
            'shop_timezone' => 'US/Eastern',
            'enable_ssl' => false,
            'use_smtp' => false,
            'smtp_encryption' => 'off',
            'smtp_port' => 25,
            'rewrite_engine' => false,
        );

        foreach ($default_data as $k => $v) {
            if (!isset($data[$k])) {
                $data[$k] = $v;
            }
        }

        Context::getContext()->shop = new Shop(1);
        Configuration::loadConfiguration();

        // use the old image system if the safe_mod is enabled otherwise the installer will fail with the fixtures installation
        if (InstallSession::getInstance()->safe_mode) {
            Configuration::updateGlobalValue('PS_LEGACY_IMAGES', 1);
        }

        $id_country = (int)Country::getByIso($data['shop_country']);

        // Set default configuration
        Configuration::updateGlobalValue('PS_SHOP_DOMAIN',                Tools::getHttpHost());
        Configuration::updateGlobalValue('PS_SHOP_DOMAIN_SSL',            Tools::getHttpHost());
        Configuration::updateGlobalValue('PS_INSTALL_VERSION',            _PS_INSTALL_VERSION_);
        Configuration::updateGlobalValue('PS_LOCALE_LANGUAGE',            $this->language->getLanguageIso());
        Configuration::updateGlobalValue('PS_SHOP_NAME',                $data['shop_name']);
        Configuration::updateGlobalValue('PS_SHOP_ACTIVITY',                $data['shop_activity']);
        Configuration::updateGlobalValue('PS_COUNTRY_DEFAULT',            $id_country);
        Configuration::updateGlobalValue('PS_LOCALE_COUNTRY',            $data['shop_country']);
        Configuration::updateGlobalValue('PS_TIMEZONE',                $data['shop_timezone']);
        Configuration::updateGlobalValue('PS_CONFIGURATION_AGREMENT',        (int)$data['configuration_agrement']);

        // Set SSL configuration
        Configuration::updateGlobalValue('PS_SSL_ENABLED', (int) $data['enable_ssl']);
        Configuration::updateGlobalValue('PS_SSL_ENABLED_EVERYWHERE', (int) $data['enable_ssl']);

        // Set mails configuration
        Configuration::updateGlobalValue('PS_MAIL_METHOD',            ($data['use_smtp']) ? 2 : 1);
        Configuration::updateGlobalValue('PS_MAIL_SMTP_ENCRYPTION',    $data['smtp_encryption']);
        Configuration::updateGlobalValue('PS_MAIL_SMTP_PORT',        $data['smtp_port']);

        // Set default rewriting settings
        Configuration::updateGlobalValue('PS_REWRITING_SETTINGS', $data['rewrite_engine']);

        // Activate rijndael 128 encrypt algorihtm if mcrypt is activated
        Configuration::updateGlobalValue('PS_CIPHER_ALGORITHM', function_exists('mcrypt_encrypt') ? 1 : 0);

        $groups = Group::getGroups((int)Configuration::get('PS_LANG_DEFAULT'));
        $groups_default = Db::getInstance()->executeS('SELECT `name` FROM '._DB_PREFIX_.'configuration WHERE `name` LIKE "PS_%_GROUP" ORDER BY `id_configuration`');
        foreach ($groups_default as &$group_default) {
            if (is_array($group_default) && isset($group_default['name'])) {
                $group_default = $group_default['name'];
            }
        }

        if (is_array($groups) && count($groups)) {
            foreach ($groups as $key => $group) {
                if (Configuration::get($groups_default[$key]) != $groups[$key]['id_group']) {
                    Configuration::updateGlobalValue($groups_default[$key], (int)$groups[$key]['id_group']);
                }
            }
        }

        $states = Db::getInstance()->executeS('SELECT `id_order_state` FROM '._DB_PREFIX_.'order_state ORDER by `id_order_state`');
        $states_default = Db::getInstance()->executeS('SELECT MIN(`id_configuration`), `name` FROM '._DB_PREFIX_.'configuration WHERE `name` LIKE "PS_OS_%" GROUP BY `value` ORDER BY`id_configuration`');

        foreach ($states_default as &$state_default) {
            if (is_array($state_default) && isset($state_default['name'])) {
                $state_default = $state_default['name'];
            }
        }

        if (is_array($states) && count($states)) {
            foreach ($states as $key => $state) {
                if (Configuration::get($states_default[$key]) != $states[$key]['id_order_state']) {
                    Configuration::updateGlobalValue($states_default[$key], (int)$states[$key]['id_order_state']);
                }
            }
            /* deprecated order state */
            Configuration::updateGlobalValue('PS_OS_OUTOFSTOCK_PAID', (int)Configuration::get('PS_OS_OUTOFSTOCK'));
        }

        // Set logo configuration
        if (file_exists(_PS_IMG_DIR_.'logo.png')) {
            list($width, $height) = getimagesize(_PS_IMG_DIR_.'logo.png');
            Configuration::updateGlobalValue('SHOP_LOGO_WIDTH', round($width));
            Configuration::updateGlobalValue('SHOP_LOGO_HEIGHT', round($height));
        }

        // Disable cache for debug mode
        if (_PS_MODE_DEV_) {
            Configuration::updateGlobalValue('PS_SMARTY_CACHE', 1);
        }

        // Active only the country selected by the merchant
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'country SET active = 0 WHERE id_country != '.(int)$id_country);

        // Set localization configuration
        $version = str_replace('.', '', _PS_VERSION_);
        $version = substr($version, 0, 2);
        $localization_file_content = $this->getLocalizationPackContent($version, $data['shop_country']);

        $locale = new LocalizationPackCore();
        $locale->loadLocalisationPack($localization_file_content, '', true);

        // Create default employee
        if (isset($data['admin_firstname']) && isset($data['admin_lastname']) && isset($data['admin_password']) && isset($data['admin_email'])) {
            $employee = new Employee();
            $employee->firstname = Tools::ucfirst($data['admin_firstname']);
            $employee->lastname = Tools::ucfirst($data['admin_lastname']);
            $employee->email = $data['admin_email'];
            $employee->passwd = md5(_COOKIE_KEY_.$data['admin_password']);
            $employee->last_passwd_gen = date('Y-m-d h:i:s', strtotime('-360 minutes'));
            $employee->bo_theme = 'default';
            $employee->default_tab = 1;
            $employee->active = true;
            $employee->optin = true;
            $employee->id_profile = 1;
            $employee->id_lang = Configuration::get('PS_LANG_DEFAULT');
            $employee->bo_menu = 0;
            if (!$employee->add()) {
                $this->setError($this->language->l('Cannot create admin account'));
                return false;
            }
        } else {
            $this->setError($this->language->l('Cannot create admin account'));
            return false;
        }

        // Update default contact
        if (isset($data['admin_email'])) {
            Configuration::updateGlobalValue('PS_SHOP_EMAIL', $data['admin_email']);

            $contacts = new PrestaShopCollection('Contact');
            foreach ($contacts as $contact) {
                $contact->email = $data['admin_email'];
                $contact->update();
            }
        }

        if (!@Tools::generateHtaccess(null, $data['rewrite_engine'])) {
            Configuration::updateGlobalValue('PS_REWRITING_SETTINGS', 0);
        }

        $qloData = [
            'shopDomain' => Tools::getHttpHost(),
            'shopDomainSsl' => Tools::getHttpHost(),
            'physicalUri' => __PS_BASE_URI__,
            'email' => $data['admin_email'],
            'firstname' => Tools::ucfirst($data['admin_firstname']),
            'lastname' => Tools::ucfirst($data['admin_lastname']),
            'countryCode' => $data['shop_country'],
            'marketingConsent' => $data['marketing_consent'],
        ];

        // Qlo Notification
        $this->setNotification($qloData);

        return true;
    }

    public function setNotification($notificationData)
    {
        if (function_exists('curl_version')) {
            $params = [
                'url' => 'https://prestashop.webkul.com/hotel-reservation-clients/getNotification.php',
                'method' => 'POST',
                'headers' => array('Content-Type: application/json'),
                'postdata' => json_encode($notificationData),
            ];

            $curlInit = curl_init();
            curl_setopt($curlInit, CURLOPT_URL, $params['url']);
            curl_setopt($curlInit, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($curlInit, CURLOPT_HTTPHEADER, $params['headers']);
            curl_setopt($curlInit, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curlInit, CURLOPT_CUSTOMREQUEST, $params['method']);
            curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, 1);
            if (isset($params['postdata'])) {
                curl_setopt($curlInit, CURLOPT_POSTFIELDS, $params['postdata']);
            }
            $response = curl_exec($curlInit);
        } else {
            // Mail Code
        }

        return true;
    }

    public function getModulesList()
    {
        $modules = array();
        if (false) {
            foreach (scandir(_PS_MODULE_DIR_) as $module) {
                if ($module[0] != '.' && is_dir(_PS_MODULE_DIR_.$module) && file_exists(_PS_MODULE_DIR_.$module.'/'.$module.'.php')) {
                    $modules[] = $module;
                }
            }
        } else {
            $modules = array(
                'hotelreservationsystem',
                'wkroomsearchblock',
                'blocknewsletter',
                'blocksocial',
                'wkfooterblock',
                'wkhotelfilterblock',
                'wkabouthotelblock',
                'wkhotelfeaturesblock',
                'wkhotelroom',
                'wktestimonialblock',
                'bankwire',
                'cheque',
                'blockmyaccount',
                'qloblockcontact',
                'blockcurrencies',
                'blocklanguages',
                'qlohotelreview',
                'wkfooterlangcurrencyblock',
                'wkfooterpaymentinfoblockcontainer',
                'wkfooteraboutblock',
                'wkfooterpaymentblock',
                'wkfooternotificationblock',
                'blocknavigationmenu',
                'dashguestcycle',
                'dashoccupancy',
                'dashavailability',
                'dashproducts',
                'dashtrends',
                'dashperformance',
                'dashgoals',
                'dashactivity',
                'graphnvd3',
                'statsdata',
                'statsvisits',
                'statsorigin',
                'statslive',
                'sekeywords',
                'statssales',
                'statspersonalinfos',
                'statsforecast',
                'statsbestcategories',
                'statsproduct',
                'statscheckup',
                'statscatalog',
                'statsbestproducts',
                'pagesnotfound',
                'statsnewsletter',
                'statsregistrations',
                'statsbestvouchers',
                'statsbestcustomers',
                'statsequipment',
                'blockcart',
                'blockuserinfo',
            );
        }
        return $modules;
    }

    public function getAddonsModulesList($params = array())
    {
        $addons_modules = array();
        return $addons_modules;
        $content = Tools::addonsRequest('install-modules', $params);
        $xml = @simplexml_load_string($content, null, LIBXML_NOCDATA);

        if ($xml !== false and isset($xml->module)) {
            foreach ($xml->module as $modaddons) {
                $addons_modules[] = array('id_module' => $modaddons->id, 'name' => $modaddons->name);
            }
        }

        return $addons_modules;
    }

    /**
     * PROCESS : installModules
     * Download module from addons and Install all modules in ~/modules/ directory
     */
    public function installModulesAddons($module = null)
    {
        $addons_modules = $module ? array($module) : $this->getAddonsModulesList();
        $modules = array();
        if (!InstallSession::getInstance()->safe_mode) {
            foreach ($addons_modules as $addons_module) {
                if (file_put_contents(_PS_MODULE_DIR_.$addons_module['name'].'.zip', Tools::addonsRequest('module', array('id_module' => $addons_module['id_module'])))) {
                    if (Tools::ZipExtract(_PS_MODULE_DIR_.$addons_module['name'].'.zip', _PS_MODULE_DIR_)) {
                        $modules[] = (string)$addons_module['name'];//if the module has been unziped we add the name in the modules list to install
                        unlink(_PS_MODULE_DIR_.$addons_module['name'].'.zip');
                    }
                }
            }
        }

        return count($modules) ? $this->installModules($modules) : true;
    }

    /**
     * PROCESS : installModules
     * Download module from addons and Install all modules in ~/modules/ directory.
     * $populateData - extra parameter sent to populate module data or not
     */
    public function installModules($module = null, $populateData = 1)
    {
        if ($module && !is_array($module)) {
            $module = array($module);
        }

        $modules = $module ? $module : $this->getModulesList();

        Module::updateTranslationsAfterInstall(false);

        $errors = array();
        foreach ($modules as $module_name) {
            if (!file_exists(_PS_MODULE_DIR_.$module_name.'/'.$module_name.'.php')) {
                continue;
            }

            $module = Module::getInstanceByName($module_name);
            $module->populateData = $populateData;
            if (!$module->install()) {
                $errors[] = $this->language->l('Cannot install module "%s"', $module_name);
            }
        }

        if ($errors) {
            $this->setError($errors);
            return false;
        }

        Module::updateTranslationsAfterInstall(true);
        Language::updateModulesTranslations($modules);

        return true;
    }

    /**
     * PROCESS : installFixtures
     * Install fixtures (E.g. demo products)
     */
    public function installFixtures($entity = null, array $data = array())
    {
        $fixtures_path = _PS_INSTALL_FIXTURES_PATH_.'fashion/';
        $fixtures_name = 'fashion';
        $zip_file = _PS_ROOT_DIR_.'/download/fixtures.zip';
        $temp_dir = _PS_ROOT_DIR_.'/download/fixtures/';

        // try to download fixtures if no low memory mode
        if ($entity === null) {
            if (Tools::copy('http://api.prestashop.com/fixtures/'.$data['shop_country'].'/'.$data['shop_activity'].'/fixtures.zip', $zip_file)) {
                Tools::deleteDirectory($temp_dir, true);
                if (Tools::ZipTest($zip_file)) {
                    if (Tools::ZipExtract($zip_file, $temp_dir)) {
                        $files = scandir($temp_dir);
                        if (count($files)) {
                            foreach ($files as $file) {
                                if (!preg_match('/^\./', $file) && is_dir($temp_dir.$file.'/')) {
                                    $fixtures_path = $temp_dir.$file.'/';
                                    $fixtures_name = $file;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        // Load class (use fixture class if one exists, or use InstallXmlLoader)
        if (file_exists($fixtures_path.'/install.php')) {
            require_once $fixtures_path.'/install.php';
            $class = 'InstallFixtures'.Tools::toCamelCase($fixtures_name);
            if (!class_exists($class, false)) {
                $this->setError($this->language->l('Fixtures class "%s" not found', $class));
                return false;
            }

            $xml_loader = new $class();
            if (!$xml_loader instanceof InstallXmlLoader) {
                $this->setError($this->language->l('"%s" must be an instance of "InstallXmlLoader"', $class));
                return false;
            }
        } else {
            $xml_loader = new InstallXmlLoader();
        }

        // Install XML data (data/xml/ folder)
        $xml_loader->setFixturesPath($fixtures_path);
        if (isset($this->xml_loader_ids) && $this->xml_loader_ids) {
            $xml_loader->setIds($this->xml_loader_ids);
        }

        $languages = array();
        foreach (Language::getLanguages(false) as $lang) {
            $languages[$lang['id_lang']] = $lang['iso_code'];
        }
        $xml_loader->setLanguages($languages);

        if ($entity) {
            $xml_loader->populateEntity($entity);
        } else {
            $xml_loader->populateFromXmlFiles();
            Tools::deleteDirectory($temp_dir, true);
            @unlink($zip_file);
        }

        if ($errors = $xml_loader->getErrors()) {
            $this->setError($errors);
            return false;
        }

        // IDS from xmlLoader are stored in order to use them for fixtures
        $this->xml_loader_ids = $xml_loader->getIds();
        unset($xml_loader);

        // Index products in search tables
        Search::indexation(true);

        return true;
    }

    /**
     * PROCESS : installTheme
     * Install theme
     */
    public function installTheme()
    {
        // @todo do a real install of the theme
        $sql_loader = new InstallSqlLoader();
        $sql_loader->setMetaData(array(
            'PREFIX_' => _DB_PREFIX_,
            'ENGINE_TYPE' => _MYSQL_ENGINE_,
        ));

        $sql_loader->parse_file(_PS_INSTALL_DATA_PATH_.'theme.sql', false);
        if ($errors = $sql_loader->getErrors()) {
            $this->setError($errors);
            return false;
        }
    }
}
