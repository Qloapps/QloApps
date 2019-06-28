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

class WebserviceKeyCore extends ObjectModel
{
    /** @var string Key */
    public $key;

    /** @var bool Webservice Account statuts */
    public $active = true;

    /** @var string Webservice Account description */
    public $description;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'webservice_account',
        'primary' => 'id_webservice_account',
        'fields' => array(
            'active' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'key' =>            array('type' => self::TYPE_STRING, 'required' => true, 'size' => 32),
            'description' =>    array('type' => self::TYPE_STRING),
        ),
    );

    public function add($autodate = true, $nullValues = false)
    {
        if (WebserviceKey::keyExists($this->key)) {
            return false;
        }
        return parent::add($autodate = true, $nullValues = false);
    }

    public static function keyExists($key)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT `key`
		FROM '._DB_PREFIX_.'webservice_account
		WHERE `key` = "'.pSQL($key).'"');
    }

    public function delete()
    {
        return (parent::delete() && ($this->deleteAssociations() !== false));
    }

    public function deleteAssociations()
    {
        return Db::getInstance()->delete('webservice_permission', 'id_webservice_account = '.(int)$this->id);
    }

    public static function getPermissionForAccount($auth_key)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT p.*
			FROM `'._DB_PREFIX_.'webservice_permission` p
			LEFT JOIN `'._DB_PREFIX_.'webservice_account` a ON (a.id_webservice_account = p.id_webservice_account)
			WHERE a.key = \''.pSQL($auth_key).'\'
		');
        $permissions = array();
        if ($result) {
            foreach ($result as $row) {
                $permissions[$row['resource']][] = $row['method'];
            }
        }
        return $permissions;
    }

    public static function isKeyActive($auth_key)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT active
		FROM `'._DB_PREFIX_.'webservice_account`
		WHERE `key` = "'.pSQL($auth_key).'"');
    }

    public static function getClassFromKey($auth_key)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT class_name
		FROM `'._DB_PREFIX_.'webservice_account`
		WHERE `key` = "'.pSQL($auth_key).'"');
    }

    public static function setPermissionForAccount($id_account, $permissions_to_set)
    {
        $ok = true;
        $sql = 'DELETE FROM `'._DB_PREFIX_.'webservice_permission` WHERE `id_webservice_account` = '.(int)$id_account;
        if (!Db::getInstance()->execute($sql)) {
            $ok = false;
        }
        if (isset($permissions_to_set)) {
            $permissions = array();
            $resources = WebserviceRequest::getResources();
            $methods = array('GET', 'PUT', 'POST', 'DELETE', 'HEAD');
            foreach ($permissions_to_set as $resource_name => $resource_methods) {
                if (in_array($resource_name, array_keys($resources))) {
                    foreach (array_keys($resource_methods) as $method_name) {
                        if (in_array($method_name, $methods)) {
                            $permissions[] = array($method_name, $resource_name);
                        }
                    }
                }
            }
            $account = new WebserviceKey($id_account);
            if ($account->deleteAssociations() && $permissions) {
                $sql = 'INSERT INTO `'._DB_PREFIX_.'webservice_permission` (`id_webservice_permission` ,`resource` ,`method` ,`id_webservice_account`) VALUES ';
                foreach ($permissions as $permission) {
                    $sql .= '(NULL , \''.pSQL($permission[1]).'\', \''.pSQL($permission[0]).'\', '.(int)$id_account.'), ';
                }
                $sql = rtrim($sql, ', ');
                if (!Db::getInstance()->execute($sql)) {
                    $ok = false;
                }
            }
        }
        return $ok;
    }
}
