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

class MessageCore extends ObjectModel
{
    public $id;

    /** @var string message content */
    public $message;

    /** @var int Cart ID (if applicable) */
    public $id_cart;

    /** @var int Order ID (if applicable) */
    public $id_order;

    /** @var int Customer ID (if applicable) */
    public $id_customer;

    /** @var int Employee ID (if applicable) */
    public $id_employee;

    /** @var bool Message is not displayed to the customer */
    public $private;

    /** @var string Object creation date */
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'message',
        'primary' => 'id_message',
        'fields' => array(
            'message' =>        array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 6400),
            'id_cart' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_order' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_customer' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_employee' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'private' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /**
     * Return the last message from cart
     *
     * @param int $id_cart Cart ID
     * @return array Message
     */
    public static function getMessageByCartId($id_cart)
    {
        return Db::getInstance()->getRow('
			SELECT *
			FROM `'._DB_PREFIX_.'message`
			WHERE `id_cart` = '.(int)$id_cart
        );
    }

    /**
     * Return messages from Order ID
     *
     * @param int $id_order Order ID
     * @param bool $private return WITH private messages
     * @return array Messages
     */
    public static function getMessagesByOrderId($id_order, $private = false, Context $context = null)
    {
        if (!Validate::isBool($private)) {
            die(Tools::displayError());
        }

        if (!$context) {
            $context = Context::getContext();
        }

        return Db::getInstance()->executeS('
			SELECT m.*, c.`firstname` AS cfirstname, c.`lastname` AS clastname, e.`firstname` AS efirstname, e.`lastname` AS elastname,
			(COUNT(mr.id_message) = 0 AND m.id_customer != 0) AS is_new_for_me
			FROM `'._DB_PREFIX_.'message` m
			LEFT JOIN `'._DB_PREFIX_.'customer` c ON m.`id_customer` = c.`id_customer`
			LEFT JOIN `'._DB_PREFIX_.'message_readed` mr
				ON mr.`id_message` = m.`id_message`
				AND mr.`id_employee` = '.(isset($context->employee) ? (int)$context->employee->id : '\'\'').'
			LEFT OUTER JOIN `'._DB_PREFIX_.'employee` e ON e.`id_employee` = m.`id_employee`
			WHERE id_order = '.(int)$id_order.'
			'.(!$private ? ' AND m.`private` = 0' : '').'
			GROUP BY m.id_message
			ORDER BY m.date_add DESC
		');
    }

    /**
     * Return messages from Cart ID
     *
     * @param int $id_order Order ID
     * @param bool $private return WITH private messages
     * @return array Messages
     */
    public static function getMessagesByCartId($id_cart, $private = false, Context $context = null)
    {
        if (!Validate::isBool($private)) {
            die(Tools::displayError());
        }

        if (!$context) {
            $context = Context::getContext();
        }

        return Db::getInstance()->executeS('
			SELECT m.*, c.`firstname` AS cfirstname, c.`lastname` AS clastname, e.`firstname` AS efirstname, e.`lastname` AS elastname,
			(COUNT(mr.id_message) = 0 AND m.id_customer != 0) AS is_new_for_me
			FROM `'._DB_PREFIX_.'message` m
			LEFT JOIN `'._DB_PREFIX_.'customer` c ON m.`id_customer` = c.`id_customer`
			LEFT JOIN `'._DB_PREFIX_.'message_readed` mr ON (mr.id_message = m.id_message AND mr.id_employee = '.(int)$context->employee->id.')
			LEFT OUTER JOIN `'._DB_PREFIX_.'employee` e ON e.`id_employee` = m.`id_employee`
			WHERE id_cart = '.(int)$id_cart.'
			'.(!$private ? ' AND m.`private` = 0' : '').'
			GROUP BY m.id_message
			ORDER BY m.date_add DESC
		');
    }

    /**
     * Registered a message 'readed'
     *
     * @param int $id_message Message ID
     * @param int $id_emplyee Employee ID
     */
    public static function markAsReaded($id_message, $id_employee)
    {
        if (!Validate::isUnsignedId($id_message) || !Validate::isUnsignedId($id_employee)) {
            die(Tools::displayError());
        }

        $result = Db::getInstance()->execute('
			INSERT INTO '._DB_PREFIX_.'message_readed (id_message , id_employee , date_add) VALUES
			('.(int)$id_message.', '.(int)$id_employee.', NOW());
		');
        return $result;
    }
}
