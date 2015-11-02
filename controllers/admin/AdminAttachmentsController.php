<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @property Attachment $object
 */
class AdminAttachmentsControllerCore extends AdminController
{
    public $bootstrap = true ;

    protected $product_attachements = array();

    public function __construct()
    {
        $this->table = 'attachment';
        $this->className = 'Attachment';
        $this->lang = true;

        $this->addRowAction('edit');
        $this->addRowAction('view');
        $this->addRowAction('delete');

        $this->_select = 'IFNULL(virtual.products, 0) as products';
        $this->_join = 'LEFT JOIN (SELECT id_attachment, COUNT(*) as products FROM '._DB_PREFIX_.'product_attachment GROUP BY id_attachment) virtual ON a.id_attachment = virtual.id_attachment';
        $this->_use_found_rows = false;

        $this->fields_list = array(
            'id_attachment' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name')
            ),
            'file' => array(
                'title' => $this->l('File')
            ),
            'file_size' => array(
                'title' => $this->l('Size'),
                'callback' => 'displayHumanReadableSize'
            ),
            'products' => array(
                'title' => $this->l('Associated with'),
                'suffix' => $this->l('product(s)'),
                'filter_key' => 'virtual!products',
            ),
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );

        parent::__construct();
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->addJs(_PS_JS_DIR_.'/admin/attachments.js');
        Media::addJsDefL('confirm_text', $this->l('This attachment is associated with the following products, do you really want to  delete it?', null, true, false));
    }

    public static function displayHumanReadableSize($size)
    {
        return Tools::formatBytes($size);
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_attachment'] = array(
                'href' => self::$currentIndex.'&addattachment&token='.$this->token,
                'desc' => $this->l('Add new attachment', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function renderView()
    {
        if (($obj = $this->loadObject(true)) && Validate::isLoadedObject($obj)) {
            $link = $this->context->link->getPageLink('attachment', true, null, 'id_attachment='.$obj->id);
            Tools::redirectLink($link);
        }
        return $this->displayWarning($this->l('File not found'));
    }

    public function renderForm()
    {
        if (($obj = $this->loadObject(true)) && Validate::isLoadedObject($obj)) {
            /** @var Attachment $obj */
            $link = $this->context->link->getPageLink('attachment', true, null, 'id_attachment='.$obj->id);

            if (file_exists(_PS_DOWNLOAD_DIR_.$obj->file)) {
                $size = round(filesize(_PS_DOWNLOAD_DIR_.$obj->file) / 1024);
            }
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Attachment'),
                'icon' => 'icon-paper-clip'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Filename'),
                    'name' => 'name',
                    'required' => true,
                    'lang' => true,
                    'col' => 4
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'description',
                    'lang' => true,
                    'col' => 6
                ),
                array(
                    'type' => 'file',
                    'file' => isset($link) ? $link : null,
                    'size' => isset($size) ? $size : null,
                    'label' => $this->l('File'),
                    'name' => 'file',
                    'required' => true,
                    'col' => 6
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );

        return parent::renderForm();
    }

    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        parent::getList((int)$id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

        if (count($this->_list)) {
            $this->product_attachements = Attachment::getProductAttached((int)$id_lang, $this->_list);

            $list_product_list = array();
            foreach ($this->_list as $list) {
                $product_list = '';

                if (isset($this->product_attachements[$list['id_attachment']])) {
                    foreach ($this->product_attachements[$list['id_attachment']] as $product) {
                        $product_list .= $product.', ';
                    }

                    $product_list = rtrim($product_list, ', ');
                }

                $list_product_list[$list['id_attachment']] = $product_list;
            }

            // Assign array in list_action_delete.tpl
            $this->tpl_delete_link_vars = array(
                'product_list' => $list_product_list,
                'product_attachements' => $this->product_attachements
            );
        }
    }

    public function postProcess()
    {
        if (_PS_MODE_DEMO_) {
            $this->errors[] = Tools::displayError('This functionality has been disabled.');
            return;
        }

        if (Tools::isSubmit('submitAdd'.$this->table)) {
            $id = (int)Tools::getValue('id_attachment');
            if ($id && $a = new Attachment($id)) {
                $_POST['file'] = $a->file;
                $_POST['mime'] = $a->mime;
            }
            if (!count($this->errors)) {
                if (isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                    if ($_FILES['file']['size'] > (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024)) {
                        $this->errors[] = sprintf(
                            $this->l('The file is too large. Maximum size allowed is: %1$d kB. The file you are trying to upload is %2$d kB.'),
                            (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024),
                            number_format(($_FILES['file']['size'] / 1024), 2, '.', '')
                        );
                    } else {
                        do {
                            $uniqid = sha1(microtime());
                        } while (file_exists(_PS_DOWNLOAD_DIR_.$uniqid));
                        if (!move_uploaded_file($_FILES['file']['tmp_name'], _PS_DOWNLOAD_DIR_.$uniqid)) {
                            $this->errors[] = $this->l('Failed to copy the file.');
                        }
                        $_POST['file_name'] = $_FILES['file']['name'];
                        @unlink($_FILES['file']['tmp_name']);
                        if (!sizeof($this->errors) && isset($a) && file_exists(_PS_DOWNLOAD_DIR_.$a->file)) {
                            unlink(_PS_DOWNLOAD_DIR_.$a->file);
                        }
                        $_POST['file'] = $uniqid;
                        $_POST['mime'] = $_FILES['file']['type'];
                    }
                } elseif (array_key_exists('file', $_FILES) && (int)$_FILES['file']['error'] === 1) {
                    $max_upload = (int)ini_get('upload_max_filesize');
                    $max_post = (int)ini_get('post_max_size');
                    $upload_mb = min($max_upload, $max_post);
                    $this->errors[] = sprintf(
                        $this->l('The file %1$s exceeds the size allowed by the server. The limit is set to %2$d MB.'),
                        '<b>'.$_FILES['file']['name'].'</b> ',
                        '<b>'.$upload_mb.'</b>'
                    );
                } elseif (!isset($a) || (isset($a) && !file_exists(_PS_DOWNLOAD_DIR_.$a->file))) {
                    $this->errors[] = $this->l('Upload error. Please check your server configurations for the maximum upload size allowed.');
                }
            }
            $this->validateRules();
        }
        $return = parent::postProcess();
        if (!$return && isset($uniqid) && file_exists(_PS_DOWNLOAD_DIR_.$uniqid)) {
            unlink(_PS_DOWNLOAD_DIR_.$uniqid);
        }
        return $return;
    }
}
