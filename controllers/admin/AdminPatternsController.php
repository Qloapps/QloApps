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

class AdminPatternsControllerCore extends AdminController
{
    public $name = "patterns";

    public function __construct()
    {
        $this->bootstrap = true;
        $this->show_toolbar = false;
        $this->context = Context::getContext();

        parent::__construct();
    }

    public function viewAccess($disable = false)
    {
        return true;
    }

    public function renderForm()
    {
        $this->fields_value = array(
            'type_text' => 'with value',
            'type_text_readonly' => 'with value that you can\'t edit',
            'type_switch' => 1,
            'days' => 17,
            'months' => 3,
            'years' => 2014,
            'groupBox_1' => false,
            'groupBox_2' => true,
            'groupBox_3' => false,
            'groupBox_4' => true,
            'groupBox_5' => true,
            'groupBox_6' => false,
            'type_color' => '#8BC954',
            'tab_note' => 'The tabs are always pushed to the top of the form, wherever they are in the fields_form array.',
            'type_free' => '<p class="form-control-static">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc lacinia in enim iaculis malesuada. Quisque congue fermentum leo et porta. Pellentesque a quam dui. Pellentesque sed augue id sem aliquet faucibus eu vel odio. Nullam non libero volutpat, pulvinar turpis non, gravida mauris. Nullam tincidunt id est at euismod. Quisque euismod quam in pellentesque mollis. Nulla suscipit porttitor massa, nec eleifend risus egestas in. Aenean luctus porttitor tempus. Morbi dolor leo, dictum id interdum vel, semper ac est. Maecenas justo augue, accumsan in velit nec, consectetur fringilla orci. Nunc ut ante erat. Curabitur dolor augue, eleifend a luctus non, aliquet a mi. Curabitur ultricies lectus in rhoncus sodales. Maecenas quis dictum erat. Suspendisse blandit lacus sed felis facilisis, in interdum quam congue.<p>'
        );

        $this->fields_form = array(
            'legend' => array(
                'title' => 'patterns of helper form.tpl',
                'icon' => 'icon-edit'
            ),
            'tabs' => array(
                'small' => 'Small Inputs',
                'large' => 'Large Inputs',
            ),
            'description' => 'You can use image instead of icon for the title.',
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => 'simple input text',
                    'name' => 'type_text'
                ),
                array(
                    'type' => 'text',
                    'label' => 'input text with desc',
                    'name' => 'type_text_desc',
                    'desc' => 'desc input text'
                ),
                array(
                    'type' => 'text',
                    'label' => 'required input text',
                    'name' => 'type_text_required',
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => 'input text with hint',
                    'name' => 'type_text_hint',
                    'hint' => 'hint input text'
                ),
                array(
                    'type' => 'text',
                    'label' => 'input text with prefix',
                    'name' => 'type_text_prefix',
                    'prefix' => 'prefix'
                ),
                array(
                    'type' => 'text',
                    'label' => 'input text with suffix',
                    'name' => 'type_text_suffix',
                    'suffix' => 'suffix'
                ),
                array(
                    'type' => 'text',
                    'label' => 'input text with placeholder',
                    'name' => 'type_text_placeholder',
                    'placeholder' => 'placeholder'
                ),
                array(
                    'type' => 'text',
                    'label' => 'input text with character counter',
                    'name' => 'type_text_maxchar',
                    'maxchar' => 30
                ),
                array(
                    'type' => 'text',
                    'lang' => true,
                    'label' => 'input text multilang',
                    'name' => 'type_text_multilang'
                ),
                array(
                    'type' => 'text',
                    'label' => 'input readonly',
                    'readonly' => true,
                    'name' => 'type_text_readonly'
                ),
                array(
                    'type' => 'text',
                    'label' => 'input fixed-width-xs',
                    'name' => 'type_text_xs',
                    'class' => 'input fixed-width-xs'
                ),
                array(
                    'type' => 'text',
                    'label' => 'input fixed-width-sm',
                    'name' => 'type_text_sm',
                    'class' => 'input fixed-width-sm'
                ),
                array(
                    'type' => 'text',
                    'label' => 'input fixed-width-md',
                    'name' => 'type_text_md',
                    'class' => 'input fixed-width-md'
                ),
                array(
                    'type' => 'text',
                    'label' => 'input fixed-width-lg',
                    'name' => 'type_text_lg',
                    'class' => 'input fixed-width-lg'
                ),
                array(
                    'type' => 'text',
                    'label' => 'input fixed-width-xl',
                    'name' => 'type_text_xl',
                    'class' => 'input fixed-width-xl'
                ),
                array(
                    'type' => 'text',
                    'label' => 'input fixed-width-xxl',
                    'name' => 'type_text_xxl',
                    'class' => 'fixed-width-xxl'
                ),
                array(
                    'type' => 'text',
                    'label' => 'input fixed-width-sm',
                    'name' => 'type_text_sm',
                    'class' => 'input fixed-width-sm',
                    'tab' => 'small',
                ),
                array(
                    'type' => 'text',
                    'label' => 'input fixed-width-md',
                    'name' => 'type_text_md',
                    'class' => 'input fixed-width-md',
                    'tab' => 'small',
                ),
                array(
                    'type' => 'text',
                    'label' => 'input fixed-width-lg',
                    'name' => 'type_text_lg',
                    'class' => 'input fixed-width-lg',
                    'tab' => 'large',
                ),
                array(
                    'type' => 'text',
                    'label' => 'input fixed-width-xl',
                    'name' => 'type_text_xl',
                    'class' => 'input fixed-width-xl',
                    'tab' => 'large',
                ),
                array(
                    'type' => 'text',
                    'label' => 'input fixed-width-xxl',
                    'name' => 'type_text_xxl',
                    'class' => 'fixed-width-xxl',
                    'tab' => 'large',
                ),
                array(
                    'type' => 'free',
                    'label' => 'About tabs',
                    'name' => 'tab_note',
                    'tab' => 'small',
                ),
                array(
                    'type' => 'text',
                    'label' => 'input fixed-width-md with prefix',
                    'name' => 'type_text_md',
                    'class' => 'input fixed-width-md',
                    'prefix' => 'prefix'
                ),
                array(
                    'type' => 'text',
                    'label' => 'input fixed-width-md with sufix',
                    'name' => 'type_text_md',
                    'class' => 'input fixed-width-md',
                    'suffix' => 'suffix'
                ),
                array(
                    'type' => 'tags',
                    'label' => 'input tags',
                    'name' => 'type_text_tags'
                ),
                array(
                    'type' => 'textbutton',
                    'label' => 'input with button',
                    'name' => 'type_textbutton',
                    'button' => array(
                        'label' => 'do something',
                        'attributes' => array(
                            'onclick' => 'alert(\'something done\');'
                        )
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => 'select',
                    'name' => 'type_select',
                    'options' => array(
                        'query' => Zone::getZones(),
                        'id' => 'id_zone',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => 'select with chosen',
                    'name' => 'type_select_chosen',
                    'class' => 'chosen',
                    'options' => array(
                        'query' => Country::getCountries((int)Context::getContext()->cookie->id_lang),
                        'id' => 'id_zone',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => 'select multiple with chosen',
                    'name' => 'type_select_multiple_chosen',
                    'class' => 'chosen',
                    'multiple' => true,
                    'options' => array(
                        'query' => Country::getCountries((int)Context::getContext()->cookie->id_lang),
                        'id' => 'id_zone',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'radio',
                    'label' => 'radios',
                    'name' => 'type_radio',
                    'values' => array(
                        array(
                            'id' => 'type_male',
                            'value' => 0,
                            'label' => 'first'
                        ),
                        array(
                            'id' => 'type_female',
                            'value' => 1,
                            'label' => 'second'
                        ),
                        array(
                            'id' => 'type_neutral',
                            'value' => 2,
                            'label' => 'third'
                        )
                    )
                ),
                array(
                    'type' => 'checkbox',
                    'label' => 'checkbox',
                    'name' => 'type_checkbox',
                    'values' => array(
                        'query' => Zone::getZones(),
                        'id' => 'id_zone',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => 'switch',
                    'name' => 'type_switch',
                    'values' => array(
                        array(
                            'id' => 'type_switch_on',
                            'value' => 1
                        ),
                        array(
                            'id' => 'type_switch_off',
                            'value' => 0
                        )
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => 'switch disabled',
                    'name' => 'type_switch_disabled',
                    'disabled' => 'true',
                    'values' => array(
                        array(
                            'id' => 'type_switch_disabled_on',
                            'value' => 1
                        ),
                        array(
                            'id' => 'type_switch_disabled_off',
                            'value' => 0
                        )
                    )
                ),
                array(
                    'type' => 'textarea',
                    'label' => 'text area (with autoresize)',
                    'name' => 'type_textarea'
                ),
                array(
                    'type' => 'textarea',
                    'label' => 'text area with rich text editor',
                    'name' => 'type_textarea_rte',
                    'autoload_rte' => true
                ),
                array(
                    'type' => 'password',
                    'label' => 'input password',
                    'name' => 'type_password'
                ),
                array(
                    'type' => 'birthday',
                    'label' => 'input birthday',
                    'name' => 'type_birthday',
                    'options' => array(
                        'days' => Tools::dateDays(),
                        'months' => Tools::dateMonths(),
                        'years' => Tools::dateYears()
                    )
                ),
                array(
                    'type' => 'group',
                    'label' => 'group',
                    'name' => 'type_group',
                    'values' => Group::getGroups(Context::getContext()->language->id)
                ),
                array(
                    'type' => 'categories',
                    'label' => 'tree categories',
                    'name' => 'type_categories',
                    'tree' => array(
                        'root_category' => 1,
                        'id' => 'id_category',
                        'name' => 'name_category',
                        'selected_categories' => array(3),
                    )
                ),
                array(
                    'type' => 'file',
                    'label' => 'input file',
                    'name' => 'type_file'
                ),
                array(
                    'type' => 'color',
                    'label' => 'input color',
                    'name' => 'type_color'
                ),
                array(
                    'type' => 'date',
                    'label' => 'input date',
                    'name' => 'type_date'
                ),
                array(
                    'type' => 'datetime',
                    'label' => 'input date and time',
                    'name' => 'type_datetime'
                ),
                array(
                    'type' => 'html',
                    'name' => 'html_data',
                    'html_content' => '<hr><strong>html:</strong> for writing free html like this <span class="label label-danger">i\'m a label</span> <span class="badge badge-info">i\'m a badge</span> <button type="button" class="btn btn-default">i\'m a button</button><hr>'
                ),
                array(
                    'type' => 'free',
                    'label' => 'input free',
                    'name' => 'type_free'
                ),
                //...
            ),
            'submit' => array(
                'title' => 'Save',
            ),
            'buttons' => array(),
        );

        return parent::renderForm();
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addjQueryPlugin('tagify', null, false);
    }

    public function renderList()
    {
        $return  = '';

        $return .= $this->renderListSimpleHeader();
        $return .= $this->renderListSmallColumns();
        $return .= $this->renderListWithParentClass();

        return $return;
    }

    public function renderListSimpleHeader()
    {
        $content = array(
            array(
                "id_carrier" => 5,
                "name" => "Lorem ipsum dolor, sit amet, consectetur adipiscing elit. Nunc lacinia in enim iaculis malesuada. Quisque congue ferm",
                "type_name" => "Azerty",
                "active" => 1,
            ),
            array(
                "id_carrier" => 6,
                "name" => "Lorem ipsum dolor sit amet, consectetur lacinia in enim iaculis malesuada. Quisque congue ferm",
                "type_name" => "Qwerty",
                "active" => 1,
            ),
            array(
                "id_carrier" => 9,
                "name" => "Lorem ipsum dolor sit amet: \ / : * ? \" < > |",
                "type_name" => "Azerty",
                "active" => 0,
            ),
            array(
                "id_carrier" => 3,
                "name" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc lacinia in enim iaculis malesuada. Quisque congue ferm",
                "type_name" => "Azerty",
                "active" => 1,
            ),
        );

        $fields_list = array(
            'id_carrier' => array(
                'title' => 'ID',
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => 'Name'
            ),
            'type_name' => array(
                'title' => 'Type',
                'type' => 'text',
            ),
            'active' => array(
                'title' => 'Status',
                'active' => 'status',
                'type' => 'bool',
            ),
        );

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->actions = array("edit", "delete");
        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->listTotal = count($content);
        $helper->identifier = 'id_carrier';
        $helper->title = 'This list use a simple Header with no toolbar';
        $helper->table = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        return $helper->generateList($content, $fields_list);
    }

    public function renderListSmallColumns()
    {
        $content = array(
            array(
                'id' => 5,
                'badge_success' => 153,
                'badge_warning' => 6,
                'badge_danger' => -2,
                'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'color_value' => 'red',
                'blue' => 'Content in custom color in blue field',
                'activeVisu_field' => 1,
                'editable_text' => "PrestaShop",
            ),
            array(
                'id' => 1,
                'badge_success' => 15561533,
                'badge_warning' => 0,
                'badge_danger' => 0,
                'text' => 'Lorem ip, consectetur adipiscing elit.',
                'color_value' => 'blue',
                'blue' => 'Content in custom color in blue field',
                'activeVisu_field' => 0,
                'editable_text' => "PrestaShop",
            ),
            array(
                'id' => 2,
                'badge_success' => 0,
                'badge_warning' => 65,
                'badge_danger' => -200,
                'text' => 'WITH VERY LONG TEXT: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. ',
                'color_value' => 'yellow',
                'blue' => 'Content in custom color in blue field',
                'activeVisu_field' => 1,
                'editable_text' => "PrestaShop Lorem ipsum dolor sit amet, consectetur adipiscing elit. ",
            ),
            array(
                'id' => 9,
                'badge_success' => 3,
                'badge_warning' => 2,
                'badge_danger' => 1,
                'text' => "WITH HTML: <br> <strong>strong</strong> <span style='background: black;'>span content</span>",
                'color_value' => '#CCCC99',
                'blue' => 'Content in custom color in blue field',
                'activeVisu_field' => 1,
                'editable_text' => "PrestaShop",
            ),
        );

        $fields_list = array(
            'id' => array(
                'title' => 'ID',
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'badge_success' => array(
                'title' => "Success",
                'badge_success' => true,
            ),
            'badge_warning' => array(
                'title' => "Warning",
                'badge_warning' => true,
            ),
            'badge_danger' => array(
                'title' => "Danger",
                'badge_danger' => true,
            ),
            'text' => array(
                'title' => "Content with prefix",
                'prefix' => "This is a prefix: ",
                'class' => "class-prefix",
            ),
            'blue' => array(
                'title' => "Content with no link",
                'color' => "color_value",
                'class' => "class-custom-nolink",
            ),
            'activeVisu_field' => array(
                'title' => "ActiveVisu",
                'activeVisu' => true,

            ),
            'editable_text' => array(
                'title' => "edit this !",
                'type' => 'editable',
                'class' => 'another-custom_class',
            ),
        );

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->actions = array();
        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->listTotal = count($content);
        $helper->identifier = 'id';
        $helper->title = 'This list shows a lot of small columns';
        $helper->table = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        return $helper->generateList($content, $fields_list);
    }

    public function renderListModel()
    {
        $content = array();

        $fields_list = array();

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->actions = null;
        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->listTotal = count($content);
        $helper->identifier = 'id_product_comment';
        $helper->title = 'Moderate Comments';
        $helper->table = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        return $helper->generateList($content, $fields_list);
    }

    public function renderListWithParentClass()
    {
        $this->bulk_actions = array(
            'delete' => array(
                'text' => 'Delete selected',
                'confirm' => 'Delete selected items?',
                'icon' => 'icon-trash'
            )
        );
        $this->fields_list = array(
            'id_carrier' => array(
                'title' => 'ID',
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'image' => array(
                'title' => 'Logo',
                'align' => 'center',
                'image' => 's',
                'class' => 'fixed-width-xs',
                'orderby' => false,
                'search' => false
            ),
            'name' => array(
                'title' => 'Name'
            ),
        );

        return parent::renderList();
    }

    public function renderOptions()
    {
        $this->fields_options = array(
            'general' => array(
                'title' =>    'General',
                'icon' =>    'icon-cogs',
                'fields' =>    array(),
                'submit' => array('title' => 'Save')
            )
        );
        return parent::renderOptions();
    }

    public function initContent()
    {
        $this->display = 'view';
        $this->page_header_toolbar_title = $this->toolbar_title = 'Patterns design sample';

        parent::initContent();

        $this->content .= $this->renderForm();
        $this->content .= $this->renderList();
        $this->content .= $this->renderOptions();

        $this->context->smarty->assign(array('content' => $this->content));
    }
}
