<?php
/**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminCustomExploreLinkSettingController extends ModuleAdminController
{
    protected $position_identifier = 'id_explore_link_to_move';
    public function __construct()
    {
        $this->table = 'htl_custom_explore_link';
        $this->className = 'WkCustomExploreLink';
        $this->_defaultOrderBy = 'position';
        $this->bootstrap = true;
        $this->identifier = 'id_explore_link';
        parent::__construct();

        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'htl_custom_explore_link_lang` cel
        ON (a.id_explore_link = cel.id_explore_link AND cel.`id_lang` = '.(int) $this->context->language->id.')';

        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'cms_lang` cmsl
        ON (a.id_cms = cmsl.id_cms AND cmsl.`id_lang` = '.(int) $this->context->language->id.')';

        $this->_select = ' IF(a.`id_cms`, cmsl.`meta_title`, cel.`name`) as name';

        $this->fields_list = array(
            'id_explore_link' => array(
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
                'callback' => 'getExploreRedirectUrl',
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
            'date_add' => array(
                'title' => $this->l('Date Add'),
                'align' => 'center',
                'type' => 'datetime',
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

    public function getExploreRedirectUrl($link, $row)
    {
        if ($row['id_cms']) {
            if (Validate::isLoadedObject($objCMS = new CMS($row['id_cms']))) {
                $link = $this->context->link->getCMSLink((int)$objCMS->id);
                return '<a target="_blank" href="'.$link.'">'.$link.'</a>';
            }
        } else {
            return '<a target="_blank" href="'.$link.'">'.$link.'</a>';
        }
    }

    public function getShowAtNavigationStatus($showAtNavigation, $row)
    {
        $link = self::$currentIndex.'&id='.$row['id_explore_link'].'&navigation_display_status&'.$this->table.
        '&token='.$this->token;
        if ($showAtNavigation) {
            return '<a class="list-action-enable action-enabled" href="'.$link.'"><i class="icon-check"></i></a>';
        } else {
            return '<a class="list-action-enable action-disabled" href="'.$link.'"><i class="icon-close"></i></a>';
        }
    }

    public function getShowAtFooterStatus($showAtFooter, $row)
    {
        $link = self::$currentIndex.'&id='.$row['id_explore_link'].'&footer_display_status&'.$this->table.
        '&token='.$this->token;
        if ($showAtFooter) {
            return '<a class="list-action-enable action-enabled" href="'.$link.'"><i class="icon-check"></i></a>';
        } else {
            return '<a class="list-action-enable action-disabled" href="'.$link.'"><i class="icon-close"></i></a>';
        }
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add New Custom Link')
        );

        return parent::renderList();
    }

    public function renderForm()
    {
        $smartyVars = array();
        $objExploreLink = $this->loadObject(true);
        if (Validate::isLoadedObject($objExploreLink)) {
            $smartyVars['exploreLinkInfo'] = (array) $objExploreLink;
            $smartyVars['edit'] = 1;
        }
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
        $id = Tools::getValue('id_explore_link');
        $active = Tools::getValue('active');
        $link = Tools::getValue('link');
        $showAtNavigation = Tools::getValue('show_at_navigation');
        $showAtFooter = Tools::getValue('show_at_footer');
        $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
        $objDefaultLanguage = Language::getLanguage((int) $defaultLangId);
        $languages = Language::getLanguages(false);

        if ($isCmsPage) {
            if (!$idCms = Tools::getValue('id_cms')) {
                $this->errors[] = $this->l('Please choose a cms page.');
            }
        } else {
            if (!trim(Tools::getValue('explore_link_name_'.$defaultLangId))) {
                $this->errors[] = $this->l('Explore link name is required at least in ').$objDefaultLanguage['name'];
            } else {
                foreach ($languages as $language) {
                    if (!Validate::isGenericName(Tools::getValue('explore_link_name_'.$language['id_lang']))) {
                        $this->errors[] = $this->l('Invalid explore link name for the language ').$language['name'];
                        break;
                    }
                }
            }
            if (!trim($link)) {
                $this->errors[] = $this->l('Redirect url is required.');
            } elseif (!Validate::isUrl($link)) {
                $this->errors[] = $this->l('Invalid redirect url.');
            }
        }

        if (!count($this->errors)) {
            $objCustomExploreLink = new WkCustomExploreLink();
            if ($id) {
                $objCustomExploreLink = new WkCustomExploreLink($id);
            } else {
                if ($objCustomExploreLink->position <= 0) {
                    $objCustomExploreLink->position = $objCustomExploreLink->getHigherPosition();
                }
            }
            if ($isCmsPage) {
                $objCustomExploreLink->id_cms = $idCms;
            } else {
                $objCustomExploreLink->id_cms = 0;
                foreach ($languages as $language) {
                    if (!trim(Tools::getValue('explore_link_name_'.$language['id_lang']))) {
                        $objCustomExploreLink->name[$language['id_lang']] = Tools::getValue(
                            'explore_link_name_'.$defaultLangId
                        );
                    } else {
                        $objCustomExploreLink->name[$language['id_lang']] = Tools::getValue(
                            'explore_link_name_'.$language['id_lang']
                        );
                    }
                }
                $objCustomExploreLink->link = $link;
            }
            $objCustomExploreLink->show_at_navigation = $showAtNavigation;
            $objCustomExploreLink->show_at_footer = $showAtFooter;
            $objCustomExploreLink->active = $active;
            if ($objCustomExploreLink->save()) {
                if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                    if ($id) {
                        Tools::redirectAdmin(
                            self::$currentIndex.'&id_explore_link='.(int) $objCustomExploreLink->id.
                            '&update'.$this->table.'&conf=4&token='.$this->token
                        );
                    } else {
                        Tools::redirectAdmin(
                            self::$currentIndex.'&id_explore_link='.(int) $objCustomExploreLink->id.
                            '&update'.$this->table.'&conf=3&token='.$this->token
                        );
                    }
                } else {
                    if ($id) {
                        Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                    } else {
                        Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
                    }
                }
            }
        }

        if (isset($id) && $id) {
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
                if (Validate::isLoadedObject($objExploreLink = new WkCustomExploreLink($id))) {
                    $objExploreLink->show_at_navigation = !(int)$objExploreLink->show_at_navigation;
                    if ($objExploreLink->save()) {
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
                if (Validate::isLoadedObject($objExploreLink = new WkCustomExploreLink($id))) {
                    $objExploreLink->show_at_footer = !(int)$objExploreLink->show_at_footer;
                    if ($objExploreLink->save()) {
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
        $idExploreLink = (int) Tools::getValue('id');
        $positions = Tools::getValue('explore_link');

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int) $pos[2] === $idExploreLink) {
                if ($objFeatureBlock = new WkCustomExploreLink((int) $pos[2])) {
                    if (isset($position)
                        && $objFeatureBlock->updatePosition($way, $position, $idExploreLink)
                    ) {
                        echo 'ok position '.(int) $position.' for custom explore link '.(int) $pos[1].'\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update explore link position '.
                        (int) $idExploreLink.' to position '.(int) $position.' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This explore link ('.(int) $idExploreLink.
                    ') can t be loaded"}';
                }
                break;
            }
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/admin/wk_explore_link.js');
    }
}
