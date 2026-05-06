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
    protected $showLogs = false;

    protected function useTaskContext()
    {
        $this->table = 'qctm_cron_task';
        $this->className = 'QctmCronTask';
        $this->identifier = 'id_cron_task';
    }

    public function __construct()
    {
        $this->bootstrap = true;

        parent::__construct();

        if (Tools::getValue('id_cron_task')) {
            $this->showLogs = true;
            $this->initLogList();
        } else {
            $this->initTaskList();
        }
    }

    public function init()
    {
        parent::init();
        if (Tools::getValue('id_cron_task')) {
            self::$currentIndex .= '&id_cron_task=' . (int) Tools::getValue('id_cron_task');
            $this->toolbar_title = $this->l('Cron Task Logs');
            $this->page_header_toolbar_btn['back-btn'] = array(
                'href' => $this->getBackButtonLink(),
                'icon' => 'process-icon-back',
                'desc' => $this->l('Back To Tasks'),
            );
        }
    }

    protected function getBackButtonLink()
    {
        $backLink = preg_replace('/([&?])id_cron_task=\d+(&)?/', '$1', self::$currentIndex);
        $backLink = rtrim($backLink, '&?');

        return $backLink . '&token=' . $this->token;
    }

    protected function initTaskList()
    {
        $this->useTaskContext();
        $this->lang = false;
        $this->_orderBy = 'id_cron_task';
        $this->_orderWay = 'ASC';
        $this->list_no_link = true;
        $this->_join = 'LEFT JOIN (
                SELECT ctl.`id_cron_task`, ctl.`date_add` AS `last_run`, ctl.`status` AS `last_status`
                FROM `' . _DB_PREFIX_ . 'qctm_cron_task_log` ctl
                INNER JOIN (
                    SELECT `id_cron_task`, MAX(`id_cron_task_log`) AS `max_id`
                    FROM `' . _DB_PREFIX_ . 'qctm_cron_task_log`
                    GROUP BY `id_cron_task`
                ) latest ON ctl.`id_cron_task_log` = latest.`max_id`
            ) AS llog ON (llog.`id_cron_task` = a.`id_cron_task`)';

        $this->_select = 'llog.`last_run`, llog.`last_status`';

        $this->addRowAction('view');

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
                'title' => $this->l('Module'),
                'filter_key' => 'a!module_name',
            ),
            'description' => array(
                'title' => $this->l('Task'),
                'filter_key' => 'a!description',
            ),
            'cron_expression' => array(
                'title' => $this->l('Cron Expression'),
                'align' => 'center',
                'class' => 'fixed-width-lg',
                'search' => false,
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

    protected function initLogList()
    {
        $this->table = 'qctm_cron_task_log';
        $this->className = 'QctmCronTaskLog';
        $this->identifier = 'id_cron_task_log';
        $this->lang = false;
        $this->_orderBy = 'id_cron_task_log';
        $this->_orderWay = 'DESC';
        $this->list_no_link = true;

        $idCronTask = (int) Tools::getValue('id_cron_task');
        if ($idCronTask) {
            $this->_where = ' AND a.`id_cron_task` = ' . $idCronTask;
        }

        $this->_select = 'ct.`module_name`, ct.`description` as task_description';
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'qctm_cron_task` ct
                          ON (a.`id_cron_task` = ct.`id_cron_task`)';

        $cronTaskLogStatuses = QctmCronTaskLog::getStatuses();

        $this->fields_list = array(
            'id_cron_task_log' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'module_name' => array(
                'title' => $this->l('Module'),
                'filter_key' => 'ct!module_name',
            ),
            'task_description' => array(
                'title' => $this->l('Task'),
                'filter' => false,
                'search' => false,
            ),
            'status' => array(
                'title' => $this->l('Status'),
                'align' => 'center',
                'type' => 'select',
                'list' => $cronTaskLogStatuses,
                'filter_key' => 'a!status',
                'callback' => 'getStatusBadge',
            ),
            'error_message' => array(
                'title' => $this->l('Error Message'),
                'maxlength' => 80,
            ),
            'execution_time' => array(
                'title' => $this->l('Execution Time'),
                'align' => 'center',
                'suffix' => ' s',
                'class' => 'fixed-width-md',
            ),
            'date_add' => array(
                'title' => $this->l('Date'),
                'type' => 'datetime',
                'align' => 'right',
                'filter_key' => 'a!date_add',
            ),
        );
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }


    public function processStatus()
    {
        $this->showLogs = false;
        $this->useTaskContext();

        return parent::processStatus();
    }

    protected function processBulkEnableSelection()
    {
        $this->showLogs = false;
        $this->useTaskContext();

        return parent::processBulkEnableSelection();
    }

    protected function processBulkDisableSelection()
    {
        $this->showLogs = false;
        $this->useTaskContext();

        return parent::processBulkDisableSelection();
    }

    public function getLastStatus($lastStatus, $row)
    {
        $this->context->smarty->assign('status', $lastStatus);
        return $this->createTemplate('qctm_badge_status.tpl')->fetch();
    }

    public function getStatusBadge($status, $row)
    {
        $this->context->smarty->assign('status', $status);
        return $this->createTemplate('qctm_badge_status.tpl')->fetch();
    }

    public function displayViewlogsLink($token, $id, $name = null)
    {
        $logLink = self::$currentIndex.'&id_cron_task=' . (int) $id. '&token=' . $this->token;

        $this->context->smarty->assign('logLink', $logLink);

        return $this->createTemplate('qctm_view_log.tpl')->fetch();
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->addCSS($this->module->getPathUri() . 'views/css/admin/qlo_cron_task_manager_admin.css');
        $this->addJS($this->module->getPathUri() . 'views/js/admin/qlo_cron_task_manager_admin.js');
    }
}
