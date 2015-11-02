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

class Advice extends ObjectModel
{
	public $id;
	
	public $id_ps_advice;
		
	public $id_tab;
	
	public $validated;

	public $hide;
	
	public $selector;
	
	public $location;
	
	public $html;
	
	public $start_day;
	
	public $stop_day;

	public $weight;
	
	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'advice',
		'primary' => 'id_advice',
		'multilang' => true,
		'fields' => array(
			'id_ps_advice' =>	array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'id_tab' =>			array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'selector' =>		array('type' => self::TYPE_STRING),
			'location' =>		array('type' => self::TYPE_STRING),
			'validated' =>		array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'start_day' =>		array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'stop_day' =>		array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'weight' =>			array('type' => self::TYPE_INT, 'validate' => 'isInt'),

			// Lang fields
			'html' => 			array('type' => self::TYPE_HTML, 'lang' => true, 'required' => true, 'validate' => 'isString'),
		),
	);
	
	public static function getIdByIdPs($id_ps_advice)
	{
		$query = new DbQuery();
		$query->select('id_advice');
		$query->from('advice', 'b');
		$query->where('`id_ps_advice` = '.(int)$id_ps_advice);
		
		return (int)Db::getInstance()->getValue($query);
	}

		public static function getValidatedByIdTab($id_tab, $premium = false, $addons = false)
	{
		$query = new DbQuery();
		$query->select('a.`id_ps_advice`, a.`selector`, a.`location`, al.`html`, a.`weight`');
		$query->from('advice', 'a');
		$query->join('
			LEFT JOIN `'._DB_PREFIX_.'advice_lang` al ON al.`id_advice` = a.`id_advice`
			LEFT JOIN `'._DB_PREFIX_.'tab_advice` at ON at.`id_advice` = a.`id_advice` ');
		
		$query->where('
			a.`validated` = 1 AND 
			a.`hide` = 0 AND 
			al.`id_lang` = '.(int)Context::getContext()->language->id.' AND 
			at.`id_tab` = '.(int)$id_tab.' AND 
			((a.`start_day` = 0 AND a.`stop_day` = 0) OR ('.date('d').' >= a.`start_day` AND '.date('d').' <= a.`stop_day`))');
		
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
		$advices = array();
		if (is_array($result))
			foreach ($result as $res)
				$advices[] = array(
					'selector' => $res['selector'],
					'location' => $res['location'],
					'html' => $res['html'],
					'id_ps_advice' => $res['id_ps_advice'],
					'weight' => $res['weight']
					);
		if (!$premium)
			foreach ($advices as $k => $a)
				if ($a['selector'] == '#dashtrends')
					unset($advices[$k]);
		if (!$addons)
			foreach ($advices as $k => $a)
				if ($a['selector'] == 'addons')
					unset($advices[$k]);
		
		return $advices;
	}
	
	public static function getValidatedPremiumByIdTab($id_tab)
	{
		$advices = self::getValidatedByIdTab($id_tab, true);

		foreach ($advices as $k => $a)
			if ($a['selector'] != '#dashtrends')
				unset($advices[$k]);
		
		return $advices;
	}
	
	public static function getAddonsAdviceByIdTab($id_tab)
	{
		$advices = self::getValidatedByIdTab($id_tab, false, true);
		foreach ($advices as $k => $a)
			if ($a['selector'] != 'addons')
				unset($advices[$k]);
		
		return $advices;
	}
	
	public static function getIdsAdviceToValidate()
	{
		$ids = array();
		$query = new DbQuery();
		$query->select('a.`id_advice`');
		$query->from('advice', 'a');
		$query->join('
			LEFT JOIN `'._DB_PREFIX_.'condition_advice` ca ON ca.`id_advice` = a.`id_advice` AND ca.`display` = 1 
			LEFT JOIN `'._DB_PREFIX_.'condition` c ON c.`id_condition` = ca.`id_condition` AND c.`validated` = 1');
		$query->where('a.`validated` = 0');
		$query->groupBy('a.`id_advice`');
		$query->having('count(*) = SUM(c.`validated`)');

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
		
		if (is_array($result))
			foreach($result as $advice)
				$ids[] = $advice['id_advice'];
		return $ids;
	}
	
	public static function getIdsAdviceToUnvalidate()
	{
		$ids = array();
		$query = new DbQuery();
		$query->select('a.`id_advice`');
		$query->from('advice', 'a');
		$query->join('
			LEFT JOIN `'._DB_PREFIX_.'condition_advice` ca ON ca.`id_advice` = a.`id_advice` AND ca.`display` = 0 
			LEFT JOIN `'._DB_PREFIX_.'condition` c ON c.`id_condition` = ca.`id_condition` AND c.`validated` = 1');
		$query->where('a.`validated` = 1');
		$query->groupBy('a.`id_advice`');
		$query->having('count(*) = SUM(c.`validated`)');

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
		
		if (is_array($result))
			foreach($result as $advice)
				$ids[] = $advice['id_advice'];
			return $ids;
	}
}
