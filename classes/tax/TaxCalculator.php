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
 *
 * TaxCaculator is responsible of the tax computation
 */
class TaxCalculatorCore
{
    /**
     * COMBINE_METHOD sum taxes
     * eg: 100€ * (10% + 15%)
     */
    const COMBINE_METHOD = 1;

    /**
     * ONE_AFTER_ANOTHER_METHOD apply taxes one after another
     * eg: (100€ * 10%) * 15%
     */
    const ONE_AFTER_ANOTHER_METHOD = 2;

    /**
     * @var array $taxes
     */
    public $taxes;

    /**
     * @var int $computation_method (COMBINE_METHOD | ONE_AFTER_ANOTHER_METHOD)
     */
    public $computation_method;


    /**
     * @param array $taxes
     * @param int $computation_method (COMBINE_METHOD | ONE_AFTER_ANOTHER_METHOD)
     */
    public function __construct(array $taxes = array(), $computation_method = TaxCalculator::COMBINE_METHOD)
    {
        // sanity check
        foreach ($taxes as $tax) {
            if (!($tax instanceof Tax)) {
                throw new Exception('Invalid Tax Object');
            }
        }

        $this->taxes = $taxes;
        $this->computation_method = (int)$computation_method;
    }

    /**
     * Compute and add the taxes to the specified price
     *
     * @param float $price_te price tax excluded
     * @return float price with taxes
     */
    public function addTaxes($price_te)
    {
        return $price_te * (1 + ($this->getTotalRate() / 100));
    }


    /**
     * Compute and remove the taxes to the specified price
     *
     * @param float $price_ti price tax inclusive
     * @return float price without taxes
     */
    public function removeTaxes($price_ti)
    {
        return $price_ti / (1 + $this->getTotalRate() / 100);
    }

    /**
     * @return float total taxes rate
     */
    public function getTotalRate()
    {
        $taxes = 0;
        if ($this->computation_method == TaxCalculator::ONE_AFTER_ANOTHER_METHOD) {
            $taxes = 1;
            foreach ($this->taxes as $tax) {
                $taxes *= (1 + (abs($tax->rate) / 100));
            }

            $taxes = $taxes - 1;
            $taxes = $taxes * 100;
        } else {
            foreach ($this->taxes as $tax) {
                $taxes += abs($tax->rate);
            }
        }

        return (float)$taxes;
    }

    public function getTaxesName()
    {
        $name = '';
        foreach ($this->taxes as $tax) {
            $name .= $tax->name[(int)Context::getContext()->language->id].' - ';
        }

        $name = rtrim($name, ' - ');

        return $name;
    }

    /**
     * Return the tax amount associated to each taxes of the TaxCalculator
     *
     * @param float $price_te
     * @return array $taxes_amount
     */
    public function getTaxesAmount($price_te)
    {
        $taxes_amounts = array();

        foreach ($this->taxes as $tax) {
            if (!isset($taxes_amounts[$tax->id])) {
                $taxes_amounts[$tax->id] = 0;
            }
            if ($this->computation_method == TaxCalculator::ONE_AFTER_ANOTHER_METHOD) {
                $taxes_amounts[$tax->id] += $price_te * (abs($tax->rate) / 100);
                $price_te = $price_te + $taxes_amounts[$tax->id];
            } else {
                $taxes_amounts[$tax->id] += ($price_te * (abs($tax->rate) / 100));
            }
        }

        return $taxes_amounts;
    }

    /**
     * Return the total taxes amount
     *
     * @param float $price_te
     * @return float $amount
     */
    public function getTaxesTotalAmount($price_te)
    {
        $amount = 0;

        $taxes = $this->getTaxesAmount($price_te);
        foreach ($taxes as $tax) {
            $amount += $tax;
        }

        return $amount;
    }

    /**
     * @param float  $unitPriceTaxExcl Room/service unit_price_tax_excl (% base and tier lookup)
     * @param string $checkInDate      'YYYY-MM-DD'
     * @param int    $numNights
     * @param int    $numAdults
     * @param int[]  $childrenAges     Ages at check-in date
     * @param int    $idCurrency
     * @param int    $collectionType   HotelBranchInformation::tourism_tax_collection_type snapshot
     * @param int    $idLang
     * @param int    $quantity         Line quantity multiplier. Rooms: always 1.
     * @param bool   $isManualApply    true only from the admin Apply-button flow — bypasses the
     *                                 at-hotel collection-type gate. Every automatic call site
     *                                 (order creation, cart/product-page/list preview) leaves this
     *                                 false, so a manual-collection hotel computes/shows nothing
     *                                 until staff act.
     * @return array ['tourism_tax_online' => float,
     *                'rows' => [['id_tax','tax_name','num_nights','num_adults','total_amount','collection_type'], ...]]
     */
    public function getTourismTaxRows(
        $unitPriceTaxExcl,
        $checkInDate,
        $numNights,
        $numAdults,
        $childrenAges,
        $idCurrency,
        $collectionType,
        $idLang,
        $quantity = 1,
        $isManualApply = false
    ) {
        $result = array('tourism_tax_online' => 0.0, 'rows' => array());

        $collectionType = (int) $collectionType;
        if (!Configuration::get('QLO_USE_TOURISM_TAX')) {
            return $result;
        }
        if ($collectionType === TourismTax::COLLECTION_TYPE_AT_HOTEL && !$isManualApply) {
            return $result;
        }

        $checkIn = new DateTime($checkInDate);
        $isoDayIndex = $checkIn->format('N') - 1;

        $rows = array();

        foreach ($this->taxes as $tax) {
            $tourismTax = TourismTax::getByTaxId((int) $tax->id);
            if (!$tourismTax) {
                continue;
            }

            if (!empty($tourismTax->valid_from) && $tourismTax->valid_from !== '0000-00-00') {
                $validFrom = new DateTime($tourismTax->valid_from);
                if ($checkIn < $validFrom) {
                    continue;
                }
            }
            if (!empty($tourismTax->valid_to) && $tourismTax->valid_to !== '0000-00-00') {
                $validTo = new DateTime($tourismTax->valid_to);
                if ($checkIn > $validTo) {
                    continue;
                }
            }
            if ($tourismTax->special_days) {
                $specialDays = json_decode($tourismTax->special_days, true);
                if (!is_array($specialDays) || !in_array(TourismTax::DAY_KEYS[$isoDayIndex], $specialDays)) {
                    continue;
                }
            }

            $isTiered = (bool) $tourismTax->is_tiered;
            $taxType = (int) $tourismTax->tax_calc_type;
            $isPerNight = (bool) $tourismTax->is_per_night;
            $isPerPerson = (bool) $tourismTax->is_per_person;
            $hasChildRate = (bool) $tourismTax->has_child_rate;

            $baseValue = (float) $tourismTax->tax_value;
            if ($isTiered) {
                $tier = TourismTaxTier::getMatchingTier((int) $tax->id, $unitPriceTaxExcl);
                if (!$tier) {
                    continue;
                }
                $baseValue = (float) $tier['tax_value'];
            }

            $adultMultiplier = 1;
            if ($isPerNight) {
                $adultMultiplier *= $numNights;
            }
            if ($isPerPerson) {
                $adultMultiplier *= $numAdults;
            }

            if ($taxType === 0) {
                $unitAmountAdult = $baseValue;
                $totalAmountAdult = $baseValue * $adultMultiplier;
            } else {
                $unitAmountAdult = $unitPriceTaxExcl * ($baseValue / 100);
                $totalAmountAdult = $unitAmountAdult * $adultMultiplier;
            }

            $totalAmountChild = 0.0;

            if ($hasChildRate && !empty($childrenAges)) {
                $contribution = TourismTaxChildRange::getChildContribution(
                    (int) $tax->id,
                    $childrenAges,
                    $taxType,
                    $unitPriceTaxExcl,
                    $baseValue
                );
                if ((int) $contribution['count'] > 0) {
                    $nightMultiplier = $isPerNight ? $numNights : 1;
                    $totalAmountChild = $contribution['total'] * $nightMultiplier;
                }
            }

            $totalAmountAdult = Tools::convertPrice($totalAmountAdult, $idCurrency);
            $totalAmountChild = Tools::convertPrice($totalAmountChild, $idCurrency);
            $totalAmount = Tools::ps_round(($totalAmountAdult + $totalAmountChild) * max(1, $quantity), 6);

            $rows[] = array(
                'id_tax' => (int) $tax->id,
                'tax_name' => (string) (isset($tax->name[$idLang]) ? $tax->name[$idLang] : ''),
                'num_nights' => $numNights,
                'num_adults' => $numAdults,
                'total_amount' => $totalAmount,
                'collection_type' => $collectionType,
            );
        }

        $result['rows'] = $rows;
        $result['tourism_tax_online'] = (float) array_sum(array_column($rows, 'total_amount'));

        return $result;
    }
}
