<?php

/**
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/afl-3.0.php
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
* @license https://opensource.org/licenses/afl-3.0.php Academic Free License 3.0
*/

class QcmcAriUpdates extends ObjectModel
{
    public $id_ari_updates;
    public $id_hotel;
    public $id_room_type;
    public $date;
    public $ari_data;
    public $status;
    public $date_add;
    public $date_upd;

    public const ARI_SYNC_PROCESSING = 1;
    public const ARI_SYNC_NOT_PROCESSED = 0;

    public static $definition = array(
        'table' => 'ari_updates',
        'primary' => 'id_ari_updates',
        'fields' => array(
            'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_room_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'date' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'ari_data' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'status' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    //Returns the true/false on the basis, if any row in the table is present for same room type and date
    public function getExistingAriUpdateData($idRoomType, $date)
    {
        $sql = 'SELECT `id_ari_updates`
                FROM `'._DB_PREFIX_.'ari_updates`
                WHERE id_room_type = '.(int)$idRoomType.'
                AND `date` = "'.pSQL($date).'"';

        return Db::getInstance()->getValue($sql);
    }

    //Returns the row hotel (order) wise where the queue row is not processed
    public function getPendingAriUpdateRows()
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'ari_updates`
                WHERE status = ' . (int) self::ARI_SYNC_NOT_PROCESSED . '
                ORDER BY id_hotel ASC, date_add ASC';

        return Db::getInstance()->executeS($sql);
    }

    //Universal function to save rows in the table, currently handles availablity only
    public function saveAriUpdateRow($idHotel, $idRoomType, $dateFrom, $dateTo)
    {
        if (empty($idHotel) || empty($idRoomType)) {
            return false;
        }
        // for loop from dateFrom to dateTo
        for ($date = $dateFrom; $date <= $dateTo; $date = date('Y-m-d', strtotime($date . ' +1 day'))) {
            if (!$this->getExistingAriUpdateData($idRoomType, $date)) {
                $objAriUpdates = new QcmcAriUpdates();
                $objAriUpdates->id_hotel = (int)$idHotel;
                $objAriUpdates->id_room_type = (int)$idRoomType;
                $objAriUpdates->date = pSQL($date);
                $objAriUpdates->ari_data = json_encode(['availability' => 1]);
                $objAriUpdates->status = self::ARI_SYNC_NOT_PROCESSED;
                $idinserted = $objAriUpdates->save();
            }
        }
        return true;
    }

    //mark the rows in the table under-processing
    public function markProcessingAriUpdateData($ids)
    {
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $objAriUpdates = new QcmcAriUpdates((int)$id);
                if (Validate::isLoadedObject($objAriUpdates)) {
                    $objAriUpdates->status   = self::ARI_SYNC_PROCESSING;
                    $objAriUpdates->date_upd = date('Y-m-d H:i:s');
                    $objAriUpdates->update();
                }
            }
        }
    }

    //Delete the rows which gets successfully processed
    public function deleteAriUpdateDataByIds($ids)
    {
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $objAriUpdates = new QcmcAriUpdates((int)$id);
                if (Validate::isLoadedObject($objAriUpdates)) {
                    $objAriUpdates->delete();
                }
            }
        }
    }

    //If the response return false then mark all the rows to not processed which were marked as processing
    public function resetAriUpdateDataToPending($ids)
    {
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $objAriUpdates = new QcmcAriUpdates((int)$id);
    
                if (Validate::isLoadedObject($objAriUpdates)) {
                    $objAriUpdates->status   = self::ARI_SYNC_NOT_PROCESSED;
                    $objAriUpdates->date_upd = date('Y-m-d H:i:s');
                    $objAriUpdates->update();
                }
            }
        }
    }
}