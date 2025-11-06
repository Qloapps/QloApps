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
            $message = trim(Tools::getValue('message')); // Html entities is not usefull, iscleanHtml check there is no bad html tags.
            $url = Tools::getValue('url');
            $phone = Tools::getValue('phone');
            $subject = trim(Tools::getValue('subject'));
            $userName = Tools::getValue('user_name');
            $id_contact = (int)Tools::getValue('id_contact');
            $objCustomerThread = new CustomerThread();
            $id_customer_thread = (int)$objCustomerThread->getIdCustomerThreadByToken(Tools::getValue('token'));
            $nameRequired = Configuration::get('PS_CUSTOMER_SERVICE_REQUIRED_NAME');
            $phoneRequired = Configuration::get('PS_CUSTOMER_SERVICE_REQUIRED_PHONE');
            if (!Configuration::get('PS_CUSTOMER_SERVICE_DISPLAY_CONTACT')) {
                $id_contact = (int) Configuration::get('PS_CUSTOMER_SERVICE_CONTACT');
            }

            if (!($from = trim(Tools::getValue('from'))) || !Validate::isEmail($from)) {
                $this->errors[] = Tools::displayError('Invalid email address.');
            } elseif (!$message) {
                $this->errors[] = Tools::displayError('The message cannot be blank.');
            } elseif (!Validate::isCleanHtml($message)) {
                $this->errors[] = Tools::displayError('Invalid message');
            } elseif (!$id_customer_thread && !$subject) {
                $this->errors[] = Tools::displayError('The title cannot be blank.');
            } elseif (!$id_customer_thread && !Validate::isCleanHtml($subject)) {
                $this->errors[] = Tools::displayError('Invalid subject$subject');
            } else if (!$id_customer_thread && $nameRequired && !trim($userName)) {
                $this->errors[] = Tools::displayError('Name is required.');
            } else if (!$id_customer_thread && trim($nameRequired) && !Validate::isGenericName($userName)) {
                $this->errors[] = Tools::displayError('Invalid name.');
            } else if (!$id_customer_thread && $phoneRequired && !trim($phone)) {
                $this->errors[] = Tools::displayError('Phone is required.');
            } else if (!$id_customer_thread && trim($phone) && !Validate::isPhoneNumber($phone)) {
                $this->errors[] = Tools::displayError('Invalid Phone number.');
            } elseif (!Validate::isLoadedObject($contact = new Contact($id_contact, $this->context->language->id))) {
                $this->errors[] = Tools::displayError('Please choose who to send the message to.');
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

                if (!$id_customer_thread) {
                    $id_customer_thread = CustomerThread::getIdCustomerThreadByEmailAndIdOrder(
                        $from,
                        $id_order,
                        $id_contact,
                        ' AND a.`status` !='. CustomerThread::QLO_CUSTOMER_THREAD_STATUS_CLOSED
                    );
                }

                $old_message = Db::getInstance()->getValue('
					SELECT cm.message FROM '._DB_PREFIX_.'customer_message cm
					LEFT JOIN '._DB_PREFIX_.'customer_thread cc on (cm.id_customer_thread = cc.id_customer_thread)
					WHERE cc.id_customer_thread = '.(int)$id_customer_thread.' AND cc.id_shop = '.(int)$this->context->shop->id.'
					ORDER BY cm.date_add DESC');
                if ($old_message == $message) {
                    $this->errors[] = Tools::displayError('Your message has already been sent.');
                    $_POST = array();
                    $contact->email = '';
                    $contact->customer_service = 0;
                } else {
                    if ($contact->customer_service) {
                        if ((int)$id_customer_thread) {
                            $ct = new CustomerThread($id_customer_thread);
                            $ct->id_order = (int)$id_order;
                        } else {
                            $ct = new CustomerThread();
                            if (isset($customer->id)) {
                                $ct->id_customer = (int)$customer->id;
                            }
                            $ct->id_shop = (int)$this->context->shop->id;
                            $ct->phone = $phone;
                            $ct->user_name = $userName;
                            $ct->subject = $subject;
                            $ct->email = $from;
                            $ct->token = Tools::passwdGen(12);
                        }

                        $ct->status = CustomerThread::QLO_CUSTOMER_THREAD_STATUS_OPEN;
                        $ct->id_lang = (int)$this->context->language->id;
                        $ct->id_contact = (int)$id_contact;

                        if ($ct->save()) {
                            $cm = new CustomerMessage();
                            $cm->id_customer_thread = $ct->id;
                            $cm->message = $message;
                            if ($id_product = (int)Tools::getValue('id_product')) {
                                $cm->id_product = $id_product;
                            }
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
                        $smartyMailVars['message'] = Tools::nl2br(stripslashes($message));
                        $smartyMailVars['email'] = $ct->email;
                        $smartyMailVars['subject'] = $ct->subject;
                        $smartyMailVars['phone'] = $ct->phone;
                        $smartyMailVars['user_name'] = $ct->user_name;

                        if (isset($file_attachment['name'])) {
                            $smartyMailVars['attached_file'] = $file_attachment['name'];
                        }

                        $contact_content_txt = $this->getEmailTemplateContent('contact_content_txt.tpl', Mail::TYPE_TEXT, $smartyMailVars);
                        $contact_content_html = $this->getEmailTemplateContent('contact_content_html.tpl', Mail::TYPE_TEXT, $smartyMailVars);
                        $var_list = array(
                            '{contact_content_txt}' => $contact_content_txt,
                            '{contact_content_html}' => $contact_content_html,
                        );
                        if (!empty($contact->email)) {
                            Mail::Send(
                                $this->context->language->id,
                                'contact',
                                Mail::l('Message from contact form'),
                                $var_list,
                                $contact->email,
                                $contact->name,
                                null,
                                null,
                                $file_attachment,
                                null,
                                _PS_MAIL_DIR_,
                                false,
                                null,
                                null,
                                $from
                            );
                        }
                    }

                    if (count($this->errors) > 1) {
                        array_unique($this->errors);
                    } elseif (!count($this->errors)) {
                        Tools::redirect($this->context->link->getPageLink('contact', null, null, array('confirm' => 1)));
                    }
                }
            }
        }
    }

    protected function getEmailTemplateContent($template_name, $mail_type, $var)
    {
        $email_configuration = Configuration::get('PS_MAIL_TYPE');
        if ($email_configuration != $mail_type && $email_configuration != Mail::TYPE_BOTH) {
            return '';
        }

        $pathToFindEmail = array(
            _PS_THEME_DIR_.'mails'.DIRECTORY_SEPARATOR.$this->context->language->iso_code.DIRECTORY_SEPARATOR.$template_name,
            _PS_THEME_DIR_.'mails'.DIRECTORY_SEPARATOR.'en'.DIRECTORY_SEPARATOR.$template_name,
            _PS_MAIL_DIR_.$this->context->language->iso_code.DIRECTORY_SEPARATOR.$template_name,
            _PS_MAIL_DIR_.'en'.DIRECTORY_SEPARATOR.$template_name,
        );

        foreach ($pathToFindEmail as $path) {
            if (Tools::file_exists_cache($path)) {
                $this->context->smarty->assign($var);
                return $this->context->smarty->fetch($path);
            }
        }

        return '';
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'contact-form.css');
        $this->addJS(_THEME_JS_DIR_.'contact-form.js');
        $this->addJS(_PS_JS_DIR_.'validate.js');

        // GOOGLE MAP
        if (($PS_API_KEY = Configuration::get('PS_API_KEY')) && ($PS_MAP_ID = Configuration::get('PS_MAP_ID')) && Configuration::get('WK_GOOGLE_ACTIVE_MAP')) {
            Media::addJsDef(
                array(
                    'PS_STORES_ICON' => $this->context->link->getMediaLink(_PS_IMG_.Configuration::get('PS_STORES_ICON')),
                    'PS_MAP_ID' => $PS_MAP_ID,
                )
            );
            $this->addJS(
                'https://maps.googleapis.com/maps/api/js?key='.$PS_API_KEY.
                '&libraries=places,marker&loading=async&callback=initMap&language='.$this->context->language->iso_code.'&region='.$this->context->country->iso_code
            );
        }
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
        $customerName = isset($this->context->customer) ? $this->context->customer->firstname.' '.$this->context->customer->lastname : '';
        $customerPhone = isset($this->context->customer) ? $this->context->customer->phone: '';
        $this->context->smarty->assign(array(
            'errors' => $this->errors,
            'email' => $email,
            'customerName' => $customerName,
            'customerPhone' => $customerPhone,
            'fileupload' => Configuration::get('PS_CUSTOMER_SERVICE_FILE_UPLOAD'),
            'max_upload_size' => (int)Tools::getMaxUploadSize()
        ));

        if ($token = Tools::getValue('token')) {
            if ($customer_thread = Db::getInstance()->getRow('
				SELECT cm.*
				FROM '._DB_PREFIX_.'customer_thread cm
				WHERE cm.id_shop = '.(int)$this->context->shop->id.'
				AND token = \''.pSQL($token).'\'
			')) {
                $order = new Order((int)$customer_thread['id_order']);
                if (Validate::isLoadedObject($order)) {
                    $customer_thread['reference'] = $order->getUniqReference();
                }
                $this->context->smarty->assign('customerThread', $customer_thread);
            }
        }

        $objShop = new Shop();
        $shopAddress = '';
        $shopAddress_obj = $objShop->getAddress();
        if (isset($shopAddress_obj) && $shopAddress_obj instanceof Address) {
            $shopAddress = AddressFormat::generateAddress($shopAddress_obj, array(), ', ', ' ');
        }

        $gblHtlAddress = $shopAddress;
        $gblHtlPhone = Configuration::get('PS_SHOP_PHONE');
        $gblHtlEmail = Configuration::get('PS_SHOP_EMAIL');
        $gblHtlRegistrationNumber = Configuration::get('PS_SHOP_DETAILS');
        $gblHtlFax = Configuration::get('PS_SHOP_FAX');
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
        $displayHotelMap = Configuration::get('WK_DISPLAY_CONTACT_PAGE_GOOLGE_MAP');
        $this->context->smarty->assign(
            array(
                'hotelsInfo' => $hotelsInfo,
                'viewOnMap' => Configuration::get('WK_GOOGLE_ACTIVE_MAP'),
                'displayHotels' => Configuration::get('WK_DISPLAY_CONTACT_PAGE_HOTEL_LIST'),
                'gblHtlPhone' => $gblHtlPhone,
                'gblHtlEmail' => $gblHtlEmail,
                'displayHotelMap' => $displayHotelMap,
                'gblHtlAddress' => $gblHtlAddress,
                'gblHtlRegistrationNumber' => $gblHtlRegistrationNumber,
                'gblHtlFax' => $gblHtlFax,
                'contacts' => Contact::getContacts($this->context->language->id),
                'message' => html_entity_decode(Tools::getValue('message')),
	            'contactKey' => $contactKey,
                'contactNameRequired' => Configuration::get('PS_CUSTOMER_SERVICE_REQUIRED_NAME'),
                'displayContactName' => Configuration::get('PS_CUSTOMER_SERVICE_DISPLAY_NAME'),
                'contactPhoneRequired' => Configuration::get('PS_CUSTOMER_SERVICE_REQUIRED_PHONE'),
                'displayContactPhone' => Configuration::get('PS_CUSTOMER_SERVICE_DISPLAY_PHONE'),
                'allowContactSelection' => Configuration::get('PS_CUSTOMER_SERVICE_DISPLAY_CONTACT')
            )
        );

        //By webkul to send hotels Map Informations for google Map.
        if ($displayHotelMap && Configuration::get('PS_API_KEY') && Configuration::get('WK_GOOGLE_ACTIVE_MAP')) {
            if ($hotelLocationArray = $objHotelInfo->getMapFormatHotelsInfo(Configuration::get('WK_MAP_HOTEL_ACTIVE_ONLY'))) {
                $this->context->smarty->assign('hotelLocationArray', str_replace(array('\n', '\r'), '', json_encode($hotelLocationArray)));
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
