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

class TreeCore
{
    const DEFAULT_TEMPLATE_DIRECTORY   = 'helpers/tree';
    const DEFAULT_TEMPLATE             = 'tree.tpl';
    const DEFAULT_HEADER_TEMPLATE      = 'tree_header.tpl';
    const DEFAULT_NODE_FOLDER_TEMPLATE = 'tree_node_folder.tpl';
    const DEFAULT_NODE_ITEM_TEMPLATE   = 'tree_node_item.tpl';

    protected $_attributes;
    private $_context;
    protected $_data;
    protected $_data_search;
    protected $_headerTemplate;
    protected $_id_tree;
    private $_id;
    protected $_node_folder_template;
    protected $_node_item_template;
    protected $_template;

    /** @var string */
    private $_template_directory;
    private $_title;
    private $_no_js;

    /** @var TreeToolbar|ITreeToolbar */
    private $_toolbar;

    public function __construct($id, $data = null)
    {
        $this->setId($id);

        if (isset($data)) {
            $this->setData($data);
        }
    }

    public function __toString()
    {
        return $this->render();
    }

    public function setActions($value)
    {
        if (!isset($this->_toolbar)) {
            $this->setToolbar(new TreeToolbarCore());
        }

        $this->getToolbar()->setTemplateDirectory($this->getTemplateDirectory())->setActions($value);
        return $this;
    }

    public function getActions()
    {
        if (!isset($this->_toolbar)) {
            $this->setToolbar(new TreeToolbarCore());
        }

        return $this->getToolbar()->setTemplateDirectory($this->getTemplateDirectory())->getActions();
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

    public function setIdTree($id_tree)
    {
        $this->_id_tree = $id_tree;
        return $this;
    }

    public function getIdTree()
    {
        return $this->_id_tree;
    }

    public function getAttributes()
    {
        if (!isset($this->_attributes)) {
            $this->_attributes = array();
        }

        return $this->_attributes;
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

    public function setDataSearch($value)
    {
        if (!is_array($value) && !$value instanceof Traversable) {
            throw new PrestaShopException('Data value must be an traversable array');
        }

        $this->_data_search = $value;
        return $this;
    }

    public function getDataSearch()
    {
        if (!isset($this->_data_search)) {
            $this->_data_search = array();
        }

        return $this->_data_search;
    }

    public function setData($value)
    {
        if (!is_array($value) && !$value instanceof Traversable) {
            throw new PrestaShopException('Data value must be an traversable array');
        }

        $this->_data = $value;
        return $this;
    }

    public function getData()
    {
        if (!isset($this->_data)) {
            $this->_data = array();
        }

        return $this->_data;
    }

    public function setHeaderTemplate($value)
    {
        $this->_headerTemplate = $value;
        return $this;
    }

    public function getHeaderTemplate()
    {
        if (!isset($this->_headerTemplate)) {
            $this->setHeaderTemplate(self::DEFAULT_HEADER_TEMPLATE);
        }

        return $this->_headerTemplate;
    }

    public function setId($value)
    {
        $this->_id = $value;
        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setNodeFolderTemplate($value)
    {
        $this->_node_folder_template = $value;
        return $this;
    }

    public function getNodeFolderTemplate()
    {
        if (!isset($this->_node_folder_template)) {
            $this->setNodeFolderTemplate(self::DEFAULT_NODE_FOLDER_TEMPLATE);
        }

        return $this->_node_folder_template;
    }

    public function setNodeItemTemplate($value)
    {
        $this->_node_item_template = $value;
        return $this;
    }

    public function getNodeItemTemplate()
    {
        if (!isset($this->_node_item_template)) {
            $this->setNodeItemTemplate(self::DEFAULT_NODE_ITEM_TEMPLATE);
        }

        return $this->_node_item_template;
    }

    public function setTemplate($value)
    {
        $this->_template = $value;
        return $this;
    }

    public function getTemplate()
    {
        if (!isset($this->_template)) {
            $this->setTemplate(self::DEFAULT_TEMPLATE);
        }

        return $this->_template;
    }

    /**
     * @param $value
     *
     * @return Tree
     */
    public function setTemplateDirectory($value)
    {
        $this->_template_directory = $this->_normalizeDirectory($value);
        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateDirectory()
    {
        if (!isset($this->_template_directory)) {
            $this->_template_directory = $this->_normalizeDirectory(
                self::DEFAULT_TEMPLATE_DIRECTORY);
        }

        return $this->_template_directory;
    }

    public function getTemplateFile($template)
    {
        if (preg_match_all('/[^Admin].*(?=Controller)/', get_class($this->getContext()->controller), $matches) !== false) {
            $controller_name = strtolower(Tools::toUnderscoreCase($matches[0][0]));
        }

        if ($this->getContext()->controller instanceof ModuleAdminController && isset($controller_name) && file_exists($this->_normalizeDirectory(
                $this->getContext()->controller->getTemplatePath()).$controller_name.DIRECTORY_SEPARATOR.$this->getTemplateDirectory().$template)) {
            return $this->_normalizeDirectory($this->getContext()->controller->getTemplatePath()).
                $controller_name.DIRECTORY_SEPARATOR.$this->getTemplateDirectory().$template;
        } elseif ($this->getContext()->controller instanceof ModuleAdminController && file_exists($this->_normalizeDirectory(
                $this->getContext()->controller->getTemplatePath()).$this->getTemplateDirectory().$template)) {
            return $this->_normalizeDirectory($this->getContext()->controller->getTemplatePath())
                .$this->getTemplateDirectory().$template;
        } elseif ($this->getContext()->controller instanceof AdminController && isset($controller_name)
            && file_exists($this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(0)).'controllers'
                .DIRECTORY_SEPARATOR.$controller_name.DIRECTORY_SEPARATOR.$this->getTemplateDirectory().$template)) {
            return $this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(0)).'controllers'
                .DIRECTORY_SEPARATOR.$controller_name.DIRECTORY_SEPARATOR.$this->getTemplateDirectory().$template;
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

    public function setNoJS($value)
    {
        $this->_no_js = $value;
        return $this;
    }

    public function setTitle($value)
    {
        $this->_title = $value;
        return $this;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function setToolbar($value)
    {
        if (!is_object($value)) {
            throw new PrestaShopException('Toolbar must be a class object');
        }

        $reflection = new ReflectionClass($value);

        if (!$reflection->implementsInterface('ITreeToolbarCore')) {
            throw new PrestaShopException('Toolbar class must implements ITreeToolbarCore interface');
        }

        $this->_toolbar = $value;
        return $this;
    }

    public function getToolbar()
    {
        if (isset($this->_toolbar)) {
            if ($this->getDataSearch()) {
                $this->_toolbar->setData($this->getDataSearch());
            } else {
                $this->_toolbar->setData($this->getData());
            }
        }

        return $this->_toolbar;
    }

    public function addAction($action)
    {
        if (!isset($this->_toolbar)) {
            $this->setToolbar(new TreeToolbarCore());
        }

        $this->getToolbar()->setTemplateDirectory($this->getTemplateDirectory())->addAction($action);
        return $this;
    }

    public function removeActions()
    {
        if (!isset($this->_toolbar)) {
            $this->setToolbar(new TreeToolbarCore());
        }

        $this->getToolbar()->setTemplateDirectory($this->getTemplateDirectory())->removeActions();
        return $this;
    }

    public function render($data = null)
    {
        //Adding tree.js
        $admin_webpath = str_ireplace(_PS_CORE_DIR_, '', _PS_ADMIN_DIR_);
        $admin_webpath = preg_replace('/^'.preg_quote(DIRECTORY_SEPARATOR, '/').'/', '', $admin_webpath);
        $bo_theme = ((Validate::isLoadedObject($this->getContext()->employee)
            && $this->getContext()->employee->bo_theme) ? $this->getContext()->employee->bo_theme : 'default');

        if (!file_exists(_PS_BO_ALL_THEMES_DIR_.$bo_theme.DIRECTORY_SEPARATOR.'template')) {
            $bo_theme = 'default';
        }

        $js_path = __PS_BASE_URI__.$admin_webpath.'/themes/'.$bo_theme.'/js/tree.js?v=' ._PS_VERSION_;
        if ($this->getContext()->controller->ajax) {
            if (!$this->_no_js) {
                $html = '<script type="text/javascript">$(function(){ $.ajax({url: "' .$js_path . '",cache:true,dataType: "script"})});</script>';
            }
        } else {
            $this->getContext()->controller->addJs($js_path);
        }

        //Create Tree Template
        $template = $this->getContext()->smarty->createTemplate(
            $this->getTemplateFile($this->getTemplate()),
            $this->getContext()->smarty
        );

        if (trim($this->getTitle()) != '' || $this->useToolbar()) {
            //Create Tree Header Template
            $headerTemplate = $this->getContext()->smarty->createTemplate(
                $this->getTemplateFile($this->getHeaderTemplate()),
                $this->getContext()->smarty
            );
            $headerTemplate->assign($this->getAttributes())
                ->assign(array(
                'title'   => $this->getTitle(),
                'toolbar' => $this->useToolbar() ? $this->renderToolbar() : null
            ));
            $template->assign('header', $headerTemplate->fetch());
        }

        //Assign Tree nodes
        $template->assign($this->getAttributes())->assign(array(
            'id'    => $this->getId(),
            'nodes' => $this->renderNodes($data),
            'id_tree' => $this->getIdTree()
        ));

        return (isset($html) ? $html : '').$template->fetch();
    }

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
                    'children' => $this->renderNodes($item['children']),
                    'node'     => $item
                ))->fetch();
            } else {
                $html .= $this->getContext()->smarty->createTemplate(
                    $this->getTemplateFile($this->getNodeItemTemplate()),
                    $this->getContext()->smarty
                )->assign(array(
                    'node' => $item
                ))->fetch();
            }
        }

        return $html;
    }

    public function renderToolbar()
    {
        return $this->getToolbar()->render();
    }

    public function useInput()
    {
        return isset($this->_input_type);
    }

    public function useToolbar()
    {
        return isset($this->_toolbar);
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
