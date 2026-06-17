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
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'configuration';
        $this->className = 'Configuration';
        $this->identifier = 'id_configuration';

        parent::__construct();
    }

    public function initContent()
    {
        $this->display = 'options';
        $this->initToolbar();
        parent::initContent();
    }

    public function renderOptions()
    {
        $grcInfoJson = Configuration::get('QLO_GUEST_REGISTRATION_CARD_INFO');
        $savedSections = array();
        if ($grcInfoJson !== false && $grcInfoJson !== '') {
            $grcData = Tools::jsonDecode($grcInfoJson, true);
            if (is_array($grcData)) {
                $savedSections = $grcData;
            }
        }

        $allSelected = empty($savedSections);
        $nodes = array();
        foreach (Order::getRegistrationCardInfo() as $sectionId => $section) {
            $sectionFields = isset($savedSections[$sectionId]) ? $savedSections[$sectionId] : array();
            $fieldNodes = array();
            foreach ($section['fields'] as $fieldId => $fieldName) {
                $fieldSelected = $allSelected || in_array($fieldId, $sectionFields);
                $fieldNodes[] = array(
                    'value'      => $fieldId,
                    'name'       => $fieldName,
                    'input_name' => 'grc_field_'.$sectionId,
                    'selected'   => $fieldSelected,
                );
            }
            $fieldsSelected = $allSelected || !empty(array_filter($fieldNodes, function ($n) { return $n['selected']; }));
            $nodes[] = array(
                'value'      => $sectionId,
                'name'       => $section['name'],
                'input_name' => 'grc_section',
                'selected'   => $fieldsSelected,
                'children'   => $fieldNodes,
            );
        }

        $tree = new HelperTree('grc-card-info-tree');
        $tree->setData($nodes)
            ->setUseCheckBox(true)
            ->setAutoSelectChildren(true)
            ->setUseBulkActions(true);

        $this->context->smarty->assign(array(
            'guest_reg_card_info_tree'        => $tree->render(),
            'guest_reg_card_form_action'           => self::$currentIndex.'&token='.$this->token,
            'guest_reg_card_preview_img_url' => $this->context->link->getBaseLink().'img/admin/guest_registration_preview.jpg',
            'guest_reg_card_preview_title'   => $this->l('Guest Registration Card Preview'),
        ));

        return $this->createTemplate('card_info_form.tpl')->fetch();
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = array($this->l('Guest Registration Card'));
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitGrcCardInfo')) {
            $grcInfo = array();
            foreach (array_keys(Order::getRegistrationCardInfo()) as $sectionId) {
                $fields = array_values(array_filter(array_map('intval', (array)Tools::getValue('grc_field_'.$sectionId, array()))));
                if (!empty($fields)) {
                    $grcInfo[$sectionId] = $fields;
                }
            }
            if (empty($grcInfo)) {
                $this->errors[] = $this->l('Please select at least one field for the Guest Registration Card.');
                return;
            }
            Configuration::updateValue('QLO_GUEST_REGISTRATION_CARD_INFO', Tools::jsonEncode($grcInfo));
            Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&conf=4');
            return;
        }

        parent::postProcess();
    }
}
