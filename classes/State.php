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

class StateCore extends ObjectModel
{
    /** @var int Country id which state belongs */
    public $id_country;

    /** @var int Zone id which state belongs */
    public $id_zone;

    /** @var string 2 letters iso code */
    public $iso_code;

    /** @var string Name */
    public $name;

    /** @var bool Status for delivery */
    public $active = true;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'state',
        'primary' => 'id_state',
        'fields' => array(
            'id_country' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_zone' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'iso_code' =>    array('type' => self::TYPE_STRING, 'validate' => 'isStateIsoCode', 'required' => true, 'size' => 7),
            'name' =>        array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32),
            'active' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );

    protected $webserviceParameters = array(
        'fields' => array(
            'id_zone' => array('xlink_resource'=> 'zones'),
            'id_country' => array('xlink_resource'=> 'countries')
        ),
    );

    public static function getStates($id_lang = false, $active = false)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT `id_state`, `id_country`, `id_zone`, `iso_code`, `name`, `active`
		FROM `'._DB_PREFIX_.'state`
		'.($active ? 'WHERE active = 1' : '').'
		ORDER BY `name` ASC');
    }

    /**
     * Get a state name with its ID
     *
     * @param int $id_state Country ID
     * @return string State name
     */
    public static function getNameById($id_state)
    {
        if (!$id_state) {
            return false;
        }
        $cache_id = 'State::getNameById_'.(int)$id_state;
        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT `name`
				FROM `'._DB_PREFIX_.'state`
				WHERE `id_state` = '.(int)$id_state
            );
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * Get a state id with its name
     *
     * @param string $id_state Country ID
     * @return int state id
     */
    public static function getIdByName($state)
    {
        if (empty($state)) {
            return false;
        }
        $cache_id = 'State::getIdByName_'.pSQL($state);
        if (!Cache::isStored($cache_id)) {
            $result = (int)Db::getInstance()->getValue('
				SELECT `id_state`
				FROM `'._DB_PREFIX_.'state`
				WHERE `name` = \''.pSQL($state).'\'
			');
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }

    /**
    * Get a state id with its iso code
    *
    * @param string $iso_code Iso code
    * @return int state id
    */
    public static function getIdByIso($iso_code, $id_country = null)
    {
        return Db::getInstance()->getValue('
		SELECT `id_state`
		FROM `'._DB_PREFIX_.'state`
		WHERE `iso_code` = \''.pSQL($iso_code).'\'
		'.($id_country ? 'AND `id_country` = '.(int)$id_country : ''));
    }

    /**
    * Delete a state only if is not in use
    *
    * @return bool
    */
    public function delete()
    {
        if (!$this->isUsed()) {
            // Database deletion
            $result = Db::getInstance()->delete($this->def['table'], '`'.$this->def['primary'].'` = '.(int)$this->id);
            if (!$result) {
                return false;
            }

            // Database deletion for multilingual fields related to the object
            if (!empty($this->def['multilang'])) {
                Db::getInstance()->delete(bqSQL($this->def['table']).'_lang', '`'.$this->def['primary'].'` = '.(int)$this->id);
            }
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Check if a state is used
     *
     * @return bool
     */
    public function isUsed()
    {
        return ($this->countUsed() > 0);
    }

    /**
     * Returns the number of utilisation of a state
     *
     * @return int count for this state
     */
    public function countUsed()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'address`
			WHERE `'.$this->def['primary'].'` = '.(int)$this->id
        );
        return $result;
    }

    public static function getStatesByIdCountry($id_country)
    {
        if (empty($id_country)) {
            die(Tools::displayError());
        }

        return Db::getInstance()->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'state` s
			WHERE s.`id_country` = '.(int)$id_country
        );
    }

    public static function hasCounties($id_state)
    {
        return count(County::getCounties((int)$id_state));
    }

    public static function getIdZone($id_state)
    {
        if (!Validate::isUnsignedId($id_state)) {
            die(Tools::displayError());
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `id_zone`
			FROM `'._DB_PREFIX_.'state`
			WHERE `id_state` = '.(int)$id_state
        );
    }

    /**
     * @param $ids_states
     * @param $id_zone
     * @return bool
     */
    public function affectZoneToSelection($ids_states, $id_zone)
    {
        // cast every array values to int (security)
        $ids_states = array_map('intval', $ids_states);
        return Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.'state` SET `id_zone` = '.(int)$id_zone.' WHERE `id_state` IN ('.implode(',', $ids_states).')
		');
    }
}
