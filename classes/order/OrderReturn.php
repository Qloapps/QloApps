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

class OrderReturnCore extends ObjectModel
{
    /** @var int */
    public $id;

    /** @var int */
    public $id_customer;

    /** @var int */
    public $id_order;

    /** @var int */
    public $state;

    /** @var string message content */
    public $question;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'order_return',
        'primary' => 'id_order_return',
        'fields' => array(
            'id_customer' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'question' =>        array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
            'state' =>            array('type' => self::TYPE_STRING),
            'date_add' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public function addReturnDetail($order_detail_list, $product_qty_list, $customization_ids, $customization_qty_input)
    {
        /* Classic product return */
        if ($order_detail_list) {
            foreach ($order_detail_list as $key => $order_detail) {
                if ($qty = (int)$product_qty_list[$key]) {
                    Db::getInstance()->insert('order_return_detail', array('id_order_return' => (int)$this->id, 'id_order_detail' => (int)$order_detail, 'product_quantity' => $qty, 'id_customization' => 0));
                }
            }
        }
        /* Customized product return */
        if ($customization_ids) {
            foreach ($customization_ids as $order_detail_id => $customizations) {
                foreach ($customizations as $customization_id) {
                    if ($quantity = (int)$customization_qty_input[(int)$customization_id]) {
                        Db::getInstance()->insert('order_return_detail', array('id_order_return' => (int)$this->id, 'id_order_detail' => (int)$order_detail_id, 'product_quantity' => $quantity, 'id_customization' => (int)$customization_id));
                    }
                }
            }
        }
    }

    public function checkEnoughProduct($order_detail_list, $product_qty_list, $customization_ids, $customization_qty_input)
    {
        $order = new Order((int)$this->id_order);
        if (!Validate::isLoadedObject($order)) {
            die(Tools::displayError());
        }
        $products = $order->getProducts();
        /* Products already returned */
        $order_return = OrderReturn::getOrdersReturn($order->id_customer, $order->id, true);
        foreach ($order_return as $or) {
            $order_return_products = OrderReturn::getOrdersReturnProducts($or['id_order_return'], $order);
            foreach ($order_return_products as $key => $orp) {
                $products[$key]['product_quantity'] -= (int)$orp['product_quantity'];
            }
        }
        /* Quantity check */
        if ($order_detail_list) {
            foreach (array_keys($order_detail_list) as $key) {
                if ($qty = (int)$product_qty_list[$key]) {
                    if ($products[$key]['product_quantity'] - $qty < 0) {
                        return false;
                    }
                }
            }
        }
        /* Customization quantity check */
        if ($customization_ids) {
            $ordered_customizations = Customization::getOrderedCustomizations((int)$order->id_cart);
            foreach ($customization_ids as $customizations) {
                foreach ($customizations as $customization_id) {
                    $customization_id = (int)$customization_id;
                    if (!isset($ordered_customizations[$customization_id])) {
                        return false;
                    }
                    $quantity = (isset($customization_qty_input[$customization_id]) ? (int)$customization_qty_input[$customization_id] : 0);
                    if ((int)$ordered_customizations[$customization_id]['quantity'] - $quantity < 0) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function countProduct()
    {
        if (!$data = Db::getInstance()->getRow('
		SELECT COUNT(`id_order_return`) AS total
		FROM `'._DB_PREFIX_.'order_return_detail`
		WHERE `id_order_return` = '.(int)$this->id)) {
            return false;
        }
        return (int)($data['total']);
    }

    public static function getOrdersReturn($customer_id, $order_id = false, $no_denied = false, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        $data = Db::getInstance()->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'order_return`
		WHERE `id_customer` = '.(int)$customer_id.
        ($order_id ? ' AND `id_order` = '.(int)$order_id : '').
        ($no_denied ? ' AND `state` != 4' : '').'
		ORDER BY `date_add` DESC');
        foreach ($data as $k => $or) {
            $state = new OrderReturnState($or['state']);
            $data[$k]['state_name'] = $state->name[$context->language->id];
            $data[$k]['type'] = 'Return';
            $data[$k]['tracking_number'] = $or['id_order_return'];
            $data[$k]['can_edit'] = false;
            $data[$k]['reference'] = Order::getUniqReferenceOf($or['id_order']);
        }
        return $data;
    }

    public static function getOrdersReturnDetail($id_order_return)
    {
        return Db::getInstance()->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'order_return_detail`
		WHERE `id_order_return` = '.(int)$id_order_return);
    }

    /**
     * @param int $order_return_id
     * @param Order $order
     * @return array
     */
    public static function getOrdersReturnProducts($order_return_id, $order)
    {
        $products_ret = OrderReturn::getOrdersReturnDetail($order_return_id);
        $products = $order->getProducts();
        $tmp = array();
        foreach ($products_ret as $return_detail) {
            $tmp[$return_detail['id_order_detail']]['quantity'] = isset($tmp[$return_detail['id_order_detail']]['quantity']) ? $tmp[$return_detail['id_order_detail']]['quantity'] + (int)$return_detail['product_quantity'] : (int)$return_detail['product_quantity'];
            $tmp[$return_detail['id_order_detail']]['customizations'] = (int)$return_detail['id_customization'];
        }
        $res_tab = array();
        foreach ($products as $key => $product) {
            if (isset($tmp[$product['id_order_detail']])) {
                $res_tab[$key] = $product;
                $res_tab[$key]['product_quantity'] = $tmp[$product['id_order_detail']]['quantity'];
                $res_tab[$key]['customizations'] = $tmp[$product['id_order_detail']]['customizations'];
            }
        }
        return $res_tab;
    }

    public static function getReturnedCustomizedProducts($id_order)
    {
        $returns = Customization::getReturnedCustomizations($id_order);
        $order = new Order((int)$id_order);
        if (!Validate::isLoadedObject($order)) {
            die(Tools::displayError());
        }
        $products = $order->getProducts();

        foreach ($returns as &$return) {
            $return['product_id'] = (int)$products[(int)$return['id_order_detail']]['product_id'];
            $return['product_attribute_id'] = (int)$products[(int)$return['id_order_detail']]['product_attribute_id'];
            $return['name'] = $products[(int)$return['id_order_detail']]['product_name'];
            $return['reference'] = $products[(int)$return['id_order_detail']]['product_reference'];
            $return['id_address_delivery'] = $products[(int)$return['id_order_detail']]['id_address_delivery'];
        }
        return $returns;
    }

    public static function deleteOrderReturnDetail($id_order_return, $id_order_detail, $id_customization = 0)
    {
        return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'order_return_detail` WHERE `id_order_detail` = '.(int)$id_order_detail.' AND `id_order_return` = '.(int)$id_order_return.' AND `id_customization` = '.(int)$id_customization);
    }

    /**
     *
     * Get return details for one product line
     * @param $id_order_detail
     */
    public static function getProductReturnDetail($id_order_detail)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT product_quantity, date_add, orsl.name as state
			FROM `'._DB_PREFIX_.'order_return_detail` ord
			LEFT JOIN `'._DB_PREFIX_.'order_return` o
			ON o.id_order_return = ord.id_order_return
			LEFT JOIN `'._DB_PREFIX_.'order_return_state_lang` orsl
			ON orsl.id_order_return_state = o.state AND orsl.id_lang = '.(int)Context::getContext()->language->id.'
			WHERE ord.`id_order_detail` = '.(int)$id_order_detail);
    }

    /**
     *
     * Add returned quantity to products list
     * @param array $products
     * @param int $id_order
     */
    public static function addReturnedQuantity(&$products, $id_order)
    {
        $details = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT od.id_order_detail, GREATEST(od.product_quantity_return, IFNULL(SUM(ord.product_quantity),0)) as qty_returned
			FROM '._DB_PREFIX_.'order_detail od
			LEFT JOIN '._DB_PREFIX_.'order_return_detail ord
			ON ord.id_order_detail = od.id_order_detail
			WHERE od.id_order = '.(int)$id_order.'
			GROUP BY od.id_order_detail'
        );
        if (!$details) {
            return;
        }

        $detail_list = array();
        foreach ($details as $detail) {
            $detail_list[$detail['id_order_detail']] = $detail;
        }

        foreach ($products as &$product) {
            if (isset($detail_list[$product['id_order_detail']]['qty_returned'])) {
                $product['qty_returned'] = $detail_list[$product['id_order_detail']]['qty_returned'];
            }
        }
    }
}
