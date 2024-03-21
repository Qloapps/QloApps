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
 * @property QuickAccess $object
 */
class AdminQuickAccessesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'quick_access';
        $this->className = 'QuickAccess';
        $this->lang = true;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->context = Context::getContext();

        if (!Tools::getValue('realedit')) {
            $this->deleted = false;
        }

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->fields_list = array(
            'id_quick_access' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name')
            ),
            'link' => array(
                'title' => $this->l('Link')
            ),
            'new_window' => array(
                'title' => $this->l('New window'),
                'align' => 'center',
                'type' => 'bool',
                'active' => 'new_window',
                'class' => 'fixed-width-sm'
            )
        );

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Quick Access menu'),
                'icon' => 'icon-align-justify'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'lang' => true,
                    'maxlength' => 32,
                    'required' => true,
                    'hint' => $this->l('Forbidden characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('URL'),
                    'name' => 'link',
                    'maxlength' => 128,
                    'required' => true,
                    'hint' => $this->l('If it\'s a URL that comes from your Back Office, you MUST remove the security token.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Open in new window'),
                    'name' => 'new_window',
                    'required' => false,
                    'values' => array(
                        array(
                            'id' => 'new_window_on',
                            'value' => 1,
                            'label' => '<img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" />'
                        ),
                        array(
                            'id' => 'new_window_off',
                            'value' => 0,
                            'label' => '<img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" />'
                        )
                    )
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->l('Save and stay'),
                    'name' => 'submitAdd'.$this->table.'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save',
                ),
            ),
        );

        parent::__construct();
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_quick_access'] = array(
                'href' => self::$currentIndex.'&addquick_access&token='.$this->token,
                'desc' => $this->l('Add new quick access', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function initProcess()
    {
        if ((isset($_GET['new_window'.$this->table]) || isset($_GET['new_window'])) && Tools::getValue($this->identifier)) {
            if ($this->tabAccess['edit'] === '1') {
                $this->action = 'newWindow';
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        }

        parent::initProcess();
    }

    public function getQuickAccessesList()
    {
        $links = QuickAccess::getQuickAccesses($this->context->language->id);
        return json_encode(array_map(array($this, 'getLinkToken'), $links));
    }

    public function getLinkToken($item)
    {
        $url = parse_url($item['link']);
        parse_str($url['query'], $query);
        $controller = $query['controller'];
        $item['token'] = Tools::getAdminTokenLite($controller);
        return $item;
    }

    public function addQuickLink()
    {
        if (!isset($this->className) || empty($this->className)) {
            return false;
        }
        $this->validateRules();

        if (count($this->errors) <= 0) {
            $this->object = new $this->className();
            $this->copyFromPost($this->object, $this->table);
            $exists = Db::getInstance()->getValue('SELECT id_quick_access FROM '._DB_PREFIX_.'quick_access WHERE link = "'.pSQL($this->object->link).'"');
            if ($exists) {
                return true;
            }
            $this->beforeAdd($this->object);

            if (method_exists($this->object, 'add') && !$this->object->add()) {
                $this->errors[] = Tools::displayError('An error occurred while creating an object.').
                    ' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
            }
            /* voluntary do affectation here */
            elseif (($_POST[$this->identifier] = $this->object->id) && $this->postImage($this->object->id) && !count($this->errors) && $this->_redirect) {
                PrestaShopLogger::addLog(sprintf($this->l('%s addition', 'AdminTab', false, false), $this->className), 1, null, $this->className, (int)$this->object->id, true, (int)$this->context->employee->id);
                $this->afterAdd($this->object);
            }
        }

        $this->errors = array_unique($this->errors);
        if (!empty($this->errors)) {
            $this->errors['has_errors'] = true;
            $this->ajaxDie(json_encode($this->errors));
            return false;
        }
        return $this->getQuickAccessesList();
    }

    public function processDelete()
    {
        parent::processDelete();
        return $this->getQuickAccessesList();
    }

    public function ajaxProcessGetUrl()
    {
        if (Tools::strtolower(Tools::getValue('method')) === 'add') {
            $params['new_window'] = 0;
            $params['name_'.(int)Configuration::get('PS_LANG_DEFAULT')] = Tools::getValue('name');
            $params['link'] = 'index.php?'.Tools::getValue('url');
            $params['submitAddquick_access'] = 1;
            unset($_POST['name']);
            $_POST = array_merge($_POST, $params);
            die($this->addQuickLink());
        } elseif (Tools::strtolower(Tools::getValue('method')) === 'remove') {
            $params['deletequick_access'] = 1;
            $_POST = array_merge($_POST, $params);
            die($this->processDelete());
        }
    }

    public function processNewWindow()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            /** @var QuickAccess $object */
            if ($object->toggleNewWindow()) {
                $this->redirect_after = self::$currentIndex.'&conf=5&token='.$this->token;
            } else {
                $this->errors[] = Tools::displayError('An error occurred while updating new window property.');
            }
        } else {
            $this->errors[] = Tools::displayError('An error occurred while updating the new window property for this object.').
                ' <b>'.$this->table.'</b> '.
                Tools::displayError('(cannot load object)');
        }

        return $object;
    }
}
