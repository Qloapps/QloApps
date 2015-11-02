<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class RiskCore extends ObjectModel
{
    public $id_risk;
    public $name;
    public $color;
    public $percent;

    public static $definition = array(
        'table' => 'risk',
        'primary' => 'id_risk',
        'multilang' => true,
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true, 'size' => 20),
            'color' =>  array('type' => self::TYPE_STRING, 'validate' => 'isColor', 'size' => 32),
            'percent' => array('type' => self::TYPE_INT, 'validate' => 'isPercentage')
        ),
    );

    public function getFields()
    {
        $this->validateFields();
        $fields['id_risk'] = (int)$this->id_risk;
        $fields['color'] = pSQL($this->color);
        $fields['percent'] = (int)$this->percent;
        return $fields;
    }

    /**
     * Check then return multilingual fields for database interaction
     *
     * @return array Multilingual fields
     */
    public function getTranslationsFieldsChild()
    {
        $this->validateFieldsLang();
        return $this->getTranslationsFields(array('name'));
    }

    public static function getRisks($id_lang = null)
    {
        if (is_null($id_lang)) {
            $id_lang = Context::getContext()->language->id;
        }

        $risks = new PrestaShopCollection('Risk', $id_lang);
        return $risks;
    }
}
