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

class AdminModulesControllerCore extends AdminController
{
    private $_modules_ad = array(
        'blockcart' => array('cartabandonmentpro'),
        /* 'bloctopmenu' => array('advancedtopmenu'), */
    );
    /*
    ** @var array map with $_GET keywords and their callback
    */
    protected $map = array(
        'check' => 'check',
        'install' => 'install',
        'uninstall' => 'uninstall',
        'configure' => 'getContent',
        'update' => 'update',
        'delete' => 'delete',
        'checkAndUpdate' => 'checkAndUpdate',
        'updateAll' => 'updateAll'
    );

    protected $list_modules_categories = array();
    protected $list_partners_modules = array();
    protected $list_natives_modules = array();

    protected $nb_modules_total = 0;
    protected $nb_modules_installed = 0;
    protected $nb_modules_activated = 0;

    protected $serial_modules = '';
    protected $modules_authors = array();

    protected $id_employee;
    protected $iso_default_country;
    protected $filter_configuration = array();

    /**
     * Admin Modules Controller Constructor
     * Init list modules categories
     * Load id employee
     * Load filter configuration
     * Load cache file
     */

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        register_shutdown_function('displayFatalError');

        // Set the modules categories
        $this->list_modules_categories['administration']['name'] = $this->l('Administration');
        $this->list_modules_categories['advertising_marketing']['name'] = $this->l('Advertising and Marketing');
        $this->list_modules_categories['analytics_stats']['name'] = $this->l('Analytics and Stats');
        $this->list_modules_categories['billing_invoicing']['name'] = $this->l('Taxes & Invoicing');
        $this->list_modules_categories['checkout']['name'] = $this->l('Checkout');
        $this->list_modules_categories['content_management']['name'] = $this->l('Content Management');
        $this->list_modules_categories['customer_reviews']['name'] = $this->l('Customer Reviews');
        $this->list_modules_categories['export']['name'] = $this->l('Export');
        $this->list_modules_categories['emailing']['name'] = $this->l('Emailing');
        $this->list_modules_categories['front_office_features']['name'] = $this->l('Front office Features');
        $this->list_modules_categories['i18n_localization']['name'] = $this->l('Internationalization and Localization');
        $this->list_modules_categories['merchandizing']['name'] = $this->l('Merchandising');
        $this->list_modules_categories['migration_tools']['name'] = $this->l('Migration Tools');
        $this->list_modules_categories['payments_gateways']['name'] = $this->l('Payments and Gateways');
        $this->list_modules_categories['payment_security']['name'] = $this->l('Site certification & Fraud prevention');
        $this->list_modules_categories['pricing_promotion']['name'] = $this->l('Pricing and Promotion');
        $this->list_modules_categories['quick_bulk_update']['name'] = $this->l('Quick / Bulk update');
/* 		$this->list_modules_categories['search_filter']['name'] = $this->l('Search and Filter'); */
        $this->list_modules_categories['seo']['name'] = $this->l('SEO');
        $this->list_modules_categories['shipping_logistics']['name'] = $this->l('Shipping and Logistics');
        $this->list_modules_categories['slideshows']['name'] = $this->l('Slideshows');
        $this->list_modules_categories['smart_shopping']['name'] = $this->l('Comparison site & Feed management');
        $this->list_modules_categories['market_place']['name'] = $this->l('Marketplace');
        $this->list_modules_categories['others']['name'] = $this->l('Other Modules');
        $this->list_modules_categories['mobile']['name'] = $this->l('Mobile');
        $this->list_modules_categories['dashboard']['name'] = $this->l('Dashboard');
        $this->list_modules_categories['i18n_localization']['name'] = $this->l('Internationalization & Localization');
        $this->list_modules_categories['emailing']['name'] = $this->l('Emailing & SMS');
        $this->list_modules_categories['social_networks']['name'] = $this->l('Social Networks');
        $this->list_modules_categories['social_community']['name'] = $this->l('Social & Community');

        uasort($this->list_modules_categories, array($this, 'checkCategoriesNames'));

        // Set Id Employee, Iso Default Country and Filter Configuration
        $this->id_employee = (int)$this->context->employee->id;
        $this->iso_default_country = $this->context->country->iso_code;
        $this->filter_configuration = Configuration::getMultiple(array(
            'PS_SHOW_TYPE_MODULES_'.(int)$this->id_employee,
            'PS_SHOW_COUNTRY_MODULES_'.(int)$this->id_employee,
            'PS_SHOW_INSTALLED_MODULES_'.(int)$this->id_employee,
            'PS_SHOW_ENABLED_MODULES_'.(int)$this->id_employee,
            'PS_SHOW_CAT_MODULES_'.(int)$this->id_employee,
        ));

        // Load cache file modules list (natives and partners modules)
        $xml_modules = false;
        if (file_exists(_PS_ROOT_DIR_.Module::CACHE_FILE_MODULES_LIST)) {
            $xml_modules = @simplexml_load_file(_PS_ROOT_DIR_.Module::CACHE_FILE_MODULES_LIST);
        }
        if ($xml_modules) {
            foreach ($xml_modules->children() as $xml_module) {
                /** @var SimpleXMLElement $xml_module */
                foreach ($xml_module->children() as $module) {
                    /** @var SimpleXMLElement $module */
                    foreach ($module->attributes() as $key => $value) {
                        if (($xml_module->attributes() == 'native' || $xml_module->attributes() == 'disk')
                            && $key == 'name'
                        ) {
                            $this->list_natives_modules[] = (string)$value;
                        }
                        if ($xml_module->attributes() == 'partner' && $key == 'name') {
                            $this->list_partners_modules[] = (string)$value;
                        }
                    }
                }
            }
        }
    }

    public function checkCategoriesNames($a, $b)
    {
        if ($a['name'] === $this->l('Other Modules')) {
            return true;
        }

        return (bool)($a['name'] > $b['name']);
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryPlugin(array('autocomplete', 'fancybox', 'tablefilter'));

        Media::addjsDef(array(
            'remove_uploaded_module_txt' => $this->l('Do you want to remove the uploaded/installed module?'),
            'module_install_error_txt' => $this->l('There was an error while installing module.'),
        ));
        $this->addJS(_PS_JS_DIR_.'admin/modules.js');

         if ($this->context->mode == Context::MODE_HOST && Tools::isSubmit('addnewmodule')) {
            $this->addJS(_PS_JS_DIR_.'admin/addons.js');
        }
    }

    public function ajaxProcessRefreshModuleList($force_reload_cache = false)
    {
        // Refresh modules_list.xml every week
        if (!Tools::isFresh(Module::CACHE_FILE_MODULES_LIST, _TIME_1_DAY_) || $force_reload_cache) {
            $xml_modules_list = _QLO_API_DOMAIN_.'/xml/'.str_replace('.', '', _QLOAPPS_VERSION_).'.xml';
            if (Tools::refresh(Module::CACHE_FILE_MODULES_LIST, 'https://'.$xml_modules_list)) {
                $this->status = 'refresh';
            } elseif (Tools::refresh(Module::CACHE_FILE_MODULES_LIST, 'http://'.$xml_modules_list)) {
                $this->status = 'refresh';
            } else {
                $this->status = 'error';
            }
        } else {
            $this->status = 'cache';
        }

        // If logged to Addons Webservices, refresh default country native modules list every day
        if ($this->status != 'error') {
            if (!Tools::isFresh(Module::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST, _TIME_1_DAY_) || $force_reload_cache) {
                if (file_put_contents(_PS_ROOT_DIR_.Module::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST, Tools::addonsRequest('native'))) {
                    $this->status = 'refresh';
                } else {
                    $this->status = 'error';
                }
            } else {
                $this->status = 'cache';
            }

            if (!Tools::isFresh(Module::CACHE_FILE_ADDONS_MODULES_LIST, _TIME_1_DAY_) || $force_reload_cache) {
                if (file_put_contents(_PS_ROOT_DIR_.Module::CACHE_FILE_ADDONS_MODULES_LIST, Tools::addonsRequest('addons-modules'))) {
                    $this->status = 'refresh';
                } else {
                    $this->status = 'error';
                }
            } else {
                $this->status = 'cache';
            }
        }

        // If logged to Addons Webservices, refresh customer modules list every 5 minutes
        if ($this->logged_on_addons && $this->status != 'error') {
            if (!Tools::isFresh(Module::CACHE_FILE_CUSTOMER_MODULES_LIST, 300) || $force_reload_cache) {
                if (file_put_contents(_PS_ROOT_DIR_.Module::CACHE_FILE_CUSTOMER_MODULES_LIST, Tools::addonsRequest('customer'))) {
                    $this->status = 'refresh';
                } else {
                    $this->status = 'error';
                }
            } else {
                $this->status = 'cache';
            }
        }
    }

    public function displayAjaxRefreshModuleList()
    {
        echo json_encode(array('status' => $this->status));
    }


    public function ajaxProcessLogOnAddonsWebservices()
    {
        $content = Tools::addonsRequest('check_customer', array('username_addons' => pSQL(trim(Tools::getValue('username_addons'))), 'password_addons' => pSQL(trim(Tools::getValue('password_addons')))));
        $xml = @simplexml_load_string($content, null, LIBXML_NOCDATA);
        if (!$xml) {
            die('KO');
        }
        $result = strtoupper((string)$xml->success);
        if (!in_array($result, array('OK', 'KO'))) {
            die('KO');
        }
        if ($result == 'OK') {
            Tools::clearXMLCache();
            Configuration::updateValue('PS_LOGGED_ON_ADDONS', 1);
            $this->context->cookie->username_addons = pSQL(trim(Tools::getValue('username_addons')));
            $this->context->cookie->password_addons = pSQL(trim(Tools::getValue('password_addons')));
            $this->context->cookie->is_contributor = (int)$xml->is_contributor;
            $this->context->cookie->write();
        }
        die($result);
    }

    public function ajaxProcessLogOutAddonsWebservices()
    {
        $this->context->cookie->username_addons = '';
        $this->context->cookie->password_addons = '';
        $this->context->cookie->is_contributor = 0;
        $this->context->cookie->write();
        die('OK');
    }

    public function ajaxProcessReloadModulesList()
    {
        if (Tools::getValue('filterCategory')) {
            Configuration::updateValue('PS_SHOW_CAT_MODULES_'.(int)$this->id_employee, Tools::getValue('filterCategory'));
        }
        if (Tools::getValue('unfilterCategory')) {
            Configuration::updateValue('PS_SHOW_CAT_MODULES_'.(int)$this->id_employee, '');
        }

        $this->initContent();
        $this->smartyOutputContent('controllers/modules/list.tpl');
        exit;
    }

    public function ajaxProcessGetTabModulesList()
    {
        // $tab_modules_list = Tools::getValue('tab_modules_list');
        $conrollerClass = Tools::getValue('controller_class');
        $controllerId = Tab::getIdFromClassName($conrollerClass);

        if (!Tab::isTabModuleListAvailable()) {
            $this->ajaxProcessRefreshModuleList();
        }

        if ($tab_modules_list = Tab::getTabModulesList($controllerId)) {
            $tab_modules_list = $this->filterTabModuleList($tab_modules_list);
        }

        $back = Tools::getValue('back_tab_modules_list');
        if ($back) {
            $back .= '&tab_modules_open=1';
        }
        $modules_list = array('installed' =>array(), 'not_installed' => array());
        if (count($tab_modules_list['slider_list'])) {
            $modules_list_unsort = $this->getModulesByInstallation($tab_modules_list['slider_list']);
        }

        $installed = $uninstalled = array();
        foreach ($tab_modules_list['slider_list'] as $key => $value) {
            $continue = 0;
            foreach ($modules_list_unsort['installed'] as $mod_in) {
                if ($mod_in->name == $value) {
                    $continue = 1;
                    $installed[] = $mod_in;
                }
            }
            if ($continue) {
                continue;
            }
            foreach ($modules_list_unsort['not_installed'] as $mod_in) {
                if ($mod_in->name == $value) {
                    $uninstalled[] = $mod_in;
                }
            }
        }

        $modules_list_sort = array(
            'installed' => $installed,
            'not_installed' => $uninstalled
            );

        $this->context->smarty->assign(array(
            'tab_modules_list' => $modules_list_sort,
            'admin_module_favorites_view' => $this->context->link->getAdminLink('AdminModules').'&select=favorites',
            'lang_iso' => $this->context->language->iso_code
        ));

        $this->smartyOutputContent('controllers/modules/tab_modules_list.tpl');
        exit;
    }

    public function ajaxProcessSetFilter()
    {
        $this->setFilterModules(Tools::getValue('module_type'), Tools::getValue('country_module_value'), Tools::getValue('module_install'), Tools::getValue('module_status'));
        die('OK');
    }

    public function ajaxProcessSaveFavoritePreferences()
    {
        $action = Tools::getValue('action_pref');
        $value = Tools::getValue('value_pref');
        $module = Tools::getValue('module_pref');
        $id_module_preference = (int)Db::getInstance()->getValue('SELECT `id_module_preference` FROM `'._DB_PREFIX_.'module_preference` WHERE `id_employee` = '.(int)$this->id_employee.' AND `module` = \''.pSQL($module).'\'');
        if ($id_module_preference > 0) {
            if ($action == 'i') {
                $update = array('interest' => ($value == '' ? null : (int)$value));
            }
            if ($action == 'f') {
                $update = array('favorite' => ($value == '' ? null : (int)$value));
            }
            Db::getInstance()->update('module_preference', $update, '`id_employee` = '.(int)$this->id_employee.' AND `module` = \''.pSQL($module).'\'', 0, true);
        } else {
            $insert = array('id_employee' => (int)$this->id_employee, 'module' => pSQL($module), 'interest' => null, 'favorite' => null);
            if ($action == 'i') {
                $insert['interest'] = ($value == '' ? null : (int)$value);
            }
            if ($action == 'f') {
                $insert['favorite'] = ($value == '' ? null : (int)$value);
            }
            Db::getInstance()->insert('module_preference', $insert, true);
        }
        die('OK');
    }

    public function ajaxProcessSaveTabModulePreferences()
    {
        $values = Tools::getValue('value_pref');
        $module = Tools::getValue('module_pref');
        if (Validate::isModuleName($module)) {
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'tab_module_preference` WHERE `id_employee` = '.(int)$this->id_employee.' AND `module` = \''.pSQL($module).'\'');
            if (is_array($values) && count($values)) {
                foreach ($values as $value) {
                    Db::getInstance()->execute('
                        INSERT INTO `'._DB_PREFIX_.'tab_module_preference` (`id_tab_module_preference`, `id_employee`, `id_tab`, `module`)
                        VALUES (NULL, '.(int)$this->id_employee.', '.(int)$value.', \''.pSQL($module).'\');');
                }
            }
        }
        die('OK');
    }

    /*
    ** Get current URL
    **
    ** @param array $remove List of keys to remove from URL
    ** @return string
    */
    protected function getCurrentUrl($remove = array())
    {
        $url = $_SERVER['REQUEST_URI'];
        if (!$remove) {
            return $url;
        }

        if (!is_array($remove)) {
            $remove = array($remove);
        }

        $url = preg_replace('#(?<=&|\?)('.implode('|', $remove).')=.*?(&|$)#i', '', $url);
        $len = strlen($url);
        if ($url[$len - 1] == '&') {
            $url = substr($url, 0, $len - 1);
        }
        return $url;
    }

    protected function extractArchive($file, $redirect = true)
    {
        $zip_folders = array();
        $tmp_folder = _PS_MODULE_DIR_.md5(time());

        $success = false;
        if (substr($file, -4) == '.zip') {
            if (Tools::ZipExtract($file, $tmp_folder)) {
                $zip_folders = scandir($tmp_folder);
                if (Tools::ZipExtract($file, _PS_MODULE_DIR_)) {
                    $success = true;
                }
            }
        } else {
            require_once _PS_TOOL_DIR_.'tar/Tar.php';
            $archive = new Archive_Tar($file);
            if ($archive->extract($tmp_folder)) {
                $zip_folders = scandir($tmp_folder);
                if ($archive->extract(_PS_MODULE_DIR_)) {
                    $success = true;
                }
            }
        }

        if (!$success) {
            $this->errors[] = Tools::displayError('There was an error while extracting the module (file may be corrupted).');
        } else {
            //check if it's a real module
            foreach ($zip_folders as $folder) {
                if (!in_array($folder, array('.', '..', '.svn', '.git', '__MACOSX')) && !Module::getInstanceByName($folder)) {
                    $this->errors[] = sprintf(Tools::displayError('The module %1$s that you uploaded is not a valid module.'), $folder);
                    $this->recursiveDeleteOnDisk(_PS_MODULE_DIR_.$folder);
                }
            }
        }

        @unlink($file);
        $this->recursiveDeleteOnDisk($tmp_folder);

        if ($success && $redirect) {
            Tools::redirectAdmin(self::$currentIndex.'&conf=8&anchor='.ucfirst($folder).'&token='.$this->token);
        }

        return $success;
    }

    protected function recursiveDeleteOnDisk($dir)
    {
        if (strpos(realpath($dir), realpath(_PS_MODULE_DIR_)) === false) {
            return;
        }
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (filetype($dir.'/'.$object) == 'dir') {
                        $this->recursiveDeleteOnDisk($dir.'/'.$object);
                    } else {
                        unlink($dir.'/'.$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    /*
    ** Filter Configuration Methods
    ** Set and reset filter configuration
    */

    protected function setFilterModules($module_type, $country_module_value, $module_install, $module_status)
    {
        Configuration::updateValue('PS_SHOW_TYPE_MODULES_'.(int)$this->id_employee, $module_type);
        Configuration::updateValue('PS_SHOW_COUNTRY_MODULES_'.(int)$this->id_employee, $country_module_value);
        Configuration::updateValue('PS_SHOW_INSTALLED_MODULES_'.(int)$this->id_employee, $module_install);
        Configuration::updateValue('PS_SHOW_ENABLED_MODULES_'.(int)$this->id_employee, $module_status);
    }

    protected function resetFilterModules()
    {
        Configuration::updateValue('PS_SHOW_TYPE_MODULES_'.(int)$this->id_employee, 'allModules');
        Configuration::updateValue('PS_SHOW_COUNTRY_MODULES_'.(int)$this->id_employee, 0);
        Configuration::updateValue('PS_SHOW_INSTALLED_MODULES_'.(int)$this->id_employee, 'installedUninstalled');
        Configuration::updateValue('PS_SHOW_ENABLED_MODULES_'.(int)$this->id_employee, 'enabledDisabled');
        Configuration::updateValue('PS_SHOW_CAT_MODULES_'.(int)$this->id_employee, '');
    }

    /*
    ** Post Process Filter
    **
    */

    public function postProcessFilterModules()
    {
        $this->setFilterModules(Tools::getValue('module_type'), Tools::getValue('country_module_value'), Tools::getValue('module_install'), Tools::getValue('module_status'));
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }

    public function postProcessResetFilterModules()
    {
        $this->resetFilterModules();
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }

    public function postProcessFilterCategory()
    {
        // Save configuration and redirect employee
        Configuration::updateValue('PS_SHOW_CAT_MODULES_'.(int)$this->id_employee, Tools::getValue('filterCategory'));
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }

    public function postProcessUnfilterCategory()
    {
        // Save configuration and redirect employee
        Configuration::updateValue('PS_SHOW_CAT_MODULES_'.(int)$this->id_employee, '');
        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }

    /*
    ** Post Process Module CallBack
    **
    */

    public function postProcessReset()
    {
        if ($this->tabAccess['edit'] === '1') {
            $module = Module::getInstanceByName(Tools::getValue('module_name'));
            if (Validate::isLoadedObject($module)) {
                if (!$module->getPermission('configure')) {
                    $this->errors[] = Tools::displayError('You do not have the permission to use this module.');
                } else {
                    if (Tools::getValue('keep_data') == '1' && method_exists($module, 'reset')) {
                        if ($module->reset()) {
                            Tools::redirectAdmin(self::$currentIndex.'&conf=21&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor='.ucfirst($module->name));
                        } else {
                            $this->errors[] = Tools::displayError('Cannot reset this module.');
                        }
                    } else {
                        if ($module->uninstall()) {
                            if ($module->install()) {
                                Tools::redirectAdmin(self::$currentIndex.'&conf=21&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor='.ucfirst($module->name));
                            } else {
                                $this->errors[] = Tools::displayError('Cannot install this module.');
                            }
                        } else {
                            $this->errors[] = Tools::displayError('Cannot uninstall this module.');
                        }
                    }
                }
            } else {
                $this->errors[] = Tools::displayError('Cannot load the module\'s object.');
            }

            if (($errors = $module->getErrors()) && is_array($errors)) {
                $this->errors = array_merge($this->errors, $errors);
            }
        } else {
            $this->errors[] = Tools::displayError('You do not have permission to add this.');
        }
    }

    public function postProcessDownload($redirect = true)
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_ || ($this->context->mode == Context::MODE_HOST)) {
            $this->errors[] = Tools::displayError('This functionality has been disabled.');
            return;
        }

        // Try to upload and unarchive the module
        if ($this->tabAccess['add'] === '1') {
            // UPLOAD_ERR_OK: 0
            // UPLOAD_ERR_INI_SIZE: 1
            // UPLOAD_ERR_FORM_SIZE: 2
            // UPLOAD_ERR_NO_TMP_DIR: 6
            // UPLOAD_ERR_CANT_WRITE: 7
            // UPLOAD_ERR_EXTENSION: 8
            // UPLOAD_ERR_PARTIAL: 3

            if (isset($_FILES['file']['error']) && $_FILES['file']['error'] != UPLOAD_ERR_OK) {
                switch ($_FILES['file']['error']) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $this->errors[] = sprintf($this->l('File too large (limit of %s bytes).'), Tools::getMaxUploadSize());
                    break;
                    case UPLOAD_ERR_PARTIAL:
                        $this->errors[] = $this->l('File upload was not completed.');
                    break;
                    case UPLOAD_ERR_NO_FILE:
                        $this->errors[] = $this->l('No file was uploaded.');
                    break;
                    default:
                        $this->errors[] = sprintf($this->l('Internal error #%s'), $_FILES['newfile']['error']);
                    break;
                }
            } elseif (!isset($_FILES['file']['tmp_name']) || empty($_FILES['file']['tmp_name'])) {
                $this->errors[] = $this->l('No file has been selected');
            } elseif (substr($_FILES['file']['name'], -4) != '.tar' && substr($_FILES['file']['name'], -4) != '.zip'
                && substr($_FILES['file']['name'], -4) != '.tgz' && substr($_FILES['file']['name'], -7) != '.tar.gz') {
                $this->errors[] = Tools::displayError('Unknown archive type.');
            } elseif (!move_uploaded_file($_FILES['file']['tmp_name'], _PS_MODULE_DIR_.$_FILES['file']['name'])) {
                $this->errors[] = Tools::displayError('An error occurred while copying the archive to the module directory.');
            } else {
                $this->extractArchive(_PS_MODULE_DIR_.$_FILES['file']['name'], $redirect);
            }
        } else {
            $this->errors[] = Tools::displayError('You do not have permission to add this.');
        }
    }

    public function postProcessEnable()
    {
        if ($this->tabAccess['edit'] === '1') {
            $module = Module::getInstanceByName(Tools::getValue('module_name'));
            if (Validate::isLoadedObject($module)) {
                if (!$module->getPermission('configure')) {
                    $this->errors[] = Tools::displayError('You do not have the permission to use this module.');
                } else {
                    if (Tools::getValue('enable')) {
                        $module->enable();
                    } else {
                        $module->disable();
                    }
                    Tools::redirectAdmin($this->getCurrentUrl('enable'));
                }
            } else {
                $this->errors[] = Tools::displayError('Cannot load the module\'s object.');
            }
        } else {
            $this->errors[] = Tools::displayError('You do not have permission to add this.');
        }
    }

    public function postProcessEnable_Device()
    {
        if ($this->tabAccess['edit'] === '1') {
            $module = Module::getInstanceByName(Tools::getValue('module_name'));
            if (Validate::isLoadedObject($module)) {
                if (!$module->getPermission('configure')) {
                    $this->errors[] = Tools::displayError('You do not have the permission to use this module.');
                } else {
                    $module->enableDevice((int)Tools::getValue('enable_device'));
                    Tools::redirectAdmin($this->getCurrentUrl('enable_device'));
                }
            } else {
                $this->errors[] = Tools::displayError('Cannot load the module\'s object.');
            }
        } else {
            $this->errors[] = Tools::displayError('You do not have permission to add this.');
        }
    }

    public function postProcessDisable_Device()
    {
        if ($this->tabAccess['edit'] === '1') {
            $module = Module::getInstanceByName(Tools::getValue('module_name'));
            if (Validate::isLoadedObject($module)) {
                if (!$module->getPermission('configure')) {
                    $this->errors[] = Tools::displayError('You do not have the permission to use this module.');
                } else {
                    $module->disableDevice((int)Tools::getValue('disable_device'));
                    Tools::redirectAdmin($this->getCurrentUrl('disable_device'));
                }
            } else {
                $this->errors[] = Tools::displayError('Cannot load the module\'s object.');
            }
        } else {
            $this->errors[] = Tools::displayError('You do not have permission to add this.');
        }
    }

    public function postProcessDelete()
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_) {
            $this->errors[] = Tools::displayError('This functionality has been disabled.');
            return;
        }

        if ($this->tabAccess['delete'] === '1') {
            if (Tools::getValue('module_name') != '') {
                $module = Module::getInstanceByName(Tools::getValue('module_name'));
                if (Validate::isLoadedObject($module) && !$module->getPermission('configure')) {
                    $this->errors[] = Tools::displayError('You do not have the permission to use this module.');
                } else {
                    // Uninstall the module before deleting the files, but do not block the process if uninstall returns false
                    if (Module::isInstalled($module->name)) {
                        $module->uninstall();
                    }
                    $moduleDir = _PS_MODULE_DIR_.str_replace(array('.', '/', '\\'), array('', '', ''), Tools::getValue('module_name'));
                    $this->recursiveDeleteOnDisk($moduleDir);
                    if (!file_exists($moduleDir)) {
                        Tools::redirectAdmin(self::$currentIndex.'&conf=22&token='.$this->token.'&tab_module='.Tools::getValue('tab_module').'&module_name='.Tools::getValue('module_name'));
                    } else {
                        $this->errors[] = Tools::displayError('Sorry, the module cannot be deleted. Please check if you have the right permissions on this folder.');
                    }
                }
            }
        } else {
            $this->errors[] = Tools::displayError('You do not have permission to delete this.');
        }
    }

    public function postProcessCallback()
    {
        $return = false;
        $installed_modules = array();

        foreach ($this->map as $key => $method) {
            if (!Tools::getValue($key)) {
                continue;
            }

            /* PrestaShop demo mode */
            if (_PS_MODE_DEMO_) {
                $this->errors[] = Tools::displayError('This functionality has been disabled.');
                return;
            }

            if ($key == 'check') {
                $this->ajaxProcessRefreshModuleList(true);
            } elseif ($key == 'checkAndUpdate') {
                $this->ajaxProcessRefreshModuleList(true);
                $modules = array();
                $modules_on_disk = Module::getModulesOnDisk(true, $this->logged_on_addons, $this->id_employee, true);

                // Browse modules list
                foreach ($modules_on_disk as $km => $module_on_disk) {
                    if (!Tools::getValue('module_name') && isset($module_on_disk->version_addons) && $module_on_disk->version_addons) {
                        $modules[] = $module_on_disk->name;
                    }
                }

                if (!Tools::getValue('module_name')) {
                    $modules_list_save = implode('|', $modules);
                }
            } elseif (($modules = Tools::getValue($key)) && $key != 'checkAndUpdate' && $key != 'updateAll') {
                if (strpos($modules, '|')) {
                    $modules_list_save = $modules;
                    $modules = explode('|', $modules);
                }

                if (!is_array($modules)) {
                    $modules = (array)$modules;
                }
            } elseif ($key == 'updateAll') {
                $loggedOnAddons = false;

                if (isset($this->context->cookie->username_addons)
                    && isset($this->context->cookie->password_addons)
                    && !empty($this->context->cookie->username_addons)
                    && !empty($this->context->cookie->password_addons)) {
                    $loggedOnAddons = true;
                }

                $allModules = Module::getModulesOnDisk(true, $loggedOnAddons, $this->context->employee->id);
                $upgradeAvailable = 0;
                $modules = array();

                foreach ($allModules as $km => $moduleToUpdate) {
                    if ($moduleToUpdate->installed && isset($moduleToUpdate->version_addons) && $moduleToUpdate->version_addons) {
                        $modules[] = (string)$moduleToUpdate->name;
                    }
                }
            }

            $module_upgraded = array();
            $module_errors = array();
            if (isset($modules)) {
                foreach ($modules as $name) {
                    $module_to_update = array();
                    $module_to_update[$name] = null;
                    $full_report = null;
                    // If Addons module, download and unzip it before installing it
                    if (!file_exists(_PS_MODULE_DIR_.$name.'/'.$name.'.php') || $key == 'update' || $key == 'updateAll') {
                        $files_list = array(
                            array('type' => 'addonsNative', 'file' => Module::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST, 'loggedOnAddons' => 0),
                            // array('type' => 'addonsBought', 'file' => Module::CACHE_FILE_CUSTOMER_MODULES_LIST, 'loggedOnAddons' => 1),
                            // array('type' => 'addonsMustHave', 'file' => Module::CACHE_FILE_MUST_HAVE_MODULES_LIST, 'loggedOnAddons' => 1),
                            array('type' => 'addonsMustHave', 'file' => Module::CACHE_FILE_ADDONS_MODULES_LIST, 'loggedOnAddons' => 1),
                        );

                        foreach ($files_list as $f) {
                            if (file_exists(_PS_ROOT_DIR_.$f['file'])) {
                                $file = $f['file'];
                                $content = Tools::file_get_contents(_PS_ROOT_DIR_.$file);
                                if ($xml = @simplexml_load_string($content, null, LIBXML_NOCDATA)) {
                                    foreach ($xml->module as $modaddons) {
                                        if (Tools::strtolower($name) == Tools::strtolower($modaddons->name)) {
                                            // $module_to_update[$name]['id'] = $modaddons->id;
                                            $module_to_update[$name]['name'] = $modaddons->name;
                                            $module_to_update[$name]['displayName'] = $modaddons->displayName;
                                            $module_to_update[$name]['need_loggedOnAddons'] = $f['loggedOnAddons'];
                                            if ($modaddons->url) {
                                                $module_to_update[$name]['url'] = $modaddons->url;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        foreach ($module_to_update as $name => $attr) {
                            if ((is_null($attr) && $this->logged_on_addons == 0) || ($attr['need_loggedOnAddons'] == 1 && $this->logged_on_addons == 0)) {
                                $this->errors[] = sprintf(Tools::displayError('You need to be download this module from store in order to update the %s module. %s'), '<strong>'.$name.'</strong>', '<a href="'.$attr['url'].'" title="Store">'.$this->l('Click here to download.').'</a>');
                                // $this->errors[] = sprintf(Tools::displayError('You need to be logged in to your PrestaShop Addons account in order to update the %s module. %s'), '<strong>'.$name.'</strong>', '<a href="#" class="addons_connect" data-toggle="modal" data-target="#modal_addons_connect" title="Addons">'.$this->l('Click here to log in.').'</a>');
                            } elseif (!is_null($attr['name'])) {
                                $download_ok = false;
                                if ($attr['need_loggedOnAddons'] == 0 && file_put_contents(_PS_MODULE_DIR_.$name.'.zip', Tools::addonsRequest('module', array('module_name' => pSQL($attr['name']))))) {
                                    $download_ok = true;
                                } elseif ($attr['need_loggedOnAddons'] == 1 && $this->logged_on_addons && file_put_contents(_PS_MODULE_DIR_.$name.'.zip', Tools::addonsRequest('module', array('module_name' => pSQL($attr['name']), 'username_addons' => pSQL(trim($this->context->cookie->username_addons)), 'password_addons' => pSQL(trim($this->context->cookie->password_addons)))))) {
                                    $download_ok = true;
                                }

                                if (!$download_ok) {
                                    $this->errors[] = sprintf(Tools::displayError('Module %s cannot be upgraded: Error while downloading the latest version.'), '<strong>'.$attr['displayName'].'</strong>');
                                } elseif (!$this->extractArchive(_PS_MODULE_DIR_.$name.'.zip', false)) {
                                    $this->errors[] = sprintf(Tools::displayError('Module %s cannot be upgraded: Error while extracting the latest version.'), '<strong>'.$attr['displayName'].'</strong>');
                                } else {
                                    $module_upgraded[] = $name;
                                }
                            } else {
                                $this->errors[] = sprintf(Tools::displayError('You do not have the rights to update the %s module. Please make sure you are logged in to the PrestaShop Addons account that purchased the module.'), '<strong>'.$name.'</strong>');
                            }
                        }
                    }

                    if (count($this->errors)) {
                        continue;
                    }

                    // Check potential error
                    if (!($module = Module::getInstanceByName(urldecode($name)))) {
                        $this->errors[] = $this->l('Module not found');
                    } elseif (($this->context->mode >= Context::MODE_HOST_CONTRIB) && in_array($module->name, Module::$hosted_modules_blacklist)) {
                        $this->errors[] = Tools::displayError('You do not have permission to access this module.');
                    } elseif ($key == 'install' && $this->tabAccess['add'] !== '1') {
                        $this->errors[] = Tools::displayError('You do not have permission to install this module.');
                    } elseif ($key == 'install' && ($this->context->mode == Context::MODE_HOST) && !Module::isModuleTrusted($module->name)) {
                        $this->errors[] = Tools::displayError('You do not have permission to install this module.');
                    } elseif ($key == 'delete' && ($this->tabAccess['delete'] !== '1' || !$module->getPermission('configure'))) {
                        $this->errors[] = Tools::displayError('You do not have permission to delete this module.');
                    } elseif ($key == 'configure' && (!$module->getPermission('configure') || !Module::isInstalled(urldecode($name)))) {
                        $this->errors[] = Tools::displayError('You do not have permission to configure this module.');
                    } elseif ($key == 'install' && Module::isInstalled($module->name)) {
                        $this->errors[] = sprintf(Tools::displayError('This module is already installed: %s.'), $module->name);
                    } elseif ($key == 'uninstall' && !Module::isInstalled($module->name)) {
                        $this->errors[] = sprintf(Tools::displayError('This module has already been uninstalled: %s.'), $module->name);
                    } elseif ($key == 'update' && !Module::isInstalled($module->name)) {
                        $this->errors[] = sprintf(Tools::displayError('This module needs to be installed in order to be updated: %s.'), $module->name);
                    } else {
                        // If we install a module, force temporary global context for multishop
                        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL && $method != 'getContent') {
                            $shop_id = (int)Context::getContext()->shop->id;
                            Context::getContext()->tmpOldShop = clone(Context::getContext()->shop);
                            if ($shop_id) {
                                Context::getContext()->shop = new Shop($shop_id);
                            }
                        }

                        //retrocompatibility
                        if (Tools::getValue('controller') != '') {
                            $_POST['tab'] = Tools::safeOutput(Tools::getValue('controller'));
                        }

                        $echo = '';
                        if ($key != 'update' && $key != 'updateAll' && $key != 'checkAndUpdate') {
                            // We check if method of module exists
                            if (!method_exists($module, $method)) {
                                throw new PrestaShopException('Method of module cannot be found');
                            }

                            if ($key == 'uninstall' && !Module::getPermissionStatic($module->id, 'uninstall')) {
                                $this->errors[] = Tools::displayError('You do not have permission to uninstall this module.');
                            }

                            if (count($this->errors)) {
                                continue;
                            }
                            // Get the return value of current method
                            $echo = $module->{$method}();

                            // After a successful install of a single module that has a configuration method, to the configuration page
                            if ($key == 'install' && $echo === true && strpos(Tools::getValue('install'), '|') === false && method_exists($module, 'getContent')) {
                                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&configure='.$module->name.'&conf=12');
                            }
                        }

                        // If the method called is "configure" (getContent method), we show the html code of configure page
                        if ($key == 'configure' && Module::isInstalled($module->name)) {
                            $this->bootstrap = (isset($module->bootstrap) && $module->bootstrap);
                            if (isset($module->multishop_context)) {
                                $this->multishop_context = $module->multishop_context;
                            }

                            $back_link = self::$currentIndex.'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name;
                            $hook_link = 'index.php?tab=AdminModulesPositions&token='.Tools::getAdminTokenLite('AdminModulesPositions').'&show_modules='.(int)$module->id;
                            $trad_link = 'index.php?tab=AdminTranslations&token='.Tools::getAdminTokenLite('AdminTranslations').'&type=modules&lang=';
                            $disable_link = $this->context->link->getAdminLink('AdminModules').'&module_name='.$module->name.'&enable=0&tab_module='.$module->tab;
                            $uninstall_link = $this->context->link->getAdminLink('AdminModules').'&module_name='.$module->name.'&uninstall='.$module->name.'&tab_module='.$module->tab;
                            $reset_link = $this->context->link->getAdminLink('AdminModules').'&module_name='.$module->name.'&reset&tab_module='.$module->tab;
                            $update_link = $this->context->link->getAdminLink('AdminModules').'&checkAndUpdate=1&module_name='.$module->name;

                            $is_reset_ready = false;
                            if (method_exists($module, 'reset')) {
                                $is_reset_ready = true;
                            }

                            $this->context->smarty->assign(
                                array(
                                    'module_name' => $module->name,
                                    'module_display_name' => $module->displayName,
                                    'back_link' => $back_link,
                                    'module_hook_link' => $hook_link,
                                    'module_disable_link' => $disable_link,
                                    'module_uninstall_link' => $uninstall_link,
                                    'module_reset_link' => $reset_link,
                                    'module_update_link' => $update_link,
                                    'trad_link' => $trad_link,
                                    'module_languages' => Language::getLanguages(false),
                                    'theme_language_dir' => _THEME_LANG_DIR_,
                                    'page_header_toolbar_title' => $this->page_header_toolbar_title,
                                    'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
                                    'add_permission' => $this->tabAccess['add'],
                                    'is_reset_ready' => $is_reset_ready,
                                )
                            );

                            // Display checkbox in toolbar if multishop
                            if (Shop::isFeatureActive()) {
                                if (Shop::getContext() == Shop::CONTEXT_SHOP) {
                                    $shop_context = 'shop <strong>'.$this->context->shop->name.'</strong>';
                                } elseif (Shop::getContext() == Shop::CONTEXT_GROUP) {
                                    $shop_group = new ShopGroup((int)Shop::getContextShopGroupID());
                                    $shop_context = 'all shops of group shop <strong>'.$shop_group->name.'</strong>';
                                } else {
                                    $shop_context = 'all shops';
                                }

                                $this->context->smarty->assign(array(
                                    'module' => $module,
                                    'display_multishop_checkbox' => true,
                                    'current_url' => $this->getCurrentUrl('enable'),
                                    'shop_context' => $shop_context,
                                ));
                            }

                            $this->context->smarty->assign(array(
                                'is_multishop' => Shop::isFeatureActive(),
                                'multishop_context' => Shop::CONTEXT_ALL | Shop::CONTEXT_GROUP | Shop::CONTEXT_SHOP
                            ));

                            if (Shop::isFeatureActive() && isset(Context::getContext()->tmpOldShop)) {
                                Context::getContext()->shop = clone(Context::getContext()->tmpOldShop);
                                unset(Context::getContext()->tmpOldShop);
                            }

                            // Display module configuration
                            $header = $this->context->smarty->fetch('controllers/modules/configure.tpl');
                            $configuration_bar = $this->context->smarty->fetch('controllers/modules/configuration_bar.tpl');

                            $output = $header.$echo;

                            if (isset($this->_modules_ad[$module->name])) {
                                $ad_modules = $this->getModulesByInstallation($this->_modules_ad[$module->name]);

                                foreach ($ad_modules['not_installed'] as &$module) {
                                    if (isset($module->addons_buy_url)) {
                                        $module->addons_buy_url = str_replace('utm_source=v1trunk_api', 'utm_source=back-office', $module->addons_buy_url)
                                            .'&utm_medium=related-modules&utm_campaign=back-office-'.strtoupper($this->context->language->iso_code)
                                            .'&utm_content='.(($this->context->mode >= Context::MODE_HOST_CONTRIB) ? 'cloud' : 'download');
                                    }
                                    if (isset($module->description_full) && trim($module->description_full) != '') {
                                        $module->show_quick_view = true;
                                    }
                                }
                                $this->context->smarty->assign(array(
                                    'ad_modules' => $ad_modules,
                                    'currentIndex' => self::$currentIndex
                                ));
                                $ad_bar = $this->context->smarty->fetch('controllers/modules/ad_bar.tpl');
                                $output .= $ad_bar;
                            }

                            $this->context->smarty->assign('module_content', $output.$configuration_bar);
                        } elseif ($echo === true) {
                            $return = 13;
                            if ($method == 'install') {
                                $return = 12;
                                $installed_modules[] = $module->id;
                            }
                        } elseif ($echo === false) {
                            $module_errors[] = array('name' => $name, 'message' => $module->getErrors());
                        }

                        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL && isset(Context::getContext()->tmpOldShop)) {
                            Context::getContext()->shop = clone(Context::getContext()->tmpOldShop);
                            unset(Context::getContext()->tmpOldShop);
                        }
                    }
                    if ($key != 'configure' && Tools::getIsset('bpay')) {
                        Tools::redirectAdmin('index.php?tab=AdminPayment&token='.Tools::getAdminToken('AdminPayment'.(int)Tab::getIdFromClassName('AdminPayment').(int)$this->id_employee));
                    }
                }
            }

            if (count($module_errors)) {
                // If error during module installation, no redirection
                $html_error = $this->generateHtmlMessage($module_errors);
                if ($key == 'uninstall') {
                    $this->errors[] = sprintf(Tools::displayError('The following module(s) could not be uninstalled properly: %s.'), $html_error);
                } else {
                    $this->errors[] = sprintf(Tools::displayError('The following module(s) could not be installed properly: %s.'), $html_error);
                }
                $this->context->smarty->assign('error_module', 'true');
            }
        }

        if ($return) {
            $params = (count($installed_modules)) ? '&installed_modules='.implode('|', $installed_modules) : '';

            // If redirect parameter is present and module installed with success, we redirect on configuration module page
            if (Tools::getValue('redirect') == 'config' && Tools::getValue('module_name') != '' && $return == '12' && Module::isInstalled(pSQL(Tools::getValue('module_name')))) {
                Tools::redirectAdmin('index.php?controller=adminmodules&configure='.Tools::getValue('module_name').'&token='.Tools::getValue('token').'&module_name='.Tools::getValue('module_name').$params);
            }
            Tools::redirectAdmin(self::$currentIndex.'&conf='.$return.'&token='.$this->token.'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor='.ucfirst($module->name).(isset($modules_list_save) ? '&modules_list='.$modules_list_save : '').$params);
        }

        if (Tools::getValue('update') || Tools::getValue('updateAll') || Tools::getValue('checkAndUpdate')) {
            $updated = '&updated=1';
            if (Tools::getValue('checkAndUpdate')) {
                $updated = '&check=1';
                if (Tools::getValue('module_name')) {
                    $module = Module::getInstanceByName(Tools::getValue('module_name'));
                    if (!Validate::isLoadedObject($module)) {
                        unset($module);
                    }
                }
            }

            $module_upgraded = implode('|', $module_upgraded);

            if (Tools::getValue('updateAll')) {
                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&allUpdated=1');
            } elseif (isset($module_upgraded) && $module_upgraded != '') {
                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&updated=1&module_name='.$module_upgraded);
            } elseif (isset($modules_list_save)) {
                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&updated=1&module_name='.$modules_list_save);
            } elseif (isset($module)) {
                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.$updated.'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor='.ucfirst($module->name).(isset($modules_list_save) ? '&modules_list='.$modules_list_save : ''));
            }
        }
    }

    protected function getModulesByInstallation($tab_modules_list = null)
    {
        // $all_modules = Module::getModulesOnDisk(true, $this->logged_on_addons, $this->id_employee);
        $all_modules = Module::getSuggestedModules();
        $all_unik_modules = array();
        $modules_list = array('installed' =>array(), 'not_installed' => array());

        foreach ($all_modules as $mod) {
            if (!isset($all_unik_modules[$mod->name])) {
                $all_unik_modules[$mod->name] = $mod;
            }
        }

        $all_modules = $all_unik_modules;

        foreach ($all_modules as $module) {
            if (!isset($tab_modules_list) || in_array($module->name, $tab_modules_list)) {
                $perm = true;
                if ($module->id) {
                    $perm &= Module::getPermissionStatic($module->id, 'configure');
                } else {
                    $id_admin_module = Tab::getIdFromClassName('AdminModules');
                    $access = Profile::getProfileAccess($this->context->employee->id_profile, $id_admin_module);
                    if (!$access['edit']) {
                        $perm &= false;
                    }
                }

                if (in_array($module->name, $this->list_partners_modules)) {
                    $module->type = 'addonsPartner';
                }

                if ($perm) {
                    $this->fillModuleData($module, 'array');
                    if ($module->id) {
                        $modules_list['installed'][] = $module;
                    } else {
                        $modules_list['not_installed'][] = $module;
                    }
                }
            }
        }

        return $modules_list;
    }

    public function postProcess()
    {
        // Parent Post Process
        parent::postProcess();

        // Get the list of installed module ans prepare it for ajax call.
        if (($list = Tools::getValue('installed_modules'))) {
            Context::getContext()->smarty->assign('installed_modules', json_encode(explode('|', $list)));
        }

        // If redirect parameter is present and module already installed, we redirect on configuration module page
        if (Tools::getValue('redirect') == 'config' && Tools::getValue('module_name') != '' && Module::isInstalled(pSQL(Tools::getValue('module_name')))) {
            Tools::redirectAdmin('index.php?controller=adminmodules&configure='.Tools::getValue('module_name').'&token='.Tools::getValue('token').'&module_name='.Tools::getValue('module_name'));
        }

        // Execute filter or callback methods
        $filter_methods = array('filterModules', 'resetFilterModules', 'filterCategory', 'unfilterCategory');
        $callback_methods = array('reset', 'download', 'enable', 'delete', 'enable_device', 'disable_device');
        $post_process_methods_list = array_merge((array)$filter_methods, (array)$callback_methods);
        foreach ($post_process_methods_list as $ppm) {
            if (Tools::isSubmit($ppm)) {
                $ppm = 'postProcess'.ucfirst($ppm);
                if (method_exists($this, $ppm)) {
                    $ppm_return = $this->$ppm();
                }
            }
        }

        // Call appropriate module callback
        if (!isset($ppm_return)) {
            $this->postProcessCallback();
        }

        if ($back = Tools::getValue('back')) {
            Tools::redirectAdmin($back);
        }
    }

    /**
     * Generate html errors for a module process
     *
     * @param $module_errors
     * @return string
     */
    protected function generateHtmlMessage($module_errors)
    {
        $html_error = '';

        if (count($module_errors)) {
            $html_error = '<ul>';
            foreach ($module_errors as $module_error) {
                $html_error_description = '';
                if (count($module_error['message']) > 0) {
                    foreach ($module_error['message'] as $e) {
                        $html_error_description .= '<br />&nbsp;&nbsp;&nbsp;&nbsp;'.$e;
                    }
                }
                $html_error .= '<li><b>'.$module_error['name'].'</b> : '.$html_error_description.'</li>';
            }
            $html_error .= '</ul>';
        }
        return $html_error;
    }

    public function initModulesList(&$modules)
    {
        foreach ($modules as $k => $module) {
            // Check add permissions, if add permissions not set, addons modules and uninstalled modules will not be displayed
            if ($this->tabAccess['add'] !== '1' && isset($module->type) && ($module->type != 'addonsNative' || $module->type != 'addonsBought')) {
                unset($modules[$k]);
            } elseif ($this->tabAccess['add'] !== '1' && (!isset($module->id) || $module->id < 1)) {
                unset($modules[$k]);
            } elseif ($module->id && !Module::getPermissionStatic($module->id, 'view') && !Module::getPermissionStatic($module->id, 'configure')) {
                unset($modules[$k]);
            } else {
                // Init serial and modules author list
                if (!in_array($module->name, $this->list_natives_modules)) {
                    $this->serial_modules .= $module->name.' '.$module->version.'-'.($module->active ? 'a' : 'i')."\n";
                }
                $module_author = $module->author;
                if (!empty($module_author) && ($module_author != '')) {
                    $this->modules_authors[strtolower($module_author)] = 'notselected';
                }
            }
        }
        $this->serial_modules = urlencode($this->serial_modules);
    }

    public function makeModulesStats($module)
    {
        // Count Installed Modules
        if (isset($module->id) && $module->id > 0) {
            $this->nb_modules_installed++;
        }

        // Count Activated Modules
        if (isset($module->id) && $module->id > 0 && $module->active > 0) {
            $this->nb_modules_activated++;
        }

        // Count Modules By Category
        if (isset($this->list_modules_categories[$module->tab]['nb'])) {
            $this->list_modules_categories[$module->tab]['nb']++;
        } else {
            $this->list_modules_categories['others']['nb']++;
        }
    }

    public function isModuleFiltered($module)
    {
        // If action on module, we display it
        if (Tools::getValue('module_name') != '' && Tools::getValue('module_name') == $module->name) {
            return false;
        }

        // Filter on module name
        $filter_name = Tools::getValue('filtername');
        if (!empty($filter_name)) {
            if (stristr($module->name, $filter_name) === false && stristr($module->displayName, $filter_name) === false && stristr($module->description, $filter_name) === false) {
                return true;
            }
            return false;
        }

        // Filter on interest
        if ($module->interest !== '') {
            if ($module->interest === '0') {
                return true;
            }
        } elseif ((int)Db::getInstance()->getValue('SELECT `id_module_preference` FROM `'._DB_PREFIX_.'module_preference` WHERE `module` = \''.pSQL($module->name).'\' AND `id_employee` = '.(int)$this->id_employee.' AND `interest` = 0') > 0) {
            return true;
        }

        // Filter on favorites
        if (Configuration::get('PS_SHOW_CAT_MODULES_'.(int)$this->id_employee) == 'favorites') {
            if ((int)Db::getInstance()->getValue('SELECT `id_module_preference` FROM `'._DB_PREFIX_.'module_preference` WHERE `module` = \''.pSQL($module->name).'\' AND `id_employee` = '.(int)$this->id_employee.' AND `favorite` = 1 AND (`interest` = 1 OR `interest` IS NULL)') < 1) {
                return true;
            }
        } else {
            // Handle "others" category
            if (!isset($this->list_modules_categories[$module->tab])) {
                $module->tab = 'others';
            }

            // Filter on module category
            $category_filtered = array();
            $filter_categories = explode('|', Configuration::get('PS_SHOW_CAT_MODULES_'.(int)$this->id_employee));
            if (count($filter_categories) > 0) {
                foreach ($filter_categories as $fc) {
                    if (!empty($fc)) {
                        $category_filtered[$fc] = 1;
                    }
                }
            }
            if (count($category_filtered) > 0 && !isset($category_filtered[$module->tab])) {
                return true;
            }
        }

        // Filter on module type and author
        $show_type_modules = $this->filter_configuration['PS_SHOW_TYPE_MODULES_'.(int)$this->id_employee];
        if ($show_type_modules == 'nativeModules' && !in_array($module->name, $this->list_natives_modules)) {
            return true;
        } elseif ($show_type_modules == 'partnerModules' && !in_array($module->name, $this->list_partners_modules)) {
            return true;
        } elseif ($show_type_modules == 'addonsModules' && (!isset($module->type) || $module->type != 'addonsBought')) {
            return true;
        } elseif ($show_type_modules == 'mustHaveModules' && (!isset($module->type) || $module->type != 'addonsMustHave')) {
            return true;
        } elseif ($show_type_modules == 'otherModules' && (in_array($module->name, $this->list_partners_modules) || in_array($module->name, $this->list_natives_modules))) {
            return true;
        } elseif (strpos($show_type_modules, 'authorModules[') !== false) {
            // setting selected author in authors set
            $author_selected = substr(str_replace(array('authorModules[', "\'"), array('', "'"), $show_type_modules), 0, -1);
            $this->modules_authors[$author_selected] = 'selected';
            if (empty($module->author) || strtolower($module->author) != $author_selected) {
                return true;
            }
        }

        // Filter on install status
        $show_installed_modules = $this->filter_configuration['PS_SHOW_INSTALLED_MODULES_'.(int)$this->id_employee];
        if ($show_installed_modules == 'installed' && !$module->id) {
            return true;
        }
        if ($show_installed_modules == 'uninstalled' && $module->id) {
            return true;
        }

        // Filter on active status
        $show_enabled_modules = $this->filter_configuration['PS_SHOW_ENABLED_MODULES_'.(int)$this->id_employee];
        if ($show_enabled_modules == 'enabled' && !$module->active) {
            return true;
        }
        if ($show_enabled_modules == 'disabled' && $module->active) {
            return true;
        }

        // Filter on country
        $show_country_modules = $this->filter_configuration['PS_SHOW_COUNTRY_MODULES_'.(int)$this->id_employee];
        if ($show_country_modules && (isset($module->limited_countries) && !empty($module->limited_countries)
                && ((is_array($module->limited_countries) && count($module->limited_countries)
                && !in_array(strtolower($this->iso_default_country), $module->limited_countries))
                || (!is_array($module->limited_countries) && strtolower($this->iso_default_country) != strval($module->limited_countries))))) {
            return true;
        }

        // Module has not been filtered
        return false;
    }

    public function renderKpis()
    {
        $time = time();
        $kpis = array();

        $helper = new HelperKpi();
        $helper->id = 'box-installed-modules';
        $helper->icon = 'icon-puzzle-piece';
        $helper->color = 'color1';
        $helper->title = $this->l('Installed Modules', null, null, false);
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=installed_modules';
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-disabled-modules';
        $helper->icon = 'icon-off';
        $helper->color = 'color2';
        $helper->title = $this->l('Disabled Modules', null, null, false);
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=disabled_modules';
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-update-modules';
        $helper->icon = 'icon-refresh';
        $helper->color = 'color3';
        $helper->title = $this->l('Modules to Update', null, null, false);
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=update_modules';
        $kpis[] = $helper->generate();

        $helper = new HelperKpiRow();
        $helper->kpis = $kpis;
        return $helper->generate();
    }

    public function initModal()
    {
        parent::initModal();

        $this->context->smarty->assign(array(
            'trad_link' => 'index.php?tab=AdminTranslations&token='.Tools::getAdminTokenLite('AdminTranslations').'&type=modules&lang=',
            'module_languages' => Language::getLanguages(false),
            'module_name' => Tools::getValue('module_name'),
        ));

        $modal_content = $this->context->smarty->fetch('controllers/modules/modal_translation.tpl');
        $this->modals[] = array(
            'modal_id' => 'moduleTradLangSelect',
            'modal_class' => 'modal-sm',
            'modal_title' => $this->l('Translate this module'),
            'modal_content' => $modal_content
        );

        $modal_content = $this->context->smarty->fetch('controllers/modules/'.(($this->context->mode == Context::MODE_HOST) ? 'modal_not_trusted_blocked.tpl' : 'modal_not_trusted.tpl'));
        $this->modals[] = array(
            'modal_id' => 'moduleNotTrusted',
            'modal_class' => 'modal-lg',
            'modal_title' => ($this->context->mode == Context::MODE_HOST) ? $this->l('This module cannot be installed') : $this->l('Important Notice'),
            'modal_content' => $modal_content
        );

        // $modal_content = $this->context->smarty->fetch('controllers/modules/modal_not_trusted_country.tpl');
        // $this->modals[] = array(
        //     'modal_id' => 'moduleNotTrustedCountry',
        //     'modal_class' => 'modal-lg',
        //     'modal_title' => $this->l('This module is Untrusted for your country'),
        //     'modal_content' => $modal_content
        // );
    }

    public function initContent()
    {
        if (Tools::isSubmit('addnewmodule') && $this->context->mode == Context::MODE_HOST) {
            $this->display = 'add';
            $this->context->smarty->assign(array('iso_code' => $this->context->language->iso_code));
            return parent::initContent();
        }

        $this->meta_title = 'Modules';

        // If we are on a module configuration, no need to load all modules
        if (Tools::getValue('configure') != '') {
            $this->context->smarty->assign(array('maintenance_mode' => !(bool)Configuration::Get('PS_SHOP_ENABLE')));

            return true;
        }

        $this->initToolbar();
        $this->initPageHeaderToolbar();

        // Init
        $smarty = $this->context->smarty;
        $autocomplete_list = 'var moduleList = [';
        $category_filtered = array();
        $filter_categories = explode('|', Configuration::get('PS_SHOW_CAT_MODULES_'.(int)$this->id_employee));
        if (count($filter_categories) > 0) {
            foreach ($filter_categories as $fc) {
                if (!empty($fc)) {
                    $category_filtered[$fc] = 1;
                }
            }
        }

        if (empty($category_filtered) && Tools::getValue('tab_module')) {
            $category_filtered[Tools::getValue('tab_module')] = 1;
        }

        foreach ($this->list_modules_categories as $k => $v) {
            $this->list_modules_categories[$k]['nb'] = 0;
        }

        // Retrieve Modules Preferences
        $modules_preferences = array();
        $tab_modules_preferences = array();
        $modules_preferences_tmp = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'module_preference` WHERE `id_employee` = '.(int)$this->id_employee);
        $tab_modules_preferences_tmp = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'tab_module_preference` WHERE `id_employee` = '.(int)$this->id_employee);

        foreach ($tab_modules_preferences_tmp as $i => $j) {
            $tab_modules_preferences[$j['module']][] = $j['id_tab'];
        }

        foreach ($modules_preferences_tmp as $k => $v) {
            if ($v['interest'] == null) {
                unset($v['interest']);
            }
            if ($v['favorite'] == null) {
                unset($v['favorite']);
            }
            $modules_preferences[$v['module']] = $v;
        }

        // Retrieve Modules List
        $modules = Module::getInstalledModulesOnDisk(true, $this->logged_on_addons, $this->id_employee);
        $this->initModulesList($modules);
        $this->nb_modules_total = count($modules);
        $module_errors = array();
        $module_success = array();
        $upgrade_available = array();
        $module_alerts = array();
        $dont_filter = false;

        //Add succes message for one module update
        if (Tools::getValue('updated') && Tools::getValue('module_name')) {
            $module_names = (string)Tools::getValue('module_name');
            if (strpos($module_names, '|')) {
                $module_names = explode('|', $module_names);
                $dont_filter = true;
            }

            if (!is_array($module_names)) {
                $module_names = (array)$module_names;
            }

            foreach ($modules as $km => $module) {
                if (in_array($module->name, $module_names)) {
                    $module_success[] = array('name' => $module->displayName, 'message' => array(
                        0 => sprintf($this->l('Current version: %s'), $module->version)));
                }
            }
        }

        if (Tools::getValue('allUpdated')) {
            $this->confirmations[] = $this->l('All modules updated successfully.');
        }

        // Browse modules list
        foreach ($modules as $km => $module) {
            //if we are in favorites view we only display installed modules
            if (Tools::getValue('select') == 'favorites' && !$module->id) {
                unset($modules[$km]);
                continue;
            }

            // Upgrade Module process, init check if a module could be upgraded
            if (Module::initUpgradeModule($module)) {
                // When the XML cache file is up-to-date, the module may not be loaded yet
                if (!class_exists($module->name)) {
                    if (!file_exists(_PS_MODULE_DIR_.$module->name.'/'.$module->name.'.php')) {
                        continue;
                    }
                    require_once(_PS_MODULE_DIR_.$module->name.'/'.$module->name.'.php');
                }

                if ($object = Adapter_ServiceLocator::get($module->name)) {
                    /** @var Module $object */
                    $object->runUpgradeModule();
                    if ((count($errors_module_list = $object->getErrors()))) {
                        $module_errors[] = array('name' => $module->displayName, 'message' => $errors_module_list);
                    } elseif ((count($conf_module_list = $object->getConfirmations()))) {
                        $module_success[] = array('name' => $module->displayName, 'message' => $conf_module_list);
                    }
                    unset($object);
                }
            }
            // Module can't be upgraded if not file exist but can change the database version...
            // User has to be prevented
            elseif (Module::getUpgradeStatus($module->name)) {
                // When the XML cache file is up-to-date, the module may not be loaded yet
                if (!class_exists($module->name)) {
                    if (file_exists(_PS_MODULE_DIR_.$module->name.'/'.$module->name.'.php')) {
                        require_once(_PS_MODULE_DIR_.$module->name.'/'.$module->name.'.php');
                        $object = Adapter_ServiceLocator::get($module->name);
                        $module_success[] = array('name' => $module->name, 'message' => array(
                            0 => sprintf($this->l('Current version: %s'), $object->version),
                            1 => $this->l('No file upgrades applied (none exist).'))
                        );
                    } else {
                        continue;
                    }
                }
                unset($object);
            }

            // Make modules stats
            $this->makeModulesStats($module);

            // Assign warnings
            if ($module->active && isset($module->warning) && !empty($module->warning) && !$this->ajax) {
                $href = Context::getContext()->link->getAdminLink('AdminModules', true).'&module_name='.$module->name.'&tab_module='.$module->tab.'&configure='.$module->name;
                $this->context->smarty->assign('text', sprintf($this->l('%1$s: %2$s'), $module->displayName, $module->warning));
                $this->context->smarty->assign('module_link', $href);
                $module_alerts[] = $this->context->smarty->fetch('controllers/modules/warning_module.tpl');
            }

            // AutoComplete array
            $autocomplete_list .= json_encode(array(
                'displayName' => (string)$module->displayName,
                'desc' => (string)$module->description,
                'name' => (string)$module->name,
                'author' => (string)$module->author,
                'image' => (isset($module->image) ? (string)$module->image : ''),
                'option' => '',
            )).', ';

            // Apply filter
            if ($this->isModuleFiltered($module) && Tools::getValue('select') != 'favorites') {
                unset($modules[$km]);
            } else {
                if (isset($modules_preferences[$modules[$km]->name])) {
                    $modules[$km]->preferences = $modules_preferences[$modules[$km]->name];
                }

                $this->fillModuleData($module, 'array');
                $module->categoryName = (isset($this->list_modules_categories[$module->tab]['name']) ? $this->list_modules_categories[$module->tab]['name'] : $this->list_modules_categories['others']['name']);
            }
            unset($object);
            if ($module->installed && isset($module->version_addons) && $module->version_addons) {
                $upgrade_available[] = array('anchor' => ucfirst($module->name), 'name' => $module->name, 'displayName' => $module->displayName, 'is_native' => $module->is_native);
            }

            if (in_array($module->name, $this->list_partners_modules)) {
                $module->type = 'addonsPartner';
            }

            if (isset($module->description_full) && trim($module->description_full) != '') {
                $module->show_quick_view = true;
            }
        }

        // Don't display categories without modules
        $cleaned_list = array();
        foreach ($this->list_modules_categories as $k => $list) {
            if ($list['nb'] > 0) {
                $cleaned_list[$k] = $list;
            }
        }

        // Actually used for the report of the upgraded errors
        if (count($module_errors)) {
            $html = $this->generateHtmlMessage($module_errors);
            $this->errors[] = sprintf(Tools::displayError('The following module(s) were not upgraded successfully: %s.'), $html);
        }
        if (count($module_success)) {
            $html = $this->generateHtmlMessage($module_success);
            $this->confirmations[] = sprintf($this->l('The following module(s) were upgraded successfully: %s.'), $html);
        }

        if (count($upgrade_available) == 0 && (int)Tools::getValue('check') == 1) {
            $this->confirmations[] = $this->l('Everything is up-to-date');
        }

        // Init tpl vars for smarty
        $tpl_vars = array(
            'token' => $this->token,
            'upgrade_available' => $upgrade_available,
            'module_alerts' => $module_alerts,
            'currentIndex' => self::$currentIndex,
            'dirNameCurrentIndex' => dirname(self::$currentIndex),
            'ajaxCurrentIndex' => str_replace('index', 'ajax-tab', self::$currentIndex),
            'autocompleteList' => rtrim($autocomplete_list, ' ,').'];',
            'showTypeModules' => $this->filter_configuration['PS_SHOW_TYPE_MODULES_'.(int)$this->id_employee],
            'showCountryModules' => $this->filter_configuration['PS_SHOW_COUNTRY_MODULES_'.(int)$this->id_employee],
            'showInstalledModules' => $this->filter_configuration['PS_SHOW_INSTALLED_MODULES_'.(int)$this->id_employee],
            'showEnabledModules' => $this->filter_configuration['PS_SHOW_ENABLED_MODULES_'.(int)$this->id_employee],
            'nameCountryDefault' => Country::getNameById($this->context->language->id, Configuration::get('PS_COUNTRY_DEFAULT')),
            'isoCountryDefault' => $this->iso_default_country,
            'categoryFiltered' => $category_filtered,
            'modules' => $modules,
            'nb_modules' => $this->nb_modules_total,
            'nb_modules_favorites' => count($this->context->employee->favoriteModulesList()),
            'nb_modules_installed' => $this->nb_modules_installed,
            'nb_modules_uninstalled' => $this->nb_modules_total - $this->nb_modules_installed,
            'nb_modules_activated' => $this->nb_modules_activated,
            'nb_modules_unactivated' => $this->nb_modules_installed - $this->nb_modules_activated,
            'list_modules_categories' => $cleaned_list,
            'list_modules_authors' => $this->modules_authors,
            'add_permission' => $this->tabAccess['add'],
            'tab_modules_preferences' => $tab_modules_preferences,
            'kpis' => $this->renderKpis(),
            'module_name' => Tools::getValue('module_name'),
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
            'modules_uri' => __PS_BASE_URI__.basename(_PS_MODULE_DIR_),
            'dont_filter' => $dont_filter,
            'is_contributor' => (int)$this->context->cookie->is_contributor,
            'maintenance_mode' => !(bool)Configuration::Get('PS_SHOP_ENABLE')
        );

        if ($this->logged_on_addons) {
            $tpl_vars['logged_on_addons'] = 1;
            $tpl_vars['username_addons'] = $this->context->cookie->username_addons;
        }
        $smarty->assign($tpl_vars);
    }

    public function ajaxProcessGetModuleQuickView()
    {
        $modules = Module::getModulesOnDisk();

        foreach ($modules as $module) {
            if ($module->name == Tools::getValue('module')) {
                break;
            }
        }

        $url = $module->url;

        if (isset($module->type) && ($module->type == 'addonsPartner' || $module->type == 'addonsNative')) {
            $url = $this->context->link->getAdminLink('AdminModules').'&install='.urlencode($module->name).'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor='.ucfirst($module->name);
        }

        $this->context->smarty->assign(array(
            'displayName' => $module->displayName,
            'image' => $module->image,
            'nb_rates' => (int)$module->nb_rates[0],
            'avg_rate' => (int)$module->avg_rate[0],
            'badges' => $module->badges,
            'compatibility' => $module->compatibility,
            'description_full' => $module->description_full,
            'additional_description' => $module->additional_description,
            'is_addons_partner' => (isset($module->type) && ($module->type == 'addonsPartner' || $module->type == 'addonsNative')),
            'url' => $url,
            'price' => $module->price
        ));
        $this->smartyOutputContent('controllers/modules/quickview.tpl');
    }

    public function ajaxProcessUploadModule()
    {
        $response = array('success' => false);
        $this->postProcessDownload(false);
        if (!count($this->errors)) {
            $filename = $_FILES['file']['name'];
            $filename = explode('.', $filename);
            if ($module = Module::getInstanceByName($filename[0])) {
                $response['success'] = true;
                if (Module::isInstalled($module->name)) {
                    $response['msg'] = $this->l('module already installed, update if available');
                } else {
                    $response['msg'] = $this->l('module uploaded successfully');
                }
                if (@filemtime(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.basename(_PS_MODULE_DIR_).DIRECTORY_SEPARATOR.$module->name
                .DIRECTORY_SEPARATOR.'logo.png')) {
                    $module->image = _MODULE_DIR_.DIRECTORY_SEPARATOR.$module->name
                    .DIRECTORY_SEPARATOR.'logo.png';
                }
                $response['data']['module'] = array(
                    'module_name' => $module->name,
                    'displayName' => $module->displayName,
                    'installed' => Module::isInstalled($module->name),
                    'author' => $module->author,
                    'image' => $module->image ? $module->image : ''
                );
            }
        } else {
            $response['errors'] = $this->errors;
        }
        $this->logAndResponseAjax($response);
    }

    public function ajaxProcessCheckModuleTrusted()
    {
        $response = array(
            'success' => false,
            'data' => array('trusted' => false)
        );
        if ($moduleName = Tools::getValue('module_name')) {
            if ($module = Module::getInstanceByName($moduleName)) {
                if ($response['data']['trusted'] = Module::isModuleTrusted($moduleName)) {
                    $response['msg'] = $this->l('Module trusted');
                } else {
                    $response['msg'] = $this->l('Module not trusted');
                }
                $response['success'] = true;
            } else {
                $this->errors[] = $this->l('Module not found.');
            }
        } else {
            $this->errors[] = $this->l('Invalid module provided');
        }
        $response['errors'] = $this->errors;
        $this->logAndResponseAjax($response);
    }

    public function ajaxProcessInstallModule()
    {
        $response = array(
            'success' => false,
        );
        if ($moduleName = Tools::getValue('module_name')) {
            if ($module = Module::getInstanceByName($moduleName)) {
                if (!Module::isInstalled($module->name)) {
                    if ($response['success'] = $module->install()) {
                        $response['msg'] = $this->l('Module installed successfully');
                        $response['data']['redirect'] = $this->context->link->getAdminLink('AdminModules').'&conf=12&anchor='.$module->name;
                    } else {
                        $this->errors[] = $this->l('Cannot install this module.');
                    }
                } else {
                    $response['success'] = true;
                    $response['msg'] = $this->l('Module already installed');
                }
            } else {
                $this->errors[] = $this->l('Module not found.');
            }
        }

        if (count($this->errors)) {
            $response['data']['callback']['process'] = 'delete';
            $response['data']['callback']['msg'] = $this->l('There was some error while installing the module, do you want to delete the uploded module');
            $response['errors'] = $this->errors;
        }
        $this->logAndResponseAjax($response);
    }

    public function ajaxProcessRollbackModuleUpload()
    {
        $response = array(
            'success' => false
        );
        $process = Tools::getValue('process');
        if ($moduleName = Tools::getValue('module_name')) {
            if($process == 'disable') {
                if ($module = Module::getInstanceByName($moduleName)) {
                    if (Module::isInstalled($module->name)) {
                        $module->disable();
                        $response['success'] = true;
                        $response['msg'] = $this->l('Module disable successfully');
                        $response['data']['redirect'] = $this->context->link->getAdminLink('AdminModules').'&conf=5';
                    }
                }
            } else if($process == 'delete' || $process == 'uninstall') {
                if ($module = Module::getInstanceByName($moduleName)) {
                    if (Module::isInstalled($module->name)) {
                        if ($module->uninstall()) {
                            if ($process == 'uninstall') {
                                $response['success'] = true;
                                $response['msg'] = $this->l('Module uninstall process completed');
                                $response['data']['redirect'] = $this->context->link->getAdminLink('AdminModules').'&conf=22';
                            }
                        }
                    }
                }
                if($process == 'delete') {
                    if (is_dir(_PS_MODULE_DIR_.$moduleName)) {
                        $this->recursiveDeleteOnDisk(_PS_MODULE_DIR_.$moduleName);
                        $response['success'] = true;
                        $response['msg'] = $this->l('Module rollback process completed');
                        $response['data']['redirect'] = $this->context->link->getAdminLink('AdminModules').'&conf=22';
                    }
                }
            }
        }

        $response['errors'] = $this->errors;

        $this->logAndResponseAjax($response);
    }

    public function ajaxProcessUpdateModule()
    {
        $response = array(
            'success' => false
        );
        if ($moduleName = Tools::getValue('module_name')) {
            if ($module = Module::getInstanceByName($moduleName)) {
                if ($installed = Module::getModuleInstallVersion($module->name)) {
                    $module->installed = (bool)$installed;
                    $module->database_version = $installed['version'];
                    if (Module::initUpgradeModule($module)) {
                        if (!class_exists($module->name)) {
                            require_once(_PS_MODULE_DIR_.$module->name.'/'.$module->name.'.php');
                        }
                        if ($object = Adapter_ServiceLocator::get($module->name)) {
                            $object->runUpgradeModule();
                            if ((count($errors_module_list = $object->getErrors()))) {
                                $this->errors = array_merge($this->errors, array('name' => $module->displayName, 'message' => $errors_module_list));
                            } elseif ((count($conf_module_list = $object->getConfirmations()))) {
                                $response['data']['upgrade'][] = array('name' => $module->displayName, 'message' => $conf_module_list);
                                $response['success'] = true;
                                $html = $this->generateHtmlMessage($response['data']['upgrade']);
                                $response['msg'] = sprintf($this->l('The following module was upgraded successfully: %s.'), $html);
                                $response['data']['redirect'] = $this->context->link->getAdminLink('AdminModules').'&conf=29&anchor='.$module->name;
                            }
                            unset($object);
                        } else {
                            $this->errors[] = $this->l('Module not found!');
                        }
                    } else {
                        $response['success'] = true;
                        $response['msg'] = $this->l('Module files updated successfully');
                        $response['data']['redirect'] = $this->context->link->getAdminLink('AdminModules').'&conf=29&anchor='.$module->name;
                    }
                } else {
                    $this->errors[] = $this->l('Module not installed, install module before updating.');
                }
            } else {
                $this->errors[] = $this->l('Module not found!');
            }
        }
        if (count($this->errors)) {
            $response['data']['callback']['process'] = 'disable';
            $response['data']['callback']['msg'] = $this->l('There was some error while updating the module, do you want to disable the module');
            $response['errors'] = $this->errors;
        }

        $this->logAndResponseAjax($response);
    }

    public function logAndResponseAjax($response)
    {
        if (isset($response['errors']) && count($response['errors'])) {
            PrestaShopLogger::addLog('Module install/upload error : Resquest => '.json_encode(Tools::getAllValues()).' || Response =>'.(json_encode($response)), 1, null, 'Module', null, true, (int)$this->context->employee->id);
        }
        $this->ajaxDie(json_encode($response));
    }
}
