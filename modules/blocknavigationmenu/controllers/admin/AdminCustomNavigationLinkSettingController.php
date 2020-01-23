<?php
/**
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminCustomNavigationLinkSettingController extends ModuleAdminController
{
    protected $position_identifier = 'id_navigation_link_to_move';
    public function __construct()
    {
        $this->table = 'htl_custom_navigation_link';
        $this->className = 'WkCustomNavigationLink';
        $this->_defaultOrderBy = 'position';
        $this->bootstrap = true;
        $this->identifier = 'id_navigation_link';
        parent::__construct();

        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'htl_custom_navigation_link_lang` cel
        ON (a.id_navigation_link = cel.id_navigation_link AND cel.`id_lang` = '.(int) $this->context->language->id.')';

        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'cms_lang` cmsl
        ON (a.id_cms = cmsl.id_cms AND cmsl.`id_lang` = '.(int) $this->context->language->id.')';

        $this->_select = ' IF(a.`id_cms`, cmsl.`meta_title`, cel.`name`) as name';


        $this->fields_options = array(
            'moduleSetting' => array(
                'title' =>    $this->l('Global Settings'),
                'fields' =>    array(
                    'WK_SHOW_FOOTER_NAVIGATION_BLOCK' => array(
                        'title' => $this->l('Show navigation block at footer'),
                        'hint' => $this->l('Enable, if you want to display navigation block at footer.'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool',
                    ),
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
        );

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->fields_list = array(
            'id_navigation_link' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'align' => 'center',
                'havingFilter' => true,
            ),
            'link' => array(
                'title' => $this->l('Link'),
                'align' => 'center',
                'search' => false,
                'callback' => 'getNavigationRedirectUrl',
            ),
            'show_at_navigation' => array(
                'title' => $this->l('Show at navigation menu'),
                'callback' => 'getShowAtNavigationStatus',
                'align' => 'center',
                'type' => 'bool',
            ),
            'show_at_footer' => array(
                'title' => $this->l('Show at footer block'),
                'callback' => 'getShowAtFooterStatus',
                'align' => 'center',
                'type' => 'bool',
            ),
            'active' => array(
                'title' => $this->l('Active'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'filter_key' => 'a!position',
                'position' => 'position',
                'align' => 'center',
            ),
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ),
            'enableSelection' => array(
                'text' => $this->l('Enable selection'),
                'icon' => 'icon-power-off text-success',
            ),
            'disableSelection' => array(
                'text' => $this->l('Disable selection'),
                'icon' => 'icon-power-off text-danger',
            ),
        );
        $this->list_no_link = true;
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add New Navigation Link')
        );
    }

    public function initContent()
    {
        parent::initContent();
        // to customize the view as per our requirements
        if ($this->display != 'add' && $this->display != 'edit') {
            $this->content = $this->renderOptions();
            $this->content .= $this->renderList();
            $this->context->smarty->assign('content', $this->content);
        }
    }

    public function getNavigationRedirectUrl($link, $row)
    {
        if ($row['id_cms']) {
            if (Validate::isLoadedObject($objCMS = new CMS($row['id_cms']))) {
                $link = $this->context->link->getCMSLink((int)$objCMS->id);
            }
        } elseif (!$row['is_custom_link']) {
            $link = $this->context->link->getPageLink($link);
        }
        return '<a target="_blank" href="'.$link.'">'.$link.'</a>';
    }

    public function getShowAtNavigationStatus($showAtNavigation, $row)
    {
        $link = self::$currentIndex.'&id='.$row['id_navigation_link'].'&navigation_display_status&'.$this->table.
        '&token='.$this->token;
        if ($showAtNavigation) {
            return '<a class="list-action-enable action-enabled" href="'.$link.'"><i class="icon-check"></i></a>';
        } else {
            return '<a class="list-action-enable action-disabled" href="'.$link.'"><i class="icon-close"></i></a>';
        }
    }

    public function getShowAtFooterStatus($showAtFooter, $row)
    {
        $link = self::$currentIndex.'&id='.$row['id_navigation_link'].'&footer_display_status&'.$this->table.
        '&token='.$this->token;
        if ($showAtFooter) {
            return '<a class="list-action-enable action-enabled" href="'.$link.'"><i class="icon-check"></i></a>';
        } else {
            return '<a class="list-action-enable action-disabled" href="'.$link.'"><i class="icon-close"></i></a>';
        }
    }

    public function renderForm()
    {
        $smartyVars = array();
        $objNavigationLink = $this->loadObject(true);
        if (Validate::isLoadedObject($objNavigationLink)) {
            $smartyVars['navigationLinkInfo'] = (array) $objNavigationLink;
            $smartyVars['edit'] = 1;
        }
        // send theme's front pages
        $themePages = array();
        if (Validate::isLoadedObject($objTheme = new Theme($this->context->shop->id_theme))) {
            if ($themePages = $objTheme->getMetas()) {
                foreach ($themePages as &$page) {
                    if ($page['id_meta']) {
                        if (Validate::isLoadedObject($objMeta = new Meta($page['id_meta']))) {
                            $page['page'] = $objMeta->page;
                        }
                    }
                }
            }
        }
        $smartyVars['themePages'] = $themePages;
        $smartyVars['languages'] = Language::getLanguages(false);
        $smartyVars['currentLang'] = Language::getLanguage((int) Configuration::get('PS_LANG_DEFAULT'));
        $smartyVars['cmsPages'] = CMS::getCMSPages($smartyVars['currentLang'], 1);
        $this->context->smarty->assign($smartyVars);
        $this->fields_form = array(
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        return parent::renderForm();
    }

    public function processSave()
    {
        $isCmsPage = Tools::getValue('is_cms_block_link');
        $idNavigationLink = Tools::getValue('id_navigation_link');
        $active = Tools::getValue('active');
        $isCustomLink = Tools::getValue('is_custom_redirect_link');
        $link = Tools::getValue('link');
        $redirectPage = Tools::getValue('redirect_page');
        $showAtNavigation = Tools::getValue('show_at_navigation');
        $showAtFooter = Tools::getValue('show_at_footer');
        $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
        $objDefaultLanguage = Language::getLanguage((int) $defaultLangId);
        $languages = Language::getLanguages(false);
        $idCms = Tools::getValue('id_cms');

        if ($isCmsPage) {
            if (!$idCms) {
                $this->errors[] = $this->l('Please choose a cms page.');
            } elseif (!Validate::isUnsignedInt($idCms)) {
                $this->errors[] = $this->l('Invalid cms page.');
            }
        } else {
            if (!trim(Tools::getValue('navigation_link_name_'.$defaultLangId))) {
                $this->errors[] = $this->l('Navigation link name is required at least in ').$objDefaultLanguage['name'];
            } else {
                foreach ($languages as $language) {
                    if (!Validate::isGenericName(Tools::getValue('navigation_link_name_'.$language['id_lang']))) {
                        $this->errors[] = $this->l('Invalid navigation link name for the language ').$language['name'];
                        break;
                    }
                }
            }
            if ($isCustomLink) {
                if (!trim($link)) {
                    $this->errors[] = $this->l('Custom redirect url is required.');
                } elseif (!Validate::isUrl($link)) {
                    $this->errors[] = $this->l('Invalid custom redirect url.');
                }
            } elseif (!trim($redirectPage)) {
                $this->errors[] = $this->l('Invalid page selected.');
            }
        }

        if (!count($this->errors)) {
            $objCustomNavigationLink = new WkCustomNavigationLink();
            if ($idNavigationLink) {
                $objCustomNavigationLink = new WkCustomNavigationLink($idNavigationLink);
            } else {
                if ($objCustomNavigationLink->position <= 0) {
                    $objCustomNavigationLink->position = $objCustomNavigationLink->getHigherPosition();
                }
            }
            if ($isCmsPage) {
                $objCustomNavigationLink->id_cms = $idCms;
            } else {
                $objCustomNavigationLink->id_cms = 0;
                foreach ($languages as $language) {
                    if (!trim(Tools::getValue('navigation_link_name_'.$language['id_lang']))) {
                        $objCustomNavigationLink->name[$language['id_lang']] = Tools::getValue(
                            'navigation_link_name_'.$defaultLangId
                        );
                    } else {
                        $objCustomNavigationLink->name[$language['id_lang']] = Tools::getValue(
                            'navigation_link_name_'.$language['id_lang']
                        );
                    }
                }
                if ($isCustomLink) {
                    $objCustomNavigationLink->is_custom_link = 1;
                    $objCustomNavigationLink->link = $link;
                } else {
                    $objCustomNavigationLink->is_custom_link = 0;
                    $objCustomNavigationLink->link = $redirectPage;
                }
            }
            $objCustomNavigationLink->show_at_navigation = $showAtNavigation;
            $objCustomNavigationLink->show_at_footer = $showAtFooter;
            $objCustomNavigationLink->active = $active;
            if ($objCustomNavigationLink->save()) {
                if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                    if ($idNavigationLink) {
                        Tools::redirectAdmin(
                            self::$currentIndex.'&id_navigation_link='.(int) $objCustomNavigationLink->id.
                            '&update'.$this->table.'&conf=4&token='.$this->token
                        );
                    } else {
                        Tools::redirectAdmin(
                            self::$currentIndex.'&id_navigation_link='.(int) $objCustomNavigationLink->id.
                            '&update'.$this->table.'&conf=3&token='.$this->token
                        );
                    }
                } else {
                    if ($idNavigationLink) {
                        Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                    } else {
                        Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
                    }
                }
            }
        }

        if (isset($idNavigationLink) && $idNavigationLink) {
            $this->display = 'edit';
        } else {
            $this->display = 'add';
        }
    }

    public function postProcess()
    {
        // change show_at_navigation status
        if (Tools::getIsset('navigation_display_status')) {
            if ($id = Tools::getValue('id')) {
                if (Validate::isLoadedObject($objNavigationLink = new WkCustomNavigationLink($id))) {
                    $objNavigationLink->show_at_navigation = !(int)$objNavigationLink->show_at_navigation;
                    if ($objNavigationLink->save()) {
                        Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                    } else {
                        $this->errors[] = $this->l('Some error occurred. Please try again.');
                    }
                } else {
                    $this->errors[] = $this->l('Object not loaded. Please try again.');
                }
            } else {
                $this->errors[] = $this->l('Object not loaded. Please try again.');
            }
        } elseif (Tools::getIsset('footer_display_status')) {
            if ($id = Tools::getValue('id')) {
                if (Validate::isLoadedObject($objNavigationLink = new WkCustomNavigationLink($id))) {
                    $objNavigationLink->show_at_footer = !(int)$objNavigationLink->show_at_footer;
                    if ($objNavigationLink->save()) {
                        Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                    } else {
                        $this->errors[] = $this->l('Some error occurred. Please try again.');
                    }
                } else {
                    $this->errors[] = $this->l('Object not loaded. Please try again.');
                }
            } else {
                $this->errors[] = $this->l('Object not loaded. Please try again.');
            }
        }
        Parent::postProcess();
    }

    // update positions
    public function ajaxProcessUpdatePositions()
    {
        $way = (int) Tools::getValue('way');
        $idNavigationLink = (int) Tools::getValue('id');
        $positions = Tools::getValue('navigation_link');

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int) $pos[2] === $idNavigationLink) {
                if ($objFeatureBlock = new WkCustomNavigationLink((int) $pos[2])) {
                    if (isset($position)
                        && $objFeatureBlock->updatePosition($way, $position, $idNavigationLink)
                    ) {
                        echo 'ok position '.(int) $position.' for custom navigation link '.(int) $pos[1].'\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update navigation link position '.
                        (int) $idNavigationLink.' to position '.(int) $position.' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This navigation link ('.(int) $idNavigationLink.
                    ') can t be loaded"}';
                }
                break;
            }
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/admin/wk_navigation_link.js');
    }
}
