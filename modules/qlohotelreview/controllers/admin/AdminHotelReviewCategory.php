<?php
/**
* 2010-2022 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2022 Webkul IN
* @license LICENSE.txt
*/

class AdminHotelReviewCategoryController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->className = 'QhrCategory';
        $this->table = 'qhr_category';
        $this->identifier = 'id_category';
        $this->lang = true;
        
        parent::__construct();
        
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->fields_list = array(
            'id_category' => array(
                'title' => $this->l('ID'),
                'hint' => $this->l('ID of the category.'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'hint' => $this->l('Name of the category.'),
                'align' => 'left',
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'hint' => $this->l('Current status of the category.'),
                'type' => 'bool',
                'active' => 'status',
                'align' => 'center',
                'filter_key' => 'a!active',
            ),
        );

        $this->list_no_link = true;

        $this->bulk_actions = array(
            'enableSelection' => array(
                'text' => $this->l('Enable selection'),
                'icon' => 'icon-power-off text-success'
            ),
            'disableSelection' => array(
                'text' => $this->l('Disable selection'),
                'icon' => 'icon-power-off text-danger'
            ),
            'divider' => array(
                'text' => 'divider'
            ),
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );
    }

    public function initToolbar()
    {
        parent::initToolbar();
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new'] = array(
                'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
                'desc' => $this->l('Add new category')
            );
        }

        if ($this->display == 'options') {
            $this->toolbar_btn = array();
            $this->toolbar_btn['new'] = array(
                'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
                'desc' => $this->l('Add new category')
            );
        }
    }

    public function initToolbarTitle()
    {
        parent::initToolbarTitle();
        if ($this->display == 'add') {
            $this->toolbar_title = $this->l('Adding new category');
        } elseif ($this->display == 'edit') {
            $this->toolbar_title = $this->l('Editing');
        } elseif ($this->display == '') {
            $this->toolbar_title = $this->l('Configuration');
        }
    }

    public function initContent()
    {
        parent::initContent();
        if ($this->display == 'options') {
            $this->content = $this->renderOptions();
            $this->content .= $this->renderList();
            $this->context->smarty->assign(array('content' => $this->content));
        }
    }

    public function renderOptions()
    {
        $this->fields_options = array(
            'general' => array(
                'title' => $this->l('Review Options'),
                'icon' => 'icon icon-cog',
                'fields' => array(
                    'QHR_ADMIN_APPROVAL_ENABLED' => array(
                        'type' => 'bool',
                        'title' => $this->l('Admin approval'),
                        'hint' => $this->l('Enable if you want each review to be approved by an employee.'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                    ),
                    'QHR_MAX_IMAGES_PER_REVIEW' => array(
                        'type' => 'text',
                        'title' => $this->l('Maximum images per review'),
                        'hint' => $this->l('The number of maximum images that can be uploaded with a review.'),
                        'class' => 'fixed-width-md',
                        'validation' => 'isUnsignedInt',
                        'required' => true,
                        'cast' => 'intval',
                    ),
                    'QHR_REVIEWS_PER_PAGE' => array(
                        'type' => 'text',
                        'title' => $this->l('Reviews per page'),
                        'hint' => $this->l('The number of reviews that are displayed per page.'),
                        'class' => 'fixed-width-md',
                        'validation' => 'isUnsignedInt',
                        'required' => true,
                        'cast' => 'intval',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'submitHotelReviewOptions',
                )
            ),
        );
        return parent::renderOptions();
    }

    public function renderList()
    {
        $this->tpl_list_vars['title'] = $this->l('Categories');
        $this->context->smarty->assign(array('icon' => 'icon-list'));
        return parent::renderList();
    }

    public function renderForm()
    {
        $smartyVars = array();
        $idCategory = Tools::getValue($this->identifier);
        $objCategory = $this->loadObject(true);

        $smartyVars['id_category'] = (int)$idCategory;
        $smartyVars['current_iso_code'] = $this->context->language->iso_code;
        $smartyVars['currentTab'] = $this;
        $smartyVars['currentObject'] = $objCategory;

        $this->multiple_fieldsets = 1;
        $this->fields_form = $this->getFieldsForm();
        $this->context->smarty->assign($smartyVars);
        return parent::renderForm();
    }

    public function getFieldsForm()
    {
        $fields_form = array();

        $fields_form[] = array(
            'form' => array(
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'submitCategory',
                    'class' => 'btn btn-default pull-right',
                ),
            ),
        );

        return $fields_form;
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitHotelReviewOptions')) {
            $imagesPerReview = Tools::getValue('QHR_MAX_IMAGES_PER_REVIEW');
            $reviewsPerPage = Tools::getValue('QHR_REVIEWS_PER_PAGE');

            if ($imagesPerReview == '') {
                $this->errors[] = $this->l('Please enter maximum images per review.');
            }
            if ($imagesPerReview && !Validate::isUnsignedInt($imagesPerReview)) {
                $this->errors[] = $this->l('Please enter valid maximum images per review.');
            }

            if ($reviewsPerPage == '') {
                $this->errors[] = $this->l('Please enter reviews per page.');
            }
            if (($reviewsPerPage == '0') || ($reviewsPerPage && !Validate::isUnsignedInt($reviewsPerPage))) {
                $this->errors[] = $this->l('Please enter valid reviews per page.');
            }
            if (!count($this->errors)) {
                Configuration::updateValue('QHR_ADMIN_APPROVAL_ENABLED', Tools::getValue('QHR_ADMIN_APPROVAL_ENABLED'));
                Configuration::updateValue('QHR_MAX_IMAGES_PER_REVIEW', $imagesPerReview);
                Configuration::updateValue('QHR_REVIEWS_PER_PAGE', $reviewsPerPage);
                Tools::redirectAdmin(self::$currentIndex.'&conf=6&token='.$this->token);
            }
        } elseif (Tools::isSubmit('submitCategory') || Tools::isSubmit('submitCategoryAndStay')) {
            $idCategory = Tools::getValue($this->identifier);
            $this->validateRules();
            if (!count($this->errors)) {
                $objCategory = new QhrCategory($idCategory);
                $this->copyFromPost($objCategory, $this->table);
                if ($objCategory->save()) {
                    $newId = $objCategory->id;
                    if (Tools::isSubmit('submitCategoryAndStay')) {
                        if ($idCategory) {
                            Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token.
                            '&update'.$this->table.'&'.$this->identifier.'='.$newId);
                        } else {
                            Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token.
                            '&update'.$this->table.'&'.$this->identifier.'='.$newId);
                        }
                    } else {
                        if ($idCategory) {
                            Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                        } else {
                            Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
                        }
                    }
                }
            }
        }
        parent::postProcess();
    }
}
