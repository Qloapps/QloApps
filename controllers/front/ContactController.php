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

class ContactControllerCore extends FrontController
{
    public $php_self = 'contact';
    public $ssl = true;

    /**
    * Start forms process
    * @see FrontController::postProcess()
    */
    public function postProcess()
    {
        if (Tools::isSubmit('submitMessage')) {
            $saveContactKey = $this->context->cookie->contactFormKey;
            $extension = array('.txt', '.rtf', '.doc', '.docx', '.pdf', '.zip', '.png', '.jpeg', '.gif', '.jpg');
            $file_attachment = Tools::fileAttachment('fileUpload');
            $message = Tools::getValue('message'); // Html entities is not usefull, iscleanHtml check there is no bad html tags.
            $url = Tools::getValue('url');
            if (!($from = trim(Tools::getValue('from'))) || !Validate::isEmail($from)) {
                $this->errors[] = Tools::displayError('Invalid email address.');
            } elseif (!$message) {
                $this->errors[] = Tools::displayError('The message cannot be blank.');
            } elseif (!Validate::isCleanHtml($message)) {
                $this->errors[] = Tools::displayError('Invalid message');
            } elseif (!($id_contact = (int)Tools::getValue('id_contact')) || !(Validate::isLoadedObject($contact = new Contact($id_contact, $this->context->language->id)))) {
                $this->errors[] = Tools::displayError('Please select a subject from the list provided. ');
            } elseif (!empty($file_attachment['name']) && $file_attachment['error'] != 0) {
                $this->errors[] = Tools::displayError('An error occurred during the file-upload process.');
            } elseif (!empty($file_attachment['name']) && !in_array(Tools::strtolower(substr($file_attachment['name'], -4)), $extension) && !in_array(Tools::strtolower(substr($file_attachment['name'], -5)), $extension)) {
                $this->errors[] = Tools::displayError('Bad file extension');
            } elseif ($url === false || !empty($url) || $saveContactKey != (Tools::getValue('contactKey'))) {
                $this->errors[] = Tools::displayError('An error occurred while sending the message.');
            } else {
                $customer = $this->context->customer;
                if (!$customer->id) {
                    $customer->getByEmail($from);
                }

                $id_order = (int)$this->getOrder();

                /**
                 * Check if customer select his order.
                 */
                if (!empty($id_order)) {
                    $order = new Order($id_order);
                    $id_order = (int) $order->id_customer === (int) $customer->id ? $id_order : 0;
                }

                if (!((
                        ($id_customer_thread = (int)Tools::getValue('id_customer_thread'))
                        && (int)Db::getInstance()->getValue('
						SELECT cm.id_customer_thread FROM '._DB_PREFIX_.'customer_thread cm
						WHERE cm.id_customer_thread = '.(int)$id_customer_thread.' AND cm.id_shop = '.(int)$this->context->shop->id.' AND token = \''.pSQL(Tools::getValue('token')).'\'')
                    ) || (
                        $id_customer_thread = CustomerThread::getIdCustomerThreadByEmailAndIdOrder($from, $id_order)
                    ))) {
                    $fields = Db::getInstance()->executeS('
					SELECT cm.id_customer_thread, cm.id_contact, cm.id_customer, cm.id_order, cm.id_product, cm.email
					FROM '._DB_PREFIX_.'customer_thread cm
					WHERE email = \''.pSQL($from).'\' AND cm.id_shop = '.(int)$this->context->shop->id.' AND ('.
                        ($customer->id ? 'id_customer = '.(int)$customer->id.' OR ' : '').'
						id_order = '.(int)$id_order.')');
                    $score = 0;
                    foreach ($fields as $key => $row) {
                        $tmp = 0;
                        if ((int)$row['id_customer'] && $row['id_customer'] != $customer->id && $row['email'] != $from) {
                            continue;
                        }
                        if ($row['id_order'] != 0 && $id_order != $row['id_order']) {
                            continue;
                        }
                        if ($row['email'] == $from) {
                            $tmp += 4;
                        }
                        if ($row['id_contact'] == $id_contact) {
                            $tmp++;
                        }
                        if (Tools::getValue('id_product') != 0 && $row['id_product'] == Tools::getValue('id_product')) {
                            $tmp += 2;
                        }
                        if ($tmp >= 5 && $tmp >= $score) {
                            $score = $tmp;
                            $id_customer_thread = $row['id_customer_thread'];
                        }
                    }
                }
                $old_message = Db::getInstance()->getValue('
					SELECT cm.message FROM '._DB_PREFIX_.'customer_message cm
					LEFT JOIN '._DB_PREFIX_.'customer_thread cc on (cm.id_customer_thread = cc.id_customer_thread)
					WHERE cc.id_customer_thread = '.(int)$id_customer_thread.' AND cc.id_shop = '.(int)$this->context->shop->id.'
					ORDER BY cm.date_add DESC');
                if ($old_message == $message) {
                    $this->context->smarty->assign('alreadySent', 1);
                    $contact->email = '';
                    $contact->customer_service = 0;
                }

                if ($contact->customer_service) {
                    if ((int)$id_customer_thread) {
                        $ct = new CustomerThread($id_customer_thread);
                        $ct->status = 'open';
                        $ct->id_lang = (int)$this->context->language->id;
                        $ct->id_contact = (int)$id_contact;
                        $ct->id_order = (int)$id_order;
                        if ($id_product = (int)Tools::getValue('id_product')) {
                            $ct->id_product = $id_product;
                        }
                        $ct->update();
                    } else {
                        $ct = new CustomerThread();
                        if (isset($customer->id)) {
                            $ct->id_customer = (int)$customer->id;
                        }
                        $ct->id_shop = (int)$this->context->shop->id;
                        $ct->id_order = (int)$id_order;
                        if ($id_product = (int)Tools::getValue('id_product')) {
                            $ct->id_product = $id_product;
                        }
                        $ct->id_contact = (int)$id_contact;
                        $ct->id_lang = (int)$this->context->language->id;
                        $ct->email = $from;
                        $ct->status = 'open';
                        $ct->token = Tools::passwdGen(12);
                        $ct->add();
                    }

                    if ($ct->id) {
                        $cm = new CustomerMessage();
                        $cm->id_customer_thread = $ct->id;
                        $cm->message = $message;
                        if (isset($file_attachment['rename']) && !empty($file_attachment['rename']) && rename($file_attachment['tmp_name'], _PS_UPLOAD_DIR_.basename($file_attachment['rename']))) {
                            $cm->file_name = $file_attachment['rename'];
                            @chmod(_PS_UPLOAD_DIR_.basename($file_attachment['rename']), 0664);
                        }
                        $cm->ip_address = (int)ip2long(Tools::getRemoteAddr());
                        $cm->user_agent = $_SERVER['HTTP_USER_AGENT'];
                        if (!$cm->add()) {
                            $this->errors[] = Tools::displayError('An error occurred while sending the message.');
                        }
                    } else {
                        $this->errors[] = Tools::displayError('An error occurred while sending the message.');
                    }
                }

                if (!count($this->errors)) {
                    $var_list = array(
                                    '{order_name}' => '-',
                                    '{attached_file}' => '-',
                                    '{message}' => Tools::nl2br(stripslashes($message)),
                                    '{email}' =>  $from,
                                    '{product_name}' => '',
                                );

                    if (isset($file_attachment['name'])) {
                        $var_list['{attached_file}'] = $file_attachment['name'];
                    }

                    $id_product = (int)Tools::getValue('id_product');

                    if (isset($ct) && Validate::isLoadedObject($ct) && $ct->id_order) {
                        $order = new Order((int)$ct->id_order);
                        $var_list['{order_name}'] = $order->getUniqReference();
                        $var_list['{id_order}'] = (int)$order->id;
                    }

                    if ($id_product) {
                        $product = new Product((int)$id_product);
                        if (Validate::isLoadedObject($product) && isset($product->name[Context::getContext()->language->id])) {
                            $var_list['{product_name}'] = $product->name[Context::getContext()->language->id];
                        }
                    }

                    if (!empty($contact->email)) {
                        if (!Mail::Send($this->context->language->id, 'contact', Mail::l('Message from contact form').' [no_sync]',
                            $var_list, $contact->email, $contact->name, null, null,
                                    $file_attachment, null,    _PS_MAIL_DIR_, false, null, null, $from)) {
                            $this->errors[] = Tools::displayError('An error occurred while sending the message.');
                        }
                    }
                }

                if (count($this->errors) > 1) {
                    array_unique($this->errors);
                } elseif (!count($this->errors)) {
                    $this->context->smarty->assign('confirmation', 1);
                }
            }
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'contact-form.css');
        $this->addJS(_THEME_JS_DIR_.'contact-form.js');
        $this->addJS(_PS_JS_DIR_.'validate.js');

        // GOOGLE MAP
        $language = $this->context->language;
        $country = $this->context->country;
        $PS_API_KEY = Configuration::get('PS_API_KEY');
        $this->addJs("https://maps.googleapis.com/maps/api/js?key=$PS_API_KEY&libraries=places&language=$language->iso_code&region=$country->iso_code");
    }

    /**
    * Assign template vars related to page content
    * @see FrontController::initContent()
    */
    public function initContent()
    {
        parent::initContent();

        $this->assignOrderList();

        $email = Tools::safeOutput(Tools::getValue('from',
        ((isset($this->context->cookie) && isset($this->context->cookie->email) && Validate::isEmail($this->context->cookie->email)) ? $this->context->cookie->email : '')));
        $this->context->smarty->assign(array(
            'errors' => $this->errors,
            'email' => $email,
            'fileupload' => Configuration::get('PS_CUSTOMER_SERVICE_FILE_UPLOAD'),
            'max_upload_size' => (int)Tools::getMaxUploadSize()
        ));

        if (($id_customer_thread = (int)Tools::getValue('id_customer_thread')) && $token = Tools::getValue('token')) {
            $customer_thread = Db::getInstance()->getRow('
				SELECT cm.*
				FROM '._DB_PREFIX_.'customer_thread cm
				WHERE cm.id_customer_thread = '.(int)$id_customer_thread.'
				AND cm.id_shop = '.(int)$this->context->shop->id.'
				AND token = \''.pSQL($token).'\'
			');

            $order = new Order((int)$customer_thread['id_order']);
            if (Validate::isLoadedObject($order)) {
                $customer_thread['reference'] = $order->getUniqReference();
            }
            $this->context->smarty->assign('customerThread', $customer_thread);
        }
        // Send Hotel location information
        $gblHtlAddress = Configuration::get('WK_HOTEL_GLOBAL_ADDRESS');
        $gblHtlPhone = Configuration::get('WK_HOTEL_GLOBAL_CONTACT_NUMBER');
        $gblHtlEmail = Configuration::get('WK_HOTEL_GLOBAL_CONTACT_EMAIL');
        $objHotelInfo = new HotelBranchInformation();
        if ($hotelsInfo = $objHotelInfo->hotelBranchesInfo(false, 1, 1)) {
            foreach ($hotelsInfo as &$hotel) {
                if (isset($hotel['id_cover_img'])
                    && $hotel['id_cover_img']
                    && Validate::isLoadedObject(
                        $objHotelImage = new HotelImage($hotel['id_cover_img'])
                    )
                ) {
                    // by webkul to get media link.
                    $htlImgLink = $this->context->link->getMediaLink($objHotelImage->getImageLink($hotel['id_cover_img'], ImageType::getFormatedName('medium')));

                    if ((bool)Tools::file_get_contents($htlImgLink)) {
                        $hotel['image_url'] = $htlImgLink;
                    } else {
                        $hotel['image_url'] = $this->context->link->getMediaLink(_MODULE_DIR_.'hotelreservationsystem/views/img/Slices/hotel-default-icon.png');
                    }
                } else {
                    $hotel['image_url'] = $this->context->link->getMediaLink(_MODULE_DIR_.'hotelreservationsystem/views/img/Slices/hotel-default-icon.png');
                }
            }
        }

	    $contactKey = md5(uniqid(microtime(), true));
        $this->context->cookie->__set('contactFormKey', $contactKey);
        $this->context->smarty->assign(
            array(
                'hotelsInfo' => $hotelsInfo,
                'gblHtlPhone' => $gblHtlPhone,
                'gblHtlEmail' => $gblHtlEmail,
                'gblHtlAddress' => $gblHtlAddress,
                'contacts' => Contact::getContacts($this->context->language->id),
                'message' => html_entity_decode(Tools::getValue('message')),
	            'contactKey' => $contactKey,
            )
        );

        //By webkul to send hotels Map Informations for google Map.
        if (Configuration::get('WK_GOOGLE_ACTIVE_MAP')) {
            $hotelLocationArray = $objHotelInfo->getMapFormatHotelsInfo(Configuration::get('WK_MAP_HOTEL_ACTIVE_ONLY'));
            if ($hotelLocationArray) {
                $this->context->smarty->assign('hotelLocationArray', json_encode($hotelLocationArray));
            }
        }
        //End
        $this->setTemplate(_PS_THEME_DIR_.'contact-form.tpl');
    }

    /**
    * Assign template vars related to order list and product list ordered by the customer
    */
    protected function assignOrderList()
    {
        if ($this->context->customer->isLogged()) {
            $this->context->smarty->assign('isLogged', 1);

            $products = array();
            $result = Db::getInstance()->executeS('
			SELECT id_order
			FROM '._DB_PREFIX_.'orders
			WHERE id_customer = '.(int)$this->context->customer->id.Shop::addSqlRestriction(Shop::SHARE_ORDER).' ORDER BY date_add');

            $orders = array();

            foreach ($result as $row) {
                $order = new Order($row['id_order']);
                $date = explode(' ', $order->date_add);
                $tmp = $order->getProducts();
                foreach ($tmp as $key => $val) {
                    $products[$row['id_order']][$val['product_id']] = array('value' => $val['product_id'], 'label' => $val['product_name']);
                }

                $orders[] = array('value' => $order->id, 'label' => $order->getUniqReference().' - '.Tools::displayDate($date[0], null) , 'selected' => (int)$this->getOrder() == $order->id);
            }

            $this->context->smarty->assign('orderList', $orders);
            $this->context->smarty->assign('orderedProductList', $products);
        }
    }

    protected function getOrder()
    {
        $id_order = false;
        if (!is_numeric($reference = Tools::getValue('id_order'))) {
            $reference = ltrim($reference, '#');
            $orders = Order::getByReference($reference);
            if ($orders) {
                foreach ($orders as $order) {
                    $id_order = (int)$order->id;
                    break;
                }
            }
        } elseif (Order::getCartIdStatic((int)Tools::getValue('id_order'))) {
            $id_order = (int)Tools::getValue('id_order');
        }
        return (int)$id_order;
    }
}
