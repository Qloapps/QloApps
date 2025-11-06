<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

abstract class AbstractCheckoutStepCore implements CheckoutStepInterface
{
    public $step_is_reachable = 0;
    public $step_is_complete = 0;
    public $step_is_current = 0;
    public $step_key;
    private $checkoutProcess;
    protected $context;

    public function __construct()
    {
        $this->context = Context::getContext();
    }

    public function setCheckoutProcess(CheckoutProcess $checkoutProcess)
    {
        $this->checkoutProcess = $checkoutProcess;

        return $this;
    }

    public function setReachable($step_is_reachable)
    {
        $this->step_is_reachable = $step_is_reachable;

        return $this;
    }

    public function isReachable()
    {
        return $this->step_is_reachable;
    }

    public function setComplete($step_is_complete)
    {
        $this->step_is_complete = $step_is_complete;

        return $this;
    }

    public function isComplete()
    {
        return $this->step_is_complete;
    }

    public function setCurrent($step_is_current)
    {
        $this->step_is_current = $step_is_current;

        return $this;
    }

    public function isCurrent()
    {
        return $this->step_is_current;
    }
}
