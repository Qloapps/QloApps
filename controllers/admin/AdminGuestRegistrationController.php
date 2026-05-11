<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License version 3.0
 * that is bundled with this package in the file LICENSE.md
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/license/osl-3.0-php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@qloapps.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your needs
 * please refer to https://store.webkul.com/customisation-guidelines for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/license/osl-3.0-php Open Software License version 3.0
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
        $this->className = 'GuestVisitPurpose';
        $this->identifier = 'id_guest_reg_purpose';
        $this->lang = true;

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
        $this->content .= $this->renderRegForm();

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
            $languages = Language::getLanguages(false);
            $regFields = $this->getRegFieldTypes();
            $idLangDefault = (int)Configuration::get('PS_LANG_DEFAULT');
            $validatedRows = array();

            // Validate all field types first, collect errors before saving anything
            foreach ($regFields as $fieldKey => $fieldConfig) {
                $postedRows = Tools::getValue($fieldConfig['table'], array());
                if (!is_array($postedRows)) {
                    $postedRows = array();
                }

                $rows = array();
                foreach ($postedRows as $rowKey => $row) {
                    if (!is_array($row)) {
                        continue;
                    }

                    // Skip blank new rows (no ID and no name in any language)
                    if (empty($row['id'])) {
                        $hasName = false;
                        if (isset($row['name']) && is_array($row['name'])) {
                            foreach ($languages as $language) {
                                if (!empty($row['name'][(int)$language['id_lang']])) {
                                    $hasName = true;
                                    break;
                                }
                            }
                        }
                        if (!$hasName) {
                            continue;
                        }
                    }

                    $validRow = array(
                        'id'     => !empty($row['id']) ? (int)$row['id'] : 0,
                        'active' => !empty($row['active']) ? 1 : 0,
                        'name'   => array(),
                    );

                    $defaultName = isset($row['name'][$idLangDefault]) ? trim($row['name'][$idLangDefault]) : '';

                    foreach ($languages as $language) {
                        $idLang = (int)$language['id_lang'];
                        $name = isset($row['name'][$idLang]) ? trim($row['name'][$idLang]) : '';

                        if ($name === '' && $defaultName !== '') {
                            $name = $defaultName;
                        }

                        if ($name === '') {
                            $this->errors[] = sprintf(
                                $this->l('%s: name is required for %s (or for the default language).'),
                                $fieldConfig['title'],
                                $language['name']
                            );
                        } elseif (!Validate::isCatalogName($name)) {
                            $this->errors[] = sprintf(
                                $this->l('%s: name is invalid for %s.'),
                                $fieldConfig['title'],
                                $language['name']
                            );
                        }

                        $validRow['name'][$idLang] = $name;
                    }

                    $rows[$rowKey] = $validRow;
                }

                $validatedRows[$fieldKey] = $rows;
            }

            // Save only when validation passes for all types
            if (!count($this->errors)) {
                foreach ($regFields as $fieldKey => $fieldConfig) {
                    $className = $fieldConfig['class'];
                    $idMethod = $fieldConfig['ids_fn'];
                    $existingIds = $className::$idMethod();
                    $savedIds = array();
                    $saveFailed = false;

                    foreach ($validatedRows[$fieldKey] as $row) {
                        $object = $row['id'] ? new $className((int)$row['id']) : new $className();
                        $object->active = (int)$row['active'];
                        $object->name = $row['name'];

                        if (!$object->save()) {
                            $this->errors[] = sprintf(
                                $this->l('An error occurred while saving %s.'),
                                Tools::strtolower($fieldConfig['title'])
                            );
                            $saveFailed = true;
                            continue;
                        }

                        $savedIds[] = (int)$object->id;
                    }

                    if (!$saveFailed) {
                        foreach (array_diff($existingIds, $savedIds) as $idToDelete) {
                            $object = new $className((int)$idToDelete);
                            if (Validate::isLoadedObject($object)) {
                                $object->delete();
                            }
                        }
                    }
                }
            }

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

        $value = Configuration::get(self::CONF_OPTIONAL_SECTIONS);
        if ($value === false || $value === '') {
            $selectedSections = array(1, 2, 3, 4, 5, 6);
        } else {
            $decoded = Tools::jsonDecode($value, true);
            $selectedSections = is_array($decoded) ? array_map('intval', $decoded) : array();
        }

        $this->context->smarty->assign(array(
            'guest_reg_preview_img_url'   => $this->context->link->getBaseLink().'img/admin/guest_registration_preview.jpg',
            'guest_reg_selected_sections' => $selectedSections,
            'guest_reg_conf_key'          => self::CONF_OPTIONAL_SECTIONS,
            'guest_reg_preview_title'     => $this->l('Guest Registration Card Preview'),
            'guest_reg_preview_btn_title' => $this->l('Preview'),
        ));

        $html .= $this->createTemplate('preview_modal.tpl')->fetch();

        return $html;
    }

    /**
     * Called automatically by AdminController::processUpdateOptions() via
     * 'updateOption' + Tools::toCamelCase('QLO_GUEST_REG_OPTIONAL_SECTIONS').
     * Validates and stores the selected optional section IDs.
     */
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

    protected function renderRegForm()
    {
        $languages = Language::getLanguages(false);
        $regFields = $this->getRegFieldTypes();

        $this->context->smarty->assign(array(
            'languages'                     => $languages,
            'default_form_language'         => (int)Configuration::get('PS_LANG_DEFAULT'),
            'dynamic_form_action'           => self::$currentIndex.'&token='.$this->token,
            'guest_reg_purpose_rows'        => $this->getRegFieldRows($regFields['purpose'], $languages),
            'guest_reg_id_proof_rows'       => $this->getRegFieldRows($regFields['id_proof'], $languages),
            'guest_reg_payment_method_rows' => $this->getRegFieldRows($regFields['payment_method'], $languages),
        ));

        return $this->createTemplate('dynamic_tables.tpl')->fetch();
    }

    /**
     * Returns display rows for one guest registration field type.
     * On validation error repopulates from posted data; otherwise fetches from the database.
     * Called 3× from renderRegForm(), one per field type.
     */
    protected function getRegFieldRows(array $fieldConfig, array $languages): array
    {
        if (Tools::isSubmit('submitBulkGuestRegistrationValues') && count($this->errors)) {
            $postedRows = Tools::getValue($fieldConfig['table'], array());
            $rows = array();

            if (!is_array($postedRows)) {
                return $rows;
            }

            foreach ($postedRows as $rowKey => $row) {
                if (!is_array($row)) {
                    continue;
                }

                $rowKey = (string)$rowKey;
                $formKey = preg_match('/^[a-zA-Z0-9_-]+$/', $rowKey) ? $rowKey : 'new_'.time();

                $displayRow = array(
                    'id'       => !empty($row['id']) ? (int)$row['id'] : 0,
                    'form_key' => $formKey,
                    'active'   => !empty($row['active']) ? 1 : 0,
                    'date_add' => '',
                    'name'     => array(),
                );

                foreach ($languages as $language) {
                    $idLang = (int)$language['id_lang'];
                    $displayRow['name'][$idLang] = isset($row['name'][$idLang]) ? (string)$row['name'][$idLang] : '';
                }

                $rows[] = $displayRow;
            }

            return $rows;
        }

        $className = $fieldConfig['class'];
        $rowsMethod = $fieldConfig['rows_fn'];

        return $className::$rowsMethod();
    }

    /**
     * @return array  Config for each guest registration field type managed on this page
     */
    protected function getRegFieldTypes(): array
    {
        return array(
            'purpose' => array(
                'class'      => 'GuestVisitPurpose',
                'table'      => 'guest_reg_purpose',
                'identifier' => 'id_guest_reg_purpose',
                'title'      => $this->l('Purpose of visit'),
                'rows_fn'    => 'getGuestVisitPurposeRows',
                'ids_fn'     => 'getGuestVisitPurposeIds',
            ),
            'id_proof' => array(
                'class'      => 'GuestRegistrationIdProof',
                'table'      => 'guest_reg_id_proof',
                'identifier' => 'id_guest_reg_id_proof',
                'title'      => $this->l('Identity proof'),
                'rows_fn'    => 'getRegistrationIdProofRows',
                'ids_fn'     => 'getRegistrationIdProofIds',
            ),
            'payment_method' => array(
                'class'      => 'GuestRegistrationPaymentMethod',
                'table'      => 'guest_reg_payment_method',
                'identifier' => 'id_guest_reg_payment_method',
                'title'      => $this->l('Payment method'),
                'rows_fn'    => 'getRegistrationPaymentMethodRows',
                'ids_fn'     => 'getRegistrationPaymentMethodIds',
            ),
        );
    }

    /**
     * @return array  Section ID → label for the optional-sections checkbox group
     */
    protected function getOptionalSectionChoices(): array
    {
        return array(
            1 => $this->l('Property Logo'),
            2 => $this->l('Additional Guests'),
            3 => $this->l('Billing & Corporate Details'),
            4 => $this->l('Payment & Deposit'),
            5 => $this->l('Property Regulations'),
            6 => $this->l('For Office Use Only'),
        );
    }
}
