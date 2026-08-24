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

class QctmCronTask extends ObjectModel
{
    public $id_cron_task;
    public $id_module;
    public $task_name;
    public $description;
    public $cron_expression;
    public $callback;
    public $active;
    public $is_system;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'qctm_cron_task',
        'primary' => 'id_cron_task',
        'fields' => array(
            'id_module' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'task_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 128, 'required' => true),
            'description' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255, 'required' => true),
            'cron_expression' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 64, 'required' => true),
            'callback' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 128, 'required' => true),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'is_system' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public static function getActiveTasks()
    {
        return Db::getInstance()->executeS(
            'SELECT `id_cron_task`, `id_module`, `task_name`, `description`,
                    `cron_expression`, `callback`, `active`
             FROM `' . _DB_PREFIX_ . 'qctm_cron_task`
             WHERE `active` = 1'
        );
    }

    public function dispatch()
    {
        $tasks = self::getActiveTasks();
        if (empty($tasks)) {
            return;
        }

        $now = new DateTime('now', new DateTimeZone(Configuration::get('PS_TIMEZONE') ?: date_default_timezone_get()));
        $moduleCache = [];
        foreach ($tasks as $task) {
            if (!\Cron\CronExpression::isValidExpression($task['cron_expression'])) {
                QctmCronTaskLog::addLog(
                    $task['id_cron_task'],
                    QctmCronTaskLog::QCTM_CRON_TASK_LOG_STATUS_ERROR,
                    'Invalid cron expression "' . $task['cron_expression'] . '"'
                );
                continue;
            }

            $cron = \Cron\CronExpression::factory($task['cron_expression']);

            if (!$cron->isDue($now)) {
                continue;
            }

            $idModule = (int) $task['id_module'];
            if (!array_key_exists($idModule, $moduleCache)) {
                $moduleCache[$idModule] = Module::getInstanceById($idModule);
            }
            $module = $moduleCache[$idModule];

            if (!$module) {
                QctmCronTaskLog::addLog(
                    $task['id_cron_task'],
                    QctmCronTaskLog::QCTM_CRON_TASK_LOG_STATUS_ERROR,
                    'Module with ID "' . (int) $task['id_module'] . '" could not be loaded'
                );
                continue;
            }

            if (!$module->active) {
                continue;
            }

            $callback = $task['callback'];

            if (!method_exists($module, $callback)) {
                QctmCronTaskLog::addLog(
                    $task['id_cron_task'],
                    QctmCronTaskLog::QCTM_CRON_TASK_LOG_STATUS_ERROR,
                    'Callback method "' . $callback . '" not found on module "' . $module->name . '"'
                );
                continue;
            }

            $startTime = microtime(true);

            try {
                $module->{$callback}();

                QctmCronTaskLog::addLog(
                    $task['id_cron_task'],
                    QctmCronTaskLog::QCTM_CRON_TASK_LOG_STATUS_SUCCESS,
                    null,
                    round(microtime(true) - $startTime, 4)
                );
            } catch (\Throwable $e) {
                QctmCronTaskLog::addLog(
                    $task['id_cron_task'],
                    QctmCronTaskLog::QCTM_CRON_TASK_LOG_STATUS_ERROR,
                    $e->getMessage(),
                    round(microtime(true) - $startTime, 4)
                );
            }
        }

    }


    public function syncAllExistingModules()
    {
        $allTasks = Hook::exec('registerCronTasks', array(), null, true);

        if (!empty($allTasks) && is_array($allTasks)) {
            foreach ($allTasks as $moduleName => $tasks) {
                if (!empty($tasks) && is_array($tasks)) {
                    $module = Module::getInstanceByName($moduleName);
                    if($module){
                        $this->registerTasksForModule($module->id, $tasks);
                    }
                }
            }
        }
    }

    /**
     * Register tasks for a specific module after validation
     *
     * @param int $idModule
     * @param array $tasks
     */
    public function registerTasksForModule($idModule, $tasks)
    {
        $module = Module::getInstanceById($idModule);
        if (!$module) {
            return;
        }


        foreach ($tasks as $task) {
            if (!$this->validateTask($module, $task)) {
                continue;
            }

            if (QctmCronTask::getByModuleAndName($idModule, $task['name'])) {
                continue;
            }

            $cronTask = new QctmCronTask();
            $cronTask->id_module = $idModule;
            $cronTask->task_name = $task['name'];
            $cronTask->description = $task['description'];
            $cronTask->cron_expression = $task['cron'];
            $cronTask->callback = $task['callback'];
            $cronTask->active = 1;
            $cronTask->is_system = 0;
            if (!$cronTask->save()) {
                PrestaShopLogger::addLog(
                    'QloCronTaskManager: failed to register task "' . $task['name'] . '" for module "' . $module->name . '"',
                    3
                );
            }
        }
    }

    /**
     * Validate a task definition array
     *
     * @param object $module
     * @param array $task
     * @return bool
     */
    public function validateTask(Module $module, $task)
    {
        if (!Validate::isGenericName($task['name']) || !Validate::isGenericName($task['description'])
            || !Validate::isString($task['cron']) || !Validate::isString($task['callback'])) {
            return false;
        }

        if (!\Cron\CronExpression::isValidExpression($task['cron'])) {
            return false;
        }

        if (!method_exists($module, $task['callback'])) {
            return false;
        }

        return true;
    }

    /**
     * @param int $idModule
     * @param string $taskName
     * @return int|false
     */
    public static function getByModuleAndName($idModule, $taskName)
    {
        return Db::getInstance()->getValue(
            'SELECT `id_cron_task`
             FROM `' . _DB_PREFIX_ . 'qctm_cron_task`
             WHERE `id_module` = ' . (int) $idModule . '
             AND `task_name` = \'' . pSQL($taskName) . '\''
        );
    }

    /**
     * Delete all tasks belonging to a module.
     * Logs are intentionally preserved for history tracking.
     *
     * @param int $idModule
     * @return bool
     */
    public static function deleteByModule($idModule)
    {
        return Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'qctm_cron_task`
             WHERE `id_module` = ' . (int) $idModule
        );
    }
}
