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
 * @property Manufacturer $object
 */
class AdminManufacturersControllerCore extends AdminController
{
    public $bootstrap = true ;
    /** @var array countries list */
    protected $countries_array = array();

    public function __construct()
    {
        $this->table = 'manufacturer';
        $this->className = 'Manufacturer';
        $this->lang = false;
        $this->deleted = false;
        $this->allow_export = true;
        $this->list_id = 'manufacturer';
        $this->identifier = 'id_manufacturer';
        $this->_defaultOrderBy = 'name';
        $this->_defaultOrderWay = 'ASC';

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );

        $this->context = Context::getContext();

        $this->fieldImageSettings = array(
            'name' => 'logo',
            'dir' => 'm'
        );

        $this->fields_list = array(
            'id_manufacturer' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'logo' => array(
                'title' => $this->l('Logo'),
                'image' => 'm',
                'orderby' => false,
                'search' => false,
                'align' => 'center',
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 'auto'
            ),
            'addresses' => array(
                'title' => $this->l('Addresses'),
                'search' => false,
                'align' => 'center'
            ),
            'products' => array(
                'title' => $this->l('Products'),
                'search' => false,
                'align' => 'center',
            ),
            'active' => array(
                'title' => $this->l('Enabled'),
                'active' => 'status',
                'type' => 'bool',
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'orderby' => false
            )
        );

        parent::__construct();
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryUi('ui.widget');
        $this->addJqueryPlugin('tagify');
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_manufacturer'] = array(
                'href' => self::$currentIndex.'&addmanufacturer&token='.$this->token,
                'desc' => $this->l('Add new manufacturer', null, null, false),
                'icon' => 'process-icon-new'
            );
            $this->page_header_toolbar_btn['new_manufacturer_address'] = array(
                'href' => self::$currentIndex.'&addaddress&token='.$this->token,
                'desc' => $this->l('Add new manufacturer address', null, null, false),
                'icon' => 'process-icon-new'
            );
        } elseif ($this->display == 'editaddresses' || $this->display == 'addaddress') {
            // Default cancel button - like old back link
            if (!isset($this->no_back) || $this->no_back == false) {
                $back = Tools::safeOutput(Tools::getValue('back', ''));
                if (empty($back)) {
                    $back = self::$currentIndex.'&token='.$this->token;
                }

                $this->page_header_toolbar_btn['cancel'] = array(
                    'href' => $back,
                    'desc' => $this->l('Cancel', null, null, false)
                );
            }
        }

        parent::initPageHeaderToolbar();
    }

    public function initListManufacturer()
    {
        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->_select = '
			COUNT(`id_product`) AS `products`, (
				SELECT COUNT(ad.`id_manufacturer`) as `addresses`
				FROM `'._DB_PREFIX_.'address` ad
				WHERE ad.`id_manufacturer` = a.`id_manufacturer`
					AND ad.`deleted` = 0
				GROUP BY ad.`id_manufacturer`) as `addresses`';
        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'product` p ON (a.`id_manufacturer` = p.`id_manufacturer`)';
        $this->_group = 'GROUP BY a.`id_manufacturer`';

        $this->context->smarty->assign('title_list', $this->l('List of manufacturers'));

        $this->content .= parent::renderList();
    }

    protected function getAddressFieldsList()
    {
        // Sub tab addresses
        $countries = Country::getCountries($this->context->language->id);
        foreach ($countries as $country) {
            $this->countries_array[$country['id_country']] = $country['name'];
        }

        return array(
            'id_address' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'manufacturer_name' => array(
                'title' => $this->l('Manufacturer'),
                'width' => 'auto',
                'filter_key' => 'manufacturer_name'
            ),
            'firstname' => array(
                'title' => $this->l('First name')
            ),
            'lastname' => array(
                'title' => $this->l('Last name'),
                'filter_key' => 'a!lastname'
            ),
            'postcode' => array(
                'title' => $this->l('Zip/Postal code'),
                'align' => 'right'
            ),
            'city' => array(
                'title' => $this->l('City')
            ),
            'country' => array(
                'title' => $this->l('Country'),
                'type' => 'select',
                'list' => $this->countries_array,
                'filter_key' => 'cl!id_country'
            )
        );
    }

    public function processExport($text_delimiter = '"')
    {
        if (strtolower($this->table) == 'address') {
            $this->_defaultOrderBy = 'id_manufacturer';
            $this->_where = 'AND a.`id_customer` = 0 AND a.`id_supplier` = 0 AND a.`id_warehouse` = 0 AND a.`deleted`= 0';
        }

        return parent::processExport($text_delimiter);
    }

    public function initListManufacturerAddresses()
    {
        $this->toolbar_title = $this->l('Addresses');
        // reset actions and query vars
        $this->actions = array();
        unset($this->fields_list, $this->_select, $this->_join, $this->_group, $this->_filterHaving, $this->_filter);

        $this->table = 'address';
        $this->list_id = 'address';
        $this->identifier = 'id_address';
        $this->deleted = true;

        $this->_defaultOrderBy = 'id_address';
        $this->_defaultOrderWay = 'ASC';

        $this->_orderBy = null;

        $this->addRowAction('editaddresses');
        $this->addRowAction('delete');

        // test if a filter is applied for this list
        if (Tools::isSubmit('submitFilter'.$this->table) || $this->context->cookie->{'submitFilter'.$this->table} !== false) {
            $this->filter = true;
        }

        // test if a filter reset request is required for this list
        $this->action = (isset($_POST['submitReset'.$this->table]) ? 'reset_filters' : '');

        $this->fields_list = $this->getAddressFieldsList();
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );

        $this->_select = 'cl.`name` as country, m.`name` AS manufacturer_name';
        $this->_join = '
			LEFT JOIN `'._DB_PREFIX_.'country_lang` cl
				ON (cl.`id_country` = a.`id_country` AND cl.`id_lang` = '.(int)$this->context->language->id.') ';
        $this->_join .= '
			LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
				ON (a.`id_manufacturer` = m.`id_manufacturer`)';
        $this->_where = 'AND a.`id_customer` = 0 AND a.`id_supplier` = 0 AND a.`id_warehouse` = 0 AND a.`deleted`= 0';

        $this->context->smarty->assign('title_list', $this->l('Manufacturers addresses'));

        // call postProcess() for take care about actions and filters
        $this->postProcess();

        $this->initToolbar();

        $this->content .= parent::renderList();
    }

    public function renderList()
    {
        $this->initListManufacturer();
        $this->initListManufacturerAddresses();
    }

    /**
     * Display editaddresses action link
     * @param string $token the token to add to the link
     * @param int $id the identifier to add to the link
     * @return string
     */
    public function displayEditaddressesLink($token = null, $id)
    {
        if (!array_key_exists('editaddresses', self::$cache_lang)) {
            self::$cache_lang['editaddresses'] = $this->l('Edit');
        }

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex.
                '&'.$this->identifier.'='.$id.
                '&editaddresses&token='.($token != null ? $token : $this->token),
            'action' => self::$cache_lang['editaddresses'],
        ));

        return $this->context->smarty->fetch('helpers/list/list_action_edit.tpl');
    }

    public function renderForm()
    {
        if (!($manufacturer = $this->loadObject(true))) {
            return;
        }

        $image = _PS_MANU_IMG_DIR_.$manufacturer->id.'.jpg';
        $image_url = ImageManager::thumbnail($image, $this->table.'_'.(int)$manufacturer->id.'.'.$this->imageType, 350,
            $this->imageType, true, true);
        $image_size = file_exists($image) ? filesize($image) / 1000 : false;

        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Manufacturers'),
                'icon' => 'icon-certificate'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'col' => 4,
                    'required' => true,
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Short description'),
                    'name' => 'short_description',
                    'lang' => true,
                    'cols' => 60,
                    'rows' => 10,
                    'autoload_rte' => 'rte', //Enable TinyMCE editor for short description
                    'col' => 6,
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'description',
                    'lang' => true,
                    'cols' => 60,
                    'rows' => 10,
                    'col' => 6,
                    'autoload_rte' => 'rte', //Enable TinyMCE editor for description
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Logo'),
                    'name' => 'logo',
                    'image' => $image_url ? $image_url : false,
                    'size' => $image_size,
                    'display_image' => true,
                    'col' => 6,
                    'hint' => $this->l('Upload a manufacturer logo from your computer.')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta title'),
                    'name' => 'meta_title',
                    'lang' => true,
                    'col' => 4,
                    'hint' => $this->l('Forbidden characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta description'),
                    'name' => 'meta_description',
                    'lang' => true,
                    'col' => 6,
                    'hint' => $this->l('Forbidden characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'tags',
                    'label' => $this->l('Meta keywords'),
                    'name' => 'meta_keywords',
                    'lang' => true,
                    'col' => 6,
                    'hint' => array(
                        $this->l('Forbidden characters:').' &lt;&gt;;=#{}',
                        $this->l('To add "tags," click inside the field, write something, and then press "Enter."')
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable'),
                    'name' => 'active',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                )
            )
        );

        if (!($manufacturer = $this->loadObject(true))) {
            return;
        }

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            );
        }

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save')
        );

        foreach ($this->_languages as $language) {
            $this->fields_value['short_description_'.$language['id_lang']] = htmlentities(stripslashes($this->getFieldValue(
                $manufacturer,
                'short_description',
                $language['id_lang']
            )), ENT_COMPAT, 'UTF-8');

            $this->fields_value['description_'.$language['id_lang']] = htmlentities(stripslashes($this->getFieldValue(
                $manufacturer,
                'description',
                $language['id_lang']
            )), ENT_COMPAT, 'UTF-8');
        }

        return parent::renderForm();
    }

    public function renderFormAddress()
    {
        // Change table and className for addresses
        $this->table = 'address';
        $this->className = 'Address';
        $id_address = Tools::getValue('id_address');

        // Create Object Address
        $address = new Address($id_address);

        $res = $address->getFieldsRequiredDatabase();
        $required_fields = array();
        foreach ($res as $row) {
            $required_fields[(int)$row['id_required_field']] = $row['field_name'];
        }

        $form = array(
            'legend' => array(
                'title' => $this->l('Addresses'),
                'icon' => 'icon-building'
            )
        );

        if (!$address->id_manufacturer || !Manufacturer::manufacturerExists($address->id_manufacturer)) {
            $form['input'][] = array(
                'type' => 'select',
                'label' => $this->l('Choose the manufacturer'),
                'name' => 'id_manufacturer',
                'options' => array(
                    'query' => Manufacturer::getManufacturers(),
                    'id' => 'id_manufacturer',
                    'name' => 'name'
                )
            );
        } else {
            $form['input'][] = array(
                'type' => 'text',
                'label' => $this->l('Manufacturer'),
                'name' => 'name',
                'col' => 4,
                'disabled' => true,
            );

            $form['input'][] = array(
                'type' => 'hidden',
                'name' => 'id_manufacturer'
            );
        }

        $form['input'][] = array(
            'type' => 'hidden',
            'name' => 'alias',
        );

        $form['input'][] = array(
            'type' => 'hidden',
            'name' => 'id_address',
        );

        if (in_array('company', $required_fields)) {
            $form['input'][] = array(
                'type' => 'text',
                'label' => $this->l('Company'),
                'name' => 'company',
                'display' => in_array('company', $required_fields),
                'required' => in_array('company', $required_fields),
                'maxlength' => 16,
                'col' => 4,
                'hint' => $this->l('Company name for this supplier')
            );
        }

        $form['input'][] = array(
            'type' => 'text',
            'label' => $this->l('Last name'),
            'name' => 'lastname',
            'required' => true,
            'col' => 4,
            'hint' => $this->l('Invalid characters:').' 0-9!&lt;&gt;,;?=+()@#"�{}_$%:'
        );
        $form['input'][] = array(
            'type' => 'text',
            'label' => $this->l('First name'),
            'name' => 'firstname',
            'required' => true,
            'col' => 4,
            'hint' => $this->l('Invalid characters:').' 0-9!&lt;&gt;,;?=+()@#"�{}_$%:'
        );
        $form['input'][] = array(
            'type' => 'text',
            'label' => $this->l('Address'),
            'name' => 'address1',
            'col' => 6,
            'required' => true,
        );
        $form['input'][] = array(
            'type' => 'text',
            'label' => $this->l('Address (2)'),
            'name' => 'address2',
            'col' => 6,
            'required' => in_array('address2', $required_fields)
        );
        $form['input'][] = array(
            'type' => 'text',
            'label' => $this->l('Zip/postal code'),
            'name' => 'postcode',
            'col' => 2,
            'required' => in_array('postcode', $required_fields)
        );
        $form['input'][] = array(
            'type' => 'text',
            'label' => $this->l('City'),
            'name' => 'city',
            'col' => 4,
            'required' => true,
        );
        $form['input'][] = array(
            'type' => 'select',
            'label' => $this->l('Country'),
            'name' => 'id_country',
            'required' => false,
            'default_value' => (int)$this->context->country->id,
            'col' => 4,
            'options' => array(
                'query' => Country::getCountries($this->context->language->id),
                'id' => 'id_country',
                'name' => 'name',
            )
        );
        $form['input'][] = array(
            'type' => 'select',
            'label' => $this->l('State'),
            'name' => 'id_state',
            'required' => false,
            'col' => 4,
            'options' => array(
                'query' => array(),
                'id' => 'id_state',
                'name' => 'name'
            )
        );
        $form['input'][] = array(
            'type' => 'text',
            'label' => $this->l('Home phone'),
            'name' => 'phone',
            'col' => 4,
            'required' => in_array('phone', $required_fields)
        );
        $form['input'][] = array(
            'type' => 'text',
            'label' => $this->l('Mobile phone'),
            'name' => 'phone_mobile',
            'col' => 4,
            'required' => in_array('phone_mobile', $required_fields)
        );
        $form['input'][] = array(
            'type' => 'textarea',
            'label' => $this->l('Other'),
            'name' => 'other',
            'required' => false,
            'hint' => $this->l('Forbidden characters:').' &lt;&gt;;=#{}',
            'rows' => 2,
            'cols' => 10,
            'col' => 6,
        );
        $form['submit'] = array(
            'title' => $this->l('Save'),
        );

        $this->fields_value = array(
            'name' => Manufacturer::getNameById($address->id_manufacturer),
            'alias' => 'manufacturer',
            'id_country' => $address->id_country
        );

        $this->initToolbar();
        $this->fields_form[0]['form'] = $form;
        $this->getlanguages();
        $helper = new HelperForm();
        $helper->show_cancel_button = true;

        $back = Tools::safeOutput(Tools::getValue('back', ''));
        if (empty($back)) {
            $back = self::$currentIndex.'&token='.$this->token;
        }
        if (!Validate::isCleanHtml($back)) {
            die(Tools::displayError());
        }

        $helper->back_url = $back;
        $helper->currentIndex = self::$currentIndex;
        $helper->token = $this->token;
        $helper->table = $this->table;
        $helper->identifier = $this->identifier;
        $helper->title = $this->l('Edit Addresses');
        $helper->id = $address->id;
        $helper->toolbar_scroll = true;
        $helper->languages = $this->_languages;
        $helper->default_form_language = $this->default_form_language;
        $helper->allow_employee_form_lang = $this->allow_employee_form_lang;
        $helper->fields_value = $this->getFieldsValue($address);
        $helper->toolbar_btn = $this->toolbar_btn;
        $this->content .= $helper->generateForm($this->fields_form);
    }

    /**
     * AdminController::initToolbar() override
     * @see AdminController::initToolbar()
     *
     */
    public function initToolbar()
    {
        switch ($this->display) {
            case 'editaddresses':
            case 'addaddress':
                $this->toolbar_btn['save'] = array(
                    'href' => '#',
                    'desc' => $this->l('Save')
                );

                // Default cancel button - like old back link
                if (!isset($this->no_back) || $this->no_back == false) {
                    $back = Tools::safeOutput(Tools::getValue('back', ''));
                    if (empty($back)) {
                        $back = self::$currentIndex.'&token='.$this->token;
                    }

                    $this->toolbar_btn['cancel'] = array(
                        'href' => $back,
                        'desc' => $this->l('Cancel')
                    );
                }
            break;

            default:
                parent::initToolbar();

                if ($this->can_import) {
                    $this->toolbar_btn['import'] = array(
                        'href' => $this->context->link->getAdminLink('AdminImport', true).'&import_type=manufacturers',
                        'desc' => $this->l('Import')
                    );
                }
        }
    }

    public function renderView()
    {
        if (!($manufacturer = $this->loadObject())) {
            return;
        }

        /** @var Manufacturer $manufacturer */

        $this->toolbar_btn['new'] = array(
                    'href' => $this->context->link->getAdminLink('AdminManufacturers').'&addaddress=1&id_manufacturer='.(int)$manufacturer->id,
                    'desc' => $this->l('Add address')
                );

        $this->toolbar_title = is_array($this->breadcrumbs) ? array_unique($this->breadcrumbs) : array($this->breadcrumbs);
        $this->toolbar_title[] = $manufacturer->name;

        $addresses = $manufacturer->getAddresses($this->context->language->id);

        $products = $manufacturer->getProductsLite($this->context->language->id);
        $total_product = count($products);
        for ($i = 0; $i < $total_product; $i++) {
            $products[$i] = new Product($products[$i]['id_product'], false, $this->context->language->id);
            $products[$i]->loadStockData();
            /* Build attributes combinations */
            $combinations = $products[$i]->getAttributeCombinations($this->context->language->id);
            foreach ($combinations as $k => $combination) {
                $comb_array[$combination['id_product_attribute']]['reference'] = $combination['reference'];
                $comb_array[$combination['id_product_attribute']]['ean13'] = $combination['ean13'];
                $comb_array[$combination['id_product_attribute']]['upc'] = $combination['upc'];
                $comb_array[$combination['id_product_attribute']]['quantity'] = $combination['quantity'];
                $comb_array[$combination['id_product_attribute']]['attributes'][] = array(
                    $combination['group_name'],
                    $combination['attribute_name'],
                    $combination['id_attribute']
                );
            }

            if (isset($comb_array)) {
                foreach ($comb_array as $key => $product_attribute) {
                    $list = '';
                    foreach ($product_attribute['attributes'] as $attribute) {
                        $list .= $attribute[0].' - '.$attribute[1].', ';
                    }
                    $comb_array[$key]['attributes'] = rtrim($list, ', ');
                }
                isset($comb_array) ? $products[$i]->combination = $comb_array : '';
                unset($comb_array);
            }
        }

        $this->tpl_view_vars = array(
            'manufacturer' => $manufacturer,
            'addresses' => $addresses,
            'products' => $products,
            'stock_management' => Configuration::get('PS_STOCK_MANAGEMENT'),
            'shopContext' => Shop::getContext(),
        );

        return parent::renderView();
    }

    public function initContent()
    {
        $this->initTabModuleList();
        // toolbar (save, cancel, new, ..)
        $this->initToolbar();
        $this->initPageHeaderToolbar();
        if ($this->display == 'editaddresses' || $this->display == 'addaddress') {
            $this->content .= $this->renderFormAddress();
        } elseif ($this->display == 'edit' || $this->display == 'add') {
            if (!$this->loadObject(true)) {
                return;
            }
            $this->content .= $this->renderForm();
        } elseif ($this->display == 'view') {
            // Some controllers use the view action without an object
            if ($this->className) {
                $this->loadObject(true);
            }
            $this->content .= $this->renderView();
        } elseif (!$this->ajax) {
            $this->content .= $this->renderList();
            $this->content .= $this->renderOptions();
        }

        $this->context->smarty->assign(array(
            'content' => $this->content,
            'url_post' => self::$currentIndex.'&token='.$this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn
        ));
    }

    /**
     * AdminController::init() override
     * @see AdminController::init()
     */
    public function init()
    {
        parent::init();

        if (Tools::isSubmit('editaddresses')) {
            $this->display = 'editaddresses';
        } elseif (Tools::isSubmit('updateaddress')) {
            $this->display = 'editaddresses';
        } elseif (Tools::isSubmit('addaddress')) {
            $this->display = 'addaddress';
        } elseif (Tools::isSubmit('submitAddaddress')) {
            $this->action = 'save';
        } elseif (Tools::isSubmit('deleteaddress')) {
            $this->action = 'delete';
        }
    }

    public function initProcess()
    {
        if (Tools::isSubmit('submitAddaddress') || Tools::isSubmit('deleteaddress') || Tools::isSubmit('submitBulkdeleteaddress') || Tools::isSubmit('exportaddress')) {
            $this->table = 'address';
            $this->className = 'Address';
            $this->identifier = 'id_address';
            $this->deleted = true;
            $this->fields_list = $this->getAddressFieldsList();
        }
        parent::initProcess();
    }

    protected function afterImageUpload()
    {
        $res = true;
        $generate_hight_dpi_images = (bool)Configuration::get('PS_HIGHT_DPI');

        /* Generate image with differents size */
        if (($id_manufacturer = (int)Tools::getValue('id_manufacturer')) &&
            isset($_FILES) &&
            count($_FILES) &&
            file_exists(_PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg')) {
            $images_types = ImageType::getImagesTypes('manufacturers');
            foreach ($images_types as $k => $image_type) {
                $res &= ImageManager::resize(
                    _PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg',
                    _PS_MANU_IMG_DIR_.$id_manufacturer.'-'.stripslashes($image_type['name']).'.jpg',
                    (int)$image_type['width'],
                    (int)$image_type['height']
                );

                if ($generate_hight_dpi_images) {
                    $res &= ImageManager::resize(
                        _PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg',
                        _PS_MANU_IMG_DIR_.$id_manufacturer.'-'.stripslashes($image_type['name']).'2x.jpg',
                        (int)$image_type['width']*2,
                        (int)$image_type['height']*2
                    );
                }
            }

            $current_logo_file = _PS_TMP_IMG_DIR_.'manufacturer_mini_'.$id_manufacturer.'_'.$this->context->shop->id.'.jpg';

            if ($res && file_exists($current_logo_file)) {
                unlink($current_logo_file);
            }
        }

        if (!$res) {
            $this->errors[] = Tools::displayError('Unable to resize one or more of your pictures.');
        }

        return $res;
    }

    protected function beforeDelete($object)
    {
        return true;
    }

    public function processSave()
    {
        if (Tools::isSubmit('submitAddaddress')) {
            $this->display = 'editaddresses';
        }

        return parent::processSave();
    }
}
