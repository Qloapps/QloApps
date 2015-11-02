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

abstract class ObjectModel extends ObjectModelCore
{
    public static $debug_list = array();

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);

        $classname = get_class($this);
        if (!isset(self::$debug_list[$classname])) {
            self::$debug_list[$classname] = array();
        }

        $class_list = array('ObjectModel', 'ObjectModelCore', $classname, $classname.'Core');
        $backtrace = debug_backtrace();
        foreach ($backtrace as $trace_id => $row) {
            if (!isset($backtrace[$trace_id]['class']) || !in_array($backtrace[$trace_id]['class'], $class_list)) {
                break;
            }
        }
        $trace_id--;

        self::$debug_list[$classname][] = array(
            'file' => @$backtrace[$trace_id]['file'],
            'line' => @$backtrace[$trace_id]['line'],
        );
    }
}
