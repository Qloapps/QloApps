<?php
/**
 * 2007-2018 PrestaShop
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
 *  @copyright 2007-2018 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class PayPalLogos
{
    private $iso_code = null;

    const LOCAL = 'Local';
    const HORIZONTAL = 'Horizontal';
    const VERTICAL = 'Vertical';

    public function __construct($iso_code)
    {
        $this->iso_code = $iso_code;
    }

    public function getLogos()
    {
        $file = dirname(__FILE__).'/'._PAYPAL_LOGO_XML_;

        if (!file_exists($file)) {
            return false;
        }

        $xml = simplexml_load_file($file);
        $logos = array();

        if (isset($xml) && $xml != false) {
            foreach ($xml->country as $item) {
                $tmp_iso_code = (string) $item->attributes()->iso_code;
                $logos[$tmp_iso_code] = (array) $item;
            }

            if (!isset($logos[$this->iso_code])) {
                $result = $this->getLocalLogos($logos['default'], 'default');
            } else {
                $result = $this->getLocalLogos($logos[$this->iso_code], $this->iso_code);
            }

            $result['default'] = $this->getLocalLogos($logos['default'], 'default');

            return $result;
        }

        return false;
    }

    public function getCardsLogo($vertical = false)
    {
        $logos = $this->getLogos();

        $orientation = $vertical === true ? self::VERTICAL : self::HORIZONTAL;
        $logo_ref = self::LOCAL.'PayPal'.$orientation.'SolutionPP';

        if (array_key_exists($logo_ref, $logos)) {
            return $logos[$logo_ref];
        } elseif (($vertical !== false) && isset($logos[self::LOCAL.'PayPal'.self::HORIZONTAL.'SolutionPP'])) {
            return $logos[self::LOCAL.'PayPal'.self::HORIZONTAL.'SolutionPP'];
        }

        if (isset($logos['default'][self::LOCAL.'Local'.$orientation.'SolutionPP'])) {
            return _MODULE_DIR_._PAYPAL_MODULE_DIRNAME_.$logos['default'][self::LOCAL.'Local'.$orientation.'SolutionPP'];
        }

        return false;
    }

    public function getLocalLogos(array $values, $iso_code)
    {
        foreach ($values as $key => $value) {
            if (!is_array($value)) {
                // Search for image file name
                preg_match('#.*/([\w._-]*)$#', $value, $logo);

                if ((count($logo) == 2) && (strstr($key, 'Local') === false)) {
                    $destination = _PAYPAL_MODULE_DIRNAME_.'/views/img/logos/'.$iso_code.'_'.$logo[1];
                    $this->updatePictures($logo[0], $destination);

                    // Define the local path after picture have been downloaded
                    $values['Local'.$key] = _MODULE_DIR_.$destination;

                    // Load back office cards path
                    if (file_exists(dirname(__FILE__).'/views/img/bo-cards/'.Tools::strtoupper($iso_code).'_bo_cards.png')) {
                        $values['BackOfficeCards'] = _MODULE_DIR_._PAYPAL_MODULE_DIRNAME_.'/views/img/bo-cards/'.Tools::strtoupper($iso_code).'_bo_cards.png';
                    } else if (file_exists(dirname(__FILE__).'/views/img/bo-cards/default.png')) {
                        $values['BackOfficeCards'] = _MODULE_DIR_._PAYPAL_MODULE_DIRNAME_.'/views/img/bo-cards/default.png';
                    }

                } else if (isset($values['Local'.$key])) {
                    // Use the local version
                    $values['Local'.$key] = _MODULE_DIR_._PAYPAL_MODULE_DIRNAME_.$values['Local'.$key];
                }

            }
        }

        return $values;
    }

    private function updatePictures($source, $destination, $force = false)
    {
        $wrappers = stream_get_wrappers();
        $https_wrapper = in_array('https', $wrappers) ? true : false;

        // 604800 => One week timestamp
        if (!file_exists(_PS_MODULE_DIR_.$destination) || ((time() - filemtime(_PS_MODULE_DIR_.$destination)) > 604800) || $force) {
            $picture = Tools::file_get_contents($source);
            if ((bool) $picture !== false) {
                if ($handle = @fopen(_PS_MODULE_DIR_.$destination, 'w+')) {
                    $size = fwrite($handle, $picture);
                    if ($size > 0 || (file_exists(_MODULE_DIR_.$destination) && (@filesize(_MODULE_DIR_.$destination) > 0))) {
                        return _MODULE_DIR_.$destination;
                    }

                }
            } elseif (strstr($source, 'https')) {
                return $this->updatePictures(str_replace('https', 'http', $source), $destination);
            } else {
                return false;
            }

        }

        return _MODULE_DIR_.$destination;
    }
}
