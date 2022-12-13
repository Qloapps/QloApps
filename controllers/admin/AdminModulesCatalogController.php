<?php
/**
* 2010-2020 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminModulesCatalogControllerCore extends AdminController
{

    public $modules;

    const CATALOG_RECOMMENDATION_CONTENT = '/cache/catalog_recommendation.html';

    const ELEMENT_TYPE_MODULE = 1;
    const ELEMENT_TYPE_THEME = 2;

    const MODULES_PER_PAGE = 18;

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function init()
    {
        parent::init();
        $this->initSuggestedModulesList();
    }

    public function initContent()
    {
        parent::initContent();

        $suggestedModules = Module::getSuggestedModules();

        $modulesToAdd = array();
        $dirModules = ModuleCore::getModulesOnDisk();
        $modules_name = array_column($suggestedModules, 'name');
        foreach ($dirModules as $mod) {
            if (($id = array_search($mod->name, $modules_name)) !== false) {
                if ($mod->installed) {
                    unset($suggestedModules[$id]);
                } else {
                    $suggestedModules[$id]->not_on_disk = false;
                }
            } else {
                if (!$mod->installed) {
                    $modulesToAdd[] = $mod;
                }
            }
        }

        $modules = array_merge($suggestedModules, $modulesToAdd);

        $link_admin_modules = $this->context->link->getAdminLink('AdminModules', true);
        foreach ($modules as $key => $module) {
            $module->options['install_url'] = $link_admin_modules.'&install='.urlencode($module->name).'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor='.ucfirst($module->name);
            $module->element_type = self::ELEMENT_TYPE_MODULE;
            $module->logo = '../../img/questionmark.png';

            if (@filemtime(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.basename(_PS_MODULE_DIR_).DIRECTORY_SEPARATOR.$module->name
                .DIRECTORY_SEPARATOR.'logo.gif')) {
                $module->logo = 'logo.gif';
            }
            if (@filemtime(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.basename(_PS_MODULE_DIR_).DIRECTORY_SEPARATOR.$module->name
                .DIRECTORY_SEPARATOR.'logo.png')) {
                $module->logo = 'logo.png';
            }

            $modules[$key] = $module;
        }

        $this->sortList($modules, 'module');
        $this->modules = $modules;

        $this->themes = $this->getSuggestedThemes();

        $this->assignSortCriteria();

        $this->context->smarty->assign(array(
            'modules' => $this->modules,
            'themes' => $this->themes,
            'modules_uri' => __PS_BASE_URI__.basename(_PS_MODULE_DIR_),
            'element_type_module' => self::ELEMENT_TYPE_MODULE,
            'element_type_theme' => self::ELEMENT_TYPE_THEME,

        ));
    }

    public function initModal()
    {
        parent::initModal();

        $modal_content = $this->context->smarty->fetch('controllers/modules/'.(($this->context->mode == Context::MODE_HOST) ? 'modal_not_trusted_blocked.tpl' : 'modal_not_trusted.tpl'));
        $this->modals[] = array(
            'modal_id' => 'moduleNotTrusted',
            'modal_class' => 'modal-lg',
            'modal_title' => ($this->context->mode == Context::MODE_HOST) ? $this->l('This module cannot be installed') : $this->l('Important Notice'),
            'modal_content' => $modal_content
        );
    }

    public function assignSortCriteria()
    {
        $sortCriterta = array(
            array (
                'key' => 'popularity',
                'value' => 'popularity',
                'title' => $this->l('Popularity')
            ),
            array (
                'key' => 'name',
                'value' => 'name',
                'title' => $this->l('Name')
            ),
            array (
                'key' => 'price_increasing',
                'value' => 'price_increasing',
                'title' => $this->l('Price (low to high)')
            ),
            array (
                'key' => 'price_decreasing',
                'value' => 'price_decreasing',
                'title' => $this->l('Price (high to low)')
            ),
        );
        $this->context->smarty->assign(array(
            'sort_criterta' => $sortCriterta,
            'module_sort' => Configuration::get('PS_SORT_MODULE_MODULES_CATALOG_'.(int)$this->context->employee->id),
            'theme_sort' => Configuration::get('PS_SORT_THEME_MODULES_CATALOG_'.(int)$this->context->employee->id),
        ));
    }

    public function getSuggestedThemes()
    {
        $installedThemes = array();
        $themes = Theme::getAllThemes()->getAll();
        foreach ($themes as $theme) {
            $installedThemes[] = Theme::getThemeInfo($theme->id);
        }
        $files_list = array(
            array('type' => 'addonsMustHave', 'file' => _PS_ROOT_DIR_.Module::CACHE_FILE_MUST_HAVE_MODULES_LIST, 'loggedOnAddons' => 0),
        );

        $theme_list = array();
        foreach ($files_list as $f) {
            $file = $f['file'];
            $content = Tools::file_get_contents($file);
            $xml = @simplexml_load_string($content, null, LIBXML_NOCDATA);
            if ($xml && isset($xml->theme)) {
                foreach ($xml->theme as $modthemes) {
                    foreach ($installedThemes as $theme) {
                        if ($theme['theme_name'] == $modthemes->name)
                            continue;
                    $item = new stdClass();
                    $item->id = 0;
                    $item->warning = '';
                    $item->type = strip_tags((string)$f['type']);
                    $item->element_type = self::ELEMENT_TYPE_THEME;
                    $item->name = strip_tags((string)$modthemes->name);
                    $item->version = strip_tags((string)$modthemes->version);
                    $item->displayName = strip_tags((string)$modthemes->displayName);
                    $item->description = stripslashes(strip_tags((string)$modthemes->description));
                    $item->description_full = stripslashes(strip_tags((string)$modthemes->description_full));
                    $item->author = strip_tags((string)$modthemes->author);
                    $item->limited_countries = array();
                    $item->parent_class = '';
                    $item->onclick_option = false;
                    $item->available_on_addons = 1;
                    $item->active = 0;
                    $item->additional_description = isset($modthemes->additional_description) ? stripslashes($modthemes->additional_description) : null;
                    $item->compatibility = isset($modthemes->compatibility) ? (array)$modthemes->compatibility : null;
                    $item->nb_rates = isset($modthemes->nb_rates) ? (array)$modthemes->nb_rates : null;
                    $item->avg_rate = isset($modthemes->avg_rate) ? (array)$modthemes->avg_rate : null;
                    $item->badges = isset($modthemes->badges) ? (array)$modthemes->badges : null;
                    $item->url = isset($modthemes->url) ? $modthemes->url : null;

                    if (isset($modthemes->img)) {
                        if (!file_exists(_PS_TMP_IMG_DIR_.md5((int)$modthemes->id.'-'.$modthemes->name).'.jpg')) {
                            if (!file_put_contents(_PS_TMP_IMG_DIR_.md5((int)$modthemes->id.'-'.$modthemes->name).'.jpg', Tools::file_get_contents($modthemes->img))) {
                                copy(_PS_IMG_DIR_.'404.gif', _PS_TMP_IMG_DIR_.md5((int)$modthemes->id.'-'.$modthemes->name).'.jpg');
                            }
                        }

                        if (file_exists(_PS_TMP_IMG_DIR_.md5((int)$modthemes->id.'-'.$modthemes->name).'.jpg')) {
                            $item->image = '../img/tmp/'.md5((int)$modthemes->id.'-'.$modthemes->name).'.jpg';
                        }
                    }

                    if ($item->type == 'addonsMustHave') {
                        $item->addons_buy_url = strip_tags((string)$modthemes->url);
                        $prices = (array)$modthemes->price;
                        $id_default_currency = Configuration::get('PS_CURRENCY_DEFAULT');

                        foreach ($prices as $currency => $price) {
                            if ($id_currency = Currency::getIdByIsoCode($currency)) {
                                $item->price = (float)$price;
                                $item->id_currency = (int)$id_currency;

                                if ($id_default_currency == $id_currency) {
                                    break;
                                }
                            }
                        }
                    }
                    $theme_list[$modthemes->id.'-'.$item->name] = $item;
                    }
                }
            }
        }
        $theme_list = array_values($theme_list);
        $this->sortList($theme_list, 'theme');

        return $theme_list;
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['addons'] = array(
            'href' => 'https://qloapps.com/addons/',
            'desc' => $this->l('Explore all Addons'),
            'imgclass' => 'modules-list',
            'target' => true
        );
    }

    public function getRecommendationContent()
    {
        if (!$this->isFresh(self::CATALOG_RECOMMENDATION_CONTENT, _TIME_1_DAY_)) {
            @file_put_contents(_PS_ROOT_DIR_.self::CATALOG_RECOMMENDATION_CONTENT, Tools::addonsRequest('catalog-recommendation'));
        }
        if (file_exists(_PS_ROOT_DIR_.self::CATALOG_RECOMMENDATION_CONTENT)) {
            return Tools::file_get_contents(_PS_ROOT_DIR_.self::CATALOG_RECOMMENDATION_CONTENT);
        }
        return false;
    }

    public function sortList(&$list, $type)
    {

        switch($type) {
            case 'module':
                $criteria = Configuration::get('PS_SORT_MODULE_MODULES_CATALOG_'.(int)$this->context->employee->id);
                break;
            case 'theme':
                $criteria = Configuration::get('PS_SORT_THEME_MODULES_CATALOG_'.(int)$this->context->employee->id);
                break;
        }
        if ($criteria != 'popularity') {
            usort($list, function($a, $b) use($criteria){
                if ($criteria == 'name') {
                    return strnatcasecmp($a->displayName, $b->displayName);
                } else if ($criteria == 'price_increasing') {
                    if (isset($a->price) && isset($b->price))
                        return $a->price > $b->price;
                    else if (isset($b->price) && $b->price)
                        return true;
                } else if ($criteria == 'price_decreasing') {
                    if (isset($a->price) && isset($b->price))
                        return $a->price < $b->price;
                    else if (isset($b->price) && $b->price)
                        return true;
                }

            });
        }
        return true;
    }

    protected function setSorting($module_sorting, $theme_sorting)
    {
        Configuration::updateValue('PS_SORT_MODULE_MODULES_CATALOG_'.(int)$this->context->employee->id, $module_sorting);
        Configuration::updateValue('PS_SORT_THEME_MODULES_CATALOG_'.(int)$this->context->employee->id, $theme_sorting);
    }


    public function ajaxProcessGetRecommendationContent()
    {
        $response = array('success' => false);
        if ($content = $this->getRecommendationContent()) {
            $response['success'] = true;
            $response['content'] = $content;
        }
        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessSetSorting()
    {
        $this->setSorting(Tools::getValue('module_sorting'), Tools::getValue('theme_sorting'));
        $this->ajaxDie(json_encode(array(
            'success' => true
        )));
    }


    protected function initSuggestedModulesList()
    {
        if (!$this->isFresh(Module::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST, _TIME_1_DAY_)) {
            file_put_contents(_PS_ROOT_DIR_.Module::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST, Tools::addonsRequest('native'));
        }

        if (!$this->isFresh(Module::CACHE_FILE_MUST_HAVE_MODULES_LIST, _TIME_1_DAY_)) {
            @file_put_contents(_PS_ROOT_DIR_.Module::CACHE_FILE_MUST_HAVE_MODULES_LIST, Tools::addonsRequest('must-have'));
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJS(_PS_JS_DIR_.'admin/modules_catalog.js');
        $this->context->controller->addJS(_PS_JS_DIR_.'/twbs-pagination/jquery.twbsPagination.min.js');
        Media::addJSdef(array(
            'num_block_per_page' => self::MODULES_PER_PAGE
        ));
    }
}