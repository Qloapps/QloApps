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

/**
 * @TODO Move undeclared variables and methods to this (base) class: $errors, $layout, checkLiveEditAccess, etc.
 * @since 1.5.0
 */
abstract class ControllerCore
{
    /** @var Context */
    protected $context;

    /** @var array List of CSS files */
    public $css_files = array();

    /** @var array List of JavaScript files */
    public $js_files = array();

    /** @var array List of PHP errors */
    public static $php_errors = array();

    /** @var bool Set to true to display page header */
    protected $display_header;

    /** @var bool Set to true to display page header javascript */
    protected $display_header_javascript;

    /** @var string Template filename for the page content */
    protected $template;

    /** @var string Set to true to display page footer */
    protected $display_footer;

    /** @var bool Set to true to only render page content (used to get iframe content) */
    protected $content_only = false;

    /** @var bool If AJAX parameter is detected in request, set this flag to true */
    public $ajax = false;

    /** @var bool If set to true, page content and messages will be encoded to JSON before responding to AJAX request */
    protected $json = false;

    /** @var string JSON response status string */
    protected $status = '';

    /**
     * @see Controller::run()
     * @var string|null Redirect link. If not empty, the user will be redirected after initializing and processing input.
     */
    protected $redirect_after = null;

    /** @var string Controller type. Possible values: 'front', 'modulefront', 'admin', 'moduleadmin' */
    public $controller_type;

    /** @var string Controller name */
    public $php_self;

    /**
     * Check if the controller is available for the current user/visitor
     */
    abstract public function checkAccess();

    /**
     * Check if the current user/visitor has valid view permissions
     */
    abstract public function viewAccess();

    /**
     * Initialize the page
     */
    public function init()
    {
        if (_PS_MODE_DEV_ && $this->controller_type == 'admin') {
            set_error_handler(array(__CLASS__, 'myErrorHandler'));
        }

        if (!defined('_PS_BASE_URL_')) {
            define('_PS_BASE_URL_', Tools::getShopDomain(true));
        }

        if (!defined('_PS_BASE_URL_SSL_')) {
            define('_PS_BASE_URL_SSL_', Tools::getShopDomainSsl(true));
        }
    }

    /**
     * Do the page treatment: process input, process AJAX, etc.
     */
    abstract public function postProcess();

    /**
     * Displays page view
     */
    abstract public function display();

    /**
     * Sets default media list for this controller
     */
    abstract public function setMedia();

    /**
     * returns a new instance of this controller
     *
     * @param string $class_name
     * @param bool $auth
     * @param bool $ssl
     * @return Controller
     */
    public static function getController($class_name, $auth = false, $ssl = false)
    {
        return new $class_name($auth, $ssl);
    }

    public function __construct()
    {
        if (is_null($this->display_header)) {
            $this->display_header = true;
        }

        if (is_null($this->display_header_javascript)) {
            $this->display_header_javascript = true;
        }

        if (is_null($this->display_footer)) {
            $this->display_footer = true;
        }

        $this->context = Context::getContext();
        $this->context->controller = $this;

        // Usage of ajax parameter is deprecated
        $this->ajax = Tools::getValue('ajax') || Tools::isSubmit('ajax');

        if (!headers_sent()
            && isset($_SERVER['HTTP_USER_AGENT'])
            && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false)) {
            header('X-UA-Compatible: IE=edge,chrome=1');
        }
    }

    /**
     * Starts the controller process (this method should not be overridden!)
     */
    public function run()
    {
        $this->init();
        if ($this->checkAccess()) {
            // setMedia MUST be called before postProcess
            if (!$this->content_only && ($this->display_header || (isset($this->className) && $this->className))) {
                $this->setMedia();
            }

            // postProcess handles ajaxProcess
            $this->postProcess();

            if (!empty($this->redirect_after)) {
                $this->redirect();
            }

            if (!$this->content_only && ($this->display_header || (isset($this->className) && $this->className))) {
                $this->initHeader();
            }

            if ($this->viewAccess()) {
                $this->initContent();
            } else {
                $this->errors[] = Tools::displayError('Access denied.');
            }

            if (!$this->content_only && ($this->display_footer || (isset($this->className) && $this->className))) {
                $this->initFooter();
            }

            // Default behavior for ajax process is to use $_POST[action] or $_GET[action]
            // then using displayAjax[action]
            if ($this->ajax) {
                $action = Tools::toCamelCase(Tools::getValue('action'), true);

                if (!empty($action) && method_exists($this, 'displayAjax'.$action)) {
                    $this->{'displayAjax'.$action}();
                } elseif (method_exists($this, 'displayAjax')) {
                    $this->displayAjax();
                }
            } else {
                $this->display();
            }
        } else {
            $this->initCursedPage();
            $this->smartyOutputContent($this->layout);
        }
    }

    /**
     * Sets page header display
     *
     * @param bool $display
     */
    public function displayHeader($display = true)
    {
        $this->display_header = $display;
    }

    /**
     * Sets page header javascript display
     *
     * @param bool $display
     */
    public function displayHeaderJavaScript($display = true)
    {
        $this->display_header_javascript = $display;
    }

    /**
     * Sets page header display
     *
     * @param bool $display
     */
    public function displayFooter($display = true)
    {
        $this->display_footer = $display;
    }

    /**
     * Sets template file for page content output
     *
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Assigns Smarty variables for the page header
     */
    abstract public function initHeader();

    /**
     * Assigns Smarty variables for the page main content
     */
    abstract public function initContent();

    /**
     * Assigns Smarty variables when access is forbidden
     */
    abstract public function initCursedPage();

    /**
     * Assigns Smarty variables for the page footer
     */
    abstract public function initFooter();

    /**
     * Redirects to $this->redirect_after after the process if there is no error
     */
    abstract protected function redirect();

    /**
     * Set $this->redirect_after that will be used by redirect() after the process
     */
    public function setRedirectAfter($url)
    {
        $this->redirect_after = $url;
    }

    /**
     * Adds a new stylesheet(s) to the page header.
     *
     * @param string|array $css_uri Path to CSS file, or list of css files like this : array(array(uri => media_type), ...)
     * @param string $css_media_type
     * @param int|null $offset
     * @param bool $check_path
     * @return true
     */
    public function addCSS($css_uri, $css_media_type = 'all', $offset = null, $check_path = true)
    {
        if (!is_array($css_uri)) {
            $css_uri = array($css_uri);
        }

        foreach ($css_uri as $css_file => $media) {
            if (is_string($css_file) && strlen($css_file) > 1) {
                if ($check_path) {
                    $css_path = Media::getCSSPath($css_file, $media);
                } else {
                    $css_path = array($css_file => $media);
                }
            } else {
                if ($check_path) {
                    $css_path = Media::getCSSPath($media, $css_media_type);
                } else {
                    $css_path = array($media => $css_media_type);
                }
            }

            $key = is_array($css_path) ? key($css_path) : $css_path;
            if ($css_path && (!isset($this->css_files[$key]) || ($this->css_files[$key] != reset($css_path)))) {
                $size = count($this->css_files);
                if ($offset === null || $offset > $size || $offset < 0 || !is_numeric($offset)) {
                    $offset = $size;
                }

                $this->css_files = array_merge(array_slice($this->css_files, 0, $offset), $css_path, array_slice($this->css_files, $offset));
            }
        }
    }

    /**
     * Removes CSS stylesheet(s) from the queued stylesheet list
     *
     * @param string|array $css_uri Path to CSS file or an array like: array(array(uri => media_type), ...)
     * @param string $css_media_type
     * @param bool $check_path
     */
    public function removeCSS($css_uri, $css_media_type = 'all', $check_path = true)
    {
        if (!is_array($css_uri)) {
            $css_uri = array($css_uri);
        }

        foreach ($css_uri as $css_file => $media) {
            if (is_string($css_file) && strlen($css_file) > 1) {
                if ($check_path) {
                    $css_path = Media::getCSSPath($css_file, $media);
                } else {
                    $css_path = array($css_file => $media);
                }
            } else {
                if ($check_path) {
                    $css_path = Media::getCSSPath($media, $css_media_type);
                } else {
                    $css_path = array($media => $css_media_type);
                }
            }

            if ($css_path && isset($this->css_files[key($css_path)]) && ($this->css_files[key($css_path)] == reset($css_path))) {
                unset($this->css_files[key($css_path)]);
            }
        }
    }

    /**
     * Adds a new JavaScript file(s) to the page header.
     *
     * @param string|array $js_uri Path to JS file or an array like: array(uri, ...)
     * @param bool $check_path
     * @return void
     */
    public function addJS($js_uri, $check_path = true)
    {
        if (is_array($js_uri)) {
            foreach ($js_uri as $js_file) {
                $js_file = explode('?', $js_file);
                $version = '';
                if (isset($js_file[1]) && $js_file[1]) {
                    $version = $js_file[1];
                }
                $js_path = $js_file = $js_file[0];
                if ($check_path) {
                    $js_path = Media::getJSPath($js_file);
                }

                // $key = is_array($js_path) ? key($js_path) : $js_path;
                if ($js_path && !in_array($js_path, $this->js_files)) {
                    $this->js_files[] = $js_path.($version ? '?'.$version : '');
                }
            }
        } else {
            $js_uri = explode('?', $js_uri);
            $version = '';
            if (isset($js_uri[1]) && $js_uri[1]) {
                $version = $js_uri[1];
            }
            $js_path = $js_uri = $js_uri[0];
            if ($check_path) {
                $js_path = Media::getJSPath($js_uri);
            }

            if ($js_path && !in_array($js_path, $this->js_files)) {
                $this->js_files[] = $js_path.($version ? '?'.$version : '');
            }
        }
    }

    /**
     * Removes JS file(s) from the queued JS file list
     *
     * @param string|array $js_uri Path to JS file or an array like: array(uri, ...)
     * @param bool $check_path
     */
    public function removeJS($js_uri, $check_path = true)
    {
        if (is_array($js_uri)) {
            foreach ($js_uri as $js_file) {
                $js_path = $js_file;
                if ($check_path) {
                    $js_path = Media::getJSPath($js_file);
                }

                if ($js_path && in_array($js_path, $this->js_files)) {
                    unset($this->js_files[array_search($js_path, $this->js_files)]);
                }
            }
        } else {
            $js_path = $js_uri;
            if ($check_path) {
                $js_path = Media::getJSPath($js_uri);
            }

            if ($js_path) {
                unset($this->js_files[array_search($js_path, $this->js_files)]);
            }
        }
    }

    /**
     * Adds jQuery library file to queued JS file list
     *
     * @param string|null $version jQuery library version
     * @param string|null $folder jQuery file folder
     * @param bool $minifier If set tot true, a minified version will be included.
     */
    public function addJquery($version = null, $folder = null, $minifier = true)
    {
        $this->addJS(Media::getJqueryPath($version, $folder, $minifier), false);
    }

    /**
     * Adds jQuery UI component(s) to queued JS file list
     *
     * @param string|array $component
     * @param string $theme
     * @param bool $check_dependencies
     */
    public function addJqueryUI($component, $theme = 'base', $check_dependencies = true)
    {
        if (!is_array($component)) {
            $component = array($component);
        }

        foreach ($component as $ui) {
            $ui_path = Media::getJqueryUIPath($ui, $theme, $check_dependencies);
            $this->addCSS($ui_path['css'], 'all', false);
            $this->addJS($ui_path['js'], false);
        }
    }

    /**
     * Adds jQuery plugin(s) to queued JS file list
     *
     * @param string|array $name
     * @param string null $folder
     * @param bool $css
     */
    public function addJqueryPlugin($name, $folder = null, $css = true)
    {
        if (!is_array($name)) {
            $name = array($name);
        }
        if (is_array($name)) {
            foreach ($name as $plugin) {
                $plugin_path = Media::getJqueryPluginPath($plugin, $folder);

                if (!empty($plugin_path['js'])) {
                    $this->addJS($plugin_path['js'], false);
                }
                if ($css && !empty($plugin_path['css'])) {
                    $this->addCSS(key($plugin_path['css']), 'all', null, false);
                }
            }
        }
    }

    /**
     * Checks if the controller has been called from XmlHttpRequest (AJAX)
     *
     * @since 1.5
     * @return bool
     */
    public function isXmlHttpRequest()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    /**
     * Renders controller templates and generates page content
     *
     * @param array|string $content Template file(s) to be rendered
     * @throws Exception
     * @throws SmartyException
     */
    protected function smartyOutputContent($content)
    {
        $this->context->cookie->write();

        $html = '';
        $js_tag = 'js_def';
        $this->context->smarty->assign($js_tag, $js_tag);

        if (is_array($content)) {
            foreach ($content as $tpl) {
                $html .= $this->context->smarty->fetch($tpl);
            }
        } else {
            $html = $this->context->smarty->fetch($content);
        }

        $html = trim($html);

        if (in_array($this->controller_type, array('front', 'modulefront')) && !empty($html) && $this->getLayout()) {
            $live_edit_content = '';
            if (!$this->useMobileTheme() && $this->checkLiveEditAccess()) {
                $live_edit_content = $this->getLiveEditFooter();
            }

            $dom_available = extension_loaded('dom') ? true : false;
            $defer = (bool)Configuration::get('PS_JS_DEFER');

            if ($defer && $dom_available) {
                $html = Media::deferInlineScripts($html);
            }
            $html = trim(str_replace(array('</body>', '</html>'), '', $html))."\n";

            $this->context->smarty->assign(array(
                $js_tag => Media::getJsDef(),
                'js_files' =>  $defer ? array_unique($this->js_files) : array(),
                'js_inline' => ($defer && $dom_available) ? Media::getInlineScript() : array()
            ));

            $javascript = $this->context->smarty->fetch(_PS_ALL_THEMES_DIR_.'javascript.tpl');

            if ($defer && (!isset($this->ajax) || ! $this->ajax)) {
                echo $html.$javascript;
            } else {
                echo preg_replace('/(?<!\$)'.$js_tag.'/', $javascript, $html);
            }
            echo $live_edit_content.((!isset($this->ajax) || ! $this->ajax) ? '</body></html>' : '');
        } else {
            echo $html;
        }
    }

    /**
     * Checks if a template is cached
     *
     * @param string $template
     * @param string|null $cache_id Cache item ID
     * @param string|null $compile_id
     * @return bool
     */
    protected function isCached($template, $cache_id = null, $compile_id = null)
    {
        Tools::enableCache();
        $res = $this->context->smarty->isCached($template, $cache_id, $compile_id);
        Tools::restoreCacheSettings();

        return $res;
    }

    /**
     * Custom error handler
     *
     * @param string $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @return bool
     */
    public static function myErrorHandler($errno, $errstr, $errfile, $errline)
    {
        if (error_reporting() === 0) {
            return false;
        }

        switch ($errno) {
            case E_USER_ERROR:
            case E_ERROR:
                die('Fatal error: '.$errstr.' in '.$errfile.' on line '.$errline);
            break;
            case E_USER_WARNING:
            case E_WARNING:
                $type = 'Warning';
            break;
            case E_USER_NOTICE:
            case E_NOTICE:
                $type = 'Notice';
            break;
            default:
                $type = 'Unknown error';
            break;
        }

        Controller::$php_errors[] = array(
            'type'    => $type,
            'errline' => (int)$errline,
            'errfile' => str_replace('\\', '\\\\', $errfile), // Hack for Windows paths
            'errno'   => (int)$errno,
            'errstr'  => $errstr
        );
        Context::getContext()->smarty->assign('php_errors', Controller::$php_errors);

        return true;
    }

    /**
     * Dies and echoes output value
     *
     * @param string|null $value
     * @param string|null $controller
     * @param string|null $method
     */
    protected function ajaxDie($value = null, $controller = null, $method = null)
    {
        if ($controller === null) {
            $controller = get_class($this);
        }

        if ($method === null) {
            $bt = debug_backtrace();
            $method = $bt[1]['function'];
        }

        Hook::exec('actionBeforeAjaxDie', array('controller' => $controller, 'method' => $method, 'value' => $value));
        Hook::exec('actionBeforeAjaxDie'.$controller.$method, array('value' => $value));

        die($value);
    }
}
