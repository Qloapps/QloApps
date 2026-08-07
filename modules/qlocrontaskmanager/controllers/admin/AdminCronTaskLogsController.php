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

class AdminCronTaskLogsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->list_no_link = true;
        $this->table = 'qctm_cron_task_log';
        $this->className = 'QctmCronTaskLog';
        $this->identifier = 'id_cron_task_log';

        parent::__construct();

        $this->initLogList();
    }

    public function init()
    {
        parent::init();

        $idCronTask = (int) Tools::getValue('id_cron_task');
        $prefix = $this->getCookieFilterPrefix();
        $cookieKey = $prefix . $this->table . 'Filter_ct!task_name';

        if ($idCronTask > 0) {
            $cronTask = new QctmCronTask($idCronTask);
            if (Validate::isLoadedObject($cronTask)) {
                $this->_where .= ' AND a.`id_cron_task` = ' . $idCronTask;
                $this->context->cookie->{$cookieKey} = $cronTask->task_name;
                $this->context->cookie->{'submitFilter' . $this->table} = 1;
                $this->toolbar_title = $this->l('Logs for: ') . $cronTask->task_name;
            } else {
                $this->toolbar_title = $this->l('Cron Task Logs');
            }
        } else {
            $this->context->cookie->{'submitFilter' . $this->table} = 0;
            $this->toolbar_title = $this->l('All Cron Task Logs');
        }

        $this->page_header_toolbar_btn['back_to_tasks'] = array(
            'href' => $this->context->link->getAdminLink('AdminCronTaskManager'),
            'icon' => 'process-icon-back',
            'desc' => $this->l('Back to Tasks'),
        );
    }

    protected function initLogList()
    {
        $this->lang = false;
        $this->_orderBy = 'id_cron_task_log';
        $this->_orderWay = 'DESC';

        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'qctm_cron_task` ct
                          ON (a.`id_cron_task` = ct.`id_cron_task`)
                        LEFT JOIN `' . _DB_PREFIX_ . 'module` m
                          ON (m.`id_module` = ct.`id_module`)';

        $this->_select = 'm.`name` AS `module_name`, ct.`task_name`, ct.`description` AS task_description';

        $cronTaskLogStatuses = QctmCronTaskLog::getStatuses();

        $this->fields_list = array(
            'id_cron_task_log' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'module_name' => array(
                'title' => $this->l('Module'),
                'filter_key' => 'm!name',
                'order_key' => 'm!name',
                'callback' => 'displayModuleName',
            ),
            'task_name' => array(
                'title' => $this->l('Task Name'),
                'filter_key' => 'ct!task_name',
                'search' => true,
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

    public function getStatusBadge($status, $row)
    {
        $this->context->smarty->assign('status', $status);
        return $this->createTemplate('qctm_badge_status.tpl')->fetch();
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

    public function setMedia()
    {
        parent::setMedia();

        $this->addCSS($this->module->getPathUri() . 'views/css/admin/qlo_cron_task_manager_admin.css');
    }
}
