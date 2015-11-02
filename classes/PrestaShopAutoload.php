<?php
/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5
 */
class PrestaShopAutoload
{
    /**
     * File where classes index is stored
     */
    const INDEX_FILE = 'cache/class_index.php';

    /**
     * @var PrestaShopAutoload
     */
    protected static $instance;

    /**
     * @var string Root directory
     */
    protected $root_dir;

    /**
     *  @var array array('classname' => 'path/to/override', 'classnamecore' => 'path/to/class/core')
     */
    public $index = array();

    public $_include_override_path = true;

    protected static $class_aliases = array(
        'Collection' => 'PrestaShopCollection',
        'Autoload' => 'PrestaShopAutoload',
        'Backup' => 'PrestaShopBackup',
        'Logger' => 'PrestaShopLogger'
    );

    protected function __construct()
    {
        $this->root_dir = _PS_CORE_DIR_.'/';
        $file = $this->normalizeDirectory(_PS_ROOT_DIR_).PrestaShopAutoload::INDEX_FILE;
        if (@filemtime($file) && is_readable($file)) {
            $this->index = include($file);
        } else {
            $this->generateIndex();
        }
    }

    /**
     * Get instance of autoload (singleton)
     *
     * @return PrestaShopAutoload
     */
    public static function getInstance()
    {
        if (!PrestaShopAutoload::$instance) {
            PrestaShopAutoload::$instance = new PrestaShopAutoload();
        }

        return PrestaShopAutoload::$instance;
    }

    /**
     * Retrieve informations about a class in classes index and load it
     *
     * @param string $classname
     */
    public function load($classname)
    {
        // Retrocompatibility
        if (isset(PrestaShopAutoload::$class_aliases[$classname]) && !interface_exists($classname, false) && !class_exists($classname, false)) {
            return eval('class '.$classname.' extends '.PrestaShopAutoload::$class_aliases[$classname].' {}');
        }

        // regenerate the class index if the requested file doesn't exists
        if ((isset($this->index[$classname]) && $this->index[$classname]['path'] && !is_file($this->root_dir.$this->index[$classname]['path']))
            || (isset($this->index[$classname.'Core']) && $this->index[$classname.'Core']['path'] && !is_file($this->root_dir.$this->index[$classname.'Core']['path']))) {
            $this->generateIndex();
        }

        // If $classname has not core suffix (E.g. Shop, Product)
        if (substr($classname, -4) != 'Core') {
            $class_dir = (isset($this->index[$classname]['override'])
                && $this->index[$classname]['override'] === true) ? $this->normalizeDirectory(_PS_ROOT_DIR_) : $this->root_dir;

            // If requested class does not exist, load associated core class
            if (isset($this->index[$classname]) && !$this->index[$classname]['path']) {
                require_once($class_dir.$this->index[$classname.'Core']['path']);

                if ($this->index[$classname.'Core']['type'] != 'interface') {
                    eval($this->index[$classname.'Core']['type'].' '.$classname.' extends '.$classname.'Core {}');
                }
            } else {
                // request a non Core Class load the associated Core class if exists
                if (isset($this->index[$classname.'Core'])) {
                    require_once($this->root_dir.$this->index[$classname.'Core']['path']);
                }

                if (isset($this->index[$classname])) {
                    require_once($class_dir.$this->index[$classname]['path']);
                }
            }
        }
        // Call directly ProductCore, ShopCore class
        elseif (isset($this->index[$classname]['path']) && $this->index[$classname]['path']) {
            require_once($this->root_dir.$this->index[$classname]['path']);
        }
    }

    /**
     * Generate classes index
     */
    public function generateIndex()
    {
        $classes = array_merge(
            $this->getClassesFromDir('classes/'),
            $this->getClassesFromDir('controllers/'),
            $this->getClassesFromDir('Adapter/'),
            $this->getClassesFromDir('Core/')
        );

        if ($this->_include_override_path) {
            $classes = array_merge(
                $classes,
                $this->getClassesFromDir('override/classes/', defined('_PS_HOST_MODE_')),
                $this->getClassesFromDir('override/controllers/', defined('_PS_HOST_MODE_'))
            );
        }

        ksort($classes);
        $content = '<?php return '.var_export($classes, true).'; ?>';

        // Write classes index on disc to cache it
        $filename = $this->normalizeDirectory(_PS_ROOT_DIR_).PrestaShopAutoload::INDEX_FILE;
        $filename_tmp = tempnam(dirname($filename), basename($filename.'.'));
        if ($filename_tmp !== false && file_put_contents($filename_tmp, $content) !== false) {
            if (!@rename($filename_tmp, $filename)) {
                unlink($filename_tmp);
            } else {
                @chmod($filename, 0666);
            }
        }
        // $filename_tmp couldn't be written. $filename should be there anyway (even if outdated), no need to die.
        else {
            Tools::error_log('Cannot write temporary file '.$filename_tmp);
        }
        $this->index = $classes;
    }

    /**
     * Retrieve recursively all classes in a directory and its subdirectories
     *
     * @param string $path Relativ path from root to the directory
     * @return array
     */
    protected function getClassesFromDir($path, $host_mode = false)
    {
        $classes = array();
        $root_dir = $host_mode ? $this->normalizeDirectory(_PS_ROOT_DIR_) : $this->root_dir;

        foreach (scandir($root_dir.$path) as $file) {
            if ($file[0] != '.') {
                if (is_dir($root_dir.$path.$file)) {
                    $classes = array_merge($classes, $this->getClassesFromDir($path.$file.'/', $host_mode));
                } elseif (substr($file, -4) == '.php') {
                    $content = file_get_contents($root_dir.$path.$file);

                    $namespacePattern = '[\\a-z0-9_]*[\\]';
                    $pattern = '#\W((abstract\s+)?class|interface)\s+(?P<classname>'.basename($file, '.php').'(?:Core)?)'
                                .'(?:\s+extends\s+'.$namespacePattern.'[a-z][a-z0-9_]*)?(?:\s+implements\s+'.$namespacePattern.'[a-z][\\a-z0-9_]*(?:\s*,\s*'.$namespacePattern.'[a-z][\\a-z0-9_]*)*)?\s*\{#i';

                    if (preg_match($pattern, $content, $m)) {
                        $classes[$m['classname']] = array(
                            'path' => $path.$file,
                            'type' => trim($m[1]),
                            'override' => $host_mode
                        );

                        if (substr($m['classname'], -4) == 'Core') {
                            $classes[substr($m['classname'], 0, -4)] = array(
                                'path' => '',
                                'type' => $classes[$m['classname']]['type'],
                                'override' => $host_mode
                            );
                        }
                    }
                }
            }
        }

        return $classes;
    }

    public function getClassPath($classname)
    {
        return (isset($this->index[$classname]) && isset($this->index[$classname]['path'])) ? $this->index[$classname]['path'] : null;
    }

    private function normalizeDirectory($directory)
    {
        return rtrim($directory, '/\\').DIRECTORY_SEPARATOR;
    }
}
