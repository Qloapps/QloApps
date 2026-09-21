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

function update_order_payment_type_160()
{
    if ($orders = Db::getInstance()->executeS(
        'SELECT `id_order`, `module` FROM `'._DB_PREFIX_.'orders`'
    )) {
        $sql = 'UPDATE `'._DB_PREFIX_.'orders`
        SET payment_type = CASE';
        $modulePaymentType = array();
        foreach ($orders as &$order) {
            if (!isset($modulePaymentType[$order['module']])) {
                $module = Module::getInstanceByName($order['module']);
                if ($module instanceof Module && $module->payment_type) {
                    $modulePaymentType[$order['module']] = $module->payment_type;
                } else {
                    $modulePaymentType[$order['module']] = 1;
                }
            }
            $sql .= ' WHEN id_order = '.(int)$order['id_order'].' THEN '.(int)$modulePaymentType[$order['module']];
        }
        $sql .= ' END WHERE id_order IN ('.pSQL(implode(', ', array_column($orders, 'id_order'))).')';
        Db::getInstance()->execute($sql);
    }
    Db::getInstance()->execute(
        'UPDATE `'._DB_PREFIX_.'order_payment` op
        INNER JOIN `'._DB_PREFIX_.'orders` o ON (op.`order_reference` = o.`reference`)
        SET op.`payment_type` = o.`payment_type`'
    );
}