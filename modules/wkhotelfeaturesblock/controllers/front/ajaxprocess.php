<?php
class WkHotelFeaturesBlockAjaxProcessModuleFrontController extends ModuleFrontController
{
	public function init()
	{
		$this->display_column_left = false;
		$this->display_column_right = false;
	}

	public function initContent()
	{
		$this->display_column_left = false;
		$this->display_column_right = false;

		$id_row = Tools::getValue('id_feature_row');

		if (isset($id_row) && $id_row)
		{
			$result = Db::getInstance()->delete('htl_features_block_data','id='.$id_row);

			if($result)
				die(1);
			else
				die(0);
		}
	}
}
?>