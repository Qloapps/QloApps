<?php
/**
* Copyright since 2010-2020 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright since 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/


class HelperTreeHotelFeaturesCore extends TreeCore
{
    const DEFAULT_TEMPLATE             = 'tree_hotel_feature.tpl';
    const DEFAULT_NODE_FOLDER_TEMPLATE = 'tree_node_hotel_feature_folder_checkbox.tpl';
    const DEFAULT_NODE_ITEM_TEMPLATE   = 'tree_node_hotel_feature_item_checkbox.tpl';

    private $_disabled_categories;
    private $_input_name;
    private $_lang;
    private $_id_hotel;
    private $_shop;
    private $_use_checkbox;
    private $_use_bulk_actions = true;
    private $_use_shop_restriction;
    private $_controller;
    private $_selected_feature = array();

    public function __construct($id, $title = null, $lang = null, $use_shop_restriction = true)
    {
        parent::__construct($id);
        if (isset($title)) {
            $this->setTitle($title);
        }

        $this->setLang($lang);
        $this->setUseShopRestriction($use_shop_restriction);
    }

    private function fillTree()
    {
        $objBranchInfo = new HotelBranchInformation();
        $objHotelFeatures = new HotelFeatures();
        $hotelFeatures = $objBranchInfo->getFeaturesOfHotelByHotelId($this->getHotelId());
        if ($features = $objHotelFeatures->HotelBranchSelectedFeaturesArray($hotelFeatures)) {
            foreach ($features as $idFeature => $feature) {
                $features[$idFeature]['id_feature'] = $idFeature;
                if (isset($feature['children']) && $feature['children']) {
                    foreach ($feature['children'] as $childFeature) {
                        if (isset($childFeature['selected']) && $childFeature['selected']) {
                            $this->_selected_feature[] = $childFeature['id'];
                        }
                    }
                }
            }
        }

        return $features;
    }

    public function getData()
    {
        if (!isset($this->_data)) {
            $this->setData($this->fillTree());
        }

        return $this->_data;
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

    public function setHotelId($idHotel)
    {
        $this->_id_hotel = $idHotel;

        return $this;
    }

    public function getHotelId()
    {
        return $this->_id_hotel;
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


    public function useShopRestriction()
    {
        return (isset($this->_use_shop_restriction) && $this->_use_shop_restriction);
    }

    public function render($data = null)
    {
        if (!isset($data)) {
            $data = $this->getData();
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
        $check_all = new TreeToolbarLink(
            'Check All',
            '#',
            'checkAllAssociatedCategories($(\'#'.$this->getId().'\'));return false;',
            'icon-check-sign');
        $check_all->setAttribute('id', 'check-all-'.$this->getId());
        $uncheck_all = new TreeToolbarLink(
            'Uncheck All',
            '#',
            'uncheckAllAssociatedCategories($(\'#'.$this->getId().'\'));return false;',
            'icon-check-empty');
        $uncheck_all->setAttribute('id', 'uncheck-all-'.$this->getId());
        $this->addAction($collapse_all);
        $this->addAction($expand_all);
        $this->addAction($check_all);
        $this->addAction($uncheck_all);
        $this->setAttribute('use_checkbox', $this->useCheckBox());
        $this->setAttribute('selected_child_features', $this->getSelectedFeatures());
        $this->getContext()->smarty->assign('token', Tools::getAdminTokenLite($this->getSelectedControllerController()));

        return parent::render($data);
    }

    public function getSelectedFeatures()
    {
        return $this->_selected_feature;
    }

    public function setSelectedControllerController($controller)
    {
        $this->_controller = $controller;

        return $this;
    }

    public function getSelectedControllerController()
    {
        return $this->_controller;
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
            if (array_key_exists('children', $item)) {
                $html .= $this->getContext()->smarty->createTemplate(
                    $this->getTemplateFile($this->getNodeFolderTemplate()),
                    $this->getContext()->smarty
                )->assign(array(
                    'input_name' => 'parentHotelFeatureBox',
                    'children' => $this->renderNodes($item['children']),
                    'node'     => $item
                ))->fetch();
            } else {
                $html .= $this->getContext()->smarty->createTemplate(
                    $this->getTemplateFile($this->getNodeItemTemplate()),
                    $this->getContext()->smarty
                )->assign(array(
                    'input_name' => 'childHotelFeatureBox',
                    'node' => $item
                ))->fetch();
            }
        }

        return $html;
    }

}
