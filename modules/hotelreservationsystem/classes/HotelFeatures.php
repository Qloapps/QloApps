<?php
	class HotelFeatures extends ObjectModel
	{
		public $id;
		public $name;
		public $parent_feature_id;
		public $position;
		public $active;
		public $date_add;
		public $date_upd;
		public static $definition = array(
			'table' => 'htl_features',
			'primary' => 'id',
			'fields' => array(
				'name' => 		   		array('type' => self::TYPE_STRING),
				'parent_feature_id' =>  array('type' => self::TYPE_INT),
				'position' => 			array('type' => self::TYPE_INT),
				'active' =>  			array('type' => self::TYPE_INT),
				'date_add' =>  	 		array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
				'date_upd' =>  	 		array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
		));

		public function getFeatureInfoById($id)
		{
			$result = Db::getInstance()->getRow('SELECT id , name FROM `'._DB_PREFIX_.'htl_features` WHERE id='.$id);
			if ($result)
				return $result;
			return false;
		}

		public function HotelAllCommonFeaturesArray()
		{
			$parent_features = Db::getInstance()->executeS('SELECT id , name, position FROM `'._DB_PREFIX_.'htl_features` WHERE parent_feature_id=0 order by position');
			if ($parent_features)
			{
				foreach ($parent_features as $value)
				{
					$result[$value['id']]['name'] = $value['name'];
					$result[$value['id']]['id'] = $value['id'];
					$result[$value['id']]['position'] = $value['position'];
					$child_features = Db::getInstance()->executeS('SELECT id , name FROM `'._DB_PREFIX_.'htl_features` WHERE parent_feature_id='.$value['id']);
					if ($child_features)
					{
						foreach ($child_features as $value1)
						{
							$result[$value['id']]['children'][] = $value1;
						}
					}
				}
			}
			else
				return false;
			if (!$result)
				return false;
			return $result;
		}

		public function deleteHotelFeatures($delete_id)
		{
			$deleteHotelFeature = Db::getInstance()->delete('htl_features','id='.$delete_id.' OR parent_feature_id='.$delete_id);
			if ($deleteHotelFeature)
				return true;
			return false;
		}

		public function HotelBranchSelectedFeaturesArray($htl_features)
		{
			$parent_features = Db::getInstance()->executeS('SELECT id , name FROM `'._DB_PREFIX_.'htl_features` WHERE parent_feature_id=0');
			if ($parent_features)
			{
				foreach ($parent_features as $value)
				{
					$result[$value['id']]['name'] = $value['name'];
					$child_features = Db::getInstance()->executeS('SELECT id , name FROM `'._DB_PREFIX_.'htl_features` WHERE parent_feature_id='.(int)$value['id']);
					if ($child_features)
					{
						foreach ($child_features as $value1)
						{
							$flag =0;
							if ($htl_features)
							{
								foreach ($htl_features as $ftr)
								{
									if ($value1['id'] == $ftr['feature_id'])
										$flag = 1;
								}
							}
							if ($flag)
								$value1['selected'] = 1;
							else
								$value1['selected'] = 0;

							$result[$value['id']]['children'][] = $value1;
						}
					}
					else
						return false;
				}
			}
			else
				return false;
			if (!$result)
				return false;
			return $result;
		}
	}