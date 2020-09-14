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

class CategoryCore extends ObjectModel
{
    public $id;

    /** @var int category ID */
    public $id_category;

    /** @var string Name */
    public $name;

    /** @var bool Status for display */
    public $active = 1;

    /** @var  int category position */
    public $position;

    /** @var string Description */
    public $description;

    /** @var int Parent category ID */
    public $id_parent;

    /** @var int default Category id */
    public $id_category_default;

    /** @var int Parents number */
    public $level_depth;

    /** @var int Nested tree model "left" value */
    public $nleft;

    /** @var int Nested tree model "right" value */
    public $nright;

    /** @var string string used in rewrited URL */
    public $link_rewrite;

    /** @var string Meta title */
    public $meta_title;

    /** @var string Meta keywords */
    public $meta_keywords;

    /** @var string Meta description */
    public $meta_description;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /** @var bool is Category Root */
    public $is_root_category;

    /** @var int */
    public $id_shop_default;

    public $groupBox;

    protected static $_links = array();

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'category',
        'primary' => 'id_category',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(
            'nleft' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'nright' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'level_depth' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'active' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'id_parent' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_shop_default' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'is_root_category' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' =>            array('type' => self::TYPE_INT),
            'date_add' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            /* Lang fields */
            'name' =>                array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => true, 'size' => 128),
            'link_rewrite' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'required' => true, 'size' => 128),
            'description' =>        array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'meta_title' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 128),
            'meta_description' =>    array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'meta_keywords' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
        ),
    );

    /** @var string id_image is the category ID when an image exists and 'default' otherwise */
    public $id_image = 'default';

    protected $webserviceParameters = array(
        'objectsNodeName' => 'categories',
        'hidden_fields' => array('nleft', 'nright', 'groupBox'),
        'fields' => array(
            'id_parent' => array('xlink_resource'=> 'categories'),
            'level_depth' => array('setter' => false),
            'nb_products_recursive' => array('getter' => 'getWsNbProductsRecursive', 'setter' => false),
        ),
        'associations' => array(
            'categories' => array('getter' => 'getChildrenWs', 'resource' => 'category', ),
            'products' => array('getter' => 'getProductsWs', 'resource' => 'product', ),
        ),
    );

    public function __construct($id_category = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id_category, $id_lang, $id_shop);

        $link = new Link();
        $imgLink = $link->getMediaLink(_PS_CAT_IMG_DIR_.(int)$this->id.'.jpg'); // by webkul use media link for images
        $this->id_image = ($this->id && (bool)Tools::file_get_contents($imgLink)) ? (int)$this->id : false;
        $this->image_dir = _PS_CAT_IMG_DIR_;
    }

    public static function getDescriptionClean($description)
    {
        return Tools::getDescriptionClean($description);
    }

    public function add($autodate = true, $null_values = false)
    {
        if (!isset($this->level_depth)) {
            $this->level_depth = $this->calcLevelDepth();
        }

        if ($this->is_root_category && ($id_root_category = (int)Configuration::get('PS_ROOT_CATEGORY'))) {
            $this->id_parent = $id_root_category;
        }

        $ret = parent::add($autodate, $null_values);
        if (Tools::isSubmit('checkBoxShopAsso_category')) {
            foreach (Tools::getValue('checkBoxShopAsso_category') as $id_shop => $value) {
                $position = (int)Category::getLastPosition((int)$this->id_parent, $id_shop);
                $this->addPosition($position, $id_shop);
            }
        } else {
            foreach (Shop::getShops(true) as $shop) {
                $position = (int)Category::getLastPosition((int)$this->id_parent, $shop['id_shop']);
                $this->addPosition($position, $shop['id_shop']);
            }
        }
        if (!isset($this->doNotRegenerateNTree) || !$this->doNotRegenerateNTree) {
            Category::regenerateEntireNtree();
        }
        $this->updateGroup($this->groupBox);
        Hook::exec('actionCategoryAdd', array('category' => $this));
        return $ret;
    }

    /**
     * update category positions in parent
     *
     * @param mixed $null_values
     * @return bool
     */
    public function update($null_values = false)
    {
        if ($this->id_parent == $this->id) {
            throw new PrestaShopException('a category cannot be its own parent');
        }

        if ($this->is_root_category && $this->id_parent != (int)Configuration::get('PS_ROOT_CATEGORY')) {
            $this->is_root_category = 0;
        }

        // Update group selection
        $this->updateGroup($this->groupBox);

        if ($this->level_depth != $this->calcLevelDepth()) {
            $this->level_depth = $this->calcLevelDepth();
            $changed = true;
        }

        // If the parent category was changed, we don't want to have 2 categories with the same position
        if (!isset($changed)) {
            $changed = $this->getDuplicatePosition();
        }
        if ($changed) {
            if (Tools::isSubmit('checkBoxShopAsso_category')) {
                foreach (Tools::getValue('checkBoxShopAsso_category') as $id_asso_object => $row) {
                    foreach ($row as $id_shop => $value) {
                        $this->addPosition((int)Category::getLastPosition((int)$this->id_parent, (int)$id_shop), (int)$id_shop);
                    }
                }
            } else {
                foreach (Shop::getShops(true) as $shop) {
                    $this->addPosition((int)Category::getLastPosition((int)$this->id_parent, $shop['id_shop']), $shop['id_shop']);
                }
            }
        }

        $ret = parent::update($null_values);
        if ($changed && (!isset($this->doNotRegenerateNTree) || !$this->doNotRegenerateNTree)) {
            $this->cleanPositions((int)$this->id_parent);
            Category::regenerateEntireNtree();
            $this->recalculateLevelDepth($this->id);
        }
        Hook::exec('actionCategoryUpdate', array('category' => $this));
        return $ret;
    }

    /**
     * @see ObjectModel::toggleStatus()
     */
    public function toggleStatus()
    {
        $result = parent::toggleStatus();
        Hook::exec('actionCategoryUpdate', array('category' => $this));
        return $result;
    }

    /**
     * Recursive scan of subcategories
     *
     * @param int $max_depth Maximum depth of the tree (i.e. 2 => 3 levels depth)
     * @param int $current_depth specify the current depth in the tree (don't use it, only for rucursivity!)
     * @param int $id_lang Specify the id of the language used
     * @param array $excluded_ids_array specify a list of ids to exclude of results
     *
     * @return array Subcategories lite tree
     */
    public function recurseLiteCategTree($max_depth = 3, $current_depth = 0, $id_lang = null, $excluded_ids_array = null)
    {
        $id_lang = is_null($id_lang) ? Context::getContext()->language->id : (int)$id_lang;

        $children = array();
        $subcats = $this->getSubCategories($id_lang, true);
        if (($max_depth == 0 || $current_depth < $max_depth) && $subcats && count($subcats)) {
            foreach ($subcats as &$subcat) {
                if (!$subcat['id_category']) {
                    break;
                } elseif (!is_array($excluded_ids_array) || !in_array($subcat['id_category'], $excluded_ids_array)) {
                    $categ = new Category($subcat['id_category'], $id_lang);
                    $children[] = $categ->recurseLiteCategTree($max_depth, $current_depth + 1, $id_lang, $excluded_ids_array);
                }
            }
        }

        if (is_array($this->description)) {
            foreach ($this->description as $lang => $description) {
                $this->description[$lang] = Category::getDescriptionClean($description);
            }
        } else {
            $this->description = Category::getDescriptionClean($this->description);
        }

        return array(
            'id' => (int)$this->id,
            'link' => Context::getContext()->link->getCategoryLink($this->id, $this->link_rewrite),
            'name' => $this->name,
            'desc'=> $this->description,
            'children' => $children
        );
    }

    public static function recurseCategory($categories, $current, $id_category = null, $id_selected = 1)
    {
        if (!$id_category) {
            $id_category = (int)Configuration::get('PS_ROOT_CATEGORY');
        }

        echo '<option value="'.$id_category.'"'.(($id_selected == $id_category) ? ' selected="selected"' : '').'>'.
        str_repeat('&nbsp;', $current['infos']['level_depth'] * 5).stripslashes($current['infos']['name']).'</option>';
        if (isset($categories[$id_category])) {
            foreach (array_keys($categories[$id_category]) as $key) {
                Category::recurseCategory($categories, $categories[$id_category][$key], $key, $id_selected);
            }
        }
    }


    /**
     * Recursively add specified category childs to $to_delete array
     *
     * @param array &$to_delete Array reference where categories ID will be saved
     * @param int $id_category Parent category ID
     */
    protected function recursiveDelete(&$to_delete, $id_category)
    {
        if (!is_array($to_delete) || !$id_category) {
            die(Tools::displayError());
        }

        $result = Db::getInstance()->executeS('
		SELECT `id_category`
		FROM `'._DB_PREFIX_.'category`
		WHERE `id_parent` = '.(int)$id_category);
        foreach ($result as $row) {
            $to_delete[] = (int)$row['id_category'];
            $this->recursiveDelete($to_delete, (int)$row['id_category']);
        }
    }

    public function deleteLite()
    {
        // Directly call the parent of delete, in order to avoid recursion
        return parent::delete();
    }

    public function delete()
    {
        if ((int)$this->id === 0 || (int)$this->id === (int)Configuration::get('PS_ROOT_CATEGORY')) {
            return false;
        }

        $this->clearCache();

        $deleted_children = $all_cat = $this->getAllChildren();
        $all_cat[] = $this;
        foreach ($all_cat as $cat) {
            /** @var Category $cat */
            $cat->deleteLite();
            if (!$this->hasMultishopEntries()) {
                $cat->deleteImage();
                $cat->cleanGroups();
                $cat->cleanAssoProducts();
                // Delete associated restrictions on cart rules
                CartRule::cleanProductRuleIntegrity('categories', array($cat->id));
                Category::cleanPositions($cat->id_parent);
                /* Delete Categories in GroupReduction */
                if (GroupReduction::getGroupsReductionByCategoryId((int)$cat->id)) {
                    GroupReduction::deleteCategory($cat->id);
                }
            }
        }

        /* Rebuild the nested tree */
        if (!$this->hasMultishopEntries() && (!isset($this->doNotRegenerateNTree) || !$this->doNotRegenerateNTree)) {
            Category::regenerateEntireNtree();
        }

        Hook::exec('actionCategoryDelete', array('category' => $this, 'deleted_children' => $deleted_children));

        return true;
    }

    /**
     * Delete several categories from database
     *
     * return boolean Deletion result
     */
    public function deleteSelection($categories)
    {
        $return = 1;
        foreach ($categories as $id_category) {
            $category = new Category($id_category);
            if ($category->isRootCategoryForAShop()) {
                return false;
            } else {
                $return &= $category->delete();
            }
        }
        return $return;
    }

    /**
     * Get the depth level for the category
     *
     * @return int Depth level
     */
    public function calcLevelDepth()
    {
        /* Root category */
        if (!$this->id_parent) {
            return 0;
        }

        $parent_category = new Category((int)$this->id_parent);
        if (!Validate::isLoadedObject($parent_category)) {
            throw new PrestaShopException('Parent category does not exist');
        }
        return $parent_category->level_depth + 1;
    }

    /**
     * Re-calculate the values of all branches of the nested tree
     */
    public static function regenerateEntireNtree()
    {
        $id = Context::getContext()->shop->id;
        $id_shop = $id ? $id: Configuration::get('PS_SHOP_DEFAULT');
        $categories = Db::getInstance()->executeS('
		SELECT c.`id_category`, c.`id_parent`
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_shop` cs
		ON (c.`id_category` = cs.`id_category` AND cs.`id_shop` = '.(int)$id_shop.')
		ORDER BY c.`id_parent`, cs.`position` ASC');
        $categories_array = array();
        foreach ($categories as $category) {
            $categories_array[$category['id_parent']]['subcategories'][] = $category['id_category'];
        }
        $n = 1;

        if (isset($categories_array[0]) && $categories_array[0]['subcategories']) {
            Category::_subTree($categories_array, $categories_array[0]['subcategories'][0], $n);
        }
    }

    protected static function _subTree(&$categories, $id_category, &$n)
    {
        $left = $n++;
        if (isset($categories[(int)$id_category]['subcategories'])) {
            foreach ($categories[(int)$id_category]['subcategories'] as $id_subcategory) {
                Category::_subTree($categories, (int)$id_subcategory, $n);
            }
        }
        $right = (int)$n++;

        Db::getInstance()->execute('
		UPDATE '._DB_PREFIX_.'category
		SET nleft = '.(int)$left.', nright = '.(int)$right.'
		WHERE id_category = '.(int)$id_category.' LIMIT 1');
    }

    /**
     * Updates level_depth for all children of the given id_category
     *
     * @param int $id_category parent category
     */
    public function recalculateLevelDepth($id_category)
    {
        if (!is_numeric($id_category)) {
            throw new PrestaShopException('id category is not numeric');
        }
        /* Gets all children */
        $categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT id_category, id_parent, level_depth
			FROM '._DB_PREFIX_.'category
			WHERE id_parent = '.(int)$id_category);
        /* Gets level_depth */
        $level = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT level_depth
			FROM '._DB_PREFIX_.'category
			WHERE id_category = '.(int)$id_category);
        /* Updates level_depth for all children */
        foreach ($categories as $sub_category) {
            Db::getInstance()->execute('
				UPDATE '._DB_PREFIX_.'category
				SET level_depth = '.(int)($level['level_depth'] + 1).'
				WHERE id_category = '.(int)$sub_category['id_category']);
            /* Recursive call */
            $this->recalculateLevelDepth($sub_category['id_category']);
        }
    }

    /**
     * Return available categories
     *
     * @param int $id_lang Language ID
     * @param bool $active return only active categories
     * @return array Categories
     */
    public static function getCategories($id_lang = false, $active = true, $order = true, $sql_filter = '', $sql_sort = '', $sql_limit = '')
    {
        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
			SELECT *
			FROM `'._DB_PREFIX_.'category` c
			'.Shop::addSqlAssociation('category', 'c').'
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').'
			WHERE 1 '.$sql_filter.' '.($id_lang ? 'AND `id_lang` = '.(int)$id_lang : '').'
			'.($active ? 'AND `active` = 1' : '').'
			'.(!$id_lang ? 'GROUP BY c.id_category' : '').'
			'.($sql_sort != '' ? $sql_sort : 'ORDER BY c.`level_depth` ASC, category_shop.`position` ASC').'
			'.($sql_limit != '' ? $sql_limit : '')
        );

        if (!$order) {
            return $result;
        }

        $categories = array();
        foreach ($result as $row) {
            $categories[$row['id_parent']][$row['id_category']]['infos'] = $row;
        }

        return $categories;
    }

    public static function getAllCategoriesName(
        $root_category = null,
        $id_lang = false,
        $active = true,
        $groups = null,
        $use_shop_restriction = true,
        $sql_filter = '',
        $sql_sort = '',
        $sql_limit = ''
    ) {
        if (isset($root_category) && !Validate::isInt($root_category)) {
            die(Tools::displayError());
        }

        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }

        if (isset($groups) && Group::isFeatureActive() && !is_array($groups)) {
            $groups = (array)$groups;
        }

        $cache_id = 'Category::getAllCategoriesName_'.md5((int)$root_category.(int)$id_lang.(int)$active.(int)$use_shop_restriction
            .(isset($groups) && Group::isFeatureActive() ? implode('', $groups) : ''));

        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->executeS(
                '
				SELECT c.id_category, cl.name
				FROM `'._DB_PREFIX_.'category` c
				'.($use_shop_restriction ? Shop::addSqlAssociation('category', 'c') : '').'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').'
				'.(isset($groups) && Group::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON c.`id_category` = cg.`id_category`' : '').'
				'.(isset($root_category) ? 'RIGHT JOIN `'._DB_PREFIX_.'category` c2 ON c2.`id_category` = '.(int)$root_category.' AND c.`nleft` >= c2.`nleft` AND c.`nright` <= c2.`nright`' : '').'
				WHERE 1 '.$sql_filter.' '.($id_lang ? 'AND `id_lang` = '.(int)$id_lang : '').'
				'.($active ? ' AND c.`active` = 1' : '').'
				'.(isset($groups) && Group::isFeatureActive() ? ' AND cg.`id_group` IN ('.implode(',', $groups).')' : '').'
				'.(!$id_lang || (isset($groups) && Group::isFeatureActive()) ? ' GROUP BY c.`id_category`' : '').'
				'.($sql_sort != '' ? $sql_sort : ' ORDER BY c.`level_depth` ASC').'
				'.($sql_sort == '' && $use_shop_restriction ? ', category_shop.`position` ASC' : '').'
				'.($sql_limit != '' ? $sql_limit : '')
            );

            Cache::store($cache_id, $result);
        } else {
            $result = Cache::retrieve($cache_id);
        }

        return $result;
    }

    public static function getNestedCategories(
        $root_category = null,
        $id_lang = false,
        $active = true,
        $groups = null,
        $use_shop_restriction = true,
        $sql_filter = '',
        $sql_sort = '',
        $sql_limit = ''
    ) {
        if (isset($root_category) && !Validate::isInt($root_category)) {
            die(Tools::displayError());
        }

        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }

        if (isset($groups) && Group::isFeatureActive() && !is_array($groups)) {
            $groups = (array)$groups;
        }

        $cache_id = 'Category::getNestedCategories_'.md5((int)$root_category.(int)$id_lang.(int)$active.(int)$use_shop_restriction
            .(isset($groups) && Group::isFeatureActive() ? implode('', $groups) : ''));

        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->executeS(
                '
				SELECT c.*, cl.*
				FROM `'._DB_PREFIX_.'category` c
				'.($use_shop_restriction ? Shop::addSqlAssociation('category', 'c') : '').'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').'
				'.(isset($groups) && Group::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON c.`id_category` = cg.`id_category`' : '').'
				'.(isset($root_category) ? 'RIGHT JOIN `'._DB_PREFIX_.'category` c2 ON c2.`id_category` = '.(int)$root_category.' AND c.`nleft` >= c2.`nleft` AND c.`nright` <= c2.`nright`' : '').'
				WHERE 1 '.$sql_filter.' '.($id_lang ? 'AND `id_lang` = '.(int)$id_lang : '').'
				'.($active ? ' AND c.`active` = 1' : '').'
				'.(isset($groups) && Group::isFeatureActive() ? ' AND cg.`id_group` IN ('.implode(',', $groups).')' : '').'
				'.(!$id_lang || (isset($groups) && Group::isFeatureActive()) ? ' GROUP BY c.`id_category`' : '').'
				'.($sql_sort != '' ? $sql_sort : ' ORDER BY c.`level_depth` ASC').'
				'.($sql_sort == '' && $use_shop_restriction ? ', category_shop.`position` ASC' : '').'
				'.($sql_limit != '' ? $sql_limit : '')
            );

            $categories = array();
            $buff = array();

            if (!isset($root_category)) {
                $root_category = Category::getRootCategory()->id;
            }

            foreach ($result as $row) {
                $current = &$buff[$row['id_category']];
                $current = $row;

                if ($row['id_category'] == $root_category) {
                    $categories[$row['id_category']] = &$current;
                } else {
                    $buff[$row['id_parent']]['children'][$row['id_category']] = &$current;
                }
            }

            Cache::store($cache_id, $categories);
        } else {
            $categories = Cache::retrieve($cache_id);
        }

        return $categories;
    }

    public static function getSimpleCategories($id_lang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.`id_category`, cl.`name`
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').')
		'.Shop::addSqlAssociation('category', 'c').'
		WHERE cl.`id_lang` = '.(int)$id_lang.'
		AND c.`id_category` != '.Configuration::get('PS_ROOT_CATEGORY').'
		GROUP BY c.id_category
		ORDER BY c.`id_category`, category_shop.`position`');
    }

    public function getShopID()
    {
        return $this->id_shop;
    }

    /**
     * Return current category childs
     *
     * @param int $id_lang Language ID
     * @param bool $active return only active categories
     * @return array Categories
     */
    public function getSubCategories($id_lang, $active = true)
    {
        $sql_groups_where = '';
        $sql_groups_join = '';
        if (Group::isFeatureActive()) {
            $sql_groups_join = 'LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON (cg.`id_category` = c.`id_category`)';
            $groups = FrontController::getCurrentCustomerGroups();
            $sql_groups_where = 'AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '='.(int)Group::getCurrent()->id);
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.*, cl.id_lang, cl.name, cl.description, cl.link_rewrite, cl.meta_title, cl.meta_keywords, cl.meta_description
		FROM `'._DB_PREFIX_.'category` c
		'.Shop::addSqlAssociation('category', 'c').'
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = '.(int)$id_lang.' '.Shop::addSqlRestrictionOnLang('cl').')
		'.$sql_groups_join.'
		WHERE `id_parent` = '.(int)$this->id.'
		'.($active ? 'AND `active` = 1' : '').'
		'.$sql_groups_where.'
		GROUP BY c.`id_category`
		ORDER BY `level_depth` ASC, category_shop.`position` ASC');

        foreach ($result as &$row) {
            $row['id_image'] = (Tools::file_exists_cache(_PS_CAT_IMG_DIR_.(int)$row['id_category'].'.jpg') || Tools::file_exists_cache(_PS_CAT_IMG_DIR_.(int)$row['id_category'].'_thumb.jpg')) ? (int)$row['id_category'] : Language::getIsoById($id_lang).'-default';
            $row['legend'] = 'no picture';
        }
        return $result;
    }

    /**
     * Returns category products
     *
     * @param int         $id_lang                Language ID
     * @param int         $p                      Page number
     * @param int         $n                      Number of products per page
     * @param string|null $order_by               ORDER BY column
     * @param string|null $order_way              Order way
     * @param bool        $get_total              If set to true, returns the total number of results only
     * @param bool        $active                 If set to true, finds only active products
     * @param bool        $random                 If true, sets a random filter for returned products
     * @param int         $random_number_products Number of products to return if random is activated
     * @param bool        $check_access           If set tot rue, check if the current customer
     *                                            can see products from this category
     * @param Context|null $context
     *
     * @return array|int|false Products, number of products or false (no access)
     * @throws PrestaShopDatabaseException
     */
    public function getProducts($id_lang, $p, $n, $order_by = null, $order_way = null, $get_total = false, $active = true, $random = false, $random_number_products = 1, $check_access = true, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        if ($check_access && !$this->checkAccess($context->customer->id)) {
            return false;
        }

        $front = in_array($context->controller->controller_type, array('front', 'modulefront'));
        $id_supplier = (int)Tools::getValue('id_supplier');

        /** Return only the number of products */
        if ($get_total) {
            $sql = 'SELECT COUNT(cp.`id_product`) AS total
					FROM `'._DB_PREFIX_.'product` p
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`
					WHERE cp.`id_category` = '.(int)$this->id.
                ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
                ($active ? ' AND product_shop.`active` = 1' : '').
                ($id_supplier ? 'AND p.id_supplier = '.(int)$id_supplier : '');

            return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }

        if ($p < 1) {
            $p = 1;
        }

        /** Tools::strtolower is a fix for all modules which are now using lowercase values for 'orderBy' parameter */
        $order_by  = Validate::isOrderBy($order_by)   ? Tools::strtolower($order_by)  : 'position';
        $order_way = Validate::isOrderWay($order_way) ? Tools::strtoupper($order_way) : 'ASC';

        $order_by_prefix = false;
        if ($order_by == 'id_product' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'p';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        } elseif ($order_by == 'manufacturer' || $order_by == 'manufacturer_name') {
            $order_by_prefix = 'm';
            $order_by = 'name';
        } elseif ($order_by == 'position') {
            $order_by_prefix = 'cp';
        }

        if ($order_by == 'price') {
            $order_by = 'orderprice';
        }

        $nb_days_new_product = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        if (!Validate::isUnsignedInt($nb_days_new_product)) {
            $nb_days_new_product = 20;
        }

        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity'.(Combination::isFeatureActive() ? ', IFNULL(product_attribute_shop.id_product_attribute, 0) AS id_product_attribute,
					product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity' : '').', pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, image_shop.`id_image` id_image,
					il.`legend` as legend, m.`name` AS manufacturer_name, cl.`name` AS category_default,
					DATEDIFF(product_shop.`date_add`, DATE_SUB("'.date('Y-m-d').' 00:00:00",
					INTERVAL '.(int)$nb_days_new_product.' DAY)) > 0 AS new, product_shop.price AS orderprice
				FROM `'._DB_PREFIX_.'category_product` cp
				LEFT JOIN `'._DB_PREFIX_.'product` p
					ON p.`id_product` = cp.`id_product`
				'.Shop::addSqlAssociation('product', 'p').
                (Combination::isFeatureActive() ? ' LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
				ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id.')':'').'
				'.Product::sqlStock('p', 0).'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON (product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id.')
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il
					ON (image_shop.`id_image` = il.`id_image`
					AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
					ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE product_shop.`id_shop` = '.(int)$context->shop->id.'
					AND cp.`id_category` = '.(int)$this->id
                    .($active ? ' AND product_shop.`active` = 1' : '')
                    .($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
                    .($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '');

        if ($random === true) {
            $sql .= ' ORDER BY RAND() LIMIT '.(int)$random_number_products;
        } else {
            $sql .= ' ORDER BY '.(!empty($order_by_prefix) ? $order_by_prefix.'.' : '').'`'.bqSQL($order_by).'` '.pSQL($order_way).'
			LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n;
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);

        if (!$result) {
            return array();
        }

        if ($order_by == 'orderprice') {
            Tools::orderbyPrice($result, $order_way);
        }

        /** Modify SQL result */
        return Product::getProductsProperties($id_lang, $result);
    }

    /**
     * Return main categories
     *
     * @param int $id_lang Language ID
     * @param bool $active return only active categories
     * @return array categories
     */
    public static function getHomeCategories($id_lang, $active = true, $id_shop = false)
    {
        return self::getChildren(Configuration::get('PS_HOME_CATEGORY'), $id_lang, $active, $id_shop);
    }

    public static function getRootCategory($id_lang = null, Shop $shop = null)
    {
        $context = Context::getContext();
        if (is_null($id_lang)) {
            $id_lang = $context->language->id;
        }
        if (!$shop) {
            if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
                $shop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));
            } else {
                $shop = $context->shop;
            }
        } else {
            return new Category($shop->getCategory(), $id_lang);
        }
        $is_more_than_one_root_category = count(Category::getCategoriesWithoutParent()) > 1;
        if (Shop::isFeatureActive() && $is_more_than_one_root_category && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $category = Category::getTopCategory($id_lang);
        } else {
            $category = new Category($shop->getCategory(), $id_lang);
        }

        return $category;
    }

    /**
     *
     * @param int  $id_parent
     * @param int  $id_lang
     * @param bool $active
     * @param bool $id_shop
     * @return array
     */
    public static function getChildren($id_parent, $id_lang, $active = true, $id_shop = false)
    {
        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }

        $cache_id = 'Category::getChildren_'.(int)$id_parent.'-'.(int)$id_lang.'-'.(bool)$active.'-'.(int)$id_shop;
        if (!Cache::isStored($cache_id)) {
            $query = 'SELECT c.`id_category`, cl.`name`, cl.`link_rewrite`, category_shop.`id_shop`
			FROM `'._DB_PREFIX_.'category` c
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').')
			'.Shop::addSqlAssociation('category', 'c').'
			WHERE `id_lang` = '.(int)$id_lang.'
			AND c.`id_parent` = '.(int)$id_parent.'
			'.($active ? 'AND `active` = 1' : '').'
			GROUP BY c.`id_category`
			ORDER BY category_shop.`position` ASC';
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }

    /**
     *
     * @param int  $id_parent
     * @param int  $id_lang
     * @param bool $active
     * @param bool $id_shop
     * @return array
     */
    public static function hasChildren($id_parent, $id_lang, $active = true, $id_shop = false)
    {
        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }

        $cache_id = 'Category::hasChildren_'.(int)$id_parent.'-'.(int)$id_lang.'-'.(bool)$active.'-'.(int)$id_shop;
        if (!Cache::isStored($cache_id)) {
            $query = 'SELECT c.id_category, "" as name
			FROM `'._DB_PREFIX_.'category` c
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').')
			'.Shop::addSqlAssociation('category', 'c').'
			WHERE `id_lang` = '.(int)$id_lang.'
			AND c.`id_parent` = '.(int)$id_parent.'
			'.($active ? 'AND `active` = 1' : '').' LIMIT 1';
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * Return an array of all children of the current category
     *
     * @param int $id_lang
     * @return PrestaShopCollection Collection of Category
     */
    public function getAllChildren($id_lang = null)
    {
        if (is_null($id_lang)) {
            $id_lang = Context::getContext()->language->id;
        }

        $categories = new PrestaShopCollection('Category', $id_lang);
        $categories->where('nleft', '>', $this->nleft);
        $categories->where('nright', '<', $this->nright);
        return $categories;
    }

    /**
     * Return an array of all parents of the current category
     *
     * @param int $id_lang
     * @return PrestaShopCollection Collection of Category
     */
    public function getAllParents($id_lang = null)
    {
        if (is_null($id_lang)) {
            $id_lang = Context::getContext()->language->id;
        }

        $categories = new PrestaShopCollection('Category', $id_lang);
        $categories->where('nleft', '<', $this->nleft);
        $categories->where('nright', '>', $this->nright);
        return $categories;
    }

    /**
     * This method allow to return children categories with the number of sub children selected for a product
     *
     * @param int $id_parent
     * @param int $id_product
     * @param int $id_lang
     * @return array
     */
    public static function getChildrenWithNbSelectedSubCat($id_parent, $selected_cat, $id_lang, Shop $shop = null, $use_shop_context = true)
    {
        if (!$shop) {
            $shop = Context::getContext()->shop;
        }

        $id_shop = $shop->id ? $shop->id : Configuration::get('PS_SHOP_DEFAULT');
        $selected_cat = explode(',', str_replace(' ', '', $selected_cat));
        $sql = '
		SELECT c.`id_category`, c.`level_depth`, cl.`name`,
		IF((
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'category` c2
			WHERE c2.`id_parent` = c.`id_category`
		) > 0, 1, 0) AS has_children,
		'.($selected_cat ? '(
			SELECT count(c3.`id_category`)
			FROM `'._DB_PREFIX_.'category` c3
			WHERE c3.`nleft` > c.`nleft`
			AND c3.`nright` < c.`nright`
			AND c3.`id_category`  IN ('.implode(',', array_map('intval', $selected_cat)).')
		)' : '0').' AS nbSelectedSubCat
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` '.Shop::addSqlRestrictionOnLang('cl', $id_shop).')
		LEFT JOIN `'._DB_PREFIX_.'category_shop` cs ON (c.`id_category` = cs.`id_category` AND cs.`id_shop` = '.(int)$id_shop.')
		WHERE `id_lang` = '.(int)$id_lang.'
		AND c.`id_parent` = '.(int)$id_parent;
        if (Shop::getContext() == Shop::CONTEXT_SHOP && $use_shop_context) {
            $sql .= ' AND cs.`id_shop` = '.(int)$shop->id;
        }
        if (!Shop::isFeatureActive() || Shop::getContext() == Shop::CONTEXT_SHOP && $use_shop_context) {
            $sql .= ' ORDER BY cs.`position` ASC';
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    /**
     * Copy products from a category to another
     *
     * @param int $id_old Source category ID
     * @param bool $id_new Destination category ID
     * @return bool Duplication result
     */
    public static function duplicateProductCategories($id_old, $id_new)
    {
        $sql = 'SELECT `id_category`
				FROM `'._DB_PREFIX_.'category_product`
				WHERE `id_product` = '.(int)$id_old;
        $result = Db::getInstance()->executeS($sql);

        $row = array();
        if ($result) {
            foreach ($result as $i) {
                $row[] = '('.implode(', ', array((int)$id_new, $i['id_category'], '(SELECT tmp.max + 1 FROM (
					SELECT MAX(cp.`position`) AS max
					FROM `'._DB_PREFIX_.'category_product` cp
					WHERE cp.`id_category`='.(int)$i['id_category'].') AS tmp)'
                )).')';
            }
        }

        $flag = Db::getInstance()->execute(
            '
			INSERT IGNORE INTO `'._DB_PREFIX_.'category_product` (`id_product`, `id_category`, `position`)
			VALUES '.implode(',', $row)
        );
        return $flag;
    }

    /**
     * Check if category can be moved in another one.
     * The category cannot be moved in a child category.
     *
     * @param int $id_category current category
     * @param int $id_parent Parent candidate
     * @return bool Parent validity
     */
    public static function checkBeforeMove($id_category, $id_parent)
    {
        if ($id_category == $id_parent) {
            return false;
        }
        if ($id_parent == Configuration::get('PS_HOME_CATEGORY')) {
            return true;
        }
        $i = (int)$id_parent;

        while (42) {
            $result = Db::getInstance()->getRow('SELECT `id_parent` FROM `'._DB_PREFIX_.'category` WHERE `id_category` = '.(int)$i);
            if (!isset($result['id_parent'])) {
                return false;
            }
            if ($result['id_parent'] == $id_category) {
                return false;
            }
            if ($result['id_parent'] == Configuration::get('PS_HOME_CATEGORY')) {
                return true;
            }
            $i = $result['id_parent'];
        }
    }

    public static function getLinkRewrite($id_category, $id_lang)
    {
        if (!Validate::isUnsignedId($id_category) || !Validate::isUnsignedId($id_lang)) {
            return false;
        }

        if (!isset(self::$_links[$id_category.'-'.$id_lang])) {
            self::$_links[$id_category.'-'.$id_lang] = Db::getInstance()->getValue('
			SELECT cl.`link_rewrite`
			FROM `'._DB_PREFIX_.'category_lang` cl
			WHERE `id_lang` = '.(int)$id_lang.'
			'.Shop::addSqlRestrictionOnLang('cl').'
			AND cl.`id_category` = '.(int)$id_category);
        }
        return self::$_links[$id_category.'-'.$id_lang];
    }

    public function getLink(Link $link = null, $id_lang = null)
    {
        if (!$link) {
            $link = Context::getContext()->link;
        }

        if (!$id_lang && is_array($this->link_rewrite)) {
            $id_lang = Context::getContext()->language->id;
        }

        return $link->getCategoryLink(
            $this,
            is_array($this->link_rewrite) ? $this->link_rewrite[$id_lang] : $this->link_rewrite,
            $id_lang
        );
    }

    public function getName($id_lang = null)
    {
        if (!$id_lang) {
            if (isset($this->name[Context::getContext()->language->id])) {
                $id_lang = Context::getContext()->language->id;
            } else {
                $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
            }
        }
        return isset($this->name[$id_lang]) ? $this->name[$id_lang] : '';
    }

    /**
     * Light back office search for categories
     *
     * @param int    $id_lang      Language ID
     * @param string $query        Searched string
     * @param bool   $unrestricted allows search without lang and includes first category and exact match
     * @param bool   $skip_cache
     * @return array Corresponding categories
     * @throws PrestaShopDatabaseException
     */
    public static function searchByName($id_lang, $query, $unrestricted = false, $skip_cache = false)
    {
        if ($unrestricted === true) {
            $key = 'Category::searchByName_'.$query;
            if ($skip_cache || !Cache::isStored($key)) {
                $categories = Db::getInstance()->getRow('
				SELECT c.*, cl.*
				FROM `'._DB_PREFIX_.'category` c
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` '.Shop::addSqlRestrictionOnLang('cl').')
				WHERE `name` = \''.pSQL($query).'\'');
                if (!$skip_cache) {
                    Cache::store($key, $categories);
                }
                return $categories;
            }
            return Cache::retrieve($key);
        } else {
            return Db::getInstance()->executeS('
			SELECT c.*, cl.*
			FROM `'._DB_PREFIX_.'category` c
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = '.(int)$id_lang.' '.Shop::addSqlRestrictionOnLang('cl').')
			WHERE `name` LIKE \'%'.pSQL($query).'%\'
			AND c.`id_category` != '.(int)Configuration::get('PS_HOME_CATEGORY'));
        }
    }

    /**
     * Retrieve category by name and parent category id
     *
     * @param int $id_lang Language ID
     * @param string  $category_name Searched category name
     * @param int $id_parent_category parent category ID
     * @return array Corresponding category
     */
    public static function searchByNameAndParentCategoryId($id_lang, $category_name, $id_parent_category)
    {
        return Db::getInstance()->getRow('
		SELECT c.*, cl.*
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
			ON (c.`id_category` = cl.`id_category`
			AND `id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
		WHERE `name` = \''.pSQL($category_name).'\'
			AND c.`id_category` != '.(int)Configuration::get('PS_HOME_CATEGORY').'
			AND c.`id_parent` = '.(int)$id_parent_category);
    }

    /**
     * Search with Pathes for categories
     *
     * @param int $id_lang Language ID
     * @param string $path of category
     * @param bool $object_to_create a category
* 	 * @param bool $method_to_create a category
     * @return array Corresponding categories
     */
    public static function searchByPath($id_lang, $path, $object_to_create = false, $method_to_create = false)
    {
        $categories = explode('/', trim($path));
        $category = $id_parent_category = false;

        if (is_array($categories) && count($categories)) {
            foreach ($categories as $category_name) {
                if ($id_parent_category) {
                    $category = Category::searchByNameAndParentCategoryId($id_lang, $category_name, $id_parent_category);
                } else {
                    $category = Category::searchByName($id_lang, $category_name, true, true);
                }

                if (!$category && $object_to_create && $method_to_create) {
                    call_user_func_array(array($object_to_create, $method_to_create), array($id_lang, $category_name, $id_parent_category));
                    $category = Category::searchByPath($id_lang, $category_name);
                }
                if (isset($category['id_category']) && $category['id_category']) {
                    $id_parent_category = (int)$category['id_category'];
                }
            }
        }
        return $category;
    }

    /**
     * Get Each parent category of this category until the root category
     *
     * @param int $id_lang Language ID
     * @return array Corresponding categories
     */
    public function getParentsCategories($id_lang = null)
    {
        $context = Context::getContext()->cloneContext();
        $context->shop = clone($context->shop);

        if (is_null($id_lang)) {
            $id_lang = $context->language->id;
        }

        $categories = null;
        $id_current = $this->id;
        if (count(Category::getCategoriesWithoutParent()) > 1 && Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && count(Shop::getShops(true, null, true)) != 1) {
            $context->shop->id_category = (int)Configuration::get('PS_ROOT_CATEGORY');
        } elseif (!$context->shop->id) {
            $context->shop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));
        }
        $id_shop = $context->shop->id;
        while (true) {
            $sql = '
			SELECT c.*, cl.*
			FROM `'._DB_PREFIX_.'category` c
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
				ON (c.`id_category` = cl.`id_category`
				AND `id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')';
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP) {
                $sql .= ' LEFT JOIN `'._DB_PREFIX_.'category_shop` cs ON (c.`id_category` = cs.`id_category` AND cs.`id_shop` = '.(int)$id_shop.')';
            }
            $sql .= ' WHERE c.`id_category` = '.(int)$id_current;
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP) {
                $sql .= ' AND cs.`id_shop` = '.(int)$context->shop->id;
            }
            $root_category = Category::getRootCategory();
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP
                && (!Tools::isSubmit('id_category') || (int)Tools::getValue('id_category') == (int)$root_category->id || (int)$root_category->id == (int)$context->shop->id_category)) {
                $sql .= ' AND c.`id_parent` != 0';
            }

            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

            if ($result) {
                $categories[] = $result;
            } elseif (!$categories) {
                $categories = array();
            }
            if (!$result || ($result['id_category'] == $context->shop->id_category)) {
                return $categories;
            }
            $id_current = $result['id_parent'];
        }
    }
    /**
    * Specify if a category already in base
    *
    * @param int $id_category Category id
    * @return bool
    */
    public static function categoryExists($id_category)
    {
        $row = Db::getInstance()->getRow('
		SELECT `id_category`
		FROM '._DB_PREFIX_.'category c
		WHERE c.`id_category` = '.(int)$id_category);

        return isset($row['id_category']);
    }

    public function cleanGroups()
    {
        return Db::getInstance()->delete('category_group', 'id_category = '.(int)$this->id);
    }

    public function cleanAssoProducts()
    {
        return Db::getInstance()->delete('category_product', 'id_category = '.(int)$this->id);
    }

    public function addGroups($groups)
    {
        foreach ($groups as $group) {
            if ($group !== false) {
                Db::getInstance()->insert('category_group', array('id_category' => (int)$this->id, 'id_group' => (int)$group));
            }
        }
    }

    public function getGroups()
    {
        $cache_id = 'Category::getGroups_'.(int)$this->id;
        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->executeS('
			SELECT cg.`id_group`
			FROM '._DB_PREFIX_.'category_group cg
			WHERE cg.`id_category` = '.(int)$this->id);
            $groups = array();
            foreach ($result as $group) {
                $groups[] = $group['id_group'];
            }
            Cache::store($cache_id, $groups);
            return $groups;
        }
        return Cache::retrieve($cache_id);
    }

    public function addGroupsIfNoExist($id_group)
    {
        $groups = $this->getGroups();
        if (!in_array((int)$id_group, $groups)) {
            return $this->addGroups(array((int)$id_group));
        }
        return false;
    }

    /**
     * checkAccess return true if id_customer is in a group allowed to see this category.
     *
     * @param mixed $id_customer
     * @access public
     * @return bool true if access allowed for customer $id_customer
     */
    public function checkAccess($id_customer)
    {
        $cache_id = 'Category::checkAccess_'.(int)$this->id.'-'.$id_customer.(!$id_customer ? '-'.(int)Group::getCurrent()->id : '');
        if (!Cache::isStored($cache_id)) {
            if (!$id_customer) {
                $result = (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT ctg.`id_group`
				FROM '._DB_PREFIX_.'category_group ctg
				WHERE ctg.`id_category` = '.(int)$this->id.' AND ctg.`id_group` = '.(int)Group::getCurrent()->id);
            } else {
                $result = (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT ctg.`id_group`
				FROM '._DB_PREFIX_.'category_group ctg
				INNER JOIN '._DB_PREFIX_.'customer_group cg on (cg.`id_group` = ctg.`id_group` AND cg.`id_customer` = '.(int)$id_customer.')
				WHERE ctg.`id_category` = '.(int)$this->id);
            }
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * Update customer groups associated to the object
     *
     * @param array $list groups
     */
    public function updateGroup($list)
    {
        $this->cleanGroups();
        if (empty($list)) {
            $list = array(Configuration::get('PS_UNIDENTIFIED_GROUP'), Configuration::get('PS_GUEST_GROUP'), Configuration::get('PS_CUSTOMER_GROUP'));
        }
        $this->addGroups($list);
    }

    public static function setNewGroupForHome($id_group)
    {
        if (!(int)$id_group) {
            return false;
        }

        return Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'category_group` (`id_category`, `id_group`)
		VALUES ('.(int)Context::getContext()->shop->getCategory().', '.(int)$id_group.')');
    }

    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS('
            SELECT cp.`id_category`, category_shop.`position`, cp.`id_parent`
            FROM `'._DB_PREFIX_.'category` cp
            '.Shop::addSqlAssociation('category', 'cp').'
            WHERE cp.`id_parent` = '.(int)$this->id_parent.'
            ORDER BY category_shop.`position` ASC')
            ) {
            return false;
        }

        $moved_category = false;
        foreach ($res as $category) {
            if ((int)$category['id_category'] == (int)$this->id) {
                $moved_category = $category;
            }
        }

        if ($moved_category === false) {
            return false;
        }
        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        $result = (Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'category` c '.Shop::addSqlAssociation('category', 'c').'
            SET c.`position`= c.`position` '.($way ? '- 1' : '+ 1').',
            category_shop.`position`= category_shop.`position` '.($way ? '- 1' : '+ 1').',
            c.`date_upd` = "'.date('Y-m-d H:i:s').'"
            WHERE category_shop.`position`
            '.($way
                ? '> '.(int)$moved_category['position'].' AND category_shop.`position` <= '.(int)$position
                : '< '.(int)$moved_category['position'].' AND category_shop.`position` >= '.(int)$position).'
            AND c.`id_parent`='.(int)$moved_category['id_parent'])
        && Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'category` c '.Shop::addSqlAssociation('category', 'c').'
            SET c.`position` = '.(int)$position.',
            category_shop.`position` = '.(int)$position.',
            c.`date_upd` = "'.date('Y-m-d H:i:s').'"
            WHERE c.`id_parent` = '.(int)$moved_category['id_parent'].'
            AND c.`id_category`='.(int)$moved_category['id_category']));
        Hook::exec('actionCategoryUpdate', array('category' => new Category($moved_category['id_category'])));
        return $result;
    }

    /**
     * cleanPositions keep order of category in $id_category_parent,
     * but remove duplicate position. Should not be used if positions
     * are clean at the beginning !
     *
     * @param mixed $id_category_parent
     * @return bool true if succeed
     */
    public static function cleanPositions($id_category_parent = null)
    {
        if ($id_category_parent === null) {
            return;
        }

        $return = true;
        $result = Db::getInstance()->executeS('
        SELECT c.`id_category`
        FROM `'._DB_PREFIX_.'category` c
        '.Shop::addSqlAssociation('category', 'c').'
        WHERE c.`id_parent` = '.(int)$id_category_parent.'
        ORDER BY category_shop.`position`');
        $count = count($result);
        for ($i = 0; $i < $count; $i++) {
            $return &= Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'category` c '.Shop::addSqlAssociation('category', 'c').'
            SET c.`position` = '.(int)($i).',
            category_shop.`position` = '.(int)($i).',
            c.`date_upd` = "'.date('Y-m-d H:i:s').'"
            WHERE c.`id_parent` = '.(int)$id_category_parent.' AND c.`id_category` = '.(int)$result[$i]['id_category']);
        }
        return $return;
    }

    /** this function return the number of category + 1 having $id_category_parent as parent.
     *
     * @todo rename that function to make it understandable (getNewLastPosition for example)
     * @param int $id_category_parent the parent category
     * @param int $id_shop
     * @return int
     */
    public static function getLastPosition($id_category_parent, $id_shop)
    {
        if ((int)Db::getInstance()->getValue('
				SELECT COUNT(c.`id_category`)
				FROM `'._DB_PREFIX_.'category` c
				LEFT JOIN `'._DB_PREFIX_.'category_shop` cs
				ON (c.`id_category` = cs.`id_category` AND cs.`id_shop` = '.(int)$id_shop.')
				WHERE c.`id_parent` = '.(int)$id_category_parent) === 1) {
            return 0;
        } else {
            return (1 + (int)Db::getInstance()->getValue('
				SELECT MAX(cs.`position`)
				FROM `'._DB_PREFIX_.'category` c
				LEFT JOIN `'._DB_PREFIX_.'category_shop` cs
				ON (c.`id_category` = cs.`id_category` AND cs.`id_shop` = '.(int)$id_shop.')
				WHERE c.`id_parent` = '.(int)$id_category_parent));
        }
    }

    public static function getUrlRewriteInformations($id_category)
    {
        return Db::getInstance()->executeS(
            'SELECT l.`id_lang`, c.`link_rewrite`
            FROM `'._DB_PREFIX_.'category_lang` AS c
            LEFT JOIN  `'._DB_PREFIX_.'lang` AS l ON c.`id_lang` = l.`id_lang`
            WHERE c.`id_category` = '.(int)$id_category.'
            AND l.`active` = 1'
        );
    }

    /**
     * Return nleft and nright fields for a given category
     *
     * @since 1.5.0
     * @param int $id
     * @return array
     */
    public static function getInterval($id)
    {
        $cache_id = 'Category::getInterval_'.(int)$id;
        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->getRow('
			SELECT nleft, nright, level_depth
			FROM '._DB_PREFIX_.'category
			WHERE id_category = '.(int)$id);
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * Check if current category is a child of shop root category
     *
     * @since 1.5.0
     * @param Shop $shop
     * @return bool
     */
    public function inShop(Shop $shop = null)
    {
        if (!$shop) {
            $shop = Context::getContext()->shop;
        }

        if (!$interval = Category::getInterval($shop->getCategory())) {
            return false;
        }
        return ($this->nleft >= $interval['nleft'] && $this->nright <= $interval['nright']);
    }

    public static function inShopStatic($id_category, Shop $shop = null)
    {
        if (!$shop || !is_object($shop)) {
            $shop = Context::getContext()->shop;
        }

        if (!$interval = Category::getInterval($shop->getCategory())) {
            return false;
        }
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT nleft, nright FROM `'._DB_PREFIX_.'category` WHERE id_category = '.(int)$id_category);
        return ($row['nleft'] >= $interval['nleft'] && $row['nright'] <= $interval['nright']);
    }

    public function getChildrenWs()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.`id_category` as id
		FROM `'._DB_PREFIX_.'category` c
		'.Shop::addSqlAssociation('category', 'c').'
		WHERE c.`id_parent` = '.(int)$this->id.'
		AND c.`active` = 1
		ORDER BY category_shop.`position` ASC');
        return $result;
    }

    public function getProductsWs()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT cp.`id_product` as id
		FROM `'._DB_PREFIX_.'category_product` cp
		WHERE cp.`id_category` = '.(int)$this->id.'
		ORDER BY `position` ASC');
        return $result;
    }

    /**
     * Search for another category with the same parent and the same position
     *
     * @return array first category found
     */
    public function getDuplicatePosition()
    {
        return Db::getInstance()->getValue('
		SELECT c.`id_category`
		FROM `'._DB_PREFIX_.'category` c
		'.Shop::addSqlAssociation('category', 'c').'
		WHERE c.`id_parent` = '.(int)$this->id_parent.'
		AND category_shop.`position` = '.(int)$this->position.'
		AND c.`id_category` != '.(int)$this->id);
    }

    public function getWsNbProductsRecursive()
    {
        $nb_product_recursive = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT COUNT(distinct(id_product))
			FROM  `'._DB_PREFIX_.'category_product`
			WHERE id_category = '.(int)$this->id.' OR
			EXISTS (
				SELECT 1
				FROM `'._DB_PREFIX_.'category` c2
				'.Shop::addSqlAssociation('category', 'c2').'
				WHERE `'._DB_PREFIX_.'category_product`.id_category = c2.id_category
					AND c2.nleft > '.(int)$this->nleft.'
					AND c2.nright < '.(int)$this->nright.'
					AND c2.active = 1
			)
		');
        if (!$nb_product_recursive) {
            return -1;
        }
        return $nb_product_recursive;
    }

    /**
     *
     * @param Array $ids_category
     * @param int $id_lang
     * @return Array
     */
    public static function getCategoryInformations($ids_category, $id_lang = null)
    {
        if ($id_lang === null) {
            $id_lang = Context::getContext()->language->id;
        }

        if (!is_array($ids_category) || !count($ids_category)) {
            return;
        }

        $categories = array();
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.`id_category`, cl.`name`, cl.`link_rewrite`, cl.`id_lang`
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').')
		'.Shop::addSqlAssociation('category', 'c').'
		WHERE cl.`id_lang` = '.(int)$id_lang.'
		AND c.`id_category` IN ('.implode(',', array_map('intval', $ids_category)).')');

        foreach ($results as $category) {
            $categories[$category['id_category']] = $category;
        }

        return $categories;
    }

    /**
     * @param $id_shop
     * @return bool
     */
    public function isParentCategoryAvailable($id_shop)
    {
        $id = Context::getContext()->shop->id;
        $id_shop = $id ? $id : Configuration::get('PS_SHOP_DEFAULT');
        return (bool)Db::getInstance()->getValue('
		SELECT c.`id_category`
		FROM `'._DB_PREFIX_.'category` c
		'.Shop::addSqlAssociation('category', 'c').'
		WHERE category_shop.`id_shop` = '.(int)$id_shop.'
		AND c.`id_parent` = '.(int)$this->id_parent);
    }

    /**
     * Add association between shop and categories
     * @param int $id_shop
     * @return bool
     */
    public function addShop($id_shop)
    {
        $data = array();
        if (!$id_shop) {
            foreach (Shop::getShops(false) as $shop) {
                if (!$this->existsInShop($shop['id_shop'])) {
                    $data[] = array(
                        'id_category' => (int)$this->id,
                        'id_shop' => (int)$shop['id_shop'],
                    );
                }
            }
        } elseif (!$this->existsInShop($id_shop)) {
            $data[] = array(
                'id_category' => (int)$this->id,
                'id_shop' => (int)$id_shop,
            );
        }

        return Db::getInstance()->insert('category_shop', $data);
    }

    public static function getRootCategories($id_lang = null, $active = true)
    {
        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT DISTINCT(c.`id_category`), cl.`name`
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (cl.`id_category` = c.`id_category` AND cl.`id_lang`='.(int)$id_lang.')
		WHERE `is_root_category` = 1
		'.($active ? 'AND `active` = 1': ''));
    }

    public static function getCategoriesWithoutParent()
    {
        $cache_id = 'Category::getCategoriesWithoutParent_'.(int)Context::getContext()->language->id;
        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT DISTINCT c.*
			FROM `'._DB_PREFIX_.'category` c
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = '.(int)Context::getContext()->language->id.')
			WHERE `level_depth` = 1');
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }

    public function isRootCategoryForAShop()
    {
        return (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT `id_shop`
		FROM `'._DB_PREFIX_.'shop`
		WHERE `id_category` = '.(int)$this->id);
    }

    /**
     * @param null $id_lang
     * @return Category
     */
    public static function getTopCategory($id_lang = null)
    {
        if (is_null($id_lang)) {
            $id_lang = (int)Context::getContext()->language->id;
        }
        $cache_id = 'Category::getTopCategory_'.(int)$id_lang;
        if (!Cache::isStored($cache_id)) {
            $id_category = (int)Db::getInstance()->getValue('
			SELECT `id_category`
			FROM `'._DB_PREFIX_.'category`
			WHERE `id_parent` = 0');
            $category = new Category($id_category, $id_lang);
            Cache::store($cache_id, $category);
            return $category;
        }
        return Cache::retrieve($cache_id);
    }

    public function addPosition($position, $id_shop = null)
    {
        $return = true;
        if (is_null($id_shop)) {
            if (Shop::getContext() != Shop::CONTEXT_SHOP) {
                foreach (Shop::getContextListShopID() as $id_shop) {
                    $return &= Db::getInstance()->execute('
						INSERT INTO `'._DB_PREFIX_.'category_shop` (`id_category`, `id_shop`, `position`) VALUES
						('.(int)$this->id.', '.(int)$id_shop.', '.(int)$position.')
						ON DUPLICATE KEY UPDATE `position` = '.(int)$position);
                }
            } else {
                $id = Context::getContext()->shop->id;
                $id_shop = $id ? $id : Configuration::get('PS_SHOP_DEFAULT');
                $return &= Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'category_shop` (`id_category`, `id_shop`, `position`) VALUES
					('.(int)$this->id.', '.(int)$id_shop.', '.(int)$position.')
					ON DUPLICATE KEY UPDATE `position` = '.(int)$position);
            }
        } else {
            $return &= Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'category_shop` (`id_category`, `id_shop`, `position`) VALUES
			('.(int)$this->id.', '.(int)$id_shop.', '.(int)$position.')
			ON DUPLICATE KEY UPDATE `position` = '.(int)$position);
        }

        return $return;
    }

    public static function getShopsByCategory($id_category)
    {
        return Db::getInstance()->executeS('
		SELECT `id_shop`
		FROM `'._DB_PREFIX_.'category_shop`
		WHERE `id_category` = '.(int)$id_category);
    }

    /**
    * Update categories for a shop
    *
    * @param string $categories Categories list to associate a shop
    * @param string $id_shop Categories list to associate a shop
    * @return array Update/insertion result
    */
    public static function updateFromShop($categories, $id_shop)
    {
        $shop = new Shop($id_shop);
        // if array is empty or if the default category is not selected, return false
        if (!is_array($categories) || !count($categories) || !in_array($shop->id_category, $categories)) {
            return false;
        }

        // delete categories for this shop
        Category::deleteCategoriesFromShop($id_shop);

        // and add $categories to this shop
        return Category::addToShop($categories, $id_shop);
    }

    /**
     * Delete category from shop $id_shop
     * @param int $id_shop
     * @return bool
     */
    public function deleteFromShop($id_shop)
    {
        return Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'category_shop`
		WHERE `id_shop` = '.(int)$id_shop.'
		AND id_category = '.(int)$this->id);
    }

    /**
     * Delete every categories
     * @return bool
     */
    public static function deleteCategoriesFromShop($id_shop)
    {
        return Db::getInstance()->delete('category_shop', 'id_shop = '.(int)$id_shop);
    }

    /**
     * Add some categories to a shop
     * @param array $categories
     * @return bool
     */
    public static function addToShop(array $categories, $id_shop)
    {
        if (!is_array($categories)) {
            return false;
        }
        $sql = 'INSERT INTO `'._DB_PREFIX_.'category_shop` (`id_category`, `id_shop`) VALUES';
        $tab_categories = array();
        foreach ($categories as $id_category) {
            $tab_categories[] = new Category($id_category);
            $sql .= '("'.(int)$id_category.'", "'.(int)$id_shop.'"),';
        }
        // removing last comma to avoid SQL error
        $sql = substr($sql, 0, strlen($sql) - 1);

        $return = Db::getInstance()->execute($sql);
        // we have to update position for every new entries
        foreach ($tab_categories as $category) {
            /** @var Category $category */
            $category->addPosition(Category::getLastPosition($category->id_parent, $id_shop), $id_shop);
        }

        return $return;
    }

    public function existsInShop($id_shop)
    {
        return (bool)Db::getInstance()->getValue('
		SELECT `id_category`
		FROM `'._DB_PREFIX_.'category_shop`
		WHERE `id_category` = '.(int)$this->id.'
		AND `id_shop` = '.(int)$id_shop);
    }
}
