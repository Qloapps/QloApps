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

class QctmCronTaskLog extends ObjectModel
{
    public $id_cron_task_log;
    public $id_cron_task;
    public $status;
    public $error_message;
    public $execution_time;
    public $date_add;

    const QCTM_CRON_TASK_LOG_STATUS_SUCCESS = 1;
    const QCTM_CRON_TASK_LOG_STATUS_ERROR = 2;

    public static $definition = array(
        'table' => 'qctm_cron_task_log',
        'primary' => 'id_cron_task_log',
        'fields' => array(
            'id_cron_task' => array('type' => self::TYPE_INT,'validate' => 'isUnsignedId','required' => true),
            'status' => array('type' => self::TYPE_INT,'validate' => 'isUnsignedId','required' => true),
            'error_message' => array('type' => self::TYPE_STRING,'validate' => 'isString'),
            'execution_time' => array('type' => self::TYPE_FLOAT,'validate' => 'isFloat'),
            'date_add' => array('type' => self::TYPE_DATE,'validate' => 'isDate'),
        ),
    );

    /**
     * Add a log entry for a task execution
     *
     * @param int $idCronTask
     * @param int $status
     * @param string|null $errorMessage
     * @param float $executionTime
     * @return bool
     */
    public static function addLog($idCronTask, $status, $errorMessage = null, $executionTime = 0)
    {
        $log = new self();
        $log->id_cron_task = (int) $idCronTask;
        $log->status = $status;
        $log->error_message = $errorMessage;
        $log->execution_time = (float) $executionTime;

        return $log->add();
    }

    /**
     * Delete logs older than specified number of days
     *
     * @param int $days
     * @return bool
     */
    public static function deleteOlderThan($days)
    {
        return Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'qctm_cron_task_log`
             WHERE `date_add` < DATE_SUB(NOW(), INTERVAL ' . (int) $days . ' DAY)'
        );
    }

    public static function getStatuses()
    {
        static $statuses = null;

        if ($statuses === null) {
            $module = Module::getInstanceByName('qlocrontaskmanager');
            $statuses = array(
                self::QCTM_CRON_TASK_LOG_STATUS_SUCCESS => $module->l('Success'),
                self::QCTM_CRON_TASK_LOG_STATUS_ERROR => $module->l('Error'),
            );
        }

        return $statuses;
    }
}
