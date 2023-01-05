<?php
/**
* 2010-2023 Webkul.
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
*  @copyright 2010-2023 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminQloappsChannelManagerConnectorController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'qcmc_channel_manager_booking';
        $this->className = 'QcmcChannelManagerBooking';
        $this->bootstrap = true;
        $this->toolbar_title = $this->l('Channel Manager Bookings');
        $this->context = Context::getContext();
        $this->identifier = 'id_channel_manager_booking';

        parent::__construct();

        $this->_join .= ' INNER JOIN `'._DB_PREFIX_.'orders` ord ON (a.id_order = ord.`id_order`)';

        $this->_select .= ' ord.`source`, ord.`total_paid`, ord.`total_paid_real`, IF(a.id_order, 1, 0) badge_success';

        $this->_orderWay = 'DESC';

        $this->fields_list = array(
            'id_order' => array(
                'title' => $this->l('Id order'),
                'align' => 'center',
                'havingFilter' => true,
                'callback' => 'getOrderLink',
            ),
            'source' => array(
                'title' => $this->l('Channel'),
                'align' => 'center',
            ),
            'total_paid' => array(
                'title' => $this->l('Order total'),
                'align' => 'center',
                'callback' => 'setPriceCurrency',
                'badge_success' => true,
            ),
            'total_paid_real' => array(
                'title' => $this->l('Recieved amount'),
                'align' => 'center',
                'callback' => 'setPriceCurrencyWithBadge',
            ),
            'date_add' => array(
                'title' => $this->l('Created on'),
                'align' => 'center',
            ),
        );

        $this->list_no_link = true;
    }

    public function getOrderLink($id_order, $row)
    {
        $displayData = '';
        if ($id_order) {
            $displayData .= '#'.$id_order;
            // $displayData .= '<a target="_blank" href="'.$this->context->link->getAdminLink('AdminOrders').'&id_order='.$id_order.
            // '&vieworder">#'.$id_order.'</a>';
        }
        return $displayData;
    }

    public function setPriceCurrency($value, $row)
    {
        if (Validate::isLoadedObject($objOrder = new Order($row['id_order']))) {
            return Tools::displayPrice($value, (int)$objOrder->id_currency);
        }
    }

    public function setPriceCurrencyWithBadge($value, $row)
    {
        $displayData = '';
        if (Validate::isLoadedObject($objOrder = new Order($row['id_order']))) {
            $displayData .= '<span class="badge '.(($row['total_paid'] == $row['total_paid_real']) ? 'badge-success' : 'badge-danger').'">';
                $displayData .= Tools::displayPrice($value, (int)$objOrder->id_currency);
            $displayData .= '</span>';
        }

        return $displayData;
    }

    public function displayViewLink($token, $idRow, $name = null)
    {
        if (Validate::isLoadedObject($objChannelManagerBooking = new QcmcChannelManagerBooking($idRow))) {
            return '<a class="btn btn-default" href="'.$this->context->link->getAdminLink('AdminOrders').'&id_order='.$objChannelManagerBooking->id_order.
            '&vieworder" title="'.$this->l('view details').'"><i class="icon-search-plus"></i> '.$this->l('View Order Detail').'</a>';
        }
    }

    public function renderList()
    {
        if ($channelManagerBookings = QcmcChannelManagerBooking::getChannelManagerBookings(0, 'DESC')) {
            $this->context->smarty->assign(
                array (
                    'icon' => 'icon-list',
                    'toolbar_title' => 'icon-list',
                    // As we get the bookings in descending order according to the date_add. So in the 0 index last booking will be found
                    'last_booking_datetime' => Tools::displayDate($channelManagerBookings[0]['date_add'], null, true),
                )
            );

            // because in helper list tpl_vars is given priority and we need different List title than list title
            // In HelperList.php assigned: 'title' => array_key_exists('title', $this->tpl_vars) ? $this->tpl_vars['title'] : $this->title
            $this->tpl_list_vars['title'] = $this->l('Below is the list of all the bookings created by channel manager.');

            unset($this->toolbar_btn['new']);

            $this->addRowAction('view');

            return parent::renderList();
        } else {
            $this->context->smarty->assign(
                array (
                    'module_dir' => _MODULE_DIR_,
                    'current_datetime' => Tools::displayDate(date('Y-m-d H:i:s'), null, true),
                )
            );
            $this->content .= $this->context->smarty->fetch(
                _PS_MODULE_DIR_.
                'qlochannelmanagerconnector/views/templates/admin/qloapps_channel_manager_connector/channel_manager_connect_info.tpl'
            );
        }
    }

    public function setMedia()
    {
        parent::setMedia();

        if (QcmcChannelManagerBooking::getChannelManagerBookings()) {
            $this->addCSS(_MODULE_DIR_.'qlochannelmanagerconnector/views/css/admin/wk_cm_booking_list.css');
            $this->addJS(_MODULE_DIR_.'qlochannelmanagerconnector/views/js/admin/wk_cm_booking_list.js');
        } else {
            $this->addCSS(_MODULE_DIR_.'qlochannelmanagerconnector/views/css/admin/wk_cm_info.css');
        }
    }
}
