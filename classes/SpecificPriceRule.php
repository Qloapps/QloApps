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

class SpecificPriceRuleCore extends ObjectModel
{
    public $name;
    public $id_shop;
    public $id_currency;
    public $id_country;
    public $id_group;
    public $from_quantity;
    public $price;
    public $reduction;
    public $reduction_tax;
    public $reduction_type;
    public $from;
    public $to;

    protected static $rules_application_enable = true;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'specific_price_rule',
        'primary' => 'id_specific_price_rule',
        'fields' => array(
            'name' =>            array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'id_shop' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_country' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_currency' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_group' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'from_quantity' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'price' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isNegativePrice', 'required' => true),
            'reduction' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'reduction_tax' =>            array('type' => self::TYPE_INT, 'validate' => 'isBool', 'required' => true),
            'reduction_type' => array('type' => self::TYPE_STRING, 'validate' => 'isReductionType', 'required' => true),
            'from' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
            'to' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
        ),
    );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'specific_price_rules',
        'objectNodeName' => 'specific_price_rule',
        'fields' => array(
            'id_shop' =>                array('xlink_resource' => 'shops', 'required' => true),
            'id_country' =>            array('xlink_resource' => 'countries', 'required' => true),
            'id_currency' =>            array('xlink_resource' => 'currencies', 'required' => true),
            'id_group' =>                array('xlink_resource' => 'groups', 'required' => true),
        ),
    );

    public function delete()
    {
        $this->deleteConditions();
        Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'specific_price WHERE id_specific_price_rule='.(int)$this->id);
        return parent::delete();
    }

    public function deleteConditions()
    {
        $ids_condition_group = Db::getInstance()->executeS('SELECT id_specific_price_rule_condition_group
																		 FROM '._DB_PREFIX_.'specific_price_rule_condition_group
																		 WHERE id_specific_price_rule='.(int)$this->id);
        if ($ids_condition_group) {
            foreach ($ids_condition_group as $row) {
                Db::getInstance()->delete('specific_price_rule_condition_group', 'id_specific_price_rule_condition_group='.(int)$row['id_specific_price_rule_condition_group']);
                Db::getInstance()->delete('specific_price_rule_condition', 'id_specific_price_rule_condition_group='.(int)$row['id_specific_price_rule_condition_group']);
            }
        }
    }

    public static function disableAnyApplication()
    {
        SpecificPriceRule::$rules_application_enable = false;
    }

    public static function enableAnyApplication()
    {
        SpecificPriceRule::$rules_application_enable = true;
    }

    public function addConditions($conditions)
    {
        if (!is_array($conditions)) {
            return;
        }

        $result = Db::getInstance()->insert('specific_price_rule_condition_group', array(
            'id_specific_price_rule' =>    (int)$this->id
        ));
        if (!$result) {
            return false;
        }
        $id_specific_price_rule_condition_group = (int)Db::getInstance()->Insert_ID();
        foreach ($conditions as $condition) {
            $result = Db::getInstance()->insert('specific_price_rule_condition', array(
                'id_specific_price_rule_condition_group' => (int)$id_specific_price_rule_condition_group,
                'type' => pSQL($condition['type']),
                'value' => (float)$condition['value'],
            ));
            if (!$result) {
                return false;
            }
        }
        return true;
    }

    public function apply($products = false)
    {
        if (!SpecificPriceRule::$rules_application_enable) {
            return;
        }

        $this->resetApplication($products);
        $products = $this->getAffectedProducts($products);
        foreach ($products as $product) {
            SpecificPriceRule::applyRuleToProduct((int)$this->id, (int)$product['id_product'], (int)$product['id_product_attribute']);
        }
    }

    public function resetApplication($products = false)
    {
        $where = '';
        if ($products && count($products)) {
            $where .= ' AND id_product IN ('.implode(', ', array_map('intval', $products)).')';
        }
        return Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'specific_price WHERE id_specific_price_rule='.(int)$this->id.$where);
    }

    /**
     * @param array|bool $products
     */
    public static function applyAllRules($products = false)
    {
        if (!SpecificPriceRule::$rules_application_enable) {
            return;
        }

        $rules = new PrestaShopCollection('SpecificPriceRule');
        foreach ($rules as $rule) {
            /** @var SpecificPriceRule $rule */
            $rule->apply($products);
        }
    }

    public function getConditions()
    {
        $conditions = Db::getInstance()->executeS('
			SELECT g.*, c.*
			FROM '._DB_PREFIX_.'specific_price_rule_condition_group g
			LEFT JOIN '._DB_PREFIX_.'specific_price_rule_condition c
				ON (c.id_specific_price_rule_condition_group = g.id_specific_price_rule_condition_group)
			WHERE g.id_specific_price_rule='.(int)$this->id
        );
        $conditions_group = array();
        if ($conditions) {
            foreach ($conditions as &$condition) {
                if ($condition['type'] == 'attribute') {
                    $condition['id_attribute_group'] = Db::getInstance()->getValue('SELECT id_attribute_group
																										FROM '._DB_PREFIX_.'attribute
																										WHERE id_attribute='.(int)$condition['value']);
                } elseif ($condition['type'] == 'feature') {
                    $condition['id_feature'] = Db::getInstance()->getValue('SELECT id_feature
																								FROM '._DB_PREFIX_.'feature_value
																								WHERE id_feature_value='.(int)$condition['value']);
                }
                $conditions_group[(int)$condition['id_specific_price_rule_condition_group']][] = $condition;
            }
        }
        return $conditions_group;
    }

    /**
     * Return the product list affected by this specific rule.
     *
     * @param bool|array $products Products list limitation.
     * @return array Affected products list IDs.
     * @throws PrestaShopDatabaseException
     */
    public function getAffectedProducts($products = false)
    {
        $conditions_group = $this->getConditions();
        $current_shop_id = Context::getContext()->shop->id;

        $result = array();

        if ($conditions_group) {
            foreach ($conditions_group as $id_condition_group => $condition_group) {
                // Base request
                $query = new DbQuery();
                $query->select('p.`id_product`')
                    ->from('product', 'p')
                    ->leftJoin('product_shop', 'ps', 'p.`id_product` = ps.`id_product`')
                    ->where('ps.id_shop = '.(int)$current_shop_id);

                $attributes_join_added = false;

                // Add the conditions
                foreach ($condition_group as $id_condition => $condition) {
                    if ($condition['type'] == 'attribute') {
                        if (!$attributes_join_added) {
                            $query->select('pa.`id_product_attribute`')
                                ->leftJoin('product_attribute', 'pa', 'p.`id_product` = pa.`id_product`')
                                ->join(Shop::addSqlAssociation('product_attribute', 'pa', false));

                            $attributes_join_added = true;
                        }

                        $query->leftJoin('product_attribute_combination', 'pac'.(int)$id_condition, 'pa.`id_product_attribute` = pac'.(int)$id_condition.'.`id_product_attribute`')
                            ->where('pac'.(int)$id_condition.'.`id_attribute` = '.(int)$condition['value']);
                    } elseif ($condition['type'] == 'manufacturer') {
                        $query->where('p.id_manufacturer = '.(int)$condition['value']);
                    } elseif ($condition['type'] == 'category') {
                        $query->leftJoin('category_product', 'cp'.(int)$id_condition, 'p.`id_product` = cp'.(int)$id_condition.'.`id_product`')
                            ->where('cp'.(int)$id_condition.'.id_category = '.(int)$condition['value']);
                    } elseif ($condition['type'] == 'supplier') {
                        $query->where('EXISTS(
							SELECT
								`ps'.(int)$id_condition.'`.`id_product`
							FROM
								`'._DB_PREFIX_.'product_supplier` `ps'.(int)$id_condition.'`
							WHERE
								`p`.`id_product` = `ps'.(int)$id_condition.'`.`id_product`
								AND `ps'.(int)$id_condition.'`.`id_supplier` = '.(int)$condition['value'].'
						)');
                    } elseif ($condition['type'] == 'feature') {
                        $query->leftJoin('feature_product', 'fp'.(int)$id_condition, 'p.`id_product` = fp'.(int)$id_condition.'.`id_product`')
                            ->where('fp'.(int)$id_condition.'.`id_feature_value` = '.(int)$condition['value']);
                    }
                }

                // Products limitation
                if ($products && count($products)) {
                    $query->where('p.`id_product` IN ('.implode(', ', array_map('intval', $products)).')');
                }

                // Force the column id_product_attribute if not requested
                if (!$attributes_join_added) {
                    $query->select('NULL as `id_product_attribute`');
                }

                $result = array_merge($result, Db::getInstance()->executeS($query));
            }
        } else {
            // All products without conditions
            $query = new DbQuery();
            $query->select('p.`id_product`')
                ->select('NULL as `id_product_attribute`')
                ->from('product', 'p')
                ->leftJoin('product_shop', 'ps', 'p.`id_product` = ps.`id_product`')
                ->where('ps.id_shop = '.(int)$current_shop_id);

            if ($products && count($products)) {
                $query->where('p.`id_product` IN ('.implode(', ', array_map('intval', $products)).')');
            }

            $result = Db::getInstance()->executeS($query);
        }

        return $result;
    }

    public static function applyRuleToProduct($id_rule, $id_product, $id_product_attribute = null)
    {
        $rule = new SpecificPriceRule((int)$id_rule);
        if (!Validate::isLoadedObject($rule) || !$id_product) {
            return false;
        }

        $specific_price = new SpecificPrice();
        $specific_price->id_specific_price_rule = (int)$rule->id;
        $specific_price->id_product = (int)$id_product;
        $specific_price->id_product_attribute = (int)$id_product_attribute;
        $specific_price->id_customer = 0;
        $specific_price->id_shop = (int)$rule->id_shop;
        $specific_price->id_country = (int)$rule->id_country;
        $specific_price->id_currency = (int)$rule->id_currency;
        $specific_price->id_group = (int)$rule->id_group;
        $specific_price->from_quantity = (int)$rule->from_quantity;
        $specific_price->price = (float)$rule->price;
        $specific_price->reduction_type = $rule->reduction_type;
        $specific_price->reduction_tax = $rule->reduction_tax;
        $specific_price->reduction = ($rule->reduction_type == 'percentage' ? $rule->reduction / 100 : (float)$rule->reduction);
        $specific_price->from = $rule->from;
        $specific_price->to = $rule->to;

        return $specific_price->add();
    }
}
