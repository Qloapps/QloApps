<?php
/**
* 2010-2020 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class HotelBookingDemands extends ObjectModel
{
    public $id_htl_booking;
    public $name;
    public $unit_price_tax_excl;
    public $unit_price_tax_incl;
    public $total_price_tax_excl;
    public $total_price_tax_incl;
    public $id_tax_rules_group;
    public $tax_computation_method;
    public $price_calc_method;
    public $date_add;
    public $date_upd;

    /** @var TaxCalculator object */
    public $tax_calculator = null;

    public static $definition = array(
        'table' => 'htl_booking_demands',
        'primary' => 'id_booking_demand',
        'fields' => array(
            'id_htl_booking' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'unit_price_tax_excl' => array('type' => self::TYPE_FLOAT),
            'unit_price_tax_incl' => array('type' => self::TYPE_FLOAT),
            'total_price_tax_excl' => array('type' => self::TYPE_FLOAT),
            'total_price_tax_incl' => array('type' => self::TYPE_FLOAT),
            'name' => array(
                'type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'required' => true, 'size' => 128
            ),
            'price_calc_method' => array('type' => self::TYPE_INT),
            'id_tax_rules_group' => array('type' => self::TYPE_INT),
            'tax_computation_method' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate')
    ));

    protected $webserviceParameters = array(
        'objectsNodeName' => 'extra_demands',
        'objectNodeName' => 'extra_demand',
        'fields' => array(
            'id_htl_booking' => array(
                'xlink_resource' => array(
                    'resourceName' => 'bookings',
                )
            ),
        ),
    );

    public function getRoomTypeBookingExtraDemands(
        $idOrder,
        $idProduct = 0,
        $idRoom = 0,
        $dateFrom = 0,
        $dateTo = 0,
        $groupByRoom = 1,
        $getTotalPrice = 0,
        $useTax = 1
    ) {
        $moduleObj = Module::getInstanceByName('hotelreservationsystem');
        $context = Context::getContext();
        if (isset($context->currency->id)
            && Validate::isLoadedObject($context->currency)
        ) {
            $idCurrency = (int)$context->currency->id;
        } else {
            $idCurrency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
        }
        $totalDemandsPrice = 0;
        $sql = 'SELECT hb.`id_room`, hb.`adults`, hb.`children`, hd.* FROM `'._DB_PREFIX_.'htl_booking_demands` hd
        LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hb ON (hd.`id_htl_booking` = hb.`id`)
        WHERE hd.`id_htl_booking` IN
        (SELECT `id` FROM `'._DB_PREFIX_.'htl_booking_detail`
        WHERE `id_order`='.(int) $idOrder;
        if ($idProduct) {
            $sql .= ' AND `id_product`='.(int)$idProduct;
        }
        if ($idRoom) {
            $sql .= ' AND `id_room`='.(int)$idRoom;
        }
        if ($dateFrom && $dateTo) {
            $dateFrom = date('Y-m-d', strtotime($dateFrom));
            $dateTo = date('Y-m-d', strtotime($dateTo));
            $sql .= ' AND `date_from`=\''.pSQL($dateFrom).'\' AND `date_to`= \''.pSQL($dateTo).'\'';
        }
        $sql .= ')';
        if ($getTotalPrice) {
            $totalDemandsPrice = 0;
        }
        if ($roomTypeDemands =  Db::getInstance()->executeS($sql)) {
            if ($getTotalPrice) {
                foreach ($roomTypeDemands as $demand) {
                    if ($useTax) {
                        $totalDemandsPrice += $demand['total_price_tax_incl'];
                    } else {
                        $totalDemandsPrice += $demand['total_price_tax_excl'];
                    }
                }
            } else {
                if ($groupByRoom) {
                    $roomDemands = array();
                    foreach ($roomTypeDemands as $demand) {
                        // Set tax_code
                        if ($taxes = static::getTaxListStatic($demand['id_booking_demand'])) {
                            $taxTemp = array();
                            foreach ($taxes as $tax) {
                                $obj = new Tax($tax['id_tax']);
                                $taxTemp[] = sprintf($moduleObj->l('%1$s%2$s%%'), ($obj->rate + 0), '');
                            }
                            $demand['extra_demands_tax_label'] = implode(', ', $taxTemp);

                        } else {
                            $demand['extra_demands_tax_label'] = $moduleObj->l('No tax', 'HotelBookingDemands');
                        }
                        $roomDemands[$demand['id_room']]['id_room'] = $demand['id_room'];
                        $roomDemands[$demand['id_room']]['adults'] = $demand['adults'];
                        $roomDemands[$demand['id_room']]['children'] = $demand['children'];
                        $roomDemands[$demand['id_room']]['extra_demands'][] = $demand;
                    }
                    unset($taxTemp);
                    return $roomDemands;
                }
            }
        }
        if ($getTotalPrice) {
            return $totalDemandsPrice;
        } else {
            return $roomTypeDemands;
        }
    }

    public static function getTaxListStatic($idBookingDemand)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'htl_booking_demands_tax` WHERE `id_booking_demand` = '.(int)$idBookingDemand
        );
    }

    public static function getTaxCalculatorStatic($idBookingDemand)
    {
        $sql = 'SELECT t.*, d.`tax_computation_method`
				FROM `'._DB_PREFIX_.'htl_booking_demands_tax` t
				LEFT JOIN `'._DB_PREFIX_.'htl_booking_demands` d ON (d.`id_booking_demand` = t.`id_booking_demand`)
				WHERE d.`id_booking_demand` = '.(int)$idBookingDemand;

        $computationMethod = 1;
        $taxes = array();
        if ($results = Db::getInstance()->executeS($sql)) {
            foreach ($results as $result) {
                $taxes[] = new Tax((int)$result['id_tax']);
            }

            $computationMethod = $result['tax_computation_method'];
        }

        return new TaxCalculator($taxes, $computationMethod);
    }

    public function setBookingDemandTaxDetails($replace = 0)
    {
        if ($this->id) {
            if ($taxCalculator = $this->tax_calculator) {
                if (!($taxCalculator instanceof TaxCalculator)) {
                    return false;
                }
                if (count($taxCalculator->taxes) == 0) {
                    return true;
                }
                $values = '';
                $priceTaxExcl = $this->unit_price_tax_excl;
                foreach ($taxCalculator->getTaxesAmount($priceTaxExcl) as $idTax => $amount) {
                    $quantity = 1;
                    if ($this->price_calc_method == HotelRoomTypeGlobalDemand::WK_PRICE_CALC_METHOD_EACH_DAY) {
                        $objBkDetail = new HotelBookingDetail($this->id_htl_booking);
                        $quantity = $objBkDetail->getNumberOfDays($objBkDetail->date_from, $objBkDetail->date_to);
                    }
                    switch (Configuration::get('PS_ROUND_TYPE')) {
                        case Order::ROUND_ITEM:
                            $unitAmount = (float)Tools::ps_round($amount, _PS_PRICE_COMPUTE_PRECISION_);
                            $totalAmount = $unitAmount * $quantity;
                            break;
                        case Order::ROUND_LINE:
                            $unitAmount = $amount;
                            $totalAmount = Tools::ps_round(
                                $unitAmount * $quantity,
                                _PS_PRICE_COMPUTE_PRECISION_
                            );
                            break;
                        case Order::ROUND_TOTAL:
                            $unitAmount = $amount;
                            $totalAmount = $unitAmount * $quantity;
                            break;
                    }
                    $values .= '('.(int)$this->id.','.(int)$idTax.','.(float)$unitAmount.','.
                    (float)$totalAmount.'),';
                }

                // if delete previous details and save new details
                if ($replace) {
                    Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'htl_booking_demands_tax` WHERE id_booking_demand='.(int)$this->id);
                }

                $values = rtrim($values, ',');
                $sql = 'INSERT INTO `'._DB_PREFIX_.'htl_booking_demands_tax`
                (id_booking_demand, id_tax, unit_amount, total_amount)
                VALUES '.$values;

                return Db::getInstance()->execute($sql);
            }
        }
        return true;
    }

    /**
     * This method returns true if at least one booking demand uses One After Another tax computation method.
     * @return bool
     */
    public function useOneAfterAnotherTaxComputationMethod()
    {
        // if one of the order details use the tax computation method the display will be different
        return Db::getInstance()->getValue('
    		SELECT od.`tax_computation_method`
    		FROM `' . _DB_PREFIX_ . 'order_detail_tax` odt
    		LEFT JOIN `' . _DB_PREFIX_ . 'order_detail` od ON (od.`id_order_detail` = odt.`id_order_detail`)
    		WHERE od.`id_order` = ' . (int) $this->id_order . '
    		AND od.`id_order_invoice` = ' . (int) $this->id . '
    		AND od.`tax_computation_method` = ' . (int) TaxCalculator::ONE_AFTER_ANOTHER_METHOD)
            || Configuration::get('PS_INVOICE_TAXES_BREAKDOWN');
    }

    public function getExtraDemandsTaxesDetails($idOrder)
    {
        $taxDetails = Db::getInstance()->executeS(
            'SELECT hb.`id` as id_htl_booking, hbd.`unit_price_tax_excl`, hbd.`total_price_tax_excl`, hb.`id_order_detail`, hdt.*, t.* FROM '._DB_PREFIX_.'orders o '.
            'INNER JOIN '._DB_PREFIX_.'htl_booking_detail hb ON hb.id_order = o.id_order '.
            'INNER JOIN '._DB_PREFIX_.'htl_booking_demands hbd ON hbd.id_htl_booking = hb.id '.
            'INNER JOIN '._DB_PREFIX_.'htl_booking_demands_tax hdt ON hbd.id_booking_demand = hdt.id_booking_demand '.
            'INNER JOIN '._DB_PREFIX_.'tax t ON t.id_tax = hdt.id_tax '.
            'WHERE o.id_order = '.(int)$idOrder
        );
        if ($taxDetails) {
            foreach ($taxDetails as &$detail) {
                $priceTaxExcl = $detail['unit_price_tax_excl'];
                $quantity = 1;
                $objBkDemand = new HotelBookingDemands($detail['id_booking_demand']);
                if ($objBkDemand->price_calc_method == HotelRoomTypeGlobalDemand::WK_PRICE_CALC_METHOD_EACH_DAY) {
                    $objBkDetail = new HotelBookingDetail($detail['id_htl_booking']);
                    $quantity = $objBkDetail->getNumberOfDays($objBkDetail->date_from, $objBkDetail->date_to);
                }
                switch (Configuration::get('PS_ROUND_TYPE')) {
                    case Order::ROUND_ITEM:
                        $unitAmount = (float)Tools::ps_round($priceTaxExcl, _PS_PRICE_COMPUTE_PRECISION_);
                        $totalTaxBase = $unitAmount * $quantity;
                        break;
                    case Order::ROUND_LINE:
                        $unitAmount = $priceTaxExcl;
                        $totalTaxBase = Tools::ps_round(
                            $unitAmount * $quantity,
                            _PS_PRICE_COMPUTE_PRECISION_
                        );
                        break;
                    case Order::ROUND_TOTAL:
                        $unitAmount = $priceTaxExcl;
                        $totalTaxBase = $unitAmount * $quantity;
                        break;
                }
                $detail['total_tax_base'] = $totalTaxBase;
            }
        }
        return $taxDetails;
    }

    public function deleteBookingDemands($idHotelBooking)
    {
        // first delete all tax details of this booking demands
        Db::getInstance()->delete(
            'htl_booking_demands_tax',
            'id_booking_demand IN (SELECT `id_booking_demand` FROM `'._DB_PREFIX_.'htl_booking_demands` WHERE `id_htl_booking` = '.(int)$idHotelBooking.')'
        );

        // delete all the demands
        return Db::getInstance()->delete('htl_booking_demands', 'id_htl_booking = '.(int)$idHotelBooking);
    }

    public function deleteBookingDemandTaxDetails($idBookingDemand)
    {
        return Db::getInstance()->delete('htl_booking_demands_tax', 'id_booking_demand = '.(int)$idBookingDemand);
    }
}
