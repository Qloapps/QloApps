<?php
/**
 * 2007-2018 PrestaShop
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2018 PrestaShop SA
 *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class AuthenticatePaymentMethods
{

    public static function getPaymentMethodsByIsoCode($iso_code)
    {
        // WPS -> Web Payment Standard
        // HSS -> Web Payment Pro / Integral Evolution
        // ECS -> Express Checkout Solution
        // PPP -> PAYPAL PLUS
        // PVZ -> Braintree / Payment VZero

        $payment_method = array(
            // EUROPE
            'BE'=>array(WPS, ECS),
            'CZ'=>array(WPS, ECS),
            'DE'=>array(WPS, ECS, PPP),
            'ES'=>array(WPS, HSS, ECS),
            'FR'=>array(WPS, HSS, ECS, PVZ),
            'IT'=>array(WPS, HSS, ECS),
            'VA'=>array(WPS, HSS, ECS),
            'NL'=>array(WPS, ECS),
            'AN'=>array(WPS, ECS), //Netherlands Antilles
            'PL'=>array(WPS, ECS),
            'PT'=>array(WPS, ECS),
            'AT'=>array(WPS, ECS),
            'CH'=>array(WPS, ECS),
            'DK'=>array(WPS, ECS),
            'FI'=>array(WPS, ECS),
            'GR'=>array(WPS, ECS),
            'HU'=>array(WPS, ECS),
            'LU'=>array(WPS, ECS),
            'NO'=>array(WPS, ECS),
            'RO'=>array(WPS, ECS),
            'RU'=>array(WPS, ECS),
            'SE'=>array(WPS, ECS),
            'SK'=>array(WPS, ECS),
            'UA'=>array(WPS, ECS),
            'TR'=>array(WPS, ECS),
            'SI'=>array(WPS, ECS),
            'GB'=>array(WPS, HSS, ECS),
            'IE'=>array(WPS, ECS),
            'LT'=>array(WPS, ECS),
            'EE'=>array(WPS, ECS),
            'LV'=>array(WPS, ECS),
            'RS'=>array(WPS, ECS),
            'HR'=>array(WPS, ECS),
            'MD'=>array(WPS, ECS),
            'BA'=>array(WPS, ECS),
            'AL'=>array(WPS, ECS),
            'MT'=>array(WPS, ECS),
            'MC'=>array(WPS, ECS),
            'IS'=>array(WPS, ECS),
            'MK'=>array(WPS, ECS),

            //ASIE
            'CN'=>array(WPS, ECS),
            'MO'=>array(WPS, ECS),
            'HK'=>array(WPS, HSS, ECS),
            'JP'=>array(WPS, HSS, ECS),
            'MY'=>array(WPS, ECS),
            'BN'=>array(WPS, ECS),
            'ID'=>array(WPS, ECS),
            'KH'=>array(WPS, ECS),
            'LA'=>array(WPS, ECS),
            'PH'=>array(WPS, ECS),
            'TL'=>array(WPS, ECS),
            'VN'=>array(WPS, ECS),
            'IL'=>array(WPS, ECS), //Israel
            'SG'=>array(WPS, ECS),
            'TH'=>array(WPS, ECS),
            'TW'=>array(WPS, ECS),

            // OCEANIE
            'NZ'=>array(WPS, ECS),
            'PW'=>array(WPS, ECS),
            'AU'=>array(WPS, HSS, ECS),

            // AMERIQUE LATINE
            'BR'=>array(WPS, ECS),
            'MX'=>array(WPS, ECS),
            'CL'=>array(WPS, ECS),
            'CO'=>array(WPS, ECS),
            'PE'=>array(WPS, ECS),

            //AFRIQUE
            'SL'=>array(WPS, ECS),
            'SN'=>array(WPS, ECS),
        );
        $return = isset($payment_method[$iso_code]) ? $payment_method[$iso_code] : false;
        if (Configuration::get('VZERO_ENABLED')) {
            $return[] = PVZ;
        }
        return $return;
    }

    public static function getCountryDependencyRetroCompatibilite($iso_code)
    {
        $localizations = array(
            'AU' => array('AU'), 'BE' => array('BE'), 'CN' => array('CN', 'MO'),
            'CZ' => array('CZ'), 'DE' => array('DE'), 'ES' => array('ES'),
            'FR' => array('FR'), 'GB' => array('GB'), 'HK' => array('HK'), 'IL' => array(
                'IL'), 'IN' => array('IN'), 'IT' => array('IT', 'VA'),
            'JP' => array('JP'), 'MY' => array('MY'), 'NL' => array('AN', 'NL'),
            'NZ' => array('NZ'), 'PL' => array('PL'), 'PT' => array('PT', 'BR'),
            'RA' => array('AF', 'AS', 'BD', 'BN', 'BT', 'CC', 'CK', 'CX', 'FM', 'HM',
                'ID', 'KH', 'KI', 'KN', 'KP', 'KR', 'KZ', 'LA', 'LK', 'MH',
                'MM', 'MN', 'MV', 'MX', 'NF', 'NP', 'NU', 'OM', 'PG', 'PH', 'PW',
                'QA', 'SB', 'TJ', 'TK', 'TL', 'TM', 'TO', 'TV', 'TZ', 'UZ', 'VN',
                'VU', 'WF', 'WS'),
            'RE' => array('IE', 'ZA', 'GP', 'GG', 'JE', 'MC', 'MS', 'MP', 'PA', 'PY',
                'PE', 'PN', 'PR', 'LC', 'SR', 'TT',
                'UY', 'VE', 'VI', 'AG', 'AR', 'CA', 'BO', 'BS', 'BB', 'BZ', 'CL',
                'CO', 'CR', 'CU', 'SV', 'GD', 'GT', 'HN', 'JM', 'NI', 'AD', 'AE',
                'AI', 'AL', 'AM', 'AO', 'AQ', 'AT', 'AW', 'AX', 'AZ', 'BA', 'BF',
                'BG', 'BH', 'BI', 'BJ', 'BL', 'BM', 'BV', 'BW', 'BY', 'CD', 'CF',
                'CG',
                'CH', 'CI', 'CM', 'CV', 'CY', 'DJ', 'DK', 'DM', 'DO', 'DZ', 'EC',
                'EE', 'EG', 'EH', 'ER', 'ET', 'FI', 'FJ', 'FK', 'FO', 'GA', 'GE',
                'GF',
                'GH', 'GI', 'GL', 'GM', 'GN', 'GQ', 'GR', 'GS', 'GU', 'GW', 'GY',
                'HR', 'HT', 'HU', 'IM', 'IO', 'IQ', 'IR', 'IS', 'JO', 'KE', 'KM',
                'KW',
                'KY', 'LB', 'LI', 'LR', 'LS', 'LT', 'LU', 'LV', 'LY', 'MA', 'MD',
                'ME', 'MF', 'MG', 'MK', 'ML', 'MQ', 'MR', 'MT', 'MU', 'MW', 'MZ',
                'NA',
                'NC', 'NE', 'NG', 'NO', 'NR', 'PF', 'PK', 'PM', 'PS', 'RE', 'RO',
                'RS', 'RU', 'RW', 'SA', 'SC', 'SD', 'SE', 'SI', 'SJ', 'SK', 'SL',
                'SM', 'SN', 'SO', 'ST', 'SY', 'SZ', 'TC', 'TD', 'TF', 'TG', 'TN',
                'UA', 'UG', 'VC', 'VG', 'YE', 'YT', 'ZM', 'ZW'),
            'SG' => array('SG'), 'TH' => array('TH'), 'TR' => array('TR'), 'TW' => array(
                'TW'), 'US' => array('US'));

        foreach ($localizations as $key => $value) {
            if (in_array($iso_code, $value)) {
                return $key;
            }
        }

        return false;
    }

    public static function getPaymentMethodsRetroCompatibilite($iso_code)
    {
        // WPS -> Web Payment Standard
        // HSS -> Web Payment Pro / Integral Evolution
        // ECS -> Express Checkout Solution
        // PPP -> PAYPAL PLUS

        $payment_method = array(
            'AU' => array(WPS, HSS, ECS),
            'BE' => array(WPS, ECS),
            'CN' => array(WPS, ECS),
            'CZ' => array(),
            'DE' => array(WPS, ECS, PPP),
            'ES' => array(WPS, HSS, ECS),
            'FR' => array(WPS, HSS, ECS),
            'GB' => array(WPS, HSS, ECS),
            'HK' => array(WPS, HSS, ECS),
            'IL' => array(WPS, ECS),
            'IN' => array(WPS, ECS),
            'IT' => array(WPS, HSS, ECS),
            'JP' => array(WPS, HSS, ECS),
            'MY' => array(WPS, ECS),
            'NL' => array(WPS, ECS),
            'NZ' => array(WPS, ECS),
            'PL' => array(WPS, ECS),
            'PT' => array(WPS, ECS),
            'RA' => array(WPS, ECS),
            'RE' => array(WPS, ECS),
            'SG' => array(WPS, ECS),
            'TH' => array(WPS, ECS),
            'TR' => array(WPS, ECS),
            'TW' => array(WPS, ECS),
            'US' => array(WPS, ECS),
            'ZA' => array(WPS, ECS));


        $return = isset($payment_method[$iso_code]) ? $payment_method[$iso_code] : $payment_method['GB'];
        if (Configuration::get('VZERO_ENABLED')) {
            $return[] = PVZ;
        }
        return $return;
    }

    public static function authenticatePaymentMethodByLang($iso_code)
    {
        return self::getPaymentMethodsRetroCompatibilite(self::getCountryDependencyRetroCompatibilite($iso_code));
    }

    public static function authenticatePaymentMethodByCountry($iso_code)
    {
        return self::getPaymentMethodsByIsoCode($iso_code);
    }
}
