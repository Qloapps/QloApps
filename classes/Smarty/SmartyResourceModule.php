<?php
/**
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Override module templates easily
 *
 * @since 1.7.0.0
 */
class SmartyResourceModuleCore extends Smarty_Resource_Custom
{
    private $shop;

    public function __construct(array $paths)
    {
        $this->paths = $paths;
    }

    /**
     * Fetch a template
     *
     * @param string $name template name
     * @param string $source template source
     * @param integer $mtime template modification timestamp (epoch)
     * @return void
     */
    protected function fetch($name, &$source, &$mtime)
    {
        foreach ($this->paths as $path) {
            if (Tools::file_exists_cache($file = $path.$name)) {
                if (_PS_MODE_DEV_) {
                    $source = implode('', [
                        '<!-- begin '.$file.' -->',
                        file_get_contents($file),
                        '<!-- end '.$file.' -->'
                    ]);
                } else {
                    $source = file_get_contents($file);
                }
                $mtime = filemtime($file);

                return;
            }
        }
    }
}
