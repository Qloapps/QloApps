<?php
/**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class QloWebservice
{
    protected $objOutput;
    protected $output;
    protected $wsObject;
    protected $mobikulGlobal; // Remove this
    protected $mkGlobal;
    protected $mkmodule;
    protected $http;
    protected $width;

    public function __construct($objOutput = null, $wsObject = null, $output = null)
    {
        $this->objOutput = $objOutput;
        $this->wsObject = $wsObject;
        $this->output = $output;

        // Need to remove these three settings, this should be handled by context
        // $this->id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        // $this->id_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
        // $this->currency = Currency::getCurrencyInstance((int) Configuration::get('PS_CURRENCY_DEFAULT'));

        $this->setContext();
        // $this->mkmodule = Module::getInstanceByName('mobikul');
        // $this->mobikulGlobal = new MobikulGlobal($this->id_lang, $objOutput, $wsObject, $output); // Remove this
        // $this->mkGlobal = new MobikulGlobal($this->id_lang, $objOutput, $wsObject, $output);
    }

    public function setContext()
    {
        $this->context = Context::getContext();

        // // Set Customer
        // if (isset($this->wsObject->urlFragments['id_customer'])) {
        //     $this->context->customer = new Customer($this->wsObject->urlFragments['id_customer']);
        // }

        // // Set language
        // if (isset($this->wsObject->urlFragments['id_lang'])) {
        //     $language = new Language($this->wsObject->urlFragments['id_lang']);
        //     if (!Validate::isLoadedObject($language)) {
        //         $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        //     }
        // } else {
        //     $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        // }
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $this->context->language = $language;

        // // Set Currency
        // if (isset($this->wsObject->urlFragments['id_currency'])) {
        //     $currency = new Currency($this->wsObject->urlFragments['id_currency']);
        //     if (!Validate::isLoadedObject($currency)) {
        //         $language = new Language((int)Configuration::get('PS_CURRENCY_DEFAULT'));
        //     }
        // } else {
        //     $currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));
        // }
        $currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));
        $this->context->currency = $currency;
    }

    public function getResult($message, $status = null, $args = array(), $xml = false)
    {
        $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('response', array());
        if ($status !== null) {
            $this->output .= $this->renderField('status', $status);
        }

        $this->output .= $this->renderField('message', $message);

        if (is_array($args) && !empty($args)) {
            foreach ($args as $key => $value) {
                $this->output .= $this->renderField($key, $value);
            }
        }

        if ($xml) {
            $this->output .= $xml;
        }

        $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('response', array());
        return $this->output;
    }

    /**
     * Create just like PS renderField function in WebServiceOutputXML class
     * @todo :: to be mode functional with attributes and others
     * @param  [type] $nodeName
     * @param  [type] $value
     * @return [type] xml node
     */
    public function renderField($nodeName, $value = null)
    {
        $node_content = '<![CDATA['.$value.']]>';
        return '<'.$nodeName.'>'.$node_content.'</'.$nodeName.'>'."\n";
    }

    // public function displayPrice($price, $idCurrency = false)
    // {
    //     if ($idCurrency) {
    //         $this->currency = Currency::getCurrencyInstance($idCurrency);
    //     } elseif (isset($this->wsObject->urlFragments['id_currency'])) {
    //         $this->currency = Currency::getCurrencyInstance($this->wsObject->urlFragments['id_currency']);
    //     } else {
    //         $this->currency = Currency::getCurrencyInstance((int) Configuration::get('PS_CURRENCY_DEFAULT'));
    //     }

    //     $defaultCurrencyInstance = Currency::getCurrencyInstance((int) Configuration::get('PS_CURRENCY_DEFAULT'));
    //     if (Validate::isLoadedObject($this->currency)) {
    //         $price = Tools::ps_round((float) $price, (int) $this->currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
    //         $convertedPrice = Tools::convertPrice($price, $this->currency, $defaultCurrencyInstance);

    //         return Tools::displayPrice($convertedPrice, $this->currency);
    //     } else {
    //         return $price;
    //     }
    // }

    // public function getProductPrice($id_product, $tax = true, $id_product_attribute = null, $decimals = 6,
    //     $divisor = null, $only_reduc = false, $usereduc = true, $quantity = 1, $force_associated_tax = false, $id_customer = null)
    // {
    //     return Product::getPriceStatic((int)$id_product, $tax, $id_product_attribute, $decimals, $divisor, $only_reduc, $usereduc, $quantity, $force_associated_tax, $id_customer);
    // }
}