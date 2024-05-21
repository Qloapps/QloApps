<?php
/**
* 2010-2020 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminHotelFeaturesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'htl_features';
        $this->className = 'HotelFeatures';
        $this->identifier  = 'id';
        $this->toolbar_title = $this->l('Manage Hotel Features');

        parent::__construct();
        $this->display = 'view';
    }

    public function initToolbar()
    {
        parent::initToolbar();

        $this->page_header_toolbar_btn['addfeatures'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add new Features'),
            'imgclass' => 'new'
        );

        $this->page_header_toolbar_btn['new'] = array(
            'href' => $this->context->link->getAdminLink('AdminAssignHotelFeatures'),
            'desc' => $this->l('Assign Features To Hotel'),
        );
    }

    public function renderView()
    {
        $objHotelFeatures = new HotelFeatures();
        $featuresList = $objHotelFeatures->HotelAllCommonFeaturesArray();
        $this->context->smarty->assign('features_list', $featuresList);
        return parent::renderView();
    }

    public function renderForm()
    {
        $smartyVars = array();
        //lang vars
        $languages = Language::getLanguages(false);
        $currentLangId = $this->default_form_language ? $this->default_form_language : Configuration::get('PS_LANG_DEFAULT');
        $currentLang = Language::getLanguage((int) $currentLangId);
        $smartyVars['languages'] = $languages;
        $smartyVars['currentLang'] = $currentLang;
        $smartyVars['ps_img_dir'] = _PS_IMG_.'l/';
        if ($id = Tools::getValue('id')) {
            $smartyVars['edit'] = 1;
            if (Validate::isLoadedObject($objFeatures = new HotelFeatures($id))) {
                $featureInfo = (array) $objFeatures;
                if ($childFeatures = $objFeatures->getChildFeaturesByParentFeatureId($id)) {
                    $featureInfo['child_features'] = array();
                    foreach ($childFeatures as $value) {
                        $objChildFeatures = new HotelFeatures($value['id']);
                        $featureInfo['child_features'][] = (array) $objChildFeatures;
                    }
                }
                $smartyVars['featureInfo'] = $featureInfo;
            }
        }
        $this->context->smarty->assign($smartyVars);
        Media::addJsDef(
            array(
                'currentLang' => $currentLang,
                'languages' => $languages,
                'img_dir_l' => _PS_IMG_.'l/',
            )
        );
        $this->fields_form = array(
            'submit' => array(
                'title' => $this->l('Save')
            )
        );
        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitHtlFeatures') || Tools::isSubmit('submitHtlFeaturesAndStay')) {
            $parentFeatureId = Tools::getValue('id');
            $pos = Tools::getValue('position');

            $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
            $objDefaultLanguage = Language::getLanguage((int) $defaultLangId);
            $languages = Language::getLanguages(false);

            if (empty($pos) || !Validate::isUnsignedInt($pos)) {
                $this->errors[] = $this->l('Position is Invalid.');
            }

            if (!trim(Tools::getValue('parent_ftr_name_'.$defaultLangId))) {
                $this->errors[] = $this->l('Parent feature name is required at least in ').
                $objDefaultLanguage['name'];
            } elseif (!Validate::isGenericName(Tools::getValue('parent_ftr_name_'.$defaultLangId))) {
                $this->errors[] = $this->l('Parent feature name is invalid.');
            } else {
                foreach ($languages as $lang) {
                    // validate non required fields
                    if (trim(Tools::getValue('parent_ftr_name_'.$lang['id_lang']))) {
                        if (!Validate::isGenericName(Tools::getValue('parent_ftr_name_'.$lang['id_lang']))) {
                            $this->errors[] = $this->l('Invalid parent feature name in ').$lang['name'];
                        }
                    }
                }
            }
            if ($childFeaturesDefLang = Tools::getValue('child_features_'.$defaultLangId)) {
                foreach ($childFeaturesDefLang as $kChild => $childftr) {
                    if (!trim($childftr)) {
                        $this->errors[] = $this->l('Child features name is required at least in ').
                        $objDefaultLanguage['name'];
                    } elseif (!Validate::isGenericName($childftr)) {
                        $this->errors[] = $this->l('Child features name is invalid : ').$childftr;
                    } else {
                        foreach ($languages as $lang) {
                            if ($childFtrLang = Tools::getValue('child_features_'.$lang['id_lang'])) {
                                if (!Validate::isGenericName($childFtrLang[$kChild])) {
                                    $this->errors[] = $this->l('Invalid child feature name in ').$lang['name'].
                                    ' : '.$childFtrLang[$kChild];
                                }
                            }
                        }
                    }
                }

                if (!count($this->errors)) {
                }
            } else {
                $this->errors[] = $this->l('Please add atleast one Child features.');
            }

            if (!count($this->errors)) {
                if (isset($parentFeatureId) && $parentFeatureId) {
                    $objHotelFeatures = new HotelFeatures($parentFeatureId);
                    $childFeatureIds = Tools::getValue('child_feature_id');
                    foreach ($languages as $lang) {
                        if (!trim(Tools::getValue('parent_ftr_name_'.$lang['id_lang']))) {
                            $objHotelFeatures->name[$lang['id_lang']] = Tools::getValue(
                                'parent_ftr_name_'.$defaultLangId
                            );
                        } else {
                            $objHotelFeatures->name[$lang['id_lang']] = Tools::getValue(
                                'parent_ftr_name_'.$lang['id_lang']
                            );
                        }
                    }
                    $objHotelFeatures->active = 1;
                    $objHotelFeatures->position = $pos;
                    $objHotelFeatures->parent_feature_id = 0;
                    $objHotelFeatures->save();

                    if ($childFeaturesDefLang) {
                        // get previous child features of the parent feature
                        if ($childFeaturesInfo = $objHotelFeatures->getChildFeaturesByParentFeatureId(
                            $parentFeatureId
                        )) {
                            $prevChildIds = array();
                            foreach ($childFeaturesInfo as $row) {
                                $prevChildIds[] = $row['id'];
                            }
                        }
                        $usedFeatures = array();
                        foreach ($childFeaturesDefLang as $key => $childftr) {
                            $objHotelFeatures = new HotelFeatures();
                            // We will only create new child features other wise edit the previous child feature
                            if ($prevChildIds
                                && isset($childFeatureIds[$key])
                                && in_array($childFeatureIds[$key], $prevChildIds)
                            ) {
                                $objHotelFeatures = new HotelFeatures($childFeatureIds[$key]);
                                // enter child feature id in the used child feature
                                $usedFeatures[] = $childFeatureIds[$key];
                            }
                            foreach ($languages as $lang) {
                                if (!trim(Tools::getValue('child_features_'.$lang['id_lang'])[$key])) {
                                    $objHotelFeatures->name[$lang['id_lang']] = $childftr;
                                } else {
                                    $objHotelFeatures->name[$lang['id_lang']] = Tools::getValue(
                                        'child_features_'.$lang['id_lang']
                                    )[$key];
                                }
                            }
                            $objHotelFeatures->active = 1;
                            $objHotelFeatures->parent_feature_id = $parentFeatureId;
                            $objHotelFeatures->save();
                        }
                        // delete the child features which are not used now
                        $notUsedChilds = array_diff($prevChildIds, $usedFeatures);
                        if ($notUsedChilds = array_diff($prevChildIds, $usedFeatures)) {
                            foreach ($notUsedChilds as $value) {
                                if ($value) {
                                    $objHotelFeatures = new HotelFeatures($value);
                                    $objHotelFeatures->delete();
                                }
                            }
                        }
                        if (Tools::isSubmit('submitHtlFeaturesAndStay')) {
                            Tools::redirectAdmin(
                                self::$currentIndex.'&id='.(int) $parentFeatureId.'&update'.$this->table.
                                '&conf=4&token='.$this->token
                            );
                        } else {
                            Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                        }
                    } else {
                        $this->errors[] = $this->l('Some error has been occurred while saving features.');
                    }
                } else {
                    $objHotelFeatures = new HotelFeatures();
                    foreach ($languages as $lang) {
                        if (!trim(Tools::getValue('parent_ftr_name_'.$lang['id_lang']))) {
                            $objHotelFeatures->name[$lang['id_lang']] = Tools::getValue(
                                'parent_ftr_name_'.$defaultLangId
                            );
                        } else {
                            $objHotelFeatures->name[$lang['id_lang']] = Tools::getValue(
                                'parent_ftr_name_'.$lang['id_lang']
                            );
                        }
                    }
                    $objHotelFeatures->active = 1;
                    $objHotelFeatures->position = $pos;
                    $objHotelFeatures->parent_feature_id = 0;
                    $objHotelFeatures->save();
                    if ($parentFeatureId = $objHotelFeatures->id) {
                        if ($childFeaturesDefLang) {
                            foreach ($childFeaturesDefLang as $key => $childftr) {
                                $objHotelFeatures = new HotelFeatures();
                                foreach ($languages as $lang) {
                                    if (!trim(Tools::getValue('child_features_'.$lang['id_lang'])[$key])) {
                                        $objHotelFeatures->name[$lang['id_lang']] = $childftr;
                                    } else {
                                        $objHotelFeatures->name[$lang['id_lang']] = Tools::getValue(
                                            'child_features_'.$lang['id_lang']
                                        )[$key];
                                    }
                                }
                                $objHotelFeatures->active = 1;
                                $objHotelFeatures->parent_feature_id = $parentFeatureId;
                                $objHotelFeatures->save();
                            }
                            if (Tools::isSubmit('submitHtlFeaturesAndStay')) {
                                Tools::redirectAdmin(
                                    self::$currentIndex.'&id='.(int) $parentFeatureId.'&update'.$this->table.
                                    '&conf=4&token='.$this->token
                                );
                            } else {
                                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                            }
                        }
                    } else {
                        $this->errors[] = $this->l('Some error has been occurred while saving features.');
                    }
                }
            }
            if ($parentFeatureId) {
                $this->display = 'edit';
            } else {
                $this->display = 'add';
            }
        }
        parent::postProcess();
    }

    public function ajaxProcessDeleteFeature()
    {
        $response = array('status' => false);
        if ($this->tabAccess['delete']) {
            $idFeature = Tools::getValue('feature_id');
            $objHotelFeatures = new HotelFeatures();
            if ($objHotelFeatures->deleteHotelFeatures($idFeature)) {
                $response['status'] = true;
            } else {
                $response['msg'] = $this->l('Some error occurred while deleting feature. Please try again.');
            }
        } else {
            $response['msg'] = $this->l('You do not have the permission to delete this.');
        }

        $this->ajaxDie(json_encode($response));
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->addJs(_MODULE_DIR_.'hotelreservationsystem/views/js/HotelReservationAdmin.js');
        $this->addCSS(_MODULE_DIR_.'hotelreservationsystem/views/css/HotelReservationAdmin.css');
    }
}
