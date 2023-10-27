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

abstract class ModuleGraphCore extends Module
{
    protected $_employee;

    /** @var array of integers graph data */
    protected $_values = array();

    /** @var array of strings graph legends (X axis) */
    protected $_legend = array();

    /**@var array string graph titles */
    protected $_titles = array('main' => null, 'x' => null, 'y' => null);

    /** @var ModuleGraphEngine graph engine */
    protected $_render;

    abstract protected function getData($layers);

    public function setEmployee($id_employee)
    {
        $this->_employee = new Employee($id_employee);
    }

    public function setLang($id_lang)
    {
        $this->_id_lang = $id_lang;
    }

    protected function setDateGraph($layers, $legend = false)
    {
        // Get dates in a manageable format
        $from_array = getdate(strtotime($this->_employee->stats_date_from));
        $to_array = getdate(strtotime($this->_employee->stats_date_to));

        // If the granularity is inferior to 1 day
        if ($this->_employee->stats_date_from == $this->_employee->stats_date_to) {
            if ($legend) {
                for ($i = 0; $i < 24; $i++) {
                    if ($layers == 1) {
                        $this->_values[$i] = 0;
                    } else {
                        for ($j = 0; $j < $layers; $j++) {
                            $this->_values[$j][$i] = 0;
                        }
                    }
                    $this->_legend[$i] = ($i % 2) ? '' : sprintf('%02dh', $i);
                }
            }
            if (is_callable(array($this, 'setDayValues'))) {
                $this->setDayValues($layers);
            }
        }
        // If the granularity is inferior to 1 month
        // @TODO : change to manage 28 to 31 days
        elseif (strtotime($this->_employee->stats_date_to) - strtotime($this->_employee->stats_date_from) <= 2678400) {
            if ($legend) {
                $days = array();
                if ($from_array['mon'] == $to_array['mon']) {
                    for ($i = $from_array['mday']; $i <= $to_array['mday']; ++$i) {
                        $days[] = $i;
                    }
                } else {
                    $imax = date('t', mktime(0, 0, 0, $from_array['mon'], 1, $from_array['year']));
                    for ($i = $from_array['mday']; $i <= $imax; ++$i) {
                        $days[] = $i;
                    }
                    for ($i = 1; $i <= $to_array['mday']; ++$i) {
                        $days[] = $i;
                    }
                }
                foreach ($days as $i) {
                    if ($layers == 1) {
                        $this->_values[$i] = 0;
                    } else {
                        for ($j = 0; $j < $layers; $j++) {
                            $this->_values[$j][$i] = 0;
                        }
                    }
                    $this->_legend[$i] = sprintf('%02d', $i);
                }
            }
            if (is_callable(array($this, 'setMonthValues'))) {
                $this->setMonthValues($layers);
            }
        }
        // If the granularity is less than 1 year
        elseif (strtotime('-1 year', strtotime($this->_employee->stats_date_to)) < strtotime($this->_employee->stats_date_from)) {
            if ($legend) {
                $months = array();
                if ($from_array['year'] == $to_array['year']) {
                    for ($i = $from_array['mon']; $i <= $to_array['mon']; ++$i) {
                        $months[] = $i;
                    }
                } else {
                    for ($i = $from_array['mon']; $i <= 12; ++$i) {
                        $months[] = $i;
                    }
                    for ($i = 1; $i <= $to_array['mon']; ++$i) {
                        $months[] = $i;
                    }
                }
                foreach ($months as $i) {
                    if ($layers == 1) {
                        $this->_values[$i] = 0;
                    } else {
                        for ($j = 0; $j < $layers; $j++) {
                            $this->_values[$j][$i] = 0;
                        }
                    }
                    $this->_legend[$i] = sprintf('%02d', $i);
                }
            }
            if (is_callable(array($this, 'setYearValues'))) {
                $this->setYearValues($layers);
            }
        }
        // If the granularity is greater than 1 year
        else {
            if ($legend) {
                $years = array();
                for ($i = $from_array['year']; $i <= $to_array['year']; ++$i) {
                    $years[] = $i;
                }
                foreach ($years as $i) {
                    if ($layers == 1) {
                        $this->_values[$i] = 0;
                    } else {
                        for ($j = 0; $j < $layers; $j++) {
                            $this->_values[$j][$i] = 0;
                        }
                    }
                    $this->_legend[$i] = sprintf('%04d', $i);
                }
            }
            if (is_callable(array($this, 'setAllTimeValues'))) {
                $this->setAllTimeValues($layers);
            }
        }
    }

    protected function csvExport($datas)
    {
        $context = Context::getContext();

        $this->setEmployee($context->employee->id);
        $this->setLang($context->language->id);

        $layers = isset($datas['layers']) ?  $datas['layers'] : 1;
        if (isset($datas['option'])) {
            $this->setOption($datas['option'], $layers);
        }
        $this->getData($layers);

        // @todo use native CSV PHP functions ?
        // Generate first line (column titles)
        if (is_array($this->_titles['main'])) {
            for ($i = 0, $total_main = count($this->_titles['main']); $i <= $total_main; $i++) {
                if ($i > 0) {
                    $this->_csv .= ';';
                }
                if (isset($this->_titles['main'][$i])) {
                    $this->_csv .= $this->_titles['main'][$i];
                }
            }
        } else { // If there is only one column title, there is in fast two column (the first without title)
            $this->_csv .= ';'.$this->_titles['main'];
        }
        $this->_csv .= "\n";
        if (count($this->_legend)) {
            $total = 0;
            if ($datas['type'] == 'pie') {
                // foreach ($this->_legend as $key => $legend) {
                    for ($i = 0, $total_main = (is_array($this->_titles['main']) ? count($this->_values) : 1); $i < $total_main; ++$i) {
                        $total += (is_array($this->_values[$i])  ? $this->_values[$i][$key] : $this->_values[$i]);
                    }
                // }
            }
            foreach ($this->_legend as $key => $legend) {
                $this->_csv .= $legend.';';
                for ($i = 0, $total_main = (is_array($this->_titles['main']) ? (count($this->_titles['main']) - 1) : 1); $i < $total_main; ++$i) {
                    if (!isset($this->_values[$i]) || !is_array($this->_values[$i])) {
                        if (isset($this->_values[$key])) {
                            // We don't want strings to be divided. Example: product name
                            if (is_numeric($this->_values[$key])) {
                                $this->_csv .= $this->_values[$key] / (($datas['type'] == 'pie') ? $total : 1);
                            } else {
                                $this->_csv .= $this->_values[$key];
                            }
                        } else {
                            $this->_csv .= '0';
                        }
                    } else {
                        // We don't want strings to be divided. Example: product name
                        if (is_numeric($this->_values[$i][$key])) {
                            $this->_csv .= $this->_values[$i][$key] / (($datas['type'] == 'pie') ? $total : 1);
                        } else {
                            $this->_csv .= $this->_values[$i][$key];
                        }
                    }
                    $this->_csv .= ';';
                }
                $this->_csv .= "\n";
            }
        }
        $this->_displayCsv();
    }

    protected function _displayCsv()
    {
        if (ob_get_level() && ob_get_length() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$this->displayName.' - '.time().'.csv"');
        echo $this->_csv;
        exit;
    }

    public function create($render, $type, $width, $height, $layers)
    {
        if (!Validate::isModuleName($render)) {
            die(Tools::displayError());
        }
        if (!Tools::file_exists_cache($file = _PS_ROOT_DIR_.'/modules/'.$render.'/'.$render.'.php')) {
            die(Tools::displayError());
        }
        require_once($file);
        $this->_render = new $render($type);

        $this->getData($layers);
        $this->_render->createValues($this->_values);
        $this->_render->setSize($width, $height);
        $this->_render->setLegend($this->_legend);
        $this->_render->setTitles($this->_titles);
    }

    public function draw()
    {
        $this->_render->draw();
    }

    /**
     * @todo Set this method as abstracted ? Quid of module compatibility.
     */
    public function setOption($option, $layers = 1)
    {
    }

    public function engine($params)
    {
        $context = Context::getContext();
        if (!($render = Configuration::get('PS_STATS_RENDER'))) {
            return Tools::displayError('No graph engine selected');
        }
        if (!Validate::isModuleName($render)) {
            die(Tools::displayError());
        }
        if (!file_exists(_PS_ROOT_DIR_.'/modules/'.$render.'/'.$render.'.php')) {
            return Tools::displayError('Graph engine selected is unavailable.');
        }

        $id_employee = (int)$context->employee->id;
        $id_lang = (int)$context->language->id;

        if (!isset($params['layers'])) {
            $params['layers'] = 1;
        }
        if (!isset($params['type'])) {
            $params['type'] = 'column';
        }
        if (!isset($params['width'])) {
            $params['width'] = '100%';
        }
        if (!isset($params['height'])) {
            $params['height'] = 270;
        }

        $url_params = $params;
        $url_params['render'] = $render;
        $url_params['module'] = Tools::getValue('module');
        $url_params['id_employee'] = $id_employee;
        $url_params['id_lang'] = $id_lang;
        $drawer = 'drawer.php?'.http_build_query(array_map('Tools::safeOutput', $url_params), '', '&');

        require_once(_PS_ROOT_DIR_.'/modules/'.$render.'/'.$render.'.php');
        return call_user_func(array($render, 'hookGraphEngine'), $params, $drawer);
    }

    protected static function getEmployee($employee = null, Context $context = null)
    {
        if (!Validate::isLoadedObject($employee)) {
            if (!$context) {
                $context = Context::getContext();
            }
            if (!Validate::isLoadedObject($context->employee)) {
                return false;
            }
            $employee = $context->employee;
        }

        if (empty($employee->stats_date_from) || empty($employee->stats_date_to)
            || $employee->stats_date_from == '0000-00-00' || $employee->stats_date_to == '0000-00-00') {
            if (empty($employee->stats_date_from) || $employee->stats_date_from == '0000-00-00') {
                $employee->stats_date_from = date('Y').'-01-01';
            }
            if (empty($employee->stats_date_to) || $employee->stats_date_to == '0000-00-00') {
                $employee->stats_date_to = date('Y').'-12-31';
            }
            $employee->update();
        }
        return $employee;
    }

    public function getDate()
    {
        return ModuleGraph::getDateBetween($this->_employee);
    }

    public static function getDateBetween($employee = null)
    {
        if ($employee = ModuleGraph::getEmployee($employee)) {
            return ' \''.$employee->stats_date_from.' 00:00:00\' AND \''.$employee->stats_date_to.' 23:59:59\' ';
        }
        return ' \''.date('Y-m').'-01 00:00:00\' AND \''.date('Y-m-t').' 23:59:59\' ';
    }

    public function getLang()
    {
        return $this->_id_lang;
    }
}
