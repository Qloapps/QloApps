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
 * @property Profile $object
 */
class AdminProfilesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->table = 'profile';
        $this->className = 'Profile';
        $this->multishop_context = Shop::CONTEXT_ALL;
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->addRowActionSkipList('delete', array(1));

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->fields_list = array(
            'id_profile' => array(
                        'title' => $this->l('ID'),
                        'align' => 'center',
                        'class' => 'fixed-width-xs'
                        ),
            'name' => array('title' => $this->l('Name'))
            );

        $this->identifier = 'id_profile';

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Profile'),
                'icon' => 'icon-group'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'required' => true,
                    'lang' => true,
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

        $list_profile = array();
        foreach (Profile::getProfiles($this->context->language->id) as $profil) {
            $list_profile[] = array('value' => $profil['id_profile'], 'name' => $profil['name']);
        }

        parent::__construct();
    }

    public function postProcess()
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_) {
            $this->errors[] = Tools::displayError('This functionality has been disabled.');
            return;
        }
        /* PrestaShop demo mode*/

        if (isset($_GET['delete'.$this->table]) && $_GET[$this->identifier] == (int)(_PS_ADMIN_PROFILE_)) {
            $this->errors[] = $this->l('For security reasons, you cannot delete the Administrator\'s profile.');
        } else {
            parent::postProcess();
        }
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_profile'] = array(
                'href' => self::$currentIndex.'&addprofile&token='.$this->token,
                'desc' => $this->l('Add new profile', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function processDelete()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            // check if any employee exists of this deleting profile before delete
            $profileEmployees = Employee::getEmployeesByProfile($object->id);
            if (empty($profileEmployees)) {
                return parent::processDelete();
            } else {
                $this->errors[] = $this->l('Failed to delete profile, because it is assigned to at least one employee.');
            }
        } else {
            $this->errors[] = $this->l('An error occurred while deleting the profile.').' '.$this->l('(cannot load object)');
        }

        return false;
    }

    protected function processBulkDelete()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $idProfile) {
                // check if any employee exists of this deleting profile before delete
                $profileEmployees = Employee::getEmployeesByProfile($idProfile);
                if (!empty($profileEmployees)) {
                    $this->errors[] = $this->l('Failed to delete profile with id').' - #'.$idProfile.' '.$this->l('because it is assigned to at least one employee. Please remove it from selection.');
                }
            }
        }

        if (empty($this->errors)) {
            return parent::processBulkDelete();
        }

        return false;
    }
}
