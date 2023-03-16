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

class AdminAssignHotelFeaturesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'htl_branch_features';
        $this->className = 'HotelBranchFeatures';
        $this->identifier  = 'id';
        $this->context = Context::getContext();

        // START send access query information to the admin controller
        $this->access_select = ' SELECT a.`id` FROM '._DB_PREFIX_.'htl_branch_features a';
        $this->access_join = ' INNER JOIN '._DB_PREFIX_.'htl_branch_info hbi ON (hbi.id = a.id_hotel)';
        if ($acsHtls = HotelBranchInformation::getProfileAccessedHotels($this->context->employee->id_profile, 1, 1)) {
            $this->access_where = ' WHERE hbi.id IN ('.implode(',', $acsHtls).')';
        }

        parent::__construct();

        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'htl_branch_info` hi ON (hi.`id` = a.`id_hotel`)';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hil
        ON (hi.`id` = hil.`id` AND hil.id_lang = '.(int)$this->context->language->id.')';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'address` addr ON (addr.`id_hotel` = hi.`id`)';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_state` = addr.`id_state`)';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (cl.`id_country` = addr.`id_country`
        AND cl.`id_lang` = '.(int)$this->context->language->id.')';

        $this->_select .= 'addr.`city` as htl_city, hi.`id` as htl_id, hil.`hotel_name` as htl_name,
        s.`name` as `state_name`, cl.`name`';
        $this->_group = 'GROUP BY a.`id_hotel`';

        $this->fields_list = array(
            'htl_id' => array(
                'title' => $this->l('Hotel ID'),
                'align' => 'center',
                'filter_key' => 'hi!id',
            ),
            'htl_name' => array(
                'title' => $this->l('Hotel Name'),
                'align' => 'center',
                'filter_key' => 'hil!hotel_name',
            ),
            'htl_city' => array(
                'title' => $this->l('City'),
                'align' => 'center',
                'filter_key' => 'addr!city',
            ),

            'state_name' => array(
                'title' => $this->l('State'),
                'align' => 'center',
                'filter_key' => 's!name',
            ),
            'name' => array(
                'title' => $this->l('Country'),
                'align' => 'center',
                'filter_key' => 'cl!name',
            ),
            'date_add' => array(
                'title' => $this->l('Date Added'),
                'align' => 'center',
                'type' => 'datetime',
                'filter_key' => 'a!date_add',
            )
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );
    }

    public function initToolbar()
    {
        parent::initToolbar();

        $this->page_header_toolbar_btn['new_feature'] = array(
            'href' => $this->context->link->getAdminLink('AdminHotelFeatures'),
            'desc' => $this->l('Hotel Features'),
            'imgclass' => 'back'
        );

        $this->page_header_toolbar_btn['assignfeatures'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Assign Features'),
            'imgclass' => 'new'
        );
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderForm()
    {
        $smartyVars = array();
        if ($this->display == 'add') {
            $objHotelFeatures = new HotelFeatures();
            $featuresList = $objHotelFeatures->HotelAllCommonFeaturesArray();
            $objBranchInfo = new HotelBranchInformation();
            $unassignedFtrsHotels = $objBranchInfo->getUnassignedFeaturesHotelIds();
            // filter hotels as per accessed hotels
            $unassignedFtrsHotels = HotelBranchInformation::filterDataByHotelAccess(
                $unassignedFtrsHotels,
                $this->context->employee->id_profile,
                1
            );

            $smartyVars['hotels'] = $unassignedFtrsHotels;
            $smartyVars['features_list'] = $featuresList;
        } elseif ($this->display == 'edit') {
            $id = Tools::getValue('id');
            $objBranchFeatures = new HotelBranchFeatures($id);
            $smartyVars['edit'] = 1;
            $objHotelFeatures = new HotelFeatures();
            $objBranchInfo = new HotelBranchInformation();

            $features_hotel = $objBranchInfo->getFeaturesOfHotelByHotelId($objBranchFeatures->id_hotel);
            $featuresList = $objHotelFeatures->HotelBranchSelectedFeaturesArray($features_hotel);

            $hotels = $objBranchInfo->hotelsNameAndId();
            $smartyVars['hotel_id'] = $objBranchFeatures->id_hotel;
            $smartyVars['hotels'] = $hotels;
            $smartyVars['features_list'] = $featuresList;
        }

        $this->context->smarty->assign($smartyVars);

        $this->fields_form = array(
                'submit' => array(
                    'title' => $this->l('Save')
                )
            );
        return parent::renderForm();
    }

    public function processSave()
    {
        if ($editId = Tools::getValue('edit_hotel_id')) {
            $objBranchFeatures = new HotelBranchFeatures();
            $objBranchFeatures->deleteBranchFeaturesByHotelId($editId);
        }
        if (!$hotelFeatures = Tools::getValue('hotel_fac')) {
            $this->errors[] = $this->l('Please select at least one feature to assign to a hotel.');
        }
        if (!$idHotel = Tools::getValue('id_hotel')) {
            $this->errors[] = $this->l('Please select a hotel first.');
        }
        if (!count($this->errors)) {
            $objHotelFeatures = new HotelBranchFeatures();
            if (!$objHotelFeatures->assignFeaturesToHotel($idHotel, $hotelFeatures)) {
                $this->errors[] = $this->l('Some problem occure while assigning Features to the hotel.');
            }
            if (empty($this->errors)) {
                if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                    Tools::redirectAdmin(
                        self::$currentIndex.'&id='.(int)$idHotel.'&update'.$this->table.'&conf=3&token='.$this->token
                    );
                } else {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                }
            }
        } else {
            $this->display = 'add';
        }
    }

    public function processDelete()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            $object = $this->loadObject();
            if ($object->id) {
                $objBranchFeatures = new HotelBranchFeatures();
                $objBranchFeatures->deleteBranchFeaturesByHotelId($object->id_hotel);
            }
        } else {
            $this->errors[] = $this->l('An error occurred while deleting the object.').'<b>'.$this->table.'</b> '.
            $this->l('(cannot load object)');
        }
        parent::processDelete();
    }

    protected function processBulkDelete()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $key => $value) {
                $objBranchFeatures = new HotelBranchFeatures($value);
                $objBranchFeatures->deleteBranchFeaturesByHotelId($objBranchFeatures->id_hotel);
            }
            parent::processBulkDelete();
        } else {
            $this->errors[] = $this->l('You must select at least one element to delete.');
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJs(_MODULE_DIR_.'hotelreservationsystem/views/js/HotelReservationAdmin.js');
        $this->addCSS(_MODULE_DIR_.'hotelreservationsystem/views/css/HotelReservationAdmin.css');
    }
}