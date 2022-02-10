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

class InstallControllerHttpProcess extends InstallControllerHttp
{
    const SETTINGS_FILE = 'config/settings.inc.php';

    protected $model_install;
    public $process_steps = array();
    public $previous_button = false;

    public function init()
    {
        require_once _PS_INSTALL_MODELS_PATH_.'install.php';
        $this->model_install = new InstallModelInstall();
    }

    /**
     * @see InstallAbstractModel::processNextStep()
     */
    public function processNextStep()
    {
    }

    /**
     * @see InstallAbstractModel::validate()
     */
    public function validate()
    {
        return false;
    }

    public function initializeContext()
    {
        global $smarty;

        Context::getContext()->shop = new Shop(1);
        Shop::setContext(Shop::CONTEXT_SHOP, 1);
        Configuration::loadConfiguration();
        Context::getContext()->language = new Language(Configuration::get('PS_LANG_DEFAULT'));
        Context::getContext()->country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));
        Context::getContext()->currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        Context::getContext()->cart = new Cart();
        Context::getContext()->employee = new Employee(1);
        define('_PS_SMARTY_FAST_LOAD_', true);
        require_once _PS_ROOT_DIR_.'/config/smarty.config.inc.php';

        Context::getContext()->smarty = $smarty;
    }

    public function process()
    {
        if (file_exists(_PS_ROOT_DIR_.'/'.self::SETTINGS_FILE)) {
            require_once _PS_ROOT_DIR_.'/'.self::SETTINGS_FILE;
        }

        if (!$this->session->process_validated) {
            $this->session->process_validated = array();
        }

        if (Tools::getValue('generateSettingsFile')) {
            $this->processGenerateSettingsFile();
        } elseif (Tools::getValue('installDatabase') && !empty($this->session->process_validated['generateSettingsFile'])) {
            $this->processInstallDatabase();
        } elseif (Tools::getValue('installDefaultData')) {
            $this->processInstallDefaultData();
        } elseif (Tools::getValue('populateDatabase') && !empty($this->session->process_validated['installDatabase'])) {
            $this->processPopulateDatabase();
        } elseif (Tools::getValue('configureShop') && !empty($this->session->process_validated['populateDatabase'])) {
            $this->processConfigureShop();
        } elseif (Tools::getValue('installFixtures') && !empty($this->session->process_validated['configureShop'])) {
            $this->processInstallFixtures();
        } elseif (Tools::getValue('installModules') && (!empty($this->session->process_validated['installFixtures']) || $this->session->install_type != 'full')) {
            $this->processInstallModules();
        } elseif (Tools::getValue('installModulesAddons') && !empty($this->session->process_validated['installModules'])) {
            $this->processInstallAddonsModules();
        } elseif (Tools::getValue('installTheme') && !empty($this->session->process_validated['installModulesAddons'])) {
            $this->processInstallTheme();
        } elseif (Tools::getValue('sendEmail') && !empty($this->session->process_validated['installTheme'])) {
            $this->processSendEmail();
        } else {
            // With no parameters, we consider that we are doing a new install, so session where the last process step
            // was stored can be cleaned
            if (Tools::getValue('restart')) {
                $this->session->process_validated = array();
                $this->session->database_clear = true;
                if (Tools::getSafeModeStatus()) {
                    $this->session->safe_mode = true;
                }
            } elseif (!Tools::getValue('submitNext')) {
                $this->session->step = 'configure';
                $this->session->last_step = 'configure';
                Tools::redirect('index.php');
            }
        }
    }

    /**
     * PROCESS : generateSettingsFile
     */
    public function processGenerateSettingsFile()
    {
        $success = $this->model_install->generateSettingsFile(
            $this->session->database_server,
            $this->session->database_login,
            $this->session->database_password,
            $this->session->database_name,
            $this->session->database_prefix,
            $this->session->database_engine
        );

        if (!$success) {
            $this->ajaxJsonAnswer(false);
        }
        $this->session->process_validated = array_merge($this->session->process_validated, array('generateSettingsFile' => true));
        $this->ajaxJsonAnswer(true);
    }

    /**
     * PROCESS : installDatabase
     * Create database structure
     */
    public function processInstallDatabase()
    {
        if (!$this->model_install->installDatabase($this->session->database_clear) || $this->model_install->getErrors()) {
            $this->ajaxJsonAnswer(false, $this->model_install->getErrors());
        }
        $this->session->process_validated = array_merge($this->session->process_validated, array('installDatabase' => true));
        $this->ajaxJsonAnswer(true);
    }

    /**
     * PROCESS : installDefaultData
     * Create default shop and languages
     */
    public function processInstallDefaultData()
    {
        // @todo remove true in populateDatabase for 1.5.0 RC version
        $result = $this->model_install->installDefaultData($this->session->shop_name, $this->session->shop_country, false, true);

        if (!$result || $this->model_install->getErrors()) {
            $this->ajaxJsonAnswer(false, $this->model_install->getErrors());
        }
        $this->ajaxJsonAnswer(true);
    }

    /**
     * PROCESS : populateDatabase
     * Populate database with default data
     */
    public function processPopulateDatabase()
    {
        $this->initializeContext();

        $this->model_install->xml_loader_ids = $this->session->xml_loader_ids;
        $result = $this->model_install->populateDatabase(Tools::getValue('entity'));
        if (!$result || $this->model_install->getErrors()) {
            $this->ajaxJsonAnswer(false, $this->model_install->getErrors());
        }
        $this->session->xml_loader_ids = $this->model_install->xml_loader_ids;
        $this->session->process_validated = array_merge($this->session->process_validated, array('populateDatabase' => true));
        $this->ajaxJsonAnswer(true);
    }

    /**
     * PROCESS : configureShop
     * Set default shop configuration
     */
    public function processConfigureShop()
    {
        $this->initializeContext();

        $success = $this->model_install->configureShop(array(
            'shop_name' => $this->session->shop_name,
            'shop_activity' => $this->session->shop_activity,
            'shop_country' => $this->session->shop_country,
            'shop_timezone' => $this->session->shop_timezone,
            'admin_firstname' => $this->session->admin_firstname,
            'admin_lastname' => $this->session->admin_lastname,
            'admin_password' => $this->session->admin_password,
            'admin_email' => $this->session->admin_email,
            'marketing_consent' => $this->session->marketing_consent,
            'send_informations' => $this->session->send_informations,
            'configuration_agrement' => $this->session->configuration_agrement,
            'rewrite_engine' => $this->session->rewrite_engine,
        ));

        if (!$success || $this->model_install->getErrors()) {
            $this->ajaxJsonAnswer(false, $this->model_install->getErrors());
        }

        $this->session->process_validated = array_merge($this->session->process_validated, array('configureShop' => true));
        $this->ajaxJsonAnswer(true);
    }

    /**
     * PROCESS : installModules
     * Install all modules in ~/modules/ directory
     */
    public function processInstallModules()
    {
        $this->initializeContext();

        // code to populate database of modules
        if ($this->session->install_type == 'full') {
            $populateData = 1;
        } else {
            $populateData = 0;
        }
        // extra parameter sent to populate module data or not
        $result = $this->model_install->installModules(
            Tools::getValue('module'),
            $populateData
        );

        if (!$result || $this->model_install->getErrors()) {
            $this->ajaxJsonAnswer(false, $this->model_install->getErrors());
        }
        $this->session->process_validated = array_merge($this->session->process_validated, array('installModules' => true));
        $this->ajaxJsonAnswer(true);
    }

    /**
     * PROCESS : installModulesAddons
     * Install modules from addons
     */
    public function processInstallAddonsModules()
    {
        $this->initializeContext();
        if (($module = Tools::getValue('module')) && $id_module = Tools::getValue('id_module')) {
            $result = $this->model_install->installModulesAddons(array('name' => $module, 'id_module' => $id_module));
        } else {
            $result = $this->model_install->installModulesAddons();
        }
        if (!$result || $this->model_install->getErrors()) {
            $this->ajaxJsonAnswer(false, $this->model_install->getErrors());
        }
        $this->session->process_validated = array_merge($this->session->process_validated, array('installModulesAddons' => true));
        $this->ajaxJsonAnswer(true);
    }

    /**
     * PROCESS : installFixtures
     * Install fixtures (E.g. demo products)
     */
    public function processInstallFixtures()
    {
        $this->initializeContext();

        $this->model_install->xml_loader_ids = $this->session->xml_loader_ids;
        if (!$this->model_install->installFixtures(Tools::getValue('entity', null), array('shop_activity' => $this->session->shop_activity, 'shop_country' => $this->session->shop_country)) || $this->model_install->getErrors()) {
            $this->ajaxJsonAnswer(false, $this->model_install->getErrors());
        }
        $this->session->xml_loader_ids = $this->model_install->xml_loader_ids;
        $this->session->process_validated = array_merge($this->session->process_validated, array('installFixtures' => true));
        $this->ajaxJsonAnswer(true);
    }

    /**
     * PROCESS : installTheme
     * Install theme
     */
    public function processInstallTheme()
    {
        $this->initializeContext();

        $this->model_install->installTheme();
        if ($this->model_install->getErrors()) {
            $this->ajaxJsonAnswer(false, $this->model_install->getErrors());
        }

        $this->session->process_validated = array_merge($this->session->process_validated, array('installTheme' => true));
        $this->ajaxJsonAnswer(true);
    }

    /**
     * @see InstallAbstractModel::display()
     */
    public function display()
    {
        // The installer SHOULD take less than 32M, but may take up to 35/36M sometimes. So 42M is a good value :)
        $low_memory = Tools::getMemoryLimit() < Tools::getOctets('42M');

        // We fill the process step used for Ajax queries
        $this->process_steps[] = array('key' => 'generateSettingsFile', 'lang' => $this->l('Create settings.inc file'));
        $this->process_steps[] = array('key' => 'installDatabase', 'lang' => $this->l('Create database tables'));
        $this->process_steps[] = array('key' => 'installDefaultData', 'lang' => $this->l('Create default website and languages'));

        // If low memory, create subtasks for populateDatabase step (entity per entity)
        $populate_step = array('key' => 'populateDatabase', 'lang' => $this->l('Populate database tables'));
        if ($low_memory) {
            $populate_step['subtasks'] = array();
            $xml_loader = new InstallXmlLoader();
            foreach ($xml_loader->getSortedEntities() as $entity) {
                $populate_step['subtasks'][] = array('entity' => $entity);
            }
        }

        $this->process_steps[] = $populate_step;
        $this->process_steps[] = array('key' => 'configureShop', 'lang' => $this->l('Configure website information'));

        if ($this->session->install_type == 'full') {
            // If low memory, create subtasks for installFixtures step (entity per entity)
            $fixtures_step = array('key' => 'installFixtures', 'lang' => $this->l('Install demonstration data'));
            if ($low_memory) {
                $fixtures_step['subtasks'] = array();
                $xml_loader = new InstallXmlLoader();
                $xml_loader->setFixturesPath();
                foreach ($xml_loader->getSortedEntities() as $entity) {
                    $fixtures_step['subtasks'][] = array('entity' => $entity);
                }
            }
            $this->process_steps[] = $fixtures_step;
        }

        $install_modules = array('key' => 'installModules', 'lang' => $this->l('Install modules'));
        if ($low_memory) {
            foreach ($this->model_install->getModulesList() as $module) {
                $install_modules['subtasks'][] = array('module' => $module);
            }
        }
        $this->process_steps[] = $install_modules;

        $install_modules = array('key' => 'installModulesAddons', 'lang' => $this->l('Install Addons modules'));

        $params = array(
            'iso_lang' => $this->language->getLanguageIso(),
            'iso_country' => $this->session->shop_country,
            'email' => $this->session->admin_email,
            'shop_url' => Tools::getHttpHost(),
            'version' => _PS_INSTALL_VERSION_
        );

        if ($low_memory) {
            foreach ($this->model_install->getAddonsModulesList($params) as $module) {
                $install_modules['subtasks'][] = array('module' => (string)$module['name'], 'id_module' => (string)$module['id_module']);
            }
        }
        $this->process_steps[] = $install_modules;

        $this->process_steps[] = array('key' => 'installTheme', 'lang' => $this->l('Install theme'));

        $this->displayTemplate('process');
    }
}
