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

		if (isset($search_data) && $search_data)
		{
			$return_data = $this->getHotelCategoryTree($search_data);

			if ($return_data)
			{
				$html = '';
				foreach ($return_data as $key => $value)
				{
					$html .= '<li value="'.$value['id_category'].'" tabindex="-1" class="search_result_li">'.$value['name'].'</li>';
				}
				$result['status'] = 'success';
				$result['data'] = $html;
			}
		}
		else if (isset($city_cat_id) && $city_cat_id)
		{
			$obj_htl_info = new HotelBranchInformation();
			$cat_ids = Category::getAllCategoriesName($city_cat_id);
			if ($cat_ids)
			{
				$html = '';
				foreach ($cat_ids as $key => $value)
				{
					$hotel_info = $obj_htl_info->hotelBranchInfoByCategoryId($value['id_category']);

					if ($hotel_info)
					{
						$html .= '<li class="hotel_name" data-hotel-cat-id="'.$hotel_info[0]['id_category'].'">'.$hotel_info[0]['hotel_name'].'</li>';
					}
				}
				$result['status'] = 'success';
				$result['data'] = $html;
			}
			else
				$result['status'] = 'failed2';
		}
		else
			$result['status'] = 'failed3';

		die(Tools::jsonEncode($result));
	}

	public function getHotelCategoryTree($search_data)
	{
		$sql = "SELECT cl.`id_category` , cl.`name` 
				FROM `"._DB_PREFIX_."category_lang` AS cl
				INNER JOIN `"._DB_PREFIX_."category` AS c ON (cl.id_category = c.id_category)
				WHERE cl.name LIKE '%$search_data%' AND c.level_depth NOT IN (0, 1, 5) and id_lang=".$this->context->language->id.' GROUP BY cl.`name`';

		$result = Db::getInstance()->executeS($sql);
		return $result;
	}
}