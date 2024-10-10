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

class HelperTreeCategoriesCore extends TreeCore
{
    const DEFAULT_TEMPLATE             = 'tree_categories.tpl';
    const DEFAULT_NODE_FOLDER_TEMPLATE = 'tree_node_folder_radio.tpl';
    const DEFAULT_NODE_ITEM_TEMPLATE   = 'tree_node_item_radio.tpl';

    private $_disabled_categories;
    private $_input_name;
    private $_lang;
    private $_root_category;
    private $_selected_categories;
    private $_full_tree = false;
    private $_shop;
    private $_use_checkbox;
    private $_use_bulk_actions = true;
    private $_use_search;
    private $_use_shop_restriction;
    private $_children_only = false;
    private $_disable_all_categories = false;

    public function __construct($id, $title = null, $root_category = null,
        $lang = null, $use_shop_restriction = true)
    {
        parent::__construct($id);

        if (isset($title)) {
            $this->setTitle($title);
        }

        if (isset($root_category)) {
            $this->setRootCategory($root_category);
        }

        $this->setLang($lang);
        $this->setUseShopRestriction($use_shop_restriction);
    }

    private function fillTree(&$categories, $rootCategoryId)
    {
        $tree = array();
        $rootCategoryId = (int)$rootCategoryId;

        foreach ($categories[$rootCategoryId] as $category) {
            $categoryId = (int)$category['id_category'];
            $tree[$categoryId] = $category;

            if (Category::hasChildren($categoryId, $this->getLang(), false, $this->getShop()->id)) {
                $categoryChildren = Category::getChildren(
                    $categoryId,
                    $this->getLang(),
                    false,
                    $this->getShop()->id
                );

                foreach ($categoryChildren as $index => $child) {
                    $childId = (int)$child['id_category'];

                    if (!array_key_exists('children', $tree[$categoryId])) {
                        $tree[$categoryId]['children'] = array($childId => $child);
                    } else {
                        $tree[$categoryId]['children'][$childId] = $child;
                    }

                    $categories[$childId] = array($child);
                }

                foreach ($tree[$categoryId]['children'] as $childId => $child) {
                    $subtree = $this->fillTree($categories, $childId);

                    foreach ($subtree as $subcategoryId => $subcategory) {
                        $tree[$categoryId]['children'][$subcategoryId] = $subcategory;
                    }
                }
            }
        }

        return $tree;
    }

    public function getData()
    {
        if (!isset($this->_data)) {
            $shop = $this->getShop();
            $lang = $this->getLang();
            $root_category = (int)$this->getRootCategory();
            if ($this->_full_tree) {
                $this->setData(Category::getNestedCategories(
                    $root_category, $lang, false, null, $this->useShopRestriction()));
                $this->setDataSearch(Category::getAllCategoriesName($root_category, $lang, false, null, $this->useShopRestriction()));
            } elseif ($this->_children_only) {
                if (empty($root_category)) {
                    $root_category = Category::getRootCategory()->id;
                }
                $categories[$root_category] = Category::getChildren($root_category, $lang, false, $shop->id);
                $children = $this->fillTree($categories, $root_category);
                $this->setData($children);
            } else {
                if (empty($root_category)) {
                    $root_category = Category::getRootCategory()->id;
                }
                $new_selected_categories = array();
                $selected_categories = $this->getSelectedCategories();
                $categories[$root_category] = Category::getChildren($root_category, $lang, false, $shop->id);
                foreach ($selected_categories as $selected_category) {
                    $category = new Category($selected_category, $lang, $shop->id);
                    $new_selected_categories[] = $selected_category;
                    $parents = $category->getParentsCategories($lang);
                    foreach ($parents as $value) {
                        $new_selected_categories[] = $value['id_category'];
                    }
                }
                $new_selected_categories = array_unique($new_selected_categories);
                foreach ($new_selected_categories as $selected_category) {
                    $current_category = Category::getChildren($selected_category, $lang, false, $shop->id);
                    if (!empty($current_category)) {
                        $categories[$selected_category] = $current_category;
                    }
                }

                $tree = Category::getCategoryInformations(array($root_category), $lang);

                $children = $this->fillTree($categories, $root_category);

                if (!empty($children)) {
                    $tree[$root_category]['children'] = $children;
                }

                $this->setData($tree);
                $this->setDataSearch(Category::getAllCategoriesName($root_category, $lang, false, null, $this->useShopRestriction()));
            }
        }

        return $this->_data;
    }

    public function setChildrenOnly($value)
    {
        $this->_children_only = $value;
        return $this;
    }

    public function setFullTree($value)
    {
        $this->_full_tree = $value;
        return $this;
    }

    public function getFullTree()
    {
        return $this->_full_tree;
    }


    public function setDisabledCategories($value)
    {
        $this->_disabled_categories = $value;
        return $this;
    }

    public function setDisablAllCategories($value)
    {
        $this->_disable_all_categories = $value;
        return $this;
    }

    public function getDisabledCategories()
    {
        return $this->_disabled_categories;
    }

    public function getDisablAllCategories()
    {
        return $this->_disable_all_categories;
    }

    public function setInputName($value)
    {
        $this->_input_name = $value;
        return $this;
    }

    public function getInputName()
    {
        if (!isset($this->_input_name)) {
            $this->setInputName('categoryBox');
        }

        return $this->_input_name;
    }

    public function setLang($value)
    {
        $this->_lang = $value;
        return $this;
    }

    public function getLang()
    {
        if (!isset($this->_lang)) {
            $this->setLang($this->getContext()->employee->id_lang);
        }

        return $this->_lang;
    }

    public function getNodeFolderTemplate()
    {
        if (!isset($this->_node_folder_template)) {
            $this->setNodeFolderTemplate(self::DEFAULT_NODE_FOLDER_TEMPLATE);
        }

        return $this->_node_folder_template;
    }

    public function getNodeItemTemplate()
    {
        if (!isset($this->_node_item_template)) {
            $this->setNodeItemTemplate(self::DEFAULT_NODE_ITEM_TEMPLATE);
        }

        return $this->_node_item_template;
    }

    public function setRootCategory($value)
    {
        if (!Validate::isInt($value)) {
            throw new PrestaShopException('Root category must be an integer value');
        }

        $this->_root_category = $value;
        return $this;
    }

    public function getRootCategory()
    {
        return $this->_root_category;
    }

    public function setSelectedCategories($value)
    {
        if (!is_array($value)) {
            throw new PrestaShopException('Selected categories value must be an array');
        }

        $this->_selected_categories = $value;
        return $this;
    }

    public function getSelectedCategories()
    {
        if (!isset($this->_selected_categories)) {
            $this->_selected_categories = array();
        }

        return $this->_selected_categories;
    }

    public function setShop($value)
    {
        $this->_shop = $value;
        return $this;
    }

    public function getShop()
    {
        if (!isset($this->_shop)) {
            if (Tools::isSubmit('id_shop')) {
                $this->setShop(new Shop(Tools::getValue('id_shop')));
            } elseif ($this->getContext()->shop->id) {
                $this->setShop(new Shop($this->getContext()->shop->id));
            } elseif (!Shop::isFeatureActive()) {
                $this->setShop(new Shop(Configuration::get('PS_SHOP_DEFAULT')));
            } else {
                $this->setShop(new Shop(0));
            }
        }

        return $this->_shop;
    }

    public function getTemplate()
    {
        if (!isset($this->_template)) {
            $this->setTemplate(self::DEFAULT_TEMPLATE);
        }

        return $this->_template;
    }

    public function setUseCheckBox($value)
    {
        $this->_use_checkbox = (bool)$value;
        return $this;
    }

    public function setUseBulkActions($value)
    {
        $this->_use_bulk_actions = (bool)$value;
        return $this;
    }

    public function setUseSearch($value)
    {
        $this->_use_search = (bool)$value;
        return $this;
    }

    public function setUseShopRestriction($value)
    {
        $this->_use_shop_restriction = (bool)$value;
        return $this;
    }

    public function useCheckBox()
    {
        return (isset($this->_use_checkbox) && $this->_use_checkbox);
    }

    public function useBulkActions()
    {
        return (isset($this->_use_bulk_actions) && $this->_use_bulk_actions);
    }

    public function useSearch()
    {
        return (isset($this->_use_search) && $this->_use_search);
    }

    public function useShopRestriction()
    {
        return (isset($this->_use_shop_restriction) && $this->_use_shop_restriction);
    }

    public function render($data = null)
    {
        if (!isset($data)) {
            $data = $this->getData();
        }

        if (isset($this->_disabled_categories)
            && !empty($this->_disabled_categories)) {
            $this->_disableCategories($data, $this->getDisabledCategories());
        }

        if($this->getDisablAllCategories()) {
            $this->_disableCategories($data);
        }

        if (isset($this->_selected_categories)
            && !empty($this->_selected_categories)) {
            $this->_getSelectedChildNumbers($data, $this->getSelectedCategories());
        }

        //Default bootstrap style of search is push-right, so we add this button first
        if ($this->useSearch()) {
            $this->addAction(new TreeToolbarSearchCategories(
                'Find a category:',
                $this->getId().'-categories-search')
            );
            $this->setAttribute('use_search', $this->useSearch());
        }

        $collapse_all = new TreeToolbarLink(
            'Collapse All',
            '#',
            '$(\'#'.$this->getId().'\').tree(\'collapseAll\');$(\'#collapse-all-'.$this->getId().'\').hide();$(\'#expand-all-'.$this->getId().'\').show(); return false;',
            'icon-collapse-alt');
        $collapse_all->setAttribute('id', 'collapse-all-'.$this->getId());
        $expand_all = new TreeToolbarLink(
            'Expand All',
            '#',
            '$(\'#'.$this->getId().'\').tree(\'expandAll\');$(\'#collapse-all-'.$this->getId().'\').show();$(\'#expand-all-'.$this->getId().'\').hide(); return false;',
            'icon-expand-alt');
        $expand_all->setAttribute('id', 'expand-all-'.$this->getId());
        $this->addAction($collapse_all);
        $this->addAction($expand_all);

        if ($this->useCheckBox()) {
            if($this->useBulkActions()) {
                $check_all = new TreeToolbarLink(
                    'Check All',
                    '#',
                    'checkAllAssociatedCategories($(\'#'.$this->getId().'\')); return false;',
                    'icon-check-sign');
                $check_all->setAttribute('id', 'check-all-'.$this->getId());
                $uncheck_all = new TreeToolbarLink(
                    'Uncheck All',
                    '#',
                    'uncheckAllAssociatedCategories($(\'#'.$this->getId().'\')); return false;',
                    'icon-check-empty');
                $uncheck_all->setAttribute('id', 'uncheck-all-'.$this->getId());
                $this->addAction($check_all);
                $this->addAction($uncheck_all);
            }
            $this->setNodeFolderTemplate('tree_node_folder_checkbox.tpl');
            $this->setNodeItemTemplate('tree_node_item_checkbox.tpl');
            $this->setAttribute('use_checkbox', $this->useCheckBox());
        }

        $this->setAttribute('selected_categories', $this->getSelectedCategories());
        $this->getContext()->smarty->assign('root_category', Configuration::get('PS_ROOT_CATEGORY'));
        $this->getContext()->smarty->assign('token', Tools::getAdminTokenLite('AdminProducts'));
        return parent::render($data);
    }

    /* Override */
    public function renderNodes($data = null)
    {
        if (!isset($data)) {
            $data = $this->getData();
        }

        if (!is_array($data) && !$data instanceof Traversable) {
            throw new PrestaShopException('Data value must be an traversable array');
        }

        $html = '';
        foreach ($data as $item) {
            if (array_key_exists('children', $item)
                && !empty($item['children'])) {
                $html .= $this->getContext()->smarty->createTemplate(
                    $this->getTemplateFile($this->getNodeFolderTemplate()),
                    $this->getContext()->smarty
                )->assign(array(
                    'input_name' => $this->getInputName(),
                    'children' => $this->renderNodes($item['children']),
                    'node'     => $item
                ))->fetch();
            } else {
                $html .= $this->getContext()->smarty->createTemplate(
                    $this->getTemplateFile($this->getNodeItemTemplate()),
                    $this->getContext()->smarty
                )->assign(array(
                    'input_name' => $this->getInputName(),
                    'node' => $item
                ))->fetch();
            }
        }

        return $html;
    }

    private function _disableCategories(&$categories, $disabled_categories = null)
    {
        foreach ($categories as &$category) {
            if (!isset($disabled_categories) || in_array($category['id_category'], $disabled_categories)) {
                $category['disabled'] = true;
                if (array_key_exists('children', $category) && is_array($category['children'])) {
                    self::_disableCategories($category['children']);
                }
            } elseif (array_key_exists('children', $category) && is_array($category['children'])) {
                self::_disableCategories($category['children'], $disabled_categories);
            }
        }
    }

    private function _getSelectedChildNumbers(&$categories, $selected, &$parent = null)
    {
        $selected_childs = 0;

        foreach ($categories as $key => &$category) {
            if (isset($parent) && in_array($category['id_category'], $selected)) {
                $selected_childs++;
            }

            if (isset($category['children']) && !empty($category['children'])) {
                $selected_childs += $this->_getSelectedChildNumbers($category['children'], $selected, $category);
            }
        }

        if (!isset($parent['selected_childs'])) {
            $parent['selected_childs'] = 0;
        }

        $parent['selected_childs'] = $selected_childs;
        return $selected_childs;
    }
}
