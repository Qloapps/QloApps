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
 * @property Language $object
 */
class AdminLanguagesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'lang';
        $this->className = 'Language';
        $this->lang = false;
        $this->deleted = false;
        $this->multishop_context = Shop::CONTEXT_ALL;

        $this->context = Context::getContext();

        $this->fieldImageSettings = array(
            array(
                'name' => 'flag',
                'dir' => 'l'
            ),
            array(
                'name' => 'no_picture',
                'dir' => 'p'
            )
        );

        $this->fields_list = array(
            'id_lang' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'flag' => array(
                'title' => $this->l('Flag'),
                'align' => 'center',
                'image' => 'l',
                'orderby' => false,
                'search' => false,
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name')
            ),
            'iso_code' => array(
                'title' => $this->l('ISO code'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'language_code' => array(
                'title' => $this->l('Language code'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'date_format_lite' => array(
                'title' => $this->l('Date format')
            ),
            'date_format_full' => array(
                'title' => $this->l('Date format (full)')
            ),
            'active' => array(
                'title' => $this->l('Enabled'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'class' => 'fixed-width-sm'
            )
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );
        $this->specificConfirmDelete = $this->l('When you delete a language, all related translations in the database will be deleted. Are you sure you want to proceed?');

        parent::__construct();
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_language'] = array(
                'href' => self::$currentIndex.'&addlang&token='.$this->token,
                'desc' => $this->l('Add new language', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->displayWarning($this->l('When you delete a language, all related translations in the database will be deleted.'));
        if (!is_writable(_PS_ROOT_DIR_.'/.htaccess') && Configuration::get('PS_REWRITING_SETTINGS')) {
            $this->displayInformation($this->l('Your .htaccess file must be writable.'));
        }
        return parent::renderList();
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Languages'),
                'icon' => 'icon-globe'
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'ps_version'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'maxlength' => 32,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('ISO code'),
                    'name' => 'iso_code',
                    'required' => true,
                    'maxlength' => 2,
                    'hint' => $this->l('Two-letter ISO code (e.g. FR, EN, DE).')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Language code'),
                    'name' => 'language_code',
                    'required' => true,
                    'maxlength' => 5,
                    'hint' => $this->l('IETF language tag (e.g. en-US, pt-BR).')
                    /* TO DO - ajouter les liens dans le hint ? */
                    /*'desc' => $this->l('IETF language tag (e.g. en-US, pt-BR).').' '.sprintf('<a href="http://en.wikipedia.org/wiki/IETF_language_tag" target="_blank">%s <img src="../img/admin/external_link.png" class="icon-top" /></a>', $this->l('IETF on Wikipedia'))*/
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Date format'),
                    'name' => 'date_format_lite',
                    'required' => true,
                    'hint' => sprintf($this->l('Short date format (e.g., %s).'), 'Y-m-d')
                    /* TO DO - ajouter les liens dans le hint ? */
                    /*'desc' => sprintf($this->l('Short date format (e.g., %s)'), '<a href="http://php.net/date" target="_blank">Y-m-d</a>')*/
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Date format (full)'),
                    'name' => 'date_format_full',
                    'required' => true,
                    'hint' => sprintf($this->l('Full date format (e.g., %s).'), 'Y-m-d H:i:s')
                    /* TO DO - ajouter les liens dans le hint ? */
                    /*'desc' => sprintf($this->l('Full date format (e.g., %s)'), '<a href="http://php.net/date" target="_blank">Y-m-d H:i:s</a>')*/
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Flag'),
                    'name' => 'flag',
                    'required' => true,
                    'hint' => $this->l('Upload the country flag from your computer.')
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('"No-picture" image'),
                    'name' => 'no_picture',
                    'hint' => $this->l('Image is displayed when "no picture is found".')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Is RTL language'),
                    'name' => 'is_rtl',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'is_rtl_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'hint' => array(
                        $this->l('Enable if this language is read from right to left.').' '.
                        $this->l('(Experimental: your theme must be compliant with RTL languages).')
                    )
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
                    'hint' => $this->l('Activate this language.')
                ),
                array(
                    'type' => 'special',
                    'name' => 'resultCheckLangPack',
                    'text' => $this->l('Check to see if a language pack is available for this ISO code.'),
                    'img' => 'ajax-loader.gif'
                )
            )
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            );
        }

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );

        /** @var Language $obj */
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        if ($obj->id && !$obj->checkFiles()) {
            $this->fields_form['new'] = array(
                'legend' => array(
                    'title' => $this->l('Warning'),
                    'image' => '../img/admin/warning.gif'
                ),
                'list_files' => array(
                    array(
                        'label' => $this->l('Translation files'),
                        'files' => Language::getFilesList($obj->iso_code, _THEME_NAME_, false, false, 'tr', true)
                    ),
                    array(
                        'label' => $this->l('Theme files'),
                        'files' => Language::getFilesList($obj->iso_code, _THEME_NAME_, false, false, 'theme', true)
                    ),
                    array(
                        'label' => $this->l('Mail files'),
                        'files' => Language::getFilesList($obj->iso_code, _THEME_NAME_, false, false, 'mail', true)
                    )
                )
            );
        }

        $this->fields_value = array('ps_version' => _PS_VERSION_);

        return parent::renderForm();
    }

    public function processDelete()
    {
        $object = $this->loadObject();
        if (!$this->checkDeletion($object)) {
            return false;
        }
        if (!$this->deleteNoPictureImages((int)$object->id)) {
            $this->errors[] = Tools::displayError('An error occurred while deleting the object.').' <b>'.$this->table.'</b> ';
        }

        return parent::processDelete();
    }

    protected function processBulkDelete()
    {
        $can_bulk = true;
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id_lang) {
                $object = new Language((int)$id_lang);
                if (!$this->checkDeletion($object)) {
                    return false;
                }
                if (!$this->deleteNoPictureImages((int)$object->id)) {
                    $this->errors[] = Tools::displayError('An error occurred while deleting the object.').' <b>'.$this->table.'</b> ';
                    return false;
                }
            }
        }
        return parent::processBulkDelete();
    }

    protected function checkDeletion($object)
    {
        if (_PS_MODE_DEMO_) {
            $this->errors[] = Tools::displayError('This functionality has been disabled.');
            return;
        }

        if (Validate::isLoadedObject($object)) {
            if ($object->id == Configuration::get('PS_LANG_DEFAULT')) {
                $this->errors[] = $this->l('You cannot delete the default language.');
            } elseif ($object->id == $this->context->language->id) {
                $this->errors[] = $this->l('You cannot delete the language currently in use. Please select a different language.');
            } else {
                return true;
            }
        } else {
            $this->errors[] = Tools::displayError('(cannot load object)');
        }

        return false;
    }

    protected function checkDisableStatus($object)
    {
        if (_PS_MODE_DEMO_) {
            $this->errors[] = Tools::displayError('This functionality has been disabled.');
            return;
        }
        if (!Validate::isLoadedObject($object)) {
            $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
        } else {
            if ($object->id == (int)Configuration::get('PS_LANG_DEFAULT')) {
                $this->errors[] = Tools::displayError('You cannot change the status of the default language.');
            } else {
                return true;
            }
        }
        return false;
    }

    public function processStatus()
    {
        $object = $this->loadObject();
        if ($this->checkDisableStatus($object)) {
            $this->checkEmployeeIdLang($object->id);
            return parent::processStatus();
        }
        return false;
    }

    protected function processBulkDisableSelection()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id_lang) {
                $object = new Language((int)$id_lang);
                if (!$this->checkDisableStatus($object)) {
                    return false;
                }
                $this->checkEmployeeIdLang($object->id);
            }
        }
        return parent::processBulkDisableSelection();
    }

    public function processAdd()
    {
        if (_PS_MODE_DEMO_) {
            $this->errors[] = Tools::displayError('This functionality has been disabled.');
            return;
        }

        if (isset($_POST['iso_code']) && !empty($_POST['iso_code']) && Validate::isLanguageIsoCode(Tools::getValue('iso_code')) && Language::getIdByIso($_POST['iso_code'])) {
            $this->errors[] = Tools::displayError('This ISO code is already linked to another language.');
        }
        if ((!empty($_FILES['no_picture']['tmp_name']) || !empty($_FILES['flag']['tmp_name'])) && Validate::isLanguageIsoCode(Tools::getValue('iso_code'))) {
            if ($_FILES['no_picture']['error'] == UPLOAD_ERR_OK) {
                $this->copyNoPictureImage(strtolower(Tools::getValue('iso_code')));
            }
            unset($_FILES['no_picture']);
        } else {
            $this->errors[] = Tools::displayError('Flag and "No picture" image fields are required.');
        }

        return parent::processAdd();
    }

    public function processUpdate()
    {
        if (_PS_MODE_DEMO_) {
            $this->errors[] = Tools::displayError('This functionality has been disabled.');
            return;
        }

        if ((isset($_FILES['no_picture']) && !$_FILES['no_picture']['error'] || isset($_FILES['flag']) && !$_FILES['flag']['error'])
                && Validate::isLanguageIsoCode(Tools::getValue('iso_code'))) {
            if ($_FILES['no_picture']['error'] == UPLOAD_ERR_OK) {
                $this->copyNoPictureImage(strtolower(Tools::getValue('iso_code')));
            }
                        // class AdminTab deal with every $_FILES content, don't do that for no_picture
                    unset($_FILES['no_picture']);
        }

            /** @var Language $object */
            $object = $this->loadObject();
        if (Tools::getValue('active') != (int)$object->active) {
            if (!$this->checkDisableStatus($object)) {
                return false;
            }
        }

        $this->checkEmployeeIdLang($object->id);
        return parent::processUpdate();
    }

    /**
     * Copy a no-product image
     *
     * @param string $language Language iso_code for no_picture image filename
     *
     * @return void|false
     */
    public function copyNoPictureImage($language)
    {
        if (isset($_FILES['no_picture']) && $_FILES['no_picture']['error'] === 0) {
            if ($error = ImageManager::validateUpload($_FILES['no_picture'], Tools::getMaxUploadSize())) {
                $this->errors[] = $error;
            } else {
                if (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['no_picture']['tmp_name'], $tmp_name)) {
                    return false;
                }
                if (!ImageManager::resize($tmp_name, _PS_IMG_DIR_.'p/'.$language.'.jpg')) {
                    $this->errors[] = Tools::displayError('An error occurred while copying "No Picture" image to your product folder.');
                }
                if (!ImageManager::resize($tmp_name, _PS_IMG_DIR_.'c/'.$language.'.jpg')) {
                    $this->errors[] = Tools::displayError('An error occurred while copying "No picture" image to your category folder.');
                }
                if (!ImageManager::resize($tmp_name, _PS_IMG_DIR_.'m/'.$language.'.jpg')) {
                    $this->errors[] = Tools::displayError('An error occurred while copying "No picture" image to your manufacturer folder.');
                } else {
                    $images_types = ImageType::getImagesTypes('products');
                    foreach ($images_types as $k => $image_type) {
                        if (!ImageManager::resize($tmp_name, _PS_IMG_DIR_.'p/'.$language.'-default-'.stripslashes($image_type['name']).'.jpg', $image_type['width'], $image_type['height'])) {
                            $this->errors[] = Tools::displayError('An error occurred while resizing "No picture" image to your product directory.');
                        }
                        if (!ImageManager::resize($tmp_name, _PS_IMG_DIR_.'c/'.$language.'-default-'.stripslashes($image_type['name']).'.jpg', $image_type['width'], $image_type['height'])) {
                            $this->errors[] = Tools::displayError('An error occurred while resizing "No picture" image to your category directory.');
                        }
                        if (!ImageManager::resize($tmp_name, _PS_IMG_DIR_.'m/'.$language.'-default-'.stripslashes($image_type['name']).'.jpg', $image_type['width'], $image_type['height'])) {
                            $this->errors[] = Tools::displayError('An error occurred while resizing "No picture" image to your manufacturer directory.');
                        }
                    }
                }
                unlink($tmp_name);
            }
        }
    }

    /**
     * deleteNoPictureImages will delete all default image created for the language id_language
     *
     * @param string $id_language
     * @return bool true if no error
     */
    protected function deleteNoPictureImages($id_language)
    {
        $language = Language::getIsoById($id_language);
        $images_types = ImageType::getImagesTypes('products');
        $dirs = array(_PS_PROD_IMG_DIR_, _PS_CAT_IMG_DIR_, _PS_MANU_IMG_DIR_, _PS_SUPP_IMG_DIR_, _PS_MANU_IMG_DIR_);
        foreach ($dirs as $dir) {
            foreach ($images_types as $k => $image_type) {
                if (file_exists($dir.$language.'-default-'.stripslashes($image_type['name']).'.jpg')) {
                    if (!unlink($dir.$language.'-default-'.stripslashes($image_type['name']).'.jpg')) {
                        $this->errors[] = Tools::displayError('An error occurred during image deletion process.');
                    }
                }
            }

            if (file_exists($dir.$language.'.jpg')) {
                if (!unlink($dir.$language.'.jpg')) {
                    $this->errors[] = Tools::displayError('An error occurred during image deletion process.');
                }
            }
        }

        return !count($this->errors) ? true : false;
    }

    /**
     * @param Language $object
     * @param string   $table
     */
    protected function copyFromPost(&$object, $table)
    {
        if ($object->id && ($object->iso_code != $_POST['iso_code'])) {
            if (Validate::isLanguageIsoCode($_POST['iso_code'])) {
                $object->moveToIso($_POST['iso_code']);
            }
        }
        parent::copyFromPost($object, $table);
    }

    public function ajaxProcessCheckLangPack()
    {
        $this->json = true;
        if (!Tools::getValue('iso_lang') || !Validate::isLanguageIsoCode(Tools::getValue('iso_lang'))) {
            $this->status = 'error';
            $this->errors[] = $this->l('Iso code is not valid');
            return;
        }
        if (!Tools::getValue('ps_version') || !Validate::isPrestaShopVersion(Tools::getValue('ps_version'))) {
            $this->status = 'error';
            $this->errors[] = $this->l('Technical Error: ps_version is not valid');
            return;
        }

        // Get all iso code available
        if ($lang_packs = Tools::file_get_contents('http://www.prestashop.com/download/lang_packs/get_language_pack.php?version='.Tools::getValue('ps_version').'&iso_lang='.Tools::strtolower(Tools::getValue('iso_lang')))) {
            $result = json_decode($lang_packs);
            if ($lang_packs !== '' && $result && !isset($result->error)) {
                $this->status = 'ok';
                $this->content = $lang_packs;
            } else {
                $this->status = 'error';
                $this->errors[] = $this->l('Wrong ISO code, or the selected language pack is unavailable.');
            }
        } else {
            $this->status = 'error';
            $this->errors[] = $this->l('Technical Error: translation server unreachable.');
        }
    }

    protected function checkEmployeeIdLang($current_id_lang)
    {
        //update employee lang if current id lang is disabled
        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'employee` set `id_lang`='.(int)Configuration::get('PS_LANG_DEFAULT').' WHERE `id_lang`='.(int)$current_id_lang);
    }

    protected function afterImageUpload()
    {
        parent::afterImageUpload();

        if (($id_lang = (int)Tools::getValue('id_lang')) &&
             isset($_FILES) && count($_FILES) && (bool)Tools::file_get_contents($this->context->link->getMediaLink(_THEME_LANG_DIR_.$id_lang.'.jpg'))) { //by webkul
            $current_file = _PS_TMP_IMG_DIR_.'lang_mini_'.$id_lang.'_'.$this->context->shop->id.'.jpg';

            if (file_exists($current_file)) {
                unlink($current_file);
            }
        }

        return true;
    }
}
