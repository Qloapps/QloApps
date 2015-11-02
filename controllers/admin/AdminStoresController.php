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
 * @property Store $object
 */
class AdminStoresControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'store';
        $this->className = 'Store';
        $this->lang = false;
        $this->toolbar_scroll = false;

        $this->context = Context::getContext();

        if (!Tools::getValue('realedit')) {
            $this->deleted = false;
        }

        $this->fieldImageSettings = array(
            'name' => 'image',
            'dir' => 'st'
        );

        $this->fields_list = array(
            'id_store' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'name' => array('title' => $this->l('Name'), 'filter_key' => 'a!name'),
            'address1' => array('title' => $this->l('Address'), 'filter_key' => 'a!address1'),
            'city' => array('title' => $this->l('City')),
            'postcode' => array('title' => $this->l('Zip/postal code')),
            'state' => array('title' => $this->l('State'), 'filter_key' => 'st!name'),
            'country' => array('title' => $this->l('Country'), 'filter_key' => 'cl!name'),
            'phone' => array('title' => $this->l('Phone')),
            'fax' => array('title' => $this->l('Fax')),
            'active' => array('title' => $this->l('Enabled'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false)
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->fields_options = array(
            'general' => array(
                'title' => $this->l('Parameters'),
                'fields' => array(
                    'PS_STORES_DISPLAY_FOOTER' => array(
                        'title' => $this->l('Display in the footer'),
                        'hint' => $this->l('Display a link to the store locator in the footer.'),
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_STORES_DISPLAY_SITEMAP' => array(
                        'title' => $this->l('Display in the sitemap page'),
                        'hint' => $this->l('Display a link to the store locator in the sitemap page.'),
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_STORES_SIMPLIFIED' => array(
                        'title' => $this->l('Show a simplified store locator'),
                        'hint' => $this->l('No map, no search, only a store directory.'),
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_STORES_CENTER_LAT' => array(
                        'title' => $this->l('Default latitude'),
                        'hint' => $this->l('Used for the initial position of the map.'),
                        'cast' => 'floatval',
                        'type' => 'text',
                        'size' => '10'
                    ),
                    'PS_STORES_CENTER_LONG' => array(
                        'title' => $this->l('Default longitude'),
                        'hint' => $this->l('Used for the initial position of the map.'),
                        'cast' => 'floatval',
                        'type' => 'text',
                        'size' => '10'
                    )
                ),
                'submit' => array('title' => $this->l('Save'))
            )
        );

        parent::__construct();

        $this->_buildOrderedFieldsShop($this->_getDefaultFieldsContent());
    }

    public function renderOptions()
    {
        // Set toolbar options
        $this->display = 'options';
        $this->show_toolbar = true;
        $this->toolbar_scroll = true;
        $this->initToolbar();

        return parent::renderOptions();
    }

    public function initToolbar()
    {
        parent::initToolbar();

        if ($this->display == 'options') {
            unset($this->toolbar_btn['new']);
        } elseif ($this->display != 'add' && $this->display != 'edit') {
            unset($this->toolbar_btn['save']);
        }
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_store'] = array(
                'href' => self::$currentIndex.'&addstore&token='.$this->token,
                'desc' => $this->l('Add new store', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function renderList()
    {
        // Set toolbar options
        $this->display = null;
        $this->initToolbar();

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->_select = 'cl.`name` country, st.`name` state';
        $this->_join = '
			LEFT JOIN `'._DB_PREFIX_.'country_lang` cl
				ON (cl.`id_country` = a.`id_country`
				AND cl.`id_lang` = '.(int)$this->context->language->id.')
			LEFT JOIN `'._DB_PREFIX_.'state` st
				ON (st.`id_state` = a.`id_state`)';

        return parent::renderList();
    }

    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $image = _PS_STORE_IMG_DIR_.$obj->id.'.jpg';
        $image_url = ImageManager::thumbnail($image, $this->table.'_'.(int)$obj->id.'.'.$this->imageType, 350,
            $this->imageType, true, true);
        $image_size = file_exists($image) ? filesize($image) / 1000 : false;

        $tmp_addr = new Address();
        $res = $tmp_addr->getFieldsRequiredDatabase();
        $required_fields = array();
        foreach ($res as $row) {
            $required_fields[(int)$row['id_required_field']] = $row['field_name'];
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Stores'),
                'icon' => 'icon-home'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'required' => false,
                    'hint' => array(
                        $this->l('Store name (e.g. City Center Mall Store).'),
                        $this->l('Allowed characters: letters, spaces and %s')
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Address'),
                    'name' => 'address1',
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Address (2)'),
                    'name' => 'address2'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Zip/postal Code'),
                    'name' => 'postcode',
                    'required' => in_array('postcode', $required_fields)
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('City'),
                    'name' => 'city',
                    'required' => true
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Country'),
                    'name' => 'id_country',
                    'required' => true,
                    'default_value' => (int)$this->context->country->id,
                    'options' => array(
                        'query' => Country::getCountries($this->context->language->id),
                        'id' => 'id_country',
                        'name' => 'name',
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('State'),
                    'name' => 'id_state',
                    'required' => true,
                    'options' => array(
                        'id' => 'id_state',
                        'name' => 'name',
                        'query' => null
                    )
                ),
                array(
                    'type' => 'latitude',
                    'label' => $this->l('Latitude / Longitude'),
                    'name' => 'latitude',
                    'required' => true,
                    'maxlength' => 12,
                    'hint' => $this->l('Store coordinates (e.g. 45.265469/-47.226478).')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Phone'),
                    'name' => 'phone'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Fax'),
                    'name' => 'fax'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Email address'),
                    'name' => 'email'
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Note'),
                    'name' => 'note',
                    'cols' => 42,
                    'rows' => 4
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Status'),
                    'name' => 'active',
                    'required' => false,
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
                    ),
                    'hint' => $this->l('Whether or not to display this store.')
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Picture'),
                    'name' => 'image',
                    'display_image' => true,
                    'image' => $image_url ? $image_url : false,
                    'size' => $image_size,
                    'hint' => $this->l('Storefront picture.')
                )
            ),
            'hours' => array(
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            );
        }

        $days = array();
        $days[1] = $this->l('Monday');
        $days[2] = $this->l('Tuesday');
        $days[3] = $this->l('Wednesday');
        $days[4] = $this->l('Thursday');
        $days[5] = $this->l('Friday');
        $days[6] = $this->l('Saturday');
        $days[7] = $this->l('Sunday');

        $hours = $this->getFieldValue($obj, 'hours');
        if (!empty($hours)) {
            $hours_unserialized = Tools::unSerialize($hours);
        }

        $this->fields_value = array(
            'latitude' => $this->getFieldValue($obj, 'latitude') ? $this->getFieldValue($obj, 'latitude') : Configuration::get('PS_STORES_CENTER_LAT'),
            'longitude' => $this->getFieldValue($obj, 'longitude') ? $this->getFieldValue($obj, 'longitude') : Configuration::get('PS_STORES_CENTER_LONG'),
            'days' => $days,
            'hours' => isset($hours_unserialized) ? $hours_unserialized : false
        );

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (isset($_POST['submitAdd'.$this->table])) {
            /* Cleaning fields */
            foreach ($_POST as $kp => $vp) {
                if (!in_array($kp, array('checkBoxShopGroupAsso_store', 'checkBoxShopAsso_store'))) {
                    $_POST[$kp] = trim($vp);
                }
            }

            /* Rewrite latitude and longitude to 8 digits */
            $_POST['latitude'] = number_format((float)$_POST['latitude'], 8);
            $_POST['longitude'] = number_format((float)$_POST['longitude'], 8);

            /* If the selected country does not contain states */
            $id_state = (int)Tools::getValue('id_state');
            $id_country = (int)Tools::getValue('id_country');
            $country = new Country((int)$id_country);

            if ($id_country && $country && !(int)$country->contains_states && $id_state) {
                $this->errors[] = Tools::displayError('You\'ve selected a state for a country that does not contain states.');
            }

            /* If the selected country contains states, then a state have to be selected */
            if ((int)$country->contains_states && !$id_state) {
                $this->errors[] = Tools::displayError('An address located in a country containing states must have a state selected.');
            }

            $latitude = (float)Tools::getValue('latitude');
            $longitude = (float)Tools::getValue('longitude');

            if (empty($latitude) || empty($longitude)) {
                $this->errors[] = Tools::displayError('Latitude and longitude are required.');
            }

            $postcode = Tools::getValue('postcode');
            /* Check zip code format */
            if ($country->zip_code_format && !$country->checkZipCode($postcode)) {
                $this->errors[] = Tools::displayError('Your Zip/postal code is incorrect.').'<br />'.Tools::displayError('It must be entered as follows:').' '.str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format)));
            } elseif (empty($postcode) && $country->need_zip_code) {
                $this->errors[] = Tools::displayError('A Zip/postal code is required.');
            } elseif ($postcode && !Validate::isPostCode($postcode)) {
                $this->errors[] = Tools::displayError('The Zip/postal code is invalid.');
            }

            /* Store hours */
            $_POST['hours'] = array();
            for ($i = 1; $i < 8; $i++) {
                $_POST['hours'][] .= Tools::getValue('hours_'.(int)$i);
            }
            $_POST['hours'] = serialize($_POST['hours']);
        }

        if (!count($this->errors)) {
            parent::postProcess();
        } else {
            $this->display = 'add';
        }
    }

    protected function postImage($id)
    {
        $ret = parent::postImage($id);
        $generate_hight_dpi_images = (bool)Configuration::get('PS_HIGHT_DPI');

        if (($id_store = (int)Tools::getValue('id_store')) && isset($_FILES) && count($_FILES) && file_exists(_PS_STORE_IMG_DIR_.$id_store.'.jpg')) {
            $images_types = ImageType::getImagesTypes('stores');
            foreach ($images_types as $k => $image_type) {
                ImageManager::resize(_PS_STORE_IMG_DIR_.$id_store.'.jpg',
                    _PS_STORE_IMG_DIR_.$id_store.'-'.stripslashes($image_type['name']).'.jpg',
                    (int)$image_type['width'], (int)$image_type['height']
                );

                if ($generate_hight_dpi_images) {
                    ImageManager::resize(_PS_STORE_IMG_DIR_.$id_store.'.jpg',
                        _PS_STORE_IMG_DIR_.$id_store.'-'.stripslashes($image_type['name']).'2x.jpg',
                        (int)$image_type['width']*2, (int)$image_type['height']*2
                    );
                }
            }
        }
        return $ret;
    }

    protected function _getDefaultFieldsContent()
    {
        $this->context = Context::getContext();
        $countryList = array();
        $countryList[] = array('id' => '0', 'name' => $this->l('Choose your country'));
        foreach (Country::getCountries($this->context->language->id) as $country) {
            $countryList[] = array('id' => $country['id_country'], 'name' => $country['name']);
        }
        $stateList = array();
        $stateList[] = array('id' => '0', 'name' => $this->l('Choose your state (if applicable)'));
        foreach (State::getStates($this->context->language->id) as $state) {
            $stateList[] = array('id' => $state['id_state'], 'name' => $state['name']);
        }

        $formFields = array(
            'PS_SHOP_NAME' => array(
                'title' => $this->l('Shop name'),
                'hint' => $this->l('Displayed in emails and page titles.'),
                'validation' => 'isGenericName',
                'required' => true,
                'type' => 'text',
                'no_escape' => true,
            ),
            'PS_SHOP_EMAIL' => array('title' => $this->l('Shop email'),
                'hint' => $this->l('Displayed in emails sent to customers.'),
                'validation' => 'isEmail',
                'required' => true,
                'type' => 'text'
            ),
            'PS_SHOP_DETAILS' => array(
                'title' => $this->l('Registration number'),
                'hint' => $this->l('Shop registration information (e.g. SIRET or RCS).'),
                'validation' => 'isGenericName',
                'type' => 'textarea',
                'cols' => 30,
                'rows' => 5
            ),
            'PS_SHOP_ADDR1' => array(
                'title' => $this->l('Shop address line 1'),
                'validation' => 'isAddress',
                'type' => 'text'
            ),
            'PS_SHOP_ADDR2' => array(
                'title' => $this->l('Shop address line 2'),
                'validation' => 'isAddress',
                'type' => 'text'
            ),
            'PS_SHOP_CODE' => array(
                'title' => $this->l('Zip/postal code'),
                'validation' => 'isGenericName',
                'type' => 'text'
            ),
            'PS_SHOP_CITY' => array(
                'title' => $this->l('City'),
                'validation' => 'isGenericName',
                'type' => 'text'
            ),
            'PS_SHOP_COUNTRY_ID' => array(
                'title' => $this->l('Country'),
                'validation' => 'isInt',
                'type' => 'select',
                'list' => $countryList,
                'identifier' => 'id',
                'cast' => 'intval',
                'defaultValue' => (int)$this->context->country->id
            ),
            'PS_SHOP_STATE_ID' => array(
                'title' => $this->l('State'),
                'validation' => 'isInt',
                'type' => 'select',
                'list' => $stateList,
                'identifier' => 'id',
                'cast' => 'intval'
            ),
            'PS_SHOP_PHONE' => array(
                'title' => $this->l('Phone'),
                'validation' => 'isGenericName',
                'type' => 'text'
            ),
            'PS_SHOP_FAX' => array(
                'title' => $this->l('Fax'),
                'validation' => 'isGenericName',
                'type' => 'text'
            ),
        );

        return $formFields;
    }

    protected function _buildOrderedFieldsShop($formFields)
    {
        // You cannot do that, because the fields must be sorted for the country you've selected.
        // Simple example: the current country is France, where we don't display the state. You choose "US" as a country in the form. The state is not dsplayed at the right place...

        // $associatedOrderKey = array(
            // 'PS_SHOP_NAME' => 'company',
            // 'PS_SHOP_ADDR1' => 'address1',
            // 'PS_SHOP_ADDR2' => 'address2',
            // 'PS_SHOP_CITY' => 'city',
            // 'PS_SHOP_STATE_ID' => 'State:name',
            // 'PS_SHOP_CODE' => 'postcode',
            // 'PS_SHOP_COUNTRY_ID' => 'Country:name',
            // 'PS_SHOP_PHONE' => 'phone');
        // $fields = array();
        // $orderedFields = AddressFormat::getOrderedAddressFields(Configuration::get('PS_SHOP_COUNTRY_ID'), false, true);
        // foreach ($orderedFields as $lineFields)
            // if (($patterns = explode(' ', $lineFields)))
                // foreach ($patterns as $pattern)
                    // if (($key = array_search($pattern, $associatedOrderKey)))
                        // $fields[$key] = $formFields[$key];
        // foreach ($formFields as $key => $value)
            // if (!isset($fields[$key]))
                // $fields[$key] = $formFields[$key];

        $fields = $formFields;
        $this->fields_options['contact'] = array(
            'title' =>    $this->l('Contact details'),
            'icon' =>    'icon-user',
            'fields' =>    $fields,
            'submit' => array('title' => $this->l('Save'))
        );
    }

    public function beforeUpdateOptions()
    {
        if (isset($_POST['PS_SHOP_STATE_ID']) && $_POST['PS_SHOP_STATE_ID'] != '0') {
            $sql = 'SELECT `active` FROM `'._DB_PREFIX_.'state`
					WHERE `id_country` = '.(int)Tools::getValue('PS_SHOP_COUNTRY_ID').'
						AND `id_state` = '.(int)Tools::getValue('PS_SHOP_STATE_ID');
            $isStateOk = Db::getInstance()->getValue($sql);
            if ($isStateOk != 1) {
                $this->errors[] = Tools::displayError('The specified state is not located in this country.');
            }
        }
    }

    public function updateOptionPsShopCountryId($value)
    {
        if (!$this->errors && $value) {
            $country = new Country($value, $this->context->language->id);
            if ($country->id) {
                Configuration::updateValue('PS_SHOP_COUNTRY_ID', $value);
                Configuration::updateValue('PS_SHOP_COUNTRY', pSQL($country->name));
            }
        }
    }

    public function updateOptionPsShopStateId($value)
    {
        if (!$this->errors && $value) {
            $state = new State($value);
            if ($state->id) {
                Configuration::updateValue('PS_SHOP_STATE_ID', $value);
                Configuration::updateValue('PS_SHOP_STATE', pSQL($state->name));
            }
        }
    }
}
