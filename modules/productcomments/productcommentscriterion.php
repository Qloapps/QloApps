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

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/ProductCommentCriterion.php');

if (empty($_GET['id_lang']) === false &&
	isset($_GET['id_product']) === true)
{
	$criterions = ProductCommentCriterion::get($_GET['id_lang']);
	if ((int)($_GET['id_product']))
		$selects = ProductCommentCriterion::getByProduct($_GET['id_product'], $_GET['id_lang']);
	echo '<select name="id_product_comment_criterion[]" id="id_product_comment_criterion" multiple="true" style="height:100px;width:360px;">';
	foreach ($criterions as $criterion)
	{
		echo '<option value="'.(int)($criterion['id_product_comment_criterion']).'"';
		if (isset($selects) === true && sizeof($selects))
		{
			foreach ($selects as $select)
				if ($select['id_product_comment_criterion'] == $criterion['id_product_comment_criterion'])
					echo ' selected="selected"';
		}
		echo '>'.htmlspecialchars($criterion['name'], ENT_COMPAT, 'UTF-8').'</option>';
	}
	echo '</select>';
}