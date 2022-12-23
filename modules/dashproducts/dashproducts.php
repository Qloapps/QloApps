<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
	exit;
}

class DashProducts extends Module
{
	public function __construct()
	{
		$this->name = 'dashproducts';
		$this->tab = 'dashboard';
		$this->version = '1.0.1';
		$this->author = 'PrestaShop';

		$this->push_filename = _PS_CACHE_DIR_.'push/activity';
		$this->allow_push = true;

		parent::__construct();
		$this->displayName = $this->l('Dashboard Products');
		$this->description = $this->l('Adds a block with a table of your latest orders and a ranking of your products');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.7.0.99');
	}

	public function install()
	{
		Configuration::updateValue('DASHPRODUCT_NBR_SHOW_LAST_ORDER', 10);
		Configuration::updateValue('DASHPRODUCT_NBR_SHOW_BEST_SELLER', 10);
		Configuration::updateValue('DASHPRODUCT_NBR_SHOW_MOST_VIEWED', 10);
		Configuration::updateValue('DASHPRODUCT_NBR_SHOW_TOP_SEARCH', 10);

		return (parent::install()
			&& $this->registerHook('dashboardZoneTwo')
			&& $this->registerHook('dashboardData')
			&& $this->registerHook('actionAdminControllerSetMedia')
			&& $this->registerHook('actionObjectOrderAddAfter')
			&& $this->registerHook('actionSearch')
		);
	}

	public function hookActionAdminControllerSetMedia()
	{
		if (Tools::getValue('controller') == 'AdminDashboard') {
			$this->context->controller->addCSS($this->_path.'views/css/dashproducts.css');
		}
	}

	public function hookDashboardZoneTwo($params)
	{
		$this->context->smarty->assign(
			array(
				'DASHACTIVITY_CART_ACTIVE' => Configuration::get('DASHACTIVITY_CART_ACTIVE'),
				'DASHACTIVITY_VISITOR_ONLINE' => Configuration::get('DASHACTIVITY_VISITOR_ONLINE'),
				'DASHPRODUCT_NBR_SHOW_LAST_ORDER' => Configuration::get('DASHPRODUCT_NBR_SHOW_LAST_ORDER'),
				'DASHPRODUCT_NBR_SHOW_BEST_SELLER' => Configuration::get('DASHPRODUCT_NBR_SHOW_BEST_SELLER'),
				'DASHPRODUCT_NBR_SHOW_TOP_SEARCH' => Configuration::get('DASHPRODUCT_NBR_SHOW_TOP_SEARCH'),
				'date_from' => Tools::displayDate($params['date_from']),
				'date_to' => Tools::displayDate($params['date_to']),
				'dashproducts_config_form' => $this->renderConfigForm(),
			)
		);

		return $this->display(__FILE__, 'dashboard_zone_two.tpl');
	}

	public function hookDashboardData($params)
	{
		$table_recent_orders = $this->getTableRecentOrders($params['id_hotel']);
		$table_best_sellers = $this->getTableBestSellers($params['date_from'], $params['date_to'], $params['id_hotel']);
		$table_most_viewed = $this->getTableMostViewed($params['date_from'], $params['date_to'], $params['id_hotel']);
		$table_top_10_most_search = $this->getTableTop10MostSearch($params['date_from'], $params['date_to'], $params['id_hotel']);

		//$table_top_5_search = $this->getTableTop5Search();
		return array(
			'data_table' => array(
				'table_recent_orders' => $table_recent_orders,
				'table_best_sellers' => $table_best_sellers,
				'table_most_viewed' => $table_most_viewed,
				'table_top_10_most_search' => $table_top_10_most_search,
				//'table_top_5_search' => $table_top_5_search
			)
		);
	}

	public function getTableRecentOrders($idHotel)
	{
		$header = array(
			array('title' => $this->l('Customer Name'), 'class' => 'text-left'),
			array('title' => $this->l('Total Rooms'), 'class' => 'text-left'),
			array('title' => $this->l('Order'), 'class' => 'text-left'),
			array('title' => $this->l('Hotel'), 'class' => 'text-left'),
			array('title' => $this->l('Total').' '.$this->l('Tax excl.'), 'class' => 'text-left'),
			array('title' => $this->l('Date'), 'class' => 'text-left'),
			array('title' => $this->l('Status'), 'class' => 'text-left'),
			array('title' => '', 'class' => 'text-right'),
		);

		$limit = (int)Configuration::get('DASHPRODUCT_NBR_SHOW_LAST_ORDER') ? (int)Configuration::get('DASHPRODUCT_NBR_SHOW_LAST_ORDER') : 10;
		$orders = AdminStatsController::getRecentOrdersByHotel($idHotel, $limit);

		$body = array();
		foreach ($orders as $order) {
			$bookingInfo = Db::getInstance()->getRow(
				'SELECT COUNT(*) AS `total_rooms`, hbd.`id_hotel`, hbd.`hotel_name`
				FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
				WHERE `id_order` = '.(int)$order['id_order']
			);

			$currency = Currency::getCurrency((int)$order['id_currency']);
			$tr = array();
			$tr[] = array(
				'id' => 'firstname_lastname',
				'value' => '<a href="'.$this->context->link->getAdminLink('AdminCustomers', true).'&id_customer='.$order['id_customer'].'&viewcustomer">'.Tools::htmlentitiesUTF8($order['firstname']).' '.Tools::htmlentitiesUTF8($order['lastname']).'</a>',
				'class' => 'text-left',
			);
			$tr[] = array(
				'id' => 'total_products',
				'value' => $bookingInfo['total_rooms'],
				'class' => 'text-left',
			);
			$tr[] = array(
				'id' => 'order',
				'value' => '<a href="'.$this->context->link->getAdminLink('AdminOrders', true).
					'&id_order='.$order['id_order'].'&vieworder" target="_blank">#'.$order['id_order'].'</a>',
				'class' => 'text-left',
			);
			$tr[] = array(
				'id' => 'hotel',
				'value' => '<a href="'.$this->context->link->getAdminLink('AdminAddHotel', true).
					'&id='.$bookingInfo['id_hotel'].'&updatehtl_branch_info" target="_blank">'.
					Tools::htmlentitiesUTF8($bookingInfo['hotel_name']).'</a>',
				'class' => 'text-left',
			);
			$tr[] = array(
				'id' => 'total_paid',
				'value' => Tools::displayPrice((float)$order['total_paid'], $currency),
				'class' => 'text-left',
				'wrapper_start' => $order['valid'] ? '<span class="badge badge-success">' : '',
				'wrapper_end' => '<span>',
			);
			$tr[] = array(
				'id' => 'date_add',
				'value' => Tools::displayDate($order['date_add']),
				'class' => 'text-left',
			);
			$tr[] = array(
				'id' => 'status',
				'value' => Tools::htmlentitiesUTF8($order['state_name']),
				'class' => 'text-left',
			);
			$tr[] = array(
				'id' => 'details',
				'value' => '',
				'class' => 'text-right',
				'wrapper_start' => '<a class="btn btn-default" href="index.php?tab=AdminOrders&id_order='.(int)$order['id_order'].'&vieworder&token='.Tools::getAdminTokenLite('AdminOrders').'" title="'.$this->l('Details').'"><i class="icon-search"></i>',
				'wrapper_end' => '</a>'
			);

			$body[] = $tr;
		}

		return array('header' => $header, 'body' => $body);
	}

	public function getTableBestSellers($date_from, $date_to, $id_hotel)
	{
		$header = array(
			array(
				'id' => 'image',
				'title' => $this->l('Image'),
				'class' => 'text-center',
			),
			array(
				'id' => 'product',
				'title' => $this->l('Room type'),
				'class' => 'text-center',
			),
			array(
				'id' => 'category',
				'title' => $this->l('Hotel'),
				'class' => 'text-center',
			),
			array(
				'id' => 'total_sold',
				'title' => $this->l('Total bookings'),
				'class' => 'text-center',
			),
			array(
				'id' => 'sales',
				'title' => $this->l('Sales'),
				'class' => 'text-center',
			),
		);

		$products = Db::getInstance()->ExecuteS(
			'SELECT
				hbd.`id_product`,
				hbd.`room_type_name` AS `product_name`,
				COUNT(hbd.`id_room`) AS `total`,
				hbd.`total_price_tax_excl` AS `price`,
				SUM(hbd.`total_price_tax_excl`) AS `sales`
			FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
			LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = hbd.`id_order`)
			WHERE o.`invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
			AND o.`valid` = 1 AND hbd.`is_refunded` = 0.'.
			(!is_null($id_hotel) ? HotelBranchInformation::addHotelRestriction($id_hotel, 'hbd') : '').'
			GROUP BY hbd.`id_product`
			ORDER BY `sales` DESC
			LIMIT '.(int) Configuration::get('DASHPRODUCT_NBR_SHOW_BEST_SELLER', 10)
		);

		$body = array();
		foreach ($products as $product) {
			$product_obj = new Product((int)$product['id_product'], false, $this->context->language->id);
			if (!Validate::isLoadedObject($product_obj)) {
				continue;
			}

			$objHotelRoomType = new HotelRoomType();
			$roomTypeInfo = $objHotelRoomType->getRoomTypeInfoByIdProduct($product_obj->id);

			$img = '';
			if (($row_image = Product::getCover($product_obj->id)) && $row_image['id_image'])
			{
				$image = new Image($row_image['id_image']);
				$path_to_image = _PS_PROD_IMG_DIR_.$image->getExistingImgPath().'.'.$this->context->controller->imageType;
				$img = ImageManager::thumbnail($path_to_image, 'product_mini_'.$product_obj->id.'.'.$this->context->controller->imageType, 45, $this->context->controller->imageType);
			}

			$productPrice = $product['price'];

			$body[] = array(
				array(
					'id' => 'product',
					'value' => $img,
					'class' => 'text-center'
				),
				array(
					'id' => 'product',
					'value' => '<a href="'.$this->context->link->getAdminLink('AdminProducts', true).
						'&id_product='.$product_obj->id.'&updateproduct" target="_blank">'.
						Tools::htmlentitiesUTF8($product['product_name']).'</a>'.
						'<br/>'.Tools::displayPrice($productPrice),
					'class' => 'text-center'
				),
				array(
					'id' => 'category',
					'value' => '<a href="'.$this->context->link->getAdminLink('AdminAddHotel', true).
						'&id='.$roomTypeInfo['id_hotel'].'&updatehtl_branch_info" target="_blank">'.
						Tools::htmlentitiesUTF8($roomTypeInfo['hotel_name']).'</a>',
					'class' => 'text-center'
				),
				array(
					'id' => 'total_sold',
					'value' => $product['total'],
					'class' => 'text-center'
				),
				array(
					'id' => 'sales',
					'value' => Tools::displayPrice($product['sales']),
					'class' => 'text-center'
				),
			);
		}

		return array('header' => $header, 'body' => $body);
	}

	public function getTableMostViewed($date_from, $date_to, $id_hotel)
	{
		$header = array(
			array(
				'id' => 'image',
				'title' => $this->l('Image'),
				'class' => 'text-center',
			),
			array(
				'id' => 'product',
				'title' => $this->l('Room type'),
				'class' => 'text-center',
			),
			array(
				'id' => 'hotel',
				'title' => $this->l('Hotel'),
				'class' => 'text-center',
			),
			array(
				'id' => 'views',
				'title' => $this->l('Views'),
				'class' => 'text-center',
			),
			array(
				'id' => 'added_to_cart',
				'title' => $this->l('Added to cart'),
				'class' => 'text-center',
			),
			array(
				'id' => 'purchased',
				'title' => $this->l('Booked'),
				'class' => 'text-center',
			),
			array(
				'id' => 'rate',
				'title' => $this->l('Conversion rate'),
				'class' => 'text-center',
			)
		);

		if (Configuration::get('PS_STATSDATA_PAGESVIEWS')) {
			$products = $this->getTotalViewed($date_from, $date_to, (int)Configuration::get('DASHPRODUCT_NBR_SHOW_MOST_VIEWED'));
			$body = array();
			if (is_array($products) && count($products)) {
				foreach ($products as $product) {
					$product_obj = new Product((int)$product['id_object'], true, $this->context->language->id);
					if (!Validate::isLoadedObject($product_obj)) {
						continue;
					}

					$img = '';
					if (($row_image = Product::getCover($product_obj->id)) && $row_image['id_image']) {
						$image = new Image($row_image['id_image']);
						$path_to_image = _PS_PROD_IMG_DIR_.$image->getExistingImgPath().'.'.$this->context->controller->imageType;
						$img = ImageManager::thumbnail($path_to_image, 'product_mini_'.$product_obj->id.'.'.$this->context->controller->imageType, 45, $this->context->controller->imageType);
					}

					$objHRT = new HotelRoomType($product_obj->id);
					$objHBI = new HotelBranchInformation($objHRT->id_hotel, $this->context->language->id);

					$tr = array();
					$tr[] = array(
						'id' => 'product',
						'value' => $img,
						'class' => 'text-center'
					);
					$tr[] = array(
						'id' => 'product',
						'value' => '<a href="'.$this->context->link->getAdminLink('AdminProducts', true).
							'&id_product='.$product_obj->id.'&updateproduct" target="_blank">'.
							Tools::htmlentitiesUTF8($product_obj->name).'</a>'.'<br/>'.
							Tools::displayPrice(Product::getPriceStatic((int)$product_obj->id)),
						'class' => 'text-center',
					);
					$tr[] = array(
						'id' => 'hotel',
						'value' => '<a href="'.$this->context->link->getAdminLink('AdminAddHotel', true).
							'&id='.$objHBI->id.'&updatehtl_branch_info" target="_blank">'.
							Tools::htmlentitiesUTF8($objHBI->hotel_name).'</a>',
						'class' => 'text-center',
					);
					$tr[] = array(
						'id' => 'views',
						'value' => $product['counter'],
						'class' => 'text-center',
					);
					$added_cart = $this->getTotalProductAddedCart($date_from, $date_to, (int)$product_obj->id);
					$tr[] = array(
						'id' => 'added_to_cart',
						'value' => $added_cart,
						'class' => 'text-center',
					);
					$purchased = $this->getTotalProductPurchased($date_from, $date_to, (int)$product_obj->id);
					$tr[] = array(
						'id' => 'purchased',
						'value' => $purchased,
						'class' => 'text-center',
					);
					$tr[] = array(
						'id' => 'rate',
						'value' => ($product['counter'] ? round(100 * $purchased / $product['counter'], 1).'%' : '-'),
						'class' => 'text-center',
					);
					$body[] = $tr;
				}
			}
		} else {
			$link = $this->context->link->getAdminLink('AdminModules').'&configure=statsdata';
			$body = '<div class="alert alert-info text-left">'.$this->l('You must enable the "Save global page views" option from ').'<u><a href="'.$link.'" target="_blank">Data mining for statistics</a></u>'.$this->l(' module in order to display the most viewed room types, or use the QloApps Google Analytics module.').'</div>';
		}
		return array('header' => $header, 'body' => $body);
	}

	public function getTableTop10MostSearch($date_from, $date_to)
	{
		$header = array(
			array(
				'id' => 'reference',
				'title' => $this->l('Hotel'),
				'class' => 'text-center'
			),
			array(
				'id' => 'image',
				'title' => $this->l('Cover image'),
				'class' => 'text-center'
			),
			array(
				'id' => 'location',
				'title' => $this->l('Location'),
				'class' => 'text-center'
			),
			array(
				'id' => 'count',
				'title' => $this->l('Count'),
				'class' => 'text-center'
			),
			// array(
			// 	'id' => 'totalQuantitySold',
			// 	'title' => $this->l('Results'),
			// 	'class' => 'text-center'
			// )
		);

		if (Configuration::get('PS_STATSDATA_PAGESVIEWS')) {
			$hotels = $this->getMostSearchedHotels(
				$date_from,
				$date_to,
				(int)Configuration::get('DASHPRODUCT_NBR_SHOW_TOP_SEARCH')
			);

			$body = array();
			if (is_array($hotels) && count($hotels))
				foreach ($hotels as $hotel) {
					$objHBI = new HotelBranchInformation($hotel['id_hotel'], $this->context->language->id);
					$addressInfo = $objHBI->getAddress($hotel['id_hotel']);
					$tr = array();
					$tr[] = array(
						'id' => 'reference',
						'value' => '<a href="'.$this->context->link->getAdminLink('AdminAddHotel', true).
						'&id='.$hotel['id_hotel'].'&updatehtl_branch_info" target="_blank">'.
						Tools::htmlentitiesUTF8($hotel['hotel_name']).'</a>',
						'class' => 'text-center',
					);
					$tr[] = array(
						'id' => 'image',
						'value' => $this->getHotelImage($objHBI->id),
						'class' => 'text-center',
					);
					$tr[] = array(
						'id' => 'location',
						'value' => $addressInfo['address1'],
						'class' => 'text-center',
					);
					$tr[] = array(
						'id' => 'views',
						'value' => $hotel['views'],
						'class' => 'text-center',
					);
					$body[] = $tr;
				}
		} else {
			$link = $this->context->link->getAdminLink('AdminModules').'&configure=statsdata';
			$body = '<div class="alert alert-info text-left">'.$this->l('You must enable the "Save global page views" option from ').'<u><a href="'.$link.'" target="_blank">Data mining for statistics</a></u>'.$this->l(' module in order to display the most viewed room types, or use the QloApps Google Analytics module.').'</div>';
		}

		return array('header' => $header, 'body' => $body);
	}

    public function getHotelImage($idHotel)
    {
        $imageDir = _MODULE_DIR_.'hotelreservationsystem/views/img/hotel_img/';
        $noPictureImagePath = _PS_IMG_.'p/en.jpg';
        $hotelImage = HotelImage::getCover($idHotel);
        $imgLink = '';
        if (is_array($hotelImage) && count($hotelImage)) {
            $imagePath = $imageDir.$hotelImage['hotel_image_id'].'.jpg';
            $imgLink = $imagePath;
        } else {
            $imgLink = $noPictureImagePath;
        }

        return '<img src="'.$imgLink.'" class="img img-thumbnail hotel-thumbnail">';
    }

	public function getTableTop5Search()
	{
		$header = array(
			array(
				'id' => 'reference',
				'title' => $this->l('Product'),
			)
		);

		$body = array();

		return array('header' => $header, 'body' => $body);
	}

	public function getTotalProductSales($date_from, $date_to, $id_product)
	{
		$sql = 'SELECT SUM(od.`product_quantity` * od.`product_price`) AS total
				FROM `'._DB_PREFIX_.'order_detail` od
				JOIN `'._DB_PREFIX_.'orders` o ON o.`id_order` = od.`id_order`
				WHERE od.`product_id` = '.(int)$id_product.'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
					AND o.valid = 1
					AND o.`date_add` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"';

		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
	}

	public function getTotalProductAddedCart($date_from, $date_to, $id_product)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT count(`id_product`) as count
		FROM `'._DB_PREFIX_.'cart_product` cp
		WHERE cp.`id_product` = '.(int)$id_product.'
		'.Shop::addSqlRestriction(false, 'cp').'
		AND cp.`date_add` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"');
	}

	public function getTotalProductPurchased($date_from, $date_to, $id_product)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
			'SELECT COUNT(hbd.`id_product`) AS `count`
			FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
			WHERE hbd.`is_refunded` = 0 AND hbd.`is_back_order` = 0 AND hbd.`id_product` = '.(int)$id_product.'
			AND hbd.`date_add` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"'
		);
	}

	public function getTotalViewed($date_from, $date_to, $limit = 10)
	{
		$objGoogleAnalytics = Module::isEnabled('qlogoogleanalytics') ? Module::getInstanceByName('qlogoogleanalytics') : false;
        if (Validate::isLoadedObject($objGoogleAnalytics) && $objGoogleAnalytics->isConfigured()) {
			$products = array();
			// Only works with the default product URL pattern at this time
			if ($result = $objGoogleAnalytics->requestReportData('ga:pagePath', 'ga:visits', $date_from, $date_to, '-ga:visits', 'ga:pagePath=~/([a-z]{2}/)?([a-z]+/)?[0-9][0-9]*\-.*\.html$', 1, $limit)) {
				foreach ($result as $row) {
					if (preg_match('@/([a-z]{2}/)?([a-z]+/)?([0-9]+)\-.*\.html@', $row['dimensions']['pagePath'], $matches)) {
						$products[] = array('id_object' => (int)$matches[3], 'counter' => $row['metrics']['visits']);
					}
				}
			}

			return $products;
		} else {
			return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT p.id_object, SUM(pv.counter) AS `counter`
			FROM `'._DB_PREFIX_.'page_viewed` pv
			LEFT JOIN `'._DB_PREFIX_.'date_range` dr ON pv.`id_date_range` = dr.`id_date_range`
			LEFT JOIN `'._DB_PREFIX_.'page` p ON pv.`id_page` = p.`id_page`
			LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON pt.`id_page_type` = p.`id_page_type`
			LEFT JOIN `'._DB_PREFIX_.'htl_room_type` hrt ON hrt.`id_product` = p.`id_object`
			WHERE pt.`name` = \'product\'
			'.Shop::addSqlRestriction(false, 'pv').'
			AND dr.`time_start` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
			AND dr.`time_end` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"'.
			(!is_null($id_hotel) ? HotelBranchInformation::addHotelRestriction($id_hotel, 'hrt') : '').'
			GROUP BY p.id_object
			ORDER BY `counter` DESC
			LIMIT '.(int)$limit);
		}
	}

	public function getMostSearchedHotels($date_from, $date_to, $limit = 10)
	{
		if (!Module::isInstalled('statsdata')) {
			return array();
		}

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
			'SELECT hbi.`id` AS `id_hotel`, cl.`name` AS `hotel_name`, pv.`counter` AS `views`
			FROM `'._DB_PREFIX_.'page_viewed` pv
			LEFT JOIN `'._DB_PREFIX_.'page` p ON (p.`id_page` = pv.`id_page`)
			LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON (pt.`id_page_type` = p.`id_page_type`)
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (cl.`id_category` = p.`id_object`)
			LEFT JOIN `'._DB_PREFIX_.'htl_branch_info` hbi ON (hbi.`id_category` = cl.`id_category`)
			LEFT JOIN `'._DB_PREFIX_.'date_range` dr ON (pv.`id_date_range` = dr.`id_date_range`)
			WHERE pt.`name` = "'.pSQL('category').'"
			AND dr.`time_start` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
			AND dr.`time_end` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
			GROUP BY p.`id_page_type`, p.`id_object`
			LIMIT '.(int)$limit
		);
	}

	public function renderConfigForm()
	{
		$fields_form = array(
			'form' => array(
				'input' => array(),
				'submit' => array(
					'title' => $this->l('   Save   '),
					'class' => 'btn btn-default pull-right submit_dash_config',
					'reset' => array(
						'title' => $this->l('Cancel'),
						'class' => 'btn btn-default cancel_dash_config',
					)
				)
			),
		);

		$inputs = array(
			array(
				'label' => $this->l('Number of "Recent Bookings" to display'),
				'config_name' => 'DASHPRODUCT_NBR_SHOW_LAST_ORDER'
			),
			array(
				'label' => $this->l('Number of "Best Selling" to display'),
				'config_name' => 'DASHPRODUCT_NBR_SHOW_BEST_SELLER'
			),
			array(
				'label' => $this->l('Number of "Most Viewed" to display'),
				'config_name' => 'DASHPRODUCT_NBR_SHOW_MOST_VIEWED'
			),
			array(
				'label' => $this->l('Number of "Top Searches" to display'),
				'config_name' => 'DASHPRODUCT_NBR_SHOW_TOP_SEARCH'
			),
		);

		foreach ($inputs as $input)
			$fields_form['form']['input'][] = array(
				'type' => 'select',
				'label' => $input['label'],
				'name' => $input['config_name'],
				'options' => array(
					'query' => array(
						array('id' => 5, 'name' => 5),
						array('id' => 10, 'name' => 10),
						array('id' => 20, 'name' => 20),
						array('id' => 50, 'name' => 50),
					),
					'id' => 'id',
					'name' => 'name',
				)
			);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->id = (int)Tools::getValue('id_carrier');
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitDashConfig';
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		return array(
			'DASHPRODUCT_NBR_SHOW_LAST_ORDER' => Configuration::get('DASHPRODUCT_NBR_SHOW_LAST_ORDER'),
			'DASHPRODUCT_NBR_SHOW_BEST_SELLER' => Configuration::get('DASHPRODUCT_NBR_SHOW_BEST_SELLER'),
			'DASHPRODUCT_NBR_SHOW_MOST_VIEWED' => Configuration::get('DASHPRODUCT_NBR_SHOW_MOST_VIEWED'),
			'DASHPRODUCT_NBR_SHOW_TOP_SEARCH' => Configuration::get('DASHPRODUCT_NBR_SHOW_TOP_SEARCH'),
		);
	}

	public function hookActionObjectOrderAddAfter($params)
	{
		Tools::changeFileMTime($this->push_filename);
	}

	public function hookActionSearch($params)
	{
		Tools::changeFileMTime($this->push_filename);
	}
}
