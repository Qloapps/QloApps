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

if (!defined('_PS_VERSION_')) {
    exit;
}

class QcmcSourceHelper
{
    private $bookingSourceMapping = [
        'booking_com' => [
            'code' => 'BOOKING_COM',
            'businessSourceCode' => 'OTA',
        ],
        'expedia' => [
            'code' => 'EXPEDIA',
            'businessSourceCode' => 'OTA',
        ],
        'goibibo_and_makemytrip' => [
            'code' => 'GOMMT',
            'businessSourceCode' => 'OTA',
        ],
        'agoda' => [
            'code' => 'AGODA',
            'businessSourceCode' => 'OTA',
        ],
        'bakuun' => [
            'code' => 'BAKUUN',
            'businessSourceCode' => 'OTA',
        ],
        'despegar' => [
            'code' => 'DESPEGAR',
            'businessSourceCode' => 'OTA',
        ],
        'yatra' => [
            'code' => 'YATRA',
            'businessSourceCode' => 'OTA',
        ],
        'airbnb' => [
            'code' => 'AIRBNB',
            'businessSourceCode' => 'OTA',
        ],
        'hostelworld' => [
            'code' => 'HOSTELWORLD',
            'businessSourceCode' => 'OTA',
        ],
        'hyperguest' => [
            'code' => 'HYPERGUEST',
            'businessSourceCode' => 'OTA',
        ],
        'trip_com_ctrip' => [
            'code' => 'TRIP_COM',
            'businessSourceCode' => 'OTA',
        ],
        'hotelbeds' => [
            'code' => 'HOTELBEDS',
            'businessSourceCode' => 'OTA',
        ],
    ];

    public function __construct()
    {
        if (!self::coreClassesExist()) {
            throw new Exception('BusinessSource and Source classes are required for QcmcSourceHelper to work.');
        }
    }

    public static function coreClassesExist()
    {
        return class_exists('BusinessSource') && class_exists('Source');
    }

    /**
     * Resolves the id_source for a channel-manager booking/business source name,
     * creating the BusinessSource/Source rows on first use if they don't exist yet.
     */
    public function getBookingSourceId($cmBookingSourceName, $cmBusinessSourceName = null)
    {
        $key = self::formatSourceKey($cmBookingSourceName);
        $bookingSourceCode = isset($this->bookingSourceMapping[$key])
            ? $this->bookingSourceMapping[$key]['code']
            : $this->formatSourceCode($cmBookingSourceName);

        if ($idSource = Source::getIdByCode($bookingSourceCode)) {
            return $idSource;
        }

        $businessSourceCode = $this->formatSourceCode($cmBusinessSourceName);
        $idBusinessSource = $this->getBusinessSourceIdByCode($businessSourceCode)
            ?: $this->createBusinessSource($businessSourceCode, $cmBusinessSourceName);

        return $this->createSource($bookingSourceCode, $idBusinessSource, $cmBookingSourceName);
    }

    private function getBusinessSourceIdByCode($code)
    {
        return (int)Db::getInstance()->getValue(
            'SELECT `id_business_source` FROM `'._DB_PREFIX_.'business_source`
            WHERE `code` = \''.pSQL($code).'\' AND `deleted` = 0'
        );
    }

    private function createBusinessSource($code, $sourceName)
    {
        $name = array();
        foreach (Language::getLanguages(false) as $lang) {
            $name[(int)$lang['id_lang']] = $this->formatSourceCode($sourceName);
        }

        $objBusinessSource = new BusinessSource();
        $objBusinessSource->code = $code;
        $objBusinessSource->name = $name;
        $objBusinessSource->position = BusinessSource::getHigherPosition() + 1;
        $objBusinessSource->unremovable = 0;
        $objBusinessSource->active = 1;
        $objBusinessSource->deleted = 0;
        $objBusinessSource->save();

        return (int)$objBusinessSource->id;
    }

    private function createSource($code, $idBusinessSource, $sourceName)
    {
        $name = array();
        foreach (Language::getLanguages(false) as $lang) {
            $name[(int)$lang['id_lang']] = $this->formatSourceCode($sourceName);
        }

        $objSource = new Source();
        $objSource->code = $code;
        $objSource->id_business_source = $idBusinessSource;
        $objSource->name = $name;
        $objSource->position = Source::getHigherPosition($idBusinessSource) + 1;
        $objSource->unremovable = 0;
        $objSource->active = 1;
        $objSource->deleted = 0;
        $objSource->save();

        return (int)$objSource->id;
    }

    public function getBusinessSourceInfo($cmBookingSourceName)
    {
        $key = self::formatSourceKey($cmBookingSourceName);

        return $this->bookingSourceMapping[$key] ?? null;
    }

    private static function formatSourceKey($sourceName)
    {
        return trim(preg_replace('/[^a-z0-9]+/', '_', strtolower($sourceName)), '_');
    }

    private static function formatSourceCode($sourceName)
    {
        return trim(preg_replace('/[^A-Z0-9]+/', '_', strtoupper($sourceName)), '_');
    }
}



