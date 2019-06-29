<?php
/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class    PrestaShopLoggerCore extends ObjectModel
{
    /** @var int Log id */
    public $id_log;

    /** @var int Log severity */
    public $severity;

    /** @var int Error code */
    public $error_code;

    /** @var string Message */
    public $message;

    /** @var string Object type (eg. Order, Customer...) */
    public $object_type;

    /** @var int Object ID */
    public $object_id;

    /** @var int Object ID */
    public $id_employee;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'log',
        'primary' => 'id_log',
        'fields' => array(
            'severity' =>        array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'error_code' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'message' =>        array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
            'object_id' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_employee' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'object_type' =>    array('type' => self::TYPE_STRING, 'validate' => 'isName'),
            'date_add' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    protected static $is_present = array();

    /**
     * Send e-mail to the shop owner only if the minimal severity level has been reached
     *
     * @param Logger
     * @param PrestaShopLogger $log
     */
    public static function sendByMail($log)
    {
        if ((int)Configuration::get('PS_LOGS_BY_EMAIL') <= (int)$log->severity) {
            Mail::Send(
                (int)Configuration::get('PS_LANG_DEFAULT'),
                'log_alert',
                Mail::l('Log: You have a new alert from your shop', (int)Configuration::get('PS_LANG_DEFAULT')),
                array(),
                Configuration::get('PS_SHOP_EMAIL')
            );
        }
    }

    /**
     * add a log item to the database and send a mail if configured for this $severity
     *
     * @param string $message the log message
     * @param int $severity
     * @param int $error_code
     * @param string $object_type
     * @param int $object_id
     * @param bool $allow_duplicate if set to true, can log several time the same information (not recommended)
     * @return bool true if succeed
     */
    public static function addLog($message, $severity = 1, $error_code = null, $object_type = null, $object_id = null, $allow_duplicate = false, $id_employee = null)
    {
        $log = new PrestaShopLogger();
        $log->severity = (int)$severity;
        $log->error_code = (int)$error_code;
        $log->message = pSQL($message);
        $log->date_add = date('Y-m-d H:i:s');
        $log->date_upd = date('Y-m-d H:i:s');

        if ($id_employee === null && isset(Context::getContext()->employee) && Validate::isLoadedObject(Context::getContext()->employee)) {
            $id_employee = Context::getContext()->employee->id;
        }

        if ($id_employee !== null) {
            $log->id_employee = (int)$id_employee;
        }

        if (!empty($object_type) && !empty($object_id)) {
            $log->object_type = pSQL($object_type);
            $log->object_id = (int)$object_id;
        }

        if ($object_type != 'Swift_Message') {
            PrestaShopLogger::sendByMail($log);
        }

        if ($allow_duplicate || !$log->_isPresent()) {
            $res = $log->add();
            if ($res) {
                self::$is_present[$log->getHash()] = isset(self::$is_present[$log->getHash()])?self::$is_present[$log->getHash()] + 1:1;
                return true;
            }
        }
        return false;
    }

    /**
     * this function md5($this->message.$this->severity.$this->error_code.$this->object_type.$this->object_id)
     *
     * @return string hash
     */
    public function getHash()
    {
        if (empty($this->hash)) {
            $this->hash = md5($this->message.$this->severity.$this->error_code.$this->object_type.$this->object_id);
        }

        return $this->hash;
    }

    public static function eraseAllLogs()
    {
        return Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.'log');
    }

    /**
     * check if this log message already exists in database.
     *
     * @return true if exists
     */
    protected function _isPresent()
    {
        if (!isset(self::$is_present[md5($this->message)])) {
            self::$is_present[$this->getHash()] = Db::getInstance()->getValue('SELECT COUNT(*)
				FROM `'._DB_PREFIX_.'log`
				WHERE
					`message` = \''.$this->message.'\'
					AND `severity` = \''.$this->severity.'\'
					AND `error_code` = \''.$this->error_code.'\'
					AND `object_type` = \''.$this->object_type.'\'
					AND `object_id` = \''.$this->object_id.'\'
				');
        }

        return self::$is_present[$this->getHash()];
    }
}
