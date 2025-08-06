<?php

/*
 * 2007-2018 PrestaShop
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
 *  @copyright  2007-2018 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\Module\AutoUpgrade;

use PrestaShop\Module\AutoUpgrade\Log\LoggerInterface;

class Workspace
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var UpgradeTools\Translator
     */
    private $translator;

    /**
     * @var array List of paths used by autoupgrade
     */
    private $paths;

    public function __construct(LoggerInterface $logger, $translator, array $paths)
    {
        $this->logger = $logger;
        $this->translator = $translator;
        $this->paths = $paths;
    }

    public function createFolders()
    {
        foreach ($this->paths as $path) {
            if (!file_exists($path) && !mkdir($path)) {
                $this->logger->error($this->translator->trans('Unable to create directory %s', array($path), 'Modules.Autoupgrade.Admin'));
            }
            if (!is_writable($path)) {
                $this->logger->error($this->translator->trans('Unable to write in the directory "%s"', array($path), 'Modules.Autoupgrade.Admin'));
            }
        }
    }
}
