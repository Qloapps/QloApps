<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CustomerMessageCore extends ObjectModel
{
    public $id;
    public $id_customer_thread;
    public $id_employee;
    public $message;
    public $file_name;
    public $ip_address;
    public $user_agent;
    public $private;
    public $date_add;
    public $date_upd;
    public $read;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'customer_message',
        'primary' => 'id_customer_message',
        'fields' => array(
            'id_employee' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_customer_thread' => array('type' => self::TYPE_INT),
            'ip_address' =>        array('type' => self::TYPE_STRING, 'validate' => 'isIp2Long', 'size' => 15),
            'message' =>            array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 16777216),
            'file_name' =>            array('type' => self::TYPE_STRING),
            'user_agent' =>        array('type' => self::TYPE_STRING),
            'private' =>            array('type' => self::TYPE_INT),
            'date_add' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'read' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool')
        ),
    );

    protected $webserviceParameters = array(
        'fields' => array(
            'id_employee' => array(
                'xlink_resource' => 'employees'
            ),
            'id_customer_thread' => array(
                'xlink_resource' => 'customer_threads'
            ),
        ),
    );

    public static function getMessagesByOrderId($id_order, $private = true)
    {
        return Db::getInstance()->executeS('
			SELECT cm.*,
				c.`firstname` AS cfirstname,
				c.`lastname` AS clastname,
				e.`firstname` AS efirstname,
				e.`lastname` AS elastname,
				(COUNT(cm.id_customer_message) = 0 AND ct.id_customer != 0) AS is_new_for_me
			FROM `'._DB_PREFIX_.'customer_message` cm
			LEFT JOIN `'._DB_PREFIX_.'customer_thread` ct
				ON ct.`id_customer_thread` = cm.`id_customer_thread`
			LEFT JOIN `'._DB_PREFIX_.'customer` c
				ON ct.`id_customer` = c.`id_customer`
			LEFT OUTER JOIN `'._DB_PREFIX_.'employee` e
				ON e.`id_employee` = cm.`id_employee`
			WHERE ct.id_order = '.(int)$id_order.'
			'.(!$private ? 'AND cm.`private` = 0' : '').'
			GROUP BY cm.id_customer_message
			ORDER BY cm.date_add DESC
		');
    }

    public static function getTotalCustomerMessages($where = null)
    {
        if (is_null($where)) {
            return (int)Db::getInstance()->getValue('
				SELECT COUNT(*)
				FROM '._DB_PREFIX_.'customer_message
				LEFT JOIN `'._DB_PREFIX_.'customer_thread` ct ON (cm.`id_customer_thread` = ct.`id_customer_thread`)
				WHERE 1'.Shop::addSqlRestriction()
            );
        } else {
            return (int)Db::getInstance()->getValue('
				SELECT COUNT(*)
				FROM '._DB_PREFIX_.'customer_message cm
				LEFT JOIN `'._DB_PREFIX_.'customer_thread` ct ON (cm.`id_customer_thread` = ct.`id_customer_thread`)
				WHERE '.$where.Shop::addSqlRestriction()
            );
        }
    }

    public function delete()
    {
        if (!empty($this->file_name)) {
            @unlink(_PS_UPLOAD_DIR_.$this->file_name);
        }
        return parent::delete();
    }
}
