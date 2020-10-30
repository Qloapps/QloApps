<?php
/**
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2017 PrestaShop SA
 *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

abstract class ObjectModelCore implements Core_Foundation_Database_EntityInterface
{
    /**
     * List of field types
     */
    const TYPE_INT     = 1;
    const TYPE_BOOL    = 2;
    const TYPE_STRING  = 3;
    const TYPE_FLOAT   = 4;
    const TYPE_DATE    = 5;
    const TYPE_HTML    = 6;
    const TYPE_NOTHING = 7;
    const TYPE_SQL     = 8;

    /**
     * List of data to format
     */
    const FORMAT_COMMON = 1;
    const FORMAT_LANG   = 2;
    const FORMAT_SHOP   = 3;

    /**
     * List of association types
     */
    const HAS_ONE  = 1;
    const HAS_MANY = 2;

    /** @var int Object ID */
    public $id;

    /** @var int Language ID */
    protected $id_lang = null;

    /** @var int Shop ID */
    protected $id_shop = null;

    /** @var array List of shop IDs */
    public $id_shop_list = array();

    /** @var bool */
    protected $get_shop_from_context = true;

    /** @var array|null Holds required fields for each ObjectModel class */
    protected static $fieldsRequiredDatabase = null;

    /**
     * @deprecated 1.5.0.1 Define property using $definition['table'] property instead.
     * @var string
     */
    protected $table;

    /**
     * @deprecated 1.5.0.1 Define property using $definition['table'] property instead.
     * @var string
     */
    protected $identifier;

    /**
     * @deprecated 1.5.0.1 Define property using $definition['table'] property instead.
     * @var array
     */
    protected $fieldsRequired = array();

    /**
     * @deprecated 1.5.0.1 Define property using $definition['table'] property instead.
     * @var array
     */
    protected $fieldsSize = array();

    /**
     * @deprecated 1.5.0.1 Define property using $definition['table'] property instead.
     * @var array
     */
    protected $fieldsValidate = array();

    /**
     * @deprecated 1.5.0.1 Define property using $definition['table'] property instead.
     * @var array
     */
    protected $fieldsRequiredLang = array();

    /**
     * @deprecated 1.5.0.1 Define property using $definition['table'] property instead.
     * @var array
     */
    protected $fieldsSizeLang = array();

    /**
     * @deprecated 1.5.0.1 Define property using $definition['table'] property instead.
     * @var array
     */
    protected $fieldsValidateLang = array();

    /**
     * @deprecated 1.5.0.1
     * @var array
     */
    protected $tables = array();

    /** @var array Tables */
    protected $webserviceParameters = array();

    /** @var string Path to image directory. Used for image deletion. */
    protected $image_dir = null;

    /** @var string image name used for image deletion. */ //by webkul
    protected $image_name = null;

    /** @var String file type of image files. */
    protected $image_format = 'jpg';

    /**
     * @var array Contains object definition
     * @since 1.5.0.1
     */
    public static $definition = array();

    /**
     * Holds compiled definitions of each ObjectModel class.
     * Values are assigned during object initialization.
     *
     * @var array
     */
    protected static $loaded_classes = array();

    /** @var array Contains current object definition. */
    protected $def;

    /** @var array|null List of specific fields to update (all fields if null). */
    protected $update_fields = null;

    /** @var Db An instance of the db in order to avoid calling Db::getInstance() thousands of times. */
    protected static $db = false;

    /** @var bool Enables to define an ID before adding object. */
    public $force_id = false;

    /**
     * @var bool If true, objects are cached in memory.
     */
    protected static $cache_objects = true;

    public static function getRepositoryClassName()
    {
        return null;
    }

    /**
     * Returns object validation rules (fields validity)
     *
     * @param  string $class Child class name for static use (optional)
     *
     * @return array Validation rules (fields validity)
     */
    public static function getValidationRules($class = __CLASS__)
    {
        $object = new $class();
        return array(
            'required'     => $object->fieldsRequired,
            'size'         => $object->fieldsSize,
            'validate'     => $object->fieldsValidate,
            'requiredLang' => $object->fieldsRequiredLang,
            'sizeLang'     => $object->fieldsSizeLang,
            'validateLang' => $object->fieldsValidateLang,
        );
    }

    /**
     * Builds the object
     *
     * @param int|null $id      If specified, loads and existing object from DB (optional).
     * @param int|null $id_lang Required if object is multilingual (optional).
     * @param int|null $id_shop ID shop for objects with multishop tables.
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        $class_name = get_class($this);
        if (!isset(ObjectModel::$loaded_classes[$class_name])) {
            $this->def = ObjectModel::getDefinition($class_name);
            $this->setDefinitionRetrocompatibility();
            if (!Validate::isTableOrIdentifier($this->def['primary']) || !Validate::isTableOrIdentifier($this->def['table'])) {
                throw new PrestaShopException('Identifier or table format not valid for class '.$class_name);
            }

            ObjectModel::$loaded_classes[$class_name] = get_object_vars($this);
        } else {
            foreach (ObjectModel::$loaded_classes[$class_name] as $key => $value) {
                $this->{$key} = $value;
            }
        }

        if ($id_lang !== null) {
            $this->id_lang = (Language::getLanguage($id_lang) !== false) ? $id_lang : Configuration::get('PS_LANG_DEFAULT');
        }

        if ($id_shop && $this->isMultishop()) {
            $this->id_shop = (int)$id_shop;
            $this->get_shop_from_context = false;
        }

        if ($this->isMultishop() && !$this->id_shop) {
            $this->id_shop = Context::getContext()->shop->id;
        }

        if ($id) {
            $entity_mapper = Adapter_ServiceLocator::get("Adapter_EntityMapper");
            $entity_mapper->load($id, $id_lang, $this, $this->def, $this->id_shop, self::$cache_objects);
        }
    }

    /**
     * Prepare fields for ObjectModel class (add, update)
     * All fields are verified (pSQL, intval, ...)
     *
     * @return array All object fields
     * @throws PrestaShopException
     */
    public function getFields()
    {
        $this->validateFields();
        $fields = $this->formatFields(self::FORMAT_COMMON);

        // For retro compatibility
        if (Shop::isTableAssociated($this->def['table'])) {
            $fields = array_merge($fields, $this->getFieldsShop());
        }

        // Ensure that we get something to insert
        if (!$fields && isset($this->id) && Validate::isUnsignedId($this->id)) {
            $fields[$this->def['primary']] = $this->id;
        }

        return $fields;
    }

    /**
     * Prepare fields for multishop
     * Fields are not validated here, we consider they are already validated in getFields() method,
     * this is not the best solution but this is the only one possible for retro compatibility.
     *
     * @since 1.5.0.1
     * @return array All object fields
     */
    public function getFieldsShop()
    {
        $fields = $this->formatFields(self::FORMAT_SHOP);
        if (!$fields && isset($this->id) && Validate::isUnsignedId($this->id)) {
            $fields[$this->def['primary']] = $this->id;
        }

        return $fields;
    }

    /**
     * Prepare multilang fields
     *
     * @since 1.5.0.1
     *
     * @return array
     * @throws PrestaShopException
     */
    public function getFieldsLang()
    {
        // Backward compatibility
        if (method_exists($this, 'getTranslationsFieldsChild')) {
            return $this->getTranslationsFieldsChild();
        }

        $this->validateFieldsLang();
        $is_lang_multishop = $this->isLangMultishop();

        $fields = array();
        if ($this->id_lang === null) {
            foreach (Language::getIDs(false) as $id_lang) {
                $fields[$id_lang] = $this->formatFields(self::FORMAT_LANG, $id_lang);
                $fields[$id_lang]['id_lang'] = $id_lang;
                if ($this->id_shop && $is_lang_multishop) {
                    $fields[$id_lang]['id_shop'] = (int)$this->id_shop;
                }
            }
        } else {
            $fields = array($this->id_lang => $this->formatFields(self::FORMAT_LANG, $this->id_lang));
            $fields[$this->id_lang]['id_lang'] = $this->id_lang;
            if ($this->id_shop && $is_lang_multishop) {
                $fields[$this->id_lang]['id_shop'] = (int)$this->id_shop;
            }
        }

        return $fields;
    }

    /**
     * Formats values of each fields.
     *
     * @since 1.5.0.1
     * @param int $type    FORMAT_COMMON or FORMAT_LANG or FORMAT_SHOP
     * @param int $id_lang If this parameter is given, only take lang fields
     *
     * @return array
     */
    protected function formatFields($type, $id_lang = null)
    {
        $fields = array();

        // Set primary key in fields
        if (isset($this->id)) {
            $fields[$this->def['primary']] = $this->id;
        }

        foreach ($this->def['fields'] as $field => $data) {
            // Only get fields we need for the type
            // E.g. if only lang fields are filtered, ignore fields without lang => true
            if (($type == self::FORMAT_LANG && empty($data['lang']))
                || ($type == self::FORMAT_SHOP && empty($data['shop']))
                || ($type == self::FORMAT_COMMON && ((!empty($data['shop']) && $data['shop'] != 'both') || !empty($data['lang'])))) {
                continue;
            }

            if (is_array($this->update_fields)) {
                if ((!empty($data['lang']) || (!empty($data['shop']) && $data['shop'] != 'both')) && (empty($this->update_fields[$field]) || ($type == self::FORMAT_LANG && empty($this->update_fields[$field][$id_lang])))) {
                    continue;
                }
            }

            // Get field value, if value is multilang and field is empty, use value from default lang
            $value = $this->$field;
            if ($type == self::FORMAT_LANG && $id_lang && is_array($value)) {
                if (!empty($value[$id_lang])) {
                    $value = $value[$id_lang];
                } elseif (!empty($data['required'])) {
                    $value = $value[Configuration::get('PS_LANG_DEFAULT')];
                } else {
                    $value = '';
                }
            }

            $purify = (isset($data['validate']) && Tools::strtolower($data['validate']) == 'iscleanhtml') ? true : false;
            // Format field value
            $fields[$field] = ObjectModel::formatValue($value, $data['type'], false, $purify, !empty($data['allow_null']));
        }

        return $fields;
    }

    /**
     * Formats a value
     *
     * @param mixed	$value
     * @param int	$type
     * @param bool	$with_quotes
     * @param bool	$purify
     * @param bool	$allow_null
     * @return mixed
     */
    public static function formatValue($value, $type, $with_quotes = false, $purify = true, $allow_null = false)
    {
        if ($allow_null && $value === null) {
            return array('type' => 'sql', 'value' => 'NULL');
        }

        switch ($type) {
            case self::TYPE_INT:
                return (int)$value;

            case self::TYPE_BOOL:
                return (int)$value;

            case self::TYPE_FLOAT:
                return (float)str_replace(',', '.', $value);

            case self::TYPE_DATE:
                if (!$value) {
                    return '0000-00-00';
                }

                if ($with_quotes) {
                    return '\''.pSQL($value).'\'';
                }
                return pSQL($value);

            case self::TYPE_HTML:
                if ($purify) {
                    $value = Tools::purifyHTML($value);
                }
                if ($with_quotes) {
                    return '\''.pSQL($value, true).'\'';
                }
                return pSQL($value, true);

            case self::TYPE_SQL:
                if ($with_quotes) {
                    return '\''.pSQL($value, true).'\'';
                }
                return pSQL($value, true);

            case self::TYPE_NOTHING:
                return $value;

            case self::TYPE_STRING:
            default :
                if ($with_quotes) {
                    return '\''.pSQL($value).'\'';
                }
                return pSQL($value);
        }
    }

    /**
     * Saves current object to database (add or update)
     *
     * @param bool $null_values
     * @param bool $auto_date
     *
     * @return bool Insertion result
     * @throws PrestaShopException
     */
    public function save($null_values = false, $auto_date = true)
    {
        return (int)$this->id > 0 ? $this->update($null_values) : $this->add($auto_date, $null_values);
    }

    /**
     * Adds current object to the database
     *
     * @param bool $auto_date
     * @param bool $null_values
     *
     * @return bool Insertion result
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function add($auto_date = true, $null_values = false)
    {
        if (isset($this->id) && !$this->force_id) {
            unset($this->id);
        }

        // @hook actionObject*AddBefore
        Hook::exec('actionObjectAddBefore', array('object' => $this));
        Hook::exec('actionObject'.get_class($this).'AddBefore', array('object' => $this));

        // Automatically fill dates
        if ($auto_date && property_exists($this, 'date_add')) {
            $this->date_add = date('Y-m-d H:i:s');
        }
        if ($auto_date && property_exists($this, 'date_upd')) {
            $this->date_upd = date('Y-m-d H:i:s');
        }

        if (Shop::isTableAssociated($this->def['table'])) {
            $id_shop_list = Shop::getContextListShopID();
            if (count($this->id_shop_list) > 0) {
                $id_shop_list = $this->id_shop_list;
            }
        }

        // Database insertion
        if (Shop::checkIdShopDefault($this->def['table'])) {
            $this->id_shop_default = (in_array(Configuration::get('PS_SHOP_DEFAULT'), $id_shop_list) == true) ? Configuration::get('PS_SHOP_DEFAULT') : min($id_shop_list);
        }
        if (!$result = Db::getInstance()->insert($this->def['table'], $this->getFields(), $null_values)) {
            return false;
        }

        // Get object id in database
        $this->id = Db::getInstance()->Insert_ID();

        // Database insertion for multishop fields related to the object
        if (Shop::isTableAssociated($this->def['table'])) {
            $fields = $this->getFieldsShop();
            $fields[$this->def['primary']] = (int)$this->id;

            foreach ($id_shop_list as $id_shop) {
                $fields['id_shop'] = (int)$id_shop;
                $result &= Db::getInstance()->insert($this->def['table'].'_shop', $fields, $null_values);
            }
        }

        if (!$result) {
            return false;
        }

        // Database insertion for multilingual fields related to the object
        if (!empty($this->def['multilang'])) {
            $fields = $this->getFieldsLang();
            if ($fields && is_array($fields)) {
                $shops = Shop::getCompleteListOfShopsID();
                $asso = Shop::getAssoTable($this->def['table'].'_lang');
                foreach ($fields as $field) {
                    foreach (array_keys($field) as $key) {
                        if (!Validate::isTableOrIdentifier($key)) {
                            throw new PrestaShopException('key '.$key.' is not table or identifier');
                        }
                    }
                    $field[$this->def['primary']] = (int)$this->id;

                    if ($asso !== false && $asso['type'] == 'fk_shop') {
                        foreach ($shops as $id_shop) {
                            $field['id_shop'] = (int)$id_shop;
                            $result &= Db::getInstance()->insert($this->def['table'].'_lang', $field);
                        }
                    } else {
                        $result &= Db::getInstance()->insert($this->def['table'].'_lang', $field);
                    }
                }
            }
        }

        // @hook actionObject*AddAfter
        Hook::exec('actionObjectAddAfter', array('object' => $this));
        Hook::exec('actionObject'.get_class($this).'AddAfter', array('object' => $this));

        return $result;
    }

    /**
     * Takes current object ID, gets its values from database,
     * saves them in a new row and loads newly saved values as a new object.
     *
     * @return ObjectModel|false
     * @throws PrestaShopDatabaseException
     */
    public function duplicateObject()
    {
        $definition = ObjectModel::getDefinition($this);

        $res = Db::getInstance()->getRow('
					SELECT *
					FROM `'._DB_PREFIX_.bqSQL($definition['table']).'`
					WHERE `'.bqSQL($definition['primary']).'` = '.(int)$this->id
        );
        if (!$res) {
            return false;
        }

        unset($res[$definition['primary']]);
        foreach ($res as $field => &$value) {
            if (isset($definition['fields'][$field])) {
                $value = ObjectModel::formatValue($value, $definition['fields'][$field]['type'], false, true,
                    !empty($definition['fields'][$field]['allow_null']));
            }
        }

        if (!Db::getInstance()->insert($definition['table'], $res)) {
            return false;
        }

        $object_id = Db::getInstance()->Insert_ID();

        if (isset($definition['multilang']) && $definition['multilang']) {
            $result = Db::getInstance()->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.bqSQL($definition['table']).'_lang`
			WHERE `'.bqSQL($definition['primary']).'` = '.(int)$this->id);
            if (!$result) {
                return false;
            }

            foreach ($result as &$row) {
                foreach ($row as $field => &$value) {
                    if (isset($definition['fields'][$field])) {
                        $value = ObjectModel::formatValue($value, $definition['fields'][$field]['type'], false, true,
                            !empty($definition['fields'][$field]['allow_null']));
                    }
                }
            }

            // Keep $row2, you cannot use $row because there is an unexplicated conflict with the previous usage of this variable
            foreach ($result as $row2) {
                $row2[$definition['primary']] = (int)$object_id;
                if (!Db::getInstance()->insert($definition['table'].'_lang', $row2)) {
                    return false;
                }
            }
        }

        /** @var ObjectModel $object_duplicated */
        $object_duplicated = new $definition['classname']((int)$object_id);
        $object_duplicated->duplicateShops((int)$this->id);

        return $object_duplicated;
    }

    /**
     * Updates the current object in the database
     *
     * @param bool $null_values
     *
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function update($null_values = false)
    {
        // @hook actionObject*UpdateBefore
        Hook::exec('actionObjectUpdateBefore', array('object' => $this));
        Hook::exec('actionObject'.get_class($this).'UpdateBefore', array('object' => $this));

        $this->clearCache();

        // Automatically fill dates
        if (property_exists($this, 'date_upd')) {
            $this->date_upd = date('Y-m-d H:i:s');
            if (isset($this->update_fields) && is_array($this->update_fields) && count($this->update_fields)) {
                $this->update_fields['date_upd'] = true;
            }
        }

        // Automatically fill dates
        if (property_exists($this, 'date_add') && $this->date_add == null) {
            $this->date_add = date('Y-m-d H:i:s');
            if (isset($this->update_fields) && is_array($this->update_fields) && count($this->update_fields)) {
                $this->update_fields['date_add'] = true;
            }
        }

        $id_shop_list = Shop::getContextListShopID();
        if (count($this->id_shop_list) > 0) {
            $id_shop_list = $this->id_shop_list;
        }

        if (Shop::checkIdShopDefault($this->def['table']) && !$this->id_shop_default) {
            $this->id_shop_default = (in_array(Configuration::get('PS_SHOP_DEFAULT'), $id_shop_list) == true) ? Configuration::get('PS_SHOP_DEFAULT') : min($id_shop_list);
        }
        // Database update
        if (!$result = Db::getInstance()->update($this->def['table'], $this->getFields(), '`'.pSQL($this->def['primary']).'` = '.(int)$this->id, 0, $null_values)) {
            return false;
        }

        // Database insertion for multishop fields related to the object
        if (Shop::isTableAssociated($this->def['table'])) {
            $fields = $this->getFieldsShop();
            $fields[$this->def['primary']] = (int)$this->id;
            if (is_array($this->update_fields)) {
                $update_fields = $this->update_fields;
                $this->update_fields = null;
                $all_fields = $this->getFieldsShop();
                $all_fields[$this->def['primary']] = (int)$this->id;
                $this->update_fields = $update_fields;
            } else {
                $all_fields = $fields;
            }

            foreach ($id_shop_list as $id_shop) {
                $fields['id_shop'] = (int)$id_shop;
                $all_fields['id_shop'] = (int)$id_shop;
                $where = $this->def['primary'].' = '.(int)$this->id.' AND id_shop = '.(int)$id_shop;

                // A little explanation of what we do here : we want to create multishop entry when update is called, but
                // only if we are in a shop context (if we are in all context, we just want to update entries that alread exists)
                $shop_exists = Db::getInstance()->getValue('SELECT '.$this->def['primary'].' FROM '._DB_PREFIX_.$this->def['table'].'_shop WHERE '.$where);

                if ($shop_exists) {
                    if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
                        foreach ($fields as $key => $val) {
                             if (!property_exists((array)$this->update_fields, $key)) {
                                unset($fields[$key]);
                            }
                        }
                    }
                    $result &= Db::getInstance()->update($this->def['table'] . '_shop', $fields, $where, 0, $null_values);
                } elseif (Shop::getContext() == Shop::CONTEXT_SHOP) {
                    $result &= Db::getInstance()->insert($this->def['table'] . '_shop', $all_fields, $null_values);
                }
            }
        }

        // Database update for multilingual fields related to the object
        if (isset($this->def['multilang']) && $this->def['multilang']) {
            $fields = $this->getFieldsLang();
            if (is_array($fields)) {
                foreach ($fields as $field) {
                    foreach (array_keys($field) as $key) {
                        if (!Validate::isTableOrIdentifier($key)) {
                            throw new PrestaShopException('key '.$key.' is not a valid table or identifier');
                        }
                    }

                    // If this table is linked to multishop system, update / insert for all shops from context
                    if ($this->isLangMultishop()) {
                        $id_shop_list = Shop::getContextListShopID();
                        if (count($this->id_shop_list) > 0) {
                            $id_shop_list = $this->id_shop_list;
                        }
                        foreach ($id_shop_list as $id_shop) {
                            $field['id_shop'] = (int)$id_shop;
                            $where = pSQL($this->def['primary']).' = '.(int)$this->id
                                        .' AND id_lang = '.(int)$field['id_lang']
                                        .' AND id_shop = '.(int)$id_shop;

                            if (Db::getInstance()->getValue('SELECT COUNT(*) FROM '.pSQL(_DB_PREFIX_.$this->def['table']).'_lang WHERE '.$where)) {
                                $result &= Db::getInstance()->update($this->def['table'].'_lang', $field, $where);
                            } else {
                                $result &= Db::getInstance()->insert($this->def['table'].'_lang', $field);
                            }
                        }
                    }
                    // If this table is not linked to multishop system ...
                    else {
                        $where = pSQL($this->def['primary']).' = '.(int)$this->id
                                    .' AND id_lang = '.(int)$field['id_lang'];
                        if (Db::getInstance()->getValue('SELECT COUNT(*) FROM '.pSQL(_DB_PREFIX_.$this->def['table']).'_lang WHERE '.$where)) {
                            $result &= Db::getInstance()->update($this->def['table'].'_lang', $field, $where);
                        } else {
                            $result &= Db::getInstance()->insert($this->def['table'].'_lang', $field, $null_values);
                        }
                    }
                }
            }
        }

        // @hook actionObject*UpdateAfter
        Hook::exec('actionObjectUpdateAfter', array('object' => $this));
        Hook::exec('actionObject'.get_class($this).'UpdateAfter', array('object' => $this));

        return $result;
    }

    /**
     * Deletes current object from database
     *
     * @return bool True if delete was successful
     * @throws PrestaShopException
     */
    public function delete()
    {
        // @hook actionObject*DeleteBefore
        Hook::exec('actionObjectDeleteBefore', array('object' => $this));
        Hook::exec('actionObject'.get_class($this).'DeleteBefore', array('object' => $this));

        $this->clearCache();
        $result = true;
        // Remove association to multishop table
        if (Shop::isTableAssociated($this->def['table'])) {
            $id_shop_list = Shop::getContextListShopID();
            if (count($this->id_shop_list)) {
                $id_shop_list = $this->id_shop_list;
            }

            $result &= Db::getInstance()->delete($this->def['table'].'_shop', '`'.$this->def['primary'].'`='.(int)$this->id.' AND id_shop IN ('.implode(', ', $id_shop_list).')');
        }

        // Database deletion
        $has_multishop_entries = $this->hasMultishopEntries();
        if ($result && !$has_multishop_entries) {
            $result &= Db::getInstance()->delete($this->def['table'], '`'.bqSQL($this->def['primary']).'` = '.(int)$this->id);
        }

        if (!$result) {
            return false;
        }

        // Database deletion for multilingual fields related to the object
        if (!empty($this->def['multilang']) && !$has_multishop_entries) {
            $result &= Db::getInstance()->delete($this->def['table'].'_lang', '`'.bqSQL($this->def['primary']).'` = '.(int)$this->id);
        }

        // @hook actionObject*DeleteAfter
        Hook::exec('actionObjectDeleteAfter', array('object' => $this));
        Hook::exec('actionObject'.get_class($this).'DeleteAfter', array('object' => $this));

        return $result;
    }

    /**
     * Deletes multiple objects from the database at once
     *
     * @param array $ids Array of objects IDs.
     *
     * @return bool
     */
    public function deleteSelection($ids)
    {
        $result = true;
        foreach ($ids as $id) {
            $this->id = (int)$id;
            $result = $result && $this->delete();
        }

        return $result;
    }

    /**
     * Toggles object status in database
     *
     * @return bool Update result
     * @throws PrestaShopException
     */
    public function toggleStatus()
    {
        // Object must have a variable called 'active'
        if (!property_exists($this, 'active')) {
            throw new PrestaShopException('property "active" is missing in object '.get_class($this));
        }

        // Update only active field
        $this->setFieldsToUpdate(array('active' => true));

        // Update active status on object
        $this->active = !(int)$this->active;

        // Change status to active/inactive
        return $this->update(false);
    }

    /**
     * @deprecated 1.5.0.1 (use getFieldsLang())
     * @param array $fields_array
     *
     * @return array
     * @throws PrestaShopException
     */
    protected function getTranslationsFields($fields_array)
    {
        $fields = array();

        if ($this->id_lang == null) {
            foreach (Language::getIDs(false) as $id_lang) {
                $this->makeTranslationFields($fields, $fields_array, $id_lang);
            }
        } else {
            $this->makeTranslationFields($fields, $fields_array, $this->id_lang);
        }

        return $fields;
    }

    /**
     * @deprecated 1.5.0.1
     * @param array $fields
     * @param array $fields_array
     * @param int   $id_language
     *
     * @throws PrestaShopException
     */
    protected function makeTranslationFields(&$fields, &$fields_array, $id_language)
    {
        $fields[$id_language]['id_lang'] = $id_language;
        $fields[$id_language][$this->def['primary']] = (int)$this->id;
        if ($this->id_shop && $this->isLangMultishop()) {
            $fields[$id_language]['id_shop'] = (int)$this->id_shop;
        }
        foreach ($fields_array as $k => $field) {
            $html = false;
            $field_name = $field;
            if (is_array($field)) {
                $field_name = $k;
                $html = (isset($field['html'])) ? $field['html'] : false;
            }

            /* Check fields validity */
            if (!Validate::isTableOrIdentifier($field_name)) {
                throw new PrestaShopException('identifier is not table or identifier : '.$field_name);
            }

            // Copy the field, or the default language field if it's both required and empty
            if ((!$this->id_lang && isset($this->{$field_name}[$id_language]) && !empty($this->{$field_name}[$id_language]))
            || ($this->id_lang && isset($this->$field_name) && !empty($this->$field_name))) {
                $fields[$id_language][$field_name] = $this->id_lang ? pSQL($this->$field_name, $html) : pSQL($this->{$field_name}[$id_language], $html);
            } elseif (in_array($field_name, $this->fieldsRequiredLang)) {
                $fields[$id_language][$field_name] = pSQL($this->id_lang ? $this->$field_name : $this->{$field_name}[Configuration::get('PS_LANG_DEFAULT')], $html);
            } else {
                $fields[$id_language][$field_name] = '';
            }
        }
    }

    /**
     * Checks if object field values are valid before database interaction
     *
     * @param bool $die
     * @param bool $error_return
     *
     * @return bool|string True, false or error message.
     * @throws PrestaShopException
     */
    public function validateFields($die = true, $error_return = false)
    {
        foreach ($this->def['fields'] as $field => $data) {
            if (!empty($data['lang'])) {
                continue;
            }

            if (is_array($this->update_fields) && empty($this->update_fields[$field]) && isset($this->def['fields'][$field]['shop']) && $this->def['fields'][$field]['shop']) {
                continue;
            }

            $message = $this->validateField($field, $this->$field);
            if ($message !== true) {
                if ($die) {
                    throw new PrestaShopException($message);
                }
                return $error_return ? $message : false;
            }
        }

        return true;
    }

    /**
     * Checks if multilingual object field values are valid before database interaction.
     *
     * @param bool $die
     * @param bool $error_return
     *
     * @return bool|string True, false or error message.
     * @throws PrestaShopException
     */
    public function validateFieldsLang($die = true, $error_return = false)
    {
        foreach ($this->def['fields'] as $field => $data) {
            if (empty($data['lang'])) {
                continue;
            }

            $values = $this->$field;

            // If the object has not been loaded in multilanguage, then the value is the one for the current language of the object
            if (!is_array($values)) {
                $values = array($this->id_lang => $values);
            }

            // The value for the default must always be set, so we put an empty string if it does not exists
            if (!isset($values[Configuration::get('PS_LANG_DEFAULT')])) {
                $values[Configuration::get('PS_LANG_DEFAULT')] = '';
            }

            foreach ($values as $id_lang => $value) {
                if (is_array($this->update_fields) && empty($this->update_fields[$field][$id_lang])) {
                    continue;
                }

                $message = $this->validateField($field, $value, $id_lang);
                if ($message !== true) {
                    if ($die) {
                        throw new PrestaShopException($message);
                    }
                    return $error_return ? $message : false;
                }
            }
        }

        return true;
    }

    /**
     * Validate a single field
     *
     * @since 1.5.0.1
     * @param string   $field        Field name
     * @param mixed    $value        Field value
     * @param int|null $id_lang      Language ID
     * @param array    $skip         Array of fields to skip.
     * @param bool     $human_errors If true, uses more descriptive, translatable error strings.
     *
     * @return true|string True or error message string.
     * @throws PrestaShopException
     */
    public function validateField($field, $value, $id_lang = null, $skip = array(), $human_errors = false)
    {
        static $ps_lang_default = null;
        static $ps_allow_html_iframe = null;

        if ($ps_lang_default === null) {
            $ps_lang_default = Configuration::get('PS_LANG_DEFAULT');
        }

        if ($ps_allow_html_iframe === null) {
            $ps_allow_html_iframe = (int)Configuration::get('PS_ALLOW_HTML_IFRAME');
        }


        $this->cacheFieldsRequiredDatabase();
        $data = $this->def['fields'][$field];



        // Check if field is required
        $required_fields = (isset(self::$fieldsRequiredDatabase[get_class($this)])) ? self::$fieldsRequiredDatabase[get_class($this)] : array();
        if (!$id_lang || $id_lang == $ps_lang_default) {
            if (!in_array('required', $skip) && (!empty($data['required']) || in_array($field, $required_fields))) {
                if (Tools::isEmpty($value)) {
                    if ($human_errors) {
                        return sprintf(Tools::displayError('The %s field is required.'), $this->displayFieldName($field, get_class($this)));
                    } else {
                        return 'Property '.get_class($this).'->'.$field.' is empty';
                    }
                }
            }
        }

        // Default value
        if (!$value && !empty($data['default'])) {
            $value = $data['default'];
            $this->$field = $value;
        }

        // Check field values
        if (!in_array('values', $skip) && !empty($data['values']) && is_array($data['values']) && !in_array($value, $data['values'])) {
            return 'Property '.get_class($this).'->'.$field.' has bad value (allowed values are: '.implode(', ', $data['values']).')';
        }

        // Check field size
        if (!in_array('size', $skip) && !empty($data['size'])) {
            $size = $data['size'];
            if (!is_array($data['size'])) {
                $size = array('min' => 0, 'max' => $data['size']);
            }

            $length = Tools::strlen($value);
            if ($length < $size['min'] || $length > $size['max']) {
                if ($human_errors) {
                    if (isset($data['lang']) && $data['lang']) {
                        $language = new Language((int)$id_lang);
                        return sprintf(Tools::displayError('The field %1$s (%2$s) is too long (%3$d chars max, html chars including).'), $this->displayFieldName($field, get_class($this)), $language->name, $size['max']);
                    } else {
                        return sprintf(Tools::displayError('The %1$s field is too long (%2$d chars max).'), $this->displayFieldName($field, get_class($this)), $size['max']);
                    }
                } else {
                    return 'Property '.get_class($this).'->'.$field.' length ('.$length.') must be between '.$size['min'].' and '.$size['max'];
                }
            }
        }

        // Check field validator
        if (!in_array('validate', $skip) && !empty($data['validate'])) {
            if (!method_exists('Validate', $data['validate'])) {
                throw new PrestaShopException('Validation function not found. '.$data['validate']);
            }

            if (!empty($value)) {
                $res = true;
                if (Tools::strtolower($data['validate']) == 'iscleanhtml') {
                    if (!call_user_func(array('Validate', $data['validate']), $value, $ps_allow_html_iframe)) {
                        $res = false;
                    }
                } else {
                    if (!call_user_func(array('Validate', $data['validate']), $value)) {
                        $res = false;
                    }
                }
                if (!$res) {
                    if ($human_errors) {
                        return sprintf(Tools::displayError('The %s field is invalid.'), $this->displayFieldName($field, get_class($this)));
                    } else {
                        return 'Property '.get_class($this).'->'.$field.' is not valid';
                    }
                }
            }
        }

        return true;
    }

    /**
     * Returns field name translation
     *
     * @param string       $field        Field name
     * @param string       $class        ObjectModel class name
     * @param bool         $htmlentities If true, applies htmlentities() to result string
     * @param Context|null $context      Context object
     *
     * @return string
     */
    public static function displayFieldName($field, $class = __CLASS__, $htmlentities = true, Context $context = null)
    {
        global $_FIELDS;

        if (!isset($context)) {
            $context = Context::getContext();
        }

        if ($_FIELDS === null && file_exists(_PS_TRANSLATIONS_DIR_.$context->language->iso_code.'/fields.php')) {
            include_once(_PS_TRANSLATIONS_DIR_.$context->language->iso_code.'/fields.php');
        }

        $key = $class.'_'.md5($field);
        return ((is_array($_FIELDS) && property_exists($_FIELDS, $key)) ? ($htmlentities ? htmlentities($_FIELDS[$key], ENT_QUOTES, 'utf-8') : $_FIELDS[$key]) : $field);
    }

    /**
     * @deprecated 1.5.0.1 Use validateController() instead
     * @param bool $htmlentities
     *
     * @return array
     */
    public function validateControler($htmlentities = true)
    {
        Tools::displayAsDeprecated();
        return $this->validateController($htmlentities);
    }

    /**
     * Validates submitted values and returns an array of errors, if any.
     *
     * @param bool $htmlentities If true, uses htmlentities() for field name translations in errors.
     *
     * @return array
     */
    public function validateController($htmlentities = true)
    {
        $this->cacheFieldsRequiredDatabase();
        $errors = array();
        $required_fields_database = (isset(self::$fieldsRequiredDatabase[get_class($this)])) ? self::$fieldsRequiredDatabase[get_class($this)] : array();
        foreach ($this->def['fields'] as $field => $data) {
            $value = Tools::getValue($field, $this->{$field});
            // Check if field is required by user
            if (in_array($field, $required_fields_database)) {
                $data['required'] = true;
            }

            // Checking for required fields
            if (isset($data['required']) && $data['required'] && empty($value) && $value !== '0') {
                if (!$this->id || $field != 'passwd') {
                    $errors[$field] = '<b>'.self::displayFieldName($field, get_class($this), $htmlentities).'</b> '.Tools::displayError('is required.');
                }
            }

            // Checking for maximum fields sizes
            if (isset($data['size']) && !empty($value) && Tools::strlen($value) > $data['size']) {
                $errors[$field] = sprintf(
                    Tools::displayError('%1$s is too long. Maximum length: %2$d'),
                    self::displayFieldName($field, get_class($this), $htmlentities),
                    $data['size']
                );
            }

            // Checking for fields validity
            // Hack for postcode required for country which does not have postcodes
            if (!empty($value) || $value === '0' || ($field == 'postcode' && $value == '0')) {
                $validation_error = false;
                if (isset($data['validate'])) {
                    $data_validate = $data['validate'];
                    if (!Validate::$data_validate($value) && (!empty($value) || $data['required'])) {
                        $errors[$field] = '<b>'.self::displayFieldName($field, get_class($this), $htmlentities).
                            '</b> '.Tools::displayError('is invalid.');
                        $validation_error = true;
                    }
                }

                if (!$validation_error) {
                    if (isset($data['copy_post']) && !$data['copy_post']) {
                        continue;
                    }
                    if ($field == 'passwd') {
                        if ($value = Tools::getValue($field)) {
                            $this->{$field} = Tools::encrypt($value);
                        }
                    } else {
                        $this->{$field} = $value;
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Returns webservice parameters of this object.
     *
     * @param string|null $ws_params_attribute_name
     *
     * @return array
     */
    public function getWebserviceParameters($ws_params_attribute_name = null)
    {
        $this->cacheFieldsRequiredDatabase();
        $default_resource_parameters = array(
            'objectSqlId' => $this->def['primary'],
            'retrieveData' => array(
                'className' => get_class($this),
                'retrieveMethod' => 'getWebserviceObjectList',
                'params' => array(),
                'table' => $this->def['table'],
            ),
            'fields' => array(
                'id' => array('sqlId' => $this->def['primary'], 'i18n' => false),
            ),
        );

        if ($ws_params_attribute_name === null) {
            $ws_params_attribute_name = 'webserviceParameters';
        }

        if (!isset($this->{$ws_params_attribute_name}['objectNodeName'])) {
            $default_resource_parameters['objectNodeName'] = $this->def['table'];
        }
        if (!isset($this->{$ws_params_attribute_name}['objectsNodeName'])) {
            $default_resource_parameters['objectsNodeName'] = $this->def['table'].'s';
        }

        if (isset($this->{$ws_params_attribute_name}['associations'])) {
            foreach ($this->{$ws_params_attribute_name}['associations'] as $assoc_name => &$association) {
                if (!property_exists($association, 'setter') || (isset($association['setter']) && !$association['setter'])) {
                    $association['setter'] = Tools::toCamelCase('set_ws_'.$assoc_name);
                }
                if (!property_exists($association, 'getter')) {
                    $association['getter'] = Tools::toCamelCase('get_ws_'.$assoc_name);
                }
            }
        }

        if (isset($this->{$ws_params_attribute_name}['retrieveData']) && isset($this->{$ws_params_attribute_name}['retrieveData']['retrieveMethod'])) {
            unset($default_resource_parameters['retrieveData']['retrieveMethod']);
        }

        $resource_parameters = array_merge_recursive($default_resource_parameters, $this->{$ws_params_attribute_name});

        $required_fields = (isset(self::$fieldsRequiredDatabase[get_class($this)]) ? self::$fieldsRequiredDatabase[get_class($this)] : array());
        foreach ($this->def['fields'] as $field_name => $details) {
            if (!isset($resource_parameters['fields'][$field_name])) {
                $resource_parameters['fields'][$field_name] = array();
            }
            $current_field = array();
            $current_field['sqlId'] = $field_name;
            if (isset($details['size'])) {
                $current_field['maxSize'] = $details['size'];
            }
            if (isset($details['lang'])) {
                $current_field['i18n'] = $details['lang'];
            } else {
                $current_field['i18n'] = false;
            }
            if ((isset($details['required']) && $details['required'] === true) || in_array($field_name, $required_fields)) {
                $current_field['required'] = true;
            } else {
                $current_field['required'] = false;
            }
            if (isset($details['validate'])) {
                $current_field['validateMethod'] = (
                    property_exists($resource_parameters['fields'][$field_name], 'validateMethod') ?
                                array_merge($resource_parameters['fields'][$field_name]['validateMethod'], array($details['validate'])) :
                                array($details['validate'])
                );
            }
            $resource_parameters['fields'][$field_name] = array_merge($resource_parameters['fields'][$field_name], $current_field);

            if (isset($details['ws_modifier'])) {
                $resource_parameters['fields'][$field_name]['modifier'] = $details['ws_modifier'];
            }
        }
        if (isset($this->date_add)) {
            $resource_parameters['fields']['date_add']['setter'] = false;
        }
        if (isset($this->date_upd)) {
            $resource_parameters['fields']['date_upd']['setter'] = false;
        }
        foreach ($resource_parameters['fields'] as $key => $resource_parameters_field) {
            if (!isset($resource_parameters_field['sqlId'])) {
                $resource_parameters['fields'][$key]['sqlId'] = $key;
            }
        }

        return $resource_parameters;
    }

    /**
     * Returns webservice object list.
     *
     * @param string $sql_join
     * @param string $sql_filter
     * @param string $sql_sort
     * @param string $sql_limit
     *
     * @return array|null
     * @throws PrestaShopDatabaseException
     */
    public function getWebserviceObjectList($sql_join, $sql_filter, $sql_sort, $sql_limit)
    {
        $assoc = Shop::getAssoTable($this->def['table']);
        $class_name = WebserviceRequest::$ws_current_classname;
        $vars = get_class_vars($class_name);
        if ($assoc !== false) {
            if ($assoc['type'] !== 'fk_shop') {
                $multi_shop_join = ' LEFT JOIN `'._DB_PREFIX_.bqSQL($this->def['table']).'_'.bqSQL($assoc['type']).'`
										AS `multi_shop_'.bqSQL($this->def['table']).'`
										ON (main.`'.bqSQL($this->def['primary']).'` = `multi_shop_'.bqSQL($this->def['table']).'`.`'.bqSQL($this->def['primary']).'`)';
                $sql_filter = 'AND `multi_shop_'.bqSQL($this->def['table']).'`.id_shop = '.Context::getContext()->shop->id.' '.$sql_filter;
                $sql_join = $multi_shop_join.' '.$sql_join;
            } else {
                $vars = get_class_vars($class_name);
                foreach ($vars['shopIDs'] as $id_shop) {
                    $or[] = '(main.id_shop = '.(int)$id_shop.(isset($this->def['fields']['id_shop_group']) ? ' OR (id_shop = 0 AND id_shop_group='.(int)Shop::getGroupFromShop((int)$id_shop).')' : '').')';
                }

                $prepend = '';
                if (count($or)) {
                    $prepend = 'AND ('.implode('OR', $or).')';
                }
                $sql_filter = $prepend.' '.$sql_filter;
            }
        }
        $query = '
		SELECT DISTINCT main.`'.bqSQL($this->def['primary']).'` FROM `'._DB_PREFIX_.bqSQL($this->def['table']).'` AS main
		'.$sql_join.'
		WHERE 1 '.$sql_filter.'
		'.($sql_sort != '' ? $sql_sort : '').'
		'.($sql_limit != '' ? $sql_limit : '');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    /**
     * Validate required fields.
     *
     * @param bool $htmlentities
     *
     * @return array
     * @throws PrestaShopException
     */
    public function validateFieldsRequiredDatabase($htmlentities = true)
    {
        $this->cacheFieldsRequiredDatabase();
        $errors = array();
        $required_fields = (isset(self::$fieldsRequiredDatabase[get_class($this)])) ? self::$fieldsRequiredDatabase[get_class($this)] : array();

        foreach ($this->def['fields'] as $field => $data) {
            if (!in_array($field, $required_fields)) {
                continue;
            }

            if (!method_exists('Validate', $data['validate'])) {
                throw new PrestaShopException('Validation function not found. '.$data['validate']);
            }

            $value = Tools::getValue($field);

            if (empty($value)) {
                $errors[$field] = sprintf(Tools::displayError('The field %s is required.'), self::displayFieldName($field, get_class($this), $htmlentities));
            }
        }

        return $errors;
    }

    /**
     * Returns an array of required fields
     *
     * @param bool $all If true, returns required fields of all object classes.
     *
     * @return array|null
     * @throws PrestaShopDatabaseException
     */
    public function getFieldsRequiredDatabase($all = false)
    {
        return Db::getInstance()->executeS('
		SELECT id_required_field, object_name, field_name
		FROM '._DB_PREFIX_.'required_field
		'.(!$all ? 'WHERE object_name = \''.pSQL(get_class($this)).'\'' : ''));
    }

    /**
     * Caches data about required objects fields in memory
     *
     * @param bool $all If true, caches required fields of all object classes.
     */
    public function cacheFieldsRequiredDatabase($all = true)
    {
        if (!is_array(self::$fieldsRequiredDatabase)) {
            $fields = $this->getfieldsRequiredDatabase((bool)$all);
            if ($fields) {
                foreach ($fields as $row) {
                    self::$fieldsRequiredDatabase[$row['object_name']][(int)$row['id_required_field']] = pSQL($row['field_name']);
                }
            } else {
                self::$fieldsRequiredDatabase = array();
            }
        }
    }

    /**
     * Sets required field for this class in the database.
     *
     * @param array $fields
     *
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function addFieldsRequiredDatabase($fields)
    {
        if (!is_array($fields)) {
            return false;
        }

        if (!Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'required_field WHERE object_name = \''.get_class($this).'\'')) {
            return false;
        }

        foreach ($fields as $field) {
            if (!Db::getInstance()->insert('required_field', array('object_name' => get_class($this), 'field_name' => pSQL($field)))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Clears cache entries that have this object's ID.
     *
     * @param bool $all If true, clears cache for all objects
     */
    public function clearCache($all = false)
    {
        if ($all) {
            Cache::clean('objectmodel_'.$this->def['classname'].'_*');
        } elseif ($this->id) {
            Cache::clean('objectmodel_'.$this->def['classname'].'_'.(int)$this->id.'_*');
        }
    }

    /**
     * Checks if current object is associated to a shop.
     *
     * @since 1.5.0.1
     * @param int|null $id_shop
     * @return bool
     */
    public function isAssociatedToShop($id_shop = null)
    {
        if ($id_shop === null) {
            $id_shop = Context::getContext()->shop->id;
        }

        $cache_id = 'objectmodel_shop_'.$this->def['classname'].'_'.(int)$this->id.'-'.(int)$id_shop;
        if (!ObjectModel::$cache_objects || !Cache::isStored($cache_id)) {
            $associated = (bool)Db::getInstance()->getValue('
				SELECT id_shop
				FROM `'.pSQL(_DB_PREFIX_.$this->def['table']).'_shop`
				WHERE `'.$this->def['primary'].'` = '.(int)$this->id.'
				AND id_shop = '.(int)$id_shop
            );

            if (!ObjectModel::$cache_objects) {
                return $associated;
            }

            Cache::store($cache_id, $associated);
            return $associated;
        }

        return Cache::retrieve($cache_id);
    }

    /**
     * This function associate an item to its context
     *
     * @param int|array $id_shops
     *
     * @return bool|void
     * @throws PrestaShopDatabaseException
     */
    public function associateTo($id_shops)
    {
        if (!$this->id) {
            return;
        }

        if (!is_array($id_shops)) {
            $id_shops = array($id_shops);
        }

        $data = array();
        foreach ($id_shops as $id_shop) {
            if (!$this->isAssociatedToShop($id_shop)) {
                $data[] = array(
                    $this->def['primary'] => (int)$this->id,
                    'id_shop' => (int)$id_shop,
                );
            }
        }

        if ($data) {
            return Db::getInstance()->insert($this->def['table'].'_shop', $data);
        }

        return true;
    }

    /**
     * Gets the list of associated shop IDs
     *
     * @since 1.5.0.1
     *
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getAssociatedShops()
    {
        if (!Shop::isTableAssociated($this->def['table'])) {
            return array();
        }

        $list = array();
        $sql = 'SELECT id_shop FROM `'._DB_PREFIX_.$this->def['table'].'_shop` WHERE `'.$this->def['primary'].'` = '.(int)$this->id;
        foreach (Db::getInstance()->executeS($sql) as $row) {
            $list[] = $row['id_shop'];
        }

        return $list;
    }

    /**
     * Copies shop association data from object with specified ID.
     *
     * @since 1.5.0.1
     * @param $id
     *
     * @return bool|void
     * @throws PrestaShopDatabaseException
     */
    public function duplicateShops($id)
    {
        if (!Shop::isTableAssociated($this->def['table'])) {
            return false;
        }

        $sql = 'SELECT id_shop
				FROM '._DB_PREFIX_.$this->def['table'].'_shop
				WHERE '.$this->def['primary'].' = '.(int)$id;
        if ($results = Db::getInstance()->executeS($sql)) {
            $ids = array();
            foreach ($results as $row) {
                $ids[] = $row['id_shop'];
            }
            return $this->associateTo($ids);
        }

        return false;
    }

    /**
     * Checks if there is more than one entry in associated shop table for current object.
     *
     * @since 1.5.0.1
     *
     * @return bool
     */
    public function hasMultishopEntries()
    {
        if (!Shop::isTableAssociated($this->def['table']) || !Shop::isFeatureActive()) {
            return false;
        }

        return (bool)Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.$this->def['table'].'_shop` WHERE `'.$this->def['primary'].'` = '.(int)$this->id);
    }

    /**
     * Checks if object is multi-shop object.
     *
     * @return bool
     */
    public function isMultishop()
    {
        return Shop::isTableAssociated($this->def['table']) || !empty($this->def['multilang_shop']);
    }

    /**
     * Checks if a field is a multi-shop field.
     *
     * @param string $field
     *
     * @return bool
     */
    public function isMultiShopField($field)
    {
        return (isset($this->def['fields'][$field]) && isset($this->def['fields'][$field]['shop']) && $this->def['fields'][$field]['shop']);
    }

    /**
     * Checks if the object is both multi-language and multi-shop.
     *
     * @return bool
     */
    public function isLangMultishop()
    {
        return !empty($this->def['multilang']) && !empty($this->def['multilang_shop']);
    }

    /**
     * Updates a table and splits the common datas and the shop datas.
     *
     * @since 1.5.0.1
     * @param string $classname
     * @param array  $data
     * @param string $where
     * @param string $specific_where Only executed for common table
     *
     * @return bool
     */
    public static function updateMultishopTable($classname, $data, $where = '', $specific_where = '')
    {
        $def = ObjectModel::getDefinition($classname);
        $update_data = array();
        foreach ($data as $field => $value) {
            if (!isset($def['fields'][$field])) {
                continue;
            }

            if (!empty($def['fields'][$field]['shop'])) {
                if ($value === null && !empty($def['fields'][$field]['allow_null'])) {
                    $update_data[] = "a.$field = NULL";
                    $update_data[] = "{$def['table']}_shop.$field = NULL";
                } else {
                    $update_data[] = "a.$field = '$value'";
                    $update_data[] = "{$def['table']}_shop.$field = '$value'";
                }
            } else {
                if ($value === null && !empty($def['fields'][$field]['allow_null'])) {
                    $update_data[] = "a.$field = NULL";
                } else {
                    $update_data[] = "a.$field = '$value'";
                }
            }
        }

        $sql = 'UPDATE '._DB_PREFIX_.$def['table'].' a
				'.Shop::addSqlAssociation($def['table'], 'a', true, null, true).'
				SET '.implode(', ', $update_data).
                (!empty($where) ? ' WHERE '.$where : '');

        return Db::getInstance()->execute($sql);
    }

    /**
     * Delete images associated with the object
     *
     * @param bool $force_delete
     *
     * @return bool
     */
    public function deleteImage($force_delete = false)
    {
        if (!$this->id) {
            return false;
        }

        if ($force_delete || !$this->hasMultishopEntries()) {
            /* Deleting object images and thumbnails (cache) */
            if ($this->image_dir) {
                if ($this->image_name) { // by webkul
                    if (file_exists($this->image_dir.$this->image_name.'.'.$this->image_format)
                        && !unlink($this->image_dir.$this->image_name.'.'.$this->image_format)) {
                        return false;
                    }
                } else {
                    if (file_exists($this->image_dir.$this->id.'.'.$this->image_format)
                       && !unlink($this->image_dir.$this->id.'.'.$this->image_format)) {
                        return false;
                    }
                }
            }
            if (file_exists(_PS_TMP_IMG_DIR_.$this->def['table'].'_'.$this->id.'.'.$this->image_format)
                && !unlink(_PS_TMP_IMG_DIR_.$this->def['table'].'_'.$this->id.'.'.$this->image_format)) {
                return false;
            }
            if (file_exists(_PS_TMP_IMG_DIR_.$this->def['table'].'_mini_'.$this->id.'.'.$this->image_format)
                && !unlink(_PS_TMP_IMG_DIR_.$this->def['table'].'_mini_'.$this->id.'.'.$this->image_format)) {
                return false;
            }

            $types = ImageType::getImagesTypes();
            foreach ($types as $image_type) {
                if ($this->image_name) { // by webkul
                    if (file_exists($this->image_dir.$this->id.'-'.stripslashes($image_type['name']).'.'.$this->image_format)
                        && !unlink($this->image_dir.$this->id.'-'.stripslashes($image_type['name']).'.'.$this->image_format)) {
                        return false;
                    }
                } else {
                    if (file_exists($this->image_dir.$this->id.'-'.stripslashes($image_type['name']).'.'.$this->image_format)
                        && !unlink($this->image_dir.$this->id.'-'.stripslashes($image_type['name']).'.'.$this->image_format)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Checks if an object exists in database.
     *
     * @param int    $id_entity
     * @param string $table
     *
     * @return bool
     */
    public static function existsInDatabase($id_entity, $table)
    {
        $row = Db::getInstance()->getRow(
            '
			SELECT `id_'.bqSQL($table).'` as id
			FROM `'._DB_PREFIX_.bqSQL($table).'` e
			WHERE e.`id_'.bqSQL($table).'` = '.(int)$id_entity,
            false
        );

        return isset($row['id']);
    }

    /**
     * Checks if an object type exists in the database.
     *
     * @since 1.5.0.1
     * @param string|null $table             Name of table linked to entity
     * @param bool        $has_active_column True if the table has an active column
     *
     * @return bool
     */
    public static function isCurrentlyUsed($table = null, $has_active_column = false)
    {
        if ($table === null) {
            $table = self::$definition['table'];
        }

        $query = new DbQuery();
        $query->select('`id_'.bqSQL($table).'`');
        $query->from($table);
        if ($has_active_column) {
            $query->where('`active` = 1');
        }

        return (bool)Db::getInstance()->getValue($query);
    }

    /**
     * Fill an object with given data. Data must be an array with this syntax:
     * array(objProperty => value, objProperty2 => value, etc.)
     *
     * @since 1.5.0.1
     * @param array    $data
     * @param int|null $id_lang
     */
    public function hydrate(array $data, $id_lang = null)
    {
        $this->id_lang = $id_lang;
        if (isset($data[$this->def['primary']])) {
            $this->id = $data[$this->def['primary']];
        }

        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Fill (hydrate) a list of objects in order to get a collection of these objects
     *
     * @since 1.5.0.1
     * @param string    $class   Class of objects to hydrate
     * @param array     $datas   List of data (multi-dimensional array)
     * @param int|null  $id_lang
     *
     * @return array
     * @throws PrestaShopException
     */
    public static function hydrateCollection($class, array $datas, $id_lang = null)
    {
        if (!class_exists($class)) {
            throw new PrestaShopException("Class '$class' not found");
        }

        $collection = array();
        $rows = array();
        if ($datas) {
            $definition = ObjectModel::getDefinition($class);
            if (!property_exists($datas[0], $definition['primary'])) {
                throw new PrestaShopException("Identifier '{$definition['primary']}' not found for class '$class'");
            }

            foreach ($datas as $row) {
                // Get object common properties
                $id = $row[$definition['primary']];
                if (!isset($rows[$id])) {
                    $rows[$id] = $row;
                }

                // Get object lang properties
                if (isset($row['id_lang']) && !$id_lang) {
                    foreach ($definition['fields'] as $field => $data) {
                        if (!empty($data['lang'])) {
                            if (!is_array($rows[$id][$field])) {
                                $rows[$id][$field] = array();
                            }
                            $rows[$id][$field][$row['id_lang']] = $row[$field];
                        }
                    }
                }
            }
        }

        // Hydrate objects
        foreach ($rows as $row) {
            /** @var ObjectModel $obj */
            $obj = new $class;
            $obj->hydrate($row, $id_lang);
            $collection[] = $obj;
        }

        return $collection;
    }

    /**
     * Returns object definition
     *
     * @param string      $class Name of object
     * @param string|null $field Name of field if we want the definition of one field only
     *
     * @return array
     */
    public static function getDefinition($class, $field = null)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if ($field === null) {
            $cache_id = 'objectmodel_def_'.$class;
        }

        if ($field !== null || !Cache::isStored($cache_id)) {
            $reflection = new ReflectionClass($class);

            if (!$reflection->hasProperty('definition')) {
                return false;
            }

            $definition = $reflection->getStaticPropertyValue('definition');

            $definition['classname'] = $class;

            if (!empty($definition['multilang'])) {
                $definition['associations'][PrestaShopCollection::LANG_ALIAS] = array(
                    'type' => self::HAS_MANY,
                    'field' => $definition['primary'],
                    'foreign_field' => $definition['primary'],
                );
            }

            if ($field) {
                return isset($definition['fields'][$field]) ? $definition['fields'][$field] : null;
            }

            Cache::store($cache_id, $definition);
            return $definition;
        }

        return Cache::retrieve($cache_id);
    }

    /**
     * Retrocompatibility for classes without $definition static
     * @TODO Remove this in 1.6 !
     *
     * @since 1.5.0.1
     */
    protected function setDefinitionRetrocompatibility()
    {
        // Retrocompatibility with $table property ($definition['table'])
        if (isset($this->def['table'])) {
            $this->table = $this->def['table'];
        } else {
            $this->def['table'] = $this->table;
        }

        // Retrocompatibility with $identifier property ($definition['primary'])
        if (isset($this->def['primary'])) {
            $this->identifier = $this->def['primary'];
        } else {
            $this->def['primary'] = $this->identifier;
        }

        // Check multilang retrocompatibility
        if (method_exists($this, 'getTranslationsFieldsChild')) {
            $this->def['multilang'] = true;
        }

        // Retrocompatibility with $fieldsValidate, $fieldsRequired and $fieldsSize properties ($definition['fields'])
        if (isset($this->def['fields'])) {
            foreach ($this->def['fields'] as $field => $data) {
                $suffix = (isset($data['lang']) && $data['lang']) ? 'Lang' : '';
                if (isset($data['validate'])) {
                    $this->{'fieldsValidate'.$suffix}[$field] = $data['validate'];
                }
                if (isset($data['required']) && $data['required']) {
                    $this->{'fieldsRequired'.$suffix}[] = $field;
                }
                if (isset($data['size'])) {
                    $this->{'fieldsSize'.$suffix}[$field] = $data['size'];
                }
            }
        } else {
            $this->def['fields'] = array();
            $suffixs = array('', 'Lang');
            foreach ($suffixs as $suffix) {
                foreach ($this->{'fieldsValidate'.$suffix} as $field => $validate) {
                    $this->def['fields'][$field]['validate'] = $validate;
                    if ($suffix == 'Lang') {
                        $this->def['fields'][$field]['lang'] = true;
                    }
                }
                foreach ($this->{'fieldsRequired'.$suffix} as $field) {
                    $this->def['fields'][$field]['required'] = true;
                    if ($suffix == 'Lang') {
                        $this->def['fields'][$field]['lang'] = true;
                    }
                }
                foreach ($this->{'fieldsSize'.$suffix} as $field => $size) {
                    $this->def['fields'][$field]['size'] = $size;
                    if ($suffix == 'Lang') {
                        $this->def['fields'][$field]['lang'] = true;
                    }
                }
            }
        }
    }

    /**
     * Return the field value for the specified language if the field is multilang,
     * else the field value.
     *
     * @since 1.5.0.1
     * @param string   $field_name
     * @param int|null $id_lang
     *
     * @return mixed
     * @throws PrestaShopException
     */
    public function getFieldByLang($field_name, $id_lang = null)
    {
        $definition = ObjectModel::getDefinition($this);
        // Is field in definition?
        if ($definition && isset($definition['fields'][$field_name])) {
            $field = $definition['fields'][$field_name];
            // Is field multilang?
            if (isset($field['lang']) && $field['lang']) {
                if (is_array($this->{$field_name})) {
                    return $this->{$field_name}[$id_lang ? $id_lang : Context::getContext()->language->id];
                }
            }
            return $this->{$field_name};
        } else {
            throw new PrestaShopException('Could not load field from definition.');
        }
    }

    /**
     * Set a list of specific fields to update
     * array(field1 => true, field2 => false,
     * langfield1 => array(1 => true, 2 => false))
     *
     * @since 1.5.0.1
     * @param array $fields
     */
    public function setFieldsToUpdate(array $fields)
    {
        $this->update_fields = $fields;
    }

    /**
     * Enables object caching
     */
    public static function enableCache()
    {
        ObjectModel::$cache_objects = true;
    }

    /**
     * Disables object caching
     */
    public static function disableCache()
    {
        ObjectModel::$cache_objects = false;
    }
}
