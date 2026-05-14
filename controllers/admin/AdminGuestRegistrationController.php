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
        $this->table = 'guest_visit_purpose';
        $this->className = 'GuestVisitPurpose';
        $this->identifier = 'id_guest_visit_purpose';
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
        $this->content .= $this->renderRegistrationForm();

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
            $idLangDefault = (int)Configuration::get('PS_LANG_DEFAULT');

            $guestRegPurposeData = $this->validateRegistrationFieldRows(
                (array)Tools::getValue('guest_visit_purpose', array()),
                $languages,
                $idLangDefault,
                $this->l('Purpose of visit')
            );
            $idProofData = $this->validateRegistrationFieldRows(
                (array)Tools::getValue('reg_id_proof', array()),
                $languages,
                $idLangDefault,
                $this->l('Identity proof')
            );
            $guestRegPaymentMethodData = $this->validateRegistrationFieldRows(
                (array)Tools::getValue('guest_reg_payment_method', array()),
                $languages,
                $idLangDefault,
                $this->l('Payment method')
            );

            if (!count($this->errors)) {
                $savedPurposeIds = array();
                foreach ($guestRegPurposeData as $row) {
                    $objGuestVisitPurpose = $row['id'] ? new GuestVisitPurpose((int)$row['id']) : new GuestVisitPurpose();
                    $objGuestVisitPurpose->active = $row['active'];
                    $objGuestVisitPurpose->name = $row['name'];
                    if ($objGuestVisitPurpose->save()) {
                        $savedPurposeIds[] = (int)$objGuestVisitPurpose->id;
                    } else {
                        $this->errors[] = $this->l('An error occurred while saving purpose of visit.');
                    }
                }
                foreach (array_diff(GuestVisitPurpose::getGuestVisitPurposeIds(), $savedPurposeIds) as $id) {
                    $objGuestVisitPurpose = new GuestVisitPurpose((int)$id);
                    $objGuestVisitPurpose->delete();
                }

                $savedIdProofIds = array();
                foreach ($idProofData as $row) {
                    $objIdProof = $row['id'] ? new IdProof((int)$row['id']) : new IdProof();
                    $objIdProof->active = $row['active'];
                    $objIdProof->name = $row['name'];
                    if ($objIdProof->save()) {
                        $savedIdProofIds[] = (int)$objIdProof->id;
                    } else {
                        $this->errors[] = $this->l('An error occurred while saving identity proof.');
                    }
                }
                foreach (array_diff(IdProof::getRegistrationIdProofIds(), $savedIdProofIds) as $id) {
                    $objIdProof = new IdProof((int)$id);
                    $objIdProof->delete();
                }

                $savedPaymentMethodIds = array();
                foreach ($guestRegPaymentMethodData as $row) {
                    $objGuestRegistrationPaymentMethod = $row['id'] ? new GuestRegistrationPaymentMethod((int)$row['id']) : new GuestRegistrationPaymentMethod();
                    $objGuestRegistrationPaymentMethod->active = $row['active'];
                    $objGuestRegistrationPaymentMethod->name = $row['name'];
                    if ($objGuestRegistrationPaymentMethod->save()) {
                        $savedPaymentMethodIds[] = (int)$objGuestRegistrationPaymentMethod->id;
                    } else {
                        $this->errors[] = $this->l('An error occurred while saving payment method.');
                    }
                }
                foreach (array_diff(GuestRegistrationPaymentMethod::getRegistrationPaymentMethodIds(), $savedPaymentMethodIds) as $id) {
                    $objGuestRegistrationPaymentMethod = new GuestRegistrationPaymentMethod((int)$id);
                    $objGuestRegistrationPaymentMethod->delete();
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

    protected function renderRegistrationForm(): string
    {
        $languages = Language::getLanguages(false);

        $this->context->smarty->assign(array(
            'languages'                     => $languages,
            'default_form_language'         => (int)Configuration::get('PS_LANG_DEFAULT'),
            'dynamic_form_action'           => self::$currentIndex.'&token='.$this->token,
            'guest_visit_purpose_rows'        => GuestVisitPurpose::getGuestVisitPurposeRows(),
            'reg_id_proof_rows'       => IdProof::getRegistrationIdProofRows(),
            'guest_reg_payment_method_rows' => GuestRegistrationPaymentMethod::getRegistrationPaymentMethodRows(),
        ));

        return $this->createTemplate('dynamic_tables.tpl')->fetch();
    }

    /**
     * Validates posted rows for one guest registration field type.
     * Populates $this->errors on failure. Returns array of validated rows.
     *
     * @param array  $postedRows
     * @param array  $languages
     * @param int    $idLangDefault
     * @param string $title
     * @return array
     */
    protected function validateRegistrationFieldRows(array $postedRows, array $languages, int $idLangDefault, string $title): array
    {
        $rows = array();

        foreach ($postedRows as $row) {
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
                        $title,
                        $language['name']
                    );
                } elseif (!Validate::isCatalogName($name)) {
                    $this->errors[] = sprintf(
                        $this->l('%s: name is invalid for %s.'),
                        $title,
                        $language['name']
                    );
                }

                $validRow['name'][$idLang] = $name;
            }

            $rows[] = $validRow;
        }

        return $rows;
    }

    /**
     * @return array  Section ID => label for the optional-sections checkbox group
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
