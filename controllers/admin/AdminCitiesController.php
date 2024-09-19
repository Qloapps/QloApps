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
 * @property City $object
 */
class AdminCitiesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'city';
        $this->className = 'City';
        $this->lang = false;
        $this->requiredDatabase = true;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->context = Context::getContext();

        if (!Tools::getValue('realedit')) {
            $this->deleted = false;
        }

        $this->bulk_actions = array(
            'delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')),
            'affectstate' => array('text' => $this->l('Assign a new state'))
        );

        $this->_select = 's.`name` AS state, cl.`name` AS country';
        $this->_join = '
		LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_state` = a.`id_state`)
		LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (cl.`id_country` = a.`id_country` AND cl.id_lang = '.(int)$this->context->language->id.')';
        $this->_use_found_rows = false;

        $countries_array = $states_array = array();

        $this->countries = Country::getCountries($this->context->language->id, false, true, false);

        foreach ($this->countries as $country) {
            $countries_array[$country['id_country']] = $country['name'];
        }
		
		$id_country = $this->context->cookie->getAll()['citiescityFilter_cl!id_country'];
		if($id_country > 0) {
			$this->states = State::getStatesByIdCountry($id_country, true);
		} else {
			$this->states = State::getStates(false, true);
		}
		
		foreach ($this->states as $state) {
            $states_array[$state['id_state']] = $state['name'];
        }
		
        $this->fields_list = array(
            'id_city' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'filter_key' => 'a!name'
            ),
            'iata_code' => array(
                'title' => $this->l('IATA code'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'country' => array(
                'title' => $this->l('Country'),
                'type' => 'select',
                'list' => $countries_array,
                'filter_key' => 'cl!id_country',
                'filter_type' => 'int',
                'order_key' => 'country'
            ),
            'state' => array(
                'title' => $this->l('State'),
                'type' => 'select',
                'list' => $states_array,
                'filter_key' => 's!id_state',
                'filter_type' => 'int',
                'order_key' => 'state'
            ),
            'active' => array(
                'title' => $this->l('Enabled'),
                'active' => 'status',
                'filter_key' => 'a!active',
                'align' => 'center',
                'type' => 'bool',
                'orderby' => false,
                'class' => 'fixed-width-sm'
            )
        );

        parent::__construct();
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_city'] = array(
                'href' => self::$currentIndex.'&addcity&token='.$this->token,
                'desc' => $this->l('Add new city', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Cities'),
                'icon' => 'icon-globe'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'maxlength' => 32,
                    'required' => true,
                    'hint' => $this->l('Provide the City name to be display in addresses and on invoices.')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('IATA code'),
                    'name' => 'iata_code',
                    'maxlength' => 7,
                    'required' => false,
                    'class' => 'uppercase',
                    'hint' => $this->l('1 to 3 letter IATA code.')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Country'),
                    'name' => 'id_country',
                    'required' => true,
                    'default_value' => (int)$this->context->country->id,
                    'options' => array(
                        'query' => Country::getCountries($this->context->language->id, false, true),
                        'id' => 'id_country',
                        'name' => 'name',
                    ),
                    'hint' => $this->l('Country where the city is located.').' '.$this->l('Only the countries with the option "contains cities" enabled are displayed.')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('State'),
                    'name' => 'id_state',
                    'required' => true,
                    'options' => array(
                        'query' => State::getStates(false, true),
                        'id' => 'id_state',
                        'name' => 'name'
                    ),
                    'hint' => array(
                        $this->l('Geographical region where this city is located.'),
                        $this->l('Used for shipping')
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Status'),
                    'name' => 'active',
                    'required' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => '<img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" />'
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => '<img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" />'
                        )
                    )
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit($this->table.'Orderby') || Tools::isSubmit($this->table.'Orderway')) {
            $this->filter = true;
        }

        /* Delete city */
        if (Tools::isSubmit('delete'.$this->table)) {
            if ($this->tabAccess['delete'] === '1') {
                if (Validate::isLoadedObject($object = $this->loadObject())) {
                    /** @var City $object */
                    if (!$object->isUsed()) {
                        if ($object->delete()) {
                            Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.(Tools::getValue('token') ? Tools::getValue('token') : $this->token));
                        }
                        $this->errors[] = Tools::displayError('An error occurred during deletion.');
                    } else {
                        $this->errors[] = Tools::displayError('This city was used in at least one address. It cannot be removed.');
                    }
                } else {
                    $this->errors[] = Tools::displayError('An error occurred while deleting the object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to delete this.');
            }
        }

        if (!count($this->errors)) {
            parent::postProcess();
        }
    }

    protected function displayAjaxCities()
    {
        $cities = Db::getInstance()->executeS('
		SELECT cc.id_city, cc.name
		FROM '._DB_PREFIX_.'city cc
		LEFT JOIN '._DB_PREFIX_.'country c ON (cc.`id_country` = c.`id_country`)
		WHERE cc.id_country = '.(int)(Tools::getValue('id_country')).' AND cc.active = 1 AND c.`contains_cities` = 1
		ORDER BY cc.`name` ASC');

        if (is_array($cities) and !empty($cities)) {
            $list = '';
            if ((bool)Tools::getValue('no_empty') != true) {
                $empty_value = (Tools::isSubmit('empty_value')) ? Tools::getValue('empty_value') : '-';
                $list = '<option value="0">'.Tools::htmlentitiesUTF8($empty_value).'</option>'."\n";
            }

            foreach ($cities as $city) {
                $list .= '<option value="'.(int)($city['id_city']).'"'.((isset($_GET['id_city']) and $_GET['id_city'] == $city['id_city']) ? ' selected="selected"' : '').'>'.$city['name'].'</option>'."\n";
            }
        } else {
            $list = 'false';
        }

        die($list);
    }
}
