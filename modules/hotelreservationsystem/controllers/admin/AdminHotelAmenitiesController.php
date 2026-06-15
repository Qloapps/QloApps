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

class AdminHotelAmenitiesController extends ModuleAdminController
{
    const IMG_DIR = 'views/img/hotel_amenities/';

    public function __construct()
    {
        $this->bootstrap   = true;
        $this->table       = 'htl_amenity';
        $this->className   = 'HotelAmenities';
        $this->identifier  = 'id_htl_amenity';
        $this->toolbar_title = $this->l('Manage Amenities');

        parent::__construct();

        $this->display = 'view';
    }

    public function initToolbar()
    {
        parent::initToolbar();

        if ($this->display == 'view') {
            $this->page_header_toolbar_btn['add_category'] = array(
                'href'     => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
                'desc'     => $this->l('Add new Amenity Category'),
                'imgclass' => 'new',
            );
        }
    }

    public function initContent()
    {
        if (Tools::isSubmit('add'.$this->table) || Tools::isSubmit('update'.$this->table)
            || Tools::isSubmit('add'.$this->table.'_item') || Tools::isSubmit('update'.$this->table.'_item')
        ) {
            $this->display = 'edit';
        }

        $this->initTabModuleList();
        $this->initToolbar();
        $this->initPageHeaderToolbar();

        if ($this->display == 'edit') {
            $this->content = $this->renderForm();
        } else {
            $this->content = $this->renderView();
        }

        $this->context->smarty->assign(array(
            'content'                   => $this->content,
            'url_post'                  => self::$currentIndex.'&token='.$this->token,
            'show_page_header_toolbar'  => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn'   => $this->page_header_toolbar_btn,
        ));
    }

    public function renderView()
    {
        $objHotelAmenities = new HotelAmenities();
        $this->context->smarty->assign(array(
            'amenities_tree' => $objHotelAmenities->getAllAmenitiesTree(),
            'img_base_url'   => $this->module->getPathUri().self::IMG_DIR,
            'current_index'  => self::$currentIndex,
            'token'          => $this->token,
            'admin_link'     => Context::getContext()->link->getAdminLink('AdminHotelAmenities'),
        ));

        return parent::renderView();
    }

    public function renderForm()
    {
        $languages   = Language::getLanguages(false);
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');
        $adminLink   = Context::getContext()->link->getAdminLink('AdminHotelAmenities');

        if (Tools::isSubmit('add'.$this->table.'_item') || Tools::isSubmit('update'.$this->table.'_item')) {
            $idAmenity  = (int)Tools::getValue('id');
            $idCategory = (int)Tools::getValue('id_category');
            $amenity    = array();

            if ($idAmenity) {
                $objHotelAmenities = new HotelAmenities($idAmenity);
                if (Validate::isLoadedObject($objHotelAmenities)) {
                    $idCategory = (int)$objHotelAmenities->parent_amenity_id;
                    $amenity = array(
                        'id'               => $objHotelAmenities->id,
                        'parent_amenity_id' => $idCategory,
                        'active'           => (int)$objHotelAmenities->active,
                        'logo_type'        => $objHotelAmenities->logo_type,
                        'logo'             => $objHotelAmenities->logo,
                        'name'             => is_array($objHotelAmenities->name) ? $objHotelAmenities->name : array(),
                    );
                }
            }

            $imgPath = $this->module->getLocalPath().self::IMG_DIR.$idAmenity.'.jpg';
            $imgUrl  = ($idAmenity && file_exists($imgPath))
                ? $this->module->getPathUri().self::IMG_DIR.$idAmenity.'.jpg'
                : false;

            $this->context->smarty->assign(array(
                'languages'       => $languages,
                'default_lang_id' => $defaultLang,
                'amenity'         => $amenity,
                'id_category'     => $idCategory,
                'existing_img'    => $imgUrl,
                'current_index'   => self::$currentIndex,
                'token'           => $this->token,
                'admin_link'      => $adminLink,
            ));

            return $this->context->smarty->createTemplate(
                $this->module->getLocalPath().'views/templates/admin/hotel_amenities/helpers/form/amenity_form.tpl'
            )->fetch();
        }

        // category form (add or edit)
        $idCategory = (int)Tools::getValue('id');
        $category   = array();

        if ($idCategory) {
            $objHotelAmenities = new HotelAmenities($idCategory);
            if (Validate::isLoadedObject($objHotelAmenities)) {
                $category = array(
                    'id'       => $objHotelAmenities->id,
                    'position' => $objHotelAmenities->position,
                    'name'     => is_array($objHotelAmenities->name) ? $objHotelAmenities->name : array(),
                );
            }
        }

        $this->context->smarty->assign(array(
            'languages'       => $languages,
            'default_lang_id' => $defaultLang,
            'category'        => $category,
            'current_index'   => self::$currentIndex,
            'token'           => $this->token,
            'admin_link'      => $adminLink,
        ));

        return $this->context->smarty->createTemplate(
            $this->module->getLocalPath().'views/templates/admin/hotel_amenities/helpers/form/category_form.tpl'
        )->fetch();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitHtlCategory') || Tools::isSubmit('submitHtlCategoryAndStay')) {
            $idCategory  = (int)Tools::getValue('id');
            $pos         = (int)Tools::getValue('position');
            $languages   = Language::getLanguages(false);
            $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

            if (!$pos || !Validate::isUnsignedInt($pos)) {
                $this->errors[] = $this->l('Position is invalid.');
            }

            $nameDefault = trim(Tools::getValue('cat_name_'.$defaultLang));
            if (!$nameDefault) {
                $this->errors[] = $this->l('Category name is required in default language.');
            } elseif (!Validate::isGenericName($nameDefault)) {
                $this->errors[] = $this->l('Category name is invalid.');
            }

            if (count($this->errors)) {
                $this->display = 'edit';
                return;
            }

            $objHotelAmenities = $idCategory ? new HotelAmenities($idCategory) : new HotelAmenities();
            foreach ($languages as $lang) {
                $val = trim(Tools::getValue('cat_name_'.$lang['id_lang']));
                $objHotelAmenities->name[$lang['id_lang']] = $val ?: $nameDefault;
            }
            $objHotelAmenities->parent_amenity_id = 0;
            $objHotelAmenities->position          = $pos;
            $objHotelAmenities->active            = 1;

            if (!$objHotelAmenities->save()) {
                $this->errors[] = $this->l('Error saving category.');
                $this->display  = 'edit';
                return;
            }

            $conf = $idCategory ? 4 : 3;
            if (Tools::isSubmit('submitHtlCategoryAndStay')) {
                Tools::redirectAdmin(self::$currentIndex.'&id='.(int)$objHotelAmenities->id.'&update'.$this->table.'&conf='.$conf.'&token='.$this->token);
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&conf='.$conf.'&token='.$this->token);
            }
            return;
        }

        if (Tools::isSubmit('submitHtlAmenityItem') || Tools::isSubmit('submitHtlAmenityItemAndStay')) {
            $idAmenity   = (int)Tools::getValue('id');
            $idCategory  = (int)Tools::getValue('id_category');
            $active      = (int)Tools::getValue('active');
            $logoType    = Tools::getValue('logo_type');
            $languages   = Language::getLanguages(false);
            $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

            if (!$idCategory) {
                $this->errors[] = $this->l('Category is required.');
            }

            $nameDefault = trim(Tools::getValue('amenity_name_'.$defaultLang));
            if (!$nameDefault) {
                $this->errors[] = $this->l('Amenity name is required in default language.');
            } elseif (!Validate::isGenericName($nameDefault)) {
                $this->errors[] = $this->l('Amenity name is invalid.');
            }

            $logoValue = '';
            if ($logoType === 'icon') {
                $logoValue = trim(Tools::getValue('logo_icon'));
                if (!$logoValue) {
                    $this->errors[] = $this->l('Icon class is required.');
                } elseif (!Validate::isGenericName(str_replace('-', ' ', $logoValue))) {
                    $this->errors[] = $this->l('Icon name is invalid.');
                }
            } else {
                $hasUpload   = isset($_FILES['logo_image']) && !empty($_FILES['logo_image']['tmp_name']);
                $hasExisting = $idAmenity && file_exists($this->module->getLocalPath().self::IMG_DIR.$idAmenity.'.jpg');
                if (!$hasUpload && !$hasExisting) {
                    $this->errors[] = $this->l('Please upload an image for this amenity.');
                }
            }

            if (count($this->errors)) {
                $this->display = 'edit';
                return;
            }

            $objHotelAmenities = $idAmenity ? new HotelAmenities($idAmenity) : new HotelAmenities();
            foreach ($languages as $lang) {
                $val = trim(Tools::getValue('amenity_name_'.$lang['id_lang']));
                $objHotelAmenities->name[$lang['id_lang']] = $val ?: $nameDefault;
            }
            $objHotelAmenities->parent_amenity_id = $idCategory;
            $objHotelAmenities->active            = $active;
            $objHotelAmenities->logo_type         = ($logoType === 'icon') ? 'icon' : 'image';
            $objHotelAmenities->logo              = ($logoType === 'icon') ? $logoValue : $objHotelAmenities->logo;

            if (!$objHotelAmenities->save()) {
                $this->errors[] = $this->l('Error saving amenity.');
                $this->display  = 'edit';
                return;
            }

            if ($logoType === 'icon') {
                $imgFile = $this->module->getLocalPath().self::IMG_DIR.$objHotelAmenities->id.'.jpg';
                if (file_exists($imgFile)) {
                    unlink($imgFile);
                }
            }

            if ($logoType === 'image' && isset($_FILES['logo_image']) && $_FILES['logo_image']['tmp_name']) {
                $imgFile = $_FILES['logo_image'];
                if ($error = ImageManager::validateUpload($imgFile, Tools::getMaxUploadSize())) {
                    $this->errors[] = $error;
                } else {
                    $imgDir = $this->module->getLocalPath().self::IMG_DIR;
                    if (!is_dir($imgDir)) {
                        mkdir($imgDir, 0755, true);
                    }
                    if (ImageManager::resize($imgFile['tmp_name'], $imgDir.$objHotelAmenities->id.'.jpg')) {
                        $objHotelAmenities->logo = $objHotelAmenities->id.'.jpg';
                        $objHotelAmenities->save();
                    } else {
                        $this->errors[] = $this->l('Error uploading amenity image.');
                    }
                }
            }

            if (count($this->errors)) {
                $this->display = 'edit';
                return;
            }

            $conf = $idAmenity ? 4 : 3;
            if (Tools::isSubmit('submitHtlAmenityItemAndStay')) {
                Tools::redirectAdmin(self::$currentIndex.'&id='.(int)$objHotelAmenities->id.'&id_category='.(int)$idCategory.'&update'.$this->table.'_item&conf='.$conf.'&token='.$this->token);
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&conf='.$conf.'&token='.$this->token);
            }
            return;
        }

        if (empty($this->errors)) {
            parent::postProcess();
        }
    }

    public function ajaxProcessDeleteCategory()
    {
        $response = array('status' => false);

        if ($this->tabAccess['delete']) {
            $objHotelAmenities = new HotelAmenities();
            if ($objHotelAmenities->deleteCategory((int)Tools::getValue('id_category'))) {
                $response['status'] = true;
            } else {
                $response['msg'] = $this->l('Error deleting category.');
            }
        } else {
            $response['msg'] = $this->l('You do not have permission to delete.');
        }

        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessDeleteAmenityItem()
    {
        $response = array('status' => false);

        if ($this->tabAccess['delete']) {
            $objHotelAmenities = new HotelAmenities((int)Tools::getValue('id_amenity'));
            if (Validate::isLoadedObject($objHotelAmenities) && $objHotelAmenities->delete()) {
                $response['status'] = true;
            } else {
                $response['msg'] = $this->l('Error deleting amenity.');
            }
        } else {
            $response['msg'] = $this->l('You do not have permission to delete.');
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
