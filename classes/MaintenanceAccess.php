<?php

/**
* 2010-2022 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2022 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class MaintenanceAccessCore  extends ObjectModel
{

    public $id_maintenance_access;
    public $ip_address;
    public $email;
    public $date_add;
    const USERNAME_ATTEMPTS_PER_QUARTER_HOUR = 3;
    const IP_ATTEMPTS_QUARTER_HOUR = 12;
    const USERNAME_ATTEMPTS_PER_HOUR = 6;
    const IP_ATTEMPTS_PER_HOUR = 24;
    const QUARTER_HOUR = 60*15;
    const HOUR = 60*60;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'maintenance_access',
        'primary' => 'id_maintenance_access',
        'fields' => array(
            'ip_address' => array('type' => self::TYPE_STRING,  'size' => 50),
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 128),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public function cleanData()
    {
        $time = (time() - (self::HOUR*2));
        Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ .'maintenance_access`
			WHERE `date_add`  < "' . pSQL(date("Y-m-d H:i:s", $time)) . '"'
        );
    }

    public function getUserFailedCount($time, $email=false, $ip_address=false, $attempts)
    {
        $sql = "SELECT COUNT(`id_maintenance_access`) FROM `". _DB_PREFIX_ ."maintenance_access` WHERE `date_add`  > '"
        . pSQL(date('Y-m-d H:i:s', $time)) ."'";

        if ($email) {
            $sql .= " AND  `email` = '".pSQL($email)."'";
        }

        if ($ip_address) {
            $sql .= " AND  `ip_address` = '".pSQL($ip_address)."'";
        }

        return Db::getInstance()->getValue($sql) >= $attempts;
    }

    public function checkLimit($email)
    {
        $has_error = false;
        $minutes_ago = time() - self::QUARTER_HOUR;
        $hours_ago = time() - self::HOUR;
        $ip_address = Tools::getRemoteAddr();

        $has_error |= $this->getUserFailedCount(
                $minutes_ago, $email, false, self::USERNAME_ATTEMPTS_PER_QUARTER_HOUR
            )? true : $has_error;
        $has_error |= $this->getUserFailedCount(
                $minutes_ago, false, $ip_address, self::IP_ATTEMPTS_QUARTER_HOUR
            )? true : $has_error;
        $has_error |= $this->getUserFailedCount(
                $hours_ago, $email, false, self::USERNAME_ATTEMPTS_PER_HOUR
            )? true : $has_error;
        $has_error |= $this->getUserFailedCount(
                $hours_ago, false, $ip_address, self::IP_ATTEMPTS_PER_HOUR
            )? true : $has_error;

        return $has_error;
    }
}