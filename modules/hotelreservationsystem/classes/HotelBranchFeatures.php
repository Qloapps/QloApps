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
                'id_hotel' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'feature_id' => array('type' => self::TYPE_STRING),
                'date_add' =>    array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
                'date_upd' =>    array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ));

        /**
         * [deleteBranchFeaturesByHotelId : For deleting features of a hotel by hotel id]
         * @param  [int] $htl_id [Hotel's id , which hotel's features to be deleted]
         * @return [Boolean] [true if deleted or false if no deleted]
         */
        public function deleteBranchFeaturesByHotelId($htl_id)
        {
            $delete = Db::getInstance()->delete('htl_branch_features', '`id_hotel`='.$htl_id);
            return $delete;
        }

        /**
         * [assignFeaturesToHotel : For assigning Hotel features to a hotel]
         * @param  [int] $id_hotel [Hotel's id , To which features to be assigned]
         * @param  [Array] $features [array of features to be assigned to the hotel]
         * @return [Boolean]           [true]
         */
        public function assignFeaturesToHotel($id_hotel, $features)
        {
            if ($features) {
                foreach ($features as $feature) {
                    $obj_htl_features = new HotelBranchFeatures();
                    $obj_htl_features->id_hotel = $id_hotel;
                    $obj_htl_features->feature_id = $feature;
                    $obj_htl_features->save();
                }
            }
            return true;
        }
    }
