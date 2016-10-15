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

class Condition extends ObjectModel
{
    public $id;
    
    public $id_ps_condition;
        
    public $type;
    
    public $request;
    
    public $operator;
    
    public $value;
    
    public $result;
    
    public $calculation_type;
    
    public $calculation_detail;
    
    public $validated;
    
    public $date_add;

    public $date_upd;
    
    public static $unauthorized = array('DELETE', 'DROP');
    
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'condition',
        'primary' => 'id_condition',
        'fields' => array(
            'id_ps_condition' =>        array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'type' =>                    array('type' => self::TYPE_STRING, 'size' => 32),
            'request' =>                array('type' => self::TYPE_STRING),
            'operator' =>                array('type' => self::TYPE_NOTHING),
            'value' =>                    array('type' => self::TYPE_STRING),
            'result' =>                    array('type' => self::TYPE_STRING),
            'calculation_type' =>        array('type' => self::TYPE_STRING),
            'calculation_detail' =>        array('type' => self::TYPE_STRING),
            'validated' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );
    
    public static function getIdByIdPs($id_ps_condition)
    {
        $query = new DbQuery();
        $query->select('id_condition');
        $query->from('condition', 'c');
        $query->where('`id_ps_condition` = '.(int)$id_ps_condition);
        
        return (int)Db::getInstance()->getValue($query);
    }
    
    public static function getIdsByHookCalculation($hook_name)
    {
        $ids = array();
        $in = array();
        
        $sub_query = new DbQuery();
        $sub_query->select('id_badge');
        $sub_query->from('badge', 'b');
        $sub_query->where('b.`validated` = 0');

        $sub_results = Db::getInstance()->executeS($sub_query);

        foreach ($sub_results as $sub_result) {
            $in[] = $sub_result['id_badge'];
        }

        $query = new DbQuery();
        $query->select('c.`id_condition`');
        $query->from('condition', 'c');
        $query->join('LEFT JOIN `'._DB_PREFIX_.'condition_badge` cb ON cb.`id_condition` = c.`id_condition`');
        $query->where('c.`calculation_type` = \'hook\'');
        $query->where('c.`calculation_detail` = \''.pSQL($hook_name).'\'');
        $query->where('c.`validated` = 0');
        if (count($in)) {
            $query->where('cb.`id_badge` IN ('.implode(',', $in).')');
        }
        $query->groupBy('c.`id_condition`');

        $result = Db::getInstance()->executeS($query);

        foreach ($result as $r) {
            $ids[] = $r['id_condition'];
        }
        
        $sub_query = new DbQuery();
        $sub_query->select('id_advice');
        $sub_query->from('advice', 'a');

        $sub_results = Db::getInstance()->executeS($sub_query);

        $in = array();

        foreach ($sub_results as $sub_result) {
            $in[] = $sub_result['id_advice'];
        }
        
        $query = new DbQuery();
        $query->select('c.`id_condition`');
        $query->from('condition', 'c');
        $query->join('LEFT JOIN `'._DB_PREFIX_.'condition_advice` ca ON ca.`id_condition` = c.`id_condition`');
        $query->where('c.`calculation_type` = \'hook\'');
        $query->where('c.`calculation_detail` = \''.pSQL($hook_name).'\'');
        $query->where('c.`validated` = 0');
        if (count($in)) {
            $query->where('ca.`id_advice` IN ('.implode(',', $in).')');
        }
        $query->groupBy('c.`id_condition`');
        $result = Db::getInstance()->executeS($query);
        
        foreach ($result as $r) {
            $ids[] = $r['id_condition'];
        }
        
        return array_unique($ids);
    }
    
    public static function getIdsDailyCalculation()
    {
        $ids = array();
        $in = array();

        //badges conditions validation
        $sub_query = new DbQuery();
        $sub_query->select('id_badge');
        $sub_query->from('badge', 'b');

        $sub_results = Db::getInstance()->executeS($sub_query);

        foreach ($sub_results as $sub_result) {
            $in[] = $sub_result['id_badge'];
        }

        $query = new DbQuery();
        $query->select('c.`id_condition`');
        $query->from('condition', 'c');
        $query->join('LEFT JOIN `'._DB_PREFIX_.'condition_badge` cb ON cb.`id_condition` = c.`id_condition`');
        $query->where('c.`calculation_type` = \'time\'');
        $query->where('DATEDIFF(NOW(), `date_upd`) >= `calculation_detail`');
        $query->where('c.`validated` = 0');
        if (count($in)) {
            $query->where('cb.`id_badge` IN ('.implode(',', $in).')');
        }
        $query->groupBy('c.`id_condition`');
        
        $result = Db::getInstance()->executeS($query);

        foreach ($result as $r) {
            $ids[] = $r['id_condition'];
        }
        
        //advice conditions validation
        $sub_query = new DbQuery();
        $sub_query->select('id_advice');
        $sub_query->from('advice', 'a');

        $sub_results = Db::getInstance()->executeS($sub_query);

        $in = array();

        foreach ($sub_results as $sub_result) {
            $in[] = $sub_result['id_advice'];
        }
        
        $query = new DbQuery();
        $query->select('c.`id_condition`');
        $query->from('condition', 'c');
        $query->join('LEFT JOIN `'._DB_PREFIX_.'condition_advice` ca ON ca.`id_condition` = c.`id_condition`');
        $query->where('c.`calculation_type` = \'time\'');
        $query->where('DATEDIFF(NOW(), `date_upd`) >= `calculation_detail`');
        $query->where('c.`validated` = 0');
        if (count($in)) {
            $query->where('ca.`id_advice` IN ('.implode(',', $in).')');
        }
        $query->groupBy('c.`id_condition`');
        
        $result = Db::getInstance()->executeS($query);
        foreach ($result as $r) {
            $ids[] = $r['id_condition'];
        }
        

        return array_unique($ids);
    }
    
    public static function getIdsByBadgeGroupPosition($badge_group_position)
    {
        $ids = array();
        
        $sub_query = new DbQuery();
        $sub_query->select('id_badge');
        $sub_query->from('badge', 'b');
        $sub_query->where('b.`group_position` = '.(int)$badge_group_position);
        $sub_query->where('b.`validated` = 0');
        $sub_query->groupBy('b.`id_group`');
        
        $query = new DbQuery();
        $query->select('c.`id_condition`');
        $query->from('condition', 'c');
        $query->join('LEFT JOIN `'._DB_PREFIX_.'condition_badge` cb ON cb.`id_condition` = c.`id_condition`');
        $query->where('c.`validated` = 0');
        $query->where('cb.`id_badge` IN ('.$sub_query.')');
        $query->groupBy('c.`id_condition`');
        
        $result = Db::getInstance()->executeS($query);
        foreach ($result as $r) {
            $ids[] = $r['id_condition'];
        }

        return $ids;
    }
    
    public static function getIdsByBadgeGroup($badge_group)
    {
        $ids = array();
        
        $sub_query = new DbQuery();
        $sub_query->select('id_badge');
        $sub_query->from('badge', 'b');
        $sub_query->where('b.`id_group` = '.(int)$badge_group);
        $sub_query->where('b.`validated` = 0');
        $sub_query->groupBy('b.`id_group`');
        
        $query = new DbQuery();
        $query->select('c.`id_condition`');
        $query->from('condition', 'c');
        $query->join('LEFT JOIN `'._DB_PREFIX_.'condition_badge` cb ON cb.`id_condition` = c.`id_condition`');
        $query->where('c.`validated` = 0');
        $query->where('cb.`id_badge` IN ('.$sub_query.')');
        $query->groupBy('c.`id_condition`');
        
        $result = Db::getInstance()->executeS($query);
        foreach ($result as $r) {
            $ids[] = $r['id_condition'];
        }

        return $ids;
    }
        
    public function processCalculation()
    {
        switch ($this->type) {
            case 'configuration':
                $this->processConfiguration();
            break;
            case 'install':
                $this->processInstall();
            break;
            case 'sql':
                $this->processSql();
            break;
        }
    }
    
    protected function processSql()
    {
        if (preg_match('/'.implode('|', self::$unauthorized).'/', $this->request)) {
            return false;
        }
        
        try {
            $this->result = (int)Db::getInstance()->getValue(GamificationTools::parseMetaData($this->request));
        } catch (Exception $e) {
            return false;
        }
        
        if ($this->makeCalculation($this->operator, $this->result, $this->value)) {
            $this->validated = 1;
        }
        $this->save();
    }
    
    protected function processConfiguration()
    {
        $this->result = Configuration::get($this->request);
        if ($this->result = $this->makeCalculation($this->operator, $this->result, $this->value)) {
            $this->validated = 1;
        }
        $this->save();
    }
    
    protected function processInstall()
    {
        $install = strtotime(_PS_CREATION_DATE_.' 00:00:00');
        $value = strtotime('+ '.(int)$this->value.' day', $install);
        $this->result = $this->makeCalculation($this->operator, (time() - $install), $value - $install);
        if ($this->result) {
            $this->validated = 1;
        }

        $this->save();
    }
    
    protected function makeCalculation($operator, $arg1, $arg2)
    {
        $result = false;
        switch ($operator) {
            case '>':
                $result = $arg1 > $arg2;
                break;
            case '>=':
                $result = $arg1 >= $arg2;
                break;
            case '<':
                $result = $arg1 < $arg2;
                break;
            case '<=':
                $result = $arg1 <= $arg2;
                break;
            case '==':
                $result = $arg1 == $arg2;
                break;
            case '!=':
                $result = $arg1 != $arg2;
                break;
            break;
        }
        return (bool)$result;
    }
}
