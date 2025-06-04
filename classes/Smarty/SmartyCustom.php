<?php
/**
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class SmartyCustomCore extends Smarty
{
    public $template_class = null;

    public function __construct()
    {
        parent::__construct();
        $this->template_class = 'Smarty_Custom_Template';
    }

    /**
     * {@inheritDoc}
     */
    public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false)
    {
        if (($overrideTemplate = Hook::exec('displayOverrideTemplate', array('default_template' => $template, 'controller' => Context::getContext()->controller)))
            && file_exists($overrideTemplate)
        ) {
            $template = $overrideTemplate;
        }

        $response = parent::fetch($template, $cache_id, $compile_id, $parent, $display, $merge_tpl_vars, $no_output_filter);
        if (isset($this->display_comments) && $this->display_comments) {
            $response =  "\n<!-- begin $template -->\n".$response."\n<!-- end $template -->\n";
        }

        return $response;
    }
}

class Smarty_Custom_Template extends Smarty_Internal_Template
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

        if (($templatePath = Hook::exec('displayOverrideTemplate', array('default_template' => $tpl, 'controller' => Context::getContext()->controller)))
            && file_exists($templatePath)
        ) {
            $template = Context::getContext()->smarty->createTemplate($templatePath);
            $tpl = $template->template_resource;
        }

        $response = parent::fetch($template, $cache_id, $compile_id, $parent, $display, $merge_tpl_vars, $no_output_filter);
        if (isset($this->display_comments) && $this->display_comments) {
            $response =  "\n<!-- begin $tpl -->\n".$response."\n<!-- end $tpl -->\n";
        }

        return $response;
    }

}
