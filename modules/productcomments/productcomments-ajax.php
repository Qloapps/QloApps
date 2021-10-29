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

// The usage of this file is deprecated !!!
require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
require_once(dirname(__FILE__).'/ProductCommentCriterion.php');
include_once(dirname(__FILE__).'/ProductComment.php');
include_once(dirname(__FILE__).'/productcomments.php');

$productCom = new productcomments();

if (Tools::getValue('action') && Tools::getValue('id_product_comment') && Context::getContext()->cookie->id_customer)
{
	if (Tools::getValue('action') == 'report')
	{
		if (!ProductComment::isAlreadyReport(Tools::getValue('id_product_comment'), Context::getContext()->cookie->id_customer) &&
			ProductComment::reportComment((int)Tools::getValue('id_product_comment'), (int)Context::getContext()->cookie->id_customer))
			die('0');
	}
	else if (Tools::getValue('action') == 'usefulness' && Tools::getValue('value') && Tools::getValue('value'))
	{
		if (!ProductComment::isAlreadyUsefulness(Tools::getValue('id_product_comment'), Context::getContext()->cookie->id_customer) &&
			ProductComment::setCommentUsefulness((int)Tools::getValue('id_product_comment'),
												 (bool)Tools::getValue('value'),
												 Context::getContext()->cookie->id_customer))
			die('0');
	}
}
else if (Tools::getValue('action') && Tools::getValue('secure_key') == $productCom->secure_key)
{
		$review = json_decode(Tools::getValue('review'));
		$id_product = 0;
		$content = null;
		$title = null;
		$name = null;
		$grades = array();
		foreach ($review as $entry)
		{
			if ($entry->key == 'id_product')
				$id_product = $entry->value;
			else if ($entry->key == 'title')
				$title = $entry->value;
			else if ($entry->key == 'content')
				$content = $entry->value;
			else if ($entry->key == 'customer_name')
				$name = $entry->value;
			else if (strstr($entry->key, 'grade'))
			{
				$id = array(explode('_', $entry->key));
				$grades[] = array('id' => $id['0']['0'], 'grade' => $entry->value);
			}
		}

		if ($title == '' || $content == '' || !$id_product || count($grades) == 0)
			die('0');

		$allow_guests = (int)Configuration::get('PRODUCT_COMMENTS_ALLOW_GUESTS');

		if (Context::getContext()->customer->id || (!Context::getContext()->customer->id && $allow_guests))
		{
			$id_guest = (!$id_customer = (int)Context::getContext()->cookie->id_customer) ? (int)Context::getContext()->cookie->id_guest : false;
			$customerComment = ProductComment::getByCustomer((int)($id_product), Context::getContext()->cookie->id_customer, true, (int)$id_guest);

			if (!$customerComment || ($customerComment && (strtotime($customerComment['date_add']) + Configuration::get('PRODUCT_COMMENTS_MINIMAL_TIME')) < time()))
			{
				$errors = array();
				$customer_name = false;
				if ($id_guest && (!$customer_name = Context::getContext()->customer->firstname.' '.Context::getContext()->customer->lastname))
					$errors[] = $productCom->l('Please fill your name');

				if (!count($errors) && $content)
				{
					$comment = new ProductComment();
					$comment->content = strip_tags($content);
					$comment->id_product = (int)$id_product;
					$comment->id_customer = (int)Context::getContext()->cookie->id_customer;
					$comment->id_guest = (int)$id_guest;
					$comment->customer_name = pSQL($customer_name);
					if (!$comment->id_customer)
						$comment->customer_name = pSQL($name);
					$comment->title = pSQL($title);
					$comment->grade = 0;
					$comment->validate = 0;


					$tgrade = 0;
					$comment->save();
					foreach ($grades as $grade)
					{
						$tgrade += $grade['grade'];
						$productCommentCriterion = new ProductCommentCriterion((int)Tools::getValue('id_product_comment_criterion_'.$grade['id']));
						if ($productCommentCriterion->id)
							$productCommentCriterion->addGrade($comment->id, $grade['grade']);
					}

					if ((count($grades) - 1) >= 0)
						$comment->grade = (int)($tgrade / ((int)count($grades)));

					if (!$comment->save())
						$errors[] = $productCom->l('An error occurred while saving your comment.');
					else
						Context::getContext()->smarty->assign('confirmation', $productCom->l('Comment posted.').((int)(Configuration::get('PRODUCT_COMMENTS_MODERATE')) ? ' '.$productCom->l('Awaiting moderator validation.') : ''));

				}
				else
					$errors[] = $productCom->l('Comment text is required.');
			}
			else
				$errors[] = sprintf($productCom->l('You should wait %d seconds before posting a new comment.'), Configuration::get('PRODUCT_COMMENTS_MINIMAL_TIME'));
		}
}

die('1');

