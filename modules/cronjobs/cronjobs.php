<?php
/**
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

if (defined('_PS_ADMIN_DIR_') === false)
	define('_PS_ADMIN_DIR_', _PS_ROOT_DIR_.'/admin/');

require_once(dirname(__FILE__).'/classes/CronJobsForms.php');

class CronJobs extends Module
{
	const EACH = -1;

	protected $_successes;
	protected $_warnings;

	public $webservice_url = 'http://webcron.prestashop.com/crons';

	public function __construct()
	{
		$this->name = 'cronjobs';
		$this->tab = 'administration';
		$this->version = '1.3.2';
		$this->module_key = '';

		$this->controllers = array('callback');

		$this->author = 'PrestaShop';
		$this->need_instance = true;

		$this->bootstrap = true;
		$this->display = 'view';

		parent::__construct();

		if ($this->id)
			$this->init();

		$this->displayName = $this->l('Cron tasks manager');
		$this->description = $this->l('Manage all your automated web tasks from a single interface.');

		if (function_exists('curl_init') == false)
			$this->warning = $this->l('To be able to use this module, please activate cURL (PHP extension).');
	}

	public function install()
	{
		Configuration::updateValue('CRONJOBS_ADMIN_DIR', Tools::encrypt($this->getAdminDir()));
		Configuration::updateValue('CRONJOBS_MODE', 'webservice');
		Configuration::updateValue('CRONJOBS_MODULE_VERSION', $this->version);
		Configuration::updateValue('CRONJOBS_WEBSERVICE_ID', 0);

		$token = Tools::encrypt(Tools::getShopDomainSsl().time());
		Configuration::updateGlobalValue('CRONJOBS_EXECUTION_TOKEN', $token);

		if (parent::install())
		{
			$this->updateWebservice(true);

			return $this->installDb() && $this->installTab() &&
				$this->registerHook('actionModuleRegisterHookAfter') &&
				$this->registerHook('actionModuleUnRegisterHookAfter') &&
				$this->registerHook('backOfficeHeader');
		}

		return false;
	}

	protected function getAdminDir()
	{
		return basename(_PS_ADMIN_DIR_);
	}

	protected function init()
	{
		$new_admin_dir = (Tools::encrypt($this->getAdminDir()) != Configuration::get('CRONJOBS_ADMIN_DIR'));
		$new_module_version = version_compare($this->version, Configuration::get('CRONJOBS_MODULE_VERSION'), '!=');

		if ($new_admin_dir || $new_module_version)
		{
			Configuration::updateValue('CRONJOBS_MODULE_VERSION', $this->version);
			Configuration::updateValue('CRONJOBS_ADMIN_DIR', Tools::encrypt($this->getAdminDir()));

			if (Configuration::get('CRONJOBS_MODE') == 'webservice')
				return $this->enableWebservice();
			return $this->disableWebservice();
		}
	}

	public function uninstall()
	{
		Configuration::deleteByName('CRONJOBS_MODE');

		$this->disableWebservice();

		return	$this->uninstallDb() &&
			$this->uninstallTab() &&
			parent::uninstall();
	}

	public function installDb()
	{
		return Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.$this->name.' (
			`id_cronjob` INTEGER(10) NOT NULL AUTO_INCREMENT,
			`id_module` INTEGER(10) DEFAULT NULL,
			`description` TEXT DEFAULT NULL,
			`task` TEXT DEFAULT NULL,
			`hour` INTEGER DEFAULT \'-1\',
			`day` INTEGER DEFAULT \'-1\',
			`month` INTEGER DEFAULT \'-1\',
			`day_of_week` INTEGER DEFAULT \'-1\',
			`updated_at` DATETIME DEFAULT NULL,
			`one_shot` BOOLEAN NOT NULL DEFAULT 0,
			`active` BOOLEAN DEFAULT FALSE,
			`id_shop` INTEGER DEFAULT \'0\',
			`id_shop_group` INTEGER DEFAULT \'0\',
			PRIMARY KEY(`id_cronjob`),
			INDEX (`id_module`))
			ENGINE='._MYSQL_ENGINE_.' default CHARSET=utf8'
		);
	}

	public function uninstallDb()
	{
		return Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name);
	}

	public function installTab()
	{
		$tab = new Tab();
		$tab->active = 1;
		$tab->name = array();
		$tab->class_name = 'AdminCronJobs';

		foreach (Language::getLanguages(true) as $lang)
			$tab->name[$lang['id_lang']] = 'Cron Jobs';

		$tab->id_parent = -1;
		$tab->module = $this->name;

		return $tab->add();
	}

	public function uninstallTab()
	{
		$id_tab = (int)Tab::getIdFromClassName('AdminCronJobs');

		if ($id_tab)
		{
			$tab = new Tab($id_tab);
			return $tab->delete();
		}

		return false;
	}

	public function hookActionModuleRegisterHookAfter($params)
	{
		$hook_name = $params['hook_name'];

		if ($hook_name == 'actionCronJob')
		{
			$module = $params['object'];
			$this->registerModuleHook($module->id);
			$this->updateWebservice(Configuration::get('CRONJOBS_MODE') == 'webservice');
		}
	}

	public function hookActionModuleUnRegisterHookAfter($params)
	{
		$hook_name = $params['hook_name'];

		if ($hook_name == 'actionCronJob')
		{
			$module = $params['object'];
			$this->unregisterModuleHook($module->id);
			$this->updateWebservice(Configuration::get('CRONJOBS_MODE') == 'webservice');
		}
	}

	public function hookBackOfficeHeader()
	{
		if (Tools::getValue('configure') == $this->name)
		{
			if (version_compare(_PS_VERSION_, '1.6', '<') == true)
			{
				$this->context->controller->addCSS($this->_path.'css/bootstrap.min.css');
				$this->context->controller->addCSS($this->_path.'css/configure-ps-15.css');
			}
			else
				$this->context->controller->addCSS($this->_path.'css/configure-ps-16.css');
		}
	}

	public function getContent()
	{
		$output = null;
		CronJobsForms::init($this);
		$this->checkLocalEnvironment();

		if (Tools::isSubmit('submitCronJobs'))
			$this->postProcessConfiguration();
		elseif (Tools::isSubmit('submitNewCronJob'))
			$submit_cron = $this->postProcessNewJob();
		elseif (Tools::isSubmit('submitUpdateCronJob'))
			$submit_cron = $this->postProcessUpdateJob();

		$this->context->smarty->assign(array(
			'module_dir' => $this->_path,
			'module_local_dir' => $this->local_path,
		));

		$this->context->smarty->assign('form_errors', $this->_errors);
		$this->context->smarty->assign('form_infos', $this->_warnings);
		$this->context->smarty->assign('form_successes', $this->_successes);

		if ((Tools::isSubmit('submitNewCronJob') || Tools::isSubmit('newcronjobs') || Tools::isSubmit('updatecronjobs')) &&
			((isset($submit_cron) == false) || ($submit_cron === false)))
		{
			$back_url = $this->context->link->getAdminLink('AdminModules', false)
				.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name
				.'&token='.Tools::getAdminTokenLite('AdminModules');
		}

		$output = $output.$this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

		if (Tools::isSubmit('newcronjobs') || ((isset($submit_cron) == true) && ($submit_cron === false)))
			$output = $output.$this->renderForm(CronJobsForms::getJobForm(), CronJobsForms::getNewJobFormValues(), 'submitNewCronJob', true, $back_url);
		elseif (Tools::isSubmit('updatecronjobs') && Tools::isSubmit('id_cronjob'))
		{
			$form_structure = CronJobsForms::getJobForm('Update cron task', true);
			$form = $this->renderForm($form_structure, CronJobsForms::getUpdateJobFormValues(), 'submitUpdateCronJob', true, $back_url, true);
			$output = $output.$form;
		}
		elseif (Tools::isSubmit('deletecronjobs') && Tools::isSubmit('id_cronjob'))
			$this->postProcessDeleteCronJob((int)Tools::getValue('id_cronjob'));
		elseif (Tools::isSubmit('oneshotcronjobs'))
			$this->postProcessUpdateJobOneShot();
		elseif (Tools::isSubmit('statuscronjobs'))
			$this->postProcessUpdateJobStatus();
		elseif (defined('_PS_HOST_MODE_') == false)
			$output = $output.$this->renderForm(CronJobsForms::getForm(), CronJobsForms::getFormValues(), 'submitCronJobs');

		return $output.$this->renderTasksList();
	}

	public function sendCallback()
	{
		ignore_user_abort(true);
		set_time_limit(0);

		ob_start();
		echo $this->name.'_prestashop';
		header('Connection: close');
		header('Content-Length: '.ob_get_length());
		ob_end_flush();
		ob_flush();
		flush();

		if (function_exists('fastcgi_finish_request'))
			fastcgi_finish_request();
	}

	public static function isActive($id_module)
	{
		$module = Module::getInstanceByName('cronjobs');

		if (($module == false) || ($module->active == false))
			return false;

		$query = 'SELECT `active` FROM '._DB_PREFIX_.'cronjobs WHERE `id_module` = \''.(int)$id_module.'\'';
		return (bool)Db::getInstance()->getValue($query);
	}

	/**
	 * $taks should be a valid URL
	 */
	public static function addOneShotTask($task, $description, $execution = array())
	{
		if (self::isTaskURLValid($task) == false)
			return false;

		$id_shop = (int)Context::getContext()->shop->id;
		$id_shop_group = (int)Context::getContext()->shop->id_shop_group;

		$query = 'SELECT `active` FROM '._DB_PREFIX_.'cronjobs
			WHERE `task` = \''.urlencode($task).'\' AND `updated_at` IS NULL
				AND `one_shot` IS TRUE
				AND `id_shop` = \''.$id_shop.'\' AND `id_shop_group` = \''.$id_shop_group.'\'';

		if ((bool)Db::getInstance()->getValue($query) == true)
			return true;

		if (count($execution) == 0)
		{
			$query = 'INSERT INTO '._DB_PREFIX_.'cronjobs
				(`description`, `task`, `hour`, `day`, `month`, `day_of_week`, `updated_at`, `one_shot`, `active`, `id_shop`, `id_shop_group`)
				VALUES (\''.$description.'\', \''.urlencode($task).'\', \'0\', \''.CronJobs::EACH.'\', \''.CronJobs::EACH.'\', \''.CronJobs::EACH.'\',
					NULL, TRUE, TRUE, '.$id_shop.', '.$id_shop_group.')';

			return Db::getInstance()->execute($query);
		}
		else
		{
			$is_frequency_valid = true;
			$hour = (int)$execution['hour'];
			$day = (int)$execution['day'];
			$month = (int)$execution['month'];
			$day_of_week = (int)$execution['day_of_week'];

			$is_frequency_valid = (($hour >= -1) && ($hour < 24) && $is_frequency_valid);
			$is_frequency_valid = (($day >= -1) && ($day <= 31) && $is_frequency_valid);
			$is_frequency_valid = (($month >= -1) && ($month <= 31) && $is_frequency_valid);
			$is_frequency_valid = (($day_of_week >= -1) && ($day_of_week < 7) && $is_frequency_valid);

			if ($is_frequency_valid == true)
			{
				$query = 'INSERT INTO '._DB_PREFIX_.'cronjobs
					(`description`, `task`, `hour`, `day`, `month`, `day_of_week`, `updated_at`, `one_shot`, `active`, `id_shop`, `id_shop_group`)
					VALUES (\''.$description.'\', \''.urlencode($task).'\', \''.$hour.'\', \''.$day.'\', \''.$month.'\', \''.$day_of_week.'\',
						NULL, TRUE, TRUE, '.$id_shop.', '.$id_shop_group.')';

				return Db::getInstance()->execute($query);
			}
		}

		return false;
	}

	protected function checkLocalEnvironment()
	{
		if ($this->isLocalEnvironment() == true)
			$this->setWarningMessage('You are using the Cron jobs module on a local installation:
				you will not be able to use the Basic mode or reliably call remote cron tasks in your current environment.
				To use this module at its best, you should switch to an online installation.');
	}

	protected function isLocalEnvironment()
	{
		if (isset($_SERVER['REMOTE_ADDR']) === false)
			return true;

		return in_array(Tools::getRemoteAddr(), array('127.0.0.1', '::1'));
	}

	protected function renderForm($form, $form_values, $action, $cancel = false, $back_url = false, $update = false)
	{
		$helper = new HelperForm();

		$helper->show_toolbar = false;
		$helper->module = $this;
		$helper->default_form_language = $this->context->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

		$helper->identifier = $this->identifier;
		$helper->submit_action = $action;

		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
		.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;

		if ($update == true)
			$helper->currentIndex .= '&id_cronjob='.(int)Tools::getValue('id_cronjob');

		$helper->token = Tools::getAdminTokenLite('AdminModules');

		$helper->tpl_vars = array(
			'fields_value' => $form_values,
			'id_language' => $this->context->language->id,
			'languages' => $this->context->controller->getLanguages(),
			'back_url' => $back_url,
			'show_cancel_button' => $cancel,
		);

		return $helper->generateForm($form);
	}

	protected function renderTasksList()
	{
		$helper = new HelperList();

		$helper->title = $this->l('Cron tasks');
		$helper->table = $this->name;
		$helper->no_link = true;
		$helper->shopLinkType = '';
		$helper->identifier = 'id_cronjob';
		$helper->actions = array('edit', 'delete');

		$values = CronJobsForms::getTasksListValues();
		$helper->listTotal = count($values);
		$helper->tpl_vars = array('show_filters' => false);

		$helper->toolbar_btn['new'] = array(
			'href' => $this->context->link->getAdminLink('AdminModules', false)
			.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name
			.'&newcronjobs=1&token='.Tools::getAdminTokenLite('AdminModules'),
			'desc' => $this->l('Add new task')
		);

		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
			.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;

		return $helper->generateList($values, CronJobsForms::getTasksList());
	}

	protected function postProcessConfiguration()
	{
		if (Tools::isSubmit('cron_mode') == true)
		{
			if (Tools::getValue('cron_mode') == 'advanced')
				return $this->disableWebservice();
			return $this->enableWebservice();
		}
	}

	protected function postProcessNewJob()
	{
		if ($this->isNewJobValid() == true)
		{
			$description = Tools::getValue('description');
			$task = urlencode(Tools::getValue('task'));
			$hour = (int)Tools::getValue('hour');
			$day = (int)Tools::getValue('day');
			$month = (int)Tools::getValue('month');
			$day_of_week = (int)Tools::getValue('day_of_week');

			$result = Db::getInstance()->getRow('SELECT id_cronjob FROM '._DB_PREFIX_.$this->name.'
				WHERE `task` = \''.$task.'\' AND `hour` = \''.$hour.'\' AND `day` = \''.$day.'\'
				AND `month` = \''.$month.'\' AND `day_of_week` = \''.$day_of_week.'\'');

			if ($result == false)
			{
				$id_shop = (int)Context::getContext()->shop->id;
				$id_shop_group = (int)Context::getContext()->shop->id_shop_group;

				$query = 'INSERT INTO '._DB_PREFIX_.$this->name.'
					(`description`, `task`, `hour`, `day`, `month`, `day_of_week`, `updated_at`, `active`, `id_shop`, `id_shop_group`)
					VALUES (\''.$description.'\', \''.$task.'\', \''.$hour.'\', \''.$day.'\', \''.$month.'\', \''.$day_of_week.'\', NULL, TRUE, '.$id_shop.', '.$id_shop_group.')';

				if (($result = Db::getInstance()->execute($query)) != false)
					return $this->setSuccessMessage('The task has been successfully added.');
				return $this->setErrorMessage('An error happened: the task could not be added.');
			}

			return $this->setErrorMessage('This cron task already exists.');
		}

		return false;
	}

	protected function postProcessUpdateJob()
	{
		if (Tools::isSubmit('id_cronjob') == false)
			return false;

		$description = Tools::getValue('description');
		$task = urlencode(Tools::getValue('task'));
		$hour = (int)Tools::getValue('hour');
		$day = (int)Tools::getValue('day');
		$month = (int)Tools::getValue('month');
		$day_of_week = (int)Tools::getValue('day_of_week');
		$id_cronjob = (int)Tools::getValue('id_cronjob');

		$id_shop = (int)Context::getContext()->shop->id;
		$id_shop_group = (int)Context::getContext()->shop->id_shop_group;

		$query = 'UPDATE '._DB_PREFIX_.$this->name.'
			SET `description` = \''.$description.'\',
				`task` = \''.$task.'\',
				`hour` = \''.$hour.'\',
				`day` = \''.$day.'\',
				`month` = \''.$month.'\',
				`day_of_week` = \''.$day_of_week.'\'
			WHERE `id_cronjob` = \''.(int)$id_cronjob.'\'';

		if (($result = Db::getInstance()->execute($query)) != false)
			return $this->setSuccessMessage('The task has been updated.');
		return $this->setErrorMessage('The task has not been updated');
	}

	public function addNewModulesTasks()
	{
		$crons = Hook::getHookModuleExecList('actionCronJob');

		if ($crons == false)
			return false;

		$id_shop = (int)Context::getContext()->shop->id;
		$id_shop_group = (int)Context::getContext()->shop->id_shop_group;

		foreach ($crons as $cron)
		{
			$id_module = (int)$cron['id_module'];
			$module = Module::getInstanceById((int)$cron['id_module']);

			if ($module == false)
			{
				Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.$this->name.' WHERE `id_cronjob` = \''.(int)$cron['id_cronjob'].'\'');
				break;
			}

			$cronjob = (bool)Db::getInstance()->getValue('SELECT `id_cronjob` FROM `'._DB_PREFIX_.$this->name.'`
				WHERE `id_module` = \''.$id_module.'\' AND `id_shop` = \''.$id_shop.'\' AND `id_shop_group` = \''.$id_shop_group.'\'');

			if ($cronjob == false)
				$this->registerModuleHook($id_module);
		}
	}

	protected function postProcessUpdateJobOneShot()
	{
		if (Tools::isSubmit('id_cronjob') == false)
			return false;

		$id_cronjob = (int)Tools::getValue('id_cronjob');
		$id_shop = (int)Context::getContext()->shop->id;
		$id_shop_group = (int)Context::getContext()->shop->id_shop_group;

		Db::getInstance()->execute('UPDATE '._DB_PREFIX_.$this->name.'
			SET `one_shot` = IF (`one_shot`, 0, 1) WHERE `id_cronjob` = \''.(int)$id_cronjob.'\'');

		Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', false)
			.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name
			.'&token='.Tools::getAdminTokenLite('AdminModules'));
	}

	protected function postProcessUpdateJobStatus()
	{
		if (Tools::isSubmit('id_cronjob') == false)
			return false;

		$id_cronjob = (int)Tools::getValue('id_cronjob');
		$id_shop = (int)Context::getContext()->shop->id;
		$id_shop_group = (int)Context::getContext()->shop->id_shop_group;

		Db::getInstance()->execute('UPDATE '._DB_PREFIX_.$this->name.'
			SET `active` = IF (`active`, 0, 1) WHERE `id_cronjob` = \''.(int)$id_cronjob.'\'');

		Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', false)
			.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name
			.'&token='.Tools::getAdminTokenLite('AdminModules'));
	}

	protected function isNewJobValid()
	{
		if ((Tools::isSubmit('description') == true) &&
			(Tools::isSubmit('task') == true) &&
			(Tools::isSubmit('hour') == true) &&
			(Tools::isSubmit('day') == true) &&
			(Tools::isSubmit('month') == true) &&
			(Tools::isSubmit('day_of_week') == true))
		{
			if (self::isTaskURLValid(Tools::getValue('task')) == false)
				return $this->setErrorMessage('The target link you entered is not valid. It should be an absolute URL, on the same domain as your shop.');

			$hour = Tools::getValue('hour');
			$day = Tools::getValue('day');
			$month = Tools::getValue('month');
			$day_of_week = Tools::getValue('day_of_week');

			return $this->isFrequencyValid($hour, $day, $month, $day_of_week);
		}

		return false;
	}

	protected function isFrequencyValid($hour, $day, $month, $day_of_week)
	{
		$success = true;

		if ((($hour >= -1) && ($hour < 24)) == false)
			$success &= $this->setErrorMessage('The value you chose for the hour is not valid. It should be between 00:00 and 23:59.');
		if ((($day >= -1) && ($day <= 31)) == false)
			$success &= $this->setErrorMessage('The value you chose for the day is not valid.');
		if ((($month >= -1) && ($month <= 31)) == false)
			$success &= $this->setErrorMessage('The value you chose for the month is not valid.');
		if ((($day_of_week >= -1) && ($day_of_week < 7)) == false)
			$success &= $this->setErrorMessage('The value you chose for the day of the week is not valid.');

		return $success;
	}

	protected static function isTaskURLValid($task)
	{
		$task = urlencode($task);
		$shop_url = urlencode(Tools::getShopDomain(true, true).__PS_BASE_URI__);
		$shop_url_ssl = urlencode(Tools::getShopDomainSsl(true, true).__PS_BASE_URI__);

		return ((strpos($task, $shop_url) === 0) || (strpos($task, $shop_url_ssl) === 0));
	}

	protected function setErrorMessage($message)
	{
		$this->_errors[] = $this->l($message);
		return false;
	}

	protected function setSuccessMessage($message)
	{
		$this->_successes[] = $this->l($message);
		return true;
	}

	protected function setWarningMessage($message)
	{
		$this->_warnings[] = $this->l($message);
		return false;
	}

	protected function enableWebservice()
	{
		Configuration::updateValue('CRONJOBS_MODE', 'webservice');
		$this->updateWebservice(true);
	}

	protected function disableWebservice()
	{
		Configuration::updateValue('CRONJOBS_MODE', 'advanced');
		$this->updateWebservice(false);
	}

	protected function updateWebservice($use_webservice)
	{
		$link = new Link();
		$admin_folder = $this->getAdminDir();
		$path = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.$admin_folder;
		$cron_url = $path.'/'.$link->getAdminLink('AdminCronJobs', false);

		$webservice_id = Configuration::get('CRONJOBS_WEBSERVICE_ID') ? '/'.Configuration::get('CRONJOBS_WEBSERVICE_ID') : null;

		$data = array(
			'callback' => $link->getModuleLink($this->name, 'callback'),
			'domain' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__,
			'cronjob' => $cron_url.'&token='.Configuration::getGlobalValue('CRONJOBS_EXECUTION_TOKEN'),
			'cron_token' => Configuration::getGlobalValue('CRONJOBS_EXECUTION_TOKEN'),
			'active' => (bool)$use_webservice
		);

		$context_options = array('http' => array(
			'method' => (is_null($webservice_id) == true) ? 'POST' : 'PUT',
			'content' => http_build_query($data)
		));

		$result = Tools::file_get_contents($this->webservice_url.$webservice_id, false, stream_context_create($context_options));

		if ($result != false)
			Configuration::updateValue('CRONJOBS_WEBSERVICE_ID', (int)$result);

		if (((Tools::isSubmit('install') == true) || (Tools::isSubmit('reset') == true)) && ((bool)$result == false))
			return true;
		elseif (((Tools::isSubmit('install') == false) || (Tools::isSubmit('reset') == false)) && ((bool)$result == false))
			return $this->setErrorMessage('An error occurred while trying to contact PrestaShop\'s cron tasks webservice.');
		elseif ($this->isLocalEnvironment() == true)
			return true;

		if ((bool)$use_webservice == true)
			return $this->setSuccessMessage('Your cron tasks have been successfully added to PrestaShop\'s cron tasks webservice.');
		return $this->setSuccessMessage('Your cron tasks have been successfully registered using the Advanced mode.');
	}

	protected function postProcessDeleteCronJob($id_cronjob)
	{
		$id_cronjob = Tools::getValue('id_cronjob');
		$id_module = Db::getInstance()->getValue('SELECT `id_module` FROM '._DB_PREFIX_.$this->name.' WHERE `id_cronjob` = \''.(int)$id_cronjob.'\'');

		if ((bool)$id_module == false)
			Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.$this->name.' WHERE `id_cronjob` = \''.(int)$id_cronjob.'\'');
		else
			Db::getInstance()->execute('UPDATE '._DB_PREFIX_.$this->name.' SET `active` = FALSE WHERE `id_cronjob` = \''.(int)$id_cronjob.'\'');

		return Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', false)
			.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name
			.'&token='.Tools::getAdminTokenLite('AdminModules'));
	}

	protected function registerModuleHook($id_module)
	{
		$module = Module::getInstanceById($id_module);
		$id_shop = (int)Context::getContext()->shop->id;
		$id_shop_group = (int)Context::getContext()->shop->id_shop_group;

		if (is_callable(array($module, 'getCronFrequency')) == true)
		{
			$frequency = $module->getCronFrequency();

			$query = 'INSERT INTO '._DB_PREFIX_.$this->name.'
				(`id_module`, `hour`, `day`, `month`, `day_of_week`, `active`, `id_shop`, `id_shop_group`)
				VALUES (\''.$id_module.'\', \''.$frequency['hour'].'\', \''.$frequency['day'].'\',
					\''.$frequency['month'].'\', \''.$frequency['day_of_week'].'\',
					TRUE, '.$id_shop.', '.$id_shop_group.')';
		}
		else
			$query = 'INSERT INTO '._DB_PREFIX_.$this->name.'
				(`id_module`, `active`, `id_shop`, `id_shop_group`)
				VALUES ('.$id_module.', FALSE, '.$id_shop.', '.$id_shop_group.')';

		return Db::getInstance()->execute($query);
	}

	protected function unregisterModuleHook($id_module)
	{
		return Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.$this->name.' WHERE `id_module` = \''.(int)$id_module.'\'');
	}
}
