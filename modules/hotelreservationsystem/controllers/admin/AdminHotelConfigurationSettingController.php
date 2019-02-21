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
        $review_module_instance = Module::getInstanceByName('productcomments');

        $hotel_reviews_conf = $this->context->link->getAdminLink('AdminModules', true).'&configure='.urlencode($review_module_instance->name).'&tab_module='.$review_module_instance->tab.'&module_name='.urlencode($review_module_instance->name);
        $href_other_conf = $this->context->link->getAdminLink('AdminOtherHotelModulesSetting');
        $href_payment_conf = $this->context->link->getAdminLink('AdminPaymentsSetting');
        $href_ord_res = $this->context->link->getAdminLink('AdminOrderRestrictSettings');
        $href_gen_conf = $this->context->link->getAdminLink('AdminHotelGeneralSettings');
        $href_feature_price_conf = $this->context->link->getAdminLink('AdminHotelFeaturePricesSettings');
        $this->tpl_view_vars = array(
            'feature_price_setting_link' => $href_feature_price_conf,
            'general_setting_link' => $href_gen_conf,
            'payment_setting_link' => $href_payment_conf,
            'order_restrict_setting_link' => $href_ord_res,
            'other_module_setting_link' => $href_other_conf,
            'htl_reviews_conf_link' => $hotel_reviews_conf,
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
