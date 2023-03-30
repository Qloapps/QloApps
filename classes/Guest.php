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

class GuestCore extends ObjectModel
{
    public $id_operating_system;
    public $id_web_browser;
    public $id_customer;
    public $javascript;
    public $screen_resolution_x;
    public $screen_resolution_y;
    public $screen_color;
    public $sun_java;
    public $adobe_flash;
    public $adobe_director;
    public $apple_quicktime;
    public $real_player;
    public $windows_media;
    public $accept_language;
    public $mobile_theme;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'guest',
        'primary' => 'id_guest',
        'fields' => array(
            'id_operating_system' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_web_browser' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_customer' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'javascript' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'screen_resolution_x' =>    array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'screen_resolution_y' =>    array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'screen_color' =>            array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'sun_java' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'adobe_flash' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'adobe_director' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'apple_quicktime' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'real_player' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'windows_media' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'accept_language' =>        array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 8),
            'mobile_theme' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );

    protected $webserviceParameters = array(
        'fields' => array(
            'id_customer' => array('xlink_resource' => 'customers'),
        ),
    );

    public function userAgent()
    {
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $acceptLanguage = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
        $this->accept_language = $this->getLanguage($acceptLanguage);
        $this->id_operating_system = $this->getOs($userAgent);
        $this->id_web_browser = $this->getBrowser($userAgent);
        $this->mobile_theme = Context::getContext()->getMobileDevice();
    }

    protected function getLanguage($acceptLanguage)
    {
        // $langsArray is filled with all the languages accepted, ordered by priority
        $langsArray = array();
        preg_match_all('/([a-z]{2}(-[a-z]{2})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/', $acceptLanguage, $array);
        if (count($array[1])) {
            $langsArray = array_combine($array[1], $array[4]);
            foreach ($langsArray as $lang => $val) {
                if ($val === '') {
                    $langsArray[$lang] = 1;
                }
            }
            arsort($langsArray, SORT_NUMERIC);
        }

        // Only the first language is returned
        return (count($langsArray) ? key($langsArray) : '');
    }

    protected function getBrowser($userAgent)
    {
        $browserArray = array(
            'Chrome' => 'Chrome/',
            'Safari' => 'Safari',
            'Safari iPad' => 'iPad',
            'Firefox' => 'Firefox/',
            'Opera' => 'Opera',
            'IE 11' => 'Trident',
            'IE 10' => 'MSIE 10',
            'IE 9' => 'MSIE 9',
            'IE 8' => 'MSIE 8',
            'IE 7' => 'MSIE 7',
            'IE 6' => 'MSIE 6'
        );
        foreach ($browserArray as $k => $value) {
            if (strstr($userAgent, $value)) {
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT `id_web_browser`
				FROM `'._DB_PREFIX_.'web_browser` wb
				WHERE wb.`name` = \''.pSQL($k).'\'');

                return $result['id_web_browser'];
            }
        }
        return null;
    }

    protected function getOs($userAgent)
    {
        $osArray = array(
            'Windows 8' => 'Windows NT 6.2',
            'Windows 7' => 'Windows NT 6.1',
            'Windows Vista' => 'Windows NT 6.0',
            'Windows XP' => 'Windows NT 5',
            'MacOsX' => 'Mac OS X',
            'Android' => 'Android',
            'Linux' => 'X11'
        );

        foreach ($osArray as $k => $value) {
            if (strstr($userAgent, $value)) {
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT `id_operating_system`
				FROM `'._DB_PREFIX_.'operating_system` os
				WHERE os.`name` = \''.pSQL($k).'\'');

                return $result['id_operating_system'];
            }
        }
        return null;
    }

    public static function getFromCustomer($id_customer)
    {
        if (!Validate::isUnsignedId($id_customer)) {
            return false;
        }

        if ($result = Db::getInstance()->getRow(
            'SELECT `id_guest` FROM `'._DB_PREFIX_.'guest` WHERE `id_customer` = '.(int)($id_customer)
        )) {
            return $result['id_guest'];
        }

        return false;
    }

    public function mergeWithCustomer($idGuest, $idCustomer)
    {
        // Since the guests are merged, the guest id in the connections table must be changed too
        Db::getInstance()->update('connections', [
            'id_guest' => (int) $idGuest,
        ], 'id_guest = ' . (int) $this->id);

        // Since the guests are merged, the guest id in the cart table must be changed too
        Db::getInstance()->update('cart', [
            'id_guest' => (int) $idGuest,
        ], 'id_guest = ' . (int) $this->id);

        // Since the guests are merged, the guest id in the htl_cart_booking_data table must be changed too
        Db::getInstance()->update('htl_cart_booking_data', [
            'id_guest' => (int) $idGuest,
        ], 'id_guest = ' . (int) $this->id);

        // The current guest is removed from the database because now we have old customer guest id
        $this->delete();
    }

    public static function setNewGuest($cookie)
    {
        $guest = new Guest(isset($cookie->id_customer) ? Guest::getFromCustomer((int)($cookie->id_customer)) : null);
        $guest->userAgent();
        $guest->save();
        $cookie->id_guest = (int)($guest->id);
    }
}
