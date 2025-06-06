<?php
class AdminCustomheaderlogoSettingController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'configuration'; // Dummy table
        $this->className = 'Configuration'; // Dummy class
        $this->identifier = 'id_configuration'; // Dummy identifier
        $this->context = Context::getContext();
        $this->module_name = 'blockheader'; // Module name for translations

        parent::__construct();

        // Define configuration keys
        $this->cfg_keys = [
            'BH_LOGO' => '', // BlockHeader Logo
            'BH_BRAND_TEXT1' => '',
            'BH_BRAND_TEXT2' => '',
            'BH_PHONE_NUMBER' => '',
            'BH_DESKTOP_LINK1_TEXT' => '',
            'BH_DESKTOP_LINK1_URL' => '',
            'BH_DESKTOP_LINK2_TEXT' => '',
            'BH_DESKTOP_LINK2_URL' => '',
            'BH_DESKTOP_CTA_TEXT' => '',
            'BH_DESKTOP_CTA_URL' => '',
            'BH_MOBILE_BG_IMAGE' => '',
            'BH_MOBILE_LINK1_ICON' => '',
            'BH_MOBILE_LINK1_TEXT' => '',
            'BH_MOBILE_LINK1_URL' => '',
            'BH_MOBILE_LINK2_ICON' => '',
            'BH_MOBILE_LINK2_TEXT' => '',
            'BH_MOBILE_LINK2_URL' => '',
            'BH_MOBILE_LINK3_ICON' => '',
            'BH_MOBILE_LINK3_TEXT' => '',
            'BH_MOBILE_LINK3_URL' => '',
            'BH_NAV_LINK1_TEXT' => '',
            'BH_NAV_LINK1_URL' => '',
            'BH_NAV_LINK1_ACTIVE' => '1',
            'BH_NAV_LINK2_TEXT' => '',
            'BH_NAV_LINK2_URL' => '',
            'BH_NAV_LINK2_ACTIVE' => '1',
            'BH_NAV_LINK3_TEXT' => '',
            'BH_NAV_LINK3_URL' => '',
            'BH_NAV_LINK3_ACTIVE' => '1',
            'BH_NAV_LINK4_TEXT' => '',
            'BH_NAV_LINK4_URL' => '',
            'BH_NAV_LINK4_ACTIVE' => '0',
            'BH_NAV_LINK5_TEXT' => '',
            'BH_NAV_LINK5_URL' => '',
            'BH_NAV_LINK5_ACTIVE' => '0',
        ];
    }

    public function initContent()
    {
        parent::initContent();
        $this->renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submit'.$this->module_name.'Configuration')) {
            $errors = array();

            // Handle file upload for Mobile Background Image
            if (isset($_FILES['BH_MOBILE_BG_IMAGE']) && !empty($_FILES['BH_MOBILE_BG_IMAGE']['tmp_name'])) {
                $uploader_mobile_bg = new Uploader('BH_MOBILE_BG_IMAGE');
                $uploader_mobile_bg->setAcceptTypes(array('jpeg', 'jpg', 'png', 'gif'));
                $uploader_mobile_bg->setSavePath(_PS_MODULE_DIR_ . $this->module->name . '/views/img/');
                $file_mobile_bg = $uploader_mobile_bg->process();

                if ($file_mobile_bg && !count($file_mobile_bg[0]['errors'])) {
                    $old_mobile_bg = Configuration::get('BH_MOBILE_BG_IMAGE');
                    if ($old_mobile_bg && $old_mobile_bg != $file_mobile_bg[0]['name'] && file_exists(_PS_MODULE_DIR_ . $this->module->name . '/views/img/' . $old_mobile_bg)) {
                        @unlink(_PS_MODULE_DIR_ . $this->module->name . '/views/img/' . $old_mobile_bg);
                    }
                    Configuration::updateValue('BH_MOBILE_BG_IMAGE', $file_mobile_bg[0]['name']);
                } else {
                    $errors = array_merge($errors, $file_mobile_bg[0]['errors']);
                }
            }
            // Handle file upload for logo
            if (isset($_FILES['BH_LOGO']) && !empty($_FILES['BH_LOGO']['tmp_name'])) {
                $uploader = new Uploader('BH_LOGO');
                $uploader->setAcceptTypes(array('jpeg', 'jpg', 'png', 'gif', 'svg'));
                $uploader->setSavePath(_PS_MODULE_DIR_ . $this->module->name . '/views/img/'); // Save in module's img dir
                $file = $uploader->process();

                if ($file && !count($file[0]['errors'])) {
                    // Delete old logo if it exists and is different
                    $old_logo = Configuration::get('BH_LOGO');
                    if ($old_logo && $old_logo != $file[0]['name'] && file_exists(_PS_MODULE_DIR_ . $this->module->name . '/views/img/' . $old_logo)) {
                        @unlink(_PS_MODULE_DIR_ . $this->module->name . '/views/img/' . $old_logo);
                    }
                    Configuration::updateValue('BH_LOGO', $file[0]['name']);
                } else {
                    $errors = array_merge($errors, $file[0]['errors']);
                }
            }

            // Update text values
            Configuration::updateValue('BH_BRAND_TEXT1', Tools::getValue('BH_BRAND_TEXT1'));
            Configuration::updateValue('BH_BRAND_TEXT2', Tools::getValue('BH_BRAND_TEXT2'));
            Configuration::updateValue('BH_PHONE_NUMBER', Tools::getValue('BH_PHONE_NUMBER'));
            Configuration::updateValue('BH_DESKTOP_LINK1_TEXT', Tools::getValue('BH_DESKTOP_LINK1_TEXT'));
            Configuration::updateValue('BH_DESKTOP_LINK1_URL', Tools::getValue('BH_DESKTOP_LINK1_URL'));
            Configuration::updateValue('BH_DESKTOP_LINK2_TEXT', Tools::getValue('BH_DESKTOP_LINK2_TEXT'));
            Configuration::updateValue('BH_DESKTOP_LINK2_URL', Tools::getValue('BH_DESKTOP_LINK2_URL'));
            Configuration::updateValue('BH_DESKTOP_CTA_TEXT', Tools::getValue('BH_DESKTOP_CTA_TEXT'));
            Configuration::updateValue('BH_DESKTOP_CTA_URL', Tools::getValue('BH_DESKTOP_CTA_URL'));
            Configuration::updateValue('BH_MOBILE_LINK1_ICON', Tools::getValue('BH_MOBILE_LINK1_ICON'));
            Configuration::updateValue('BH_MOBILE_LINK1_TEXT', Tools::getValue('BH_MOBILE_LINK1_TEXT'));
            Configuration::updateValue('BH_MOBILE_LINK1_URL', Tools::getValue('BH_MOBILE_LINK1_URL'));
            Configuration::updateValue('BH_MOBILE_LINK2_ICON', Tools::getValue('BH_MOBILE_LINK2_ICON'));
            Configuration::updateValue('BH_MOBILE_LINK2_TEXT', Tools::getValue('BH_MOBILE_LINK2_TEXT'));
            Configuration::updateValue('BH_MOBILE_LINK2_URL', Tools::getValue('BH_MOBILE_LINK2_URL'));
            Configuration::updateValue('BH_MOBILE_LINK3_ICON', Tools::getValue('BH_MOBILE_LINK3_ICON'));
            Configuration::updateValue('BH_MOBILE_LINK3_TEXT', Tools::getValue('BH_MOBILE_LINK3_TEXT'));
            Configuration::updateValue('BH_MOBILE_LINK3_URL', Tools::getValue('BH_MOBILE_LINK3_URL'));
            for ($i = 1; $i <= 5; ++$i) {
                Configuration::updateValue('BH_NAV_LINK'.$i.'_TEXT', Tools::getValue('BH_NAV_LINK'.$i.'_TEXT'));
                Configuration::updateValue('BH_NAV_LINK'.$i.'_URL', Tools::getValue('BH_NAV_LINK'.$i.'_URL'));
                Configuration::updateValue('BH_NAV_LINK'.$i.'_ACTIVE', Tools::getValue('BH_NAV_LINK'.$i.'_ACTIVE', 0));
            }

            if (count($errors)) {
                $this->errors = array_merge($this->errors, $errors);
            } else {
                $this->confirmations[] = $this->l('Settings updated successfully.');
            }
        }
        parent::postProcess();
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Logo & Branding Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'file',
                        'label' => $this->l('Header Logo'),
                        'name' => 'BH_LOGO',
                        'desc' => $this->l('Upload a logo for the header. (SVG, PNG, JPG, GIF)'),
                        'display_image' => true,
                        'image' => Configuration::get('BH_LOGO') ? '../modules/'.$this->module->name.'/views/img/'.Configuration::get('BH_LOGO') : false,
                        'thumb' => Configuration::get('BH_LOGO') ? '../modules/'.$this->module->name.'/views/img/'.Configuration::get('BH_LOGO') : false,
                        'delete_url' => $this->context->link->getAdminLink('AdminCustomheaderlogoSetting').'&deleteLogo=1', // Optional: Add delete functionality
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Branding Text Line 1'),
                        'name' => 'BH_BRAND_TEXT1',
                        'desc' => $this->l('First line of text below the logo.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Branding Text Line 2'),
                        'name' => 'BH_BRAND_TEXT2',
                        'desc' => $this->l('Second line of text below branding text 1.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Desktop Phone Number'),
                        'name' => 'BH_PHONE_NUMBER',
                        'desc' => $this->l('Phone number displayed in the desktop header.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Desktop Left Link 1 - Text'),
                        'name' => 'BH_DESKTOP_LINK1_TEXT',
                        'desc' => $this->l('Text for the first link on the left side of the desktop header.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Desktop Left Link 1 - URL'),
                        'name' => 'BH_DESKTOP_LINK1_URL',
                        'desc' => $this->l('URL for the first link on the left. Include http(s)://'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Desktop Left Link 2 - Text'),
                        'name' => 'BH_DESKTOP_LINK2_TEXT',
                        'desc' => $this->l('Text for the second link on the left side of the desktop header.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Desktop Left Link 2 - URL'),
                        'name' => 'BH_DESKTOP_LINK2_URL',
                        'desc' => $this->l('URL for the second link on the left. Include http(s)://'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Desktop Right CTA Button - Text'),
                        'name' => 'BH_DESKTOP_CTA_TEXT',
                        'desc' => $this->l('Text for the Call-to-Action button on the right.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Desktop Right CTA Button - URL'),
                        'name' => 'BH_DESKTOP_CTA_URL',
                        'desc' => $this->l('URL for the Call-to-Action button. Include http(s)://'),
                    ),
                    // New fields for Mobile Specifics will be inserted here by the script
                    array(
                        'type' => 'file',
                        'label' => $this->l('Mobile Header Background Image (Optional)'),
                        'name' => 'BH_MOBILE_BG_IMAGE',
                        'desc' => $this->l('Upload a background image for the mobile header overlay section.'),
                        'display_image' => true,
                        'image' => Configuration::get('BH_MOBILE_BG_IMAGE') ? '../modules/'.$this->module->name.'/views/img/'.Configuration::get('BH_MOBILE_BG_IMAGE') : false,
                        'thumb' => Configuration::get('BH_MOBILE_BG_IMAGE') ? '../modules/'.$this->module->name.'/views/img/'.Configuration::get('BH_MOBILE_BG_IMAGE') : false,
                        'delete_url' => $this->context->link->getAdminLink('AdminCustomheaderlogoSetting').'&deleteMobileBgImage=1',
                    ),
                    array('type' => 'html', 'name' => 'bh_mobile_links_separator_start', 'html_content' => '<hr><h4>'.$this->l('Mobile Bottom Bar Link 1').'</h4>'),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link 1 - Icon Class'),
                        'name' => 'BH_MOBILE_LINK1_ICON',
                        'desc' => $this->l('e.g., icon-home, fa fa-bars. Provided by your theme or icon font.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link 1 - Text'),
                        'name' => 'BH_MOBILE_LINK1_TEXT',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link 1 - URL'),
                        'name' => 'BH_MOBILE_LINK1_URL',
                        'desc' => $this->l('Include http(s)://'),
                    ),
                    array('type' => 'html', 'name' => 'bh_mobile_links_separator_mid1', 'html_content' => '<hr><h4>'.$this->l('Mobile Bottom Bar Link 2').'</h4>'),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link 2 - Icon Class'),
                        'name' => 'BH_MOBILE_LINK2_ICON',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link 2 - Text'),
                        'name' => 'BH_MOBILE_LINK2_TEXT',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link 2 - URL'),
                        'name' => 'BH_MOBILE_LINK2_URL',
                    ),
                    array('type' => 'html', 'name' => 'bh_mobile_links_separator_mid2', 'html_content' => '<hr><h4>'.$this->l('Mobile Bottom Bar Link 3 (CTA)').'</h4>'),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link 3 (CTA) - Icon Class'),
                        'name' => 'BH_MOBILE_LINK3_ICON',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link 3 (CTA) - Text'),
                        'name' => 'BH_MOBILE_LINK3_TEXT',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link 3 (CTA) - URL'),
                        'name' => 'BH_MOBILE_LINK3_URL',
                    ),
                    array('type' => 'html', 'name' => 'bh_mobile_links_separator_end', 'html_content' => '<hr>'),
                    array('type' => 'html', 'name' => 'bh_nav_links_separator_start', 'html_content' => '<h3>'.$this->l('Main Navigation Links (Desktop & Mobile)').'</h3><p>'.$this->l('Configure up to 5 main navigation links. These will be displayed in the desktop navigation bar and the mobile slide-out menu.').'</p>'),
                    array('type' => 'html', 'name' => 'bh_nav_link1_header', 'html_content' => '<hr><h4>'.sprintf($this->l('Navigation Link %d'), 1).'</h4>'),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display Link '. 1),
                        'name' => 'BH_NAV_LINK1_ACTIVE',
                        'is_bool' => true,
                        'values' => array(
                            array('id' => 'active_on', 'value' => 1, 'label' => $this->l('Enabled')),
                            array('id' => 'active_off', 'value' => 0, 'label' => $this->l('Disabled')),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link '. 1 .' - Text'),
                        'name' => 'BH_NAV_LINK1_TEXT',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link '. 1 .' - URL'),
                        'name' => 'BH_NAV_LINK1_URL',
                        'desc' => $this->l('Include http(s):// or use PrestaShop page identifiers like contact,authentication,my-account,cms:X (for CMS ID X), category:X (for Cat ID X)'),
                    ),
                    array('type' => 'html', 'name' => 'bh_nav_link2_header', 'html_content' => '<hr><h4>'.sprintf($this->l('Navigation Link %d'), 2).'</h4>'),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display Link '. 2),
                        'name' => 'BH_NAV_LINK2_ACTIVE',
                        'is_bool' => true,
                        'values' => array(
                            array('id' => 'active_on', 'value' => 1, 'label' => $this->l('Enabled')),
                            array('id' => 'active_off', 'value' => 0, 'label' => $this->l('Disabled')),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link '. 2 .' - Text'),
                        'name' => 'BH_NAV_LINK2_TEXT',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link '. 2 .' - URL'),
                        'name' => 'BH_NAV_LINK2_URL',
                        'desc' => $this->l('Include http(s):// or use PrestaShop page identifiers like contact,authentication,my-account,cms:X (for CMS ID X), category:X (for Cat ID X)'),
                    ),
                    array('type' => 'html', 'name' => 'bh_nav_link3_header', 'html_content' => '<hr><h4>'.sprintf($this->l('Navigation Link %d'), 3).'</h4>'),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display Link '. 3),
                        'name' => 'BH_NAV_LINK3_ACTIVE',
                        'is_bool' => true,
                        'values' => array(
                            array('id' => 'active_on', 'value' => 1, 'label' => $this->l('Enabled')),
                            array('id' => 'active_off', 'value' => 0, 'label' => $this->l('Disabled')),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link '. 3 .' - Text'),
                        'name' => 'BH_NAV_LINK3_TEXT',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link '. 3 .' - URL'),
                        'name' => 'BH_NAV_LINK3_URL',
                        'desc' => $this->l('Include http(s):// or use PrestaShop page identifiers like contact,authentication,my-account,cms:X (for CMS ID X), category:X (for Cat ID X)'),
                    ),
                    array('type' => 'html', 'name' => 'bh_nav_link4_header', 'html_content' => '<hr><h4>'.sprintf($this->l('Navigation Link %d'), 4).'</h4>'),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display Link '. 4),
                        'name' => 'BH_NAV_LINK4_ACTIVE',
                        'is_bool' => true,
                        'values' => array(
                            array('id' => 'active_on', 'value' => 1, 'label' => $this->l('Enabled')),
                            array('id' => 'active_off', 'value' => 0, 'label' => $this->l('Disabled')),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link '. 4 .' - Text'),
                        'name' => 'BH_NAV_LINK4_TEXT',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link '. 4 .' - URL'),
                        'name' => 'BH_NAV_LINK4_URL',
                        'desc' => $this->l('Include http(s):// or use PrestaShop page identifiers like contact,authentication,my-account,cms:X (for CMS ID X), category:X (for Cat ID X)'),
                    ),
                    array('type' => 'html', 'name' => 'bh_nav_link5_header', 'html_content' => '<hr><h4>'.sprintf($this->l('Navigation Link %d'), 5).'</h4>'),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display Link '. 5),
                        'name' => 'BH_NAV_LINK5_ACTIVE',
                        'is_bool' => true,
                        'values' => array(
                            array('id' => 'active_on', 'value' => 1, 'label' => $this->l('Enabled')),
                            array('id' => 'active_off', 'value' => 0, 'label' => $this->l('Disabled')),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link '. 5 .' - Text'),
                        'name' => 'BH_NAV_LINK5_TEXT',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link '. 5 .' - URL'),
                        'name' => 'BH_NAV_LINK5_URL',
                        'desc' => $this->l('Include http(s):// or use PrestaShop page identifiers like contact,authentication,my-account,cms:X (for CMS ID X), category:X (for Cat ID X)'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submit'.$this->module_name.'Configuration',
                ),
            ),
        );

        $helper = new HelperForm();
        $helper->module = $this->module;
        $helper->name_controller = $this->module_name.'Configuration'; // Unique name for the form
        $helper->token = Tools::getAdminTokenLite('AdminCustomheaderlogoSetting');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->module->name;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit'.$this->module_name.'Configuration';

        // Load current values
        $helper->fields_value['BH_LOGO'] = Configuration::get('BH_LOGO');
        $helper->fields_value['BH_BRAND_TEXT1'] = Configuration::get('BH_BRAND_TEXT1');
        $helper->fields_value['BH_BRAND_TEXT2'] = Configuration::get('BH_BRAND_TEXT2');
        $helper->fields_value['BH_PHONE_NUMBER'] = Configuration::get('BH_PHONE_NUMBER');
        $helper->fields_value['BH_DESKTOP_LINK1_TEXT'] = Configuration::get('BH_DESKTOP_LINK1_TEXT');
        $helper->fields_value['BH_DESKTOP_LINK1_URL'] = Configuration::get('BH_DESKTOP_LINK1_URL');
        $helper->fields_value['BH_DESKTOP_LINK2_TEXT'] = Configuration::get('BH_DESKTOP_LINK2_TEXT');
        $helper->fields_value['BH_DESKTOP_LINK2_URL'] = Configuration::get('BH_DESKTOP_LINK2_URL');
        $helper->fields_value['BH_DESKTOP_CTA_TEXT'] = Configuration::get('BH_DESKTOP_CTA_TEXT');
        $helper->fields_value['BH_DESKTOP_CTA_URL'] = Configuration::get('BH_DESKTOP_CTA_URL');
        $helper->fields_value['BH_MOBILE_BG_IMAGE'] = Configuration::get('BH_MOBILE_BG_IMAGE');
        $helper->fields_value['BH_MOBILE_LINK1_ICON'] = Configuration::get('BH_MOBILE_LINK1_ICON');
        $helper->fields_value['BH_MOBILE_LINK1_TEXT'] = Configuration::get('BH_MOBILE_LINK1_TEXT');
        $helper->fields_value['BH_MOBILE_LINK1_URL'] = Configuration::get('BH_MOBILE_LINK1_URL');
        $helper->fields_value['BH_MOBILE_LINK2_ICON'] = Configuration::get('BH_MOBILE_LINK2_ICON');
        $helper->fields_value['BH_MOBILE_LINK2_TEXT'] = Configuration::get('BH_MOBILE_LINK2_TEXT');
        $helper->fields_value['BH_MOBILE_LINK2_URL'] = Configuration::get('BH_MOBILE_LINK2_URL');
        $helper->fields_value['BH_MOBILE_LINK3_ICON'] = Configuration::get('BH_MOBILE_LINK3_ICON');
        $helper->fields_value['BH_MOBILE_LINK3_TEXT'] = Configuration::get('BH_MOBILE_LINK3_TEXT');
        $helper->fields_value['BH_MOBILE_LINK3_URL'] = Configuration::get('BH_MOBILE_LINK3_URL');
        for ($i = 1; $i <= 5; ++$i) {
            $helper->fields_value['BH_NAV_LINK'.$i.'_TEXT'] = Configuration::get('BH_NAV_LINK'.$i.'_TEXT');
            $helper->fields_value['BH_NAV_LINK'.$i.'_URL'] = Configuration::get('BH_NAV_LINK'.$i.'_URL');
            $helper->fields_value['BH_NAV_LINK'.$i.'_ACTIVE'] = Configuration::get('BH_NAV_LINK'.$i.'_ACTIVE');
        }

        $this->content .= $helper->generateForm(array($fields_form));
    }

    // Optional: Add logic for deleting logo if delete_url is used
    public function processDeleteLogo()
    {
        if (Tools::isSubmit('deleteLogo')) {
            $logo_name = Configuration::get('BH_LOGO');
            if ($logo_name && file_exists(_PS_MODULE_DIR_ . $this->module->name . '/views/img/' . $logo_name)) {
                if (unlink(_PS_MODULE_DIR_ . $this->module->name . '/views/img/' . $logo_name)) {
                    Configuration::updateValue('BH_LOGO', null);
                    $this->confirmations[] = $this->l('Logo deleted successfully.');
                } else {
                    $this->errors[] = $this->l('Error deleting logo file.');
                }
            } else {
                $this->errors[] = $this->l('Logo file not found.');
            }
            // Redirect to avoid re-deletion on refresh or handle via AJAX
             Tools::redirectAdmin($this->context->link->getAdminLink('AdminCustomheaderlogoSetting').'&conf=4');
        }
    }

     public function initProcess()
    {
        if (Tools::isSubmit('deleteLogo')) {
            $this->processDeleteLogo();
        }
        parent::initProcess();
    }

    // Optional: Add logic for deleting mobile background image if delete_url is used
    public function processDeleteMobileBgImage()
    {
        if (Tools::isSubmit('deleteMobileBgImage')) {
            $image_name = Configuration::get('BH_MOBILE_BG_IMAGE');
            if ($image_name && file_exists(_PS_MODULE_DIR_ . $this->module->name . '/views/img/' . $image_name)) {
                if (unlink(_PS_MODULE_DIR_ . $this->module->name . '/views/img/' . $image_name)) {
                    Configuration::updateValue('BH_MOBILE_BG_IMAGE', null);
                    $this->confirmations[] = $this->l('Mobile background image deleted successfully.');
                } else {
                    $this->errors[] = $this->l('Error deleting mobile background image file.');
                }
            } else {
                $this->errors[] = $this->l('Mobile background image file not found.');
            }
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminCustomheaderlogoSetting').'&conf=4');
        }
    }
}
