<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Blockheader extends Module
{
    public function __construct()
    {
        $this->name = 'blockheader'; // Corrected name to lowercase 'blockheader'
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Your Name'; // Placeholder, will be filled by user if needed
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminCustomheaderlogoSetting'));
    }

        $this->displayName = $this->l('Custom Header Block');
        $this->description = $this->l('Adds a custom sticky header with desktop and mobile views.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); // QloApps is based on PS 1.6
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerHooks()
            || !$this->installTab('AdminCustomheaderlogoSetting', 'Header Logo & Branding')) {
            return false;
        }

        // Default configurations
        Configuration::updateValue('BH_BRAND_TEXT1', 'Your Brand');
        Configuration::updateValue('BH_PHONE_NUMBER', 'Call Us: 123-456-7890');
        Configuration::updateValue('BH_DESKTOP_LINK1_TEXT', 'About Us');
        Configuration::updateValue('BH_DESKTOP_LINK1_URL', Context::getContext()->link->getPageLink('cms', true, null, 'id_cms=4'));
        Configuration::updateValue('BH_DESKTOP_CTA_TEXT', 'Contact Us');
        Configuration::updateValue('BH_DESKTOP_CTA_URL', Context::getContext()->link->getPageLink('contact', true));
        Configuration::updateValue('BH_MOBILE_LINK1_ICON', 'fa fa-home');
        Configuration::updateValue('BH_MOBILE_LINK1_TEXT', 'Home');
        Configuration::updateValue('BH_MOBILE_LINK1_URL', Context::getContext()->link->getPageLink('index', true));
        Configuration::updateValue('BH_MOBILE_LINK3_ICON', 'fa fa-envelope');
        Configuration::updateValue('BH_MOBILE_LINK3_TEXT', 'Quote');
        Configuration::updateValue('BH_MOBILE_LINK3_URL', Context::getContext()->link->getPageLink('contact', true));

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            // || !Configuration::deleteByName('BH_LOGO') // Decide if configs should be deleted
            || !$this->uninstallTab('AdminCustomheaderlogoSetting')) {
            return false;
        }
        return true;
    }

    // Placeholder for hookDisplayHeader
    // public function hookDisplayHeader($params)
    // {
    //     \$this->context->controller->addCSS(\$this->_path.'views/css/blockheader.css');
    //     \$this->context->controller->addJS(\$this->_path.'views/js/blockheader.js');
    // }

    // Placeholder for hookDisplayTop
    // public function hookDisplayTop(\$params)
    // {
    //     \$this->context->smarty->assign(array(
    //         'blockheader_logo' => Configuration::get('BLOCKHEADER_LOGO'),
    //         'blockheader_text1' => Configuration::get('BLOCKHEADER_BRAND_TEXT1'),
    //         // ... more variables
    //     ));
    //     return \$this->display(__FILE__, 'views/templates/hook/headerBlock.tpl');
    // }

    // Helper function to install an Admin Tab
    private function installTab(\$className, \$tabName, \$tabParentName = false)
    {
        \$tab = new Tab();
        \$tab->active = 1;
        \$tab->class_name = \$className;
        \$tab->name = array();
        foreach (Language::getLanguages(true) as \$lang) {
            \$tab->name[\$lang['id_lang']] = \$tabName;
        }
        if (\$tabParentName) {
            \$tab->id_parent = (int)Tab::getIdFromClassName(\$tabParentName);
        } else {
            // Attempt to find a generic parent tab like "Modules" or a design/theme related one
            \$parentId = Tab::getIdFromClassName('AdminParentModulesSf'); // Common parent for module configurations
            if (!\$parentId) {
                \$parentId = Tab::getIdFromClassName('AdminParentThemes');
            }
            if (!\$parentId) {
                \$parentId = Tab::getIdFromClassName('AdminModules'); // Older PrestaShop
            }
             if (!\$parentId) {
                \$parentId = (int)Tab::getIdFromClassName('DEFAULT'); // Fallback
            }
            \$tab->id_parent = \$parentId;
        }
        \$tab->module = \$this->name;
        return \$tab->add();
    }

    // Helper function to uninstall an Admin Tab
    private function uninstallTab(\$className)
    {
        \$id_tab = (int)Tab::getIdFromClassName(\$className);
        if (\$id_tab) {
            \$tab = new Tab(\$id_tab);
            return \$tab->delete();
        }
        return true; // Return true if tab doesn't exist or already deleted
    }

    public function hookDisplayHeader($params)
    {
        $this->context->controller->addCSS($this->_path.'views/css/blockheader.css', 'all');
        $this->context->controller->addJS($this->_path.'views/js/blockheader.js');
        return null;
    }

    public function hookDisplayTop($params)
    {
        if (!$this->isCached('views/templates/hook/headerBlock.tpl', $this->getCacheId())) {
            $this->context->smarty->assign(array(
                'BH_LOGO' => Configuration::get('BH_LOGO'),
                'BH_BRAND_TEXT1' => Configuration::get('BH_BRAND_TEXT1'),
                'BH_BRAND_TEXT2' => Configuration::get('BH_BRAND_TEXT2'),
                'BH_PHONE_NUMBER' => Configuration::get('BH_PHONE_NUMBER'),
                'BH_DESKTOP_LINK1_TEXT' => Configuration::get('BH_DESKTOP_LINK1_TEXT'),
                'BH_DESKTOP_LINK1_URL' => Configuration::get('BH_DESKTOP_LINK1_URL'),
                'BH_DESKTOP_LINK2_TEXT' => Configuration::get('BH_DESKTOP_LINK2_TEXT'),
                'BH_DESKTOP_LINK2_URL' => Configuration::get('BH_DESKTOP_LINK2_URL'),
                'BH_DESKTOP_CTA_TEXT' => Configuration::get('BH_DESKTOP_CTA_TEXT'),
                'BH_DESKTOP_CTA_URL' => Configuration::get('BH_DESKTOP_CTA_URL'),
                'BH_MOBILE_BG_IMAGE' => Configuration::get('BH_MOBILE_BG_IMAGE'),
                'BH_MOBILE_LINK1_ICON' => Configuration::get('BH_MOBILE_LINK1_ICON'),
                'BH_MOBILE_LINK1_TEXT' => Configuration::get('BH_MOBILE_LINK1_TEXT'),
                'BH_MOBILE_LINK1_URL' => Configuration::get('BH_MOBILE_LINK1_URL'),
                'BH_MOBILE_LINK2_ICON' => Configuration::get('BH_MOBILE_LINK2_ICON'),
                'BH_MOBILE_LINK2_TEXT' => Configuration::get('BH_MOBILE_LINK2_TEXT'),
                'BH_MOBILE_LINK2_URL' => Configuration::get('BH_MOBILE_LINK2_URL'),
                'BH_MOBILE_LINK3_ICON' => Configuration::get('BH_MOBILE_LINK3_ICON'),
                'BH_MOBILE_LINK3_TEXT' => Configuration::get('BH_MOBILE_LINK3_TEXT'),
                'BH_MOBILE_LINK3_URL' => Configuration::get('BH_MOBILE_LINK3_URL'),
                'module_dir' => $this->_path,
                'shop_name' => Configuration::get('PS_SHOP_NAME')
            ));
        }
        return $this->display(__FILE__, 'views/templates/hook/headerBlock.tpl', $this->getCacheId());
    }

    private function registerHooks()
    {
        return $this->registerHook('displayHeader') &&
               $this->registerHook('displayTop') && /* Or displayNavFullWidth depending on theme */
               $this->registerHook('displayBlockHeaderNavigation') && /* Custom hook for desktop nav items */
               $this->registerHook('displayBlockHeaderMobileNavigation'); /* Custom hook for mobile nav items */
    }

    private function getBlockHeaderNavigationLinks()
    {
        $links = array();
        for ($i = 1; $i <= 5; ++$i) {
            if (Configuration::get('BH_NAV_LINK'.$i.'_ACTIVE')) {
                $text = Configuration::get('BH_NAV_LINK'.$i.'_TEXT');
                $url_conf = Configuration::get('BH_NAV_LINK'.$i.'_URL');
                $url = '';

                if (!empty($text) && !empty($url_conf)) {
                    // Check for PrestaShop specific links like contact, cms:ID, category:ID
                    if (strpos($url_conf, 'http://') === 0 || strpos($url_conf, 'https://') === 0) {
                        $url = $url_conf;
                    } elseif (strpos($url_conf, 'cms:') === 0) {
                        $id_cms = (int)str_replace('cms:', '', $url_conf);
                        $url = $this->context->link->getCMSLink($id_cms);
                    } elseif (strpos($url_conf, 'category:') === 0) {
                        $id_category = (int)str_replace('category:', '', $url_conf);
                        $url = $this->context->link->getCategoryLink($id_category);
                    } else { // Simple page names like 'contact', 'authentication'
                        $url = $this->context->link->getPageLink($url_conf, true);
                    }

                    $links[] = array(
                        'text' => $text,
                        'url' => $url,
                        'active' => true, // Already filtered by BH_NAV_LINKX_ACTIVE
                        // 'current' => false, // Add logic for current page highlighting if needed
                    );
                }
            }
        }
        return $links;
    }

    public function hookDisplayBlockHeaderNavigation($params)
    {
        if (!$this->isCached('views/templates/hook/_partials/navigation_links.tpl', $this->getCacheId('desktop_nav'))) {
            $custom_navigation_links = $this->getBlockHeaderNavigationLinks();
            $this->context->smarty->assign('custom_navigation_links', $custom_navigation_links);
        }
        return $this->display(__FILE__, 'views/templates/hook/_partials/navigation_links.tpl', $this->getCacheId('desktop_nav'));
    }

    public function hookDisplayBlockHeaderMobileNavigation($params)
    {
        // For now, mobile uses the same links and template. Can be customized later.
        if (!$this->isCached('views/templates/hook/_partials/navigation_links.tpl', $this->getCacheId('mobile_nav'))) {
            $custom_navigation_links = $this->getBlockHeaderNavigationLinks();
            $this->context->smarty->assign(array(
                'custom_navigation_links' => $custom_navigation_links,
                'mobile_nav_class' => true, // To potentially style mobile UL differently via CSS
            ));
        }
        return $this->display(__FILE__, 'views/templates/hook/_partials/navigation_links.tpl', $this->getCacheId('mobile_nav'));
    }
}
