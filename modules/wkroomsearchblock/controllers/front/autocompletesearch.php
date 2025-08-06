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

class WkRoomSearchBlockAutoCompleteSearchModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;

        $result = array('status' => false);
        $search_data = Tools::getValue('to_search_data');
        $location_category_id = Tools::getValue('location_category_id');
        $obj_htl_info = new HotelBranchInformation();
        if (isset($search_data) && $search_data) {
            $locationCategories = $obj_htl_info->getHotelCategoryTree($search_data);
            if ($locationCategories) {
                $this->context->smarty->assign(array('location_categories' => $locationCategories));
                $html = $this->context->smarty->fetch(
                    $this->module->getTemplatePath('location-options.tpl')
                );
                $result['status'] = true;
                $result['data'] = $html;
            }
        } elseif (isset($location_category_id) && $location_category_id) {
            $locationCategories = Category::getAllCategoriesName($location_category_id);
            if ($locationCategories) {
                $hotelsInfo = array();
                foreach ($locationCategories as $category) {
                    if ($hotelInfo = $obj_htl_info->hotelBranchInfoByCategoryId($category['id_category'])) {
                        $maxOrderDate = HotelOrderRestrictDate::getMaxOrderDate($hotelInfo['id']);
                        $maxOrderDate = date('Y-m-d', strtotime($maxOrderDate));
                        $preparationTime = (int) HotelOrderRestrictDate::getPreparationTime($hotelInfo['id']);
                        $hotelsInfo[] = array(
                            'id_hotel' => $hotelInfo['id'],
                            'id_category' => $hotelInfo['id_category'],
                            'hotel_name' => $hotelInfo['hotel_name'],
                            'max_order_date' => $maxOrderDate,
                            'preparation_time' => $preparationTime
                        );
                    }
                }

                $this->context->smarty->assign(array('hotels_info' => $hotelsInfo));
                $html = $this->context->smarty->fetch(
                    $this->module->getTemplatePath('hotel-options.tpl')
                );

                $result['status'] = true;
                $result['html_hotel_options'] = $html;
            }
        }

        $this->ajaxDie(json_encode($result));
    }
}
