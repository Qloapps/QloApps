<?php
/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class HelperKpiRowCore extends Helper
{
    public $base_folder = 'helpers/kpi/';
    public $base_tpl = 'row.tpl';

    public $kpis = array();
    public $refresh = true;

    public function generate()
    {
        $this->tpl = $this->createTemplate($this->base_tpl);

        // set visiblity for each KPI
        $countVisible = 0;
        $cookieKeyPrefix = 'kpi_visibility_'.$this->context->controller->className.'_';
        foreach ($this->kpis as &$kpi) {
            $cookieKey = $cookieKeyPrefix.$kpi->id;
            if (isset($this->context->cookie->$cookieKey)) {
                $kpi->visible = (bool) $this->context->cookie->$cookieKey;
            } else {
                $kpi->visible = true;
            }

            $countVisible = $kpi->visible ? ++$countVisible : $countVisible;
        }

        $cookieKeyView = 'kpi_wrapping_'.$this->context->controller->className;

        $this->tpl->assign('kpis', $this->kpis);
        $this->tpl->assign('refresh', $this->refresh);
        $this->tpl->assign('no_wrapping', (int) $this->context->cookie->$cookieKeyView);
        $this->tpl->assign('count_visible', $countVisible);

        return $this->tpl->fetch();
    }
}
