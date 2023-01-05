<?php

class AdminHotelConfigurationSettingController extends ModuleAdminController
{
    protected $position_identifier = 'id_settings_link';

    public function __construct()
    {
        $this->table = 'htl_settings_link';
        $this->identifier = 'id_settings_link';
        $this->className = 'HotelSettingsLink';
        $this->_defaultOrderBy = 'position';
        $this->lang = true;
        $this->bootstrap = true;
        $this->list_no_link = true;

        parent::__construct();

        $this->addRowAction('edit');
        $this->addRowAction('delete');

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

        $this->fields_list = array(
            'id_settings_link' => array(
                'title' => $this->l('ID'),
                'hint' => $this->l('ID of the settings link.'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'hint' => $this->l('Name of the settings link.'),
                'align' => 'left',
                'class' => 'fixed-width-xxl',
            ),
            'link' => array(
                'title' => $this->l('Link'),
                'hint' => $this->l('Link of the page where admin is redirected.'),
                'align' => 'left',
                'callback' => 'displayColumnLink',
            ),
            'icon' => array(
                'title' => $this->l('Icon'),
                'hint' => $this->l('Icon of the settings link.'),
                'align' => 'center',
                'callback' => 'displayColumnIcon',
            ),
            'new_window' => array(
                'title' => $this->l('New Window'),
                'hint' => $this->l('Active if link is opened in a new window.'),
                'align' => 'center',
                'active' => 'new_window',
                'ajax' => true,
                'orderby' => false,
                'type' => 'bool',
                'class' => 'fixed-width-sm',
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'filter_key' => 'a!position',
                'position' => 'position',
                'align' => 'center',
                'class' => 'fixed-width-md',
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'hint' => $this->l('Current status of the settings link.'),
                'type' => 'bool',
                'active' => 'status',
                'align' => 'center',
                'class' => 'fixed-width-lg',
                'filter_key' => 'a!active',
            ),
        );
    }

    public function displayColumnIcon($icon)
    {
        $tpl = $this->context->smarty->createTemplate(
            $this->module->getLocalPath().'views/templates/admin/'.$this->tpl_folder.'helpers/list/link-icon.tpl'
        );

        $tpl->assign('icon', $icon);

        return $tpl->fetch();
    }

    public function displayColumnLink($link)
    {
        $this->loadObject(true);
        $tpl = $this->context->smarty->createTemplate(
            $this->module->getLocalPath().'views/templates/admin/'.$this->tpl_folder.'helpers/list/link-page.tpl'
        );

        $tpl->assign(array(
            'saved_link' => $link,
            'generated_link' => $this->object->generateLink($link),
        ));

        return $tpl->fetch();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        if ($this->display == 'view') {
            $this->toolbar_title = $this->l('Hotel Settings');
            $this->page_header_toolbar_btn['manage_links'] = array(
                'icon' => 'process-icon-cogs',
                'href' => self::$currentIndex.'&display=list&token='.$this->token,
                'desc' => $this->l('Manage links'),
            );
        } elseif ($this->display == 'list') {
            $this->page_header_toolbar_title = $this->l('Settings Links');
            $this->page_header_toolbar_btn['new'] = array(
                'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
                'desc' => $this->l('Add new link'),
            );
        }
    }

    public function initContent()
    {
        if (!$this->ajax) {
            if (Tools::getValue('display') == 'list') {
                $this->display = 'list';
            } elseif (empty($this->display)
                && !Tools::isSubmit('submitFilter'.$this->table)
            ) {
                $this->display = 'view';
            }
        }

        parent::initContent();
    }

    public function renderList()
    {
        $this->context->smarty->assign('icon', 'icon-list');
        $this->toolbar_title = $this->l('Settings Links');

        $this->loadObject(true);
        $unremovableLinks = $this->object->getUnremovableLinks();
        if (is_array($unremovableLinks) && count($unremovableLinks)) {
            $ids = array();
            foreach ($unremovableLinks as $unremovableLink) {
                $ids[] = $unremovableLink['id_settings_link'];
            }
            $this->addRowActionSkipList('delete', $ids);
        }

        return parent::renderList();
    }

    public function renderForm()
    {
        $smartyVars = array();
        $idSettingsLink = (int) Tools::getValue($this->identifier);
        $objHotelSettingsLink = $this->loadObject(true);

        $smartyVars['id_hotel_settings_link'] = $idSettingsLink;
        $smartyVars['currentTab'] = $this;
        $smartyVars['currentObject'] = $objHotelSettingsLink;

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
                    'name' => 'submitHotelSettingsLink',
                    'class' => 'btn btn-default pull-right',
                ),
            ),
        );

        return $fields_form;
    }

    public function renderView()
    {
        $settingsLinks = $this->object->getAllSettingsLinks();
        foreach ($settingsLinks as $index => &$settingsLink) {
            $settingsLink['generated_link'] = $this->object->generateLink($settingsLink['link']);
        }

        $this->tpl_view_vars = array(
            'settings_links' => $settingsLinks,
        );

        return parent::renderView();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitHotelSettingsLink') || Tools::isSubmit('submitHotelSettingsLinkAndStay')) {
            $idHotelSettingsLink = Tools::getValue($this->identifier);
            $this->validateRules();

            if (is_array($this->errors) && !count($this->errors)) {
                $objHotelSettingsLink = new HotelSettingsLink($idHotelSettingsLink);
                $this->copyFromPost($objHotelSettingsLink, $this->table);
                if (!$idHotelSettingsLink) {
                    $objHotelSettingsLink->position = $objHotelSettingsLink->getHigherPosition();
                }
                if ($objHotelSettingsLink->save()) {
                    $newId = $objHotelSettingsLink->id;

                    if (is_array($this->errors) && !count($this->errors)) {
                        if (Tools::isSubmit('submitHotelSettingsLinkAndStay')) {
                            if ($idHotelSettingsLink) {
                                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token.
                                '&update'.$this->table.'&'.$this->identifier.'='.$newId);
                            } else {
                                Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token.
                                '&update'.$this->table.'&'.$this->identifier.'='.$newId);
                            }
                        } else {
                            self::$currentIndex .= '&display=list';
                            if ($idHotelSettingsLink) {
                                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                            } else {
                                Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
                            }
                        }
                    }
                }
            }
        }

        parent::postProcess();
    }

    public function processStatus()
    {
        self::$currentIndex .= '&display=list';
        return parent::processStatus();
    }

    public function processDelete()
    {
        self::$currentIndex .= '&display=list';
        return parent::processDelete();
    }

    public function processBulkDelete()
    {
        self::$currentIndex .= '&display=list';
        return parent::processBulkDelete();
    }

    public function ajaxProcessNewWindowHtlSettingsLink()
    {
        $response = array(
            'success' => 0,
            'text' => $this->l('An error occurred while updating object.')
        );

        $idSettingsLink = Tools::getValue('id_settings_link');
        $objHotelSettingsLink = new HotelSettingsLink($idSettingsLink);

        if (Validate::isLoadedObject($objHotelSettingsLink)) {
            $objHotelSettingsLink->new_window = (int) !$objHotelSettingsLink->new_window;

            if ($objHotelSettingsLink->save()) {
                $response['success'] = 1;
                $response['text'] = $this->l('The object has been updated successfully.');
            }
        }

        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessUpdatePositions()
    {
        $way = (int) Tools::getValue('way');
        $idSettingsLink = (int) Tools::getValue('id');
        $positions = Tools::getValue('settings_link');

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int) $pos[2] === $idSettingsLink) {
                if ($objHotelSettingsLink = new HotelSettingsLink((int) $pos[2])) {
                    if (isset($position) && $objHotelSettingsLink->updatePosition($way, $position, $idSettingsLink)) {
                        $this->ajaxDie(json_encode(true));
                    } else {
                        $this->ajaxDie(json_encode(array(
                            'hasError' => true,
                            'errors' => sprintf(
                                $this->l('Can not update position for object ID: %d to position %d'),
                                (int) $idSettingsLink,
                                $position
                            ),
                        )));
                    }
                } else {
                    $this->ajaxDie(json_encode(array(
                        'hasError' => true,
                        'errors' => sprintf(
                            $this->l('The object with ID: %d can not be loaded.'),
                            (int) $idSettingsLink
                        ),
                    )));
                }

                break;
            }
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_MODULE_DIR_.'hotelreservationsystem/views/css/HotelReservationAdmin.css');
        $this->addJs(_MODULE_DIR_.'hotelreservationsystem/views/js/HotelReservationAdmin.js');
    }
}
