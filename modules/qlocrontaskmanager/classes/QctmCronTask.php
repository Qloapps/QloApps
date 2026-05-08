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
            'cron_expression' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64, 'required' => true),
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
