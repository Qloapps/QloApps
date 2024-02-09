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
 * @property Meta $object
 */
class AdminMetaControllerCore extends AdminController
{
    public $table = 'meta';
    public $className = 'Meta';
    public $lang = true;

    /** @var ShopUrl */
    protected $url = false;
    protected $toolbar_scroll = false;

    public function __construct()
    {
        $this->table = 'meta';
        $this->className = 'Meta';

        $this->bootstrap = true;
        $this->identifier_name = 'page';
        $this->ht_file = _PS_ROOT_DIR_.'/.htaccess';
        $this->rb_file = _PS_ROOT_DIR_.'/robots.txt';
        $this->rb_data = $this->getRobotsContent();

        $this->explicitSelect = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->fields_list = array(
            'id_meta' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'page' => array('title' => $this->l('Page')),
            'title' => array('title' => $this->l('Page title')),
            'url_rewrite' => array('title' => $this->l('Friendly URL'))
        );
        $this->_where = ' AND a.configurable = 1';
        $this->_group = 'GROUP BY a.id_meta';

        parent::__construct();

        $this->sm_file = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.$this->context->shop->id.'_index_sitemap.xml';
        // Options to generate friendly urls
        $mod_rewrite = Tools::modRewriteActive();
        $general_fields = array(
            'PS_REWRITING_SETTINGS' => array(
                'title' => $this->l('Friendly URL'),
                'hint' => ($mod_rewrite ? $this->l('Enable this option only if your server allows URL rewriting (recommended).') : ''),
                'validation' => 'isBool',
                'cast' => 'intval',
                'type' => 'bool',
                'desc' => (!$mod_rewrite ? $this->l('URL rewriting (mod_rewrite) is not active on your server, or it is not possible to check your server configuration. If you want to use Friendly URLs, you must activate this mod.') : '')
            ),
            'PS_ALLOW_ACCENTED_CHARS_URL' => array(
                'title' => $this->l('Accented URL'),
                'hint' => $this->l('Enable this option if you want to allow accented characters in your friendly URLs.').' '.$this->l('You should only activate this option if you are using non-latin characters ; for all the latin charsets, your SEO will be better without this option.'),
                'validation' => 'isBool',
                'cast' => 'intval',
                'type' => 'bool'
            ),
            'PS_CANONICAL_REDIRECT' => array(
                'title' => $this->l('Redirect to the canonical URL'),
                'validation' => 'isUnsignedInt',
                'cast' => 'intval',
                'type' => 'select',
                'list' => array(
                    array('value' => 0, 'name' => $this->l('No redirection (you may have duplicate content issues)')),
                    array('value' => 1, 'name' => $this->l('302 Moved Temporarily (recommended while setting up your store)')),
                    array('value' => 2, 'name' => $this->l('301 Moved Permanently (recommended once you have gone live)'))
                ),
                'identifier' => 'value',
            ),
        );

        $url_description = '';
        if (!defined('_PS_HOST_MODE_')) {
            if ($this->checkConfiguration($this->ht_file)) {
                $general_fields['PS_HTACCESS_DISABLE_MULTIVIEWS'] = array(
                    'title' => $this->l('Disable Apache\'s MultiViews option'),
                    'hint' => $this->l('Enable this option only if you have problems with URL rewriting.'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                );

                $general_fields['PS_HTACCESS_DISABLE_MODSEC'] = array(
                    'title' => $this->l('Disable Apache\'s mod_security module'),
                    'hint' => $this->l('Some of QloApps\'s features might not work correctly with a specific configuration of Apache\'s mod_security module. We recommend to turn it off.'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                );
            } else {
                $url_description = $this->l('Before you can use this tool, you need to:');
                $url_description .= $this->l('1) Create a blank .htaccess file in your root directory.');
                $url_description .= $this->l('2) Give it write permissions (CHMOD 666 on Unix system).');
            }
        }

        // Options to generate robot.txt
        $robots_description = $this->l('Your robots.txt file MUST be in your website\'s root directory and nowhere else (e.g. http://www.example.com/robots.txt).');
        if ($this->checkConfiguration($this->rb_file)) {
            $robots_description .= $this->l('Generate your "robots.txt" file by clicking on the following button (this will erase the old robots.txt file)');
            $robots_submit = array('name' => 'submitRobots', 'title' => $this->l('Generate robots.txt file'));
        } else {
            $robots_description .= $this->l('Before you can use this tool, you need to:');
            $robots_description .= $this->l('1) Create a blank robots.txt file in your root directory.');
            $robots_description .= $this->l('2) Give it write permissions (CHMOD 666 on Unix system).');
        }

        $robots_options = array(
            'title' => $this->l('Robots file generation'),
            'description' => $robots_description,
        );

        if (isset($robots_submit)) {
            $robots_options['submit'] = $robots_submit;
        }

        if (!defined('_PS_HOST_MODE_')) {
            // Options for shop URL if multishop is disabled
            $shop_url_options = array(
                'title' => $this->l('Set shop URL'),
                'fields' => array(),
            );

            if (!Shop::isFeatureActive()) {
                $this->url = ShopUrl::getShopUrls($this->context->shop->id)->where('main', '=', 1)->getFirst();
                if ($this->url) {
                    $shop_url_options['description'] = $this->l('Here you can set the URL for your shop. If you migrate your shop to a new URL, remember to change the values below.');
                    $shop_url_options['fields'] = array(
                        'domain' => array(
                            'title' =>    $this->l('Shop domain'),
                            'validation' => 'isString',
                            'type' => 'text',
                            'defaultValue' => $this->url->domain,
                        ),
                        'domain_ssl' => array(
                            'title' =>    $this->l('SSL domain'),
                            'validation' => 'isString',
                            'type' => 'text',
                            'defaultValue' => $this->url->domain_ssl,
                        ),
                        'uri' => array(
                            'title' =>    $this->l('Base URI'),
                            'validation' => 'isString',
                            'type' => 'text',
                            'defaultValue' => $this->url->physical_uri,
                        )
                    );
                    $shop_url_options['submit'] = array('title' => $this->l('Save'));
                }
            } else {
                $shop_url_options['description'] = $this->l('The multistore option is enabled. If you want to change the URL of your shop, you must go to the "Multistore" page under the "Advanced Parameters" menu.');
            }
        }

        // List of options
        $this->fields_options = array(
            'general' => array(
                'title' =>    $this->l('Set up URLs'),
                'description' => $url_description,
                'fields' =>    $general_fields,
                'submit' => array('title' => $this->l('Save'))
            )
        );

        if (!defined('_PS_HOST_MODE_')) {
            $this->fields_options['shop_url'] = $shop_url_options;
        }
        // else {
        //     $this->fields_options['manage_domain_name'] = array(
        //         'title' => $this->l('Manage domain name'),
        //         'description' => $this->l('You can search for a new domain name or add a domain name that you already own. You will be redirected to your PrestaShop account.'),
        //         'buttons' => array(
        //             array(
        //                 'title' => $this->l('Add a domain name'),
        //                 'href' => 'https://www.prestashop.com/cloud/',
        //                 'class' => 'pull-right', 'icon' => 'process-icon-new',
        //                 'js' => 'return !window.open(this.href);'
        //             )
        //         )
        //     );
        // }

        // Add display route options to options form
        if (Configuration::get('PS_REWRITING_SETTINGS') || Tools::getValue('PS_REWRITING_SETTINGS')) {
            if (Configuration::get('PS_REWRITING_SETTINGS')) {
                $this->addAllRouteFields();
            }
            $this->fields_options['routes']['title'] = $this->l('Schema of URLs');
            $this->fields_options['routes']['description'] = $this->l('This section enables you to change the default pattern of your links. In order to use this functionality, QloApps\'s "Friendly URL" option must be enabled, and Apache\'s URL rewriting module (mod_rewrite) must be activated on your web server.').'<br />'.$this->l('There are several available keywords for each route listed below; note that keywords with * are required!').'<br />'.$this->l('To add a keyword in your URL, use the {keyword} syntax. If the keyword is not empty, you can add text before or after the keyword with syntax {prepend:keyword:append}. For example {-hey-:meta_title} will add "-hey-my-title" in the URL if the meta title is set.');
            $this->fields_options['routes']['submit'] = array('title' => $this->l('Save'));
        }

        $this->fields_options['robots'] = $robots_options;
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_meta'] = array(
                'href' => self::$currentIndex.'&addmeta&token='.$this->token,
                'desc' => $this->l('Add a new page', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function initProcess()
    {
        parent::initProcess();
        // This is a composite page, we don't want the "options" display mode
        if ($this->display == 'options') {
            $this->display = '';
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryUi('ui.widget');
        $this->addJqueryPlugin('tagify');
    }

    public function addFieldRoute($route_id, $title)
    {
        $keywords = array();
        foreach (Dispatcher::getInstance()->default_routes[$route_id]['keywords'] as $keyword => $data) {
            $keywords[] = ((isset($data['param'])) ? '<span class="red">'.$keyword.'*</span>' : $keyword);
        }

        $this->fields_options['routes']['fields']['PS_ROUTE_'.$route_id] = array(
            'title' =>    $title,
            'desc' => sprintf($this->l('Keywords: %s'), implode(', ', $keywords)),
            'validation' => 'isString',
            'type' => 'text',
            'size' => 70,
            'defaultValue' => Dispatcher::getInstance()->default_routes[$route_id]['rule'],
        );
    }

    public function renderForm()
    {
        $files = Meta::getPages(true, ($this->object->page ? $this->object->page : false));

        $is_index = false;
        if (is_object($this->object) && is_array($this->object->url_rewrite) && count($this->object->url_rewrite)) {
            foreach ($this->object->url_rewrite as $rewrite) {
                if ($is_index != true) {
                    $is_index = ($this->object->page == 'index' && empty($rewrite)) ? true : false;
                }
            }
        }

        $pages = array(
            'common' => array(
                'name' => $this->l('Default pages'),
                'query' => array(),
            ),
            'module' => array(
                'name' => $this->l('Modules pages'),
                'query' => array(),
            ),
        );

        foreach ($files as $name => $file) {
            $k = (preg_match('#^module-#', $file)) ? 'module' : 'common';
            $pages[$k]['query'][] = array(
                'id' => $file,
                'page' => $name,
            );
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Meta tags'),
                'icon' => 'icon-tags'
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_meta',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Page'),
                    'name' => 'page',

                    'options' => array(
                        'optiongroup' => array(
                            'label' => 'name',
                            'query' => $pages,
                        ),
                        'options' => array(
                            'id' => 'id',
                            'name' => 'page',
                            'query' => 'query',
                        ),
                    ),
                    'hint' => $this->l('Name of the related page.'),
                    'required' => true,
                    'empty_message' => '<p>'.$this->l('There is no page available!').'</p>',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Page title'),
                    'name' => 'title',
                    'lang' => true,
                    'hint' => array(
                        $this->l('Title of this page.'),
                        $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta description'),
                    'name' => 'description',
                    'lang' => true,
                    'hint' => array(
                        $this->l('A short description of your shop.'),
                        $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                    )
                ),
                array(
                    'type' => 'tags',
                    'label' => $this->l('Meta keywords'),
                    'name' => 'keywords',
                    'lang' => true,
                    'hint' =>  array(
                        $this->l('List of keywords for search engines.'),
                        $this->l('To add tags, click in the field, write something, and then press the "Enter" key.'),
                        $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Rewritten URL'),
                    'name' => 'url_rewrite',
                    'lang' => true,
                    'required' => true,
                    'disabled' => (bool)$is_index,
                    'hint' => array(
                        $this->l('For instance, "contacts" for http://example.com/shop/contacts to redirect to http://example.com/shop/contact-form.php'),
                        $this->l('Only letters and hyphens are allowed.'),
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save')
            )
        );
        return parent::renderForm();
    }

    public function postProcess()
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_ && Tools::isSubmit('submitOptionsmeta')
            && (Tools::getValue('domain') != Configuration::get('PS_SHOP_DOMAIN') || Tools::getValue('domain_ssl') != Configuration::get('PS_SHOP_DOMAIN_SSL'))) {
            $this->errors[] = Tools::displayError('This functionality has been disabled.');
            return;
        }

        if (Tools::isSubmit('submitAddmeta')) {
            $default_language = Configuration::get('PS_LANG_DEFAULT');
            if (Tools::getValue('page') != 'index') {
                $defaultLangIsValidated = Validate::isLinkRewrite(Tools::getValue('url_rewrite_'.$default_language));
                $englishLangIsValidated = Validate::isLinkRewrite(Tools::getValue('url_rewrite_1'));
            } else {    // index.php can have empty rewrite rule
                $defaultLangIsValidated = !Tools::getValue('url_rewrite_'.$default_language) || Validate::isLinkRewrite(Tools::getValue('url_rewrite_'.$default_language));
                $englishLangIsValidated = !Tools::getValue('url_rewrite_1') || Validate::isLinkRewrite(Tools::getValue('url_rewrite_1'));
            }

            if (!$defaultLangIsValidated && !$englishLangIsValidated) {
                $this->errors[] = Tools::displayError('The URL rewrite field must be filled in either the default or English language.');
                return false;
            }

            foreach (Language::getIDs(false) as $id_lang) {
                $current = Tools::getValue('url_rewrite_'.$id_lang);
                if (strlen($current) == 0) {
                    // Prioritize default language first
                    if ($defaultLangIsValidated) {
                        $_POST['url_rewrite_'.$id_lang] = Tools::getValue('url_rewrite_'.$default_language);
                    } else {
                        $_POST['url_rewrite_'.$id_lang] = Tools::getValue('url_rewrite_1');
                    }
                }
            }

            Hook::exec('actionAdminMetaSave');
        } elseif (Tools::isSubmit('submitRobots')) {
            $this->generateRobotsFile();
        }

        if (Tools::isSubmit('PS_ROUTE_product_rule')) {
            Tools::clearCache($this->context->smarty);
        }

        if (Tools::isSubmit('deletemeta') && (int)Tools::getValue('id_meta') > 0) {
            Db::getInstance()->delete('theme_meta', 'id_meta='.(int)Tools::getValue('id_meta'));
        }

        $ret = parent::postProcess();

        if (Tools::isSubmit('submitAddmeta') && Validate::isLoadedObject($ret)) {
            /** @var Theme $ret */
            $themes = Theme::getThemes();
            $theme_meta_value = array();
            foreach ($themes as $theme) {
                /** @var Theme $theme */
                $theme_meta_value[] = array(
                    'id_theme' => (int)$theme->id,
                    'id_meta' => (int)$ret->id,
                    'left_column' => (int)$theme->default_left_column,
                    'right_column' => (int)$theme->default_right_column
                );
            }
            if (count($theme_meta_value) > 0) {
                Db::getInstance()->insert('theme_meta', $theme_meta_value, false, true, DB::INSERT_IGNORE);
            }
        }

        return $ret;
    }

    public function generateRobotsFile()
    {
        if (!$write_fd = @fopen($this->rb_file, 'w')) {
            $this->errors[] = sprintf(Tools::displayError('Cannot write into file: %s. Please check write permissions.'), $this->rb_file);
        } else {
            Hook::exec('actionAdminMetaBeforeWriteRobotsFile', array(
                'rb_data' => &$this->rb_data
            ));

            // PS Comments
            fwrite($write_fd, "# robots.txt automaticaly generated by QloApps - open-source hotel management solution.\n");
            fwrite($write_fd, "# https://www.qloapps.com - https://forums.qloapps.com/\n");
            fwrite($write_fd, "# This file is to prevent the crawling and indexing of certain parts\n");
            fwrite($write_fd, "# of your site by web crawlers and spiders run by sites like Yahoo!\n");
            fwrite($write_fd, "# and Google. By telling these \"robots\" where not to go on your site,\n");
            fwrite($write_fd, "# you save bandwidth and server resources.\n");
            fwrite($write_fd, "# For more information about the robots.txt standard, see:\n");
            fwrite($write_fd, "# http://www.robotstxt.org/robotstxt.html\n");

            // User-Agent
            fwrite($write_fd, "User-agent: *\n");

            // Allow Directives
            if (count($this->rb_data['Allow'])) {
                fwrite($write_fd, "# Allow Directives\n");
                foreach ($this->rb_data['Allow'] as $allow) {
                    fwrite($write_fd, 'Allow: '.$allow."\n");
                }
            }

            // Private pages
            if (count($this->rb_data['GB'])) {
                fwrite($write_fd, "# Private pages\n");
                foreach ($this->rb_data['GB'] as $gb) {
                    fwrite($write_fd, 'Disallow: /*'.$gb."\n");
                }
            }

            // Directories
            if (count($this->rb_data['Directories'])) {
                fwrite($write_fd, "# Directories\n");
                foreach ($this->rb_data['Directories'] as $dir) {
                    fwrite($write_fd, 'Disallow: */'.$dir."\n");
                }
            }

            // Files
            if (count($this->rb_data['Files'])) {
                $activeLanguageCount = count(Language::getIDs());
                fwrite($write_fd, "# Files\n");
                foreach ($this->rb_data['Files'] as $iso_code => $files) {
                    foreach ($files as $file) {
                        if ($activeLanguageCount > 1) {
                            // Friendly URLs have language ISO code when multiple languages are active
                            fwrite($write_fd, 'Disallow: /*' . $iso_code . '/' . $file . "\n");
                        } elseif ($activeLanguageCount == 1) {
                            // Friendly URL does not have language ISO when only one language is active
                            fwrite($write_fd, 'Disallow: /*' . $file . "\n");
                        } else {
                            fwrite($write_fd, 'Disallow: /'.$file."\n");
                        }
                    }
                }
            }

            // Sitemap
            if (file_exists($this->sm_file) && filesize($this->sm_file)) {
                fwrite($write_fd, "# Sitemap\n");
                $sitemap_filename = basename($this->sm_file);
                fwrite($write_fd, 'Sitemap: '.(Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$_SERVER['SERVER_NAME']
                    .__PS_BASE_URI__.$sitemap_filename."\n");
            }

            Hook::exec('actionAdminMetaAfterWriteRobotsFile', array(
                'rb_data' => $this->rb_data,
                'write_fd' => &$write_fd
            ));

            fclose($write_fd);

            $this->redirect_after = self::$currentIndex.'&conf=4&token='.$this->token;
        }
    }

    public function getList($id_lang, $orderBy = null, $orderWay = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, Context::getContext()->shop->id);
    }

    public function renderList()
    {
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->displayInformation($this->l('You can only display the page list in a shop context.'));
        } else {
            return parent::renderList();
        }
    }

    /**
     * Validate route syntax and save it in configuration
     *
     * @param string $route_id
     */
    public function checkAndUpdateRoute($route_id)
    {
        $default_routes = Dispatcher::getInstance()->default_routes;
        if (!isset($default_routes[$route_id])) {
            return;
        }

        $rule = Tools::getValue('PS_ROUTE_'.$route_id);
        if (!Validate::isRoutePattern($rule)) {
            $this->errors[] = sprintf('The route %s is not valid', htmlspecialchars($rule));
        } else {
            if (!$rule || $rule == $default_routes[$route_id]['rule']) {
                Configuration::updateValue('PS_ROUTE_'.$route_id, '');
                return;
            }

            $errors = array();
            if (!Dispatcher::getInstance()->validateRoute($route_id, $rule, $errors)) {
                foreach ($errors as $error) {
                    $this->errors[] = sprintf('Keyword "{%1$s}" required for route "%2$s" (rule: "%3$s")', $error, $route_id, htmlspecialchars($rule));
                }
            } else {
                Configuration::updateValue('PS_ROUTE_'.$route_id, $rule);
            }
        }
    }

    /**
     * Called when PS_REWRITING_SETTINGS option is saved
     */
    public function updateOptionPsRewritingSettings()
    {
        Configuration::updateValue('PS_REWRITING_SETTINGS', (int)Tools::getValue('PS_REWRITING_SETTINGS'));

        $this->updateOptionDomain(Tools::getValue('domain'));
        $this->updateOptionDomainSsl(Tools::getValue('domain_ssl'));

        if (Tools::getIsset('uri')) {
            $this->updateOptionUri(Tools::getValue('uri'));
        }

        if (Tools::generateHtaccess($this->ht_file, null, null, '', Tools::getValue('PS_HTACCESS_DISABLE_MULTIVIEWS'), false, Tools::getValue('PS_HTACCESS_DISABLE_MODSEC'))) {
            Tools::enableCache();
            Tools::clearCache($this->context->smarty);
            Tools::restoreCacheSettings();
        } else {
            Configuration::updateValue('PS_REWRITING_SETTINGS', 0);
            // Message copied/pasted from the information tip
            $message = $this->l('Before being able to use this tool, you need to:');
            $message .= '<br />- '.$this->l('Create a blank .htaccess in your root directory.');
            $message .= '<br />- '.$this->l('Give it write permissions (CHMOD 666 on Unix system).');
            $this->errors[] = $message;
        }
    }

    public function updateOptionPsRouteProductRule()
    {
        $this->checkAndUpdateRoute('product_rule');
    }

    public function updateOptionPsRouteCategoryRule()
    {
        $this->checkAndUpdateRoute('category_rule');
    }

    public function updateOptionPsRouteLayeredRule()
    {
        $this->checkAndUpdateRoute('layered_rule');
    }

    public function updateOptionPsRouteSupplierRule()
    {
        $this->checkAndUpdateRoute('supplier_rule');
    }

    public function updateOptionPsRouteManufacturerRule()
    {
        $this->checkAndUpdateRoute('manufacturer_rule');
    }

    public function updateOptionPsRouteCmsRule()
    {
        $this->checkAndUpdateRoute('cms_rule');
    }

    public function updateOptionPsRouteCmsCategoryRule()
    {
        $this->checkAndUpdateRoute('cms_category_rule');
    }

    /**
     * Update shop domain (for mono shop)
     *
     * @param string $value
     *
     * @throws PrestaShopException
     */
    public function updateOptionDomain($value)
    {
        if (!Shop::isFeatureActive() && $this->url && $this->url->domain != $value) {
            if (Validate::isCleanHtml($value)) {
                $this->url->domain = $value;
                $this->url->update();
                Configuration::updateGlobalValue('PS_SHOP_DOMAIN', $value);
            } else {
                $this->errors[] = Tools::displayError('This domain is not valid.');
            }
        }
    }

    /**
     * Update shop SSL domain (for mono shop)
     *
     * @param string $value
     *
     * @throws PrestaShopException
     */
    public function updateOptionDomainSsl($value)
    {
        if (!Shop::isFeatureActive() && $this->url && $this->url->domain_ssl != $value) {
            if (Validate::isCleanHtml($value)) {
                $this->url->domain_ssl = $value;
                $this->url->update();
                Configuration::updateGlobalValue('PS_SHOP_DOMAIN_SSL', $value);
            } else {
                $this->errors[] = Tools::displayError('The SSL domain is not valid.');
            }
        }
    }

    /**
     * Update shop physical uri for mono shop)
     *
     * @param string $value
     *
     * @throws PrestaShopException
     */
    public function updateOptionUri($value)
    {
        if (!Shop::isFeatureActive() && $this->url && $this->url->physical_uri != $value) {
            $this->url->physical_uri = $value;
            $this->url->update();
        }
    }

    /**
     * Function used to render the options for this controller
     */
    public function renderOptions()
    {
        // If friendly url is not active, do not display custom routes form
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $this->addAllRouteFields();
        }

        if ($this->fields_options && is_array($this->fields_options)) {
            $helper = new HelperOptions($this);
            $this->setHelperDisplay($helper);
            $helper->toolbar_scroll = true;
            $helper->toolbar_btn = array(
                'save' => array(
                    'href' => '#',
                    'desc' => $this->l('Save')
                )
            );
            $helper->id = $this->id;
            $helper->tpl_vars = $this->tpl_option_vars;
            $options = $helper->generateOptions($this->fields_options);

            return $options;
        }
    }

    /**
     * Add all custom route fields to the options form
     */
    public function addAllRouteFields()
    {
        $this->addFieldRoute('product_rule', $this->l('Route to products'));
        $this->addFieldRoute('category_rule', $this->l('Route to category'));
        $this->addFieldRoute('supplier_rule', $this->l('Route to supplier'));
        $this->addFieldRoute('manufacturer_rule', $this->l('Route to manufacturer'));
        $this->addFieldRoute('cms_rule', $this->l('Route to CMS page'));
        $this->addFieldRoute('cms_category_rule', $this->l('Route to CMS category'));
        $this->addFieldRoute('module', $this->l('Route to modules'));
    }

    /**
     * Check if a file is writable
     *
     * @param string $file
     * @return bool
     */
    public function checkConfiguration($file)
    {
        if (file_exists($file)) {
            return is_writable($file);
        }
        return is_writable(dirname($file));
    }

    public function getRobotsContent()
    {
        $tab = array();

        // Special allow directives
        $tab['Allow'] = array('*/modules/*.css', '*/modules/*.js');

        // Directories
        $tab['Directories'] = array('classes/', 'config/', 'download/', 'mails/', 'modules/', 'translations/', 'tools/');

        // Files
        $disallow_controllers = array(
            'addresses', 'address', 'authentication', 'cart', 'discount', 'footer',
            'get-file', 'header', 'history', 'identity', 'images.inc', 'init', 'my-account', 'order', 'order-opc',
            'order-slip', 'order-detail', 'order-follow', 'order-return', 'order-confirmation', 'pagination', 'password',
            'pdf-invoice', 'pdf-order-return', 'pdf-order-slip', 'product-sort', 'search', 'statistics','attachment', 'guest-tracking'
        );

        // Rewrite files
        $tab['Files'] = array();
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $sql = 'SELECT ml.url_rewrite, l.iso_code
					FROM '._DB_PREFIX_.'meta m
					INNER JOIN '._DB_PREFIX_.'meta_lang ml ON ml.id_meta = m.id_meta
					INNER JOIN '._DB_PREFIX_.'lang l ON l.id_lang = ml.id_lang
					WHERE l.active = 1 AND m.page IN (\''.implode('\', \'', $disallow_controllers).'\')';
            if ($results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
                foreach ($results as $row) {
                    $tab['Files'][$row['iso_code']][] = $row['url_rewrite'];
                }
            }
        }

        $tab['GB'] = array(
            '?orderby=','?orderway=','?tag=','?id_currency=','?search_query=','?back=','?n=',
            '&orderby=','&orderway=','&tag=','&id_currency=','&search_query=','&back=','&n='
        );

        foreach ($disallow_controllers as $controller) {
            $tab['GB'][] = 'controller='.$controller;
        }

        return $tab;
    }
}
