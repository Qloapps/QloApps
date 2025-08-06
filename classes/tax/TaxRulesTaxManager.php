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
 * @since 1.5.0.1
 */
class TaxRulesTaxManagerCore implements TaxManagerInterface
{
    public $address;
    public $type;
    public $tax_calculator;

    /**
     * @var Core_Business_ConfigurationInterface
     */
    private $configurationManager;

    /**
     *
     * @param Address $address
     * @param mixed $type An additional parameter for the tax manager (ex: tax rules id for TaxRuleTaxManager)
     */
    public function __construct(Address $address, $type, Core_Business_ConfigurationInterface $configurationManager = null)
    {
        if ($configurationManager === null) {
            $this->configurationManager = Adapter_ServiceLocator::get('Core_Business_ConfigurationInterface');
        } else {
            $this->configurationManager = $configurationManager;
        }

        $this->address = $address;
        $this->type = $type;
    }

    /**
    * Returns true if this tax manager is available for this address
    *
    * @return bool
    */
    public static function isAvailableForThisAddress(Address $address)
    {
        return true; // default manager, available for all addresses
    }

    /**
    * Return the tax calculator associated to this address
    *
    * @return TaxCalculator
    */
    public function getTaxCalculator()
    {
        static $tax_enabled = null;

        if (isset($this->tax_calculator)) {
            return $this->tax_calculator;
        }

        if ($tax_enabled === null) {
            $tax_enabled = $this->configurationManager->get('PS_TAX');
        }

        if (!$tax_enabled) {
            return new TaxCalculator(array());
        }

        $taxes = array();
        $postcode = 0;

        if (!empty($this->address->postcode)) {
            $postcode = $this->address->postcode;
        }

        $cache_id = (int)$this->address->id_country.'-'.(int)$this->address->id_state.'-'.$postcode.'-'.(int)$this->type;

        if (!Cache::isStored($cache_id)) {
            $rows = Db::getInstance()->executeS('
				SELECT tr.*
				FROM `'._DB_PREFIX_.'tax_rule` tr
				JOIN `'._DB_PREFIX_.'tax_rules_group` trg ON (tr.`id_tax_rules_group` = trg.`id_tax_rules_group`)
				WHERE trg.`active` = 1
				AND tr.`id_country` = '.(int)$this->address->id_country.'
				AND tr.`id_tax_rules_group` = '.(int)$this->type.'
				AND tr.`id_state` IN (0, '.(int)$this->address->id_state.')
				AND tr.`id_tax` !=0
				AND (\''.pSQL($postcode).'\' BETWEEN tr.`zipcode_from` AND tr.`zipcode_to`
					OR (tr.`zipcode_to` = 0 AND tr.`zipcode_from` IN(0, \''.pSQL($postcode).'\')))
				ORDER BY tr.`zipcode_from` DESC, tr.`zipcode_to` DESC, tr.`id_state` DESC, tr.`id_country` DESC');

            $behavior = 0;
            $first_row = true;

            foreach ($rows as $row) {
                $tax = new Tax((int)$row['id_tax']);

                $taxes[] = $tax;

                // the applied behavior correspond to the most specific rules
                if ($first_row) {
                    $behavior = $row['behavior'];
                    $first_row = false;
                }

                if ($row['behavior'] == 0) {
                    break;
                }
            }
            $result = new TaxCalculator($taxes, $behavior);
            Cache::store($cache_id, $result);
            return $result;
        }

        return Cache::retrieve($cache_id);
    }
}
