<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
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
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

class AdminQloappsChannelManagerConnectorController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'order';
        $this->className = 'Order';
        $this->identifier = 'id_order';
        $this->lang = false;
        $this->deleted = false;
        $this->bootstrap = true;
        $this->toolbar_title = $this->l('Channel Manager Bookings');
        $this->context = Context::getContext();

        parent::__construct();

        // 'a' is the orders row itself now (no separate booking table) — an order is CM-sourced
        // if id_channel_manager_booking is set, OR its source isn't the shop's own domain (covers
        // CM bookings where the channel manager never sent id_channel_manager_booking).
        $this->_where = ' AND ( ( a.`id_channel_manager_booking` IS NOT NULL AND a.`id_channel_manager_booking` != \'\' )
            OR a.`source` != \''.pSQL(Configuration::get('PS_SHOP_DOMAIN')).'\' ) ';

        $this->_orderBy = 'date_add';
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

    public function getOrderLink($idOrder, $row)
    {
        $displayData = '';
        if ($idOrder) {
            $displayData .= '#'.$idOrder;
        }
        return $displayData;
    }

    public function setPriceCurrency($value, $row)
    {
        return Tools::displayPrice($value, (int)$row['id_currency']);
    }

    public function setPriceCurrencyWithBadge($value, $row)
    {
        $displayData = '';
        $displayData .= '<span class="badge '.(($row['total_paid'] == $row['total_paid_real']) ? 'badge-success' : 'badge-danger').'">';
            $displayData .= Tools::displayPrice($value, (int)$row['id_currency']);
        $displayData .= '</span>';

        return $displayData;
    }

    public function displayViewLink($token, $idRow, $name = null)
    {
        return '<a class="btn btn-default" href="'.$this->context->link->getAdminLink('AdminOrders').'&id_order='.(int)$idRow.
        '&vieworder" title="'.$this->l('view details').'"><i class="icon-search-plus"></i> '.$this->l('View Order Detail').'</a>';
    }

    public function renderList()
    {
        if (QcmcChannelManagerOrder::hasChannelManagerBookings()) {
            $this->context->smarty->assign(
                array (
                    'icon' => 'icon-list',
                    'toolbar_title' => 'icon-list',
                    'last_booking_datetime' => Tools::displayDate(QcmcChannelManagerOrder::getLastChannelManagerBookingDate(), null, true),
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
                'qlocmconnector/views/templates/admin/qloapps_channel_manager_connector/qcmc_channel_manager_connect_info.tpl'
            );
        }
    }

    public function setMedia()
    {
        parent::setMedia();

        if (QcmcChannelManagerOrder::hasChannelManagerBookings()) {
            $this->addCSS(_MODULE_DIR_.'qlocmconnector/views/css/admin/qcmc_cm_booking_list.css');
            $this->addJS(_MODULE_DIR_.'qlocmconnector/views/js/admin/qcmc_cm_booking_list.js');
        } else {
            $this->addCSS(_MODULE_DIR_.'qlocmconnector/views/css/admin/qcmc_cm_info.css');
        }
    }
}
