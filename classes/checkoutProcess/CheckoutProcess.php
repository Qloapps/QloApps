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

class CheckoutProcessCore
{
    public $steps = array();

    public function handleRequest()
    {
        foreach ($this->steps as $step) {
            $step->handleRequest();
        }
        return $this;
    }

    public function addStep(CheckoutStepInterface $step)
    {
        $step->setCheckoutProcess($this);
        $this->steps[] = $step;
        return $this;
    }

    public function getSteps()
    {
        return $this->steps;
    }

    public function setNextStepReachable()
    {
        foreach ($this->getSteps() as $step) {
            if (!$step->isReachable()) {
                $step->setReachable(true);
                break;
            }
            if (!$step->isComplete()) {
                break;
            }
        }

        return $this;
    }

    public function markCurrentStep()
    {
        $steps = $this->getSteps();

        foreach ($steps as $step) {
            if ($step->isCurrent()) {
                // If a step marked itself as current
                // then we assume it has a good reason
                // to do so and we don't auto-advance.
                return $this;
            }
        }

        foreach ($steps as $position => $step) {
            $nextStep = ($position < count($steps) - 1) ? $steps[$position + 1] : null;

            if ($step->isReachable() && (!$step->isComplete() || ($nextStep && !$nextStep->isReachable()))) {
                $step->setCurrent(true);

                return $this;
            }
        }

        return $this;
    }

    public function invalidateAllStepsAfterCurrent()
    {
        $markAsUnreachable = false;
        foreach ($this->getSteps() as $step) {
            if ($markAsUnreachable) {
                $step->setComplete(false)->setReachable(false);
            }

            if ($step->isCurrent()) {
                $markAsUnreachable = true;
            }
        }

        return $this;
    }

    // if any event occurred which is changing cart/address/payment/customer then reset cehckout cookies
    public static function refreshCheckoutProcess()
    {
        $context = Context::getContext();
        $context->cookie->__set('cart_summary_proceeded', 0);
        $context->cookie->__set('customer_details_proceeded', 0);
    }
}
