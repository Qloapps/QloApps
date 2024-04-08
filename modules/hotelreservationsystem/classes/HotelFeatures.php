<?php
/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class HotelFeatures extends ObjectModel
{
    public $name;
    public $parent_feature_id;
    public $position;
    public $active;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_features',
        'primary' => 'id',
        'multilang' => true,
        'fields' => array(
            'parent_feature_id' => array('type' => self::TYPE_INT, 'required' => true),
            'position' => array('type' => self::TYPE_INT),
            'active' => array('type' => self::TYPE_INT, 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            //lang fields
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true),
        )
    );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'hotel_features',
        'objectNodeName' => 'hotel_feature',
        'fields' => array(),
    );

    public function delete()
    {
        if ($id = $this->id) {
            if (Db::getInstance()->delete('htl_branch_features', '`feature_id` = '.(int) $id)) {
                return parent::delete();
            }
        }
        return false;
    }

    /**
     * [getFeatureInfoById :: To get feature in formation by its id]
     * @param  [int] $id [description]
     * @return [false | array]     [Returns false if no data found otherwise returns array of the information of the
     * feature]
     */
    public function getFeatureInfoById($id, $idLang = 0)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        return Db::getInstance()->getRow(
            'SELECT hf.`id` , hfl.`name`
            FROM `'._DB_PREFIX_.'htl_features` hf
            LEFT JOIN `'._DB_PREFIX_.'htl_features_lang` hfl
            ON (hfl.`id` = hf.`id` AND hfl.`id_lang` = '.(int)$idLang.')
            WHERE hf.`id`='.(int) $id
        );
    }

    /**
     * [HotelAllCommonFeaturesArray :: To get array of all hotel features information according to parents feature and
     * children features under parent feature]
     * @return [boolean] [If data found returns array of all common features as parent then children under this parent]
     */
    public function HotelAllCommonFeaturesArray($idLang = 0)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        $result = array();
        if ($parentFeatures = Db::getInstance()->executeS(
            'SELECT hf.`id`, hf.`position`, hfl.`name`
            FROM `'._DB_PREFIX_.'htl_features` hf
            LEFT JOIN `'._DB_PREFIX_.'htl_features_lang` hfl
            ON (hfl.`id` = hf.`id` AND hfl.`id_lang` = '.(int)$idLang.')
            WHERE hf.`parent_feature_id`=0  order by position'
        )) {
            foreach ($parentFeatures as $value) {
                $result[$value['id']]['name'] = $value['name'];
                $result[$value['id']]['id'] = $value['id'];
                $result[$value['id']]['position'] = $value['position'];
                if ($childFeatures = Db::getInstance()->executeS(
                    'SELECT hf.`id`, hfl.`name`
                    FROM `'._DB_PREFIX_.'htl_features` hf
                    LEFT JOIN `'._DB_PREFIX_.'htl_features_lang` hfl
                    ON (hfl.`id` = hf.`id` AND hfl.`id_lang` = '.(int)$idLang.')
                    WHERE hf.`parent_feature_id`='.(int) $value['id']
                )) {
                    foreach ($childFeatures as $value1) {
                        $result[$value['id']]['children'][] = $value1;
                    }
                }
            }
        }
        if (!$result) {
            return false;
        }
        return $result;
    }

    /**
     * [deleteHotelFeatures :: Deletes the feature which id is $deleteId and also delete all the child features which
     * parent id is $deleteId]
     * @param  [int] $deleteId [id(primary_key) of the htl_features table]
     * @return [boolean] [true if deleted otherwise false]
     */
    public function deleteHotelFeatures($deleteId, $deleteOnlyChilds = 0)
    {
        $sql = 'SELECT `id` FROM `'._DB_PREFIX_.'htl_features` WHERE `parent_feature_id`='.(int) $deleteId;

        if (!$deleteOnlyChilds) {
            $sql .= ' OR `id`='.(int) $deleteId;
        }
        if ($idsToDetele = Db::getInstance()->executeS($sql)) {
            foreach ($idsToDetele as $value) {
                $objHotelFeatures = new HotelFeatures($value['id']);
                $objHotelFeatures->delete();
            }
        }
        return true;
    }

    /**
     * [HotelBranchSelectedFeaturesArray :: To get array of all hotel features information according to parents and
     * children including which child features are selected for a hotel]
     * @param [array] $htl_features [array containing information of the features of a hotel]
     * @return [boolean] [If data found returns array of all common features as parent then children under this parent
     * and also set an index in the array that defines whether the child feature is selected for the hotel or not]
     */
    public function HotelBranchSelectedFeaturesArray($htl_features, $idLang = 0)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        $result = array();
        if ($parentFeatures = Db::getInstance()->executeS(
            'SELECT hf.`id`, hfl.`name`
            FROM `'._DB_PREFIX_.'htl_features` hf
            LEFT JOIN `'._DB_PREFIX_.'htl_features_lang` hfl
            ON (hfl.`id` = hf.`id` AND hfl.`id_lang` = '.(int)$idLang.')
            WHERE hf.`parent_feature_id`=0'
        )) {
            foreach ($parentFeatures as $value) {
                $result[$value['id']]['name'] = $value['name'];
                if ($childFeatures = Db::getInstance()->executeS(
                    'SELECT hf.`id`, hfl.`name`
                    FROM `'._DB_PREFIX_.'htl_features` hf
                    LEFT JOIN `'._DB_PREFIX_.'htl_features_lang` hfl
                    ON (hfl.`id` = hf.`id` AND hfl.`id_lang` = '.(int)$idLang.')
                    WHERE hf.`parent_feature_id`='.(int) $value['id']
                )) {
                    foreach ($childFeatures as $value1) {
                        $flag =0;
                        if ($htl_features) {
                            foreach ($htl_features as $ftr) {
                                if ($value1['id'] == $ftr['feature_id']) {
                                    $flag = 1;
                                }
                            }
                        }
                        if ($flag) {
                            $value1['selected'] = 1;
                        } else {
                            $value1['selected'] = 0;
                        }

                        $result[$value['id']]['children'][] = $value1;
                    }
                }
            }
        }
        if (!$result) {
            return false;
        }
        return $result;
    }

    public function getChildFeaturesByParentFeatureId($parent_feature_id)
    {
        return Db::getInstance()->executeS(
            'SELECT `id` FROM `'._DB_PREFIX_.'htl_features` WHERE `parent_feature_id`='.(int)$parent_feature_id
        );
    }

    public function updateHotelFeatureInfoByParentFeatureId($parent_feature_id, $update_params)
    {
        return Db::getInstance()->update('htl_features', $update_params, 'id='.(int) $parent_feature_id);
    }

    public function searchByName($query, $idLang = false)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        return Db::getInstance()->executeS(
            'SELECT hf.*, hfl.* FROM `'._DB_PREFIX_.'htl_features` hf
            LEFT JOIN `'._DB_PREFIX_.'htl_features_lang` hfl
            ON hfl.`id` = hf.`id`
            WHERE hfl.`name` LIKE \'%'.pSQL($query).'%\'
            AND hfl.`id_lang`='.(int) $idLang
        );
    }
}
