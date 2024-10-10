<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
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
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

class AdminFooterPaymentBlockSettingController extends ModuleAdminController
{
    protected $position_identifier = 'id_payment_block_to_move';
    public function __construct()
    {
        $this->table = 'htl_footer_payment_block_info';
        $this->className = 'WkFooterPaymentBlockInfo';
        $this->bootstrap = true;
        $this->_defaultOrderBy = 'position';
        $this->context = Context::getContext();

        $this->fields_list = array(
            'id_payment_block' => array(
                'title' => $this->l('ID'),
            ),
            'date_upd' => array(
                'title' => $this->l('Payment Image'),
                'align' => 'center',
                'callback' => 'getPaymentImage',
            ),
            'name' => array(
                'title' => $this->l('Name'),
            ),
            'active' => array(
                'title' => $this->l('Active'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'align' => 'center',
                'filter_key' => 'a!position',
                'position' => 'position',
                'align' => 'center',
            ),
            'date_add' => array(
                'title' => $this->l('Date Add'),
                'align' => 'center',
                'type' => 'datetime',
            ),
        );

        $this->identifier = 'id_payment_block';

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ),
            'enableSelection' => array(
                'text' => $this->l('Enable selection'),
                'icon' => 'icon-power-off text-success',
            ),
            'disableSelection' => array(
                'text' => $this->l('Disable selection'),
                'icon' => 'icon-power-off text-danger',
            ),
        );
        parent::__construct();
    }

    public function getPaymentImage($echo, $row)
    {
        $image = '';
        if ($row['id_payment_block']) {
            $imgUrl = $this->context->link->getMediaLink(_MODULE_DIR_.$this->module->name.'/views/img/payment_img/'.$row['id_payment_block'].'.jpg');
            if ($imgExist = (bool)Tools::file_get_contents($imgUrl)) {
                $image = "<img class='img-thumbnail img-responsive' style='max-width:70px' src='".$imgUrl."'>";
            }
        }
        return $image;
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add New Payment Image')
        );

        return parent::renderList();
    }

    public function renderForm()
    {
        if (!($object = $this->loadObject(true))) {
            return;
        }
        $imgUrl = $this->context->link->getMediaLink(_MODULE_DIR_.$this->module->name.'/views/img/payment_img/'.$object->id.'.jpg');
        if ($imgExist = (bool)Tools::file_get_contents($imgUrl)) {
            $image = "<img class='img-thumbnail img-responsive' style='max-width:100px' src='".$imgUrl."'>";
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Footer payment Block Configuration'),
                'icon' => 'icon-money'
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Payment Method Name'),
                    'name' => 'name',
                    'required' => true,
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Payment image'),
                    'name' => 'payment_image',
                    'display_image' => true,
                    'image' => $imgExist ? $image : false,
                    'hint' => $this->l(
                        'Upload an image of the payment method to show on payment block at footer of the page.'
                    ),
                    'required' => true,
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save')
            ));

        return parent::renderForm();
    }

    public function processSave()
    {
        $idPaymentBlock = Tools::getValue('id_payment_block');
        $paymentName = Tools::getValue('name');

        //validate fields
        if (!trim($paymentName)) {
            $this->errors[] = Tools::displayError('Payment\'s Name is a required field.');
        }
        if (isset($_FILES['payment_image']['tmp_name']) && $_FILES['payment_image']['tmp_name']) {
            if ($error = ImageManager::validateUpload($_FILES['payment_image'], Tools::getMaxUploadSize())) {
                $this->errors[] = $error;
            }
        } elseif (!$idPaymentBlock) {
            $this->errors[] = $this->l('Please select an image for payment block.');
        }


        if (!count($this->errors)) {
            if ($idPaymentBlock) {
                $objPaymentBlockInfo = new WkFooterPaymentBlockInfo($idPaymentBlock);
            } else {
                $objPaymentBlockInfo = new WkFooterPaymentBlockInfo();
                $objPaymentBlockInfo->position = WkFooterPaymentBlockInfo::getHigherPosition();
            }

            $objPaymentBlockInfo->name = $paymentName;
            $objPaymentBlockInfo->active = Tools::getValue('active');
            $objPaymentBlockInfo->position = WkFooterPaymentBlockInfo::getHigherPosition();
            if ($objPaymentBlockInfo->save()) {
                if ($_FILES['payment_image']['size']) {
                    $imgPath = _PS_MODULE_DIR_.$this->module->name.'/views/img/payment_img/'.
                    $objPaymentBlockInfo->id.'.jpg';
                    if (!ImageManager::resize($_FILES['payment_image']['tmp_name'], $imgPath)) {
                        $this->errors[] = $this->l('Some error occurred while uploading payment image. Please try
                        again.');
                    }
                }
            }
            if ($idPaymentBlock) {
                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
            }
        } else {
            if ($idPaymentBlock) {
                $this->display = 'edit';
            } else {
                $this->display = 'add';
            }
        }
    }

    // update positions of membership
    public function ajaxProcessUpdatePositions()
    {
        $way = (int) Tools::getValue('way');
        $idPaymentBlock = (int) Tools::getValue('id');
        $positions = Tools::getValue('payment_block');

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int) $pos[2] === $idPaymentBlock) {
                if ($objPaymentBlockInfo = new WkFooterPaymentBlockInfo((int) $pos[2])) {
                    if (isset($position)
                        && $objPaymentBlockInfo->updatePosition($way, $position, $idPaymentBlock)
                    ) {
                        echo 'ok position '.(int) $position.' for payment block '.(int) $pos[1].'\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update payment block '.
                        (int) $idPaymentBlock.' to position '.(int) $position.' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This payment block ('.(int) $idPaymentBlock.
                    ') cant be loaded"}';
                }

                break;
            }
        }
    }
}
