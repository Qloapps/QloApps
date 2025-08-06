<?php

/**
 * 2007-2017 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\Module\AutoUpgrade\Twig\Block;

use PrestaShop\Module\AutoUpgrade\UpgradeSelfCheck;
use PrestaShop\Module\AutoUpgrade\Tools14;
use Context;
use Twig_Environment;

/**
 * Builds the upgrade checklist block.
 */
class UpgradeChecklist
{
    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $prodRootPath;

    /**
     * @var string
     */
    private $adminPath;

    /**
     * @var string
     */
    private $autoupgradePath;

    /**
     * @var UpgradeSelfCheck
     */
    private $selfCheck;

    /**
     * @var string
     */
    private $currentIndex;

    /**
     * @var string
     */
    private $token;

    /**
     * UpgradeChecklistBlock constructor.
     *
     * @param Twig_Environment $twig
     * @param UpgradeSelfCheck $upgradeSelfCheck
     * @param string $prodRootPath
     * @param string $adminPath
     * @param string $autoupgradePath
     * @param string $currentIndex
     * @param string $token
     */
    public function __construct(
        Twig_Environment $twig,
        UpgradeSelfCheck $upgradeSelfCheck,
        $prodRootPath,
        $adminPath,
        $autoupgradePath,
        $currentIndex,
        $token
    ) {
        $this->twig = $twig;
        $this->selfCheck = $upgradeSelfCheck;
        $this->prodRootPath = $prodRootPath;
        $this->adminPath = $adminPath;
        $this->autoupgradePath = $autoupgradePath;
        $this->currentIndex = $currentIndex;
        $this->token = $token;
    }

    /**
     * Returns the block's HTML.
     *
     * @return string
     */
    public function render()
    {
        $data = array(
            'showErrorMessage' => !$this->selfCheck->isOkForUpgrade(),
            'moduleVersion' => $this->selfCheck->getModuleVersion(),
            'moduleIsUpToDate' => $this->selfCheck->isModuleVersionLatest(),
            'versionGreaterThan1_5_3' => version_compare(_PS_VERSION_, '1.5.3.0', '>'),
            'adminToken' => Tools14::getAdminTokenLite('AdminModules'),
            'informationsLink' => Context::getContext()->link->getAdminLink('AdminInformation'),
            'rootDirectoryIsWritable' => $this->selfCheck->isRootDirectoryWritable(),
            'rootDirectoryWritableReport' => $this->selfCheck->getRootWritableReport(),
            'adminDirectoryIsWritable' => $this->selfCheck->isAdminAutoUpgradeDirectoryWritable(),
            'adminDirectoryWritableReport' => $this->selfCheck->getAdminAutoUpgradeDirectoryWritableReport(),
            'safeModeIsDisabled' => $this->selfCheck->isSafeModeDisabled(),
            'allowUrlFopenOrCurlIsEnabled' => $this->selfCheck->isFOpenOrCurlEnabled(),
            'zipIsEnabled' => $this->selfCheck->isZipEnabled(),
            'storeIsInMaintenance' => $this->selfCheck->isShopDeactivated(),
            'currentIndex' => $this->currentIndex,
            'token' => $this->token,
            'cachingIsDisabled' => $this->selfCheck->isCacheDisabled(),
            'maxExecutionTime' => $this->selfCheck->getMaxExecutionTime(),
            'phpUpgradeRequired' => $this->selfCheck->isPhpUpgradeRequired(),
            'isPrestaShopReady' => $this->selfCheck->isPrestaShopReady(),
        );

        return $this->twig->render('@ModuleAutoUpgrade/block/checklist.twig', $data);
    }
}
