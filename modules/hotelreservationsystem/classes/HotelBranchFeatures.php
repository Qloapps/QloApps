<?php
/**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

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
     * @param  [int] $idHotel [Hotel's id , which hotel's features to be deleted]
     * @return [Boolean] [true if deleted or false if no deleted]
     */
    public function deleteBranchFeaturesByHotelId($idHotel)
    {
        return Db::getInstance()->delete('htl_branch_features', '`id_hotel` = '.(int) $idHotel);
    }

    /**
     * [assignFeaturesToHotel : For assigning Hotel features to a hotel]
     * @param  [int] $idHotel [Hotel's id , To which features to be assigned]
     * @param  [Array] $features [array of features to be assigned to the hotel]
     * @return [Boolean]           [true]
     */
    public function assignFeaturesToHotel($idHotel, $features)
    {
        if ($features) {
            foreach ($features as $feature) {
                $obj_htl_features = new HotelBranchFeatures();
                $obj_htl_features->id_hotel = $idHotel;
                $obj_htl_features->feature_id = $feature;
                $obj_htl_features->save();
            }
        }
        return true;
    }
}
