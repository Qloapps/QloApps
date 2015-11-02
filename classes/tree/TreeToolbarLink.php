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

class TreeToolbarLinkCore extends TreeToolbarButtonCore implements
    ITreeToolbarButtonCore
{
    private $_action;
    private $_icon_class;
    private $_link;
    protected $_template = 'tree_toolbar_link.tpl';

    public function __construct($label, $link, $action = null, $iconClass = null)
    {
        parent::__construct($label);

        $this->setLink($link);
        $this->setAction($action);
        $this->setIconClass($iconClass);
    }

    public function setAction($value)
    {
        return $this->setAttribute('action', $value);
    }

    public function getAction()
    {
        return $this->getAttribute('action');
    }

    public function setIconClass($value)
    {
        return $this->setAttribute('icon_class', $value);
    }

    public function getIconClass()
    {
        return $this->getAttribute('icon_class');
    }

    public function setLink($value)
    {
        return $this->setAttribute('link', $value);
    }

    public function getLink()
    {
        return $this->getAttribute('link');
    }
}
