<?php

class AdminHotelConfigurationSettingController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'configuration';
        $this->className = 'Configuration';
        $this->bootstrap = true;
        if (!($obj = $this->loadObject(true))) {
            return;
        }
        $this->toolbar_title = $this->l('Manage Hotel Settings');
        parent::__construct();
    }

    public function initContent()
    {
        $this->show_toolbar = false;
        $this->display = 'view';
        parent::initContent();
    }

    public function renderView()
    {
        $this->tpl_view_vars = array(
            'feature_price_setting_link' => $this->context->link->getAdminLink('AdminHotelFeaturePricesSettings'),
            'general_setting_link' => $this->context->link->getAdminLink('AdminHotelGeneralSettings'),
            'additional_demand_setting_link' => $this->context->link->getAdminLink('AdminRoomTypeGlobalDemand'),
        );

        return parent::renderView();
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_MODULE_DIR_.'hotelreservationsystem/views/css/HotelReservationAdmin.css');
        $this->addJs(_MODULE_DIR_.'hotelreservationsystem/views/js/HotelReservationAdmin.js');
    }
}
