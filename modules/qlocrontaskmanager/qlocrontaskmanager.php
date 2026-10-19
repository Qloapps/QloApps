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

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/classes/QctmRequiredClasses.php';

class QloCronTaskManager extends Module
{
    public function __construct()
    {
        $this->name = 'qlocrontaskmanager';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Webkul';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Cron Task Manager');
        $this->description = $this->l('A centralized cron scheduling system where a single server cron entry manages all module tasks.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module? All cron task data will be lost.');
    }


    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitCronTaskManagerSettings')) {
            $output .= $this->processSaveSettings();
        }

        if (Tools::isSubmit('regenerateToken')) {
            Configuration::updateValue('QCTM_CRON_TASK_MANAGER_TOKEN', Tools::passwdGen(32));
            $output .= $this->displayConfirmation($this->l('Token has been regenerated. Update your server cron entry with the new URL.'));
        }

        if(Tools::getValue('module_name') === $this->name) {
            $this->context->controller->addCSS($this->_path . 'views/css/admin/qlo_cron_task_manager_admin.css');
            $this->context->controller->addJS($this->_path . 'views/js/admin/qlo_cron_task_manager_admin.js');
        }

        $output .= $this->renderCronCommandPanel();
        $output .= $this->renderSettingsForm();

        return $output;
    }

    protected function processSaveSettings()
    {
        $logRetention = Tools::getValue('QCTM_CRON_TASK_MANAGER_LOG_RETENTION_DAYS');

        if(!Validate::isUnsignedInt($logRetention)) {
            return $this->displayError($this->l('Log retention must be an integer value.'));
        }

        if ($logRetention < 1 || $logRetention > 365) {
            return $this->displayError($this->l('Log retention must be between 1 and 365 days.'));
        }

        Configuration::updateValue('QCTM_CRON_TASK_MANAGER_LOG_RETENTION_DAYS', $logRetention);

        return $this->displayConfirmation($this->l('Settings saved successfully.'));
    }

    protected function renderCronCommandPanel()
    {
        $token = Configuration::get('QCTM_CRON_TASK_MANAGER_TOKEN');
        $cronUrl = Tools::getShopDomainSsl(true) . __PS_BASE_URI__
            . 'modules/qlocrontaskmanager/cron.php?token=' . $token;
        $cronCommand = '* * * * * curl -s "' . $cronUrl . '"';

        $this->context->smarty->assign(array(
            'cronCommand' => $cronCommand,
        ));

        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }

    protected function renderSettingsForm()
    {
        $token = Configuration::get('QCTM_CRON_TASK_MANAGER_TOKEN');

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        $helper->fields_value = array(
            'QCTM_CRON_TASK_MANAGER_TOKEN' => $token,
            'QCTM_CRON_TASK_MANAGER_LOG_RETENTION_DAYS' => (int) Configuration::get('QCTM_CRON_TASK_MANAGER_LOG_RETENTION_DAYS'),
        );

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Secure Token'),
                        'name' => 'QCTM_CRON_TASK_MANAGER_TOKEN',
                        'readonly' => true,
                        'col' => 6,
                        'desc' => $this->l('This token secures the cron endpoint. After regenerating it, you will need to update your server cron entry.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Log Retention (days)'),
                        'name' => 'QCTM_CRON_TASK_MANAGER_LOG_RETENTION_DAYS',
                        'col' => 2,
                        'required' => true,
                        'validation' => 'isUnsignedInt',
                        'suffix' => $this->l('days'),
                        'desc' => $this->l('Number of days to keep execution logs.'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitCronTaskManagerSettings',
                ),
                'buttons' => array(
                    array(
                        'title' => $this->l('Regenerate Token'),
                        'icon' => 'process-icon-refresh',
                        'name' => 'regenerateToken',
                        'type' => 'submit',
                        'class' => 'btn btn-default',
                    ),
                ),
            ),
        );

        return $helper->generateForm(array($fields_form));
    }

    public function hookActionModuleInstallAfter($params)
    {
        if (!isset($params['object']) || !($params['object'] instanceof Module)) {
            return;
        }

        $module = $params['object'];

        if (method_exists($module, 'hookRegisterCronTasks')) {
            $tasks = $module->hookRegisterCronTasks();
            if (!empty($tasks) && is_array($tasks)) {
                $objCronTask = new QctmCronTask();
                $objCronTask->registerTasksForModule($module->id, $tasks);
            }
        }
    }

    public function hookDashboardZoneThree($params)
    {
        if ($this->isCrontabConfigured() !== false) {
            return '';
        }

        $this->context->smarty->assign(array(
            'qctmConfigureLink' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name,
        ));

        return $this->display(__FILE__, 'views/templates/admin/dashboard/qctm_cron_not_configured.tpl');
    }

    public function isCrontabConfigured()
    {
        if (!function_exists('shell_exec')) {
            return null;
        }

        $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));
        if (in_array('shell_exec', $disabled, true)) {
            return null;
        }

        $output = @shell_exec('crontab -l 2>/dev/null');

        return strpos((string) $output, 'qlocrontaskmanager/cron.php') !== false;
    }

    public function hookActionModuleUninstallBefore($params)
    {
        if (!isset($params['object']) || !($params['object'] instanceof Module) || $params['object']->id == $this->id) {
            return;
        }

        QctmCronTask::deleteByModule($params['object']->id);
    }

    /**
     * Called by the system log-cleanup cron task (daily at midnight).
     */
    public function runLogCleanup()
    {
        $days = (int) Configuration::get('QCTM_CRON_TASK_MANAGER_LOG_RETENTION_DAYS');
        if ($days > 0) {
            QctmCronTaskLog::deleteOlderThan($days);
        }

    }

    public function install()
    {
        $db = new QctmCronTaskManagerDb();
        $objCronTask = new QctmCronTask();

        if (!parent::install()
            || !$this->registerHooks()
            || !$db->createTables()
            || !$db->installDefaultData()
            || !$this->installTabs()
            || !$this->installDefaultTasks()
        ) {
            return false;
        }

        $objCronTask->syncAllExistingModules();

        return true;
    }

    public function registerHooks()
    {
        return $this->registerHook(
            array(
                'actionModuleInstallAfter',
                'actionModuleUninstallBefore',
                'dashboardZoneThree',
            )
        );
    }

    public function uninstall()
    {
        $db = new QctmCronTaskManagerDb();

        if (!$this->uninstallTabs()
            || !$db->deleteConfigurations()
            || !$db->dropTables()
            || !parent::uninstall()
        ) {
            return false;
        }

        return true;
    }

    protected function installDefaultTasks()
    {
        if (QctmCronTask::getByModuleAndName($this->id, 'log_cleanup')) {
            return true;
        }

        $task = new QctmCronTask();
        $task->id_module = (int) $this->id;
        $task->task_name = 'log_cleanup';
        $task->description = 'Automatic log cleanup';
        $task->cron_expression = '0 0 * * *';
        $task->callback = 'runLogCleanup';
        $task->active = 1;
        $task->is_system = 1;

        return $task->save();
    }

    protected function installTabs()
    {
        $tabCronTasks = new Tab();
        $tabCronTasks->class_name = 'AdminCronTaskManager';
        $tabCronTasks->id_parent = (int) Tab::getIdFromClassName('AdminTools');
        $tabCronTasks->module = $this->name;
        $tabCronTasks->position = Tab::getNewLastPosition($tabCronTasks->id_parent);

        foreach (Language::getLanguages(false) as $language) {
            $tabCronTasks->name[$language['id_lang']] = $this->l('Cron Task Manager');
        }

        if (!$tabCronTasks->add()) {
            return false;
        }

        // Hidden tab — accessible via URL but not shown in navigation menu
        $tabLogs = new Tab();
        $tabLogs->class_name = 'AdminCronTaskLogs';
        $tabLogs->id_parent = -1;
        $tabLogs->module = $this->name;

        foreach (Language::getLanguages(false) as $language) {
            $tabLogs->name[$language['id_lang']] = $this->l('Cron Task Logs');
        }

        return $tabLogs->add();
    }

    protected function uninstallTabs()
    {
        foreach (array('AdminCronTaskManager', 'AdminCronTaskLogs') as $className) {
            $idTab = (int) Tab::getIdFromClassName($className);
            if ($idTab) {
                $tab = new Tab($idTab);
                if (!$tab->delete()) {
                    return false;
                }
            }
        }

        return true;
    }
}
