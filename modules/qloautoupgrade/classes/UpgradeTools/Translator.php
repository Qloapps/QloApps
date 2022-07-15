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

namespace PrestaShop\Module\AutoUpgrade\UpgradeTools;

class Translator
{
    private $caller;

    public function __construct($caller)
    {
        $this->caller = $caller;
    }

    /**
     * Translate a string to the current language.
     *
     * This methods has the same signature as the 1.7 trans method, but only relies
     *  on the module translation files.
     *
     * @param string $id Original text
     * @param array $parameters Parameters to apply
     * @param string $domain Unused
     * @param string $locale Unused
     *
     * @return string Translated string with parameters applied
     */
    public function trans($id, array $parameters = array(), $domain = 'Modules.Autoupgrade.Admin', $locale = null)
    {
        // If PrestaShop core is not instancied properly, do not try to translate
        if (!method_exists('\Context', 'getContext') || null === \Context::getContext()->language) {
            return $this->applyParameters($id, $parameters);
        }

        if (method_exists('\Translate', 'getModuleTranslation')) {
            $translated = \Translate::getModuleTranslation('autoupgrade', $id, $this->caller, null);
            if (!count($parameters)) {
                return $translated;
            }
        } else {
            $translated = $id;
        }

        return $this->applyParameters($translated, $parameters);
    }

    /**
     * @param string $id
     * @param array $parameters
     *
     * @return string Translated string with parameters applied
     *
     * @internal Public for tests
     */
    public function applyParameters($id, array $parameters = array())
    {
        // Replace placeholders for non numeric keys
        foreach ($parameters as $placeholder => $value) {
            if (is_int($placeholder)) {
                continue;
            }
            $id = str_replace($placeholder, $value, $id);
            unset($parameters[$placeholder]);
        }

        return call_user_func_array('sprintf', array_merge(array($id), $parameters));
    }
}
