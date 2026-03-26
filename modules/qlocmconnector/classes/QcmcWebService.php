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

class QmkWebService
{
    protected $objOutput;
    protected $output;
    protected $wsObject;
    protected $objModule;
    protected $http;
    protected $width;
    protected $jsonOutput;
    protected $outputFormat;
    public $parent;
    public $context;


    public function __construct($objOutput = null, $wsObject = null, $output = null, $outputFormat = 'xml')
    {
        $this->objModule = Module::getInstanceByName('qlocmconnector');
        $this->objOutput = $objOutput;
        $this->wsObject = $wsObject;
        $this->output = $output;
        $this->outputFormat = $outputFormat;
        $this->setContext();
    }

    public function setContext()
    {
        $this->context = Context::getContext();
        $this->context->controller = new FrontController();
        $unidentifiedGroupCustomer = new Customer();
        if (Group::isFeatureActive()) {
            $unidentifiedGroupCustomer->id_default_group = (int) Configuration::get('PS_UNIDENTIFIED_GROUP');
        }

        // Set Customer
        $this->context->customer = $unidentifiedGroupCustomer;
        if (isset($this->wsObject->urlFragments['id_customer'])) {
            $customer = new Customer($this->wsObject->urlFragments['id_customer']);
            if (Validate::isLoadedObject($customer) && $customer->active) {
                $this->context->customer = $customer;
                $this->context->customer->logged = 1; // Assuming that when getting id_customer, customer is logged in.
            }
        } else {
            if (isset($this->wsObject->urlSegment[2]) && $this->wsObject->urlSegment[2] == 'payment') {
                if (isset($this->wsObject->urlSegment[3]) && $this->wsObject->urlSegment[3]) {
                    $this->context->customer = new Customer($this->wsObject->urlSegment[3]);
                } else {
                    $urlObjCart = new Cart($this->wsObject->urlFragments['id_cart']);
                    $this->context->customer = new Customer($urlObjCart->id_customer);
                }
            }
        }

        // Set Cart
        if (isset($this->wsObject->urlFragments['id_cart'])) {
            $this->context->cart = new Cart($this->wsObject->urlFragments['id_cart']);
            if (isset($this->wsObject->urlFragments['id_customer']) && !$this->wsObject->urlFragments['id_customer']) {
                $this->context->customer = new Customer($this->context->cart->id_customer);
            }
        }

        // Set language
        $language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        if (isset($this->wsObject->urlFragments['id_lang'])) {
            $requestedlanguage = new Language($this->wsObject->urlFragments['id_lang']);
            if (Validate::isLoadedObject($requestedlanguage) && $requestedlanguage->active) {
                $language = $requestedlanguage;
            }
        }

        $this->context->language = $language;
        // Set Currency
        $currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        if (isset($this->wsObject->urlFragments['id_currency'])) {
            $requestedCurrency = new Currency($this->wsObject->urlFragments['id_currency']);
            if (Validate::isLoadedObject($requestedCurrency)
                && $requestedCurrency->active
                && !$requestedCurrency->deleted
            ) {
                $currency = $requestedCurrency;
            }
        }

        $this->context->currency = $currency;
        if (isset($this->wsObject->urlFragments['id_guest'])
            && $this->wsObject->urlFragments['id_guest']
        ) {
            $objGuest = new Guest((int) $this->wsObject->urlFragments['id_guest']);
            $this->context->cookie->id_guest = $objGuest->id;
        }

        if (Validate::isLoadedObject($this->context->customer) && $this->context->customer->active) {
            $this->context->customer->logged = 1;   // hook exec lisy cache build on the basis of groups
            if ($customerGuestId = Guest::getFromCustomer((int) $this->context->customer->id)) {
                $this->context->customer->id_guest = $customerGuestId;
            }
            $this->context->customer->save();
        } else {
            $this->context->customer = $unidentifiedGroupCustomer;
            if (!isset($this->context->cart)) {
                $this->context->cart = new Cart();
            }
        }

        if (Validate::isLoadedObject($this->context->cart)) {
            $this->context->cart->id_currency = $this->context->currency->id;
            $this->context->cart->id_lang = $this->context->language->id;
            if ($this->context->customer->id) {
                $this->context->cart->id_customer = $this->context->customer->id;
                if ($customerAddressId = Address::getFirstCustomerAddressId($this->context->customer->id)) {
                    $this->context->cart->id_address_invoice = $customerAddressId;
                }
            }

            $this->context->cart->save();
        }

        // to update product $_taxCalculationMethod according to context
        Product::getTaxCalculationMethod((int) $this->context->customer->id);
    }

    public function renderOutputUsingArray($response, $keyToIgnore = array(), $parentKey = '', $useEmpty = false)
    {
        $output = '';
        foreach ($response as $key => $res) {
            if (in_array($key, $keyToIgnore) && $key) {
                continue;
            }

            $currentKey = $key;
            if (gettype($key) == 'integer' || (int) $key) {
                $key = $parentKey;
            }

            if (is_array($res) && count($res)) {
                $output .= $this->renderHeader($key);
                $output .= $this->renderOutputUsingArray($res, $keyToIgnore, $key, $useEmpty);
                $output .= $this->renderFooter($key);
            } else {
                if (empty($res) && !$useEmpty && !in_array($key, array('prefix', 'suffix'))) {
                    $res = 0;
                }

                if (isset($this->wsObject->urlFragments['schema'])) {
                    if ($this->wsObject->urlFragments['schema'] == 'blank' || $this->wsObject->urlFragments['schema'] == 'synopsis') {
                        $res = null;
                    } else {
                        throw new WebserviceException(
                            $this->objModule->l('Please select a schema of type \'synopsis\' to get the whole schema informations (which fields are required, which kind of content...) or \'blank\' to get an empty schema to fill before using POST request.', 'QmkWebService'),
                            array(100, 400)
                        );
                    }
                }

                $output .= $this->objOutput->objectRender->renderField(
                    array(
                        'sqlId' => $key,
                        'value' => $res
                    )
                );
            }
        }

        return $output;
    }

    public function renderHeader($parentKey = '')
    {
        if ($parentKey != '') {
            $this->parent[] = $parentKey;
            return  $this->objOutput->objectRender->renderNodeHeader($parentKey,
                array('objectNodeName' => $parentKey)
            );
        }

        return '';
    }

    public function renderFooter($parentKey = '', $multiple = false)
    {
        if ($parentKey != '') {
            array_pop($this->parent);
            return $this->objOutput->objectRender->renderNodeFooter($parentKey,
                array('objectNodeName' => $parentKey)
            );
        }

        return '';
    }

    public function renderResponse($message, $status =null, $args = array(), $parentKey ='', $errors = array(), $root = 'response')
    {
        $jsonOutput = array();
        $output = '';
        $output .= $this->renderHeader($root);
        if ($status !== null) {
            $args['status'] = (bool) $status;
        } else {
            $args['status'] = false;
        }

        $args['message'] = $message;
        $output .= $this->renderOutputUsingArray($args, array(), $parentKey);
        $jsonOutput[$root] = $args;
        if (isset($errors) && count($errors) > 0) {
            $output .= $this->renderHeader('errors');
            $output .= $this->renderOutputUsingArray($errors, array(), 'error');
            $jsonOutput[$root]['errors'] = $errors['error'];
            $output .= $this->renderFooter('errors');
        }

        $output .= $this->renderFooter($root);
        if ($this->outputFormat == 'json') {
            return json_encode($jsonOutput);
        }

        return $output;
    }


    public function resizeImage($url, $name)
    {
        // @todo: add code for the resized image here... If needed.
        // create new url
        $newUrl = $url.$name;

        return $this->context->link->getMediaLink($newUrl);
    }

    public function renderFormFields($fields, $parentKey ='', $parentKeysToIgnore = array())
    {
        $output = '';
        foreach ($fields as $key => $field) {
            $currentKey = $key;
            if (gettype($key) == 'integer' || (int) $key) {
                $key = $parentKey;
            }

            if (is_array($field) && count($field)) {
                $isParent = false;
                if ($key != end($this->parent)) {
                    $isParent = true;
                    if (!in_array($key, $parentKeysToIgnore)) {
                        $output .= $this->renderHeader($key);
                    }
                }

                $output .= $this->renderFormFields($field, $key, $parentKeysToIgnore);
                if ($isParent
                    || $key == 'option'
                    || $key == 'new_option'
                    || $key == 'field'
                ) {
                    if ($key == end($this->parent)) {
                        if (!in_array($key, $parentKeysToIgnore)) {
                            $output .= $this->renderFooter($key);
                        }
                    }
                }
            } else {
                if (isset($this->wsObject->urlFragments['schema'])) {
                    if ($this->wsObject->urlFragments['schema'] == 'blank' || $this->wsObject->urlFragments['schema'] == 'synopsis') {
                        $field = null;
                    } else {
                        throw new WebserviceException(
                            $this->objModule->l('Please select a schema of type \'synopsis\' to get the whole schema informations (which fields are required, which kind of content...) or \'blank\' to get an empty schema to fill before using POST request.', 'QmkWebService'),
                            array(100, 400)
                        );
                    }
                }

                if (is_array($field) && !count($field)) {
                    $field = '';
                }

                $output .= $this->objOutput->objectRender->renderField(
                    array(
                        'sqlId' => $key,
                        'value' => $field
                    )
                );
            }
        }

        return $output;
    }

}