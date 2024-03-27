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
 * @property Gender $object
 */
class AdminGendersControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'gender';
        $this->className = 'Gender';
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->context = Context::getContext();

        if (!Tools::getValue('realedit')) {
            $this->deleted = false;
        }

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->default_image_height = 16;
        $this->default_image_width = 16;

        $this->fieldImageSettings = array(
            'name' => 'image',
            'dir' => 'genders'
        );

        $this->fields_list = array(
            'id_gender' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Social title'),
                'filter_key' => 'b!name'
            ),
            'type' => array(
                'title' => $this->l('Gender'),
                'orderby' => false,
                'type' => 'select',
                'list' => array(
                    0 => $this->l('Male'),
                    1 => $this->l('Female'),
                    2 => $this->l('Neutral')
                ),
                'filter_key' => 'a!type',
                'callback' => 'displayGenderType',
                'callback_object' => $this
            ),
            'image' => array(
                'title' => $this->l('Image'),
                'align' => 'center',
                'image' => 'genders',
                'orderby' => false,
                'search' => false
            )
        );

        parent::__construct();
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_gender'] = array(
                'href' => self::$currentIndex.'&addgender&token='.$this->token,
                'desc' => $this->l('Add new title', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Social titles'),
                'icon' => 'icon-male'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Social title'),
                    'name' => 'name',
                    'lang' => true,
                    'col' => 4,
                    'hint' => $this->l('Invalid characters:').' 0-9!&lt;&gt;,;?=+()@#"�{}_$%:',
                    'required' => true
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Gender'),
                    'name' => 'type',
                    'required' => false,
                    'class' => 't',
                    'values' => array(
                        array(
                            'id' => 'type_male',
                            'value' => 0,
                            'label' => $this->l('Male')
                        ),
                        array(
                            'id' => 'type_female',
                            'value' => 1,
                            'label' => $this->l('Female')
                        ),
                        array(
                            'id' => 'type_neutral',
                            'value' => 2,
                            'label' => $this->l('Neutral')
                        )
                    )
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Image'),
                    'name' => 'image',
                    'col' => 6,
                    'value' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Image width'),
                    'name' => 'img_width',
                    'col' => 2,
                    'hint' => $this->l('Image width in pixels. Enter "0" to use the original size.')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Image height'),
                    'name' => 'img_height',
                    'col' => 2,
                    'hint' => $this->l('Image height in pixels. Enter "0" to use the original size.')
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->l('Save and stay'),
                    'name' => 'submitAdd'.$this->table.'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save',
                ),
            ),
        );

        /** @var Gender $obj */
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $this->fields_value = array(
            'img_width' => $this->default_image_width,
            'img_height' => $this->default_image_height,
            'image' => $obj->getImage()
        );

        return parent::renderForm();
    }

    public function displayGenderType($value, $tr)
    {
        return $this->fields_list['type']['list'][$value];
    }

    protected function postImage($id)
    {
        if (isset($this->fieldImageSettings['name']) && isset($this->fieldImageSettings['dir'])) {
            if (!Validate::isInt(Tools::getValue('img_width')) || !Validate::isInt(Tools::getValue('img_height'))) {
                $this->errors[] = Tools::displayError('Width and height must be numeric values.');
            } else {
                if ((int)Tools::getValue('img_width') > 0 && (int)Tools::getValue('img_height') > 0) {
                    $width = (int)Tools::getValue('img_width');
                    $height = (int)Tools::getValue('img_height');
                } else {
                    $width = null;
                    $height = null;
                }
                return $this->uploadImage($id, $this->fieldImageSettings['name'], $this->fieldImageSettings['dir'].'/', false, $width, $height);
            }
        }
        return !count($this->errors) ? true : false;
    }

    protected function afterImageUpload()
    {
        parent::afterImageUpload();

        if (($id_gender = (int)Tools::getValue('id_gender')) &&
             isset($_FILES) && count($_FILES) && Tools::file_get_contents($this->context->link->getMediaLink(_THEME_GENDERS_DIR_.$id_gender.'.jpg'))) { // by webkul
            $current_file = _PS_TMP_IMG_DIR_.'gender_mini_'.$id_gender.'_'.$this->context->shop->id.'.jpg';

            if (file_exists($current_file)) {
                unlink($current_file);
            }
        }

        return true;
    }
}
