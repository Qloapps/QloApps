<?php

class WkRoomSearchBlockAutoCompleteSearchModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $result = array();
        $this->display_column_left = false;
        $this->display_column_right = false;

        $search_data = Tools::getValue('to_search_data');
        $city_cat_id = Tools::getValue('hotel_city_cat_id');

        /*Max date of ordering for order restrict*/
        $is_order_restrict_process = Tools::getValue('is_order_restrict_process');

        if (isset($is_order_restrict_process) && $is_order_restrict_process) {
            $hotel_category_id = Tools::getValue('hotel_category_id');
            $id_hotel = HotelBranchInformation::getHotelIdByIdCategory($hotel_category_id);
            $max_order_date = HotelOrderRestrictDate::getMaxOrderDate($id_hotel);
            $result['status'] = 'success';
            $result['max_order_date'] = date('Y-m-d', strtotime($max_order_date));
            die(Tools::jsonEncode($result));
        }
        /*End*/
        $obj_htl_info = new HotelBranchInformation();
        if (isset($search_data) && $search_data) {
            $return_data = $obj_htl_info->getHotelCategoryTree($search_data);
            if ($return_data) {
                $html = '';
                foreach ($return_data as $key => $value) {
                    $html .= '<li value="'.$value['id_category'].'" tabindex="-1" class="search_result_li">'.$value['name'].'</li>';
                }
                $result['status'] = 'success';
                $result['data'] = $html;
            }
        } elseif (isset($city_cat_id) && $city_cat_id) {
            $cat_ids = Category::getAllCategoriesName($city_cat_id);
            if ($cat_ids) {
                $html = '';
                foreach ($cat_ids as $key => $value) {
                    $hotel_info = $obj_htl_info->hotelBranchInfoByCategoryId($value['id_category']);

                    if ($hotel_info) {
                        $html .= '<li class="hotel_name" data-hotel-cat-id="'.$hotel_info[0]['id_category'].'">'.$hotel_info[0]['hotel_name'].'</li>';
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

        die(Tools::jsonEncode($result));
    }
}
