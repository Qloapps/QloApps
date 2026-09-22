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

class QghcAriUpdates extends ObjectModel
{
    const ARI_TYPE_AVAILABILITY = 'availability';
    const ARI_TYPE_RATE         = 'rate';

    public $id_qghc_ari_updates;
    public $id_hotel;
    public $id_room_type;
    public $ari_date;
    public $ari_data;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table'   => 'qghc_ari_updates',
        'primary' => 'id_qghc_ari_updates',
        'fields'  => array(
            'id_hotel'     => array('type' => self::TYPE_INT,    'validate' => 'isUnsignedId', 'required' => true),
            'id_room_type' => array('type' => self::TYPE_INT,    'validate' => 'isUnsignedId', 'required' => true),
            'ari_date'     => array('type' => self::TYPE_DATE,   'validate' => 'isDate',       'required' => true),
            'ari_data'     => array('type' => self::TYPE_STRING),
            'date_add'     => array('type' => self::TYPE_DATE,   'validate' => 'isDate'),
            'date_upd'     => array('type' => self::TYPE_DATE,   'validate' => 'isDate'),
        ),
    );

    // Upsert one row per day in [dateFrom, dateTo] (inclusive).
    // $changedData keys: self::ARI_TYPE_AVAILABILITY => 1 (availability changed), self::ARI_TYPE_RATE => 1 (price/LOS changed).
    // On update, flags are OR-merged so a date that had both types retains both.
    public function saveChangedRow($idHotel, $idRoomType, $dateFrom, $dateTo, $changedData = array())
    {
        if (!$idHotel || !$idRoomType) {
            return false;
        }

        if (empty($changedData)) {
            $changedData = array(self::ARI_TYPE_AVAILABILITY => 1);
        }

        $existing = $this->getExistingRows((int)$idHotel, (int)$idRoomType, $dateFrom, $dateTo);

        for ($date = $dateFrom; $date <= $dateTo; $date = date('Y-m-d', strtotime($date . ' +1 day'))) {
            if (isset($existing[$date])) {
                $existingChangedData = $existing[$date]['ari_data']
                    ? json_decode($existing[$date]['ari_data'], true)
                    : array();
                $mergedChangedData = is_array($existingChangedData)
                    ? array_merge($existingChangedData, $changedData)
                    : $changedData;

                Db::getInstance()->execute(
                    'UPDATE `' . _DB_PREFIX_ . 'qghc_ari_updates`
                     SET `date_upd` = NOW(),
                         `ari_data` = \'' . pSQL(json_encode($mergedChangedData)) . '\'
                     WHERE `id_qghc_ari_updates` = ' . (int)$existing[$date]['id_qghc_ari_updates']
                );
            } else {
                $result = Db::getInstance()->insert(
                    'qghc_ari_updates',
                    array(
                        'id_hotel'     => (int)$idHotel,
                        'id_room_type' => (int)$idRoomType,
                        'ari_date'     => $date,
                        'ari_data'     => json_encode($changedData),
                        'date_add'     => date('Y-m-d H:i:s'),
                        'date_upd'     => date('Y-m-d H:i:s'),
                    ),
                    false,
                    true,
                    Db::INSERT_IGNORE
                );
                if (!$result) {
                    // insert failed — silently skip; the ARI sync will catch up on next push
                }
            }
        }

        return true;
    }

    // Returns all changed rows for a property including ari_data.
    public function getChangedRows($idHotel)
    {
        return Db::getInstance()->executeS(
            'SELECT `id_qghc_ari_updates`, `id_hotel`, `id_room_type`, `ari_date`, `ari_data`
             FROM `' . _DB_PREFIX_ . 'qghc_ari_updates`
             WHERE `id_hotel` = ' . (int)$idHotel . '
             ORDER BY `id_room_type` ASC, `ari_date` ASC'
        );
    }

    // Deletes fetched rows by primary key after CM has confirmed receipt.
    public function deleteChangedRowsByIds($ids)
    {
        if (empty($ids)) {
            return;
        }
        $idList = implode(',', array_map('intval', $ids));
        Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'qghc_ari_updates`
             WHERE `id_qghc_ari_updates` IN (' . $idList . ')'
        );
    }

    private function getExistingRows($idHotel, $idRoomType, $dateFrom, $dateTo)
    {
        $rows = Db::getInstance()->executeS(
            'SELECT `id_qghc_ari_updates`, `ari_date`, `ari_data`
             FROM `' . _DB_PREFIX_ . 'qghc_ari_updates`
             WHERE `id_hotel` = ' . (int)$idHotel . '
             AND `id_room_type` = ' . (int)$idRoomType . '
             AND `ari_date` BETWEEN \'' . pSQL($dateFrom) . '\' AND \'' . pSQL($dateTo) . '\''
        );

        $result = array();
        if ($rows) {
            foreach ($rows as $row) {
                $result[date('Y-m-d', strtotime($row['ari_date']))] = $row;
            }
        }
        return $result;
    }
}
