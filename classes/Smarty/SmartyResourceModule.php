<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
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
