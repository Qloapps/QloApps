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

class GroupReductionCore extends ObjectModel
{
    public $id_group;
    public $id_category;
    public $reduction;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'group_reduction',
        'primary' => 'id_group_reduction',
        'fields' => array(
            'id_group' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_category' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'reduction' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
        ),
    );

    protected static $reduction_cache = array();

    public function add($autodate = true, $null_values = false)
    {
        return (parent::add($autodate, $null_values) && $this->_setCache());
    }

    public function update($null_values = false)
    {
        return (parent::update($null_values) && $this->_updateCache());
    }

    public function delete()
    {
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT cp.`id_product`
			FROM `'._DB_PREFIX_.'category_product` cp
			WHERE cp.`id_category` = '.(int)$this->id_category
        );

        $ids = array();
        foreach ($products as $row) {
            $ids[] = $row['id_product'];
        }

        if ($ids) {
            Db::getInstance()->delete('product_group_reduction_cache', 'id_product IN ('.implode(', ', $ids).')');
        }
        return (parent::delete());
    }

    protected function _clearCache()
    {
        return Db::getInstance()->delete('product_group_reduction_cache', 'id_group = '.(int)$this->id_group);
    }

    protected function _setCache()
    {
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT cp.`id_product`
			FROM `'._DB_PREFIX_.'category_product` cp
			WHERE cp.`id_category` = '.(int)$this->id_category
        );

        $values = array();
        foreach ($products as $row) {
            $values[] = '('.(int)$row['id_product'].', '.(int)$this->id_group.', '.(float)$this->reduction.')';
        }

        if (count($values)) {
            $query = 'INSERT INTO `'._DB_PREFIX_.'product_group_reduction_cache` (`id_product`, `id_group`, `reduction`)
			VALUES '.implode(', ', $values).' ON DUPLICATE KEY UPDATE
			`reduction` = IF(VALUES(`reduction`) > `reduction`, VALUES(`reduction`), `reduction`)';
            return (Db::getInstance()->execute($query));
        }

        return true;
    }

    protected function _updateCache()
    {
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT cp.`id_product`
			FROM `'._DB_PREFIX_.'category_product` cp
			WHERE cp.`id_category` = '.(int)$this->id_category,
        false);

        $ids = array();
        foreach ($products as $product) {
            $ids[] = $product['id_product'];
        }

        $result = true;
        if ($ids) {
            $result &= Db::getInstance()->update('product_group_reduction_cache', array(
                'reduction' => (float)$this->reduction,
            ), 'id_product IN('.implode(', ', $ids).') AND id_group = '.(int)$this->id_group);
        }

        return $result;
    }

    public static function getGroupReductions($id_group, $id_lang)
    {
        $lang = $id_lang.Shop::addSqlRestrictionOnLang('cl');
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT gr.`id_group_reduction`, gr.`id_group`, gr.`id_category`, gr.`reduction`, cl.`name` AS category_name
			FROM `'._DB_PREFIX_.'group_reduction` gr
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (cl.`id_category` = gr.`id_category` AND cl.`id_lang` = '.(int)$lang.')
			WHERE `id_group` = '.(int)$id_group
        );
    }

    public static function getValueForProduct($id_product, $id_group)
    {
        if (!Group::isFeatureActive()) {
            return 0;
        }

        if (!isset(self::$reduction_cache[$id_product.'-'.$id_group])) {
            self::$reduction_cache[$id_product.'-'.$id_group] = Db::getInstance()->getValue('
			SELECT `reduction`
			FROM `'._DB_PREFIX_.'product_group_reduction_cache`
			WHERE `id_product` = '.(int)$id_product.' AND `id_group` = '.(int)$id_group);
        }
        // Should return string (decimal in database) and not a float
        return self::$reduction_cache[$id_product.'-'.$id_group];
    }

    public static function doesExist($id_group, $id_category)
    {
        return (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT `id_group`
		FROM `'._DB_PREFIX_.'group_reduction`
		WHERE `id_group` = '.(int)$id_group.' AND `id_category` = '.(int)$id_category);
    }

    public static function getGroupsByCategoryId($id_category)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT gr.`id_group` as id_group, gr.`reduction` as reduction, id_group_reduction
			FROM `'._DB_PREFIX_.'group_reduction` gr
			WHERE `id_category` = '.(int)$id_category
        );
    }

    /**
     * @deprecated 1.5.3.0
     * @param int $id_category
     * @return array|null
     */
    public static function getGroupByCategoryId($id_category)
    {
        Tools::displayAsDeprecated('Use GroupReduction::getGroupsByCategoryId($id_category)');
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT gr.`id_group` as id_group, gr.`reduction` as reduction, id_group_reduction
			FROM `'._DB_PREFIX_.'group_reduction` gr
			WHERE `id_category` = '.(int)$id_category, false);
    }

    public static function getGroupsReductionByCategoryId($id_category)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT gr.`id_group_reduction` as id_group_reduction, id_group
			FROM `'._DB_PREFIX_.'group_reduction` gr
			WHERE `id_category` = '.(int)$id_category
        );
    }

    /**
     * @deprecated 1.5.3.0
     * @param int $id_category
     * @return array|null
     */
    public static function getGroupReductionByCategoryId($id_category)
    {
        Tools::displayAsDeprecated('Use GroupReduction::getGroupsByCategoryId($id_category)');
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT gr.`id_group_reduction` as id_group_reduction
			FROM `'._DB_PREFIX_.'group_reduction` gr
			WHERE `id_category` = '.(int)$id_category, false);
    }

    public static function setProductReduction($id_product, $id_group = null, $id_category = null, $reduction = null)
    {
        $res = true;
        GroupReduction::deleteProductReduction((int)$id_product);

        $categories = Product::getProductCategories((int)$id_product);

        if ($categories) {
            foreach ($categories as $category) {
                $reductions = GroupReduction::getGroupsByCategoryId((int)$category);
                if ($reductions) {
                    foreach ($reductions as $reduction) {
                        $current_group_reduction = new GroupReduction((int)$reduction['id_group_reduction']);
                        $res &= $current_group_reduction->_setCache();
                    }
                }
            }
        }

        return $res;
    }

    public static function deleteProductReduction($id_product)
    {
        $query = 'DELETE FROM `'._DB_PREFIX_.'product_group_reduction_cache` WHERE `id_product` = '.(int)$id_product;
        if (Db::getInstance()->execute($query) === false) {
            return false;
        }
        return true;
    }

    public static function duplicateReduction($id_product_old, $id_product)
    {
        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executes('
			SELECT pgr.`id_product`, pgr.`id_group`, pgr.`reduction`
			FROM `'._DB_PREFIX_.'product_group_reduction_cache` pgr
			WHERE pgr.`id_product` = '.(int)$id_product_old
        );

        if (!$res) {
            return true;
        }

        $query = '';

        foreach ($res as $row) {
            $query .= 'INSERT INTO `'._DB_PREFIX_.'product_group_reduction_cache` (`id_product`, `id_group`, `reduction`) VALUES ';
            $query .= '('.(int)$id_product.', '.(int)$row['id_group'].', '.(float)$row['reduction'].') ON DUPLICATE KEY UPDATE `reduction` = '.(float)$row['reduction'].';';
        }

        return Db::getInstance()->execute($query);
    }

    public static function deleteCategory($id_category)
    {
        $query = 'DELETE FROM `'._DB_PREFIX_.'group_reduction` WHERE `id_category` = '.(int)$id_category;
        if (Db::getInstance()->Execute($query) === false) {
            return false;
        }
        return true;
    }
}
