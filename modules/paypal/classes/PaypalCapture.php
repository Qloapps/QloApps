<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2018 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class PaypalCapture extends ObjectModel
{

    public $id_order;
    public $capture_amount;
    public $result;
    public $date_add;
    public $date_upd;
    public $id_paypal_capture;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition;

    // for Prestashop 1.4
    protected $tables;
    protected $fieldsRequired;
    protected $fieldsSize;
    protected $fieldsValidate;
    protected $table = 'paypal_capture';
    protected $identifier = 'id_paypal_capture';

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            self::$definition = array(
                'table' => 'paypal_capture',
                'primary' => 'id_paypal_capture',
                'fields' => array(
                    'id_order' => array('type' => 1, 'validate' => 'isUnsignedId'),
                    'result' => array('type' => 3, 'validate' => 'isString'),
                    'capture_amount' => array('type' => 4, 'validate' => 'isFloat'),
                    'date_add' => array('type' => 5, 'validate' => 'isDate'),
                    'date_upd' => array('type' => 5, 'validate' => 'isDate'),
                ),
            );
        } else {
            $tables = array('paypal_capture');
            $fieldsRequired = array('id_order', 'result', 'capture_amount', 'date_add', 'date_upd');
            $fieldsValidate = array();
        }

        $this->date_add = date('Y-m-d H:i:s');
        $this->date_upd = date('Y-m-d H:i:s');

        return parent::__construct($id, $id_lang, $id_shop);
    }

    public function getFields()
    {
        $fields = parent::getFields();

        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $fields['result'] = pSQL($this->result);
            $fields['capture_amount'] = pSQL($this->capture_amount);
            $fields['date_add'] = pSQL($this->date_add);
            $fields['date_upd'] = pSQL($this->date_upd);
            $fields['id_order'] = pSQL($this->id_order);
        }

        return $fields;
    }

    public static function getTotalAmountCapturedByIdOrder($id_order)
    {
        //Tester la version de prestashop

        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $query = 'SELECT SUM(capture_amount) AS tt FROM '._DB_PREFIX_.'paypal_capture WHERE id_order ='.(int) $id_order.' AND result="Completed" ';
            $result = Db::getInstance()->getRow($query);

            return Tools::ps_round($result['tt'], 2);
        } else {
            $query = new DbQuery();
            $query->select('SUM(capture_amount)');
            $query->from(self::$definition['table']);
            $query->where('id_order = '.(int) $id_order);
            $query->where('result = "Completed"');
            return Tools::ps_round(DB::getInstance()->getValue($query), 2);
        }

    }

    public function getRestToPaid(Order $order)
    {
        $cart = new Cart($order->id_cart);
        $totalPaid = Tools::ps_round($cart->getOrderTotal(), 2);
        return Tools::ps_round($totalPaid, 2) - Tools::ps_round(self::getTotalAmountCapturedByIdOrder($order->id), 2);
    }

    public function getRestToCapture($id_order)
    {
        $cart = Cart::getCartByOrderId($id_order);
        $total = Tools::ps_round($cart->getOrderTotal(), 2) - Tools::ps_round(self::getTotalAmountCapturedByIdOrder($id_order), 2);

        if ($total > Tools::ps_round(0, 2)) {
            return true;
        } else {
            return false;
        }

    }

    public function getListCaptured()
    {
        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $query = 'SELECT * FROM '._DB_PREFIX_.'paypal_capture WHERE id_order ='.(int)$this->id_order.' ORDER BY date_add DESC ;';
        } else {
            $query = new DbQuery();
            $query->from(self::$definition['table']);
            $query->where('id_order = '.(int)$this->id_order);
            $query->orderBy('date_add DESC');
        }

        $result = DB::getInstance()->executeS($query);

        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            foreach ($result as &$foo) {
                $foo['date'] = Tools::displayDate($foo['date_add'], Configuration::get('PS_LANG_DEFAULT'), true);
            }

        }
        return $result;
    }

    public static function parsePrice($price)
    {
        $regexp = "/^([0-9\s]{0,10})((\.|,)[0-9]{0,2})?$/isD";

        if (preg_match($regexp, $price)) {
            $array_regexp = array("#,#isD", "# #isD");
            $array_replace = array(".", "");
            $price = preg_replace($array_regexp, $array_replace, $price);

            return Tools::ps_round($price, 2);
        } else {
            return false;
        }

    }
}
