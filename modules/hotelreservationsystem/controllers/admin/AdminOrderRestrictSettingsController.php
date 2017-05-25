<?php

class AdminOrderRestrictSettingsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'htl_order_restrict_date';
        $this->className = 'HotelOrderRestrictDate';
        $this->bootstrap = true;
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'htl_branch_info` hb ON (a.`id_hotel` = hb.`id`)';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_state` = hb.`state_id`)';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (cl.`id_country` = hb.`country_id` AND cl.`id_lang` = '.Configuration::get('PS_LANG_DEFAULT').')';
        $this->_select = 's.`name` as `state_name`, cl.`name` as country_name, hb.`hotel_name`, hb.`city`, ';
        $this->context = Context::getContext();
        $this->fields_options = array(
            'orderrestrict' => array(
                'title' => $this->l('Order Restriction Setting'),
                'fields' => array(
                    'MAX_GLOBAL_BOOKING_DATE' => array(
                        'title' => $this->l('Maximum Global Date To Book a room'),
                        'type' => 'text',
                        'id' => 'max_global_book_date',
                        'hint' => $this->l('This is the maximum date till which date rooms of all your hotels can be booked.'),
                    ),
                ),
                'submit' => array('title' => $this->l('Save')),
            ),
        );
        $this->fields_list = array();
        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
            ),
            'hotel_name' => array(
                'title' => $this->l('Hotel Name'),
                'align' => 'center',
            ),
            'city' => array(
                'title' => $this->l('City'),
                'align' => 'center',
            ),
            'state_name' => array(
                'title' => $this->l('State'),
                'align' => 'center',
                'filter_key' => 's!name',
            ),
            'country_name' => array(
                'title' => $this->l('Country'),
                'align' => 'center',
                'filter_key' => 'cl!name',
            ),
            'max_order_date' => array(
                'title' => $this->l('Maximum Booking Date'),
                'align' => 'center',
            ), );
        $this->identifier = 'id';
        $this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'),
                                'icon' => 'icon-trash',
                                'confirm' => $this->l('Delete selected items?'), ),
            );
        parent::__construct();
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add new Date'),
        );
    }

    public function renderForm()
    {
        if ($this->display == 'add') {
            $order_restrict_date = new HotelOrderRestrictDate();
            $hotels_list = $order_restrict_date->getUnsavedHotelsForOrderRestrict();
            $this->context->smarty->assign('hotels_list', $hotels_list);
        } elseif ($this->display == 'edit') {
            $id = Tools::getValue('id');
            $obj_order_restrict = new HotelOrderRestrictDate($id);
            $hotel_info_obj = new HotelBranchInformation($obj_order_restrict->id_hotel);
            $ordr_restrict_data['hotel_name'] = $hotel_info_obj->hotel_name;
            $ordr_restrict_data['id_hotel'] = $obj_order_restrict->id_hotel;
            $ordr_restrict_data['max_date'] = date('d-m-Y', strtotime($obj_order_restrict->max_order_date));
            $ordr_restrict_data['hidden_max_date'] = date('Y-m-d', strtotime($obj_order_restrict->max_order_date));
            $this->context->smarty->assign('ordr_restrict_hotel_data', $ordr_restrict_data);
            $this->context->smarty->assign('id', $id);
            $this->context->smarty->assign('edit', 1);
        }

        $this->fields_form = array(
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            );

        return parent::renderForm();
    }

    public function processSave()
    {
        $id = Tools::getValue('id');
        $hotel_id = Tools::getValue('hotel_id');
        $max_htl_book_date = Tools::getValue('max_htl_book_date');
        if (!$hotel_id) {
            $this->errors[] = Tools::displayError('Please Select a Hotel.');
        }
        if (!$max_htl_book_date) {
            $this->errors[] = Tools::displayError('Please select maximum order date of booking.');
        } else {
            $max_htl_book_date = date('Y-m-d', strtotime($max_htl_book_date));
            if (!Validate::isDate($max_htl_book_date)) {
                $this->errors[] = Tools::displayError('Enter a valid date.');
            }
        }

        if (!count($this->errors)) {
            $obj_order_restrict = new HotelOrderRestrictDate();
            $ordr_restrict_data = $obj_order_restrict->getDataByHotelId($hotel_id);
            if (isset($ordr_restrict_data['id']) && $ordr_restrict_data['id']) {
                $obj_order_restrict = new HotelOrderRestrictDate($ordr_restrict_data['id']);
            }

            $obj_order_restrict->id_hotel = $hotel_id;
            $obj_order_restrict->max_order_date = $max_htl_book_date;
            $obj_order_restrict->save();
            $new_id = $obj_order_restrict->id;
            if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                if (isset($new_id) && $new_id) {
                    Tools::redirectAdmin(self::$currentIndex.'&id='.(int) $new_id.'&update'.$this->table.'&conf=4&token='.$this->token);
                } else {
                    Tools::redirectAdmin(self::$currentIndex.'&add'.$this->table.'&conf=4&token='.$this->token);
                }
            } else {
                $redirect_link = $this->context->link->getAdminLink('AdminOrderRestrictSettings');
                Tools::redirectAdmin($redirect_link.'&conf=4');
            }
        } else {
            $this->display = 'add';
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitOptionshtl_order_restrict_date')) {
            $max_global_date = Tools::getValue('MAX_GLOBAL_BOOKING_DATE');
            $max_global_date_frm = date('Y-m-d', strtotime($max_global_date));
            if (!Validate::isDate($max_global_date_frm)) {
                $this->errors[] = Tools::displayError('Enter a valid date.');
            } else if (strtotime(date('Y-m-d')) > strtotime($max_global_date_frm)) {
                $this->errors[] = Tools::displayError('Maximum Global Date can not be before current date.');
            }
            if (!count($this->errors)) {
                Configuration::updateValue('MAX_GLOBAL_BOOKING_DATE', $max_global_date);
            }
        } else {
            parent::postProcess();
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJs(_MODULE_DIR_.'hotelreservationsystem/views/js/HotelReservationAdmin.js');
        $this->addCSS(_MODULE_DIR_.'hotelreservationsystem/views/css/HotelReservationAdmin.css');
    }
}
