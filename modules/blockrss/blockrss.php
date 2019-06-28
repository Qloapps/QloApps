<?php
/*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

include_once(_PS_CLASS_DIR_.'../tools/pear/PEAR.php');
include_once(_PS_PEAR_XML_PARSER_PATH_.'Parser.php');

class Blockrss extends Module
{

	private static $xmlFields = array('title', 'guid', 'description', 'author', 'comments', 'pubDate', 'source', 'link', 'content');

 	function __construct()
 	{
 	 	$this->name = 'blockrss';
 	 	$this->tab = 'front_office_features';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('RSS feed block');
		$this->description = $this->l('Adds a block displaying a RSS feed.');

		$this->version = '1.3.2';
		$this->author = 'PrestaShop';
		$this->error = false;
		$this->valid = false;
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6.99.99');
 	}

 	function install()
 	{
 	 	if (!parent::install())
			return false;

		Configuration::updateValue('RSS_FEED_TITLE', $this->l('RSS feed'));
		Configuration::updateValue('RSS_FEED_NBR', 5);

		return ($this->registerHook('header') && $this->registerHook('leftColumn'));
  	}

	public function getContent()
	{
		$output = '';

		if (Tools::isSubmit('submitBlockRss'))
		{
			$errors = array();
			$urlfeed = Tools::getValue('RSS_FEED_URL');
			$title = Tools::getValue('RSS_FEED_TITLE');
			$nbr = (int)Tools::getValue('RSS_FEED_NBR');

			if ($urlfeed AND !Validate::isAbsoluteUrl($urlfeed))
				$errors[] = $this->l('Invalid feed URL');
			elseif (!$title OR empty($title) OR !Validate::isGenericName($title))
				$errors[] = $this->l('Invalid title');
			elseif (!$nbr OR $nbr <= 0 OR !Validate::isInt($nbr))
				$errors[] = $this->l('Invalid number of feeds');
			elseif (stristr($urlfeed, $_SERVER['HTTP_HOST'].__PS_BASE_URI__))
				$errors[] = $this->l('You have selected a feed URL from your own website. Please choose another URL.');
			elseif (!($contents = Tools::file_get_contents($urlfeed)))
				$errors[] = $this->l('Feed is unreachable, check your URL');
			/* Even if the feed was reachable, We need to make sure that the feed is well formated */
			else
			{
				try
				{
					$xmlFeed = new XML_Feed_Parser($contents);
				}
				catch (XML_Feed_Parser_Exception $e)
				{
					$errors[] = $this->l('Invalid feed:').' '.$e->getMessage();
				}
			}

			if (!sizeof($errors))
			{
				Configuration::updateValue('RSS_FEED_URL', $urlfeed);
				Configuration::updateValue('RSS_FEED_TITLE', $title);
				Configuration::updateValue('RSS_FEED_NBR', $nbr);

				$output .= $this->displayConfirmation($this->l('Settings updated'));
			}
			else
				$output .= $this->displayError(implode('<br />', $errors));
		}
		else
		{
			$errors = array();
			if (stristr(Configuration::get('RSS_FEED_URL'), $_SERVER['HTTP_HOST'].__PS_BASE_URI__))
				$errors[] = $this->l('You have selected a feed URL from your own website. Please choose another URL.');

			if (sizeof($errors))
				$output .= $this->displayError(implode('<br />', $errors));
		}
		return $output.$this->renderForm();
	}

	function hookLeftColumn($params)
	{
		// Conf
		$title = strval(Configuration::get('RSS_FEED_TITLE'));
		$url = strval(Configuration::get('RSS_FEED_URL'));
		$nb = (int)(Configuration::get('RSS_FEED_NBR'));

		$cacheId = $this->getCacheId($this->name.'-'.date("YmdH"));
		if (!$this->isCached('blockrss.tpl', $cacheId))
		{
			// Getting data
			$rss_links = array();
			if ($url && ($contents = Tools::file_get_contents($url)))
				try
				{
					if (@$src = new XML_Feed_Parser($contents))
						for ($i = 0; $i < ($nb ? $nb : 5); $i++)
							if (@$item = $src->getEntryByOffset($i))
							{
								$xmlValues = array();
								foreach (self::$xmlFields as $xmlField)
									$xmlValues[$xmlField] = $item->__get($xmlField);
								$xmlValues['enclosure'] = $item->getEnclosure();

								// First image
								if (isset($xmlValues['content']) && $xmlValues['content'])
								{
									preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/', $xmlValues['content'], $image);
									if (array_key_exists(1, $image) && $image[1])
									{
										if ($image[1][0] == '/')
											$image[1] = 'http:'.$image[1];
										// Try if distant image exist : timeout 0.3s
										$context = stream_context_create(array('http' => array('timeout' => 0.3)));
										if (file_get_contents($image[1], false, $context, -1, 1) !== false)
											$xmlValues['image'] = $image[1];
									}
								}

								// Compatibility
								$xmlValues['url'] = $xmlValues['link'];
								$rss_links[] = $xmlValues;
							}
				}
				catch (XML_Feed_Parser_Exception $e)
				{
					Tools::dieOrLog(sprintf($this->l('Error: invalid RSS feed in "blockrss" module: %s'), $e->getMessage()), false);
				}
			// Display smarty
			$this->smarty->assign(array('title' => ($title ? $title : $this->l('RSS feed')), 'rss_links' => $rss_links));
		}
 	 	return $this->display(__FILE__, 'blockrss.tpl', $cacheId);
 	}

	function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}

	function hookHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'blockrss.css', 'all');
	}

	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Block title'),
						'name' => 'RSS_FEED_TITLE',
						'desc' => $this->l('Create a title for the block (default: \'RSS feed\').'),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Add a feed URL'),
						'name' => 'RSS_FEED_URL',
						'desc' => $this->l('Add the URL of the feed you want to use (sample: http://news.google.com/?output=rss).'),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Number of threads displayed'),
						'name' => 'RSS_FEED_NBR',
						'class' => 'fixed-width-sm',
						'desc' => $this->l('Number of threads displayed in the block (default value: 5).'),
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitBlockRss';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		return array(
			'RSS_FEED_TITLE' => Tools::getValue('RSS_FEED_TITLE', Configuration::get('RSS_FEED_TITLE')),
			'RSS_FEED_URL' => Tools::getValue('RSS_FEED_URL', Configuration::get('RSS_FEED_URL')),
			'RSS_FEED_NBR' => Tools::getValue('RSS_FEED_NBR', Configuration::get('RSS_FEED_NBR')),
		);
	}
}
