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

class MaintenanceAccessCore extends ObjectModel
{
    public $id_maintenance_access;
    public $ip_address;
    public $email;
    public $date_add;

    const LOGIN_ATTEMPTS_WINDOW = 30; /* in minutes */

    public static $definition = array(
        'table' => 'maintenance_access',
        'primary' => 'id_maintenance_access',
        'fields' => array(
            'ip_address' => array('type' => self::TYPE_STRING,  'size' => 50),
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 128),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public function removeFailedAttempts($email, $ipAddress)
    {
        Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ .'maintenance_access` ma
            WHERE (ma.`email` = "'.pSQL($email).'" AND ma.`ip_address` = "'.pSQL($ipAddress).'")'
        );
    }

    public function getFailedAttemptsCount($email, $ipAddress)
    {
        $sql = 'SELECT COUNT(ma.`id_maintenance_access`)
        FROM `'._DB_PREFIX_.'maintenance_access` ma
        WHERE (ma.`email` = "'.pSQL($email).'" OR ma.`ip_address` = "'.pSQL($ipAddress).'")
        AND TIMESTAMPDIFF(
            MINUTE,
            ma.`date_add`,
            (
                SELECT MAX(ma.`date_add`)
                FROM `'._DB_PREFIX_.'maintenance_access` ma
                WHERE (ma.`email` = "'.pSQL($email).'" OR ma.`ip_address` = "'.pSQL($ipAddress).'")
                AND TIMESTAMPDIFF(MINUTE, ma.`date_add`, NOW()) < '.(int) MaintenanceAccess::LOGIN_ATTEMPTS_WINDOW.'
            )
        ) < '.(int) MaintenanceAccess::LOGIN_ATTEMPTS_WINDOW;

        return Db::getInstance()->getValue($sql);
    }

    public function getLastAttempt($email, $ipAddress)
    {
        return Db::getInstance()->getRow(
            'SELECT *
            FROM `'._DB_PREFIX_.'maintenance_access` ma
            WHERE (ma.`email` = "'.pSQL($email).'" OR ma.`ip_address` = "'.pSQL($ipAddress).'")
            ORDER BY ma.`date_add` DESC'
        );
    }
}
