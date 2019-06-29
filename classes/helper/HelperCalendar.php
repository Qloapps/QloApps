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

class HelperCalendarCore extends Helper
{
    const DEFAULT_DATE_FORMAT    = 'Y-mm-dd';
    const DEFAULT_COMPARE_OPTION = 1;

    private $_actions;
    private $_compare_actions;
    private $_compare_date_from;
    private $_compare_date_to;
    private $_compare_date_option;
    private $_date_format;
    private $_date_from;
    private $_date_to;
    private $_rtl;

    public function __construct()
    {
        $this->base_folder = 'helpers/calendar/';
        $this->base_tpl = 'calendar.tpl';
        parent::__construct();
    }

    public function setActions($value)
    {
        if (!is_array($value) && !$value instanceof Traversable) {
            throw new PrestaShopException('Actions value must be an traversable array');
        }

        $this->_actions = $value;
        return $this;
    }

    public function getActions()
    {
        if (!isset($this->_actions)) {
            $this->_actions = array();
        }

        return $this->_actions;
    }

    public function setCompareActions($value)
    {
        if (!is_array($value) && !$value instanceof Traversable) {
            throw new PrestaShopException('Actions value must be an traversable array');
        }

        $this->_compare_actions = $value;
        return $this;
    }

    public function getCompareActions()
    {
        if (!isset($this->_compare_actions)) {
            $this->_compare_actions = array();
        }

        return $this->_compare_actions;
    }

    public function setCompareDateFrom($value)
    {
        $this->_compare_date_from = $value;
        return $this;
    }

    public function getCompareDateFrom()
    {
        return $this->_compare_date_from;
    }

    public function setCompareDateTo($value)
    {
        $this->_compare_date_to = $value;
        return $this;
    }

    public function getCompareDateTo()
    {
        return $this->_compare_date_to;
    }

    public function setCompareOption($value)
    {
        $this->_compare_date_option = (int)$value;
        return $this;
    }

    public function getCompareOption()
    {
        if (!isset($this->_compare_date_option)) {
            $this->_compare_date_option = self::DEFAULT_COMPARE_OPTION;
        }

        return $this->_compare_date_option;
    }

    public function setDateFormat($value)
    {
        if (!is_string($value)) {
            throw new PrestaShopException('Date format must be a string');
        }

        $this->_date_format = $value;
        return $this;
    }

    public function getDateFormat()
    {
        if (!isset($this->_date_format)) {
            $this->_date_format = self::DEFAULT_DATE_FORMAT;
        }

        return $this->_date_format;
    }

    public function setDateFrom($value)
    {
        if (!isset($value) || $value == '') {
            $value = date('Y-m-d', strtotime('-31 days'));
        }

        if (!is_string($value)) {
            throw new PrestaShopException('Date must be a string');
        }

        $this->_date_from = $value;
        return $this;
    }

    public function getDateFrom()
    {
        if (!isset($this->_date_from)) {
            $this->_date_from = date('Y-m-d', strtotime('-31 days'));
        }

        return $this->_date_from;
    }

    public function setDateTo($value)
    {
        if (!isset($value) || $value == '') {
            $value = date('Y-m-d');
        }

        if (!is_string($value)) {
            throw new PrestaShopException('Date must be a string');
        }

        $this->_date_to = $value;
        return $this;
    }

    public function getDateTo()
    {
        if (!isset($this->_date_to)) {
            $this->_date_to = date('Y-m-d');
        }

        return $this->_date_to;
    }

    public function setRTL($value)
    {
        $this->_rtl = (bool)$value;
        return $this;
    }

    public function addAction($action)
    {
        if (!isset($this->_actions)) {
            $this->_actions = array();
        }

        $this->_actions[] = $action;

        return $this;
    }

    public function addCompareAction($action)
    {
        if (!isset($this->_compare_actions)) {
            $this->_compare_actions = array();
        }

        $this->_compare_actions[] = $action;

        return $this;
    }

    public function generate()
    {
        $context = Context::getContext();
        $admin_webpath = str_ireplace(_PS_CORE_DIR_, '', _PS_ADMIN_DIR_);
        $admin_webpath = preg_replace('/^'.preg_quote(DIRECTORY_SEPARATOR, '/').'/', '', $admin_webpath);
        $bo_theme = ((Validate::isLoadedObject($context->employee)
            && $context->employee->bo_theme) ? $context->employee->bo_theme : 'default');

        if (!file_exists(_PS_BO_ALL_THEMES_DIR_.$bo_theme.DIRECTORY_SEPARATOR
            .'template')) {
            $bo_theme = 'default';
        }

        if ($context->controller->ajax) {
            $html = '<script type="text/javascript" src="'.__PS_BASE_URI__.$admin_webpath
                .'/themes/'.$bo_theme.'/js/date-range-picker.js"></script>';
            $html .= '<script type="text/javascript" src="'.__PS_BASE_URI__.$admin_webpath
                .'/themes/'.$bo_theme.'/js/calendar.js"></script>';
        } else {
            $html = '';
            $context->controller->addJs(__PS_BASE_URI__.$admin_webpath
                .'/themes/'.$bo_theme.'/js/date-range-picker.js');
            $context->controller->addJs(__PS_BASE_URI__.$admin_webpath
                .'/themes/'.$bo_theme.'/js/calendar.js');
        }

        $this->tpl = $this->createTemplate($this->base_tpl);
        $this->tpl->assign(array(
            'date_format'       => $this->getDateFormat(),
            'date_from'         => $this->getDateFrom(),
            'date_to'           => $this->getDateTo(),
            'compare_date_from' => $this->getCompareDateFrom(),
            'compare_date_to'   => $this->getCompareDateTo(),
            'actions'           => $this->getActions(),
            'compare_actions'   => $this->getCompareActions(),
            'compare_option'    => $this->getCompareOption(),
            'is_rtl'            => $this->isRTL()
        ));

        $html .= parent::generate();
        return $html;
    }

    public function isRTL()
    {
        if (!isset($this->_rtl)) {
            $this->_rtl = Context::getContext()->language->is_rtl;
        }

        return $this->_rtl;
    }
}
