<?php
class AdminHotelRoomModuleSettingController extends ModuleAdminController 
{
	public function __construct() 
	{
		$this->table = 'htl_room_block_data';
		$this->className = 'WkHotelRoomDisplay';
		$this->bootstrap = true;
		$this->context = Context::getContext();

		$this->fields_options = array(
			'global' => array(
				'title' =>	$this->l('Hotel Room Display Setting'),
				'icon' =>   'icon-cogs',
				'fields' =>	array(
					'HOTEL_ROOM_DISPLAY_HEADING' => array(
						'title' => $this->l('Hotel Room Block Title'),
						'type' => 'text',
						'required' => 'true',
						'validation' => 'isCatalogName',
						'id' => 'HOTEL_ROOM_DISPLAY_HEADING',
						'hint' => $this->l('Block Heading. Ex: Our Rooms.'),
					),
					'HOTEL_ROOM_DISPLAY_DESCRIPTION' => array(
						'title' => $this->l('Hotel Room Block Description'),
						'type' => 'textarea',
						'required' => 'true',
						'id' => 'HOTEL_ROOM_DISPLAY_DESCRIPTION',
						'validation' => 'isCatalogName',
						'rows' => '4',
						'cols' => '2',
						'hint' => $this->l('Block description.'),
					),
				),
				'submit' => array('title' => $this->l('Save'))
			),
		);
		
		$this->fields_list = array(
			'id' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
			),
			'id_product' => array(
                'title' => $this->l('Room Image'),
                'align' => 'center',
                'orderby' => false,
                'filter' => false,
                'search' => false,
                'callback' => 'getProductImage',
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

    public function getProductImage($id_product)
    {
        $product_obj = new Product($id_product, false, Configuration::get('PS_LANG_DEFAULT'));
        $cover_image_id = Product::getCover($product_obj->id);
        if($cover_image_id)
            $prod_img = $this->context->link->getImageLink($product_obj->link_rewrite, $product_obj->id.'-'.$cover_image_id['id_image'], 'home_default');
        else 
            $prod_img = $this->context->link->getImageLink($product_obj->link_rewrite, $this->context->language->iso_code."-default", 'home_default');
        return '<img src="'.$prod_img.'" class="img-thumbnail htlRoomImg">';
    }

	public function renderList() 
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->page_header_toolbar_btn['new'] = array(
			'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
			'desc' => $this->l('Add New Hotel Room Block')
		);

		return parent::renderList();
	}

    public function renderForm() 
	{
		if (!($obj = $this->loadObject(true))) {
            return;
        }

        $prod_name = $id_product = false;

        if ($this->display == 'edit') {
            $obj_room_block = new WkHotelRoomDisplay($obj->id);
            $id_product = $obj_room_block->id_product;
            $product = new Product($id_product, false, Configuration::get('PS_LANG_DEFAULT'));
            $prod_name = $product->name;
        }
        $html = '<div class="input-group col-lg-5">';
            $html .= '<input type="text" value="'.$prod_name.'" name="productName" id="productName" class="form-control" autocomplete="off">';
            $html .= '<input type="hidden" value="'.$id_product.'" name="id_product" id="id_product" class="form-control">';
            $html .= '<span class="input-group-addon"><i class="icon-search"></i></span>';
            $html .= '<ul class="list-unstyled prod_suggest_ul"></ul>';
        $html .= '</div>';

		$this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Rooms Configuration'),
                'icon' => 'icon-globe'
            ),
            'input' => array(
                array(
                    'label' => $this->l('Search Room Type'),
                    'type' => 'html',
                    'name' => 'product_search',
                    'html_content' => $html,
                    'required' => true,
                    'hint' => $this->l('Select Room Type which you want to display in home page.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'required' => true,
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
				'title' => $this->l('Save')
			));
		
		return parent::renderForm();
	}

	public function processSave()
	{
        $idRoomBlock = Tools::getValue('id');
        $id_product = Tools::getValue('id_product');
        if ($idRoomBlock)
            $obj_room_block = new WkHotelRoomDisplay($idRoomBlock);
        else        
            $obj_room_block = new WkHotelRoomDisplay();

		/*==== Validations ====*/
        if (!Tools::getValue('id_product')) {
            $this->errors[] = Tools::displayError($this->l('Please select Room Type.'));
        } elseif (!Validate::isUnsignedId(Tools::getValue('id_product'))) {
            $this->errors[] = Tools::displayError($this->l('Please enter valid room type.'));
        }
        else {
            $checkRoomTpeEntry = $obj_room_block->checkRoomTypeAlreadySelected($id_product, $idRoomBlock);
            if ($checkRoomTpeEntry) {
                $this->errors[] = Tools::displayError($this->l('This Room Type is already selected.'));
            }
        }
        /*==== Validations ====*/

		if (!count($this->errors)) {
			$obj_room_block->id_product = $id_product;
            $obj_room_block->active = Tools::getValue('active');
            $obj_room_block->save();
            $idRoomBlock = $obj_room_block->id; 

			if (Tools::getValue("id")) {
                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
            }
		} else {
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
        $this->addJS(_MODULE_DIR_.'wkhotelroom/views/js/WkHotelRoomBlockAdmin.js');
        $this->addCSS(_MODULE_DIR_.'wkhotelroom/views/css/WkHotelRoomBlockAdmin.css');
    }
}