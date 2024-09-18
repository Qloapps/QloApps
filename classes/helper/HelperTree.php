<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

class HelperTreeCore extends TreeCore
{

    const DEFAULT_NODE_FOLDER_TEMPLATE = 'tree_node_folder_radio.tpl';
    const DEFAULT_NODE_ITEM_TEMPLATE   = 'tree_node_item_radio.tpl';


    private $_use_search;
    private $_use_bulk_actions;
    private $_use_checkbox;
    private $_show_collapse_expand_button = true;
    private $_auto_select_children = false;
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

    public function setAutoSelectChildren($value)
    {
        $this->_auto_select_children = (bool)$value;
        return $this;
    }

    public function autoSelectChildren()
    {
        return (isset($this->_auto_select_children) && $this->_auto_select_children);
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
        $this->setAttribute('auto_select_children', $this->autoSelectChildren());

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