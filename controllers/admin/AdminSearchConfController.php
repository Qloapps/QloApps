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
 * @property Alias $object
 */
class AdminSearchConfControllerCore extends AdminController
{
    protected $toolbar_scroll = false;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'alias';
        $this->className = 'Alias';
        $this->lang = false;

        parent::__construct();

        // Alias fields
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        if (!Tools::getValue('realedit')) {
            $this->deleted = false;
        }

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->fields_list = array(
            'alias' => array('title' => $this->l('Aliases')),
            'search' => array('title' => $this->l('Search')),
            'active' => array('title' => $this->l('Status'), 'class' => 'fixed-width-sm', 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false)
        );

        // Search options
        $current_file_name = array_reverse(explode('/', $_SERVER['SCRIPT_NAME']));
        $cron_url = Tools::getHttpHost(true, true).__PS_BASE_URI__.basename(_PS_ADMIN_DIR_).
            '/searchcron.php?full=1&token='.substr(_COOKIE_KEY_, 34, 8).(Shop::getContext() == Shop::CONTEXT_SHOP ? '&id_shop='.(int)Context::getContext()->shop->id : '');

        list($total, $indexed) = Db::getInstance()->getRow('SELECT COUNT(*) as "0", SUM(product_shop.indexed) as "1" FROM '._DB_PREFIX_.'product p '.Shop::addSqlAssociation('product', 'p').' WHERE product_shop.`visibility` IN ("both", "search") AND product_shop.`active` = 1');

        $this->fields_options = array(
            'indexation' => array(
                'title' => $this->l('Indexing'),
                'icon' => 'icon-cogs',
                'info' => '<p>
						'.$this->l('The "indexed" products have been analyzed by Qloapps and will appear in the results of a front office search.').'<br />
						'.$this->l('Indexed products').' <strong>'.(int)$indexed.' / '.(int)$total.'</strong>.
					</p>
					<p>
						'.$this->l('Building the product index may take a few minutes.').'
						'.$this->l('If your server stops before the process ends, you can resume the indexing by clicking "Add missing products to the index".').'
					</p>
					<a href="searchcron.php?token='.substr(_COOKIE_KEY_, 34, 8).'&amp;redirect=1'.(Shop::getContext() == Shop::CONTEXT_SHOP ? '&id_shop='.(int)Context::getContext()->shop->id : '').'" class="btn-link">
						<i class="icon-external-link-sign"></i>
						'.$this->l('Add missing products to the index').'
					</a><br />
					<a href="searchcron.php?full=1&amp;token='.substr(_COOKIE_KEY_, 34, 8).'&amp;redirect=1'.(Shop::getContext() == Shop::CONTEXT_SHOP ? '&id_shop='.(int)Context::getContext()->shop->id : '').'" class="btn-link">
						<i class="icon-external-link-sign"></i>
						'.$this->l('Re-build the entire index').'
					</a><br /><br />
					<p>
						'.$this->l('You can set a cron job that will rebuild your index using the following URL:').'<br />
						<a href="'.Tools::safeOutput($cron_url).'">
							<i class="icon-external-link-sign"></i>
							'.Tools::safeOutput($cron_url).'
						</a>
					</p><br />',
                'fields' =>    array(
                    'PS_SEARCH_INDEXATION' => array(
                        'title' => $this->l('Indexing'),
                        'validation' => 'isBool',
                        'type' => 'bool',
                        'cast' => 'intval',
                        'desc' => $this->l('Enable the automatic indexing of products. If you enable this feature, the products will be indexed in the search automatically when they are saved. If the feature is disabled, you will have to index products manually by using the links provided in the field set.')
                    )
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
            'search' => array(
                'title' =>    $this->l('Search'),
                'icon' =>    'icon-search',
                'fields' =>    array(
                    'PS_SEARCH_AJAX' => array(
                        'title' => $this->l('Ajax search'),
                        'validation' => 'isBool',
                        'type' => 'bool',
                        'cast' => 'intval',
                        'hint' => array(
                            $this->l('Enable ajax search for your visitors.'),
                            $this->l('With ajax search, the first 10 products matching the user query will appear in real time below the input field.')
                        )
                    ),
                    'PS_INSTANT_SEARCH' => array(
                        'title' => $this->l('Instant search'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool',
                        'hint' => array(
                            $this->l('Enable instant search for your visitors?'),
                            $this->l('With instant search, the results will appear immediately as the user writes a query.')
                        )
                    ),
                    'PS_SEARCH_START' => array(
                        'title' => $this->l('Search within word'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool',
                        'desc' => $this->l('By default, to search for “blouse”, you have to enter “blous”, “blo”, etc (beginning of the word) – but not “lous” (within the word).').'<br/>'.
                                  $this->l('With this option enabled, it also gives the good result if you search for “lous”, “ouse”, or anything contained in the word.'),
                        'hint' => array(
                            $this->l('Enable search within a whole word, rather than from its beginning only.'),
                            $this->l('It checks if the searched term is contained in the indexed word. This may be resource-consuming.')
                        )
                    ),
                    'PS_SEARCH_END' => array(
                        'title' => $this->l('Search exact end match'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool',
                        'desc' => $this->l('By default, if you search "book", you will have "book", "bookcase" and "bookend".').'<br/>'.
                                  $this->l('With this option enabled, it only gives one result “book”, as exact end of the indexed word is matching.'),
                        'hint' => array(
                            $this->l('Enable more precise search with the end of the word.'),
                            $this->l('It checks if the searched term is the exact end of the indexed word.')
                        )
                    ),
                    'PS_SEARCH_MINWORDLEN' => array(
                        'title' => $this->l('Minimum word length (in characters)'),
                        'hint' => $this->l('Only words this size or larger will be indexed.'),
                        'validation' => 'isUnsignedInt',
                        'type' => 'text',
                        'cast' => 'intval'
                    ),
                    'PS_SEARCH_BLACKLIST' => array(
                        'title' => $this->l('Blacklisted words'),
                        'validation' => 'isGenericName',
                        'hint' => $this->l('Please enter the index words separated by a "|".'),
                        'type' => 'textareaLang'
                    )
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
            'relevance' => array(
                'title' =>    $this->l('Weight'),
                'icon' =>    'icon-cogs',
                'info' =>
                        $this->l('The "weight" represents its importance and relevance for the ranking of the products when completing a new search.').'<br />
						'.$this->l('A word with a weight of eight will have four times more value than a word with a weight of two.').'<br /><br />
						'.$this->l('We advise you to set a greater weight for words which appear in the name or reference of a product. This will allow the search results to be as precise and relevant as possible.').'<br /><br />
						'.$this->l('Setting a weight to 0 will exclude that field from search index. Re-build of the entire index is required when changing to or from 0'),
                'fields' =>    array(
                    'PS_SEARCH_WEIGHT_PNAME' => array(
                        'title' => $this->l('Product name weight'),
                        'validation' => 'isUnsignedInt',
                        'type' => 'text',
                        'cast' => 'intval'
                    ),
                    'PS_SEARCH_WEIGHT_REF' => array(
                        'title' => $this->l('Reference weight'),
                        'validation' => 'isUnsignedInt',
                        'type' => 'text',
                        'cast' => 'intval'
                    ),
                    'PS_SEARCH_WEIGHT_SHORTDESC' => array(
                        'title' => $this->l('Short description weight'),
                        'validation' => 'isUnsignedInt',
                        'type' => 'text',
                        'cast' => 'intval'
                    ),
                    'PS_SEARCH_WEIGHT_DESC' => array(
                        'title' => $this->l('Description weight'),
                        'validation' => 'isUnsignedInt',
                        'type' => 'text',
                        'cast' => 'intval'
                    ),
                    'PS_SEARCH_WEIGHT_CNAME' => array(
                        'title' => $this->l('Category weight'),
                        'validation' => 'isUnsignedInt',
                        'type' => 'text',
                        'cast' => 'intval'
                    ),
                    'PS_SEARCH_WEIGHT_MNAME' => array(
                        'title' => $this->l('Manufacturer weight'),
                        'validation' => 'isUnsignedInt',
                        'type' => 'text',
                        'cast' => 'intval'
                    ),
                    'PS_SEARCH_WEIGHT_TAG' => array(
                        'title' => $this->l('Tags weight'),
                        'validation' => 'isUnsignedInt',
                        'type' => 'text',
                        'cast' => 'intval'
                    ),
                    'PS_SEARCH_WEIGHT_ATTRIBUTE' => array(
                        'title' => $this->l('Attributes weight'),
                        'validation' => 'isUnsignedInt',
                        'type' => 'text',
                        'cast' => 'intval'
                    ),
                    'PS_SEARCH_WEIGHT_FEATURE' => array(
                        'title' => $this->l('Features weight'),
                        'validation' => 'isUnsignedInt',
                        'type' => 'text',
                        'cast' => 'intval'
                    )
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
        );
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_alias'] = array(
                'href' => self::$currentIndex.'&addalias&token='.$this->token,
                'desc' => $this->l('Add new alias', null, null, false),
                'icon' => 'process-icon-new'
            );
        }
        $this->identifier_name = 'alias';
        parent::initPageHeaderToolbar();
        if ($this->can_import) {
            $this->toolbar_btn['import'] = array(
                'href' => $this->context->link->getAdminLink('AdminImport', true).'&import_type=alias',
                'desc' => $this->l('Import', null, null, false)
            );
        }
    }

    public function initProcess()
    {
        parent::initProcess();
        // This is a composite page, we don't want the "options" display mode
        if ($this->display == 'options') {
            $this->display = '';
        }
    }

    /**
     * Function used to render the options for this controller
     */
    public function renderOptions()
    {
        if ($this->fields_options && is_array($this->fields_options)) {
            $helper = new HelperOptions($this);
            $this->setHelperDisplay($helper);
            $helper->toolbar_scroll = true;
            $helper->toolbar_btn = array('save' => array(
                'href' => '#',
                'desc' => $this->l('Save')
            ));
            $helper->id = $this->id;
            $helper->tpl_vars = $this->tpl_option_vars;
            $options = $helper->generateOptions($this->fields_options);

            return $options;
        }
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Aliases'),
                'icon' => 'icon-search'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Alias'),
                    'name' => 'alias',
                    'required' => true,
                    'hint' => array(
                        $this->l('Enter each alias separated by a comma (e.g. \'qloapps,preztashop,prestasohp\').'),
                        $this->l('Forbidden characters: &lt;&gt;;=#{}')
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Result'),
                    'name' => 'search',
                    'required' => true,
                    'hint' => $this->l('Search this word instead.')
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );

        $this->fields_value = array('alias' => $this->object->getAliases());

        return parent::renderForm();
    }

    public function processSave()
    {
        $search = strval(Tools::getValue('search'));
        $string = strval(Tools::getValue('alias'));
        $aliases = explode(',', $string);
        if (empty($search) || empty($string)) {
            $this->errors[] = $this->l('Aliases and results are both required.');
        }
        if (!Validate::isValidSearch($search)) {
            $this->errors[] = $search.' '.$this->l('Is not a valid result');
        }
        foreach ($aliases as $alias) {
            if (!Validate::isValidSearch($alias)) {
                $this->errors[] = $alias.' '.$this->l('Is not a valid alias');
            }
        }

        if (!count($this->errors)) {
            foreach ($aliases as $alias) {
                $obj = new Alias(null, trim($alias), trim($search));
                $obj->save();
            }
        }

        if (empty($this->errors)) {
            $this->confirmations[] = $this->l('Creation successful');
        }
    }
}
