<?php

class AdminAboutHotelBlockSettingController extends ModuleAdminController 
{
    public $bootstrap = true;
    protected $position_identifier = 'id';
    public function __construct()
    {
        $this->table = 'htl_interior_image';
        $this->className = 'WkHotelInteriorImage';
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->identifier_name = 'display_name';

        $this->fields_options = array(
            'global' => array(
                'title' =>  $this->l('Hotel Interior Description'),
                'icon' =>   'icon-cogs',
                'fields' => array(
                    'HOTEL_INTERIOR_HEADING' => array(
                        'title' => $this->l('Heading'),
                        'type' => 'text',
                        'required' => true,
                        'hint' => $this->l('Block Heading. Ex: Interior.')
                    ),
                    'HOTEL_INTERIOR_DESCRIPTION' => array(
                        'title' => $this->l('Description'),
                        'type' => 'text',
                        'required' => true,
                        'hint' => $this->l('Block description.')
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'type' => 'submit',
                )
            ),
        );

        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Interior Image'),
                'align' => 'center',
                'orderby' => false,
                'filter' => false,
                'search' => false,
                'callback' => 'getInteriorImage',
                'class' => 'fixed-width-xs',
            ),
            'display_name' => array(
                'title' => $this->l('Display Name'),
                'align' => 'text-center',
            ),
            'active' => array(
                'title' => $this->l('Active'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'filter_key' => 'a!position',
                'position' => 'position',
                'align' => 'center',
            ),
            'date_add' => array(
                'title' => $this->l('Date Add'),
                'align' => 'center',
                'type' => 'datetime',
                'filter_key' => 'a!date_add',
                'class' => 'fixed-width-xs'
            ),
        );
        $this->identifier = 'id';
        
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

    public function getInteriorImage($img_name)
    {
        $image = _PS_MODULE_DIR_.$this->module->name.'/views/img/hotel_interior/'.$img_name;
        if (file_exists($image)) {
            return '<img src="'._MODULE_DIR_.'wkabouthotelblock/views/img/hotel_interior/'.$img_name.'" class="img-thumbnail htlInteriorImg">';
        } else {
            return '--';
        }
    }

    public function renderList()
    {
        $this->informations[] = $this->l('For better view, upload hotel interior image in multiple of 3.');

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add New Hotel Image'),
        );

        return parent::renderList();
    }

    public function renderForm()
    {
        $image_url = $image_size = false;

        if ($this->display == 'edit')
        {
            $id_htl_interior = Tools::getValue('id');
            $obj_inter_img = new WkHotelInteriorImage($id_htl_interior);
            $img_name = $obj_inter_img->name;

            $image = _PS_MODULE_DIR_.$this->module->name.'/views/img/hotel_interior/'.$img_name;
	        $image_url = ImageManager::thumbnail($image, $this->table.'_'.(int)$id_htl_interior.'.'.$this->imageType, 350,
	            $this->imageType, true, true);
	        $image_size = file_exists($image) ? filesize($image) / 1000 : false;
        }
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Add New Hotel Interior Image'),
                'icon' => 'icon-list-ul'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Image Display name'),
                    'name' => 'display_name',
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Hotel Interior Image'),
                    'name' => 'interior_img',
                    'required' => true,
                    'display_image' => true,
                    'image' => $image_url ? $image_url : false,
                    'size' => $image_size,
                    'col' => 6,
                    'hint' => sprintf($this->l('Maximum image size: %1s'), Tools::formatBytes(Tools::getMaxUploadSize())),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'required' => false,
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
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );
        return parent::renderForm();
    }

    public function processSave()
    {
        $file = $_FILES['interior_img'];

        /*==== Validations ====*/
        if (Tools::getValue('display_name')) {
            if (!Validate::isCatalogName(Tools::getValue('display_name'))) {
                $this->errors[] = Tools::displayError($this->l('Please enter valid name.'));
            }
        }

        if (!(Tools::getValue("id") && !$file['size'])) {
	        if (!$file['size']) {
	            $this->errors[] = Tools::displayError($this->l('Hotel Interior Image Required.'));
	        } elseif ($file['error']) {
	            $this->errors[] = Tools::displayError($this->l('Cannot upload file.'));
	        } elseif (!(preg_match('/\.(jpe?g|gif|png)$/', $file['name']) && ImageManager::isRealImage($file['tmp_name'], $file['type']))) {
	            $this->errors[] = Tools::displayError($this->l('Please upload image file.'));
	        }
        }

        /*==== Validations ====*/

        if (!count($this->errors)) {
        	if (Tools::getValue("id")) {
        		$obj_inter_img = new WkHotelInteriorImage(Tools::getValue("id"));
        	} else {
        		$obj_inter_img = new WkHotelInteriorImage();
        	}

        	if (Tools::getValue("id") && $file['size'] && !$file['error']) {
        		unlink(_PS_MODULE_DIR_.$this->module->name.'/views/img/hotel_interior/'.$obj_inter_img->name);
        	}

        	if ($file['size']) {
	            do {
	                $tmp_name = uniqid().'.jpg';
	            } while (file_exists(_PS_MODULE_DIR_.$this->module->name.'/views/img/hotel_interior/'.$tmp_name));
                // $img_size = getimagesize($file['tmp_name']);
                // $final_width = (375 * $img_size[0]) / $img_size[1];     
	            ImageManager::resize($file['tmp_name'], _PS_MODULE_DIR_.$this->module->name.'/views/img/hotel_interior/'.$tmp_name);
            	
                $obj_inter_img->name = $tmp_name;
        	}

            $obj_inter_img->display_name = Tools::getValue('display_name');
            $obj_inter_img->active = Tools::getValue('active');
            $obj_inter_img->save();

            if (Tools::getValue("id")) {
                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
            }
        }
        else {
            if (Tools::getValue("id"))
                $this->display = 'edit';
            else
                $this->display = 'add';
        }
    }

    public function postProcess()
    {
        parent::postProcess();
        $this->addjQueryPlugin(array(
                'tablednd',
            ));
        $this->addJS(_MODULE_DIR_.'wkabouthotelblock/views/js/WkAboutHotelBlockAdmin.js');
        $this->addCSS(_MODULE_DIR_.'wkabouthotelblock/views/css/WkAboutHotelBlockAdmin.css');
    }
}