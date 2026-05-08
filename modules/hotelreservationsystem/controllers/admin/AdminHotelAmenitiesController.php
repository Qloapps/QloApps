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
                'href'     => self::$currentIndex.'&addhtl_amenity&token='.$this->token,
                'desc'     => $this->l('Add new Amenity Category'),
                'imgclass' => 'new',
            );
        }
    }

    public function initContent()
    {
        if (Tools::isSubmit('addhtl_amenity') || Tools::isSubmit('updatehtl_amenity')
            || Tools::isSubmit('addhtl_amenity_item') || Tools::isSubmit('updatehtl_amenity_item')
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
        $obj = new HotelAmenities();
        $this->context->smarty->assign(array(
            'amenities_tree' => $obj->getAllAmenitiesTree(),
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

        if (Tools::isSubmit('addhtl_amenity_item') || Tools::isSubmit('updatehtl_amenity_item')) {
            $idAmenity  = (int)Tools::getValue('id');
            $idCategory = (int)Tools::getValue('id_category');
            $amenity    = array();

            if ($idAmenity) {
                $obj = new HotelAmenities($idAmenity);
                if (Validate::isLoadedObject($obj)) {
                    $idCategory = (int)$obj->parent_amenity_id;
                    $amenity = array(
                        'id'               => $obj->id,
                        'parent_amenity_id' => $idCategory,
                        'active'           => (int)$obj->active,
                        'is_featured'      => (int)$obj->is_featured,
                        'logo_type'        => $obj->logo_type,
                        'logo'             => $obj->logo,
                        'name'             => is_array($obj->name) ? $obj->name : array(),
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
            $obj = new HotelAmenities($idCategory);
            if (Validate::isLoadedObject($obj)) {
                $category = array(
                    'id'       => $obj->id,
                    'position' => $obj->position,
                    'name'     => is_array($obj->name) ? $obj->name : array(),
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
        if (Tools::isSubmit('submitHtlCategory')) {
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

            $obj = $idCategory ? new HotelAmenities($idCategory) : new HotelAmenities();
            foreach ($languages as $lang) {
                $val = trim(Tools::getValue('cat_name_'.$lang['id_lang']));
                $obj->name[$lang['id_lang']] = $val ?: $nameDefault;
            }
            $obj->parent_amenity_id = 0;
            $obj->position          = $pos;
            $obj->active            = 1;

            if (!$obj->save()) {
                $this->errors[] = $this->l('Error saving category.');
                $this->display  = 'edit';
                return;
            }

            Tools::redirectAdmin(self::$currentIndex.'&conf='.($idCategory ? 4 : 3).'&token='.$this->token);
            return;
        }

        if (Tools::isSubmit('submitHtlAmenityItem')) {
            $idAmenity   = (int)Tools::getValue('id');
            $idCategory  = (int)Tools::getValue('id_category');
            $active      = (int)Tools::getValue('active');
            $isFeatured  = (int)Tools::getValue('is_featured');
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

            $obj = $idAmenity ? new HotelAmenities($idAmenity) : new HotelAmenities();
            foreach ($languages as $lang) {
                $val = trim(Tools::getValue('amenity_name_'.$lang['id_lang']));
                $obj->name[$lang['id_lang']] = $val ?: $nameDefault;
            }
            $obj->parent_amenity_id = $idCategory;
            $obj->active            = $active;
            $obj->is_featured       = $isFeatured;
            $obj->logo_type         = ($logoType === 'icon') ? 'icon' : 'image';
            $obj->logo              = ($logoType === 'icon') ? $logoValue : $obj->logo;

            if (!$obj->save()) {
                $this->errors[] = $this->l('Error saving amenity.');
                $this->display  = 'edit';
                return;
            }

            if ($logoType === 'icon') {
                $imgFile = $this->module->getLocalPath().self::IMG_DIR.$obj->id.'.jpg';
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
                    if (ImageManager::resize($imgFile['tmp_name'], $imgDir.$obj->id.'.jpg')) {
                        $obj->logo = $obj->id.'.jpg';
                        $obj->save();
                    } else {
                        $this->errors[] = $this->l('Error uploading amenity image.');
                    }
                }
            }

            if (count($this->errors)) {
                $this->display = 'edit';
                return;
            }

            Tools::redirectAdmin(self::$currentIndex.'&conf='.($idAmenity ? 4 : 3).'&token='.$this->token);
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
            $obj = new HotelAmenities();
            if ($obj->deleteHotelAmenities((int)Tools::getValue('id_category'))) {
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
            $obj = new HotelAmenities((int)Tools::getValue('id_amenity'));
            if (Validate::isLoadedObject($obj) && $obj->delete()) {
                $response['status'] = true;
            } else {
                $response['msg'] = $this->l('Error deleting amenity.');
            }
        } else {
            $response['msg'] = $this->l('You do not have permission to delete.');
        }

        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessToggleFeatured()
    {
        $response = array('status' => false);

        if ($this->tabAccess['edit']) {
            $obj = new HotelAmenities((int)Tools::getValue('id_amenity'));
            if (Validate::isLoadedObject($obj)) {
                $obj->is_featured = $obj->is_featured ? 0 : 1;
                if ($obj->save()) {
                    $response['status']      = true;
                    $response['is_featured'] = (bool)$obj->is_featured;
                } else {
                    $response['msg'] = $this->l('Error updating amenity.');
                }
            } else {
                $response['msg'] = $this->l('Amenity not found.');
            }
        } else {
            $response['msg'] = $this->l('You do not have permission to edit.');
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
