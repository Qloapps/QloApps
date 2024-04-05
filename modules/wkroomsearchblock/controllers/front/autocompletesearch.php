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
        $result = array();
        $this->display_column_left = false;
        $this->display_column_right = false;
        $search_data = Tools::getValue('to_search_data');
        $location_category_id = Tools::getValue('location_category_id');
        $obj_htl_info = new HotelBranchInformation();
        if (isset($search_data) && $search_data) {
            $return_data = $obj_htl_info->getHotelCategoryTree($search_data);
            if ($return_data) {
                $html = '';
                foreach ($return_data as $value) {
                    $html .= '<li value="'.$value['id_category'].'" tabindex="-1" class="search_result_li">'.
                    $value['name'].'</li>';
                }
                $result['status'] = 'success';
                $result['data'] = $html;
            }
        } elseif (isset($location_category_id) && $location_category_id) {
            $cat_ids = Category::getAllCategoriesName($location_category_id);
            if ($cat_ids) {
                $html = '';
                foreach ($cat_ids as $value) {
                    if ($hotel_info = $obj_htl_info->hotelBranchInfoByCategoryId($value['id_category'])) {
                        $maxOrderDate = HotelOrderRestrictDate::getMaxOrderDate($hotel_info['id']);
                        $preparationTime = (int) HotelOrderRestrictDate::getPreparationTime($hotel_info['id']);
                        $maxOrderDate = date('Y-m-d', strtotime($maxOrderDate));
                        $html .= '<li tabindex="-1" class="search_result_li" data-id-hotel="'.$hotel_info['id'].'" data-hotel-cat-id="'.
                        $hotel_info['id_category'].'" data-max_order_date="'.$maxOrderDate.'" data-preparation_time="'.$preparationTime.'">'.
                        $hotel_info['hotel_name'].'</li>';
                    }
                }
                $result['status'] = 'success';
                $result['data'] = $html;
            } else {
                $result['status'] = 'failed2';
            }
        } else {
            $result['status'] = 'failed3';
        }
        die(json_encode($result));
    }
}
