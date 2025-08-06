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

use PrestaShop\Module\AutoUpgrade\ChannelInfo;
use PrestaShop\Module\AutoUpgrade\Parameters\UpgradeConfiguration;
use Twig_Environment;

class ChannelInfoBlock
{
    /**
     * @var UpgradeConfiguration
     */
    private $config;

    /**
     * @var ChannelInfo
     */
    private $channelInfo;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * ChannelInfoBlock constructor.
     *
     * @param UpgradeConfiguration $config
     * @param ChannelInfo $channelInfo
     * @param Twig_Environment $twig
     */
    public function __construct(UpgradeConfiguration $config, ChannelInfo $channelInfo, Twig_Environment $twig)
    {
        $this->config = $config;
        $this->channelInfo = $channelInfo;
        $this->twig = $twig;
    }

    /**
     * @return string HTML
     */
    public function render()
    {
        $channel = $this->channelInfo->getChannel();
        $upgradeInfo = $this->channelInfo->getInfo();

        if ($channel == 'private') {
            $upgradeInfo['link'] = $this->config->get('private_release_link');
            $upgradeInfo['md5'] = $this->config->get('private_release_md5');
        }

        return $this->twig->render(
            '@ModuleAutoUpgrade/block/channelInfo.twig',
            array(
                'upgradeInfo' => $upgradeInfo,
            )
        );
    }
}
