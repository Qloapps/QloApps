<?php
/**
* Since 2010 Webkul.
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
*  @copyright Since 2010 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class HelperTreeCore extends TreeCore
{

    const DEFAULT_NODE_FOLDER_TEMPLATE = 'tree_node_folder_radio.tpl';
    const DEFAULT_NODE_ITEM_TEMPLATE   = 'tree_node_item_radio.tpl';


    private $_use_search;
    private $_use_bulk_actions;
    private $_use_checkbox;
    private $_show_collapse_expand_button = true;
    private $_max_height = 350;


    public function setUseSearch($value)
    {
        $this->_use_search = (bool)$value;
        return $this;
    }

    public function useSearch()
    {
        return (isset($this->_use_search) && $this->_use_search);
    }

    public function setUseBulkActions($value)
    {
        $this->_use_bulk_actions = (bool)$value;
        return $this;
    }

    public function useBulkActions()
    {
        return (isset($this->_use_bulk_actions) && $this->_use_bulk_actions);
    }

    public function setUseCheckBox($value)
    {
        $this->_use_checkbox = (bool)$value;
        return $this;
    }

    public function useCheckBox()
    {
        return (isset($this->_use_checkbox) && $this->_use_checkbox);
    }

    public function setShowCollapseExpandButton($value)
    {
        $this->_show_collapse_expand_button = (bool)$value;
        return $this;
    }

    public function setMaxHeight($value)
    {
        $this->_max_height = (bool)$value;
        return $this;
    }

    public function maxHeight()
    {
        return $this->_max_height;
    }


    public function showCollapseExpandButton()
    {
        return (isset($this->_show_collapse_expand_button) && $this->_show_collapse_expand_button);
    }

    public function render($data = null)
    {
        if ($this->useCheckBox()) {
            if($this->useBulkActions()) {
                $check_all = new TreeToolbarLink(
                    'Check All',
                    '#',
                    'checkAll($(\'#'.$this->getId().'\')); return false;',
                    'icon-check-sign');
                $check_all->setAttribute('id', 'check-all-'.$this->getId());
                $uncheck_all = new TreeToolbarLink(
                    'Uncheck All',
                    '#',
                    'uncheckAll($(\'#'.$this->getId().'\')); return false;',
                    'icon-check-empty');
                $uncheck_all->setAttribute('id', 'uncheck-all-'.$this->getId());
                $this->addAction($check_all);
                $this->addAction($uncheck_all);
            }
            $this->setNodeFolderTemplate('tree_node_folder_checkbox.tpl');
            $this->setNodeItemTemplate('tree_node_item_checkbox.tpl');
            $this->setAttribute('use_checkbox', $this->useCheckBox());
        }

        if ($this->useSearch()) {
            $this->addAction(new TreeToolbarSearch(
                $this->getId().' search',
                $this->getId().'-search')
            );
            $this->setAttribute('use_search', $this->useSearch());
        }

        if ($this->showCollapseExpandButton()) {
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
        }

        $this->setAttribute('max_height', $this->maxHeight());
        $this->setAttribute('root_selectable', $this->rootElementSelectable());


        return parent::render($data);
    }
}