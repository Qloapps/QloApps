<?php
/*
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * This class is only here to show the possibility of extending InstallXmlLoader, which is the
 * class parsing all XML files, copying all images, etc.
 *
 * Please read documentation in ~/install/dev/ folder if you want to customize PrestaShop install / fixtures.
 */
class InstallFixturesFashion extends InstallXmlLoader
{
    public function createEntityCustomer($identifier, array $data, array $data_lang)
    {
        if ($identifier == 'John') {
            $data['passwd'] = Tools::encrypt('123456789');
            $data['last_passwd_gen'] = date('Y-m-d H:i:s');
            $data['birthday'] = date('Y-m-d', strtotime('-30 years'));
            $data['newsletter_date_add'] = date('Y-m-d H:i:s');
        }

        return $this->createEntity('customer', $identifier, 'Customer', $data, $data_lang);
    }

    public function createEntityAddress($identifier, array $data, array $data_lang)
    {
        if ($identifier == 'My_address') {
            $idCountry = Configuration::get('PS_COUNTRY_DEFAULT');
            $data['id_country'] = $idCountry;

            if (Country::containsStates($idCountry)) {
                if ($states = State::getStatesByIdCountry($idCountry)) {
                    $data['id_state'] = $states[0]['id_state'];
                } else {
                    $data['id_state'] = 0;
                }
            }

            if (Country::getNeedZipCode($idCountry)) {
                $data['postcode'] = Tools::generateRandomZipcode($idCountry);
            }
        }

        return $this->createEntity('address', $identifier, 'Address', $data, $data_lang);
    }
}
