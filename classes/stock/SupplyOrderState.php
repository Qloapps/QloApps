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

/**
 * @since 1.5.0
 */
class SupplyOrderStateCore extends ObjectModel
{
    /**
     * @var string Name of the state
     */
    public $name;

    /**
     * @var bool Tells if a delivery note can be issued (i.e. the order has been validated)
     */
    public $delivery_note;

    /**
     * @var bool Tells if the order is still editable by an employee (i.e. you can add products)
     */
    public $editable;

    /**
     * @var bool Tells if the the order has been delivered
     */
    public $receipt_state;

    /**
     * @var bool Tells if the the order is in a state corresponding to a product pending receipt
     */
    public $pending_receipt;

    /**
     * @var bool Tells if the the order is in an enclosed state (i.e. terminated, canceled)
     */
    public $enclosed;

    /**
     * @var string Color used to display the state in the specified color (Ex. #FFFF00)
     */
    public $color;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'supply_order_state',
        'primary' => 'id_supply_order_state',
        'multilang' => true,
        'fields' => array(
            'delivery_note' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'editable' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'receipt_state' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'pending_receipt' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'enclosed' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'color' =>                array('type' => self::TYPE_STRING, 'validate' => 'isColor'),
            'name' =>                array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
        ),
    );

    /**
     * @see ObjectModel::$webserviceParameters
     */
    protected $webserviceParameters = array(
        'objectsNodeName' => 'supply_order_states',
        'objectNodeName' => 'supply_order_state',
        'fields' => array(
        ),
    );

    /**
     * Gets the list of supply order statuses
     *
     * @param int $id_state_referrer Optional, used to know what state is available after this one
     * @param int $id_lang Optional Id Language
     * @return array States
     */
    public static function getSupplyOrderStates($id_state_referrer = null, $id_lang = null)
    {
        if ($id_lang == null) {
            $id_lang = Context::getContext()->language->id;
        }

        $query = new DbQuery();
        $query->select('sl.name, s.id_supply_order_state');
        $query->from('supply_order_state', 's');
        $query->leftjoin('supply_order_state_lang', 'sl', 's.id_supply_order_state = sl.id_supply_order_state AND sl.id_lang='.(int)$id_lang);

        if (!is_null($id_state_referrer)) {
            $is_receipt_state = false;
            $is_editable = false;
            $is_delivery_note = false;
            $is_pending_receipt = false;

            //check current state to see what state is available
            $state = new SupplyOrderState((int)$id_state_referrer);
            if (Validate::isLoadedObject($state)) {
                $is_receipt_state = $state->receipt_state;
                $is_editable = $state->editable;
                $is_delivery_note = $state->delivery_note;
                $is_pending_receipt = $state->pending_receipt;
            }

            $query->where('s.id_supply_order_state <> '.(int)$id_state_referrer);

            //check first if the order is editable
            if ($is_editable) {
                $query->where('s.editable = 1 OR s.delivery_note = 1 OR s.enclosed = 1');
            }
            //check if the delivery note is available or if the state correspond to a pending receipt state
            elseif ($is_delivery_note || $is_pending_receipt) {
                $query->where('(s.delivery_note = 0 AND s.editable = 0) OR s.enclosed = 1');
            }
            //check if the state correspond to a receipt state
            elseif ($is_receipt_state) {
                $query->where('s.receipt_state = 1 AND s.id_supply_order_state > '.(int)$id_state_referrer);
            }
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    /**
     * Gets the list of supply order statuses
     *
     * @param array $ids Optional Do not include these ids in the result
     * @param int $id_lang Optional
     * @return array
     */
    public static function getStates($ids = null, $id_lang = null)
    {
        if ($id_lang == null) {
            $id_lang = Context::getContext()->language->id;
        }

        if ($ids && !is_array($ids)) {
            $ids = array();
        }

        $query = new DbQuery();
        $query->select('sl.name, s.id_supply_order_state');
        $query->from('supply_order_state', 's');
        $query->leftjoin('supply_order_state_lang', 'sl', 's.id_supply_order_state = sl.id_supply_order_state AND sl.id_lang='.(int)$id_lang);
        if ($ids) {
            $query->where('s.id_supply_order_state NOT IN('.implode(',', array_map('intval', $ids)).')');
        }

        $query->orderBy('sl.name ASC');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }
}
