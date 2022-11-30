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

if (!defined('_PS_VERSION_')) {
	exit;
}

class BlockSocial extends Module
{
	public function __construct()
	{
		$this->name = 'blocksocial';
		$this->tab = 'front_office_features';
		$this->version = '1.2.0';
		$this->author = 'PrestaShop';

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Social networking block');
		$this->description = $this->l('Allows you to add information about your brand\'s social networking accounts.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function install()
	{
		if (!parent::install()
			|| !Configuration::updateValue('BLOCKSOCIAL_FACEBOOK', '')
			|| !Configuration::updateValue('BLOCKSOCIAL_TWITTER', '')
			|| !Configuration::updateValue('BLOCKSOCIAL_RSS', '')
			|| !Configuration::updateValue('BLOCKSOCIAL_YOUTUBE', '')
			|| !Configuration::updateValue('BLOCKSOCIAL_PINTEREST', '')
			|| !Configuration::updateValue('BLOCKSOCIAL_VIMEO', '')
			|| !Configuration::updateValue('BLOCKSOCIAL_INSTAGRAM', '')
			|| !$this->registerHook('displayFooterNotificationHook')
		) {
			return false;
		}

		return true;
	}

	public function uninstall()
	{
		// Delete configuration
		if (!parent::uninstall()
			|| !Configuration::deleteByName('BLOCKSOCIAL_FACEBOOK')
			|| !Configuration::deleteByName('BLOCKSOCIAL_TWITTER')
			|| !Configuration::deleteByName('BLOCKSOCIAL_RSS')
			|| !Configuration::deleteByName('BLOCKSOCIAL_YOUTUBE')
			|| !Configuration::deleteByName('BLOCKSOCIAL_PINTEREST')
			|| !Configuration::deleteByName('BLOCKSOCIAL_VIMEO')
			|| !Configuration::deleteByName('BLOCKSOCIAL_INSTAGRAM')
		) {
			return false;
		}

		return true;
	}

	public function getContent()
	{
		// If we try to update the settings
		$output = '';
		if (Tools::isSubmit('submitModule')) {
			Configuration::updateValue('BLOCKSOCIAL_FACEBOOK', Tools::getValue('blocksocial_facebook', ''));
			Configuration::updateValue('BLOCKSOCIAL_TWITTER', Tools::getValue('blocksocial_twitter', ''));
			Configuration::updateValue('BLOCKSOCIAL_RSS', Tools::getValue('blocksocial_rss', ''));
			Configuration::updateValue('BLOCKSOCIAL_YOUTUBE', Tools::getValue('blocksocial_youtube', ''));
			Configuration::updateValue('BLOCKSOCIAL_PINTEREST', Tools::getValue('blocksocial_pinterest', ''));
			Configuration::updateValue('BLOCKSOCIAL_VIMEO', Tools::getValue('blocksocial_vimeo', ''));
			Configuration::updateValue('BLOCKSOCIAL_INSTAGRAM', Tools::getValue('blocksocial_instagram', ''));

			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&conf=4');
		}

		return $output.$this->renderForm();
	}

	public function hookDisplayFooterNotificationHook()
	{
		$this->smarty->assign(array(
			'facebook_url' => Configuration::get('BLOCKSOCIAL_FACEBOOK'),
			'twitter_url' => Configuration::get('BLOCKSOCIAL_TWITTER'),
			'rss_url' => Configuration::get('BLOCKSOCIAL_RSS'),
			'youtube_url' => Configuration::get('BLOCKSOCIAL_YOUTUBE'),
			'pinterest_url' => Configuration::get('BLOCKSOCIAL_PINTEREST'),
			'vimeo_url' => Configuration::get('BLOCKSOCIAL_VIMEO'),
			'instagram_url' => Configuration::get('BLOCKSOCIAL_INSTAGRAM'),
		));

		return $this->display(__FILE__, 'blocksocial.tpl');
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
						'label' => $this->l('Facebook URL'),
						'name' => 'blocksocial_facebook',
						'desc' => $this->l('Your Facebook fan page.'),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Twitter URL'),
						'name' => 'blocksocial_twitter',
						'desc' => $this->l('Your official Twitter account.'),
					),
					array(
						'type' => 'text',
						'label' => $this->l('RSS URL'),
						'name' => 'blocksocial_rss',
						'desc' => $this->l('The RSS feed of your choice (your blog, your store, etc.).'),
					),
					array(
						'type' => 'text',
						'label' => $this->l('YouTube URL'),
						'name' => 'blocksocial_youtube',
						'desc' => $this->l('Your official YouTube account.'),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Pinterest URL:'),
						'name' => 'blocksocial_pinterest',
						'desc' => $this->l('Your official Pinterest account.'),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Vimeo URL:'),
						'name' => 'blocksocial_vimeo',
						'desc' => $this->l('Your official Vimeo account.'),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Instagram URL:'),
						'name' => 'blocksocial_instagram',
						'desc' => $this->l('Your official Instagram account.'),
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
		$helper->submit_action = 'submitModule';
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
			'blocksocial_facebook' => Tools::getValue('blocksocial_facebook', Configuration::get('BLOCKSOCIAL_FACEBOOK')),
			'blocksocial_twitter' => Tools::getValue('blocksocial_twitter', Configuration::get('BLOCKSOCIAL_TWITTER')),
			'blocksocial_rss' => Tools::getValue('blocksocial_rss', Configuration::get('BLOCKSOCIAL_RSS')),
			'blocksocial_youtube' => Tools::getValue('blocksocial_youtube', Configuration::get('BLOCKSOCIAL_YOUTUBE')),
			'blocksocial_pinterest' => Tools::getValue('blocksocial_pinterest', Configuration::get('BLOCKSOCIAL_PINTEREST')),
			'blocksocial_vimeo' => Tools::getValue('blocksocial_vimeo', Configuration::get('BLOCKSOCIAL_VIMEO')),
			'blocksocial_instagram' => Tools::getValue('blocksocial_instagram', Configuration::get('BLOCKSOCIAL_INSTAGRAM')),
		);
	}
}
