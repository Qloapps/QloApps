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

class AdminExportIcalFileController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();

        parent::__construct();
    }

    public function postProcess()
    {
        if (!Tools::getValue('action')) {
            $method = Tools::getValue('ics_method');
            if ($method == 'export_order_bookings') {
                if ($idOrder = Tools::getValue('id_order')) {
                    // get bookings ics file from a specific order order
                    header('Content-Type: text/calendar; charset=utf-8');
                    header('Content-Disposition: attachment; filename=qlo_bookings.ics');
                    $params = array();
                    $params['id_order'] = $idOrder;
                    $objIcalHelper = new QieIcalHelper();
                    echo $objIcalHelper->getBookingsICalendarValues($params);

                    exit;
                } else {
                    die(Tools::displayError('The order ID is missing.'));
                }
            } elseif ($method == 'export_all_bookings') {
                // get ics file for all the bookings from all orders
                header('Content-Type: text/calendar; charset=utf-8');
                header('Content-Disposition: attachment; filename=qlo_bookings.ics');
                
                $objIcalHelper = new QieIcalHelper();
                $params = array();
                $params['date_from'] = Tools::getValue('ics_date_from');
                $params['date_to'] = Tools::getValue('ics_date_to');
                $params['hotels'] = Tools::getValue('ics_hotels');
                echo $objIcalHelper->getBookingsICalendarValues($params);

                exit;
            } else {
                die(Tools::displayError($this->l('Action is missing.')));
            }
        }

        parent::postProcess();
    }

    public function ajaxProcessValidateIcsForm()
    {
        $result = array();
        $result['status'] = 0;
        $result['errors'] = array();

        $dateFrom = Tools::getValue('ics_date_from');
        $dateTo = Tools::getValue('ics_date_to');
        $hotels = Tools::getValue('ics_hotels');

        if ($dateFrom || $dateTo) {
            if ($dateFrom) {
                $dateFrom = date('Y-m-d', strtotime($dateFrom));
                if (!Validate::isDate($dateFrom)) {
                    $result['errors']['ics_date_from'] = $this->l('Start date is invalid.');
                }
            } else {
                $result['errors']['ics_date_from'] = $this->l('Start date is invalid.');
            }

            if ($dateTo) {
                $dateTo = date('Y-m-d', strtotime($dateTo));
                if (!Validate::isDate($dateTo)) {
                    $result['errors']['ics_date_to'] = $this->l('End date is invalid.');
                }
            } else {
                $result['errors']['ics_date_to'] = $this->l('End date is invalid.');
            }

            if ($dateFrom && $dateTo) {
                if (strtotime($dateFrom) > strtotime($dateTo)) {
                    $result['errors']['ics_date_to'] = $this->l('End date is invalid.');
                }
            }
        }
        if (!$hotels) {
            $result['errors']['ics_hotels'] = $this->l('Please select atleast one hotel or all hotels from the option.');
        }
        $params = array();
        $params['date_from'] = Tools::getValue('ics_date_from');
        $params['date_to'] = Tools::getValue('ics_date_to');
        $params['hotels'] = Tools::getValue('ics_hotels');
        $objIcalHelper = new QieIcalHelper();
        if (!$objIcalHelper->getBookingOrders($params)) {
            $result['errors']['ics_no_bookings'] = $this->l('No bookings are available.');
        }
        if (!count($result['errors'])) {
            $result['status'] = 1;
        }
        echo Tools::jsonEncode($result);

        die;
    }
}
