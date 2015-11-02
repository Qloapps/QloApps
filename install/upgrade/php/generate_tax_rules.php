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

function generate_tax_rules()
{
    $res = true;
    $taxes = Db::getInstance()->executeS('SELECT * from `'._DB_PREFIX_.'tax` WHERE active = 1');

    // if no tax found, nothing to do, return true
    if (!is_array($taxes)) {
        return true;
    }

    foreach ($taxes as $tax) {
        $id_tax = $tax['id_tax'];
        $row = array(
            'active' => 1,
            'id_tax_rules_group' => $id_tax,
            'name' => 'Rule '.$tax['rate'].'%',
        );
        $res &= Db::getInstance()->insert('tax_rules_group', $row);
        $id_tax_rules_group = Db::getInstance()->insert_id();


        $countries = Db::getInstance()->executeS('
		SELECT * FROM `'._DB_PREFIX_.'country` c
		LEFT JOIN `'._DB_PREFIX_.'zone` z ON (c.`id_zone` = z.`id_zone`)
		LEFT JOIN `'._DB_PREFIX_.'tax_zone` tz ON (tz.`id_zone` = z.`id_zone`)
		WHERE `id_tax` = '.(int)$id_tax
        );
        if ($countries) {
            foreach ($countries as $country) {
                $res &= Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'tax_rule`
					(`id_tax_rules_group`, `id_country`, `id_state`, `state_behavior`, `id_tax`)
					VALUES
					('.$id_tax_rules_group.', '.(int)$country['id_country'].', 0, 0, '.(int)$id_tax. ')');
            }
        }

        $states = Db::getInstance()->executeS('
		SELECT * FROM `'._DB_PREFIX_.'states` s
		LEFT JOIN `'._DB_PREFIX_.'tax_state` ts ON (ts.`id_state` = s.`id_state`)
		WHERE `id_tax` = '.(int)$id_tax);

        if ($states) {
            foreach ($states as $state) {
                if (!in_array($state['tax_behavior'], array(PS_PRODUCT_TAX, PS_STATE_TAX, PS_BOTH_TAX))) {
                    $tax_behavior = PS_PRODUCT_TAX;
                } else {
                    $tax_behavior = $state['tax_behavior'];
                }
                $res &= Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'tax_rule`
					(`id_tax_rules_group`, `id_country`, `id_state`, `state_behavior`, `id_tax`)
					VALUES (
					'.$id_tax_rules_group.',
				 	'.(int)$state['id_country'].',
	 					'.(int)$state['id_state'].',
				 '.(int)$tax_behavior.',
				 '.(int)$id_tax.
                 ')');
            }
        }

        $res &= Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.'product`
		SET `id_tax_rules_group` = '.$id_tax_rules_group.'
		WHERE `id_tax` = '.(int)$id_tax
        );

        $res &= Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.'carrier`
		SET `id_tax_rules_group` = '.$id_tax_rules_group.'
		WHERE `id_tax` = '.(int)$id_tax
        );

        $socolissimo_overcost_tax = Db::getInstance()->getValue('SELECT value
			FROM `'._DB_PREFIX_.'configuration`
			WHERE name="SOCOLISSIMO_OVERCOST_TAX"');
        if ($socolissimo_overcost_tax == $id_tax) {
            $res &= Db::getInstance()->getValue('UPDATE `'._DB_PREFIX_.'configuration`
			set value="'.$id_tax_rules_group.'" WHERE name="SOCOLISSIMO_OVERCOST_TAX"');
        }
    }
    return $res;
}
