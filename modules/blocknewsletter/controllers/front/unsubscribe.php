<?php
/**
* Copyright since 2010 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright since 2010 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class BlocknewsletterUnsubscribeModuleFrontController extends ModuleFrontController
{
	/**
	 * @see FrontController::postProcess()
	 */
	public function postProcess()
	{
		$this->errors = array_merge(
			$this->errors,
			$this->module->unsubscribeByToken(Tools::getValue('token'))
		);
	}

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		$this->display_column_left = false;
		$this->display_column_right = false;

		parent::initContent();

		$this->setTemplate('unsubscription_execution.tpl');
	}

	public function setMedia()
	{
		parent::setMedia();

		$this->addJS($this->module->getPathUri().'views/js/front/redirect.js');
		Media::addJsDef(array(
			'homepage_url' => $this->context->link->getPageLink('index'),
		));
	}
}
