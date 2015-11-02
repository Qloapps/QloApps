<?php
	class HotelBranchFeatures extends ObjectModel
	{
		public $id;
		public $id_hotel;
		public $feature_id;
		public $date_add;
		public $date_upd;
		public static $definition = array(
			'table' => 'htl_branch_features',
			'primary' => 'id',
			'fields' => array(
				'id_hotel' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'feature_id' => array('type' => self::TYPE_STRING),
				'date_add' =>  	array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
				'date_upd' =>  	array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
		));

		public function deleteBranchFeaturesByHotelId($htl_id)
		{
			$delete = Db::getInstance()->delete('htl_branch_features', '`id_hotel`='.$htl_id);
			return $delete;
		}

		public function assignFeaturesToHotel($id_hotel, $features)
		{
			if ($features)
			{
				foreach($features as $feature)
				{
					$obj_htl_features = new HotelBranchFeatures();
					$obj_htl_features->id_hotel = $id_hotel;
					$obj_htl_features->feature_id = $feature;
					$obj_htl_features->save();
				}
			}
			return true;
		}
	
	}