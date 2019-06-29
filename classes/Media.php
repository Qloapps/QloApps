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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2017 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class MediaCore
{
    public static $jquery_ui_dependencies = array(
        'ui.core' => array('fileName' => 'jquery.ui.core.min.js', 'dependencies' => array(), 'theme' => true),
        'ui.widget' => array('fileName' => 'jquery.ui.widget.min.js', 'dependencies' => array(), 'theme' => false),
        'ui.mouse' => array('fileName' => 'jquery.ui.mouse.min.js', 'dependencies' => array('ui.core', 'ui.widget'), 'theme' => false),
        'ui.position' => array('fileName' => 'jquery.ui.position.min.js', 'dependencies' => array(), 'theme' => false),
        'ui.draggable' => array('fileName' => 'jquery.ui.draggable.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.mouse'), 'theme' => false),
        'ui.droppable' => array('fileName' => 'jquery.ui.droppable.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.mouse', 'ui.draggable'), 'theme' => false),
        'ui.resizable' => array('fileName' => 'jquery.ui.resizable.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.mouse'), 'theme' => true),
        'ui.selectable' => array('fileName' => 'jquery.ui.selectable.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.mouse'), 'theme' => true),
        'ui.sortable' => array('fileName' => 'jquery.ui.sortable.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.mouse'), 'theme' => true),
        'ui.autocomplete' => array('fileName' => 'jquery.ui.autocomplete.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.position', 'ui.menu'), 'theme' => true),
        'ui.button' => array('fileName' => 'jquery.ui.button.min.js', 'dependencies' => array('ui.core', 'ui.widget'), 'theme' => true),
        'ui.dialog' => array('fileName' => 'jquery.ui.dialog.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.position','ui.button'), 'theme' => true),
        'ui.menu' => array('fileName' => 'jquery.ui.menu.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.position'), 'theme' => true),
        'ui.slider' => array('fileName' => 'jquery.ui.slider.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.mouse'), 'theme' => true),
        'ui.spinner' => array('fileName' => 'jquery.ui.spinner.min.js', 'dependencies' => array('ui.core', 'ui.widget', 'ui.button'), 'theme' => true),
        'ui.tabs' => array('fileName' => 'jquery.ui.tabs.min.js', 'dependencies' => array('ui.core', 'ui.widget'), 'theme' => true),
        'ui.datepicker' => array('fileName' => 'jquery.ui.datepicker.min.js', 'dependencies' => array('ui.core'), 'theme' => true),
        'ui.progressbar' => array('fileName' => 'jquery.ui.progressbar.min.js', 'dependencies' => array('ui.core', 'ui.widget'), 'theme' => true),
        'ui.tooltip' => array('fileName' => 'jquery.ui.tooltip.min.js', 'dependencies' => array('ui.core', 'ui.widget','ui.position','effects.core'), 'theme' => true),
        'ui.accordion' => array('fileName' => 'jquery.ui.accordion.min.js', 'dependencies' => array('ui.core', 'ui.widget','effects.core'), 'theme' => true),
        'effects.core' => array('fileName' => 'jquery.effects.core.min.js', 'dependencies' => array(), 'theme' => false),
        'effects.blind' => array('fileName' => 'jquery.effects.blind.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.bounce' => array('fileName' => 'jquery.effects.bounce.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.clip' => array('fileName' => 'jquery.effects.clip.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.drop' => array('fileName' => 'jquery.effects.drop.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.explode' => array('fileName' => 'jquery.effects.explode.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.fade' => array('fileName' => 'jquery.effects.fade.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.fold' => array('fileName' => 'jquery.effects.fold.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.highlight' => array('fileName' => 'jquery.effects.highlight.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.pulsate' => array('fileName' => 'jquery.effects.pulsate.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.scale' => array('fileName' => 'jquery.effects.scale.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.shake' => array('fileName' => 'jquery.effects.shake.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.slide' => array('fileName' => 'jquery.effects.slide.min.js', 'dependencies' => array('effects.core'), 'theme' => false),
        'effects.transfer' => array('fileName' => 'jquery.effects.transfer.min.js', 'dependencies' => array('effects.core'), 'theme' => false)
    );

    /**
     * @var array list of javascript definitions
     */
    protected static $js_def = array();

    /**
     * @var array list of javascript inline scripts
     */
    protected static $inline_script = array();

    /**
     * @var array list of javascript external scripts
     */
    protected static $inline_script_src = array();

    /**
     * @var string pattern used in replaceByAbsoluteURL
     */
    public static $pattern_callback = '#(url\((?![\'"]?(?:data:|//|https?:))(?:\'|")?)([^\)\'"]*)(?=[\'"]?\))#s';

    /**
     * @var string used for preg_replace_callback parameter (avoid global)
     */
    protected static $current_css_file;

    /**
     * @var string pattern used in packJSinHTML
     */
    public static $pattern_js = '/(<\s*script(?:\s+[^>]*(?:javascript|src)[^>]*)?\s*>)(.*)(<\s*\/script\s*[^>]*>)/Uims';

    protected static $pattern_keepinline = 'data-keepinline';

    public static function minifyHTML($html_content)
    {
        if (strlen($html_content) > 0) {
            //set an alphabetical order for args
            // $html_content = preg_replace_callback(
            // '/(<[a-zA-Z0-9]+)((\s*[a-zA-Z0-9]+=[\"\\\'][^\"\\\']*[\"\\\']\s*)*)>/',
            // array('Media', 'minifyHTMLpregCallback'),
            // $html_content,
            // Media::getBackTrackLimit());

            require_once(_PS_TOOL_DIR_.'minify_html/minify_html.class.php');
            $html_content = str_replace(chr(194).chr(160), '&nbsp;', $html_content);
            if (trim($minified_content = Minify_HTML::minify($html_content, array('cssMinifier', 'jsMinifier'))) != '') {
                $html_content = $minified_content;
            }

            return $html_content;
        }
        return false;
    }

    public static function minifyHTMLpregCallback($preg_matches)
    {
        $args = array();
        preg_match_all('/[a-zA-Z0-9]+=[\"\\\'][^\"\\\']*[\"\\\']/is', $preg_matches[2], $args);
        $args = $args[0];
        sort($args);
        // if there is no args in the balise, we don't write a space (avoid previous : <title >, now : <title>)
        if (empty($args)) {
            $output = $preg_matches[1].'>';
        } else {
            $output = $preg_matches[1].' '.implode(' ', $args).'>';
        }
        return $output;
    }

    public static function packJSinHTML($html_content)
    {
        if (strlen($html_content) > 0) {
            $html_content_copy = $html_content;
            if (!preg_match('/'.Media::$pattern_keepinline.'/', $html_content)) {
                    $html_content = preg_replace_callback(
                    Media::$pattern_js,
                    array('Media', 'packJSinHTMLpregCallback'),
                    $html_content,
                    Media::getBackTrackLimit());

                // If the string is too big preg_replace return an error
                // In this case, we don't compress the content
                if (function_exists('preg_last_error') && preg_last_error() == PREG_BACKTRACK_LIMIT_ERROR) {
                    if (_PS_MODE_DEV_) {
                        Tools::error_log('ERROR: PREG_BACKTRACK_LIMIT_ERROR in function packJSinHTML');
                    }
                    return $html_content_copy;
                }
            }
            return $html_content;
        }
        return false;
    }

    public static function packJSinHTMLpregCallback($preg_matches)
    {
        if (!(trim($preg_matches[2]))) {
            return $preg_matches[0];
        }
        $preg_matches[1] = $preg_matches[1].'/* <![CDATA[ */';
        $preg_matches[2] = Media::packJS($preg_matches[2]);
        $preg_matches[count($preg_matches) - 1] = '/* ]]> */'.$preg_matches[count($preg_matches) - 1];
        unset($preg_matches[0]);
        $output = implode('', $preg_matches);
        return $output;
    }

    public static function packJS($js_content)
    {
        if (!empty($js_content)) {
            require_once(_PS_TOOL_DIR_.'js_minify/jsmin.php');
            try {
                $js_content = JSMin::minify($js_content);
            } catch (Exception $e) {
                if (_PS_MODE_DEV_) {
                    echo $e->getMessage();
                }
                return ';'.trim($js_content, ';').';';
            }
        }
        return ';'.trim($js_content, ';').';';
    }

    public static function minifyCSS($css_content, $fileuri = false, &$import_url = array())
    {
        Media::$current_css_file = $fileuri;

        if (strlen($css_content) > 0) {
            $limit  = Media::getBackTrackLimit();
            $css_content = preg_replace('#/\*.*?\*/#s', '', $css_content, $limit);
            $css_content = preg_replace_callback(Media::$pattern_callback, array('Media', 'replaceByAbsoluteURL'), $css_content, $limit);
            $css_content = preg_replace('#\s+#', ' ', $css_content, $limit);
            $css_content = str_replace(array("\t", "\n", "\r"), '', $css_content);
            $css_content = str_replace(array('; ', ': '), array(';', ':'), $css_content);
            $css_content = str_replace(array(' {', '{ '), '{', $css_content);
            $css_content = str_replace(', ', ',', $css_content);
            $css_content = str_replace(array('} ', ' }', ';}'), '}', $css_content);
            $css_content = str_replace(array(':0px', ':0em', ':0pt', ':0%'), ':0', $css_content);
            $css_content = str_replace(array(' 0px', ' 0em', ' 0pt', ' 0%'), ' 0', $css_content);
            $css_content = str_replace('\'images_ie/', '\'images/', $css_content);
            $css_content = preg_replace_callback('#(AlphaImageLoader\(src=\')([^\']*\',)#s', array('Tools', 'replaceByAbsoluteURL'), $css_content);
            // Store all import url
            preg_match_all('#@(import|charset) .*?;#i', $css_content, $m);
            for ($i = 0, $total = count($m[0]); $i < $total; $i++) {
                if (isset($m[1][$i]) && $m[1][$i] == 'import') {
                    $import_url[] = $m[0][$i];
                }
                $css_content = str_replace($m[0][$i], '', $css_content);
            }

            return trim($css_content);
        }
        return false;
    }

    public static function replaceByAbsoluteURL($matches)
    {
        if (array_key_exists(1, $matches) && array_key_exists(2, $matches)) {
            if (!preg_match('/^(?:https?:)?\/\//iUs', $matches[2])) {
                $protocol_link = Tools::getCurrentUrlProtocolPrefix();
                $sep = '/';
                $tmp = $matches[2][0] == $sep ? $matches[2] : dirname(Media::$current_css_file).$sep.ltrim($matches[2], $sep);
                $server = Tools::getMediaServer($tmp);

                return $matches[1].$protocol_link.$server.$tmp;
            } else
                return $matches[0];
        }
        return false;
    }

    /**
     * addJS return javascript path
     *
     * @param mixed $js_uri
     *
     * @return string
     */
    public static function getJSPath($js_uri)
    {
        return Media::getMediaPath($js_uri);
    }

    /**
     * addCSS return stylesheet path.
     *
     * @param mixed $css_uri
     * @param string $css_media_type
     * @param bool $need_rtl
     *
     * @return string
     */
    public static function getCSSPath($css_uri, $css_media_type = 'all', $need_rtl = true)
    {
        // RTL Ready: search and load rtl css file if it's not originally rtl
        if ($need_rtl && Context::getContext()->language->is_rtl) {
            $css_uri_rtl = preg_replace('/(^[^.].*)(\.css)$/', '$1_rtl.css', $css_uri);
            $rtl_media = Media::getMediaPath($css_uri_rtl, $css_media_type);
            if ($rtl_media != false) {
                return $rtl_media;
            }
        }
        // End RTL
        return Media::getMediaPath($css_uri, $css_media_type);
    }

    public static function getMediaPath($media_uri, $css_media_type = null)
    {
        if (is_array($media_uri) || $media_uri === null || empty($media_uri)) {
            return false;
        }

        $url_data = parse_url($media_uri);
        if (!is_array($url_data)) {
            return false;
        }

        if (!array_key_exists('host', $url_data)) {
            $media_uri_host_mode = '/'.ltrim(str_replace(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, _PS_CORE_DIR_), __PS_BASE_URI__, $media_uri), '/\\');
            $media_uri = '/'.ltrim(str_replace(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, _PS_ROOT_DIR_), __PS_BASE_URI__, $media_uri), '/\\');
            // remove PS_BASE_URI on _PS_ROOT_DIR_ for the following
            $file_uri = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $media_uri);
            $file_uri_host_mode = _PS_CORE_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, Tools::str_replace_once(_PS_CORE_DIR_, '', $media_uri));

            if (!@filemtime($file_uri) || @filesize($file_uri) === 0) {
                if (!defined('_PS_HOST_MODE_')) {
                    return false;
                } elseif (!@filemtime($file_uri_host_mode) || @filesize($file_uri_host_mode) === 0) {
                    return false;
                } else {
                    $media_uri = $media_uri_host_mode;
                }
            }

            $media_uri = str_replace('//', '/', $media_uri);
        }

        if ($css_media_type) {
            return array($media_uri => $css_media_type);
        }

        return $media_uri;
    }

    /**
     * return jquery path.
     *
     * @param mixed $version
     *
     * @return string
     */
    public static function getJqueryPath($version = null, $folder = null, $minifier = true)
    {
        $add_no_conflict = false;
        if ($version === null) {
            $version = _PS_JQUERY_VERSION_;
        } //set default version
        elseif (preg_match('/^([0-9\.]+)$/Ui', $version)) {
            $add_no_conflict = true;
        } else {
            return false;
        }

        if ($folder === null) {
            $folder = _PS_JS_DIR_.'jquery/';
        } //set default folder
        //check if file exist
        $file = $folder.'jquery-'.$version.($minifier ? '.min.js' : '.js');

        // remove PS_BASE_URI on _PS_ROOT_DIR_ for the following
        $url_data = parse_url($file);
        $file_uri = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $url_data['path']);
        $file_uri_host_mode = _PS_CORE_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $url_data['path']);
        // check if js files exists, if not try to load query from ajax.googleapis.com

        $return = array();

        if (@filemtime($file_uri) || (defined('_PS_HOST_MODE_') && @filemtime($file_uri_host_mode))) {
            $return[] = Media::getJSPath($file);
        } else {
            $return[] = Media::getJSPath(Tools::getCurrentUrlProtocolPrefix().'ajax.googleapis.com/ajax/libs/jquery/'
                .$version.'/jquery'.($minifier ? '.min.js' : '.js'));
        }

        if ($add_no_conflict) {
            $return[] = Media::getJSPath(Context::getContext()->shop->getBaseURL(true, false)._PS_JS_DIR_
                .'jquery/jquery.noConflict.php?version='.$version);
        }

        //added query migrate for compatibility with new version of jquery will be removed in ps 1.6
        $return[] = Media::getJSPath(_PS_JS_DIR_.'jquery/jquery-migrate-1.2.1.min.js');

        return $return;
    }

    /**
     * return jqueryUI component path.
     *
     * @param mixed $component
     *
     * @return string
     */
    public static function getJqueryUIPath($component, $theme, $check_dependencies)
    {
        $ui_path = array('js' => array(), 'css' => array());
        $folder = _PS_JS_DIR_.'jquery/ui/';
        $file = 'jquery.'.$component.'.min.js';
        $url_data = parse_url($folder.$file);
        $file_uri = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $url_data['path']);
        $file_uri_host_mode = _PS_CORE_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $url_data['path']);
        $ui_tmp = array();
        if (isset(Media::$jquery_ui_dependencies[$component]) && Media::$jquery_ui_dependencies[$component]['theme'] && $check_dependencies) {
            $theme_css = Media::getCSSPath($folder.'themes/'.$theme.'/jquery.ui.theme.css');
            $comp_css = Media::getCSSPath($folder.'themes/'.$theme.'/jquery.'.$component.'.css');
            if (!empty($theme_css) || $theme_css) {
                $ui_path['css'] = array_merge($ui_path['css'], $theme_css);
            }
            if (!empty($comp_css) || $comp_css) {
                $ui_path['css'] = array_merge($ui_path['css'], $comp_css);
            }
        }
        if ($check_dependencies && array_key_exists($component, self::$jquery_ui_dependencies)) {
            foreach (self::$jquery_ui_dependencies[$component]['dependencies'] as $dependency) {
                $ui_tmp[] = Media::getJqueryUIPath($dependency, $theme, false);
                if (self::$jquery_ui_dependencies[$dependency]['theme']) {
                    $dep_css = Media::getCSSPath($folder.'themes/'.$theme.'/jquery.'.$dependency.'.css');
                }


                if (isset($dep_css) && (!empty($dep_css) || $dep_css)) {
                    $ui_path['css'] = array_merge($ui_path['css'], $dep_css);
                }
            }
        }
        if (@filemtime($file_uri) || (defined('_PS_HOST_MODE_') && @filemtime($file_uri_host_mode))) {
            if (!empty($ui_tmp)) {
                foreach ($ui_tmp as $ui) {
                    if (!empty($ui['js'])) {
                        $ui_path['js'][] = $ui['js'];
                    }

                    if (!empty($ui['css'])) {
                        $ui_path['css'][] = $ui['css'];
                    }
                }
                $ui_path['js'][] = Media::getJSPath($folder.$file);
            } else {
                $ui_path['js'] = Media::getJSPath($folder.$file);
            }
        }

        //add i18n file for datepicker
        if ($component == 'ui.datepicker') {
            if (!is_array($ui_path['js'])) {
                $ui_path['js'] = array($ui_path['js']);
            }

            $ui_path['js'][] = Media::getJSPath($folder.'i18n/jquery.ui.datepicker-'.Context::getContext()->language->iso_code.'.js');
        }

        return $ui_path;
    }

    /**
     * return jquery plugin path.
     *
     * @param mixed $name
     *
     * @return string|boolean
     */
    public static function getJqueryPluginPath($name, $folder = null)
    {
        $plugin_path = array('js' => array(), 'css' => array());
        if ($folder === null) {
            $folder = _PS_JS_DIR_.'jquery/plugins/';
        } //set default folder

        $file = 'jquery.'.$name.'.js';
        $url_data = parse_url($folder);
        $file_uri = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $url_data['path']);
        $file_uri_host_mode = _PS_CORE_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $url_data['path']);

        if (@file_exists($file_uri.$file) || (defined('_PS_HOST_MODE_') && @file_exists($file_uri_host_mode.$file))) {
            $plugin_path['js'] = Media::getJSPath($folder.$file);
        } elseif (@file_exists($file_uri.$name.'/'.$file) || (defined('_PS_HOST_MODE_') && @file_exists($file_uri_host_mode.$name.'/'.$file))) {
            $plugin_path['js'] = Media::getJSPath($folder.$name.'/'.$file);
        } else {
            return false;
        }
        $plugin_path['css'] = Media::getJqueryPluginCSSPath($name, $folder);

        return $plugin_path;
    }

    /**
     * return jquery plugin css path if exist.
     *
     * @param mixed $name
     *
     * @return string|boolean
     */
    public static function getJqueryPluginCSSPath($name, $folder = null)
    {
        if ($folder === null) {
            $folder = _PS_JS_DIR_.'jquery/plugins/';
        } //set default folder
        $file = 'jquery.'.$name.'.css';
        $url_data = parse_url($folder);
        $file_uri = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $url_data['path']);
        $file_uri_host_mode = _PS_CORE_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $url_data['path']);

        if (@file_exists($file_uri.$file) || (defined('_PS_HOST_MODE_') && @file_exists($file_uri_host_mode.$file))) {
            return Media::getCSSPath($folder.$file);
        } elseif (@file_exists($file_uri.$name.'/'.$file) || (defined('_PS_HOST_MODE_') && @file_exists($file_uri_host_mode.$name.'/'.$file))) {
            return Media::getCSSPath($folder.$name.'/'.$file);
        } else {
            return false;
        }
    }

    /**
     * Combine Compress and Cache CSS (ccc) calls
     *
     * @param array $css_files
     * @param array $cache_path
     *
     * @return array processed css_files
     */
    public static function cccCss($css_files, $cache_path = null)
    {
        //inits
        $css_files_by_media = array();
        $external_css_files = array();
        $compressed_css_files = array();
        $compressed_css_files_not_found = array();
        $compressed_css_files_infos = array();
        $protocol_link = Tools::getCurrentUrlProtocolPrefix();
        //if cache_path not specified, set curent theme cache folder
        $cache_path = $cache_path ? $cache_path : _PS_THEME_DIR_.'cache/';
        $css_split_need_refresh = false;

        // group css files by media
        foreach ($css_files as $filename => $media) {
            if (!array_key_exists($media, $css_files_by_media)) {
                $css_files_by_media[$media] = array();
            }

            $infos = array();
            $infos['uri'] = $filename;
            $url_data = parse_url($filename);

            if (array_key_exists('host', $url_data)) {
                $external_css_files[$filename] = $media;
                continue;
            }

            $infos['path'] = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, '/', $url_data['path']);

            if (!@filemtime($infos['path'])) {
                $infos['path'] = _PS_CORE_DIR_.Tools::str_replace_once(__PS_BASE_URI__, '/', $url_data['path']);
            }

            $css_files_by_media[$media]['files'][] = $infos;
            if (!array_key_exists('date', $css_files_by_media[$media])) {
                $css_files_by_media[$media]['date'] = 0;
            }
            $css_files_by_media[$media]['date'] = max(
                (int)@filemtime($infos['path']),
                $css_files_by_media[$media]['date']
            );

            if (!array_key_exists($media, $compressed_css_files_infos)) {
                $compressed_css_files_infos[$media] = array('key' => '');
            }
            $compressed_css_files_infos[$media]['key'] .= $filename;
        }

        // get compressed css file infos
        $version = (int)Configuration::get('PS_CCCCSS_VERSION');
        foreach ($compressed_css_files_infos as $media => &$info) {
            $key = md5($info['key'].$protocol_link);
            $filename = $cache_path.'v_'.$version.'_'.$key.'_'.$media.'.css';

            $info = array(
                'key' => $key,
                'date' => (int)@filemtime($filename)
            );
        }

        foreach ($css_files_by_media as $media => $media_infos) {
            if ($media_infos['date'] > $compressed_css_files_infos[$media]['date']) {
                if ($compressed_css_files_infos[$media]['date']) {
                    Configuration::updateValue('PS_CCCCSS_VERSION', ++$version);
                    break;
                }
            }
        }

        // aggregate and compress css files content, write new caches files
        $import_url = array();
        foreach ($css_files_by_media as $media => $media_infos) {
            $cache_filename = $cache_path.'v_'.$version.'_'.$compressed_css_files_infos[$media]['key'].'_'.$media.'.css';
            if ($media_infos['date'] > $compressed_css_files_infos[$media]['date']) {
                $css_split_need_refresh = true;
                $cache_filename = $cache_path.'v_'.$version.'_'.$compressed_css_files_infos[$media]['key'].'_'.$media.'.css';
                $compressed_css_files[$media] = '';
                foreach ($media_infos['files'] as $file_infos) {
                    if (file_exists($file_infos['path'])) {
                        $compressed_css_files[$media] .= Media::minifyCSS(file_get_contents($file_infos['path']), $file_infos['uri'], $import_url);
                    } else {
                        $compressed_css_files_not_found[] = $file_infos['path'];
                    }
                }
                if (!empty($compressed_css_files_not_found)) {
                    $content = '/* WARNING ! file(s) not found : "'.
                        implode(',', $compressed_css_files_not_found).
                        '" */'."\n".$compressed_css_files[$media];
                } else {
                    $content = $compressed_css_files[$media];
                }

                $content = '@charset "UTF-8";'."\n".$content;
                $content = implode('', $import_url).$content;
                file_put_contents($cache_filename, $content);
                chmod($cache_filename, 0777);
            }
            $compressed_css_files[$media] = $cache_filename;
        }

        // rebuild the original css_files array
        $css_files = array();
        foreach ($compressed_css_files as $media => $filename) {
            $url = str_replace(_PS_THEME_DIR_, _THEMES_DIR_._THEME_NAME_.'/', $filename);
            $css_files[$protocol_link.Tools::getMediaServer($url).$url] = $media;
        }

        $compiled_css = array_merge($external_css_files, $css_files);

        //If browser not IE <= 9, bypass ieCssSplitter
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        if (!preg_match('/(?i)msie [1-9]/', $user_agent)) {
            return $compiled_css;
        }
        $splitted_css = self::ieCssSplitter($compiled_css, $cache_path.'ie9', $css_split_need_refresh);

        return array_merge($splitted_css, $compiled_css);
    }

    /**
     * Splits stylesheets that go beyond the IE limit of 4096 selectors
     *
     * @param array $compiled_css
     * @param string $cache_path
     * @param bool $refresh
     *
     * @return array processed css_files
     */
    public static function ieCssSplitter($compiled_css, $cache_path, $refresh = false)
    {
        $splitted_css = array();
        $protocol_link = Tools::getCurrentUrlProtocolPrefix();
        //return cached css
        if (!$refresh) {
            foreach (array_diff(scandir($cache_path), array('..', '.')) as $file) {
                $css_url = str_replace(_PS_ROOT_DIR_, '', $protocol_link.Tools::getMediaServer('').$cache_path.DIRECTORY_SEPARATOR.$file);
                $splitted_css[$css_url] = 'all';
            }
            return array('lteIE9' => $splitted_css);
        }
        if (!is_dir($cache_path)) {
            mkdir($cache_path, 0777, true);
        }
        require_once(_PS_ROOT_DIR_.'/tools/CssSplitter.php');
        $splitter = new CssSplitter();
        $css_rule_limit = 4095;
        foreach ($compiled_css as $css => $media) {
            $file_info = parse_url($css);
            $file_basename = basename($file_info['path']);
            $css_content = file_get_contents(_PS_ROOT_DIR_.$file_info['path']);
            $count = $splitter->countSelectors($css_content) - $css_rule_limit;
            if (($count / $css_rule_limit) > 0) {
                $part = 2;
                for ($i = $count; $i > 0; $i -= $css_rule_limit) {
                    $new_css_name = 'ie_split_'.$part.'_'.$file_basename;
                    $css_url = str_replace(_PS_ROOT_DIR_, '', $protocol_link.Tools::getMediaServer('').$cache_path.DIRECTORY_SEPARATOR.$new_css_name);
                    $splitted_css[$css_url] = $media;
                    file_put_contents($cache_path.DIRECTORY_SEPARATOR.$new_css_name, $splitter->split($css_content, $part));
                    chmod($cache_path.DIRECTORY_SEPARATOR.$new_css_name, 0777);
                    $part++;
                }
            }
        }
        if (count($splitted_css) > 0) {
            return array('lteIE9' => $splitted_css);
        }
        return array('lteIE9' => array());
    }

    public static function getBackTrackLimit()
    {
        static $limit = null;
        if ($limit === null) {
            $limit = @ini_get('pcre.backtrack_limit');
            if (!$limit) {
                $limit = -1;
            }
        }

        return $limit;
    }

    /**
     * Combine Compress and Cache (ccc) JS calls
     *
     * @param array $js_files
     *
     * @return array processed js_files
     */
    public static function cccJS($js_files)
    {
        //inits
        $compressed_js_files_not_found = array();
        $js_files_infos = array();
        $js_files_date = 0;
        $compressed_js_filename = '';
        $js_external_files = array();
        $protocol_link = Tools::getCurrentUrlProtocolPrefix();
        $cache_path = _PS_THEME_DIR_.'cache/';

        // get js files infos
        foreach ($js_files as $filename) {
            if (Validate::isAbsoluteUrl($filename)) {
                $js_external_files[] = $filename;
            } else {
                $infos = array();
                $infos['uri'] = $filename;
                $url_data = parse_url($filename);
                $infos['path'] = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, '/', $url_data['path']);

                if (!@filemtime($infos['path'])) {
                    $infos['path'] = _PS_CORE_DIR_.Tools::str_replace_once(__PS_BASE_URI__, '/', $url_data['path']);
                }

                $js_files_infos[] = $infos;

                $js_files_date = max(
                    (int)@filemtime($infos['path']),
                    $js_files_date
                );
                $compressed_js_filename .= $filename;
            }
        }

        // get compressed js file infos
        $compressed_js_filename = md5($compressed_js_filename);
        $version = (int)Configuration::get('PS_CCCJS_VERSION');
        $compressed_js_path = $cache_path.'v_'.$version.'_'.$compressed_js_filename.'.js';
        $compressed_js_file_date = (int)@filemtime($compressed_js_path);

        // aggregate and compress js files content, write new caches files
        if ($js_files_date > $compressed_js_file_date) {
            if ($compressed_js_file_date) {
                Configuration::updateValue('PS_CCCJS_VERSION', ++$version);
            }

            $compressed_js_path = $cache_path.'v_'.$version.'_'.$compressed_js_filename.'.js';
            $content = '';
            foreach ($js_files_infos as $file_infos) {
                if (file_exists($file_infos['path'])) {
                    $tmp_content = file_get_contents($file_infos['path']);
                    if (preg_match('@\.(min|pack)\.[^/]+$@', $file_infos['path'], $matches)) {
                        $content .= preg_replace('/\/\/@\ssourceMappingURL\=[_a-zA-Z0-9-.]+\.'.$matches[1].'\.map\s+/', '', $tmp_content);
                    } else {
                        $content .= Media::packJS($tmp_content);
                    }
                } else {
                    $compressed_js_files_not_found[] = $file_infos['path'];
                }
            }

            if (!empty($compressed_js_files_not_found)) {
                $content = '/* WARNING ! file(s) not found : "'.
                    implode(',', $compressed_js_files_not_found).
                    '" */'."\n".$content;
            }

            file_put_contents($compressed_js_path, $content);
            chmod($compressed_js_path, 0777);
        }

        // rebuild the original js_files array
        if (strpos($compressed_js_path, _PS_ROOT_DIR_) !== false) {
            $url = str_replace(_PS_ROOT_DIR_.'/', __PS_BASE_URI__, $compressed_js_path);
        }

        if (strpos($compressed_js_path, _PS_CORE_DIR_) !== false) {
            $url = str_replace(_PS_CORE_DIR_.'/', __PS_BASE_URI__, $compressed_js_path);
        }

        return array_merge(array($protocol_link.Tools::getMediaServer($url).$url), $js_external_files);
    }

    /**
     * Clear theme cache
     *
     * @return void
     */
    public static function clearCache()
    {
        foreach (array(_PS_THEME_DIR_.'cache') as $dir) {
            if (file_exists($dir)) {
                foreach (array_diff(scandir($dir), array('..', '.', 'index.php')) as $file) {
                    Tools::deleteFile($dir.DIRECTORY_SEPARATOR.$file);
                }
            }
        }

        $version = (int)Configuration::get('PS_CCCJS_VERSION');
        Configuration::updateValue('PS_CCCJS_VERSION', ++$version);
        $version = (int)Configuration::get('PS_CCCCSS_VERSION');
        Configuration::updateValue('PS_CCCCSS_VERSION', ++$version);
    }

    /**
     * Get JS definitions
     *
     * @return array JS definitions
     */
    public static function getJsDef()
    {
        ksort(Media::$js_def);
        return Media::$js_def;
    }

    /**
     * Get JS inline script
     *
     * @return array inline script
     */
    public static function getInlineScript()
    {
        return Media::$inline_script;
    }

    /**
     * Add a new javascript definition at bottom of page
     *
     * @param mixed $js_def
     *
     * @return void
     */
    public static function addJsDef($js_def)
    {
        if (is_array($js_def)) {
            foreach ($js_def as $key => $js) {
                Media::$js_def[$key] = $js;
            }
        } elseif ($js_def) {
            Media::$js_def[] = $js_def;
        }
    }

    /**
     * Add a new javascript definition from a capture at bottom of page
     *
     * @param mixed $params
     * @param string $content
     * @param Smarty $smarty
     * @param bool $repeat
     *
     * @return void
     */
    public static function addJsDefL($params, $content, $smarty = null, &$repeat = false)
    {
        if (!$repeat && isset($params) && Tools::strlen($content)) {
            if (!is_array($params)) {
                $params = (array)$params;
            }

            foreach ($params as $param) {
                Media::$js_def[$param] = $content;
            }
        }
    }

    public static function deferInlineScripts($output)
    {
        /* Try to enqueue in js_files inline scripts with src but without conditionnal comments */
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        @$dom->loadHTML(($output));
        libxml_use_internal_errors(false);
        $scripts = $dom->getElementsByTagName('script');
        if (is_object($scripts) && $scripts->length) {
            foreach ($scripts as $script) {
                /** @var DOMElement $script */
                if ($src = $script->getAttribute('src')) {
                    if (substr($src, 0, 2) == '//') {
                        $src = Tools::getCurrentUrlProtocolPrefix().substr($src, 2);
                    }

                    $patterns = array(
                        '#code\.jquery\.com/jquery-([0-9\.]+)(\.min)*\.js$#Ui',
                        '#ajax\.googleapis\.com/ajax/libs/jquery/([0-9\.]+)/jquery(\.min)*\.js$#Ui',
                        '#ajax\.aspnetcdn\.com/ajax/jquery/jquery-([0-9\.]+)(\.min)*\.js$#Ui',
                        '#cdnjs\.cloudflare\.com/ajax/libs/jquery/([0-9\.]+)/jquery(\.min)*\.js$#Ui',
                        '#/jquery-([0-9\.]+)(\.min)*\.js$#Ui'
                    );

                    foreach ($patterns as $pattern) {
                        $matches = array();
                        if (preg_match($pattern, $src, $matches)) {
                            $minifier = $version = false;
                            if (isset($matches[2]) && $matches[2]) {
                                $minifier = (bool)$matches[2];
                            }
                            if (isset($matches[1]) && $matches[1]) {
                                $version = $matches[1];
                            }
                            if ($version) {
                                if ($version != _PS_JQUERY_VERSION_) {
                                    Context::getContext()->controller->addJquery($version, null, $minifier);
                                }
                                array_push(Media::$inline_script_src, $src);
                            }
                        }
                    }
                    if (!in_array($src, Media::$inline_script_src) && !$script->getAttribute(Media::$pattern_keepinline)) {
                        Context::getContext()->controller->addJS($src);
                    }
                }
            }
        }
        $output = preg_replace_callback(Media::$pattern_js, array('Media', 'deferScript'), $output);
        return $output;
    }

    /**
     * Get all JS scripts and place it to bottom
     * To be used in callback with deferInlineScripts
     *
     * @param array $matches
     *
     * @return bool|string Empty string or original script lines
     */
    public static function deferScript($matches)
    {
        if (!is_array($matches)) {
            return false;
        }
        $inline = '';

        if (isset($matches[0])) {
            $original = trim($matches[0]);
        }

        if (isset($matches[2])) {
            $inline = trim($matches[2]);
        }

        /* This is an inline script, add its content to inline scripts stack then remove it from content */
        if (!empty($inline) && preg_match(Media::$pattern_js, $original) !== false && !preg_match('/'.Media::$pattern_keepinline.'/', $original) && Media::$inline_script[] = $inline) {
            return '';
        }
        /* This is an external script, if it already belongs to js_files then remove it from content */
        preg_match('/src\s*=\s*["\']?([^"\']*)[^>]/ims', $original, $results);
        if (array_key_exists(1, $results)) {
            if (substr($results[1], 0, 2) == '//') {
                $protocol_link = Tools::getCurrentUrlProtocolPrefix();
                $results[1] = $protocol_link.ltrim($results[1], '/');
            }

            if (in_array($results[1], Context::getContext()->controller->js_files) || in_array($results[1], Media::$inline_script_src)) {
                return '';
            }
        }

        /* return original string because no match was found */
        return "\n".$original;
    }
}
