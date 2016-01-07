<?php
class WkPaypalAdaptiveCancelUrlModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	public function initContent()
	{
		if ($this->module->active)
        {
			parent::initContent();
			$link = new Link();
			if (isset($this->context->cookie->id_customer))
			{
				$this->setTemplate('cancel_url.tpl');
			}
			else
				Tools::redirect($link->getPageLink('my-account'));
		}
	}
}