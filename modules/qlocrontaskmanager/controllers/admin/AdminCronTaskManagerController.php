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

class AdminCronTaskManagerController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->list_no_link = true;
        $this->table = 'qctm_cron_task';
        $this->className = 'QctmCronTask';
        $this->identifier = 'id_cron_task';
        $this->_orderBy = 'id_cron_task';

        $this->lang = false;
        $this->_orderWay = 'ASC';
        
        parent::__construct();
        // Exclude hidden system tasks from the listing
        $this->_where = ' AND a.`is_system` = 0';

        $this->_join = 'LEFT JOIN (
                SELECT ctl.`id_cron_task`, ctl.`date_add` AS `last_run`, ctl.`status` AS `last_status`
                FROM `' . _DB_PREFIX_ . 'qctm_cron_task_log` ctl
                INNER JOIN (
                    SELECT `id_cron_task`, MAX(`id_cron_task_log`) AS `max_id`
                    FROM `' . _DB_PREFIX_ . 'qctm_cron_task_log`
                    GROUP BY `id_cron_task`
                ) latest ON ctl.`id_cron_task_log` = latest.`max_id`
            ) AS llog ON (llog.`id_cron_task` = a.`id_cron_task`)
            LEFT JOIN `' . _DB_PREFIX_ . 'module` m ON (m.`id_module` = a.`id_module`)';

        $this->_select = 'm.`name` as `module_name`, llog.`last_run`, llog.`last_status`, a.`cron_expression` AS `next_run_expression`';

        $this->addRowAction('viewLogs');
        $this->addRowAction('edit');
        $this->addRowAction('resetModuleCrons');

        $this->bulk_actions = array(
            'enableSelection' => array(
                'text' => $this->l('Enable selected'),
                'icon' => 'icon-power-off text-success',
            ),
            'disableSelection' => array(
                'text' => $this->l('Disable selected'),
                'icon' => 'icon-power-off text-danger',
            ),
        );

        $this->fields_list = array(
            'id_cron_task' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'module_name' => array(
                'title' => $this->l('Module Name'),
                'filter_key' => 'm!name',
                'order_key' => 'm!name',
                'callback' => 'displayModuleName',
            ),
            'task_name' => array(
                'title' => $this->l('Task Name'),
                'filter_key' => 'a!task_name',
            ),
            'cron_expression' => array(
                'title' => $this->l('Cron Expression'),
                'align' => 'center',
                'class' => 'fixed-width-lg',
                'search' => false,
                'callback' => 'getCronWithReadable',
            ),
            'last_run' => array(
                'title' => $this->l('Last Run'),
                'align' => 'center',
                'filter' => false,
                'search' => false,
            ),
            'last_status' => array(
                'title' => $this->l('Last Status'),
                'align' => 'center',
                'filter' => false,
                'search' => false,
                'callback' => 'getLastStatus',
            ),
            'next_run_expression' => array(
                'title' => $this->l('Next Run'),
                'align' => 'center',
                'filter' => false,
                'search' => false,
                'callback' => 'getNextRun',
            ),
            'date_add' => array(
                'title' => $this->l('Added Time'),
                'align' => 'center',
                'filter' => false,
                'search' => false,
            ),
            'active' => array(
                'title' => $this->l('Active'),
                'active' => 'status',
                'type' => 'bool',
                'align' => 'center',
                'class' => 'fixed-width-sm',
                'filter_key' => 'a!active',
            ),
        );

    }

    public function init()
    {
        parent::init();

        if(!Validate::isUnsignedInt($idCronTask = Tools::getValue('id_cron_task')) && $this->display == 'edit') {
            $this->errors = array($this->l('Invalid Cron Task ID'));
            $this->display = 'list';
        }else{
            if (Validate::isUnsignedInt($idCronTask = Tools::getValue('id_cron_task')) && Validate::isLoadedObject($objCronTask = new QctmCronTask($idCronTask)) && $this->display == 'edit' ) {
                $this->toolbar_title = $this->l('Edit Cron Task: ') . $objCronTask->task_name;
            }
        }

    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
        if($this->display  &&  $this->display == 'list') {
            $this->page_header_toolbar_btn['view_all_logs'] = array(
                'href' => $this->context->link->getAdminLink('AdminCronTaskLogs'),
                'desc' => $this->l('View All Logs'),
                'icon' => 'icon-history',
            );
        }else if($this->display  &&  $this->display == 'edit') {
            $idCronTask = Tools::getValue('id_cron_task');
            $this->page_header_toolbar_btn['view_logs'] = array(
                'href' => $this->context->link->getAdminLink('AdminCronTaskLogs') . '&id_cron_task=' . (int) $idCronTask,
                'desc' => $this->l('View Logs'),
                'icon' => 'icon-history',
            );
        }
    }

  

    public function renderForm()
    {
        $cronExpression = '* * * * *';
        if(Validate::isLoadedObject($this->object)){
            $cronExpression = $this->object->cron_expression;
        }

        $isValidExpression = \Cron\CronExpression::isValidExpression($cronExpression);
        if ($isValidExpression) {
            $readable = $this->l('Runs ') . (new QctmCronExpressionTranslator())->getCronExpressionTranslation($cronExpression);
        } else {
            $readable = $this->l('Invalid cron expression.');
        }

        $moduleDisplayName = '';
        if (Validate::isLoadedObject($this->object) && $this->object->id_module) {
            $module = Module::getInstanceById($this->object->id_module);
            $moduleDisplayName = ($module && $module->displayName) ? $module->displayName : '#' . (int) $this->object->id_module;
        }
        $this->fields_value['module_display_name'] = $moduleDisplayName;

        $this->context->smarty->assign(array(
            'qctmCronExpression' => $cronExpression,
            'qctmCronReadable'   => $readable,
            'qctmAjaxUrl'        => $this->context->link->getAdminLink($this->controller_name) . '&ajax=1',
        ));

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Cron Task'),
                'icon' => 'icon-time',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Module Name'),
                    'name' => 'module_display_name',
                    'disabled' => true,
                    'col' => 5,
                    'desc' => $this->l('The module that owns this task.'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Task Name'),
                    'name' => 'task_name',
                    'disabled' => true,
                    'required' => true,
                    'col' => 5,
                    'hint' => $this->l('e.g. send_daily_report'),
                    'desc' => $this->l('A unique identifier for this task within the module.'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Description'),
                    'name' => 'description',
                    'required' => true,
                    'disabled' => true,
                    'col' => 7,
                    'hint' => $this->l('e.g. Sends the daily booking summary report'),
                    'desc' => $this->l('A short human-readable description of what this task does.'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Cron Expression'),
                    'name' => 'cron_expression',
                    'required' => true,
                    'col' => 4,
                    'class' => 'qctm-cron-expression',
                    'hint' => $this->l('e.g. 0 0 * * *'),
                    'desc' => $readable,
                ),
                array(
                    'type' => 'html',
                    'name' => 'qctm_cron_helper',
                    'html_content' => $this->createTemplate('qctm_cron_expression_helper.tpl')->fetch(),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'required' => false,
                    'desc' => $this->l('Disabled tasks are skipped.'),
                    'values' => array(
                        array('id' => 'active_on', 'value' => 1, 'label' => $this->l('Enabled')),
                        array('id' => 'active_off', 'value' => 0, 'label' => $this->l('Disabled')),
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );
        return parent::renderForm();
    }

    public function ajaxProcessGetCronReadable()
    {
        $expression = Tools::getValue('cron_expression');
        $result = array('readable' => $this->l('Invalid cron expression'), 'valid' => false);

        if (\Cron\CronExpression::isValidExpression($expression)) {
            $result['valid'] = true;
            $result['readable'] = $this->l('Runs ') . (new QctmCronExpressionTranslator())->getCronExpressionTranslation($expression);
        }

        die(Tools::jsonEncode($result));
    }

    public function processSave()
    {
        if (!$this->validateCronExpression()) {
            $this->display = 'edit';
            return false;
        }

        return parent::processSave();
    }

    protected function validateCronExpression()
    {
        $cronExpression = Tools::getValue('cron_expression');

        if (!\Cron\CronExpression::isValidExpression($cronExpression)) {
            $this->errors[] = $this->l('Invalid cron expression. Must have exactly 5 parts: minute hour day month weekday. Example: * * * * *');
            return false;
        }

        return true;
    }

    public function processStatus()
    {
        return parent::processStatus();
    }

    protected function processBulkEnableSelection()
    {
        return parent::processBulkEnableSelection();
    }

    protected function processBulkDisableSelection()
    {
        return parent::processBulkDisableSelection();
    }

    public function displayViewLogsLink($token = null, $id = null, $row = array())
    {
        $this->context->smarty->assign(
            'viewLogsLink',
            $this->context->link->getAdminLink('AdminCronTaskLogs') . '&id_cron_task=' . (int) $id
        );
        return $this->createTemplate('qctm_view_logs_link.tpl')->fetch();
    }

    public function displayResetModuleCronsLink($token = null, $id = null, $row = array())
    {
        $this->context->smarty->assign(
            'resetLink',
            $this->context->link->getAdminLink('AdminCronTaskManager')
                . '&action=resetModuleCrons&' . $this->identifier . '=' . (int) $id
        );
        return $this->createTemplate('qctm_reset_module_crons_link.tpl')->fetch();
    }

    public function processResetModuleCrons()
    {
        $idCronTask = (int) Tools::getValue($this->identifier);
        $cronTask = new QctmCronTask($idCronTask);

        if (!Validate::isLoadedObject($cronTask)) {
            $this->errors[] = $this->l('Cron task not found.');
            return false;
        }

        $module = Module::getInstanceById($cronTask->id_module);

        if (!$module) {
            $this->errors[] = $this->l('Module not found.');
            return false;
        }

        if (!method_exists($module, 'hookRegisterCronTasks')) {
            $this->errors[] = $this->l('Module does not define any cron tasks.');
            return false;
        }

        $tasks = $module->hookRegisterCronTasks();

        if (empty($tasks) || !is_array($tasks)) {
            $this->errors[] = $this->l('No tasks returned by module.');
            return false;
        }

        foreach ($tasks as $task) {
            if ($task['name'] === $cronTask->task_name) {
                if (!$cronTask->validateTask($module, $task)) {
                    $this->errors[] = $this->l('Module returned an invalid task definition (bad cron expression or missing callback).');
                    return false;
                }

                $cronTask->cron_expression = $task['cron'];
                $cronTask->description = $task['description'];
                $cronTask->callback = $task['callback'];

                if (!$cronTask->save()) {
                    $this->errors[] = $this->l('Failed to reset cron task.');
                    return false;
                }

                $this->confirmations[] = $this->l('Cron task reset to module defaults successfully.');
                return true;
            }
        }

        $this->errors[] = $this->l('Task definition not found in module.');
        return false;
    }

    public function getCronWithReadable($cronExpression, $row)
    {
        $readable = \Cron\CronExpression::isValidExpression($cronExpression)
            ? (new QctmCronExpressionTranslator())->getCronExpressionTranslation($cronExpression)
            : $cronExpression;

        return '<code>' . htmlspecialchars($cronExpression) . '</code>'
            . '<br><small class="text-muted">' . htmlspecialchars($readable) . '</small>';
    }

    public function displayModuleName($moduleName, $row)
    {
        $cacheKey = 'displayModuleName_' . $moduleName;

        if (!Cache::isStored($cacheKey)) {
            $module = Module::getInstanceByName($moduleName);
            $displayName = ($module && $module->displayName) ? $module->displayName : $moduleName;
            Cache::store($cacheKey, $displayName);
        }

        return Cache::retrieve($cacheKey);
    }

    public function getNextRun($cronExpression, $row)
    {
        if (!$cronExpression || !\Cron\CronExpression::isValidExpression($cronExpression)) {
            return '-';
        }
        
        try {
            $next = \Cron\CronExpression::factory($cronExpression)->getNextRunDate();
            return $next->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return '-';
        }
    }

    public function getLastStatus($lastStatus, $row)
    {
        $this->context->smarty->assign('status', $lastStatus);
        return $this->createTemplate('qctm_badge_status.tpl')->fetch();
    }


    public function setMedia()
    {
        parent::setMedia();

        Media::addJsDef(
            array(
                'INVALID_CRON_EXPRESSION' => $this->l('Invalid cron expression.'),
            )
        );
        $this->addCSS($this->module->getPathUri() . 'views/css/admin/qlo_cron_task_manager_admin.css');
        $this->addJS($this->module->getPathUri() . 'views/js/admin/qlo_cron_task_manager_admin.js');
    }
}
