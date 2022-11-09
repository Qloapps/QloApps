<?php

/**
 * 2007-2016 PrestaShop.
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2016 PrestaShop SA
 *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
use PrestaShop\Module\AutoUpgrade\Tools14;
use PrestaShop\Module\AutoUpgrade\UpgradeTools\TaskRepository;

/**
 * This file is the entrypoint for all ajax requests during a upgrade, rollback or configuration.
 * In order to get the admin context, this file is copied to the admin/autoupgrade folder of your shop when the module configuration is reached.
 *
 * Calling it from the module/autoupgrade folder will have unwanted consequences on the upgrade and your shop.
 */
require_once realpath(dirname(__FILE__) . '/../../modules/qloautoupgrade') . '/ajax-upgradetabconfig.php';
$container = autoupgrade_init_container(dirname(__FILE__));

(new \PrestaShop\Module\AutoUpgrade\ErrorHandler($container->getLogger()))->enable();

if (!$container->getCookie()->check($_COOKIE)) {
    // If this is an XSS attempt, then we should only display a simple, secure page
    if (ob_get_level() && ob_get_length() > 0) {
        ob_clean();
    }
    echo '{wrong token}';
    http_response_code(401);
    die(1);
}

$controller = TaskRepository::get(Tools14::getValue('action'), $container);
$controller->init();
$controller->run();
echo $controller->getJsonResponse();
