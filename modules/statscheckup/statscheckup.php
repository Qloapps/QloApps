<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_.'hotelreservationsystem/define.php';

class StatsCheckUp extends Module
{
    private $html = '';
    private $id_hotel = 0;

    const ORDER_BY_ID = 1;
    const ORDER_BY_NAME = 2;
    const ORDER_BY_ORDERS = 3;

    public function __construct()
    {
        $this->name = 'statscheckup';
        $this->tab = 'analytics_stats';
        $this->version = '1.5.2';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Catalog evaluation');
        $this->description = $this->l('Adds a quick evaluation of your catalog quality to the Stats dashboard.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.7.0.99');
    }

    public function install()
    {
        $confs = array(
            'ROOM_TYPE_CHECKUP_DESCRIPTIONS_LT' => 100,
            'ROOM_TYPE_CHECKUP_DESCRIPTIONS_GT' => 400,
            'ROOM_TYPE_CHECKUP_IMAGES_LT' => 1,
            'ROOM_TYPE_CHECKUP_IMAGES_GT' => 2,
            'ROOM_TYPE_CHECKUP_ORDERS_LT' => 1,
            'ROOM_TYPE_CHECKUP_ORDERS_GT' => 2,
            'ROOM_TYPE_CHECKUP_TOTAL_ROOMS_LT' => 1,
            'ROOM_TYPE_CHECKUP_TOTAL_ROOMS_GT' => 3,
            'HOTEL_CHECKUP_DESCRIPTIONS_LT' => 100,
            'HOTEL_CHECKUP_DESCRIPTIONS_GT' => 400,
            'HOTEL_CHECKUP_IMAGES_LT' => 1,
            'HOTEL_CHECKUP_IMAGES_GT' => 2,
            'HOTEL_CHECKUP_ORDERS_LT' => 1,
            'HOTEL_CHECKUP_ORDERS_GT' => 2,
            'HOTEL_CHECKUP_TOTAL_ROOMS_LT' => 1,
            'HOTEL_CHECKUP_TOTAL_ROOMS_GT' => 3,
        );

        foreach ($confs as $confname => $confdefault) {
            if (!Configuration::get($confname)) {
                Configuration::updateValue($confname, (int)$confdefault);
            }
        }

        return (parent::install() && $this->registerHook('AdminStatsModules'));
    }

    public function hookAdminStatsModules()
    {
        $this->html = '
            <div class="panel-heading">'
                .$this->displayName.'
            </div>';

        $activeTab = 'hotels';

        if (Tools::isSubmit('submitCheckupRoomType')) {
            $confs = array(
                'ROOM_TYPE_CHECKUP_DESCRIPTIONS_LT',
                'ROOM_TYPE_CHECKUP_DESCRIPTIONS_GT',
                'ROOM_TYPE_CHECKUP_IMAGES_LT',
                'ROOM_TYPE_CHECKUP_IMAGES_GT',
                'ROOM_TYPE_CHECKUP_ORDERS_LT',
                'ROOM_TYPE_CHECKUP_ORDERS_GT',
                'ROOM_TYPE_CHECKUP_TOTAL_ROOMS_LT',
                'ROOM_TYPE_CHECKUP_TOTAL_ROOMS_GT',
            );

            foreach ($confs as $confname) {
                Configuration::updateValue($confname, (int)Tools::getValue($confname));
            }

            $this->html .= $this->displayConfirmation($this->l('Configuration updated.'));
        }

        if (Tools::isSubmit('submitCheckupHotel')) {
            $confs = array(
                'HOTEL_CHECKUP_DESCRIPTIONS_LT',
                'HOTEL_CHECKUP_DESCRIPTIONS_GT',
                'HOTEL_CHECKUP_IMAGES_LT',
                'HOTEL_CHECKUP_IMAGES_GT',
                'HOTEL_CHECKUP_ORDERS_LT',
                'HOTEL_CHECKUP_ORDERS_GT',
                'HOTEL_CHECKUP_TOTAL_ROOMS_LT',
                'HOTEL_CHECKUP_TOTAL_ROOMS_GT',
            );

            foreach ($confs as $confname) {
                Configuration::updateValue($confname, (int)Tools::getValue($confname));
            }

            $this->html .= $this->displayConfirmation($this->l('Configuration updated.'));
        }

        if (Tools::isSubmit('submitCheckupService')) {
            $confs = array(
                'SERVICE_CHECKUP_DESCRIPTIONS_SHORT_LT',
                'SERVICE_CHECKUP_DESCRIPTIONS_SHORT_GT',
                'SERVICE_CHECKUP_IMAGES_LT',
                'SERVICE_CHECKUP_IMAGES_GT',
                'SERVICE_CHECKUP_ORDERS_LT',
                'SERVICE_CHECKUP_ORDERS_GT',
            );

            foreach ($confs as $confname) {
                Configuration::updateValue($confname, (int)Tools::getValue($confname));
            }

            $this->html .= $this->displayConfirmation($this->l('Configuration updated.'));

            $activeTab = 'services';
        }

        if (Tools::isSubmit('submitCheckupOrder')) {
            $this->context->cookie->checkup_order = (int)Tools::getValue('submitCheckupOrder');
            $this->html .= $this->displayConfirmation($this->l('Configuration updated.'));
        }

        if (!isset($this->context->cookie->checkup_order)) {
            $this->context->cookie->checkup_order = self::ORDER_BY_ID;
        }

        if (Tools::getValue('id_hotel')) {
            $this->id_hotel = (int) Tools::getValue('id_hotel');
        }

        $this->html .= '
            <ul class="nav nav-tabs">
                <li '.($activeTab == 'hotels' ? 'class="active"' : '').'>
                    <a href="#statscheckup_hotels" data-toggle="tab">
                        <span>'.$this->l('Hotels').'</span>
                    </a>
                </li>
                <li '.($activeTab == 'services' ? 'class="active"' : '').'>
                    <a href="#statscheckup_services" data-toggle="tab">
                        <span>'.$this->l('Services').'</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content panel panel-sm">
                <div class="tab-pane '.($activeTab == 'hotels' ? 'active' : '').'" id="statscheckup_hotels">
                    '.$this->getHotelsTab().'
                </div>
                <div class="tab-pane '.($activeTab == 'services' ? 'active' : '').'" id="statscheckup_services">
                    '.$this->getServicesTab().'
                </div>
            </div>
        ';

        return $this->html;
    }

    private function getHotelsTab()
    {
        $employee = Context::getContext()->employee;
        $prop30 = ((strtotime($employee->stats_date_to.' 23:59:59') - strtotime($employee->stats_date_from.' 00:00:00')) / 60 / 60 / 24) / 30;

        // Get languages
        $sql = 'SELECT l.*
        FROM '._DB_PREFIX_.'lang l'
        .Shop::addSqlAssociation('lang', 'l');
        $languages = Db::getInstance()->executeS($sql);

        $array_colors = array(
            0 => '<img src="../modules/'.$this->name.'/img/red.png" alt="'.$this->l('Bad').'" title="'.$this->l('Bad').'" />',
            1 => '<img src="../modules/'.$this->name.'/img/orange.png" alt="'.$this->l('Average').'" title="'.$this->l('Average').'" />',
            2 => '<img src="../modules/'.$this->name.'/img/green.png" alt="'.$this->l('Good').'" title="'.$this->l('Good').'" />'
        );

        $divisor = 4;
        $totals = array('products' => 0, 'active' => 0, 'images' => 0, 'orders' => 0, 'total_rooms' => 0);
        foreach ($languages as $language) {
            $divisor++;
            $totals['description_'.$language['iso_code']] = 0;
        }

        // set data from here
        $result = null;
        if ($this->id_hotel) {
            $result = $this->getRoomTypeStats();

            if (!$result) {
                return $this->l('No room type was found.');
            }
        } else {
            $result = $this->getHotelStats();

            if (!$result) {
                return $this->l('No hotel was found.');
            }
        }

        $array_conf = array(
            'DESCRIPTIONS' => array('name' => $this->l('Descriptions'), 'text' => $this->l('chars (without HTML)')),
            'IMAGES' => array('name' => $this->l('Images'), 'text' => $this->l('images')),
            'ORDERS' => array('name' => $this->l('Orders'), 'text' => $this->l('orders / month')),
            'TOTAL_ROOMS' => array('name' => $this->l('Total rooms'), 'text' => $this->l('rooms')),
        );

        $hotelsHtml = '
        <form action="'.Tools::safeOutput(AdminController::$currentIndex.'&token='.Tools::getValue('token').'&module='.$this->name.($this->id_hotel ? '&id_hotel='.$this->id_hotel : '')).'" method="post" class="checkup form-horizontal">
            <table class="table checkup">
                <thead>
                    <tr>
                        <th></th>
                        <th><span class="title_box active">'.$array_colors[0].' '.$this->l('Not enough').'</span></th>
                        <th><span class="title_box active">'.$array_colors[2].' '.$this->l('Alright').'</span></th>
                    </tr>
                </thead>';
        foreach ($array_conf as $conf => $translations) {
            $confKeyLt = ($this->id_hotel ? 'ROOM_TYPE' : 'HOTEL').'_CHECKUP_'.$conf.'_LT';
            $confKeyGt = ($this->id_hotel ? 'ROOM_TYPE' : 'HOTEL').'_CHECKUP_'.$conf.'_GT';
            $hotelsHtml .= '
                <tbody>
                    <tr>
                        <td>
                            <label class="control-label col-lg-12">'.$translations['name'].'</label>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-lg-11 input-group">
                                    <span class="input-group-addon">'.$this->l('Less than').'</span>
                                    <input type="text" name="'.$confKeyLt.'" value="'.Tools::safeOutput(Tools::getValue($confKeyLt, Configuration::get($confKeyLt))).'" />
                                    <span class="input-group-addon">'.$translations['text'].'</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-lg-12 input-group">
                                    <span class="input-group-addon">'.$this->l('Greater than').'</span>
                                    <input type="text" name="'.$confKeyGt.'" value="'.Tools::safeOutput(Tools::getValue($confKeyGt, Configuration::get($confKeyGt))).'" />
                                    <span class="input-group-addon">'.$translations['text'].'</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>';
        }
        $hotelsHtml .= '</table>
            <button type="submit" name="'.($this->id_hotel ? 'submitCheckupRoomType' : 'submitCheckupHotel').'" class="btn btn-default pull-right">
                <i class="icon-save"></i> '.$this->l('Save').'
            </button>
        </form>
        <form action="'.Tools::safeOutput(AdminController::$currentIndex.'&token='.Tools::getValue('token').'&module='.$this->name.($this->id_hotel ? '&id_hotel='.$this->id_hotel : '')).'" method="post" class="form-horizontal alert">
            <div class="row">
                <div class="col-lg-12">
                    <label class="control-label pull-left">'.$this->l('Order by').'</label>
                    <div class="col-lg-3">
                        <select name="submitCheckupOrder" onchange="this.form.submit();">
                            <option value="'.self::ORDER_BY_ID.'">'.$this->l('ID').'</option>
                            <option value="'.self::ORDER_BY_NAME.'" '.($this->context->cookie->checkup_order == self::ORDER_BY_NAME ? 'selected="selected"' : '').'>'.$this->l('Name').'</option>
                            <option value="'.self::ORDER_BY_ORDERS.'" '.($this->context->cookie->checkup_order == self::ORDER_BY_ORDERS ? 'selected="selected"' : '').'>'.$this->l('Orders').'</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>
        <div style="overflow-x:auto">
        <table class="table checkup2">
            <thead>
                <tr>
                    <th><span class="title_box active">'.$this->l('ID').'</span></th>
                    <th><span class="title_box active">'.(!$this->id_hotel ? $this->l('Hotel') : $this->l('Room type')).'</span></th>
                    <th class="center"><span class="title_box active">'.$this->l('Active').'</span></th>';
        foreach ($languages as $language) {
            $hotelsHtml .= '<th><span class="title_box active">'.$this->l('Desc.').' ('.Tools::strtoupper($language['iso_code']).')</span></th>';
        }
        $hotelsHtml .= '
                    <th class="center"><span class="title_box active">'.$this->l('Images').'</span></th>
                    <th class="center"><span class="title_box active">'.$this->l('Orders').'</span></th>
                    <th class="center"><span class="title_box active">'.$this->l('Total rooms').'</span></th>
                    <th class="center"><span class="title_box active">'.$this->l('Overall status').'</span></th>
                    '.(!$this->id_hotel ? '<th class="center"><span class="title_box active">'.$this->l('Action').'</span></th>' : '').'
                </tr>
            </thead>
            <tbody>';


        $confPrefix = $this->id_hotel ? 'ROOM_TYPE_' : 'HOTEL_';
        $confCheckup = array(
            'IMAGES_LT' => Configuration::get($confPrefix.'CHECKUP_IMAGES_LT'),
            'IMAGES_GT' => Configuration::get($confPrefix.'CHECKUP_IMAGES_GT'),
            'ORDERS_LT' => Configuration::get($confPrefix.'CHECKUP_ORDERS_LT'),
            'ORDERS_GT' => Configuration::get($confPrefix.'CHECKUP_ORDERS_GT'),
            'TOTAL_ROOMS_LT' => Configuration::get($confPrefix.'CHECKUP_TOTAL_ROOMS_LT'),
            'TOTAL_ROOMS_GT' => Configuration::get($confPrefix.'CHECKUP_TOTAL_ROOMS_GT'),
            'DESCRIPTIONS_LT' => Configuration::get($confPrefix.'CHECKUP_DESCRIPTIONS_LT'),
            'DESCRIPTIONS_GT' => Configuration::get($confPrefix.'CHECKUP_DESCRIPTIONS_GT'),
        );

        foreach ($result as $row) {
            $totals['products']++;
            $scores = array(
                'active' => ($row['active'] ? 2 : 0),
                'images' => ($row['nbImages'] < $confCheckup['IMAGES_LT'] ? 0 : ($row['nbImages'] > $confCheckup['IMAGES_GT'] ? 2 : 1)),
                'orders' => (($row['nbOrders'] * $prop30 < $confCheckup['ORDERS_LT']) ? 0 : (($row['nbOrders'] * $prop30 > $confCheckup['ORDERS_GT']) ? 2 : 1)),
                'total_rooms' => (($row['totalRooms'] < $confCheckup['TOTAL_ROOMS_LT']) ? 0 : (($row['totalRooms'] > $confCheckup['TOTAL_ROOMS_GT']) ? 2 : 1)),
            );
            $totals['active'] += (int)$scores['active'];
            $totals['images'] += (int)$scores['images'];
            $totals['orders'] += (int)$scores['orders'];
            $totals['total_rooms'] += (int)$scores['total_rooms'];
            $descriptions = $this->getDescriptions($this->id_hotel ? 'room_type' : 'hotel', $row['id_object']);
            foreach ($descriptions as $description) {
                if (isset($description['iso_code']) && isset($description['description'])) {
                    $row['desclength_'.$description['iso_code']] = Tools::strlen(strip_tags($description['description']));
                }
                if (isset($description['iso_code'])) {
                    $scores['description_'.$description['iso_code']] = ($row['desclength_'.$description['iso_code']] < $confCheckup['DESCRIPTIONS_LT'] ? 0 : ($row['desclength_'.$description['iso_code']] > $confCheckup['DESCRIPTIONS_GT'] ? 2 : 1));
                    $totals['description_'.$description['iso_code']] += $scores['description_'.$description['iso_code']];
                }
            }
            $scores['average'] = array_sum($scores) / $divisor;
            $scores['average'] = ($scores['average'] < 1 ? 0 : ($scores['average'] > 1.5 ? 2 : 1));

            $objectLink = '';
            if ($this->id_hotel) {
                $objectLink = $this->context->link->getAdminLink('AdminProducts').'&updateproduct&id_product='.$row['id_object'];
            } else {
                $objectLink = $this->context->link->getAdminLink('AdminAddHotel').'&updatehtl_branch_info&id='.$row['id_object'];
            }
            $hotelsHtml .= '
                <tr>
                    <td>'.$row['id_object'].'</td>
                    <td><a href="'.$objectLink.'" target="_blank">'.Tools::substr($row['object_name'], 0, 42).'</a></td>
                    <td class="center">'.$array_colors[$scores['active']].'</td>';
            foreach ($languages as $language) {
                if (isset($row['desclength_'.$language['iso_code']])) {
                    $hotelsHtml .= '<td class="center">'.(int)$row['desclength_'.$language['iso_code']].' '.$array_colors[$scores['description_'.$language['iso_code']]].'</td>';
                } else {
                    $hotelsHtml .= '<td>0 '.$array_colors[0].'</td>';
                }
            }

            $objectViewLink = '';
            if (!$this->id_hotel) {
                $objectViewLink = $this->context->link->getAdminLink('AdminStats').'&module='.$this->name.'&id_hotel='.$row['id_object'];
            }

            $hotelsHtml .= '
                    <td class="center">'.(int)$row['nbImages'].' '.$array_colors[$scores['images']].'</td>
                    <td class="center">'.(int)$row['nbOrders'].' '.$array_colors[$scores['orders']].'</td>
                    <td class="center">'.(int)$row['totalRooms'].' '.$array_colors[$scores['total_rooms']].'</td>
                    <td class="center">'.$array_colors[$scores['average']].'</td>
                    '.(!$this->id_hotel ? '<td class="center"><a class="btn btn-sm btn-default" href="'.$objectViewLink.'" title="'.$this->l('View').'"><i class="icon icon-eye"></i></a></td>' : '').'
                </tr>';
        }

        $hotelsHtml .= '</tbody>';

        $totals['active'] = $totals['active'] / $totals['products'];
        $totals['active'] = ($totals['active'] < 1 ? 0 : ($totals['active'] > 1.5 ? 2 : 1));
        $totals['images'] = $totals['images'] / $totals['products'];
        $totals['images'] = ($totals['images'] < 1 ? 0 : ($totals['images'] > 1.5 ? 2 : 1));
        $totals['orders'] = $totals['orders'] / $totals['products'];
        $totals['orders'] = ($totals['orders'] < 1 ? 0 : ($totals['orders'] > 1.5 ? 2 : 1));
        $totals['total_rooms'] = $totals['total_rooms'] / $totals['products'];
        $totals['total_rooms'] = ($totals['total_rooms'] < 1 ? 0 : ($totals['total_rooms'] > 1.5 ? 2 : 1));
        foreach ($languages as $language) {
            $totals['description_'.$language['iso_code']] = $totals['description_'.$language['iso_code']] / $totals['products'];
            $totals['description_'.$language['iso_code']] = ($totals['description_'.$language['iso_code']] < 1 ? 0 : ($totals['description_'.$language['iso_code']] > 1.5 ? 2 : 1));
        }
        $totals['average'] = array_sum($totals) / $divisor;
        $totals['average'] = ($totals['average'] < 1 ? 0 : ($totals['average'] > 1.5 ? 2 : 1));

        $hotelsHtml .= '
            <tfoot>
                <tr>
                    <th colspan="2"></th>
                    <th class="center"><span class="title_box active">'.$this->l('Active').'</span></th>';
        foreach ($languages as $language) {
            $hotelsHtml .= '<th class="center"><span class="title_box active">'.$this->l('Desc.').' ('.Tools::strtoupper($language['iso_code']).')</span></th>';
        }
        $hotelsHtml .= '
                    <th class="center"><span class="title_box active">'.$this->l('Images').'</span></th>
                    <th class="center"><span class="title_box active">'.$this->l('Orders').'</span></th>
                    <th class="center"><span class="title_box active">'.$this->l('Total rooms').'</span></th>
                    <th class="center"><span class="title_box active">'.$this->l('Overall status').'</span></th>
                    '.(!$this->id_hotel ? '<th></th>' : '').'
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td class="center">'.$array_colors[$totals['active']].'</td>';
        foreach ($languages as $language) {
            $hotelsHtml .= '<td class="center">'.$array_colors[$totals['description_'.$language['iso_code']]].'</td>';
        }
        $hotelsHtml .= '
                    <td class="center">'.$array_colors[$totals['images']].'</td>
                    <td class="center">'.$array_colors[$totals['orders']].'</td>
                    <td class="center">'.$array_colors[$totals['total_rooms']].'</td>
                    <td class="center">'.$array_colors[$totals['average']].'</td>
                    '.(!$this->id_hotel ? '<td></td>' : '').'
                </tr>
                '.($this->id_hotel ? '
                <tr>
                    <td colspan="100%">
                        <a class="btn btn-default" href="'.$this->context->link->getAdminLink('AdminStats').'&module='.$this->name.''.'">
                            <i class="icon-arrow-circle-left"></i> '.$this->l('Back').'
                        </a>
                    </td>
                </tr>' : '').'
            </tfoot>
        </table></div>';

        return $hotelsHtml;
    }

    private function getServicesTab()
    {
        $employee = Context::getContext()->employee;
        $prop30 = ((strtotime($employee->stats_date_to.' 23:59:59') - strtotime($employee->stats_date_from.' 00:00:00')) / 60 / 60 / 24) / 30;

        // Get languages
        $sql = 'SELECT l.*
        FROM '._DB_PREFIX_.'lang l'
        .Shop::addSqlAssociation('lang', 'l');
        $languages = Db::getInstance()->executeS($sql);

        $array_colors = array(
            0 => '<img src="../modules/'.$this->name.'/img/red.png" alt="'.$this->l('Bad').'" title="'.$this->l('Bad').'" />',
            1 => '<img src="../modules/'.$this->name.'/img/orange.png" alt="'.$this->l('Average').'" title="'.$this->l('Average').'" />',
            2 => '<img src="../modules/'.$this->name.'/img/green.png" alt="'.$this->l('Good').'" title="'.$this->l('Good').'" />'
        );

        $divisor = 4;
        $totals = array('services' => 0, 'active' => 0, 'images' => 0, 'orders' => 0, 'associated_room_types' => 0);
        foreach ($languages as $language) {
            $divisor++;
            $totals['description_'.$language['iso_code']] = 0;
        }

        // set data from here
        $result = $this->getServiceStats();

        if (!$result) {
            return $this->l('No service was found.');
        }

        $array_conf = array(
            'DESCRIPTIONS_SHORT' => array('name' => $this->l('Descriptions'), 'text' => $this->l('chars (without HTML)')),
            'IMAGES' => array('name' => $this->l('Images'), 'text' => $this->l('images')),
            'ORDERS' => array('name' => $this->l('Orders'), 'text' => $this->l('orders / month')),
        );

        $servicesHtml = '
        <form action="'.Tools::safeOutput(AdminController::$currentIndex.'&token='.Tools::getValue('token').'&module='.$this->name).'" method="post" class="checkup form-horizontal">
            <table class="table checkup">
                <thead>
                    <tr>
                        <th></th>
                        <th><span class="title_box active">'.$array_colors[0].' '.$this->l('Not enough').'</span></th>
                        <th><span class="title_box active">'.$array_colors[2].' '.$this->l('Alright').'</span></th>
                    </tr>
                </thead>';
        foreach ($array_conf as $conf => $translations) {
            $confKeyLt = 'SERVICE_CHECKUP_'.$conf.'_LT';
            $confKeyGt = 'SERVICE_CHECKUP_'.$conf.'_GT';
            $servicesHtml .= '
                <tbody>
                    <tr>
                        <td>
                            <label class="control-label col-lg-12">'.$translations['name'].'</label>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-lg-11 input-group">
                                    <span class="input-group-addon">'.$this->l('Less than').'</span>
                                    <input type="text" name="'.$confKeyLt.'" value="'.Tools::safeOutput(Tools::getValue($confKeyLt, Configuration::get($confKeyLt))).'" />
                                    <span class="input-group-addon">'.$translations['text'].'</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-lg-12 input-group">
                                    <span class="input-group-addon">'.$this->l('Greater than').'</span>
                                    <input type="text" name="'.$confKeyGt.'" value="'.Tools::safeOutput(Tools::getValue($confKeyGt, Configuration::get($confKeyGt))).'" />
                                    <span class="input-group-addon">'.$translations['text'].'</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>';
        }
        $servicesHtml .= '</table>
            <button type="submit" name="submitCheckupService" class="btn btn-default pull-right">
                <i class="icon-save"></i> '.$this->l('Save').'
            </button>
        </form>
        <form action="'.Tools::safeOutput(AdminController::$currentIndex.'&token='.Tools::getValue('token').'&module='.$this->name).'" method="post" class="form-horizontal alert">
            <div class="row">
                <div class="col-lg-12">
                    <label class="control-label pull-left">'.$this->l('Order by').'</label>
                    <div class="col-lg-3">
                        <select name="submitCheckupOrder" onchange="this.form.submit();">
                            <option value="'.self::ORDER_BY_ID.'">'.$this->l('ID').'</option>
                            <option value="'.self::ORDER_BY_NAME.'" '.($this->context->cookie->checkup_order == self::ORDER_BY_NAME ? 'selected="selected"' : '').'>'.$this->l('Name').'</option>
                            <option value="'.self::ORDER_BY_ORDERS.'" '.($this->context->cookie->checkup_order == self::ORDER_BY_ORDERS ? 'selected="selected"' : '').'>'.$this->l('Orders').'</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>
        <div style="overflow-x:auto">
        <table class="table checkup2">
            <thead>
                <tr>
                    <th><span class="title_box active">'.$this->l('ID').'</span></th>
                    <th><span class="title_box active">'.$this->l('Service').'</span></th>
                    <th class="center"><span class="title_box active">'.$this->l('Active').'</span></th>
                    <th class="center"><span class="title_box active">'.$this->l('Associated room types').'</span></th>';
        foreach ($languages as $language) {
            $servicesHtml .= '<th class="center"><span class="title_box active">'.$this->l('Desc.').' ('.Tools::strtoupper($language['iso_code']).')</span></th>';
        }
        $servicesHtml .= '
                    <th class="center"><span class="title_box active">'.$this->l('Images').'</span></th>
                    <th class="center"><span class="title_box active">'.$this->l('Orders').'</span></th>
                    <th class="center"><span class="title_box active">'.$this->l('Overall status').'</span></th>
                    '.(!$this->id_hotel ? '<th class="center"><span class="title_box active">'.$this->l('Action').'</span></th>' : '').'
                </tr>
            </thead>
            <tbody>';


        $confCheckup = array(
            'IMAGES_LT' => Configuration::get('SERVICE_CHECKUP_IMAGES_LT'),
            'IMAGES_GT' => Configuration::get('SERVICE_CHECKUP_IMAGES_GT'),
            'ORDERS_LT' => Configuration::get('SERVICE_CHECKUP_ORDERS_LT'),
            'ORDERS_GT' => Configuration::get('SERVICE_CHECKUP_ORDERS_GT'),
            'DESCRIPTIONS_SHORT_LT' => Configuration::get('SERVICE_CHECKUP_DESCRIPTIONS_SHORT_LT'),
            'DESCRIPTIONS_SHORT_GT' => Configuration::get('SERVICE_CHECKUP_DESCRIPTIONS_SHORT_GT'),
        );

        foreach ($result as $row) {
            $totals['services']++;
            $scores = array(
                'active' => ($row['active'] ? 2 : 0),
                'images' => ($row['nbImages'] < $confCheckup['IMAGES_LT'] ? 0 : ($row['nbImages'] > $confCheckup['IMAGES_GT'] ? 2 : 1)),
                'orders' => (($row['nbOrders'] * $prop30 < $confCheckup['ORDERS_LT']) ? 0 : (($row['nbOrders'] * $prop30 > $confCheckup['ORDERS_GT']) ? 2 : 1)),
            );
            $totals['active'] += (int)$scores['active'];
            $totals['images'] += (int)$scores['images'];
            $totals['orders'] += (int)$scores['orders'];
            $descriptions = $this->getDescriptions('service', $row['id_object']);
            foreach ($descriptions as $description) {
                if (isset($description['iso_code']) && isset($description['description_short'])) {
                    $row['desclength_'.$description['iso_code']] = Tools::strlen(strip_tags($description['description_short']));
                }
                if (isset($description['iso_code'])) {
                    $scores['description_'.$description['iso_code']] = ($row['desclength_'.$description['iso_code']] < $confCheckup['DESCRIPTIONS_SHORT_LT'] ? 0 : ($row['desclength_'.$description['iso_code']] > $confCheckup['DESCRIPTIONS_SHORT_GT'] ? 2 : 1));
                    $totals['description_'.$description['iso_code']] += $scores['description_'.$description['iso_code']];
                }
            }
            $scores['average'] = array_sum($scores) / $divisor;
            $scores['average'] = ($scores['average'] < 1 ? 0 : ($scores['average'] > 1.5 ? 2 : 1));

            $objectLink = $this->context->link->getAdminLink('AdminNormalProducts').'&updateproduct&id_product='.$row['id_object'];
            $servicesHtml .= '
                <tr>
                    <td>'.$row['id_object'].'</td>
                    <td><a href="'.$objectLink.'" target="_blank">'.Tools::substr($row['object_name'], 0, 42).'</a></td>
                    <td class="center">'.$array_colors[$scores['active']].'</td>
                    <td class="center">'.$row['nbAssociatedRoomTypes'].'</td>';
            foreach ($languages as $language) {
                if (isset($row['desclength_'.$language['iso_code']])) {
                    $servicesHtml .= '<td class="center">'.(int)$row['desclength_'.$language['iso_code']].' '.$array_colors[$scores['description_'.$language['iso_code']]].'</td>';
                } else {
                    $servicesHtml .= '<td class="center">0 '.$array_colors[0].'</td>';
                }
            }

            $servicesHtml .= '
                    <td class="center">'.(int)$row['nbImages'].' '.$array_colors[$scores['images']].'</td>
                    <td class="center">'.(int)$row['nbOrders'].' '.$array_colors[$scores['orders']].'</td>
                    <td class="center">'.$array_colors[$scores['average']].'</td>
                </tr>';
        }

        $servicesHtml .= '</tbody>';

        $totals['active'] = $totals['active'] / $totals['services'];
        $totals['active'] = ($totals['active'] < 1 ? 0 : ($totals['active'] > 1.5 ? 2 : 1));
        $totals['images'] = $totals['images'] / $totals['services'];
        $totals['images'] = ($totals['images'] < 1 ? 0 : ($totals['images'] > 1.5 ? 2 : 1));
        $totals['orders'] = $totals['orders'] / $totals['services'];
        $totals['orders'] = ($totals['orders'] < 1 ? 0 : ($totals['orders'] > 1.5 ? 2 : 1));
        $totals['associated_room_types'] = $totals['associated_room_types'] / $totals['services'];
        $totals['associated_room_types'] = ($totals['associated_room_types'] < 1 ? 0 : ($totals['associated_room_types'] > 1.5 ? 2 : 1));
        foreach ($languages as $language) {
            $totals['description_'.$language['iso_code']] = $totals['description_'.$language['iso_code']] / $totals['services'];
            $totals['description_'.$language['iso_code']] = ($totals['description_'.$language['iso_code']] < 1 ? 0 : ($totals['description_'.$language['iso_code']] > 1.5 ? 2 : 1));
        }
        $totals['average'] = array_sum($totals) / $divisor;
        $totals['average'] = ($totals['average'] < 1 ? 0 : ($totals['average'] > 1.5 ? 2 : 1));

        $servicesHtml .= '
            <tfoot>
                <tr>
                    <th colspan="2"></th>
                    <th class="center"><span class="title_box active">'.$this->l('Active').'</span></th>
                    <th class="center"><span class="title_box active">'.$this->l('Associated room types').'</span></th>';
        foreach ($languages as $language) {
            $servicesHtml .= '<th class="center"><span class="title_box active">'.$this->l('Desc.').' ('.Tools::strtoupper($language['iso_code']).')</span></th>';
        }
        $servicesHtml .= '
                    <th class="center"><span class="title_box active">'.$this->l('Images').'</span></th>
                    <th class="center"><span class="title_box active">'.$this->l('Orders').'</span></th>
                    <th class="center"><span class="title_box active">'.$this->l('Overall status').'</span></th>
                    '.(!$this->id_hotel ? '<th></th>' : '').'
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td class="center">'.$array_colors[$totals['active']].'</td>
                    <td class="center">NA</td>';
        foreach ($languages as $language) {
            $servicesHtml .= '<td class="center">'.$array_colors[$totals['description_'.$language['iso_code']]].'</td>';
        }
        $servicesHtml .= '
                    <td class="center">'.$array_colors[$totals['images']].'</td>
                    <td class="center">'.$array_colors[$totals['orders']].'</td>
                    <td class="center">'.$array_colors[$totals['average']].'</td>
                </tr>
            </tfoot>
        </table></div>';

        return $servicesHtml;
    }

    public function getHotelStats()
    {
        $date_from = date('Y-m-d', strtotime($this->context->employee->stats_date_from));
        $date_to = date('Y-m-d', strtotime($this->context->employee->stats_date_to));
        $id_lang = $this->context->language->id;

        $order_by = 'hbi.`id`';
        if ($this->context->cookie->checkup_order == self::ORDER_BY_NAME) {
            $order_by = 'object_name';
        } elseif ($this->context->cookie->checkup_order == self::ORDER_BY_ORDERS) {
            $order_by = 'nbOrders DESC';
        }

        $sql = 'SELECT hbi.`id` AS id_object, hbil.`hotel_name` AS object_name, hbi.`active`,
        (
            SELECT COUNT(*)
            FROM '._DB_PREFIX_.'htl_image hi
            WHERE hi.`id_hotel` = hbi.`id`
        ) AS nbImages,
        (
            SELECT COUNT(DISTINCT hbd.`id_order`) FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            LEFT JOIN `'._DB_PREFIX_.'orders` o
            ON (o.`id_order` = hbd.`id_order`)
            WHERE hbd.`id_hotel` = hbi.`id` AND o.`valid` = 1
            AND hbd.`date_to` > "'.pSQL($date_from).'" AND hbd.`date_from` < "'.pSQL($date_to).'"
        ) AS nbOrders,
        (
            SELECT COUNT(*)
            FROM `'._DB_PREFIX_.'htl_room_information` hri
            WHERE hri.`id_hotel` = hbi.`id`
        ) AS totalRooms
        FROM `'._DB_PREFIX_.'htl_branch_info` hbi
        LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbil
        ON (hbil.`id` = hbi.`id` AND hbil.`id_lang` = '.(int) $id_lang .')
        ORDER BY '.$order_by;

        return Db::getInstance()->executeS($sql);
    }

    public function getRoomTypeStats()
    {
        $date_from = date('Y-m-d', strtotime($this->context->employee->stats_date_from));
        $date_to = date('Y-m-d', strtotime($this->context->employee->stats_date_to));
        $id_lang = $this->context->language->id;

        $order_by = 'p.`id_product`';
        if ($this->context->cookie->checkup_order == self::ORDER_BY_NAME) {
            $order_by = 'object_name';
        } elseif ($this->context->cookie->checkup_order == self::ORDER_BY_ORDERS) {
            $order_by = 'nbOrders DESC';
        }

        $sql = 'SELECT p.`id_product` AS id_object, p.`active`, pl.`name` AS object_name,
        (
            SELECT COUNT(*)
            FROM '._DB_PREFIX_.'image i
            WHERE i.`id_product` = p.`id_product`
        ) AS nbImages,
        (
            SELECT COUNT(DISTINCT hbd.`id_order`) FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = hbd.`id_order`)
            WHERE hbd.`id_product` = p.`id_product` AND o.`valid` = 1
            AND hbd.`date_to` > "'.pSQL($date_from).'" AND hbd.`date_from` < "'.pSQL($date_to).'"
        ) AS nbOrders,
        (
            SELECT COUNT(*)
            FROM `'._DB_PREFIX_.'htl_room_information` hri
            WHERE hri.`id_product` = p.`id_product`
        ) AS totalRooms
        FROM '._DB_PREFIX_.'product p
        LEFT JOIN '._DB_PREFIX_.'product_lang pl
        ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int) $id_lang.')
        LEFT JOIN '._DB_PREFIX_.'htl_room_type hrt
        ON (hrt.`id_product` = p.`id_product`)
        WHERE hrt.`id_hotel` = '.(int) $this->id_hotel.'
        ORDER BY '.$order_by;

        return Db::getInstance()->executeS($sql);
    }

    public function getServiceStats()
    {
        $date_from = date('Y-m-d', strtotime($this->context->employee->stats_date_from));
        $date_to = date('Y-m-d', strtotime($this->context->employee->stats_date_to));
        $id_lang = $this->context->language->id;

        $order_by = 'p.`id_product`';
        if ($this->context->cookie->checkup_order == self::ORDER_BY_NAME) {
            $order_by = 'object_name';
        } elseif ($this->context->cookie->checkup_order == self::ORDER_BY_ORDERS) {
            $order_by = 'nbOrders DESC';
        }

        $sql = 'SELECT p.`id_product` AS id_object, p.`active`, pl.`name` AS object_name,
        (
            SELECT COUNT(*)
            FROM '._DB_PREFIX_.'image i
            WHERE i.`id_product` = p.`id_product`
        ) AS nbImages,
        (
            SELECT COUNT(DISTINCT rsod.`id_order`) FROM `'._DB_PREFIX_.'htl_room_type_service_product_order_detail` rsod
            LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = rsod.`id_order`)
            LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON (hbd.`id` = rsod.`id_htl_booking_detail`)
            WHERE rsod.`id_product` = p.`id_product` AND o.`valid` = 1
            AND hbd.`date_to` > "'.pSQL($date_from).'" AND hbd.`date_from` < "'.pSQL($date_to).'"
        ) AS nbOrders,
        (
            SELECT COUNT(*)
            FROM `'._DB_PREFIX_.'htl_room_type_service_product` rsp
            WHERE rsp.`id_product` = p.`id_product`
        ) AS nbAssociatedRoomTypes
        FROM '._DB_PREFIX_.'product p
        LEFT JOIN '._DB_PREFIX_.'product_lang pl
        ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int) $id_lang.')
        WHERE p.`booking_product` = 0
        ORDER BY '.$order_by;

        return Db::getInstance()->executeS($sql);
    }

    public function getDescriptions($object_type, $id_object = null)
    {
        if ($object_type == 'hotel') {
            return Db::getInstance()->executeS(
                'SELECT l.`iso_code`, hbil.`description`
                FROM '._DB_PREFIX_.'htl_branch_info_lang hbil
                LEFT JOIN '._DB_PREFIX_.'lang l
                ON hbil.`id_lang` = l.`id_lang`
                WHERE hbil.`id` = '.(int) $id_object
            );
        } elseif ($object_type == 'room_type')  {
            return Db::getInstance()->executeS(
                'SELECT l.`iso_code`, pl.`description`
                FROM '._DB_PREFIX_.'product_lang pl
                LEFT JOIN '._DB_PREFIX_.'lang l
                ON pl.`id_lang` = l.`id_lang`
                INNER JOIN '._DB_PREFIX_.'htl_room_type hrt
                ON (hrt.`id_product` = pl.`id_product`)
                WHERE hrt.`id_hotel` = '.(int) $this->id_hotel.'
                AND pl.`id_product` = '.(int) $id_object
            );
        } elseif ($object_type == 'service')  {
            return Db::getInstance()->executeS(
                'SELECT l.`iso_code`, pl.`description_short`
                FROM '._DB_PREFIX_.'product p
                LEFT JOIN '._DB_PREFIX_.'product_lang pl
                ON (pl.`id_product` = p.`id_product`)
                LEFT JOIN '._DB_PREFIX_.'lang l
                ON pl.`id_lang` = l.`id_lang`
                WHERE p.`booking_product` = 0'
            );
        }
    }
}
