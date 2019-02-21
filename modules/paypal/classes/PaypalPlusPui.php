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
 *  @version  Release: $Revision: 13573 $
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class PaypalPlusPui extends ObjectModel
{

    public $id_paypal_plus_pui;
    public $id_order;
    public $pui_informations;

    public static $definition = array(
        'table' => 'paypal_plus_pui',
        'primary' => 'id_paypal_plus_pui',
        'fields' => array(
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'pui_informations' => array('type' => self::TYPE_STRING),
        ),
    );


    public function getByIdOrder($id_order)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('paypal_plus_pui');
        $sql->where('id_order = '.(int)$id_order);
        return Db::getInstance()->getRow($sql);
    }
}
