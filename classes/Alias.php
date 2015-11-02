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

class AliasCore extends ObjectModel
{
    public $alias;
    public $search;
    public $active = true;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'alias',
        'primary' => 'id_alias',
        'fields' => array(
            'search' => array('type' => self::TYPE_STRING, 'validate' => 'isValidSearch', 'required' => true, 'size' => 255),
            'alias' =>    array('type' => self::TYPE_STRING, 'validate' => 'isValidSearch', 'required' => true, 'size' => 255),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );

    public function __construct($id = null, $alias = null, $search = null, $id_lang = null)
    {
        $this->def = Alias::getDefinition($this);
        $this->setDefinitionRetrocompatibility();

        if ($id) {
            parent::__construct($id);
        } elseif ($alias && Validate::isValidSearch($alias)) {
            if (!Alias::isFeatureActive()) {
                $this->alias = trim($alias);
                $this->search = trim($search);
            } else {
                $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT a.id_alias, a.search, a.alias
				FROM `'._DB_PREFIX_.'alias` a
				WHERE `alias` = \''.pSQL($alias).'\' AND `active` = 1');

                if ($row) {
                    $this->id = (int)$row['id_alias'];
                    $this->search = $search ? trim($search) : $row['search'];
                    $this->alias = $row['alias'];
                } else {
                    $this->alias = trim($alias);
                    $this->search = trim($search);
                }
            }
        }
    }


    public function add($autodate = true, $nullValues = false)
    {
        $this->alias = Tools::replaceAccentedChars($this->alias);
        $this->search = Tools::replaceAccentedChars($this->search);

        if (parent::add($autodate, $nullValues)) {
            // Set cache of feature detachable to true
            Configuration::updateGlobalValue('PS_ALIAS_FEATURE_ACTIVE', '1');
            return true;
        }
        return false;
    }

    public function delete()
    {
        if (parent::delete()) {
            // Refresh cache of feature detachable
            Configuration::updateGlobalValue('PS_ALIAS_FEATURE_ACTIVE', Alias::isCurrentlyUsed($this->def['table'], true));
            return true;
        }
        return false;
    }

    public function getAliases()
    {
        if (!Alias::isFeatureActive()) {
            return '';
        }

        $aliases = Db::getInstance()->executeS('
		SELECT a.alias
		FROM `'._DB_PREFIX_.'alias` a
		WHERE `search` = \''.pSQL($this->search).'\'');

        $aliases = array_map('implode', $aliases);
        return implode(', ', $aliases);
    }

    /**
     * This method is allow to know if a feature is used or active
     * @since 1.5.0.1
     * @return bool
     */
    public static function isFeatureActive()
    {
        return Configuration::get('PS_ALIAS_FEATURE_ACTIVE');
    }

    /**
     * This method is allow to know if a alias exist for AdminImportController
     * @since 1.5.6.0
     * @return bool
     */
    public static function aliasExists($id_alias)
    {
        $row = Db::getInstance()->getRow('
			SELECT `id_alias`
			FROM '._DB_PREFIX_.'alias a
			WHERE a.`id_alias` = '.(int)$id_alias
        );

        return isset($row['id_alias']);
    }
}
