<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class GraphNvD3 extends ModuleGraphEngine
{
    private $_width;
    private $_height;
    private $_values;
    private $_legend;
    private $_titles;
    private $_formats;

    public function __construct($type = null)
    {
        if ($type !== null) {
            return parent::__construct($type);
        }

        $this->name = 'graphnvd3';
        $this->tab = 'analytics_stats';
        $this->version = '1.5.3';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        Module::__construct();

        $this->displayName = $this->l('NVD3 Charts');
        $this->description = '';
    }

    public function install()
    {
        return (parent::install() && $this->registerHook('GraphEngine') && $this->registerHook('actionAdminControllerSetMedia'));
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        $admin_webpath = str_ireplace(_PS_ROOT_DIR_, '', _PS_ADMIN_DIR_);
        $admin_webpath = preg_replace('/^'.preg_quote(DIRECTORY_SEPARATOR, '/').'/', '', $admin_webpath);

        $this->context->controller->addJS(array(
            _PS_JS_DIR_.'vendor/d3.v3.min.js',
            __PS_BASE_URI__.$admin_webpath.'/themes/'.$this->context->employee->bo_theme.'/js/vendor/nv.d3.min.js',
        ));
        $this->context->controller->addCSS(__PS_BASE_URI__.$admin_webpath.'/themes/'.$this->context->employee->bo_theme.'/css/vendor/nv.d3.css');
    }

    public static function hookGraphEngine($params, $drawer)
    {
        static $divid = 1;

        if (strpos($params['width'], '%') !== false) {
            $params['width'] = (int)preg_replace('/\s*%\s*/', '', $params['width']).'%';
        } else {
            $params['width'] = (int)$params['width'].'px';
        }

        $nvd3_func = array(
            'line' => (isset($params['has_label_y']) && $params['has_label_y']) ?
                'nv.models.lineChart().margin({ left: 80 })' :
                'nv.models.lineChart()',
            'pie' => '
                nv.models.pieChart()
                    .x(function(d) { return d.label; })
                    .y(function(d) { return d.value; })
                    .showLabels(true)
                    .showLegend(false)'
        );

        return '
		<div id="nvd3_chart_'.$divid.'" class="chart with-transitions">
			<svg style="width:'.$params['width'].';height:'.(int)$params['height'].'px"></svg>
		</div>
		<script>
			$.ajax({
			url: "'.addslashes($drawer).'",
			dataType: "json",
			type: "GET",
			cache: false,
			headers: {"cache-control": "no-cache"},
			success: function(jsonData){
				nv.addGraph(function(){
					var chart = '.$nvd3_func[$params['type']].';

					if (jsonData.axisLabels.xAxis != null)
						chart.xAxis.axisLabel(jsonData.axisLabels.xAxis);
					if (jsonData.axisLabels.yAxis != null)
						chart.yAxis.axisLabel(jsonData.axisLabels.yAxis);

                    if (jsonData.axisFormat.xAxis)
                        chart.xAxis.tickFormat(d3.format(jsonData.axisFormat.xAxis));
                    if (jsonData.axisFormat.yAxis)
                        chart.yAxis.tickFormat(d3.format(jsonData.axisFormat.yAxis));
					d3.select("#nvd3_chart_'.($divid++).' svg")
						.datum(jsonData.data)
						.transition().duration(500)
						.call(chart);

					nv.utils.windowResize(chart.update);

					return chart;
				});
			}
		});
		</script>';
    }

    public function createValues($values)
    {
        $this->_values = $values;
    }

    public function setSize($width, $height)
    {
        $this->_width = $width;
        $this->_height = $height;
    }

    public function setLegend($legend)
    {
        $this->_legend = $legend;
    }

    public function setTitles($titles)
    {
        $this->_titles = $titles;
    }

    public function setFormat($_formats)
    {
        $this->_formats = $_formats;
    }

    public function draw()
    {
        $array = array(
            'axisLabels' => array('xAxis' => $this->_titles['x'], 'yAxis' => $this->_titles['y']),
            'axisFormat' => array('xAxis' => $this->_formats['x'], 'yAxis' => $this->_formats['y']),
            'data' => array()
        );

        if (!isset($this->_values[0]) || !is_array($this->_values[0])) {
            $nvd3_values = array();
            if (Tools::getValue('type') == 'pie') {
                foreach ($this->_values as $x => $y) {
                    $nvd3_values[] = array('label' => $this->_legend[$x], 'value' => $y);
                }
                $array['data'] = $nvd3_values;
            } else {
                foreach ($this->_values as $x => $y) {
                    $nvd3_values[] = array('x' => $x, 'y' => $y);
                }
                $array['data'][] = array('values' => $nvd3_values, 'key' => $this->_titles['main']);
            }
        } else {
            foreach ($this->_values as $layer => $gross_values) {
                $nvd3_values = array();
                foreach ($gross_values as $x => $y) {
                    $nvd3_values[] = array('x' => $x, 'y' => $y);
                }
                $array['data'][] = array('values' => $nvd3_values, 'key' => $this->_titles['main'][$layer]);
            }
        }
        die(preg_replace('/"([0-9]+)"/', '$1', json_encode($array)));
    }
}
