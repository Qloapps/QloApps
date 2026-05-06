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

include_once 'define.php';

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
        $this->description = $this->l('Centralized cron scheduling system. One server cron entry manages all module tasks.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall? All cron task data will be lost.');
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

        $this->context->controller->addCSS($this->_path . 'views/css/admin/qlo_cron_task_manager_admin.css');
        $this->context->controller->addJS($this->_path . 'views/js/admin/qlo_cron_task_manager_admin.js');

        $output .= $this->renderCronCommandPanel();
        $output .= $this->renderSettingsForm();

        return $output;
    }

    protected function processSaveSettings()
    {
        $logRetention = (int) Tools::getValue('QCTM_CRON_TASK_MANAGER_LOG_RETENTION_DAYS');

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
                        'desc' => $this->l('This token secures the cron endpoint. Regenerating it will require updating your server cron entry.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Log Retention (days)'),
                        'name' => 'QCTM_CRON_TASK_MANAGER_LOG_RETENTION_DAYS',
                        'col' => 2,
                        'required' => true,
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
                $this->registerTasksForModule($module->name, $tasks);
            }
        }
    }

    public function hookActionModuleUninstallBefore($params)
    {
        if (!isset($params['object']) || !($params['object'] instanceof Module)) {
            return;
        }

        QctmCronTask::deleteByModule($params['object']->name);
    }

    protected function syncAllExistingModules()
    {
        $allTasks = Hook::exec('registerCronTasks', array(), null, true);

        if (!empty($allTasks) && is_array($allTasks)) {
            foreach ($allTasks as $moduleName => $tasks) {
                if (!empty($tasks) && is_array($tasks)) {
                    $this->registerTasksForModule($moduleName, $tasks);
                }
            }
        }
    }

    /**
     * Register tasks for a specific module after validation
     *
     * @param string $moduleName
     * @param array $tasks
     */
    protected function registerTasksForModule($moduleName, $tasks)
    {
        foreach ($tasks as $task) {
            if (!$this->validateTask($moduleName, $task)) {
                continue;
            }

            if (QctmCronTask::getByModuleAndName($moduleName, $task['name'])) {
                continue;
            }

            $cronTask = new QctmCronTask();
            $cronTask->module_name = $moduleName;
            $cronTask->task_name = $task['name'];
            $cronTask->description = $task['description'];
            $cronTask->cron_expression = $task['cron'];
            $cronTask->callback = $task['callback'];
            $cronTask->active = 1;
            if (!$cronTask->save()) {
                PrestaShopLogger::addLog(
                    'QloCronTaskManager: failed to register task "' . $task['name'] . '" for module "' . $moduleName . '"',
                    3
                );
            }
        }
    }

    /**
     * Validate a task definition array
     *
     * @param string $moduleName
     * @param array $task
     * @return bool
     */
    protected function validateTask($moduleName, $task)
    {
        if (empty($task['name']) || empty($task['description'])
            || empty($task['cron']) || empty($task['callback'])) {
            return false;
        }

        if (!\Cron\CronExpression::isValidExpression($task['cron'])) {
            return false;
        }

        $module = Module::getInstanceByName($moduleName);
        if (!$module || !method_exists($module, $task['callback'])) {
            return false;
        }

        return true;
    }

    public function dispatch()
    {
        $now = new DateTime();
        $tasks = QctmCronTask::getActiveTasks();

        if (empty($tasks)) {
            return;
        }

        foreach ($tasks as $task) {
            $cron = \Cron\CronExpression::factory($task['cron_expression']);

            if (!$cron->isDue($now)) {
                continue;
            }

            $module = Module::getInstanceByName($task['module_name']);

            if (!$module || !$module->active) {
                continue;
            }

            $callback = $task['callback'];

            if (!method_exists($module, $callback)) {
                QctmCronTaskLog::addLog(
                    $task['id_cron_task'],
                    QctmCronTaskLog::QCTM_CRON_TASK_LOG_STATUS_ERROR,
                    'Callback method "' . $callback . '" not found on module "' . $task['module_name'] . '"'
                );
                continue;
            }

            $startTime = microtime(true);

            try {
                $module->{$callback}();
                $executionTime = round(microtime(true) - $startTime, 4);

                QctmCronTaskLog::addLog(
                    $task['id_cron_task'],
                    QctmCronTaskLog::QCTM_CRON_TASK_LOG_STATUS_SUCCESS,
                    null,
                    $executionTime
                );
            } catch (\Throwable $e) {
                $executionTime = round(microtime(true) - $startTime, 4);

                QctmCronTaskLog::addLog(
                    $task['id_cron_task'],
                    QctmCronTaskLog::QCTM_CRON_TASK_LOG_STATUS_ERROR,
                    $e->getMessage(),
                    $executionTime
                );
            }
        }

        $this->cleanOldLogs();
    }

    public function cleanOldLogs()
    {
        $lastRun = (int) Configuration::get('QCTM_LOG_CLEANUP_LAST_RUN');

        if ($lastRun && (time() - $lastRun) < 86400) {
            return;
        }

        $days = (int) Configuration::get('QCTM_CRON_TASK_MANAGER_LOG_RETENTION_DAYS');
        if ($days > 0) {
            QctmCronTaskLog::deleteOlderThan($days);
        }

        Configuration::updateValue('QCTM_LOG_CLEANUP_LAST_RUN', time());
    }

    public function install()
    {
        $db = new QctmCronTaskManagerDb();

        if (!parent::install()
            || !$this->registerHook('actionModuleInstallAfter')
            || !$this->registerHook('actionModuleUninstallBefore')
            || !$db->createTables()
            || !$db->installDefaultData()
            || !$this->installTabs()
        ) {
            return false;
        }

        $this->syncAllExistingModules();

        return true;
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

    protected function installTabs()
    {
        $tab = new Tab();
        $tab->class_name = 'AdminCronTaskManager';
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminTools');
        $tab->module = $this->name;
        $tab->position = Tab::getNewLastPosition($tab->id_parent);

        foreach (Language::getLanguages(false) as $language) {
            $tab->name[$language['id_lang']] = $this->l('Cron Task Manager');
        }

        return $tab->add();
    }

    protected function uninstallTabs()
    {
        $idTab = (int) Tab::getIdFromClassName('AdminCronTaskManager');
        if ($idTab) {
            $tab = new Tab($idTab);
            if (!$tab->delete()) {
                return false;
            }
        }

        return true;
    }
}
