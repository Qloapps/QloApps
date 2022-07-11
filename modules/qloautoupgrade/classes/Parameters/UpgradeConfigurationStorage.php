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

namespace PrestaShop\Module\AutoUpgrade\Parameters;

use PrestaShop\Module\AutoUpgrade\Upgrader;

class UpgradeConfigurationStorage extends FileConfigurationStorage
{
    /**
     * UpgradeConfiguration loader.
     *
     * @param string $configFileName
     *
     * @return \PrestaShop\Module\AutoUpgrade\Parameters\UpgradeConfiguration
     */
    public function load($configFileName = '')
    {
        $data = array_merge(
            $this->getDefaultData(),
            parent::load($configFileName)
        );

        return new UpgradeConfiguration($data);
    }

    /**
     * @param \PrestaShop\Module\AutoUpgrade\Parameters\UpgradeConfiguration $config
     * @param string $configFileName Destination path of the config file
     *
     * @return bool
     */
    public function save($config, $configFileName)
    {
        if (!$config instanceof UpgradeConfiguration) {
            throw new \InvalidArgumentException('Config is not a instance of UpgradeConfiguration');
        }

        return parent::save($config->toArray(), $configFileName);
    }

    public function getDefaultData()
    {
        return array(
            'PS_AUTOUP_PERFORMANCE' => 1,
            'PS_AUTOUP_CUSTOM_MOD_DESACT' => 1,
            'PS_AUTOUP_UPDATE_DEFAULT_THEME' => 1,
            'PS_AUTOUP_CHANGE_DEFAULT_THEME' => 0,
            'PS_AUTOUP_KEEP_MAILS' => 0,
            'PS_AUTOUP_BACKUP' => 1,
            'PS_AUTOUP_KEEP_IMAGES' => 1,
            'channel' => Upgrader::DEFAULT_CHANNEL,
            'archive.filename' => Upgrader::DEFAULT_FILENAME,
        );
    }
}
