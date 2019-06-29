<?php
/*
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * Controllers don't need to be loaded with includeController anymore since they use Autoload
 *
 * @deprecated since 1.5.0
 */
class ControllerFactoryCore
{
    /**
     * @deprecated since 1.5.0
     */
    public static function includeController($className)
    {
        Tools::displayAsDeprecated();

        if (!class_exists($className, false)) {
            require_once(_PS_CORE_DIR_.'/controllers/'.$className.'.php');
            if (file_exists(_PS_ROOT_DIR_.'/override/controllers/'.$className.'.php')) {
                require_once(_PS_ROOT_DIR_.'/override/controllers/'.$className.'.php');
            } else {
                $coreClass = new ReflectionClass($className.'Core');
                if ($coreClass->isAbstract()) {
                    eval('abstract class '.$className.' extends '.$className.'Core {}');
                } else {
                    eval('class '.$className.' extends '.$className.'Core {}');
                }
            }
        }
    }

    /**
     * @deprecated since 1.5.0
     */
    public static function getController($className, $auth = false, $ssl = false)
    {
        ControllerFactory::includeController($className);
        return new $className($auth, $ssl);
    }
}
