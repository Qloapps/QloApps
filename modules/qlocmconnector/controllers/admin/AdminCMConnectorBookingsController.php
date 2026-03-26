<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@qloapps.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your needs
 * please refer to https://store.webkul.com/customisation-guidelines for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/afl-3.0.php Academic Free License 3.0
 */

class AdminCMConnectorBookingsController extends ModuleAdminController
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
                'title' => $this->l('Order ID'),
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
                'title' => $this->l('Received amount'),
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

    public function getOrderLink($idOrder)
    {
        $displayData = '';
        if ($idOrder) {
            $displayData .= '#'.$idOrder;
        }

        return $displayData;
    }

    public function setPriceCurrency($value, $row)
    {
        if (Validate::isLoadedObject($objOrder = new Order((int) $row['id_order']))) {
            return Tools::displayPrice($value, (int) $objOrder->id_currency);
        }
    }

    public function setPriceCurrencyWithBadge($value, $row)
    {
        $displayData = '';
        if (Validate::isLoadedObject($objOrder = new Order((int) $row['id_order']))) {
            $displayData .= '<span class="badge '.(($row['total_paid'] == $row['total_paid_real']) ? 'badge-success' : 'badge-danger').'">';
            $displayData .= Tools::displayPrice($value, (int) $objOrder->id_currency);
            $displayData .= '</span>';
        }

        return $displayData;
    }

    public function displayViewLink($token, $idRow, $name = null)
    {
        if (Validate::isLoadedObject($objChannelManagerBooking = new QcmcChannelManagerBooking((int) $idRow))) {
            return '<a class="btn btn-default" href="'.$this->context->link->getAdminLink('AdminOrders').'&id_order='.(int) $objChannelManagerBooking->id_order.
                '&vieworder" title="'.$this->l('view details').'"><i class="icon-search-plus"></i> '.$this->l('View order detail').'</a>';
        }
    }

    public function renderList()
    {
        if ($channelManagerBookings = QcmcChannelManagerBooking::getChannelManagerBookings(0, 'DESC')) {
            $this->context->smarty->assign(
                array(
                    // Bookings are fetched in DESC order, so index 0 is the most recent one.
                    'last_booking_datetime' => Tools::displayDate($channelManagerBookings[0]['date_add'], null, true),
                )
            );

            // HelperList prioritizes tpl vars title over list title.
            $this->tpl_list_vars['title'] = $this->l('Below is the list of all bookings created by Channel Manager.');

            unset($this->toolbar_btn['new']);
            $this->addRowAction('view');

            return parent::renderList();
        } else {
            $this->context->smarty->assign(
                array (
                    'module_dir' => _MODULE_DIR_.$this->module->name,
                    'current_datetime' => Tools::displayDate(date('Y-m-d H:i:s'), null, true),
                )
            );
            $this->content .= $this->context->smarty->fetch( _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/cm_connector/channel_manager_connect_info.tpl');
        }
    }

    public function setMedia()
    {
        parent::setMedia();

        if (QcmcChannelManagerBooking::getChannelManagerBookings()) {
            $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/admin/wk_cm_booking_list.css');
            $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/admin/wk_cm_booking_list.js');
        } else {
            $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/admin/wk_cm_info.css');
        }
    }
}
