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

abstract class TreeToolbarButtonCore
{
    const DEFAULT_TEMPLATE_DIRECTORY = 'helpers/tree';

    protected $_attributes;
    private $_class;
    private $_context;
    private $_id;
    private $_label;
    private $_name;
    protected $_template;
    protected $_template_directory;

    public function __construct($label, $id = null, $name = null, $class = null)
    {
        $this->setLabel($label);
        $this->setId($id);
        $this->setName($name);
        $this->setClass($class);
    }

    public function __toString()
    {
        return $this->render();
    }

    public function setAttribute($name, $value)
    {
        if (!isset($this->_attributes)) {
            $this->_attributes = array();
        }

        $this->_attributes[$name] = $value;
        return $this;
    }

    public function getAttribute($name)
    {
        return $this->hasAttribute($name) ? $this->_attributes[$name] : null;
    }

    public function setAttributes($value)
    {
        if (!is_array($value) && !$value instanceof Traversable) {
            throw new PrestaShopException('Data value must be an traversable array');
        }

        $this->_attributes = $value;
        return $this;
    }

    public function getAttributes()
    {
        if (!isset($this->_attributes)) {
            $this->_attributes = array();
        }

        return $this->_attributes;
    }

    public function setClass($value)
    {
        return $this->setAttribute('class', $value);
    }

    public function getClass()
    {
        return $this->getAttribute('class');
    }

    public function setContext($value)
    {
        $this->_context = $value;
        return $this;
    }

    public function getContext()
    {
        if (!isset($this->_context)) {
            $this->_context = Context::getContext();
        }

        return $this->_context;
    }

    public function setId($value)
    {
        return $this->setAttribute('id', $value);
    }

    public function getId()
    {
        return $this->getAttribute('id');
    }

    public function setLabel($value)
    {
        return $this->setAttribute('label', $value);
    }

    public function getLabel()
    {
        return $this->getAttribute('label');
    }

    public function setName($value)
    {
        return $this->setAttribute('name', $value);
    }

    public function getName()
    {
        return $this->getAttribute('name');
    }

    public function setTemplate($value)
    {
        $this->_template = $value;
        return $this;
    }

    public function getTemplate()
    {
        return $this->_template;
    }

    public function setTemplateDirectory($value)
    {
        $this->_template_directory = $this->_normalizeDirectory($value);
        return $this;
    }

    public function getTemplateDirectory()
    {
        if (!isset($this->_template_directory)) {
            $this->_template_directory = $this->_normalizeDirectory(self::DEFAULT_TEMPLATE_DIRECTORY);
        }

        return $this->_template_directory;
    }

    public function getTemplateFile($template)
    {
        if (preg_match_all('/((?:^|[A-Z])[a-z]+)/', get_class($this->getContext()->controller), $matches) !== false) {
            $controllerName = strtolower($matches[0][1]);
        }

        if ($this->getContext()->controller instanceof ModuleAdminController && file_exists($this->_normalizeDirectory(
                $this->getContext()->controller->getTemplatePath()).$this->getTemplateDirectory().$template)) {
            return $this->_normalizeDirectory($this->getContext()->controller->getTemplatePath())
                .$this->getTemplateDirectory().$template;
        } elseif ($this->getContext()->controller instanceof AdminController && isset($controllerName)
            && file_exists($this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(0)).'controllers'
                .DIRECTORY_SEPARATOR.$controllerName.DIRECTORY_SEPARATOR.$this->getTemplateDirectory().$template)) {
            return $this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(0)).'controllers'
                .DIRECTORY_SEPARATOR.$controllerName.DIRECTORY_SEPARATOR.$this->getTemplateDirectory().$template;
        } elseif (file_exists($this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(1))
                .$this->getTemplateDirectory().$template)) {
            return $this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(1))
                    .$this->getTemplateDirectory().$template;
        } elseif (file_exists($this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(0))
                .$this->getTemplateDirectory().$template)) {
            return $this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(0))
                .$this->getTemplateDirectory().$template;
        } else {
            return $this->getTemplateDirectory().$template;
        }
    }

    public function hasAttribute($name)
    {
        return (isset($this->_attributes)
            && array_key_exists($name, $this->_attributes));
    }

    public function render()
    {
        return $this->getContext()->smarty->createTemplate(
            $this->getTemplateFile($this->getTemplate()),
            $this->getContext()->smarty
        )->assign($this->getAttributes())->fetch();
    }

    private function _normalizeDirectory($directory)
    {
        $last = $directory[strlen($directory) - 1];

        if (in_array($last, array('/', '\\'))) {
            $directory[strlen($directory) - 1] = DIRECTORY_SEPARATOR;
            return $directory;
        }

        $directory .= DIRECTORY_SEPARATOR;
        return $directory;
    }
}
