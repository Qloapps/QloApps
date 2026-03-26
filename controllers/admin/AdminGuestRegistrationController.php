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

class AdminGuestRegistrationControllerCore extends AdminController
{
    const CONF_ENABLE_HEADER = 'QLO_GUEST_REG_ENABLE_HEADER';
    const CONF_OPTIONAL_SECTIONS = 'QLO_GUEST_REG_OPTIONAL_SECTIONS';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->table = 'guest_reg_purpose';
        $this->className = 'GuestRegPurpose';
        $this->identifier = 'id_guest_reg_purpose';
        $this->lang = true;
        $this->override_folder = 'guest_registration'.DIRECTORY_SEPARATOR;

        parent::__construct();

        $this->fields_options = array(
            'general' => array(
                'title' => $this->l('Guest Registration Card settings'),
                'icon' => 'icon-cogs',
                'fields' => array(
                    self::CONF_ENABLE_HEADER => array(
                        'title' => $this->l('Enable PDF header'),
                        'desc' => $this->l('Display the standard QloApps PDF header on the Guest Registration Card.'),
                        'cast' => 'intval',
                        'type' => 'bool',
                    ),
                    self::CONF_OPTIONAL_SECTIONS => array(
                        'title' => $this->l('Optional sections'),
                        'desc' => $this->l('Choose which optional sections should appear on the Guest Registration Card.'),
                        'type' => 'checkbox',
                        'auto_value' => false,
                        'choices' => $this->getOptionalSectionChoices(),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    public function initContent()
    {
        $this->display = 'options';
        $this->initToolbar();
        $this->initPageHeaderToolbar();

        $this->content = $this->renderOptions();
        $this->content .= $this->renderDynamicFieldManagement();

        $this->context->smarty->assign(array(
            'content' => $this->content,
            'url_post' => self::$currentIndex.'&token='.$this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
        ));
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = array($this->l('Guest Registration Card'));
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        unset($this->page_header_toolbar_btn['cancel']);
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitBulkGuestRegistrationValues')) {
            $this->processBulkDynamicFieldSave();

            if (!count($this->errors)) {
                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&conf=4');
            }

            return;
        }

        parent::postProcess();
    }

    public function renderOptions()
    {
        $this->fields_options['general']['fields'][self::CONF_OPTIONAL_SECTIONS]['value'] = '';

        $html = parent::renderOptions();
        $selectedSections = Tools::jsonEncode($this->getSelectedOptionalSections());

        $html .= '
        <script type="text/javascript">
            $(document).ready(function() {
                var selectedSections = '.$selectedSections.';
                $("input[name=\"'.self::CONF_OPTIONAL_SECTIONS.'\"]").each(function() {
                    var checkboxValue = parseInt($(this).val(), 10);
                    $(this).attr("name", "'.self::CONF_OPTIONAL_SECTIONS.'[]");
                    if ($.inArray(checkboxValue, selectedSections) !== -1) {
                        $(this).prop("checked", true);
                    }
                });
            });
        </script>';

        return $html;
    }

    protected function updateOptionQloGuestRegOptionalSections($value)
    {
        if (!is_array($value)) {
            $value = array();
        }

        $allowed = array_map('intval', array_keys($this->getOptionalSectionChoices()));
        $selected = array();

        foreach ($value as $sectionId) {
            $sectionId = (int)$sectionId;
            if (in_array($sectionId, $allowed)) {
                $selected[] = $sectionId;
            }
        }

        Configuration::updateValue(self::CONF_OPTIONAL_SECTIONS, Tools::jsonEncode(array_values(array_unique($selected))));
    }

    protected function renderDynamicFieldManagement()
    {
        $languages = Language::getLanguages(false);
        $defaultFormLanguage = (int)Configuration::get('PS_LANG_DEFAULT');

        $this->context->smarty->assign(array(
            'languages' => $languages,
            'default_form_language' => $defaultFormLanguage,
            'dynamic_form_action' => self::$currentIndex.'&token='.$this->token,
            'guest_reg_purpose_rows' => $this->getEntityRows('purpose'),
            'guest_reg_id_proof_rows' => $this->getEntityRows('id_proof'),
            'guest_reg_payment_method_rows' => $this->getEntityRows('payment_method'),
        ));

        return $this->createTemplate('dynamic_tables.tpl')->fetch();
    }

    protected function processBulkDynamicFieldSave()
    {
        $languages = Language::getLanguages(false);
        $savedRows = array();

        foreach ($this->getManagedEntities() as $entityKey => $entity) {
            $postedRows = Tools::getValue($entity['table'], array());
            if (!is_array($postedRows)) {
                $postedRows = array();
            }

            $savedRows[$entityKey] = $this->prepareEntityRowsForSave($entityKey, $postedRows, $languages);
        }

        if (count($this->errors)) {
            return false;
        }

        foreach ($savedRows as $entityKey => $rows) {
            $this->persistEntityRows($entityKey, $rows);
        }

        return true;
    }

    protected function prepareEntityRowsForSave($entityKey, $postedRows, $languages)
    {
        $entity = $this->getManagedEntity($entityKey);
        $rows = array();
        
        // Get the system's default language ID
        $idLangDefault = (int)Configuration::get('PS_LANG_DEFAULT');

        foreach ($postedRows as $rowKey => $row) {
            if (!is_array($row)) {
                continue;
            }

            if ($this->isEmptyEntityRow($row, $languages)) {
                continue;
            }

            $preparedRow = array(
                'id' => !empty($row['id']) ? (int)$row['id'] : 0,
                'active' => !empty($row['active']) ? 1 : 0,
                'name' => array(),
            );

            // Capture the default language value first to use as a fallback
            $defaultValue = '';
            if (isset($row['name'][$idLangDefault])) {
                $defaultValue = trim($row['name'][$idLangDefault]);
            }

            foreach ($languages as $language) {
                $idLang = (int)$language['id_lang'];
                $value = '';
                
                if (isset($row['name'][$idLang])) {
                    $value = trim($row['name'][$idLang]);
                }

                // AUTOMATIC FALLBACK: If current language is blank, use the default language text
                if ($value === '' && $defaultValue !== '') {
                    $value = $defaultValue;
                }

                // Validation
                if ($value === '') {
                    $this->errors[] = sprintf($this->l('%s: name is required for %s (or for the default language).'), $entity['title'], $language['name']);
                } elseif (!Validate::isCatalogName($value)) {
                    $this->errors[] = sprintf($this->l('%s: name is invalid for %s.'), $entity['title'], $language['name']);
                }

                $preparedRow['name'][$idLang] = $value;
            }

            $rows[$rowKey] = $preparedRow;
        }

        return $rows;
    }

    protected function persistEntityRows($entityKey, $rows)
    {
        $entity = $this->getManagedEntity($entityKey);
        $existingIds = $this->getEntityIds($entityKey);
        $savedIds = array();

        foreach ($rows as $row) {
            $object = $row['id'] ? new $entity['class']((int)$row['id']) : new $entity['class']();
            $object->active = (int)$row['active'];
            $object->name = $row['name'];

            if (!$object->save()) {
                $this->errors[] = sprintf($this->l('An error occurred while saving %s.'), Tools::strtolower($entity['title']));
                continue;
            }

            $savedIds[] = (int)$object->id;
        }

        if (count($this->errors)) {
            return false;
        }

        foreach (array_diff($existingIds, $savedIds) as $idToDelete) {
            $object = new $entity['class']((int)$idToDelete);
            if (Validate::isLoadedObject($object)) {
                $object->delete();
            }
        }

        return true;
    }

    protected function getEntityRows($entityKey)
    {
        $entity = $this->getManagedEntity($entityKey);
        $languages = Language::getLanguages(false);
        $rows = array();

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT a.`'.$entity['identifier'].'`, a.`active`, a.`date_add`, al.`id_lang`, al.`name`
            FROM `'._DB_PREFIX_.$entity['table'].'` a
            LEFT JOIN `'._DB_PREFIX_.$entity['table'].'_lang` al
                ON (a.`'.$entity['identifier'].'` = al.`'.$entity['identifier'].'`)
            ORDER BY a.`'.$entity['identifier'].'` ASC'
        );

        if ($result) {
            foreach ($result as $row) {
                $idEntity = (int)$row[$entity['identifier']];
                if (!isset($rows[$idEntity])) {
                    $rows[$idEntity] = array(
                        'id' => $idEntity,
                        'active' => (int)$row['active'],
                        'date_add' => $row['date_add'],
                        'name' => array(),
                    );

                    foreach ($languages as $language) {
                        $rows[$idEntity]['name'][(int)$language['id_lang']] = '';
                    }
                }

                if ((int)$row['id_lang']) {
                    $rows[$idEntity]['name'][(int)$row['id_lang']] = $row['name'];
                }
            }
        }

        return array_values($rows);
    }

    protected function getManagedEntity($entityKey)
    {
        $entities = $this->getManagedEntities();

        return $entities[$entityKey];
    }

    protected function getManagedEntities()
    {
        return array(
            'purpose' => array(
                'class' => 'GuestRegPurpose',
                'table' => 'guest_reg_purpose',
                'identifier' => 'id_guest_reg_purpose',
                'title' => $this->l('Purpose of visit'),
            ),
            'id_proof' => array(
                'class' => 'GuestRegIdProof',
                'table' => 'guest_reg_id_proof',
                'identifier' => 'id_guest_reg_id_proof',
                'title' => $this->l('Identity proof'),
            ),
            'payment_method' => array(
                'class' => 'GuestRegPaymentMethod',
                'table' => 'guest_reg_payment_method',
                'identifier' => 'id_guest_reg_payment_method',
                'title' => $this->l('Payment method'),
            ),
        );
    }

    protected function getOptionalSectionChoices()
    {
        return array(
            1 => $this->l('Additional Guests'),
            2 => $this->l('Billing & Corporate Details'),
            3 => $this->l('Payment & Deposit'),
            4 => $this->l('Property Regulations'),
            5 => $this->l('For Office Use Only'),
            6 => $this->l('Footer'),
        );
    }

    protected function getSelectedOptionalSections()
    {
        $value = Configuration::get(self::CONF_OPTIONAL_SECTIONS);

        if ($value === false || $value === '') {
            return array(1, 2, 3, 4, 5, 6);
        }

        $value = Tools::jsonDecode($value, true);

        if (!is_array($value)) {
            return array();
        }

        return array_map('intval', $value);
    }

    protected function getEntityIds($entityKey)
    {
        $entity = $this->getManagedEntity($entityKey);
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT `'.$entity['identifier'].'`
            FROM `'._DB_PREFIX_.$entity['table'].'`'
        );

        if (!$result) {
            return array();
        }

        return array_map('intval', array_column($result, $entity['identifier']));
    }

    protected function isEmptyEntityRow($row, $languages)
    {
        if (!empty($row['id'])) {
            return false;
        }

        if (!isset($row['name']) || !is_array($row['name'])) {
            return true;
        }

        foreach ($languages as $language) {
            $idLang = (int)$language['id_lang'];
            if (!empty($row['name'][$idLang])) {
                return false;
            }
        }

        return true;
    }
}
