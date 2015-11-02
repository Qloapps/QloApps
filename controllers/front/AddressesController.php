<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AddressesControllerCore extends FrontController
{
    public $auth = true;
    public $php_self = 'addresses';
    public $authRedirection = 'addresses';
    public $ssl = true;

    /**
     * Set default medias for this controller
     */
    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'addresses.css');
        $this->addJS(_THEME_JS_DIR_.'tools.js'); // retro compat themes 1.5
        $this->addJS(_THEME_JS_DIR_.'addresses.js');
    }

    /**
     * Initialize addresses controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        if (!Validate::isLoadedObject($this->context->customer)) {
            die(Tools::displayError('The customer could not be found.'));
        }
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $total = 0;
        $multiple_addresses_formated = array();
        $ordered_fields = array();
        $addresses = $this->context->customer->getAddresses($this->context->language->id);
        // @todo getAddresses() should send back objects
        foreach ($addresses as $detail) {
            $address = new Address($detail['id_address']);
            $multiple_addresses_formated[$total] = AddressFormat::getFormattedLayoutData($address);
            unset($address);
            ++$total;

            // Retro theme < 1.4.2
            $ordered_fields = AddressFormat::getOrderedAddressFields($detail['id_country'], false, true);
        }

        // Retro theme 1.4.2
        if ($key = array_search('Country:name', $ordered_fields)) {
            $ordered_fields[$key] = 'country';
        }

        $addresses_style = array(
            'company' => 'address_company',
            'vat_number' => 'address_company',
            'firstname' => 'address_name',
            'lastname' => 'address_name',
            'address1' => 'address_address1',
            'address2' => 'address_address2',
            'city' => 'address_city',
            'country' => 'address_country',
            'phone' => 'address_phone',
            'phone_mobile' => 'address_phone_mobile',
            'alias' => 'address_title',
        );

        $this->context->smarty->assign(array(
            'addresses_style' => $addresses_style,
            'multipleAddresses' => $multiple_addresses_formated,
            'ordered_fields' => $ordered_fields,
            'addresses' => $addresses, // retro compat themes 1.5ibility Theme < 1.4.1
        ));

        $this->setTemplate(_PS_THEME_DIR_.'addresses.tpl');
    }
}
