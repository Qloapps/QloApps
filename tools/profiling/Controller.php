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

abstract class Controller extends ControllerCore
{
    protected $total_filesize = 0;
    protected $total_query_time = 0;
    protected $total_global_var_size = 0;
    protected $total_modules_time = 0;
    protected $total_modules_memory = 0;
    protected $global_var_size = array();

    protected $modules_perfs = array();
    protected $hooks_perfs = array();

    protected $array_queries = array();

    protected $profiler = array();

    private function getMemoryColor($n)
    {
        $n /= 1048576;
        if ($n > 3) {
            return '<span style="color:red">'.sprintf('%0.2f', $n).'</span>';
        } elseif ($n > 1) {
            return '<span style="color:#EF8B00">'.sprintf('%0.2f', $n).'</span>';
        } elseif (round($n, 2) > 0) {
            return '<span style="color:green">'.sprintf('%0.2f', $n).'</span>';
        }
        return '<span style="color:green">-</span>';
    }

    private function getPeakMemoryColor($n)
    {
        $n /= 1048576;
        if ($n > 16) {
            return '<span style="color:red">'.sprintf('%0.1f', $n).'</span>';
        }
        if ($n > 12) {
            return '<span style="color:#EF8B00">'.sprintf('%0.1f', $n).'</span>';
        }
        return '<span style="color:green">'.sprintf('%0.1f', $n).'</span>';
    }

    private function displaySQLQueries($n)
    {
        if ($n > 150) {
            return '<span style="color:red">'.$n.' queries</span>';
        }
        if ($n > 100) {
            return '<span style="color:#EF8B00">'.$n.' queries</span>';
        }
        return '<span style="color:green">'.$n.' quer'.($n == 1 ? 'y' : 'ies').'</span>';
    }

    private function displayRowsBrowsed($n)
    {
        if ($n > 400) {
            return '<span style="color:red">'.$n.' rows browsed</span>';
        }
        if ($n > 100) {
            return '<span style="color:#EF8B00">'.$n.'  rows browsed</span>';
        }
        return '<span style="color:green">'.$n.' row'.($n == 1 ? '' : 's').' browsed</span>';
    }

    private function getPhpVersionColor($version)
    {
        if (version_compare($version, '5.3') < 0) {
            return '<span style="color:red">'.$version.' (Upgrade strongly recommended)</span>';
        } elseif (version_compare($version, '5.4') < 0) {
            return '<span style="color:#EF8B00">'.$version.' (Consider upgrading)</span>';
        }
        return '<span style="color:green">'.$version.' (OK)</span>';
    }

    private function getMySQLVersionColor($version)
    {
        if (version_compare($version, '5.5') < 0) {
            return '<span style="color:red">'.$version.' (Upgrade strongly recommended)</span>';
        } elseif (version_compare($version, '5.6') < 0) {
            return '<span style="color:#EF8B00">'.$version.' (Consider upgrading)</span>';
        }
        return '<span style="color:green">'.$version.' (OK)</span>';
    }

    private function getLoadTimeColor($n, $kikoo = false)
    {
        if ($n > 1.6) {
            return '<span style="color:red">'.round($n * 1000).'</span>'.($kikoo ? ' ms - You\'d better run your shop on a toaster' : '');
        } elseif ($n > 0.8) {
            return '<span style="color:#EF8B00">'.round($n * 1000).'</span>'.($kikoo ? ' ms - OK... for a shared hosting' : '');
        } elseif ($n > 0) {
            return '<span style="color:green">'.round($n * 1000).'</span>'.($kikoo ? ' ms - Unicorn powered webserver!' : '');
        }
        return '<span style="color:green">-</span>'.($kikoo ? ' ms - Faster than light' : '');
    }

    private function getTotalQueriyingTimeColor($n)
    {
        if ($n >= 100) {
            return '<span style="color:red">'.$n.'</span>';
        } elseif ($n >= 50) {
            return '<span style="color:#EF8B00">'.$n.'</span>';
        }
        return '<span style="color:green">'.$n.'</span>';
    }

    private function getNbQueriesColor($n)
    {
        if ($n >= 100) {
            return '<span style="color:red">'.$n.'</span>';
        } elseif ($n >= 50) {
            return '<span style="color:#EF8B00">'.$n.'</span>';
        }
        return '<span style="color:green">'.$n.'</span>';
    }

    private function getTimeColor($n)
    {
        if ($n > 4) {
            return 'style="color:red"';
        }
        if ($n > 2) {
            return 'style="color:#EF8B00"';
        }
        return 'style="color:green"';
    }

    private function getQueryColor($n)
    {
        if ($n > 5) {
            return 'style="color:red"';
        }
        if ($n > 2) {
            return 'style="color:#EF8B00"';
        }
        return 'style="color:green"';
    }

    private function getTableColor($n)
    {
        if ($n > 30) {
            return 'style="color:red"';
        }
        if ($n > 20) {
            return 'style="color:#EF8B00"';
        }
        return 'style="color:green"';
    }

    private function getObjectModelColor($n)
    {
        if ($n > 50) {
            return 'style="color:red"';
        }
        if ($n > 10) {
            return 'style="color:#EF8B00"';
        }
        return 'style="color:green"';
    }

    protected function stamp($block)
    {
        return array('block' => $block, 'memory_usage' => memory_get_usage(), 'peak_memory_usage' => memory_get_peak_usage(), 'time' => microtime(true));
    }

    public function __construct()
    {
        $this->profiler[] = $this->stamp('config');

        parent::__construct();
        $this->profiler[] = $this->stamp('__construct');
    }

    public function run()
    {
        $this->init();
        $this->profiler[] = $this->stamp('init');

        if ($this->checkAccess()) {
            $this->profiler[] = $this->stamp('checkAccess');

            if (!$this->content_only && ($this->display_header || (isset($this->className) && $this->className))) {
                $this->setMedia();
                $this->profiler[] = $this->stamp('setMedia');
            }

            $this->postProcess();
            $this->profiler[] = $this->stamp('postProcess');

            if (!$this->content_only && ($this->display_header || (isset($this->className) && $this->className))) {
                $this->initHeader();
                $this->profiler[] = $this->stamp('initHeader');
            }

            $this->initContent();
            $this->profiler[] = $this->stamp('initContent');

            if (!$this->content_only && ($this->display_footer || (isset($this->className) && $this->className))) {
                $this->initFooter();
                $this->profiler[] = $this->stamp('initFooter');
            }

            if ($this->ajax) {
                $action = Tools::toCamelCase(Tools::getValue('action'), true);
                if (!empty($action) && method_exists($this, 'displayAjax'.$action)) {
                    $this->{'displayAjax'.$action}();
                } elseif (method_exists($this, 'displayAjax')) {
                    $this->displayAjax();
                }
                return;
            }
        } else {
            $this->initCursedPage();
        }

        $this->displayProfiling();
    }

    private function getVarSize($var)
    {
        $start_memory = memory_get_usage();
        try {
            $tmp = Tools::unSerialize(serialize($var));
        } catch (Exception $e) {
            $tmp = $this->getVarData($var);
        }
        $size = memory_get_usage() - $start_memory;
        return $size;
    }

    private function getVarData($var)
    {
        if (is_object($var)) {
            return $var;
        }
        return (string)$var;
    }

    protected function processProfilingData()
    {
        global $start_time;

        // Including a lot of files uses memory
        foreach (get_included_files() as $file) {
            $this->total_filesize += filesize($file);
        }

        // Sum querying time
        foreach (Db::getInstance()->queries as $data) {
            $this->total_query_time += $data['time'];
        }

        foreach ($GLOBALS as $key => $value) {
            if ($key != 'GLOBALS') {
                $this->total_global_var_size += ($size = $this->getVarSize($value));
                if ($size > 1024) {
                    $this->global_var_size[$key] = round($size / 1024);
                }
            }
        }
        arsort($this->global_var_size);

        $cache = Cache::retrieveAll();
        $this->total_cache_size = $this->getVarSize($cache);

        // Retrieve module perfs
        $result = Db::getInstance()->ExecuteS('
    		SELECT *
    		FROM '._DB_PREFIX_.'modules_perfs
    		WHERE session = '.(int)Module::$_log_modules_perfs_session.'
    		AND time_start >= '.(float)$start_time.'
    		AND time_end <= '.(float)$this->profiler[count($this->profiler) - 1]['time']
        );

        foreach ($result as $row) {
            $tmp_time = $row['time_end'] - $row['time_start'];
            $tmp_memory = $row['memory_end'] - $row['memory_start'];
            $this->total_modules_time += $tmp_time;
            $this->total_modules_memory += $tmp_memory;

            if (!isset($this->modules_perfs[$row['module']])) {
                $this->modules_perfs[$row['module']] = array('time' => 0, 'memory' => 0, 'methods' => array());
            }
            $this->modules_perfs[$row['module']]['time'] += $tmp_time;
            $this->modules_perfs[$row['module']]['methods'][$row['method']]['time'] = $tmp_time;
            $this->modules_perfs[$row['module']]['memory'] += $tmp_memory;
            $this->modules_perfs[$row['module']]['methods'][$row['method']]['memory'] = $tmp_memory;

            if (!isset($this->hooks_perfs[$row['method']])) {
                $this->hooks_perfs[$row['method']] = array('time' => 0, 'memory' => 0, 'modules' => array());
            }
            $this->hooks_perfs[$row['method']]['time'] += $tmp_time;
            $this->hooks_perfs[$row['method']]['modules'][$row['module']]['time'] = $tmp_time;
            $this->hooks_perfs[$row['method']]['memory'] += $tmp_memory;
            $this->hooks_perfs[$row['method']]['modules'][$row['module']]['memory'] = $tmp_memory;
        }
        uasort($this->modules_perfs, 'prestashop_querytime_sort');
        uasort($this->hooks_perfs, 'prestashop_querytime_sort');

        $queries = Db::getInstance()->queries;
        uasort($queries, 'prestashop_querytime_sort');
        foreach ($queries as $data) {
            $query_row = array(
                'time' => $data['time'],
                'query' => $data['query'],
                'location' => str_replace('\\', '/', substr($data['stack'][0]['file'], strlen(_PS_ROOT_DIR_))).':'.$data['stack'][0]['line'],
                'filesort' => false,
                'rows' => 1,
                'group_by' => false,
                'stack' => array(),
            );
            if (preg_match('/^\s*select\s+/i', $data['query'])) {
                $explain = Db::getInstance()->executeS('explain '.$data['query']);
                if (stristr($explain[0]['Extra'], 'filesort')) {
                    $query_row['filesort'] = true;
                }
                foreach ($explain as $row) {
                    $query_row['rows'] *= $row['rows'];
                }
                if (stristr($data['query'], 'group by') && !preg_match('/(avg|count|min|max|group_concat|sum)\s*\(/i', $data['query'])) {
                    $query_row['group_by'] = true;
                }
            }

            array_shift($data['stack']);
            foreach ($data['stack'] as $call) {
                $query_row['stack'][] = str_replace('\\', '/', substr($call['file'], strlen(_PS_ROOT_DIR_))).':'.$call['line'];
            }

            $this->array_queries[] = $query_row;
        }

        uasort(ObjectModel::$debug_list, function ($a, $b) { return (count($a) < count($b)) ? 1 : -1; } );
        arsort(Db::getInstance()->tables);
        arsort(Db::getInstance()->uniqQueries);
    }

    protected function displayProfilingLinks()
    {
        echo '
		<div class="col-4">
			<ol>
				<li><a href="#stopwatch">Stopwatch SQL</a></li>
				<li><a href="#doubles">Doubles</a></li>
				<li><a href="#tables">Tables stress</a></li>
				'.(isset(ObjectModel::$debug_list) ? '<li><a href="#objectModels">ObjectModel instances</a></li>' : '').'
				<li><a href="#includedFiles">Included Files</a></li>
			</ol>
		</div>';
    }

    protected function displayProfilingStyle()
    {
        echo '
		<style>
			#prestashop_profiling {
				padding: 20px;
			}

			.ps_back-office.page-sidebar #prestashop_profiling {
				margin-left: 210px;
			}
			.ps_back-office.page-sidebar-closed #prestashop_profiling {
				margin-left: 50px;
			}
			.ps_back-office #prestashop_profiling {
				clear: both;
				padding: 10px;
				margin-bottom: 50px;
			}

			#prestashop_profiling * {
				box-sizing:border-box;
				-moz-box-sizing:border-box;
				color: #888;
			}

			#prestashop_profiling td .pre {
				padding: 6px;
				margin-right: 10px;
				overflow: auto;
				display: block;
				color: #777;
				font-size: 12px;
				line-height: 1.42857;
				word-break: break-all;
				word-wrap: break-word;
				background-color: whitesmoke;
				border: 1px solid #cccccc;
				max-width: 960px;
			}
			.sortable thead th {
				cursor:pointer;
			}

			#prestashop_profiling .row {
				clear: both;
				margin-bottom: 20px;
			}

			#prestashop_profiling .col-4 {
				float: left;
				padding: 0 10px;
				width: 33%;
			}
			@media (max-width: 1200px) {
				#prestashop_profiling .col-4 {
					width: 50%;
				}
			}
			@media (max-width: 600px) {
				#prestashop_profiling .col-4 {
					width: 100%;
				}
			}
		</style>
		<script type="text/javascript" src="https://cdn.rawgit.com/drvic10k/bootstrap-sortable/1.11.2/Scripts/bootstrap-sortable.js"></script>';
    }

    protected function displayProfilingSummary()
    {
        global $start_time;

        echo '
		<div class="col-4">
			<table class="table table-condensed">
				<tr><td>Load Time</td><td>'.$this->getLoadTimeColor($this->profiler[count($this->profiler) - 1]['time'] - $start_time, true).'</td></tr>
				<tr><td>Querying Time</td><td>'.$this->getTotalQueriyingTimeColor(round(1000 * $this->total_query_time)).' ms</span>
				<tr><td>Queries</td><td>'.$this->getNbQueriesColor(count($this->array_queries)).'</td></tr>
				<tr><td>Memory Peak Usage</td><td>'.$this->getPeakMemoryColor($this->profiler[count($this->profiler) - 1]['peak_memory_usage']).' Mb</td></tr>
				<tr><td>Included Files</td><td>'.count(get_included_files()).' files - '.$this->getMemoryColor($this->total_filesize).' Mb</td></tr>
				<tr><td>PrestaShop Cache</td><td>'.$this->getMemoryColor($this->total_cache_size).' Mb</td></tr>
				<tr><td><a href="javascript:void(0);" onclick="$(\'.global_vars_detail\').toggle();">Global vars</a></td><td>'.$this->getMemoryColor($this->total_global_var_size).' Mb</td></tr>';
        foreach ($this->global_var_size as $global => $size) {
            echo '<tr class="global_vars_detail" style="display:none"><td>- global $'.$global.'</td><td>'.$size.'k</td></tr>';
        }
        echo '
			</table>
		</div>';
    }

    protected function displayProfilingConfiguration()
    {
        echo '
		<div class="col-4">
			<table class="table table-condensed">
				<tr><td>PrestaShop Version</td><td>'._PS_VERSION_.'</td></tr>
				<tr><td>PHP Version</td><td>'.$this->getPhpVersionColor(phpversion()).'</td></tr>
				<tr><td>MySQL Version</td><td>'.$this->getMySQLVersionColor(Db::getInstance()->getVersion()).'</td></tr>
				<tr><td>Memory Limit</td><td>'.ini_get('memory_limit').'</td></tr>
				<tr><td>Max Execution Time</td><td>'.ini_get('max_execution_time').'s</td></tr>
				<tr><td>Smarty Cache</td><td><span style="color:'.(Configuration::get('PS_SMARTY_CACHE') ? 'green">enabled' : 'red">disabled').'</td></tr>
				<tr><td>Smarty Compilation</td><td><span style="color:'.(Configuration::get('PS_SMARTY_FORCE_COMPILE') == 0 ? 'green">never recompile' : (Configuration::get('PS_SMARTY_FORCE_COMPILE') == 1 ? '#EF8B00">auto' : 'red">force compile')).'</td></tr>
			</table>
		</div>';
    }

    protected function displayProfilingRun()
    {
        global $start_time;

        echo '
		<div class="col-4">
			<table class="table table-condensed">
				<tr><th>&nbsp;</th><th>Time</th><th>Cumulated Time</th><th>Memory Usage</th><th>Memory Peak Usage</th></tr>';
        $last = array('time' => $start_time, 'memory_usage' => 0);
        foreach ($this->profiler as $row) {
            if ($row['block'] == 'checkAccess' && $row['time'] == $last['time']) {
                continue;
            }
            echo '<tr>
				<td>'.$row['block'].'</td>
				<td>'.$this->getLoadTimeColor($row['time'] - $last['time']).' ms</td>
				<td>'.$this->getLoadTimeColor($row['time'] - $start_time).' ms</td>
				<td>'.$this->getMemoryColor($row['memory_usage'] - $last['memory_usage']).' Mb</td>
				<td>'.$this->getMemoryColor($row['peak_memory_usage']).' Mb</td>
			</tr>';
            $last = $row;
        }
        echo '
			</table>
		</div>';
    }

    protected function displayProfilingHooks()
    {
        $count_hooks = count($this->hooks_perfs);

        echo '
		<div class="col-4">
			<table class="table table-condensed">
				<tr>
					<th>Hook</th>
					<th>Time</th>
					<th>Memory Usage</th>
				</tr>';
        foreach ($this->hooks_perfs as $hook => $hooks_perfs) {
            echo '
				<tr>
					<td>
						<a href="javascript:void(0);" onclick="$(\'.'.$hook.'_modules_details\').toggle();">'.$hook.'</a>
					</td>
					<td>
						'.$this->getLoadTimeColor($hooks_perfs['time']).' ms
					</td>
					<td>
						'.$this->getMemoryColor($hooks_perfs['memory']).' Mb
					</td>
				</tr>';
            foreach ($hooks_perfs['modules'] as $module => $perfs) {
                echo '
				<tr class="'.$hook.'_modules_details" style="background-color:#EFEFEF;display:none">
					<td>
						=&gt; '.$module.'
					</td>
					<td>
						'.$this->getLoadTimeColor($perfs['time']).' ms
					</td>
					<td>
						'.$this->getMemoryColor($perfs['memory']).' Mb
					</td>
				</tr>';
            }
        }
        echo '	<tr>
					<th><b>'.($count_hooks == 1 ? '1 hook' : (int)$count_hooks.' hooks').'</b></th>
					<th>'.$this->getLoadTimeColor($this->total_modules_time).' ms</th>
					<th>'.$this->getMemoryColor($this->total_modules_memory).' Mb</th>
				</tr>
			</table>
		</div>';
    }

    protected function displayProfilingModules()
    {
        $count_modules = count($this->modules_perfs);

        echo '
		<div class="col-4">
			<table class="table table-condensed">
				<tr>
					<th>Module</th>
					<th>Time</th>
					<th>Memory Usage</th>
				</tr>';
        foreach ($this->modules_perfs as $module => $modules_perfs) {
            echo '
				<tr>
					<td>
						<a href="javascript:void(0);" onclick="$(\'.'.$module.'_hooks_details\').toggle();">'.$module.'</a>
					</td>
					<td>
						'.$this->getLoadTimeColor($modules_perfs['time']).' ms
					</td>
					<td>
						'.$this->getMemoryColor($modules_perfs['memory']).' Mb
					</td>
				</tr>';
            foreach ($modules_perfs['methods'] as $hook => $perfs) {
                echo '
				<tr class="'.$module.'_hooks_details" style="background-color:#EFEFEF;display:none">
					<td>
						=&gt; '.$hook.'
					</td>
					<td>
						'.$this->getLoadTimeColor($perfs['time']).' ms
					</td>
					<td>
						'.$this->getMemoryColor($perfs['memory']).' Mb
					</td>
				</tr>';
            }
        }
        echo '	<tr>
					<th><b>'.($count_modules == 1 ? '1 module' : (int)$count_modules.' modules').'</b></th>
					<th>'.$this->getLoadTimeColor($this->total_modules_time).' ms</th>
					<th>'.$this->getMemoryColor($this->total_modules_memory).' Mb</th>
				</tr>
			</table>
		</div>';
    }

    protected function displayProfilingStopwatch()
    {
        echo '
		<div class="row">
			<h2><a name="stopwatch">Stopwatch SQL - '.count($this->array_queries).' queries</a></h2>
			<table class="table table-condensed table-bordered sortable">
				<thead>
					<tr>
						<th>Query</th>
						<th>Time (ms)</th>
						<th>Rows</th>
						<th>Filesort</th>
						<th>Group By</th>
						<th>Location</th>
					</tr>
				</thead>
				<tbody>';
        foreach ($this->array_queries as $data) {
            $callstack = implode('<br>', $data['stack']);
            $callstack_md5 = md5($callstack);

            echo '
				<tr>
					<td class="pre"><pre>'.preg_replace("/(^[\s]*)/m", "", htmlspecialchars($data['query'], ENT_NOQUOTES, 'utf-8', false)).'</pre></td>
					<td data-value="'.$data['time'].'"><span '.$this->getTimeColor($data['time'] * 1000).'>'.(round($data['time'] * 1000, 1) < 0.1 ? '< 1' : round($data['time'] * 1000, 1)).'</span></td>
					<td>'.(int)$data['rows'].'</td>
					<td data-value="'.$data['filesort'].'">'.($data['filesort'] ? '<span style="color:red">Yes</span>' : '').'</td>
					<td data-value="'.$data['group_by'].'">'.($data['group_by'] ? '<span style="color:red">Yes</span>' : '').'</td>
					<td data-value="'.$data['location'].'">
						<a href="javascript:void(0);" onclick="$(\'#callstack_'.$callstack_md5.'\').toggle();">'.$data['location'].'</a>
						<div id="callstack_'.$callstack_md5.'" style="display:none">'.implode('<br>', $data['stack']).'</div>
					</td>
				</tr>';
        }
        echo '</table>
		</div>';
    }

    protected function displayProfilingDoubles()
    {
        echo '<div class="row">
		<h2><a name="doubles">Doubles</a></h2>
			<table class="table table-condensed">';
        foreach (Db::getInstance()->uniqQueries as $q => $nb) {
            if ($nb > 1) {
                echo '<tr><td><span '.$this->getQueryColor($nb).'>'.$nb.'</span></td><td class="pre"><pre>'.$q.'</pre></td></tr>';
            }
        }
        echo '</table>
		</div>';
    }

    protected function displayProfilingTableStress()
    {
        echo '<div class="row">
		<h2><a name="tables">Tables stress</a></h2>
		<table class="table table-condensed">';
        foreach (Db::getInstance()->tables as $table => $nb) {
            echo '<tr><td><span '.$this->getTableColor($nb).'>'.$nb.'</span> '.$table.'</td></tr>';
        }
        echo '</table>
		</div>';
    }

    protected function displayProfilingObjectModel()
    {
        echo '
		<div class="row">
			<h2><a name="objectModels">ObjectModel instances</a></h2>
			<table class="table table-condensed">
				<tr><th>Name</th><th>Instances</th><th>Source</th></tr>';
        foreach (ObjectModel::$debug_list as $class => $info) {
            echo '<tr>
					<td>'.$class.'</td>
					<td><span '.$this->getObjectModelColor(count($info)).'>'.count($info).'</span></td>
					<td>';
            foreach ($info as $trace) {
                echo str_replace(array(_PS_ROOT_DIR_, '\\'), array('', '/'), $trace['file']).' ['.$trace['line'].']<br />';
            }
            echo '	</td>
				</tr>';
        }
        echo '</table>
		</div>';
    }

    protected function displayProfilingFiles()
    {
        $i = 0;

        echo '<div class="row">
		<h2><a name="includedFiles">Included Files</a></h2>
		<table class="table table-condensed">
			<tr><th>#</th><th>Filename</th></tr>';
        foreach (get_included_files() as $file) {
            $file = str_replace('\\', '/', str_replace(_PS_ROOT_DIR_, '', $file));
            if (strpos($file, '/tools/profiling/') === 0) {
                continue;
            }
            echo '<tr><td>'.(++$i).'</td><td>'.$file.'</td></tr>';
        }
        echo '</table>
		</div>';
    }

    public function displayProfiling()
    {
        if (!empty($this->redirect_after)) {
            echo '
			<html>
				<head>
					<meta charset="utf-8" />
					<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
					<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
					<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
				</head>
				<body>
					<div class="container" style="width:100%">
						<div class="row">
							<div class="col-lg-12">
								<h2>Caught redirection to <a href="'.htmlspecialchars($this->redirect_after).'"> '.htmlspecialchars($this->redirect_after).' </a></h2>
							</div>
						</div>';
        } else {
            // Call original display method
            $this->display();
            $this->profiler[] = $this->stamp('display');
        }

        // Process all profiling data
        $this->processProfilingData();

        // Add some specific style for profiling information
        $this->displayProfilingStyle();

        echo '<div id="prestashop_profiling" class="bootstrap">';

        echo '<div class="row">';
        $this->displayProfilingSummary();
        $this->displayProfilingConfiguration();
        $this->displayProfilingRun();
        echo '</div><div class="row">';
        $this->displayProfilingHooks();
        $this->displayProfilingModules();
        $this->displayProfilingLinks();
        echo '</div>';

        $this->displayProfilingStopwatch();
        $this->displayProfilingDoubles();
        $this->displayProfilingTableStress();
        if (isset(ObjectModel::$debug_list)) {
            $this->displayProfilingObjectModel();
        }
        $this->displayProfilingFiles();

        if (!empty($this->redirect_after)) {
            echo '</div></body></html>';
        }
    }
}

function prestashop_querytime_sort($a, $b)
{
    if ($a['time'] == $b['time']) {
        return 0;
    }
    return ($a['time'] > $b['time']) ? -1 : 1;
}
