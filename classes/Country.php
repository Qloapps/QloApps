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

class CountryCore extends ObjectModel
{
    public $id;

    /** @var int Zone id which country belongs */
    public $id_zone;

    /** @var int Currency id which country belongs */
    public $id_currency;

    /** @var string 2 letters iso code */
    public $iso_code;

    /** @var int international call prefix */
    public $call_prefix;

    /** @var string Name */
    public $name;

    /** @var bool Contain states */
    public $contains_states;

    /** @var bool Need identification number dni/nif/nie */
    public $need_identification_number;

    /** @var bool Need Zip Code */
    public $need_zip_code;

    /** @var string Zip Code Format */
    public $zip_code_format;

    /** @var bool Display or not the tax incl./tax excl. mention in the front office */
    public $display_tax_label = true;

    /** @var bool Status for delivery */
    public $active = true;

    protected static $_idZones = array();

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'country',
        'primary' => 'id_country',
        'multilang' => true,
        'fields' => array(
            'id_zone' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_currency' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'call_prefix' =>                array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'iso_code' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isLanguageIsoCode', 'required' => true, 'size' => 3),
            'active' =>                    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'contains_states' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'need_identification_number' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'need_zip_code' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'zip_code_format' =>            array('type' => self::TYPE_STRING, 'validate' => 'isZipCodeFormat'),
            'display_tax_label' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),

            /* Lang fields */
            'name' =>                        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 64),
        ),
        'associations' => array(
            'zone' =>                        array('type' => self::HAS_ONE),
            'currency' =>                    array('type' => self::HAS_ONE),
        )
    );

    protected static $cache_iso_by_id = array();

    protected $webserviceParameters = array(
        'objectsNodeName' => 'countries',
        'fields' => array(
            'id_zone' => array('xlink_resource'=> 'zones'),
            'id_currency' => array('xlink_resource'=> 'currencies'),
        ),
    );

    public function delete()
    {
        if (!parent::delete()) {
            return false;
        }
        return Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'cart_rule_country WHERE id_country = '.(int)$this->id);
    }

    /**
     * @brief Return available countries
     *
     * @param int $id_lang Language ID
     * @param bool $active return only active coutries
     * @param bool $contain_states return only country with states
     * @param bool $list_states Include the states list with the returned list
     *
     * @return Array Countries and corresponding zones
     */
    public static function getCountries($id_lang, $active = false, $contain_states = false, $list_states = true)
    {
        $countries = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT cl.*,c.*, cl.`name` country, z.`name` zone
		FROM `'._DB_PREFIX_.'country` c '.Shop::addSqlAssociation('country', 'c').'
		LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'zone` z ON (z.`id_zone` = c.`id_zone`)
		WHERE 1'.($active ? ' AND c.active = 1' : '').($contain_states ? ' AND c.`contains_states` = '.(int)$contain_states : '').'
		ORDER BY cl.name ASC');
        foreach ($result as $row) {
            $countries[$row['id_country']] = $row;
        }

        if ($list_states) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'state` ORDER BY `name` ASC');
            foreach ($result as $row) {
                if (isset($countries[$row['id_country']]) && $row['active'] == 1) { /* Does not keep the state if its country has been disabled and not selected */
                        $countries[$row['id_country']]['states'][] = $row;
                }
            }
        }
        return $countries;
    }

    public static function getCountriesByIdShop($id_shop, $id_lang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'country` c
		LEFT JOIN `'._DB_PREFIX_.'country_shop` cs ON (cs.`id_country`= c.`id_country`)
		LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = '.(int)$id_lang.')
		WHERE `id_shop` = '.(int)$id_shop);
    }

    /**
     * Get a country ID with its iso code
     *
     * @param string $iso_code Country iso code
     * @param bool $active return only active coutries
     * @return int Country ID
     */
    public static function getByIso($iso_code, $active = false)
    {
        if (!Validate::isLanguageIsoCode($iso_code)) {
            die(Tools::displayError());
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT `id_country`
			FROM `'._DB_PREFIX_.'country`
			WHERE `iso_code` = \''.pSQL(strtoupper($iso_code)).'\''
            .($active ? ' AND active = 1' : '')
        );

        if (isset($result['id_country']))
        {
            return (int)$result['id_country'];
        }
        return false;
    }

    public static function getIdZone($id_country)
    {
        if (!Validate::isUnsignedId($id_country)) {
            die(Tools::displayError());
        }

        if (isset(self::$_idZones[$id_country])) {
            return (int)self::$_idZones[$id_country];
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `id_zone`
		FROM `'._DB_PREFIX_.'country`
		WHERE `id_country` = '.(int)$id_country);

        if (isset($result['id_zone']))
        {
            self::$_idZones[$id_country] = (int)$result['id_zone'];
            return (int)$result['id_zone'];
        }
        return false;
    }

    /**
     * Get a country name with its ID
     *
     * @param int $id_lang Language ID
     * @param int $id_country Country ID
     * @return string Country name
     */
    public static function getNameById($id_lang, $id_country)
    {
        $key = 'country_getNameById_'.$id_country.'_'.$id_lang;
        if (!Cache::isStored($key)) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
							SELECT `name`
							FROM `'._DB_PREFIX_.'country_lang`
							WHERE `id_lang` = '.(int)$id_lang.'
							AND `id_country` = '.(int)$id_country
                        );
            Cache::store($key, $result);
            return $result;
        }
        return Cache::retrieve($key);
    }

    /**
     * Get a country iso with its ID
     *
     * @param int $id_country Country ID
     * @return string Country iso
     */
    public static function getIsoById($id_country)
    {
        if (!isset(Country::$cache_iso_by_id[$id_country])) {
            Country::$cache_iso_by_id[$id_country] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `iso_code`
			FROM `'._DB_PREFIX_.'country`
			WHERE `id_country` = '.(int)$id_country);
        }
        if (isset(Country::$cache_iso_by_id[$id_country]))
        {
            return Country::$cache_iso_by_id[$id_country];
        }
        return false;
    }

    /**
     * Get a country id with its name
     *
     * @param int $id_lang Language ID
     * @param string $country Country Name
     * @return int Country ID
     */
    public static function getIdByName($id_lang = null, $country)
    {
        $sql = '
		SELECT `id_country`
		FROM `'._DB_PREFIX_.'country_lang`
		WHERE `name` = \''.pSQL($country).'\'';
        if ($id_lang) {
            $sql .= ' AND `id_lang` = '.(int)$id_lang;
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        if (isset($result['id_country']))
        {
            return (int)$result['id_country'];
        }
        return false;
    }

    public static function getNeedZipCode($id_country)
    {
        if (!(int)$id_country) {
            return false;
        }

        return (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT `need_zip_code`
		FROM `'._DB_PREFIX_.'country`
		WHERE `id_country` = '.(int)$id_country);
    }

    public static function getZipCodeFormat($id_country)
    {
        if (!(int)$id_country) {
            return false;
        }

        $zip_code_format = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT `zip_code_format`
		FROM `'._DB_PREFIX_.'country`
		WHERE `id_country` = '.(int)$id_country);

        if (isset($zip_code_format) && $zip_code_format)
        {
            return $zip_code_format;
        }
        return false;
    }

    /**
     * Returns the default country Id
     *
     * @deprecated as of 1.5 use $context->country->id instead
     * @return int default country id
     */
    public static function getDefaultCountryId()
    {
        Tools::displayAsDeprecated();
        return Context::getContext()->country->id;
    }

    public static function getCountriesByZoneId($id_zone, $id_lang)
    {
        if (empty($id_zone) || empty($id_lang)) {
            die(Tools::displayError());
        }

        $sql = ' SELECT DISTINCT c.*, cl.*
				FROM `'._DB_PREFIX_.'country` c
				'.Shop::addSqlAssociation('country', 'c', false).'
				LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_country` = c.`id_country`)
				LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country`)
				WHERE (c.`id_zone` = '.(int)$id_zone.' OR s.`id_zone` = '.(int)$id_zone.')
				AND `id_lang` = '.(int)$id_lang;
        return Db::getInstance()->executeS($sql);
    }

    public function isNeedDni()
    {
        return Country::isNeedDniByCountryId($this->id);
    }

    public static function isNeedDniByCountryId($id_country)
    {
        return (bool)Db::getInstance()->getValue('
			SELECT `need_identification_number`
			FROM `'._DB_PREFIX_.'country`
			WHERE `id_country` = '.(int)$id_country);
    }

    public static function containsStates($id_country)
    {
        return (bool)Db::getInstance()->getValue('
			SELECT `contains_states`
			FROM `'._DB_PREFIX_.'country`
			WHERE `id_country` = '.(int)$id_country);
    }

    /**
     * @param $ids_countries
     * @param $id_zone
     * @return bool
     */
    public function affectZoneToSelection($ids_countries, $id_zone)
    {
        // cast every array values to int (security)
        $ids_countries = array_map('intval', $ids_countries);
        return Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.'country` SET `id_zone` = '.(int)$id_zone.' WHERE `id_country` IN ('.implode(',', $ids_countries).')
		');
    }

    /**
     * Replace letters of zip code format And check this format on the zip code
     * @param $zip_code
     * @return (bool)
     */
    public function checkZipCode($zip_code)
    {
        $zip_regexp = '/^'.$this->zip_code_format.'$/ui';
        $zip_regexp = str_replace(' ', '( |)', $zip_regexp);
        $zip_regexp = str_replace('-', '(-|)', $zip_regexp);
        $zip_regexp = str_replace('N', '[0-9]', $zip_regexp);
        $zip_regexp = str_replace('L', '[a-zA-Z]', $zip_regexp);
        $zip_regexp = str_replace('C', $this->iso_code, $zip_regexp);

        return (bool)preg_match($zip_regexp, $zip_code);
    }

    public static function addModuleRestrictions(array $shops = array(), array $countries = array(), array $modules = array())
    {
        if (!count($shops)) {
            $shops = Shop::getShops(true, null, true);
        }

        if (!count($countries)) {
            $countries = Country::getCountries((int)Context::getContext()->cookie->id_lang);
        }

        if (!count($modules)) {
            $modules = Module::getPaymentModules();
        }

        $sql = false;
        foreach ($shops as $id_shop) {
            foreach ($countries as $country) {
                foreach ($modules as $module) {
                    $sql .= '('.(int)$module['id_module'].', '.(int)$id_shop.', '.(int)$country['id_country'].'),';
                }
            }
        }

        if ($sql) {
            $sql = 'INSERT IGNORE INTO `'._DB_PREFIX_.'module_country` (`id_module`, `id_shop`, `id_country`) VALUES '.rtrim($sql, ',');
            return Db::getInstance()->execute($sql);
        } else {
            return true;
        }
    }

    public function add($autodate = true, $null_values = false)
    {
        $return = parent::add($autodate, $null_values) && self::addModuleRestrictions(array(), array(array('id_country' => $this->id)), array());
        return $return;
    }
}
