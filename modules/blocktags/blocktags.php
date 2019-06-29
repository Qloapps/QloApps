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

class BlockTags extends Module
{
	function __construct()
	{
		$this->name = 'blocktags';
		$this->tab = 'front_office_features';
		$this->version = '1.3.1';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Tags block');
		$this->description = $this->l('Adds a block containing your product tags.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6.99.99');
	}

	function install()
	{
		$success = (parent::install()
			&& $this->registerHook('header')
			&& $this->registerHook('leftColumn')
			&& $this->registerHook('addproduct')
			&& $this->registerHook('updateproduct')
			&& $this->registerHook('deleteproduct')
			&& Configuration::updateValue('BLOCKTAGS_NBR', 10)
			&& Configuration::updateValue('BLOCKTAGS_MAX_LEVEL', 3)
			&& Configuration::updateValue('BLOCKTAGS_RANDOMIZE', false)
		);

		$this->_clearCache('*');

		return $success;
	}

	public function uninstall()
	{
		$this->_clearCache('*');

		return parent::uninstall();
	}

	public function hookAddProduct($params)
	{
		$this->_clearCache('*');
	}

	public function hookUpdateProduct($params)
	{
		$this->_clearCache('*');
	}

	public function hookDeleteProduct($params)
	{
		$this->_clearCache('*');
	}

	public function _clearCache($template, $cache_id = NULL, $compile_id = NULL)
	{
		parent::_clearCache('blocktags.tpl');
	}

	public function getContent()
        {
                $output = '';
                $errors = array();
                if (Tools::isSubmit('submitBlockTags'))
                {
                        $tagsNbr = Tools::getValue('BLOCKTAGS_NBR');
                        if (!strlen($tagsNbr))
                                $errors[] = $this->l('Please complete the "Displayed tags" field.');
                        elseif (!Validate::isInt($tagsNbr) || (int)($tagsNbr) <= 0)
                                $errors[] = $this->l('Invalid number.');

                        $tagsLevels = Tools::getValue('BLOCKTAGS_MAX_LEVEL');
                        if (!strlen($tagsLevels))
                                $errors[] = $this->l('Please complete the "Tag levels" field.');
                        elseif (!Validate::isInt($tagsLevels) || (int)($tagsLevels) <= 0)
                                $errors[] = $this->l('Invalid value for "Tag levels". Choose a positive integer number.');

                        $randomize = Tools::getValue('BLOCKTAGS_RANDOMIZE');
                        if (!strlen($randomize))
                        	$errors[] = $this->l('Please complete the "Randomize" field.');
                        elseif (!Validate::isBool($randomize))
                        	$errors[] = $this->l('Invalid value for "Randomize". It has to be a boolean.');

                        if (count($errors))
                                $output = $this->displayError(implode('<br />', $errors));
                        else
                        {
                                Configuration::updateValue('BLOCKTAGS_NBR', (int)$tagsNbr);
                                Configuration::updateValue('BLOCKTAGS_MAX_LEVEL', (int)$tagsLevels);
                                Configuration::updateValue('BLOCKTAGS_RANDOMIZE', (bool)$randomize);

                                $output = $this->displayConfirmation($this->l('Settings updated'));
                        }
                }
                return $output.$this->renderForm();
        }

	/**
	* Returns module content for left column
	*
	* @param array $params Parameters
	* @return string Content
	*
	*/
	function hookLeftColumn($params)
	{
		if (!$this->isCached('blocktags.tpl', $this->getCacheId('blocktags')))
		{
			$tags = Tag::getMainTags((int)($params['cookie']->id_lang), (int)(Configuration::get('BLOCKTAGS_NBR')));

			$max = -1;
			$min = -1;
			foreach ($tags as $tag)
			{
				if ($tag['times'] > $max)
					$max = $tag['times'];
				if ($tag['times'] < $min || $min == -1)
					$min = $tag['times'];
			}

			if ($min == $max)
				$coef = $max;
			else
				$coef = (Configuration::get('BLOCKTAGS_MAX_LEVEL') - 1) / ($max - $min);

			if (!count($tags))
				return false;
			if (Configuration::get('BLOCKTAGS_RANDOMIZE'))
				shuffle($tags);
			foreach ($tags as &$tag)
				$tag['class'] = 'tag_level'.(int)(($tag['times'] - $min) * $coef + 1);
			$this->smarty->assign('tags', $tags);
		}
		return $this->display(__FILE__, 'blocktags.tpl', $this->getCacheId('blocktags'));
	}

	function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}

	function hookHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'blocktags.css', 'all');
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
						'label' => $this->l('Displayed tags'),
						'name' => 'BLOCKTAGS_NBR',
						'class' => 'fixed-width-xs',
						'desc' => $this->l('Set the number of tags you would like to see displayed in this block. (default: 10)')
                                        ),
                                        array(
                                                'type' => 'text',
                                                'label' => $this->l('Tag levels'),
                                                'name' => 'BLOCKTAGS_MAX_LEVEL',
                                                'class' => 'fixed-width-xs',
                                                'desc' => $this->l('Set the number of different tag levels you would like to use. (default: 3)')
                                        ),
                                        array(
                                        	'type' => 'switch',
                                        	'label' => $this->l('Random display'),
                                        	'name' => 'BLOCKTAGS_RANDOMIZE',
                                        	'class' => 'fixed-width-xs',
                                        	'desc' => $this->l('If enabled, displays tags randomly. By default, random display is disabled and the most used tags are displayed first.'),
                                        	'values' => array(
                                        		array(
                                        			'id' => 'active_on',
                                        			'value' => 1,
                                        			'label' => $this->l('Enabled')
                                        			),
                                        		array(
                                        			'id' => 'active_off',
                                        			'value' => 0,
                                        			'label' => $this->l('Disabled')
                                        		)
                                        	)
                                        )
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitBlockTags';
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
			'BLOCKTAGS_NBR' => Tools::getValue('BLOCKTAGS_NBR', (int)Configuration::get('BLOCKTAGS_NBR')),
			'BLOCKTAGS_MAX_LEVEL' => Tools::getValue('BLOCKTAGS_MAX_LEVEL', (int)Configuration::get('BLOCKTAGS_MAX_LEVEL')),
			'BLOCKTAGS_RANDOMIZE' => Tools::getValue('BLOCKTAGS_RANDOMIZE', (bool)Configuration::get('BLOCKTAGS_RANDOMIZE')),
		);
	}

}
