<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Badge extends ObjectModel
{
    public $id;
    
    public $id_ps_badge;
        
    public $type;
    
    public $id_group;
    
    public $group_position;
    
    public $group_name;

    public $scoring;
    
    public $validated;
    
    public $name;
    
    public $description;

    public $awb;
    
    const BAGDE_IMG_URL = 'gamification.prestashop.com/api/getBadgeImg';
    
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'badge',
        'primary' => 'id_badge',
        'multilang' => true,
        'fields' => array(
            'id_ps_badge' =>        array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'type' =>                array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 32),
            'id_group' =>            array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 32),
            'group_position' =>    array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'scoring' =>            array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'validated' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'awb' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),

            // Lang fields
            'name' =>                array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 64),
            'description' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
            'group_name' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
        ),
    );
    
    public function getBadgeImgUrl()
    {
        return Tools::getShopProtocol().self::BAGDE_IMG_URL.'/'.(int)$this->id_ps_badge.'/'.(int)$this->validated.'.png';
    }
    
    public function validate()
    {
        $this->validated = 1;
        $this->save();
        return true;
    }
    
    public static function getIdByIdPs($id_ps_badge)
    {
        $query = new DbQuery();
        $query->select('id_badge');
        $query->from('badge', 'b');
        $query->where('`id_ps_badge` = '.(int)$id_ps_badge);
        
        return (int)Db::getInstance()->getValue($query);
    }
    
    public static function getIdsBadgesToValidate()
    {
        $ids = array();
        $query = new DbQuery();
        $query->select('b.`id_badge`');
        $query->from('badge', 'b');
        $query->join('
			LEFT JOIN `'._DB_PREFIX_.'condition_badge` cb ON cb.`id_badge` = b.`id_badge` 
			LEFT JOIN `'._DB_PREFIX_.'condition` c ON c.`id_condition` = cb.`id_condition` AND c.`validated` = 1');
        $query->where('b.validated = 0');
        $query->groupBy('b.`id_badge`');
        $query->having('count(*) = SUM(c.validated)');

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        
        foreach ($result as $badge) {
            $ids[] = $badge['id_badge'];
        }
        return $ids;
    }
    
    public function getNextBadgeId()
    {
        $query = new DbQuery();
        $query->select('b.`id_badge`');
        $query->from('badge', 'b');
        $query->where('b.id_group = \''.pSQL($this->id_group).'\' AND b.validated = 0');
        $query->orderBy('b.group_position');
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }
}
