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

class RangeWeightCore extends ObjectModel
{
    public $id_carrier;
    public $delimiter1;
    public $delimiter2;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'range_weight',
        'primary' => 'id_range_weight',
        'fields' => array(
            'id_carrier' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'delimiter1' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat', 'required' => true),
            'delimiter2' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat', 'required' => true),
        ),
    );

    protected $webserviceParameters = array(
            'objectNodeName' => 'weight_range',
            'objectsNodeName' => 'weight_ranges',
            'fields' => array(
            'id_carrier' => array('xlink_resource' => 'carriers'))
    );

    /**
     * Override add to create delivery value for all zones
     * @see classes/ObjectModelCore::add()
     *
     * @param bool $null_values
     * @param bool $autodate
     * @return bool Insertion result
     */
    public function add($autodate = true, $null_values = false)
    {
        if (!parent::add($autodate, $null_values) || !Validate::isLoadedObject($this)) {
            return false;
        }
        if (defined('PS_INSTALLATION_IN_PROGRESS')) {
            return true;
        }
        $carrier = new Carrier((int)$this->id_carrier);
        $price_list = array();
        foreach ($carrier->getZones() as $zone) {
            $price_list[] = array(
                'id_range_price' => null,
                'id_range_weight' => (int)$this->id,
                'id_carrier' => (int)$this->id_carrier,
                'id_zone' => (int)$zone['id_zone'],
                'price' => 0,
            );
        }
        $carrier->addDeliveryPrice($price_list);

        return true;
    }

    /**
    * Get all available price ranges
    *
    * @return array Ranges
    */
    public static function getRanges($id_carrier)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT *
            FROM `'._DB_PREFIX_.'range_weight`
            WHERE `id_carrier` = '.(int)$id_carrier.'
            ORDER BY `delimiter1` ASC');
    }

    public static function rangeExist($id_carrier, $delimiter1, $delimiter2, $id_reference = null)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT count(*)
            FROM `'._DB_PREFIX_.'range_weight` rw'.
            (is_null($id_carrier) && $id_reference ? '
            INNER JOIN `'._DB_PREFIX_.'carrier` c on (rw.`id_carrier` = c.`id_carrier`)' : '').'
            WHERE'.
            ($id_carrier ? ' `id_carrier` = '.(int)$id_carrier : '').
            (is_null($id_carrier) && $id_reference ? ' c.`id_reference` = '.(int)$id_reference : '').'
            AND `delimiter1` = '.(float)$delimiter1.' AND `delimiter2` = '.(float)$delimiter2);
    }

    public static function isOverlapping($id_carrier, $delimiter1, $delimiter2, $id_rang = null)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT count(*)
            FROM `'._DB_PREFIX_.'range_weight`
            WHERE `id_carrier` = '.(int)$id_carrier.'
            AND ((`delimiter1` >= '.(float)$delimiter1.' AND `delimiter1` < '.(float)$delimiter2.')
                OR (`delimiter2` > '.(float)$delimiter1.' AND `delimiter2` < '.(float)$delimiter2.')
                OR ('.(float)$delimiter1.' > `delimiter1` AND '.(float)$delimiter1.' < `delimiter2`)
                OR ('.(float)$delimiter2.' < `delimiter1` AND '.(float)$delimiter2.' > `delimiter2`)
            )
            '.(!is_null($id_rang) ? ' AND `id_range_weight` != '.(int)$id_rang : ''));
    }
}
