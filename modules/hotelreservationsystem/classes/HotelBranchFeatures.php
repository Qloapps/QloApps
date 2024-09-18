<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
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
