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

class HotelOrderRefundStages extends objectModel
{
    public $id;
    public $name;

    public static $definition = array(
        'table' => 'htl_order_refund_stages',
        'primary' => 'id',
        'fields' => array(
            'name' =>    array('type' => self::TYPE_STRING),
        ),
    );

    /**
     * [getNameById :: To get name of the order cancellation (refund) stage by its id]
     * @param  [int] $id [Id of the order refund stage]
     * @return [string|boolean]     [If name found of the passed id returns name of the stage else returns false]
     */
    public function getNameById($id)
    {
        $result = Db::getInstance()->getValue('SELECT `name` FROM `'._DB_PREFIX_.'htl_order_refund_stages` WHERE id='.$id);
        if ($result) {
            return $result;
        }
        return false;
    }

    /**
     * [getOrderRefundStages :: To get all possible Order refund stages]
     * @return [array|boolean] [if data found Returns array containing all stages information else returns false]
     */
    public function getOrderRefundStages()
    {
        $result = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'htl_order_refund_stages`');
        if ($result) {
            return $result;
        }
        return false;
    }
}
