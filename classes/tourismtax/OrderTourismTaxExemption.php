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

class OrderTourismTaxExemptionCore extends ObjectModel
{
    public $id_exemption;
    public $id_order_tourism_tax;
    public $id_htl_booking;
    public $id_service_product_order_detail;
    public $id_order;
    public $id_employee;
    public $note;
    public $date_add;

    public static $definition = array(
        'table' => 'order_tourism_tax_exemption',
        'primary' => 'id_exemption',
        'fields' => array(
            'id_order_tourism_tax' => array('type' => self::TYPE_INT,    'validate' => 'isUnsignedId', 'required' => true),
            'id_htl_booking' => array('type' => self::TYPE_INT,    'validate' => 'isUnsignedId', 'required' => true),
            'id_service_product_order_detail' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_order' => array('type' => self::TYPE_INT,    'validate' => 'isUnsignedId', 'required' => true),
            'id_employee' => array('type' => self::TYPE_INT,  'validate' => 'isUnsignedId', 'required' => true),
            'note' => array('type' => self::TYPE_STRING, 'validate' => 'isMessage', 'allow_null' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /**
     * Return all exemption records for a given order_tourism_tax row.
     *
     * @param int $idOrderTourismTax
     * @return array
     */
    public static function getByOrderTourismTax($idOrderTourismTax)
    {
        return Db::getInstance()->executeS(
            'SELECT e.* FROM `' . _DB_PREFIX_ . 'order_tourism_tax_exemption` e
             WHERE e.`id_order_tourism_tax` = ' . (int) $idOrderTourismTax . '
             ORDER BY e.`date_add` ASC'
        );
    }
}
