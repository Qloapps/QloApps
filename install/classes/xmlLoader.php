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

class InstallXmlLoader
{
    /**
     * @var InstallLanguages
     */
    protected $language;

    /**
     * @var array List of languages stored as array(id_lang => iso)
     */
    protected $languages = array();

    /**
     * @var array Store in cache all loaded XML files
     */
    protected $cache_xml_entity = array();

    /**
     * @var array List of errors
     */
    protected $errors = array();

    protected $data_path;
    protected $lang_path;
    protected $img_path;
    public $path_type;

    protected $ids = array();

    protected $primaries = array();

    protected $delayed_inserts = array();

    public function __construct()
    {
        $this->language = InstallLanguages::getInstance();
        $this->setDefaultPath();
    }

    /**
     * Set list of installed languages
     *
     * @param array $languages array(id_lang => iso)
     */
    public function setLanguages(array $languages)
    {
        $this->languages = $languages;
    }

    public function setDefaultPath()
    {
        $this->path_type = 'common';
        $this->data_path = _PS_INSTALL_DATA_PATH_.'xml/';
        $this->lang_path = _PS_INSTALL_LANGS_PATH_;
        $this->img_path = _PS_INSTALL_DATA_PATH_.'img/';
    }

    public function setFixturesPath($path = null)
    {
        if ($path === null) {
            $path = _PS_INSTALL_FIXTURES_PATH_.'fashion/';
        }

        $this->path_type = 'fixture';
        $this->data_path = $path.'data/';
        $this->lang_path = $path.'langs/';
        $this->img_path = $path.'img/';
    }

    /**
     * Get list of errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Add an error
     *
     * @param string $error
     */
    public function setError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * Store an ID related to an entity and its identifier (E.g. we want to save that product with ID "ipod_nano" has the ID 1)
     *
     * @param string $entity
     * @param string $identifier
     * @param int $id
     */
    public function storeId($entity, $identifier, $id)
    {
        $this->ids[$entity.':'.$identifier] = $id;
    }

    /**
     * Retrieve an ID related to an entity and its identifier
     *
     * @param string $entity
     * @param string $identifier
     */
    public function retrieveId($entity, $identifier)
    {
        return isset($this->ids[$entity.':'.$identifier]) ? $this->ids[$entity.':'.$identifier] : 0;
    }

    public function getIds()
    {
        return $this->ids;
    }

    public function setIds($ids)
    {
        $this->ids = $ids;
    }

    public function getSortedEntities()
    {
        // Browse all XML files from data/xml directory
        $entities = array();
        $dependencies = array();
        $fd = opendir($this->data_path);
        while ($file = readdir($fd)) {
            if (preg_match('#^(.+)\.xml$#', $file, $m)) {
                $entity = $m[1];
                $xml = $this->loadEntity($entity);

                // Store entities dependencies (with field type="relation")
                if ($xml->fields) {
                    foreach ($xml->fields->field as $field) {
                        if ($field['relation'] && $field['relation'] != $entity) {
                            if (!isset($dependencies[(string)$field['relation']])) {
                                $dependencies[(string)$field['relation']] = array();
                            }
                            $dependencies[(string)$field['relation']][] = $entity;
                        }
                    }
                }
                $entities[] = $entity;
            }
        }
        closedir($fd);

        // Sort entities to populate database in good order (E.g. zones before countries)
        do {
            $current = (isset($sort_entities)) ? $sort_entities : array();
            $sort_entities = array();
            foreach ($entities as $key => $entity) {
                if (isset($dependencies[$entity])) {
                    $min = count($entities) - 1;
                    foreach ($dependencies[$entity] as $item) {
                        if (($key = array_search($item, $sort_entities)) !== false) {
                            $min = min($min, $key);
                        }
                    }
                    if ($min == 0) {
                        array_unshift($sort_entities, $entity);
                    } else {
                        array_splice($sort_entities, $min, 0, array($entity));
                    }
                } else {
                    $sort_entities[] = $entity;
                }
            }
            $entities = $sort_entities;
        } while ($current != $sort_entities);

        return $sort_entities;
    }

    /**
     * Read all XML files from data folder and populate tables
     */
    public function populateFromXmlFiles()
    {
        $entities = $this->getSortedEntities();

        // Populate entities
        foreach ($entities as $entity) {
            $this->populateEntity($entity);
        }
    }

    /**
     * Populate an entity
     *
     * @param string $entity
     */
    public function populateEntity($entity)
    {
        if (method_exists($this, 'populateEntity'.Tools::toCamelCase($entity))) {
            $this->{'populateEntity'.Tools::toCamelCase($entity)}();
            return;
        }

        if (substr($entity, 0, 1) == '.' || substr($entity, 0, 1) == '_') {
            return;
        }

        $xml = $this->loadEntity($entity);

        // Read list of fields
        if (!is_object($xml) || !$xml->fields) {
            throw new PrestashopInstallerException('List of fields not found for entity '.$entity);
        }

        if ($this->isMultilang($entity)) {
            $multilang_columns = $this->getColumns($entity, true);
            $xml_langs = array();
            $default_lang = null;
            foreach ($this->languages as $id_lang => $iso) {
                if ($iso == $this->language->getLanguageIso()) {
                    $default_lang = $id_lang;
                }

                try {
                    $xml_langs[$id_lang] = $this->loadEntity($entity, $iso);
                } catch (PrestashopInstallerException $e) {
                    $xml_langs[$id_lang] = null;
                }
            }
        }

        // Load all row for current entity and prepare data to be populated
        foreach ($xml->entities->$entity as $node) {
            $data = array();
            $identifier = (string)$node['id'];

            // Read attributes
            foreach ($node->attributes() as $k => $v) {
                if ($k != 'id') {
                    $data[$k] = (string)$v;
                }
            }

            // Read cdatas
            foreach ($node->children() as $child) {
                $data[$child->getName()] = (string)$child;
            }

            // Load multilang data
            $data_lang = array();
            if ($this->isMultilang($entity)) {
                $xpath_query = $entity.'[@id="'.$identifier.'"]';
                foreach ($xml_langs as $id_lang => $xml_lang) {
                    if (!$xml_lang) {
                        continue;
                    }

                    if (($node_lang = $xml_lang->xpath($xpath_query)) || ($node_lang = $xml_langs[$default_lang]->xpath($xpath_query))) {
                        $node_lang = $node_lang[0];
                        foreach ($multilang_columns as $column => $is_text) {
                            $value = '';
                            if ($node_lang[$column]) {
                                $value = (string)$node_lang[$column];
                            }

                            if ($node_lang->$column) {
                                $value = (string)$node_lang->$column;
                            }
                            $data_lang[$column][$id_lang] = $value;
                        }
                    }
                }
            }

            $data = $this->rewriteRelationedData($entity, $data);
            if (method_exists($this, 'createEntity'.Tools::toCamelCase($entity))) {
                // Create entity with custom method in current class
                $method = 'createEntity'.Tools::toCamelCase($entity);
                $this->$method($identifier, $data, $data_lang);
            } else {
                $this->createEntity($entity, $identifier, (string)$xml->fields['class'], $data, $data_lang);
            }

            if ($xml->fields['image']) {
                if (method_exists($this, 'copyImages'.Tools::toCamelCase($entity))) {
                    $this->{'copyImages'.Tools::toCamelCase($entity)}($identifier, $data);
                } else {
                    $this->copyImages($entity, $identifier, (string)$xml->fields['image'], $data);
                }
            }
        }

        $this->flushDelayedInserts();
        unset($this->cache_xml_entity[$this->path_type][$entity]);
    }

    protected function getFallBackToDefaultLanguage($iso)
    {
        return file_exists($this->lang_path.$iso.'/data/') ? $iso : 'en';
    }

    /**
     * Special case for "tag" entity
     */
    public function populateEntityTag()
    {
        foreach ($this->languages as $id_lang => $iso) {
            if (!file_exists($this->lang_path.$this->getFallBackToDefaultLanguage($iso).'/data/tag.xml')) {
                continue;
            }

            $xml = $this->loadEntity('tag', $this->getFallBackToDefaultLanguage($iso));
            $tags = array();
            foreach ($xml->tag as $tag_node) {
                $products = trim((string)$tag_node['products']);
                if (!$products) {
                    continue;
                }

                foreach (explode(',', $products) as $product) {
                    $product = trim($product);
                    $product_id = $this->retrieveId('product', $product);
                    if (!isset($tags[$product_id])) {
                        $tags[$product_id] = array();
                    }
                    $tags[$product_id][] = trim((string)$tag_node['name']);
                }
            }

            foreach ($tags as $id_product => $tag_list) {
                Tag::addTags($id_lang, $id_product, $tag_list);
            }
        }
    }

    /**
     * Load an entity XML file
     *
     * @param string $entity
     * @return SimpleXMLElement
     */
    protected function loadEntity($entity, $iso = null)
    {
        if (!isset($this->cache_xml_entity[$this->path_type][$entity][$iso])) {
            if (substr($entity, 0, 1) == '.' || substr($entity, 0, 1) == '_') {
                return;
            }

            $path = $this->data_path.$entity.'.xml';
            if ($iso) {
                $path = $this->lang_path.$this->getFallBackToDefaultLanguage($iso).'/data/'.$entity.'.xml';
            }

            if (!file_exists($path)) {
                throw new PrestashopInstallerException('XML data file '.$entity.'.xml not found');
            }

            $this->cache_xml_entity[$this->path_type][$entity][$iso] = @simplexml_load_file($path, 'InstallSimplexmlElement');
            if (!$this->cache_xml_entity[$this->path_type][$entity][$iso]) {
                throw new PrestashopInstallerException('XML data file '.$entity.'.xml invalid');
            }
        }

        return $this->cache_xml_entity[$this->path_type][$entity][$iso];
    }

    /**
     * Check fields related to an other entity, and replace their values by the ID created by the other entity
     *
     * @param string $entity
     * @param array $data
     */
    protected function rewriteRelationedData($entity, array $data)
    {
        $xml = $this->loadEntity($entity);
        foreach ($xml->fields->field as $field) {
            if ($field['relation']) {
                $id = $this->retrieveId((string)$field['relation'], $data[(string)$field['name']]);
                if (!$id && $data[(string)$field['name']] && is_numeric($data[(string)$field['name']])) {
                    $id = $data[(string)$field['name']];
                }
                $data[(string)$field['name']] = $id;
            }
        }
        return $data;
    }

    public function flushDelayedInserts()
    {
        foreach ($this->delayed_inserts as $entity => $queries) {
            $type = Db::INSERT_IGNORE;
            if ($entity == 'access') {
                $type = Db::REPLACE;
            }

            if (!Db::getInstance()->insert($entity, $queries, false, true, $type)) {
                $this->setError($this->language->l('An SQL error occurred for entity <i>%1$s</i>: <i>%2$s</i>', $entity, Db::getInstance()->getMsgError()));
            }
            unset($this->delayed_inserts[$entity]);
        }
    }

    /**
     * Create a simple entity with all its data and lang data
     * If a methode createEntity$entity exists, use it. Else if $classname is given, use it. Else do a simple insert in database.
     *
     * @param string $entity
     * @param string $identifier
     * @param string $classname
     * @param array $data
     * @param array $data_lang
     */
    public function createEntity($entity, $identifier, $classname, array $data, array $data_lang = array())
    {
        $xml = $this->loadEntity($entity);
        if ($classname) {
            // Create entity with ObjectModel class
            $object = new $classname();
            $object->hydrate($data);
            if ($data_lang) {
                $object->hydrate($data_lang);
            }
            $object->add(true, (isset($xml->fields['null'])) ? true : false);
            $entity_id = $object->id;
            unset($object);
        } else {
            // Generate primary key manually
            $primary = '';
            $entity_id = 0;
            if (!$xml->fields['primary']) {
                $primary = 'id_'.$entity;
            } elseif (strpos((string)$xml->fields['primary'], ',') === false) {
                $primary = (string)$xml->fields['primary'];
            }
            unset($xml);

            if ($primary) {
                $entity_id = $this->generatePrimary($entity, $primary);
                $data[$primary] = $entity_id;
            }

            // Store INSERT queries in order to optimize install with grouped inserts
            $this->delayed_inserts[$entity][] = array_map('pSQL', $data);
            if ($data_lang) {
                $real_data_lang = array();
                foreach ($data_lang as $field => $list) {
                    foreach ($list as $id_lang => $value) {
                        $real_data_lang[$id_lang][$field] = $value;
                    }
                }

                foreach ($real_data_lang as $id_lang => $insert_data_lang) {
                    $insert_data_lang['id_'.$entity] = $entity_id;
                    $insert_data_lang['id_lang'] = $id_lang;
                    $this->delayed_inserts[$entity.'_lang'][] = array_map('pSQL', $insert_data_lang);
                }

                // Store INSERT queries for _shop associations
                $entity_asso = Shop::getAssoTable($entity);
                if ($entity_asso !== false && $entity_asso['type'] == 'shop') {
                    $this->delayed_inserts[$entity.'_shop'][] = array(
                        'id_shop' => 1,
                        'id_'.$entity => $entity_id,
                    );
                }
            }
        }

        $this->storeId($entity, $identifier, $entity_id);
    }

    public function createEntityConfiguration($identifier, array $data, array $data_lang)
    {
        if (Db::getInstance()->getValue('SELECT id_configuration FROM '._DB_PREFIX_.'configuration WHERE name = \''.pSQL($data['name']).'\'')) {
            return;
        }

        $entity = 'configuration';
        $entity_id = $this->generatePrimary($entity, 'id_configuration');
        $data['id_configuration'] = $entity_id;

        // Store INSERT queries in order to optimize install with grouped inserts
        $this->delayed_inserts[$entity][] = array_map('pSQL', $data);
        if ($data_lang) {
            $real_data_lang = array();
            foreach ($data_lang as $field => $list) {
                foreach ($list as $id_lang => $value) {
                    $real_data_lang[$id_lang][$field] = $value;
                }
            }

            foreach ($real_data_lang as $id_lang => $insert_data_lang) {
                $insert_data_lang['id_'.$entity] = $entity_id;
                $insert_data_lang['id_lang'] = $id_lang;
                $this->delayed_inserts[$entity.'_lang'][] = array_map('pSQL', $insert_data_lang);
            }
        }

        $this->storeId($entity, $identifier, $entity_id);
    }
    
    public function createEntityStockAvailable($identifier, array $data, array $data_lang)
    {
        $stock_available = new StockAvailable();
        $stock_available->updateQuantity($data['id_product'], $data['id_product_attribute'], $data['quantity'], $data['id_shop']);
    }

    public function createEntityTab($identifier, array $data, array $data_lang)
    {
        static $position = array();

        $entity = 'tab';
        $xml = $this->loadEntity($entity);

        if (!isset($position[$data['id_parent']])) {
            $position[$data['id_parent']] = 0;
        }
        $data['position'] = $position[$data['id_parent']]++;

        // Generate primary key manually
        $primary = '';
        $entity_id = 0;
        if (!$xml->fields['primary']) {
            $primary = 'id_'.$entity;
        } elseif (strpos((string)$xml->fields['primary'], ',') === false) {
            $primary = (string)$xml->fields['primary'];
        }

        if ($primary) {
            $entity_id = $this->generatePrimary($entity, $primary);
            $data[$primary] = $entity_id;
        }

        // Store INSERT queries in order to optimize install with grouped inserts
        $this->delayed_inserts[$entity][] = array_map('pSQL', $data);
        if ($data_lang) {
            $real_data_lang = array();
            foreach ($data_lang as $field => $list) {
                foreach ($list as $id_lang => $value) {
                    $real_data_lang[$id_lang][$field] = $value;
                }
            }

            foreach ($real_data_lang as $id_lang => $insert_data_lang) {
                $insert_data_lang['id_'.$entity] = $entity_id;
                $insert_data_lang['id_lang'] = $id_lang;
                $this->delayed_inserts[$entity.'_lang'][] = array_map('pSQL', $insert_data_lang);
            }
        }

        $this->storeId($entity, $identifier, $entity_id);
    }

    public function generatePrimary($entity, $primary)
    {
        if (!isset($this->primaries[$entity])) {
            $this->primaries[$entity] = (int)Db::getInstance()->getValue('SELECT '.$primary.' FROM '._DB_PREFIX_.$entity.' ORDER BY '.$primary.' DESC');
        }
        return ++$this->primaries[$entity];
    }

    public function copyImages($entity, $identifier, $path, array $data, $extension = 'jpg')
    {
        // Get list of image types
        $reference = array(
            'product' => 'products',
            'category' => 'categories',
            'manufacturer' => 'manufacturers',
            'supplier' => 'suppliers',
            'scene' => 'scenes',
            'store' => 'stores',
        );

        $types = array();
        if (isset($reference[$entity])) {
            $types = ImageType::getImagesTypes($reference[$entity]);
        }

        // For each path copy images
        $path = array_map('trim', explode(',', $path));
        foreach ($path as $p) {
            $from_path = $this->img_path.$p.'/';
            $dst_path =  _PS_IMG_DIR_.$p.'/';
            $entity_id = $this->retrieveId($entity, $identifier);

            if (!@copy($from_path.$identifier.'.'.$extension, $dst_path.$entity_id.'.'.$extension)) {
                $this->setError($this->language->l('Cannot create image "%1$s" for entity "%2$s"', $identifier, $entity));
                return;
            }

            foreach ($types as $type) {
                $origin_file = $from_path.$identifier.'-'.$type['name'].'.'.$extension;
                $target_file = $dst_path.$entity_id.'-'.$type['name'].'.'.$extension;

                // Test if dest folder is writable
                if (!is_writable(dirname($target_file))) {
                    $this->setError($this->language->l('Cannot create image "%1$s" (bad permissions on folder "%2$s")', $identifier.'-'.$type['name'], dirname($target_file)));
                }
                // If a file named folder/entity-type.extension exists just copy it, this is an optimisation in order to prevent to much resize
                elseif (file_exists($origin_file)) {
                    if (!@copy($origin_file, $target_file)) {
                        $this->setError($this->language->l('Cannot create image "%s"', $identifier.'-'.$type['name']));
                    }
                    @chmod($target_file, 0644);
                }
                // Resize the image if no cache was prepared in fixtures
                elseif (!ImageManager::resize($from_path.$identifier.'.'.$extension, $target_file, $type['width'], $type['height'])) {
                    $this->setError($this->language->l('Cannot create image "%1$s" for entity "%2$s"', $identifier.'-'.$type['name'], $entity));
                }
            }
        }
        Image::moveToNewFileSystem();
    }

    public function copyImagesScene($identifier, array $data)
    {
        $this->copyImages('scene', $identifier, 'scenes', $data);

        $from_path = $this->img_path.'scenes/thumbs/';
        $dst_path =  _PS_IMG_DIR_.'scenes/thumbs/';
        $entity_id = $this->retrieveId('scene', $identifier);

        if (!@copy($from_path.$identifier.'-m_scene_default.jpg', $dst_path.$entity_id.'-m_scene_default.jpg')) {
            $this->setError($this->language->l('Cannot create image "%1$s" for entity "%2$s"', $identifier, 'scene'));
            return;
        }
    }

    public function copyImagesOrderState($identifier, array $data)
    {
        $this->copyImages('order_state', $identifier, 'os', $data, 'gif');
    }

    public function copyImagesTab($identifier, array $data)
    {
        $from_path = $this->img_path.'t/';
        $dst_path =  _PS_IMG_DIR_.'t/';
        if (file_exists($from_path.$data['class_name'].'.gif') && !file_exists($dst_path.$data['class_name'].'.gif')) {
            //test if file exist in install dir and if do not exist in dest folder.
            if (!@copy($from_path.$data['class_name'].'.gif', $dst_path.$data['class_name'].'.gif')) {
                $this->setError($this->language->l('Cannot create image "%1$s" for entity "%2$s"', $identifier, 'tab'));
                return;
            }
        }
    }

    public function copyImagesImage($identifier)
    {
        $path = $this->img_path.'p/';
        $image = new Image($this->retrieveId('image', $identifier));
        $dst_path = $image->getPathForCreation();
        if (!@copy($path.$identifier.'.jpg', $dst_path.'.'.$image->image_format)) {
            $this->setError($this->language->l('Cannot create image "%1$s" for entity "%2$s"', $identifier, 'product'));
            return;
        }
        @chmod($dst_path.'.'.$image->image_format, 0644);

        $types = ImageType::getImagesTypes('products');
        foreach ($types as $type) {
            $origin_file = $path.$identifier.'-'.$type['name'].'.jpg';
            $target_file = $dst_path.'-'.$type['name'].'.'.$image->image_format;

            // Test if dest folder is writable
            if (!is_writable(dirname($target_file))) {
                $this->setError($this->language->l('Cannot create image "%1$s" (bad permissions on folder "%2$s")', $identifier.'-'.$type['name'], dirname($target_file)));
            }
            // If a file named folder/entity-type.jpg exists just copy it, this is an optimisation in order to prevent to much resize
            elseif (file_exists($origin_file)) {
                if (!@copy($origin_file, $target_file)) {
                    $this->setError($this->language->l('Cannot create image "%1$s" for entity "%2$s"', $identifier.'-'.$type['name'], 'product'));
                }
                @chmod($target_file, 0644);
            }
            // Resize the image if no cache was prepared in fixtures
            elseif (!ImageManager::resize($path.$identifier.'.jpg', $target_file, $type['width'], $type['height'])) {
                $this->setError($this->language->l('Cannot create image "%1$s" for entity "%2$s"', $identifier.'-'.$type['name'], 'product'));
            }
        }
    }

    public function getTables()
    {
        static $tables = null;

        if (is_null($tables)) {
            $tables = array();
            foreach (Db::getInstance()->executeS('SHOW TABLES') as $row) {
                $table = current($row);
                if (preg_match('#^'._DB_PREFIX_.'(.+?)(_lang)?$#i', $table, $m)) {
                    $tables[$m[1]] = (isset($m[2]) && $m[2]) ? true : false;
                }
            }
        }

        return $tables;
    }

    public function hasElements($table)
    {
        return (bool)Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.$table);
    }

    public function getColumns($table, $multilang = false, array $exclude = array())
    {
        static $columns = array();

        if ($multilang) {
            return ($this->isMultilang($table)) ? $this->getColumns($table.'_lang', false, array('id_'.$table)) : array();
        }

        if (!isset($columns[$table])) {
            $columns[$table] = array();
            $sql = 'SHOW COLUMNS FROM `'._DB_PREFIX_.bqSQL($table).'`';
            foreach (Db::getInstance()->executeS($sql) as $row) {
                $columns[$table][$row['Field']] = $this->checkIfTypeIsText($row['Type']);
            }
        }

        $exclude = array_merge(array('id_'.$table, 'date_add', 'date_upd', 'deleted', 'id_lang'), $exclude);

        $list = array();
        foreach ($columns[$table] as $k => $v) {
            if (!in_array($k, $exclude)) {
                $list[$k] = $v;
            }
        }

        return $list;
    }

    public function getClasses($path = null)
    {
        static $cache = null;

        if (!is_null($cache)) {
            return $cache;
        }

        $dir = $path;
        if (is_null($dir)) {
            $dir = _PS_CLASS_DIR_;
        }

        $classes = array();
        foreach (scandir($dir) as $file) {
            if ($file[0] != '.' && $file != 'index.php') {
                if (is_dir($dir.$file)) {
                    $classes = array_merge($classes, $this->getClasses($dir.$file.'/'));
                } elseif (preg_match('#^(.+)\.php$#', $file, $m)) {
                    $classes[] = $m[1];
                }
            }
        }

        sort($classes);
        if (is_null($path)) {
            $cache = $classes;
        }
        return $classes;
    }

    public function checkIfTypeIsText($type)
    {
        if (preg_match('#^(longtext|text|tinytext)#i', $type)) {
            return true;
        }

        if (preg_match('#^varchar\(([0-9]+)\)$#i', $type, $m)) {
            return intval($m[1]) >= 64 ? true : false;
        }
        return false;
    }

    public function isMultilang($entity)
    {
        $tables = $this->getTables();
        return isset($tables[$entity]) && $tables[$entity];
    }

    public function entityExists($entity)
    {
        return file_exists($this->data_path.$entity.'.xml');
    }

    public function getEntitiesList()
    {
        $entities = array();
        foreach (scandir($this->data_path) as $file) {
            if ($file[0] != '.' && preg_match('#^(.+)\.xml$#', $file, $m)) {
                $entities[] = $m[1];
            }
        }
        return $entities;
    }

    public function getEntityInfo($entity)
    {
        $info = array(
            'config' => array(
                'id' =>        '',
                'primary' =>    '',
                'class' =>        '',
                'sql' =>        '',
                'ordersql' =>    '',
                'image' =>        '',
                'null' =>        '',
            ),
            'fields' =>    array(),
        );

        if (!$this->entityExists($entity)) {
            return $info;
        }

        $xml = @simplexml_load_file($this->data_path.$entity.'.xml', 'InstallSimplexmlElement');
        if (!$xml) {
            return $info;
        }

        if ($xml->fields['id']) {
            $info['config']['id'] = (string)$xml->fields['id'];
        }

        if ($xml->fields['primary']) {
            $info['config']['primary'] = (string)$xml->fields['primary'];
        }

        if ($xml->fields['class']) {
            $info['config']['class'] = (string)$xml->fields['class'];
        }

        if ($xml->fields['sql']) {
            $info['config']['sql'] = (string)$xml->fields['sql'];
        }

        if ($xml->fields['ordersql']) {
            $info['config']['ordersql'] = (string)$xml->fields['ordersql'];
        }

        if ($xml->fields['null']) {
            $info['config']['null'] = (string)$xml->fields['null'];
        }

        if ($xml->fields['image']) {
            $info['config']['image'] = (string)$xml->fields['image'];
        }

        foreach ($xml->fields->field as $field) {
            $column = (string)$field['name'];
            $info['fields'][$column] = array();
            if (isset($field['relation'])) {
                $info['fields'][$column]['relation'] = (string)$field['relation'];
            }
        }
        return $info;
    }

    public function getDependencies()
    {
        $entities = array();
        foreach ($this->getEntitiesList() as $entity) {
            $entities[$entity] = $this->getEntityInfo($entity);
        }

        $dependencies = array();
        foreach ($entities as $entity => $info) {
            foreach ($info['fields'] as $field => $info_field) {
                if (isset($info_field['relation']) && $info_field['relation'] != $entity) {
                    if (!isset($dependencies[$info_field['relation']])) {
                        $dependencies[$info_field['relation']] = array();
                    }
                    $dependencies[$info_field['relation']][] = $entity;
                }
            }
        }

        return $dependencies;
    }

    public function generateEntitySchema($entity, array $fields, array $config)
    {
        if ($this->entityExists($entity)) {
            $xml = $this->loadEntity($entity);
        } else {
            $xml = new InstallSimplexmlElement('<entity_'.$entity.' />');
        }
        unset($xml->fields);

        // Fill <fields> attributes (config)
        $xml_fields = $xml->addChild('fields');
        foreach ($config as $k => $v) {
            if ($v) {
                $xml_fields[$k] = $v;
            }
        }

        // Create list of fields
        foreach ($fields as $column => $info) {
            $field = $xml_fields->addChild('field');
            $field['name'] = $column;
            if (isset($info['relation'])) {
                $field['relation'] = $info['relation'];
            }
        }

        // Recreate entities nodes, in order to have the <entities> node after the <fields> node
        $store_entities = clone $xml->entities;
        unset($xml->entities);
        $xml->addChild('entities', $store_entities);

        $xml->asXML($this->data_path.$entity.'.xml');
    }

    /**
     * ONLY FOR DEVELOPMENT PURPOSE
     */
    public function generateAllEntityFiles()
    {
        $entities = array();
        foreach ($this->getEntitiesList() as $entity) {
            $entities[$entity] = $this->getEntityInfo($entity);
        }
        $this->generateEntityFiles($entities);
    }

    /**
     * ONLY FOR DEVELOPMENT PURPOSE
     */
    public function generateEntityFiles($entities)
    {
        $dependencies = $this->getDependencies();

        // Sort entities to populate database in good order (E.g. zones before countries)
        do {
            $current = (isset($sort_entities)) ? $sort_entities : array();
            $sort_entities = array();
            foreach ($entities as $entity) {
                if (isset($dependencies[$entity])) {
                    $min = count($entities) - 1;
                    foreach ($dependencies[$entity] as $item) {
                        if (($key = array_search($item, $sort_entities)) !== false) {
                            $min = min($min, $key);
                        }
                    }
                    if ($min == 0) {
                        array_unshift($sort_entities, $entity);
                    } else {
                        array_splice($sort_entities, $min, 0, array($entity));
                    }
                } else {
                    $sort_entities[] = $entity;
                }
            }
            $entities = $sort_entities;
        } while ($current != $sort_entities);

        foreach ($sort_entities as $entity) {
            $this->generateEntityContent($entity);
        }
    }

    public function generateEntityContent($entity)
    {
        $xml = $this->loadEntity($entity);
        if (method_exists($this, 'getEntityContents'.Tools::toCamelCase($entity))) {
            $content = $this->{'getEntityContents'.Tools::toCamelCase($entity)}($entity);
        } else {
            $content = $this->getEntityContents($entity);
        }

        unset($xml->entities);
        $entities = $xml->addChild('entities');
        $this->createXmlEntityNodes($entity, $content['nodes'], $entities);
        $xml->asXML($this->data_path.$entity.'.xml');

        // Generate multilang XML files
        if ($content['nodes_lang']) {
            foreach ($content['nodes_lang'] as $id_lang => $nodes) {
                if (!isset($this->languages[$id_lang])) {
                    continue;
                }

                $iso = $this->languages[$id_lang];
                if (!is_dir($this->lang_path.$this->getFallBackToDefaultLanguage($iso).'/data')) {
                    mkdir($this->lang_path.$this->getFallBackToDefaultLanguage($iso).'/data');
                }

                $xml_node = new InstallSimplexmlElement('<entity_'.$entity.' />');
                $this->createXmlEntityNodes($entity, $nodes, $xml_node);
                $xml_node->asXML($this->lang_path.$this->getFallBackToDefaultLanguage($iso).'/data/'.$entity.'.xml');
            }
        }

        if ($xml->fields['image']) {
            if (method_exists($this, 'backupImage'.Tools::toCamelCase($entity))) {
                $this->{'backupImage'.Tools::toCamelCase($entity)}((string)$xml->fields['image']);
            } else {
                $this->backupImage($entity, (string)$xml->fields['image']);
            }
        }
    }

    /**
     * ONLY FOR DEVELOPMENT PURPOSE
     */
    public function getEntityContents($entity)
    {
        $xml = $this->loadEntity($entity);
        $primary = (isset($xml->fields['primary']) && $xml->fields['primary']) ? (string)$xml->fields['primary'] : 'id_'.$entity;
        $is_multilang = $this->isMultilang($entity);

        // Check if current table is an association table (if multiple primary keys)
        $is_association = false;
        if (strpos($primary, ',') !== false) {
            $is_association = true;
            $primary = array_map('trim', explode(',', $primary));
        }

        // Build query
        $sql = new DbQuery();
        $sql->select('a.*');
        $sql->from($entity, 'a');
        if ($is_multilang) {
            $sql->select('b.*');
            $sql->leftJoin($entity.'_lang', 'b', 'a.'.$primary.' = b.'.$primary);
        }

        if (isset($xml->fields['sql']) && $xml->fields['sql']) {
            $sql->where((string)$xml->fields['sql']);
        }

        if (!$is_association) {
            $sql->select('a.'.$primary);
            if (!isset($xml->fields['ordersql']) || !$xml->fields['ordersql']) {
                $sql->orderBy('a.'.$primary);
            }
        }

        if ($is_multilang && (!isset($xml->fields['ordersql']) || !$xml->fields['ordersql'])) {
            $sql->orderBy('b.id_lang');
        }

        if (isset($xml->fields['ordersql']) && $xml->fields['ordersql']) {
            $sql->orderBy((string)$xml->fields['ordersql']);
        }

        // Get multilang columns
        $alias_multilang = array();
        if ($is_multilang) {
            $columns = $this->getColumns($entity);
            $multilang_columns = $this->getColumns($entity, true);

            // If some columns from _lang table have same name than original table, rename them (E.g. value in configuration)
            foreach ($multilang_columns as $c => $is_text) {
                if (isset($columns[$c])) {
                    $alias = $c.'_alias';
                    $alias_multilang[$c] = $alias;
                    $sql->select('a.'.$c.' as '.$c.', b.'.$c.' as '.$alias);
                }
            }
        }

        // Get all results
        $nodes = $nodes_lang = array();
        $results = Db::getInstance()->executeS($sql);
        if (Db::getInstance()->getNumberError()) {
            $this->setError($this->language->l('SQL error on query <i>%s</i>', $sql));
        } else {
            foreach ($results as $row) {
                // Store common columns
                if ($is_association) {
                    $id = $entity;
                    foreach ($primary as $key) {
                        $id .= '_'.$row[$key];
                    }
                } else {
                    $id = $this->generateId($entity, $row[$primary], $row, (isset($xml->fields['id']) && $xml->fields['id']) ? (string)$xml->fields['id'] : null);
                }

                if (!isset($nodes[$id])) {
                    $node = array();
                    foreach ($xml->fields->field as $field) {
                        $column = (string)$field['name'];
                        if (isset($field['relation'])) {
                            $sql = 'SELECT `id_'.bqSQL($field['relation']).'`
									FROM `'.bqSQL(_DB_PREFIX_.$field['relation']).'`
									WHERE `id_'.bqSQL($field['relation']).'` = '.(int)$row[$column];
                            $node[$column] = $this->generateId((string)$field['relation'], Db::getInstance()->getValue($sql));

                            // A little trick to allow storage of some hard values, like '-1' for tab.id_parent
                            if (!$node[$column] && $row[$column]) {
                                $node[$column] = $row[$column];
                            }
                        } else {
                            $node[$column] = $row[$column];
                        }
                    }
                    $nodes[$id] = $node;
                }

                // Store multilang columns
                if ($is_multilang && $row['id_lang']) {
                    $node = array();
                    foreach ($multilang_columns as $column => $is_text) {
                        $node[$column] = $row[isset($alias_multilang[$column]) ? $alias_multilang[$column] : $column];
                    }
                    $nodes_lang[$row['id_lang']][$id] = $node;
                }
            }
        }

        return array(
            'nodes' =>        $nodes,
            'nodes_lang' =>    $nodes_lang,
        );
    }

    public function getEntityContentsTag()
    {
        $nodes_lang = array();

        $sql = 'SELECT t.id_tag, t.id_lang, t.name, pt.id_product
				FROM '._DB_PREFIX_.'tag t
				LEFT JOIN '._DB_PREFIX_.'product_tag pt ON t.id_tag = pt.id_tag
				ORDER BY id_lang';
        foreach (Db::getInstance()->executeS($sql) as $row) {
            $identifier = $this->generateId('tag', $row['id_tag']);
            if (!isset($nodes_lang[$row['id_lang']])) {
                $nodes_lang[$row['id_lang']] = array();
            }

            if (!isset($nodes_lang[$row['id_lang']][$identifier])) {
                $nodes_lang[$row['id_lang']][$identifier] = array(
                    'name' =>        $row['name'],
                    'products' =>    '',
                );
            }

            $nodes_lang[$row['id_lang']][$identifier]['products'] .= (($nodes_lang[$row['id_lang']][$identifier]['products']) ? ',' : '').$this->generateId('product', $row['id_product']);
        }

        return array(
            'nodes' =>        array(),
            'nodes_lang' => $nodes_lang,
        );
    }

    /**
     * ONLY FOR DEVELOPMENT PURPOSE
     */
    public function generateId($entity, $primary, array $row = array(), $id_format = null)
    {
        static $ids = array();

        if (isset($ids[$entity][$primary])) {
            return $ids[$entity][$primary];
        }

        if (!isset($ids[$entity])) {
            $ids[$entity] = array();
        }

        if (!$primary) {
            return '';
        }

        if (!$id_format || !$row || !$row[$id_format]) {
            $ids[$entity][$primary] = $entity.'_'.$primary;
        } else {
            $value = $row[$id_format];
            $value = preg_replace('#[^a-z0-9_-]#i', '_', $value);
            $value = preg_replace('#_+#', '_', $value);
            $value = preg_replace('#^_+#', '', $value);
            $value = preg_replace('#_+$#', '', $value);

            $store_identifier = $value;
            $i = 1;
            while (in_array($store_identifier, $ids[$entity])) {
                $store_identifier = $value.'_'.$i++;
            }
            $ids[$entity][$primary] = $store_identifier;
        }
        return $ids[$entity][$primary];
    }

    /**
     * ONLY FOR DEVELOPMENT PURPOSE
     */
    public function createXmlEntityNodes($entity, array $nodes, SimpleXMLElement $entities)
    {
        $types = array_merge($this->getColumns($entity), $this->getColumns($entity, true));
        foreach ($nodes as $id => $node) {
            $entity_node = $entities->addChild($entity);
            $entity_node['id'] = $id;
            foreach ($node as $k => $v) {
                if (isset($types[$k]) && $types[$k]) {
                    $entity_node->addChild($k, $v);
                } else {
                    $entity_node[$k] = $v;
                }
            }
        }
    }

    /**
     * ONLY FOR DEVELOPMENT PURPOSE
     */
    public function backupImage($entity, $path)
    {
        $reference = array(
            'product' => 'products',
            'category' => 'categories',
            'manufacturer' => 'manufacturers',
            'supplier' => 'suppliers',
            'scene' => 'scenes',
            'store' => 'stores',
        );

        $types = array();
        if (isset($reference[$entity])) {
            $types = array();
            foreach (ImageType::getImagesTypes($reference[$entity]) as $type) {
                $types[] = $type['name'];
            }
        }

        $path_list = array_map('trim', explode(',', $path));
        foreach ($path_list as $p) {
            $backup_path = $this->img_path.$p.'/';
            $from_path = _PS_IMG_DIR_.$p.'/';

            if (!is_dir($backup_path) && !mkdir($backup_path)) {
                $this->setError(sprintf('Cannot create directory <i>%s</i>', $backup_path));
            }

            foreach (scandir($from_path) as $file) {
                if ($file[0] != '.' && preg_match('#^(([0-9]+)(-('.implode('|', $types).'))?)\.(gif|jpg|jpeg|png)$#i', $file, $m)) {
                    $file_id = $m[2];
                    $file_type = $m[3];
                    $file_extension = $m[5];
                    copy($from_path.$file, $backup_path.$this->generateId($entity, $file_id).$file_type.'.'.$file_extension);
                }
            }
        }
    }

    /**
     * ONLY FOR DEVELOPMENT PURPOSE
     */
    public function backupImageImage()
    {
        $types = array();
        foreach (ImageType::getImagesTypes('products') as $type) {
            $types[] = $type['name'];
        }

        $backup_path = $this->img_path.'p/';
        $from_path = _PS_PROD_IMG_DIR_;
        if (!is_dir($backup_path) && !mkdir($backup_path)) {
            $this->setError(sprintf('Cannot create directory <i>%s</i>', $backup_path));
        }

        foreach (Image::getAllImages() as $image) {
            $image = new Image($image['id_image']);
            $image_path = $image->getExistingImgPath();
            if (file_exists($from_path.$image_path.'.'.$image->image_format)) {
                copy($from_path.$image_path.'.'.$image->image_format, $backup_path.$this->generateId('image', $image->id).'.'.$image->image_format);
            }

            foreach ($types as $type) {
                if (file_exists($from_path.$image_path.'-'.$type.'.'.$image->image_format)) {
                    copy($from_path.$image_path.'-'.$type.'.'.$image->image_format, $backup_path.$this->generateId('image', $image->id).'-'.$type.'.'.$image->image_format);
                }
            }
        }
    }

    /**
     * ONLY FOR DEVELOPMENT PURPOSE
     */
    public function backupImageTab()
    {
        $backup_path = $this->img_path.'t/';
        $from_path = _PS_IMG_DIR_.'t/';
        if (!is_dir($backup_path) && !mkdir($backup_path)) {
            $this->setError(sprintf('Cannot create directory <i>%s</i>', $backup_path));
        }

        $xml = $this->loadEntity('tab');
        foreach ($xml->entities->tab as $tab) {
            if (file_exists($from_path.$tab->class_name.'.gif')) {
                copy($from_path.$tab->class_name.'.gif', $backup_path.$tab->class_name.'.gif');
            }
        }
    }
}
