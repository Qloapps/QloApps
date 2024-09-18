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

class SmartyDev extends Smarty
{
    public function __construct()
    {
        parent::__construct();
        $this->template_class = 'Smarty_Dev_Template';
    }

    /**
     * {@inheritDoc}
     */
    public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false)
    {
        return "\n<!-- begin $template -->\n"
                .parent::fetch($template, $cache_id, $compile_id, $parent, $display, $merge_tpl_vars, $no_output_filter)
                ."\n<!-- end $template -->\n";
    }
}

class Smarty_Dev_Template extends Smarty_Internal_Template
{
    /** @var SmartyCustom|null */
    public $smarty = null;

    public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false)
    {
        if (!is_null($template)) {
            $tpl = $template->template_resource;
        } else {
            $tpl = $this->template_resource;
        }

        return "\n<!-- begin $tpl -->\n"
                .parent::fetch($template, $cache_id, $compile_id, $parent, $display, $merge_tpl_vars, $no_output_filter)
                ."\n<!-- end $tpl -->\n";
    }
}
