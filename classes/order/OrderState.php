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

class OrderStateCore extends ObjectModel
{
    /** @var string Name */
    public $name;

    /** @var string Template name if there is any e-mail to send */
    public $template;

    /** @var bool Send an e-mail to customer ? */
    public $send_email;

    public $module_name;

    /** @var bool Allow customer to view and download invoice when order is at this state */
    public $invoice;

    /** @var string Display state in the specified color */
    public $color;

    public $unremovable;

    /** @var bool Log authorization */
    public $logable;

    /** @var bool Delivery */
    public $delivery;

    /** @var bool Hidden */
    public $hidden;

    /** @var bool Shipped */
    public $shipped;

    /** @var bool Paid */
    public $paid;

    /** @var bool Attach PDF Invoice */
    public $pdf_invoice;

    /** @var bool Attach PDF Delivery Slip */
    public $pdf_delivery;

    /** @var bool True if carrier has been deleted (staying in database as deleted) */
    public $deleted = 0;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'order_state',
        'primary' => 'id_order_state',
        'multilang' => true,
        'fields' => array(
            'send_email' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'module_name' => array('type' => self::TYPE_STRING, 'validate' => 'isModuleName'),
            'invoice' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'color' =>        array('type' => self::TYPE_STRING, 'validate' => 'isColor'),
            'logable' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'shipped' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'unremovable' =>array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'delivery' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'hidden' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'paid' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'pdf_delivery' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'pdf_invoice' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'deleted' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),

            /* Lang fields */
            'name' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 64),
            'template' =>    array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isTplName', 'size' => 64),
        ),
    );

    protected $webserviceParameters = array(
        'fields' => array(
            'unremovable' => array(),
            'delivery' => array(),
            'hidden' => array(),
        ),
    );

    const FLAG_NO_HIDDEN    = 1;  /* 00001 */
    const FLAG_LOGABLE        = 2;  /* 00010 */
    const FLAG_DELIVERY        = 4;  /* 00100 */
    const FLAG_SHIPPED        = 8;  /* 01000 */
    const FLAG_PAID        = 16; /* 10000 */

    /**
    * Get all available order statuses
    *
    * @param int $id_lang Language id for status name
    * @return array Order statuses
    */
    public static function getOrderStates($id_lang)
    {
        $cache_id = 'OrderState::getOrderStates_'.(int)$id_lang;
        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'order_state` os
			LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$id_lang.')
			WHERE deleted = 0
			ORDER BY `name` ASC');
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }

    /**
    * Check if we can make a invoice when order is in this state
    *
    * @param int $id_order_state State ID
    * @return bool availability
    */
    public static function invoiceAvailable($id_order_state)
    {
        $result = false;
        if (Configuration::get('PS_INVOICE')) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `invoice`
			FROM `'._DB_PREFIX_.'order_state`
			WHERE `id_order_state` = '.(int)$id_order_state);
        }
        return (bool)$result;
    }

    public function isRemovable()
    {
        return !($this->unremovable);
    }
}
