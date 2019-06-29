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

/**
 * @property WebserviceKey $object
 */
class AdminWebserviceControllerCore extends AdminController
{
    /** this will be filled later */
    public $fields_form = array('webservice form');
    protected $toolbar_scroll = false;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'webservice_account';
        $this->className = 'WebserviceKey';
        $this->lang = false;
        $this->edit = true;
        $this->delete = true;
        $this->id_lang_default = Configuration::get('PS_LANG_DEFAULT');

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->fields_list = array(
            'key' => array(
                'title' => $this->l('Key'),
                'class' => 'fixed-width-md'
            ),
            'description' => array(
                'title' => $this->l('Key description'),
                'align' => 'left',
                'orderby' => false
            ),
            'active' => array(
                'title' => $this->l('Enabled'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'class' => 'fixed-width-xs'
            )
        );

        $this->fields_options = array(
                'general' => array(
                    'title' =>    $this->l('Configuration'),
                    'fields' =>    array(
                        'PS_WEBSERVICE' => array('title' => $this->l('Enable Qloapps\'s webservice'),
                            'desc' => $this->l('Before activating the webservice, you must be sure to: ').
                                                '<ol>
													<li>'.$this->l('Check that URL rewriting is available on this server.').'</li>
													<li>'.$this->l('Check that the five methods GET, POST, PUT, DELETE and HEAD are supported by this server.').'</li>
												</ol>',
                            'cast' => 'intval',
                            'type' => 'bool'),
                    ),
                    'submit' => array('title' => $this->l('Save'))
                ),
            );

        if (!defined('_PS_HOST_MODE_')) {
            $this->fields_options['general']['fields']['PS_WEBSERVICE_CGI_HOST'] = array(
                'title' => $this->l('Enable CGI mode for PHP'),
                'desc' => $this->l('Before choosing "Yes", check that PHP is not configured as an Apache module on your server.'),
                'cast' => 'intval',
                'type' => 'bool'
            );
        }

        parent::__construct();
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_webservice'] = array(
                'href' => self::$currentIndex.'&addwebservice_account&token='.$this->token,
                'desc' => $this->l('Add new webservice key', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }


    protected function processUpdateOptions()
    {
        parent::processUpdateOptions();
        Tools::generateHtaccess();
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Webservice Accounts'),
                'icon' => 'icon-lock'
            ),
            'input' => array(
                array(
                    'type' => 'textbutton',
                    'label' => $this->l('Key'),
                    'name' => 'key',
                    'id' => 'code',
                    'required' => true,
                    'hint' => $this->l('Webservice account key.'),
                    'button' => array(
                        'label' => $this->l('Generate!'),
                        'attributes' => array(
                            'onclick' => 'gencode(32)'
                        )
                    )
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Key description'),
                    'name' => 'description',
                    'rows' => 3,
                    'cols' => 110,
                    'hint' => $this->l('Quick description of the key: who it is for, what permissions it has, etc.'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Status'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
                array(
                    'type' => 'resources',
                    'label' => $this->l('Permissions'),
                    'name' => 'resources',
                )
            )
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            );
        }

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );

        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $ressources = WebserviceRequest::getResources();
        $permissions = WebserviceKey::getPermissionForAccount($obj->key);

        $this->tpl_form_vars = array(
            'ressources' => $ressources,
            'permissions' => $permissions
        );

        return parent::renderForm();
    }

    public function initContent()
    {
        if ($this->display != 'add' && $this->display != 'edit') {
            $this->checkForWarning();
        }

        parent::initContent();
    }

    /**
     * Function used to render the options for this controller
     */
    public function renderOptions()
    {
        if ($this->fields_options && is_array($this->fields_options)) {
            $helper = new HelperOptions($this);
            $this->setHelperDisplay($helper);
            $helper->toolbar_scroll = true;
            $helper->toolbar_btn = array('save' => array(
                                'href' => '#',
                                'desc' => $this->l('Save')
                            ));
            $helper->id = $this->id;
            $helper->tpl_vars = $this->tpl_option_vars;
            $options = $helper->generateOptions($this->fields_options);

            return $options;
        }
    }

    public function initProcess()
    {
        parent::initProcess();
        // This is a composite page, we don't want the "options" display mode
        if ($this->display == 'options') {
            $this->display = '';
        }
    }

    public function postProcess()
    {
        if (Tools::getValue('key') && strlen(Tools::getValue('key')) < 32) {
            $this->errors[] = Tools::displayError('Key length must be 32 character long.');
        }
        if (WebserviceKey::keyExists(Tools::getValue('key')) && !Tools::getValue('id_webservice_account')) {
            $this->errors[] = Tools::displayError('This key already exists.');
        }
        return parent::postProcess();
    }

    protected function afterAdd($object)
    {
        Tools::generateHtaccess();
        WebserviceKey::setPermissionForAccount($object->id, Tools::getValue('resources', array()));
    }

    protected function afterUpdate($object)
    {
        Tools::generateHtaccess();
        WebserviceKey::setPermissionForAccount($object->id, Tools::getValue('resources', array()));
    }

    public function checkForWarning()
    {
        if (strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') === false) {
            $this->warnings[] = $this->l('To avoid operating problems, please use an Apache server.');
            if (function_exists('apache_get_modules')) {
                $apache_modules = apache_get_modules();
                if (!in_array('mod_auth_basic', $apache_modules)) {
                    $this->warnings[] = $this->l('Please activate the \'mod_auth_basic\' Apache module to allow authentication of PrestaShop\'s webservice.');
                }
                if (!in_array('mod_rewrite', $apache_modules)) {
                    $this->warnings[] = $this->l('Please activate the \'mod_rewrite\' Apache module to allow the PrestaShop webservice.');
                }
            } else {
                $this->warnings[] = $this->l('We could not check to see if basic authentication and rewrite extensions have been activated. Please manually check if they\'ve been activated in order to use the PrestaShop webservice.');
            }
        }
        if (!extension_loaded('SimpleXML')) {
            $this->warnings[] = $this->l('Please activate the \'SimpleXML\' PHP extension to allow testing of PrestaShop\'s webservice.');
        }
        if (!configuration::get('PS_SSL_ENABLED')) {
            $this->warnings[] = $this->l('It is preferable to use SSL (https:) for webservice calls, as it avoids the "man in the middle" type security issues.');
        }

        foreach ($this->_list as $k => $item) {
            if ($item['is_module'] && $item['class_name'] && $item['module_name'] &&
                ($instance = Module::getInstanceByName($item['module_name'])) &&
                !$instance->useNormalPermissionBehaviour()) {
                unset($this->_list[$k]);
            }
        }

        $this->renderList();
    }
}
