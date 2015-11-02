<?php
/*
* 2007-2015 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class ProductCommentCriterion extends ObjectModel
{
	public	$id;
	public	$id_product_comment_criterion_type;
	public	$name;
	public	$active = true;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'product_comment_criterion',
		'primary' => 'id_product_comment_criterion',
		'multilang' => true,
		'fields' => array(
			'id_product_comment_criterion_type' =>	array('type' => self::TYPE_INT),
			'active' =>								array('type' => self::TYPE_BOOL),
			// Lang fields
			'name' =>								array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
		)
	);

	public function delete()
	{
		if (!parent::delete())
			return false;
		if ($this->id_product_comment_criterion_type == 2)
		{
			if (!Db::getInstance()->execute('
					DELETE FROM '._DB_PREFIX_.'product_comment_criterion_category
					WHERE id_product_comment_criterion='.(int)$this->id))
				return false;
		}
		elseif ($this->id_product_comment_criterion_type == 3)
		{
			if (!Db::getInstance()->execute('
					DELETE FROM '._DB_PREFIX_.'product_comment_criterion_product
					WHERE id_product_comment_criterion='.(int)$this->id))
				return false;
		}

		return Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'product_comment_grade`
			WHERE `id_product_comment_criterion` = '.(int)$this->id);
	}

	public function update($nullValues = false)
	{
		$previousUpdate = new self((int)$this->id);
		if (!parent::update($nullValues))
			return false;
		if ($previousUpdate->id_product_comment_criterion_type != $this->id_product_comment_criterion_type)
		{
			if ($previousUpdate->id_product_comment_criterion_type == 2)
				return Db::getInstance()->execute('
					DELETE FROM '._DB_PREFIX_.'product_comment_criterion_category
					WHERE id_product_comment_criterion = '.(int)$previousUpdate->id);
			elseif ($previousUpdate->id_product_comment_criterion_type == 3)
				return Db::getInstance()->execute('
					DELETE FROM '._DB_PREFIX_.'product_comment_criterion_product
					WHERE id_product_comment_criterion = '.(int)$previousUpdate->id);
		}
		return true;
	}

	/**
	 * Link a Comment Criterion to a product
	 *
	 * @return boolean succeed
	 */
	public function addProduct($id_product)
	{
		if (!Validate::isUnsignedId($id_product))
			die(Tools::displayError());
		return Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'product_comment_criterion_product` (`id_product_comment_criterion`, `id_product`)
			VALUES('.(int)$this->id.','.(int)$id_product.')
		');
	}

	/**
	 * Link a Comment Criterion to a category
	 *
	 * @return boolean succeed
	 */
	public function addCategory($id_category)
	{
		if (!Validate::isUnsignedId($id_category))
			die(Tools::displayError());
		return Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'product_comment_criterion_category` (`id_product_comment_criterion`, `id_category`)
			VALUES('.(int)$this->id.','.(int)$id_category.')
		');
	}

	/**
	 * Add grade to a criterion
	 *
	 * @return boolean succeed
	 */
	public function addGrade($id_product_comment, $grade)
	{
		if (!Validate::isUnsignedId($id_product_comment))
			die(Tools::displayError());
		if ($grade < 0)
			$grade = 0;
		elseif ($grade > 10)
			$grade = 10;
		return (Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'product_comment_grade`
		(`id_product_comment`, `id_product_comment_criterion`, `grade`) VALUES(
		'.(int)($id_product_comment).',
		'.(int)$this->id.',
		'.(int)($grade).')'));
	}

	/**
	 * Get criterion by Product
	 *
	 * @return array Criterion
	 */
	public static function getByProduct($id_product, $id_lang)
	{
		if (!Validate::isUnsignedId($id_product) ||
			!Validate::isUnsignedId($id_lang))
			die(Tools::displayError());
		$alias = 'p';
		$table = '';
		// check if version > 1.5 to add shop association
		if (version_compare(_PS_VERSION_, '1.5', '>'))
		{
			$table = '_shop';
			$alias = 'ps';
		}

		$cache_id = 'ProductCommentCriterion::getByProduct_'.(int)$id_product.'-'.(int)$id_lang;
		if (!Cache::isStored($cache_id))
		{
			$result = Db::getInstance()->executeS('
				SELECT pcc.`id_product_comment_criterion`, pccl.`name`
				FROM `'._DB_PREFIX_.'product_comment_criterion` pcc
				LEFT JOIN `'._DB_PREFIX_.'product_comment_criterion_lang` pccl
					ON (pcc.id_product_comment_criterion = pccl.id_product_comment_criterion)
				LEFT JOIN `'._DB_PREFIX_.'product_comment_criterion_product` pccp
					ON (pcc.`id_product_comment_criterion` = pccp.`id_product_comment_criterion` AND pccp.`id_product` = '.(int)$id_product.')
				LEFT JOIN `'._DB_PREFIX_.'product_comment_criterion_category` pccc
					ON (pcc.`id_product_comment_criterion` = pccc.`id_product_comment_criterion`)
				LEFT JOIN `'._DB_PREFIX_.'product'.$table.'` '.$alias.'
					ON ('.$alias.'.id_category_default = pccc.id_category AND '.$alias.'.id_product = '.(int)$id_product.')
				WHERE pccl.`id_lang` = '.(int)($id_lang).'
				AND (
					pccp.id_product IS NOT NULL
					OR ps.id_product IS NOT NULL
					OR pcc.id_product_comment_criterion_type = 1
				)
				AND pcc.active = 1
				GROUP BY pcc.id_product_comment_criterion
			');
			Cache::store($cache_id, $result);
		}
		return Cache::retrieve($cache_id);
	}

	/**
	 * Get Criterions
	 *
	 * @return array Criterions
	 */
	public static function getCriterions($id_lang, $type = false, $active = false)
	{
		if (!Validate::isUnsignedId($id_lang))
			die(Tools::displayError());
		
		$sql = '
			SELECT pcc.`id_product_comment_criterion`, pcc.id_product_comment_criterion_type, pccl.`name`, pcc.active
			FROM `'._DB_PREFIX_.'product_comment_criterion` pcc
			JOIN `'._DB_PREFIX_.'product_comment_criterion_lang` pccl ON (pcc.id_product_comment_criterion = pccl.id_product_comment_criterion)
			WHERE pccl.`id_lang` = '.(int)$id_lang.($active ? ' AND active = 1' : '').($type ? ' AND id_product_comment_criterion_type = '.(int)$type : '').'
			ORDER BY pccl.`name` ASC';
		$criterions = Db::getInstance()->executeS($sql);

		$types = self::getTypes();
		foreach ($criterions as $key => $data)
			$criterions[$key]['type_name'] = $types[$data['id_product_comment_criterion_type']];

		return $criterions;
	}

	public function getProducts()
	{
		$res = Db::getInstance()->executeS('
			SELECT pccp.id_product, pccp.id_product_comment_criterion
			FROM `'._DB_PREFIX_.'product_comment_criterion_product` pccp
			WHERE pccp.id_product_comment_criterion = '.(int)$this->id);
		$products = array();
		if ($res)
			foreach ($res AS $row)
				$products[] = (int)$row['id_product'];
		return $products;
	}

	public function getCategories()
	{
		$res = Db::getInstance()->executeS('
			SELECT pccc.id_category, pccc.id_product_comment_criterion
			FROM `'._DB_PREFIX_.'product_comment_criterion_category` pccc
			WHERE pccc.id_product_comment_criterion = '.(int)$this->id);
		$criterions = array();
		if ($res)
			foreach ($res AS $row)
				$criterions[] = (int)$row['id_category'];
		return $criterions;
	}

	public function deleteCategories()
	{
		return Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'product_comment_criterion_category`
			WHERE `id_product_comment_criterion` = '.(int)$this->id);
	}

	public function deleteProducts()
	{
		return Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'product_comment_criterion_product`
			WHERE `id_product_comment_criterion` = '.(int)$this->id);
	}

	public static function getTypes()
	{
		// Instance of module class for translations
		$module = new ProductComments();

		return array(
			1 => $module->l('Valid for the entire catalog', 'ProductCommentCriterion'),
			2 => $module->l('Restricted to some categories', 'ProductCommentCriterion'),
			3 => $module->l('Restricted to some products', 'ProductCommentCriterion')
		);
	}
}
