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

class OrderHistoryCore extends ObjectModel
{
    /** @var int Order id */
    public $id_order;

    /** @var int Order status id */
    public $id_order_state;

    /** @var int Employee id for this history entry */
    public $id_employee;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'order_history',
        'primary' => 'id_order_history',
        'fields' => array(
            'id_order' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order_state' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_employee' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_add' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /**
     * @see  ObjectModel::$webserviceParameters
     */
    protected $webserviceParameters = array(
        'objectsNodeName' => 'order_histories',
        'fields' => array(
            'id_employee' => array('xlink_resource'=> 'employees'),
            'id_order_state' => array('required' => true, 'xlink_resource'=> 'order_states'),
            'id_order' => array('xlink_resource' => 'orders'),
        ),
        'objectMethods' => array(
            'add' => 'addWs',
        ),
    );

    /**
     * Sets the new state of the given order
     *
     * @param int $new_order_state
     * @param int/object $id_order
     * @param bool $use_existing_payment
     */
    public function changeIdOrderState($new_order_state, $id_order, $use_existing_payment = false)
    {
        if (!$new_order_state || !$id_order) {
            return;
        }

        if (!is_object($id_order) && is_numeric($id_order)) {
            $order = new Order((int)$id_order);
        } elseif (is_object($id_order)) {
            $order = $id_order;
        } else {
            return;
        }

        ShopUrl::cacheMainDomainForShop($order->id_shop);

        $new_os = new OrderState((int)$new_order_state, $order->id_lang);
        $old_os = $order->getCurrentOrderState();

        // executes hook
        if (in_array($new_os->id, array(Configuration::get('PS_OS_PAYMENT_ACCEPTED'), Configuration::get('PS_OS_WS_PAYMENT')))) {
            Hook::exec('actionPaymentConfirmation', array('id_order' => (int)$order->id), null, false, true, false, $order->id_shop);
        }

        // executes hook
        Hook::exec('actionOrderStatusUpdate', array('newOrderStatus' => $new_os, 'id_order' => (int)$order->id), null, false, true, false, $order->id_shop);

        if (Validate::isLoadedObject($order) && ($new_os instanceof OrderState)) {
            $context = Context::getContext();

            // An email is sent the first time a virtual item is validated
            $virtual_products = $order->getVirtualProducts();
            if ($virtual_products && (!$old_os || !$old_os->logable) && $new_os && $new_os->logable) {
                $assign = array();
                foreach ($virtual_products as $key => $virtual_product) {
                    $id_product_download = ProductDownload::getIdFromIdProduct($virtual_product['product_id']);
                    $product_download = new ProductDownload($id_product_download);
                    // If this virtual item has an associated file, we'll provide the link to download the file in the email
                    if ($product_download->display_filename != '') {
                        $assign[$key]['name'] = $product_download->display_filename;
                        $dl_link = $product_download->getTextLink(false, $virtual_product['download_hash'])
                            .'&id_order='.(int)$order->id
                            .'&secure_key='.$order->secure_key;
                        $assign[$key]['link'] = $dl_link;
                        if (isset($virtual_product['download_deadline']) && $virtual_product['download_deadline'] != '0000-00-00 00:00:00') {
                            $assign[$key]['deadline'] = Tools::displayDate($virtual_product['download_deadline']);
                        }
                        if ($product_download->nb_downloadable != 0) {
                            $assign[$key]['downloadable'] = (int)$product_download->nb_downloadable;
                        }
                    }
                }

                $customer = new Customer((int)$order->id_customer);

                $links = '<ul>';
                foreach ($assign as $product) {
                    $links .= '<li>';
                    $links .= '<a href="'.$product['link'].'">'.Tools::htmlentitiesUTF8($product['name']).'</a>';
                    if (isset($product['deadline'])) {
                        $links .= '&nbsp;'.Tools::htmlentitiesUTF8(Tools::displayError('expires on', false)).'&nbsp;'.$product['deadline'];
                    }
                    if (isset($product['downloadable'])) {
                        $links .= '&nbsp;'.Tools::htmlentitiesUTF8(sprintf(Tools::displayError('downloadable %d time(s)', false), (int)$product['downloadable']));
                    }
                    $links .= '</li>';
                }
                $links .= '</ul>';
                $data = array(
                    '{lastname}' => $customer->lastname,
                    '{firstname}' => $customer->firstname,
                    '{id_order}' => (int)$order->id,
                    '{order_name}' => $order->getUniqReference(),
                    '{nbProducts}' => count($virtual_products),
                    '{virtualProducts}' => $links
                );
                // If there is at least one downloadable file
                if (!empty($assign)) {
                    Mail::Send((int)$order->id_lang, 'download_product', Mail::l('The virtual product that you bought is available for download', $order->id_lang), $data, $customer->email, $customer->firstname.' '.$customer->lastname,
                        null, null, null, null, _PS_MAIL_DIR_, false, (int)$order->id_shop);
                }
            }

            // @since 1.5.0 : gets the stock manager
            $manager = null;
            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                $manager = StockManagerFactory::getManager();
            }

            $error_or_canceled_statuses = array(Configuration::get('PS_OS_ERROR'), Configuration::get('PS_OS_CANCELED'));

            $employee = null;
            if (!(int)$this->id_employee || !Validate::isLoadedObject(($employee = new Employee((int)$this->id_employee)))) {
                if (!Validate::isLoadedObject($old_os) && $context != null) {
                    // First OrderHistory, there is no $old_os, so $employee is null before here
                    $employee = $context->employee; // filled if from BO and order created (because no old_os)
                    if ($employee) {
                        $this->id_employee = $employee->id;
                    }
                } else {
                    $employee = null;
                }
            }


            // foreach products of the order
            foreach ($order->getProductsDetail() as $product) {
                if (Validate::isLoadedObject($old_os)) {
                    // if becoming logable => adds sale
                    if ($new_os->logable && !$old_os->logable) {
                        ProductSale::addProductSale($product['product_id'], $product['product_quantity']);
                        // @since 1.5.0 - Stock Management
                        if (!Pack::isPack($product['product_id']) &&
                            in_array($old_os->id, $error_or_canceled_statuses) &&
                            !StockAvailable::dependsOnStock($product['id_product'], (int)$order->id_shop)) {
                            StockAvailable::updateQuantity($product['product_id'], $product['product_attribute_id'], -(int)$product['product_quantity'], $order->id_shop);
                        }
                    }
                    // if becoming unlogable => removes sale
                    elseif (!$new_os->logable && $old_os->logable) {
                        ProductSale::removeProductSale($product['product_id'], $product['product_quantity']);

                        // @since 1.5.0 - Stock Management
                        if (!Pack::isPack($product['product_id']) &&
                            in_array($new_os->id, $error_or_canceled_statuses) &&
                            !StockAvailable::dependsOnStock($product['id_product'])) {
                            StockAvailable::updateQuantity($product['product_id'], $product['product_attribute_id'], (int)$product['product_quantity'], $order->id_shop);
                        }
                    }
                    // if waiting for payment => payment error/canceled
                    elseif (!$new_os->logable && !$old_os->logable &&
                            in_array($new_os->id, $error_or_canceled_statuses) &&
                            !in_array($old_os->id, $error_or_canceled_statuses) &&
                            !StockAvailable::dependsOnStock($product['id_product'])) {
                        StockAvailable::updateQuantity($product['product_id'], $product['product_attribute_id'], (int)$product['product_quantity'], $order->id_shop);
                    }
                }
                // From here, there is 2 cases : $old_os exists, and we can test shipped state evolution,
                // Or old_os does not exists, and we should consider that initial shipped state is 0 (to allow decrease of stocks)

                // @since 1.5.0 : if the order is being shipped and this products uses the advanced stock management :
                // decrements the physical stock using $id_warehouse
                if ($new_os->shipped == 1 && (!Validate::isLoadedObject($old_os) || $old_os->shipped == 0) &&
                    Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') &&
                    Warehouse::exists($product['id_warehouse']) &&
                    $manager != null &&
                    (int)$product['advanced_stock_management'] == 1) {
                    // gets the warehouse
                    $warehouse = new Warehouse($product['id_warehouse']);

                    // decrements the stock (if it's a pack, the StockManager does what is needed)
                    $manager->removeProduct(
                        $product['product_id'],
                        $product['product_attribute_id'],
                        $warehouse,
                        ($product['product_quantity'] - $product['product_quantity_refunded'] - $product['product_quantity_return']),
                        Configuration::get('PS_STOCK_CUSTOMER_ORDER_REASON'),
                        true,
                        (int)$order->id,
                        0,
                        $employee
                    );
                }
                // @since.1.5.0 : if the order was shipped, and is not anymore, we need to restock products
                elseif ($new_os->shipped == 0 && Validate::isLoadedObject($old_os) && $old_os->shipped == 1 &&
                        Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') &&
                        Warehouse::exists($product['id_warehouse']) &&
                        $manager != null &&
                        (int)$product['advanced_stock_management'] == 1) {
                    // if the product is a pack, we restock every products in the pack using the last negative stock mvts
                    if (Pack::isPack($product['product_id'])) {
                        $pack_products = Pack::getItems($product['product_id'], Configuration::get('PS_LANG_DEFAULT', null, null, $order->id_shop));
                        foreach ($pack_products as $pack_product) {
                            if ($pack_product->advanced_stock_management == 1) {
                                $mvts = StockMvt::getNegativeStockMvts($order->id, $pack_product->id, 0, $pack_product->pack_quantity * $product['product_quantity']);
                                foreach ($mvts as $mvt) {
                                    $manager->addProduct(
                                        $pack_product->id,
                                        0,
                                        new Warehouse($mvt['id_warehouse']),
                                        $mvt['physical_quantity'],
                                        null,
                                        $mvt['price_te'],
                                        true,
                                        null,
                                        $employee
                                    );
                                }
                                if (!StockAvailable::dependsOnStock($product['id_product'])) {
                                    StockAvailable::updateQuantity($pack_product->id, 0, (int)$pack_product->pack_quantity * $product['product_quantity'], $order->id_shop);
                                }
                            }
                        }
                    }
                    // else, it's not a pack, re-stock using the last negative stock mvts
                    else {
                        $mvts = StockMvt::getNegativeStockMvts($order->id, $product['product_id'],
                            $product['product_attribute_id'],
                            ($product['product_quantity'] - $product['product_quantity_refunded'] - $product['product_quantity_return']));

                        foreach ($mvts as $mvt) {
                            $manager->addProduct(
                                $product['product_id'],
                                $product['product_attribute_id'],
                                new Warehouse($mvt['id_warehouse']),
                                $mvt['physical_quantity'],
                                null,
                                $mvt['price_te'],
                                true
                            );
                        }
                    }
                }
            }
        }

        $this->id_order_state = (int)$new_order_state;

        // changes invoice number of order ?
        if (!Validate::isLoadedObject($new_os) || !Validate::isLoadedObject($order)) {
            die(Tools::displayError('Invalid new order status'));
        }

        // the order is valid if and only if the invoice is available and the order is not cancelled
        $order->current_state = $this->id_order_state;
        $order->valid = $new_os->logable;
        $order->update();

        if ($new_os->invoice && !$order->invoice_number) {
            $order->setInvoice($use_existing_payment);
        } elseif ($new_os->delivery && !$order->delivery_number) {
            $order->setDeliverySlip();
        }

        // set orders as paid
        if ($new_os->paid == 1) {
            $invoices = $order->getInvoicesCollection();
            if ($order->total_paid != 0) {
                // if order is created by API then create a direct object instead of creating an object from module
                if ($order->module == 'wsorder') {
                    $payment_method = new WebserviceOrder();
                } else {
                    $payment_method = Module::getInstanceByName($order->module);
                }
            }

            foreach ($invoices as $invoice) {
                /** @var OrderInvoice $invoice */
                $rest_paid = $invoice->getRestPaid();
                if ($rest_paid > 0) {
                    if ($order->total_paid != 0) {
                        $payment_method = $payment_method->displayName;
                    } else {
                        $payment_method = null;
                    }
                    $order->addOrderPayment(
                        $rest_paid,
                        $payment_method,
                        null,
                        null,
                        null,
                        $invoice
                    );
                }
            }
        }

        // updates delivery date even if it was already set by another state change
        if ($new_os->delivery) {
            $order->setDelivery();
        }

        // executes hook
        Hook::exec('actionOrderStatusPostUpdate', array('newOrderStatus' => $new_os, 'id_order' => (int)$order->id, ), null, false, true, false, $order->id_shop);

        ShopUrl::resetMainDomainCache();
    }

    /**
     * Returns the last order status
     * @param int $id_order
     * @return OrderState|bool
     * @deprecated 1.5.0.4
     * @see Order->current_state
     */
    public static function getLastOrderState($id_order)
    {
        Tools::displayAsDeprecated();
        $id_order_state = Db::getInstance()->getValue('
		SELECT `id_order_state`
		FROM `'._DB_PREFIX_.'order_history`
		WHERE `id_order` = '.(int)$id_order.'
		ORDER BY `date_add` DESC, `id_order_history` DESC');

        // returns false if there is no state
        if (!$id_order_state) {
            return false;
        }

        // else, returns an OrderState object
        return new OrderState($id_order_state, Configuration::get('PS_LANG_DEFAULT'));
    }

    /**
     * @param bool $autodate Optional
     * @param array $template_vars Optional
     * @param Context $context Deprecated
     * @return bool
     */
    public function addWithemail($autodate = true, $template_vars = false, Context $context = null)
    {
        $order = new Order($this->id_order);

        if (!$this->add($autodate)) {
            return false;
        }

        if (!$this->sendEmail($order, $template_vars)) {
            return false;
        }

        return true;
    }

    public function sendEmail($order, $template_vars = false)
    {
        $result = Db::getInstance()->getRow('
			SELECT osl.`template`, c.`lastname`, c.`firstname`, osl.`name` AS osname, c.`email`, o.`module` as `module_name`, os.`id_order_state`, os.`pdf_invoice`, os.`pdf_delivery`
			FROM `'._DB_PREFIX_.'order_history` oh
				LEFT JOIN `'._DB_PREFIX_.'orders` o ON oh.`id_order` = o.`id_order`
				LEFT JOIN `'._DB_PREFIX_.'customer` c ON o.`id_customer` = c.`id_customer`
				LEFT JOIN `'._DB_PREFIX_.'order_state` os ON oh.`id_order_state` = os.`id_order_state`
				LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = o.`id_lang`)
			WHERE oh.`id_order_history` = '.(int)$this->id.' AND os.`send_email` = 1');
        if (isset($result['template']) && Validate::isEmail($result['email'])) {
            ShopUrl::cacheMainDomainForShop($order->id_shop);

            $topic = $result['osname'];
            $data = array(
                '{lastname}' => $result['lastname'],
                '{firstname}' => $result['firstname'],
                '{id_order}' => (int)$this->id_order,
                '{order_name}' => $order->getUniqReference(),
                '{extra_mail_content_html}' => '',
                '{extra_mail_content_txt}' => '',
                '{payment_method}' => '',
            );

            if ($result['module_name']) {
                if (Validate::isLoadedObject($module = Module::getInstanceByName($result['module_name']))) {
                    $data['{payment_method}'] = $module->displayName;
                    // if any modle need to send extra content in mail, that module need to implement this function
                    // this function should return an array with content for both html and txt mail template
                    // return  array('{extra_mail_content_html}' => '', '{extra_mail_content_txt}' => '')
                    if (method_exists($module, 'getExtraMailContent')) {
                        if (is_array($extra_mail_content = $module->getExtraMailContent(
                            $result['id_order_state'],
                            $order
                        ))) {
                            $data = array_merge($data, $extra_mail_content);
                        }
                    }
                }
            }

            if ($template_vars) {
                $data = array_merge($data, $template_vars);
            }

            $data['{total_paid}'] = Tools::displayPrice((float)$order->total_paid, new Currency((int)$order->id_currency), false);

            if (Validate::isLoadedObject($order)) {
                // Attach invoice and / or delivery-slip if they exists and status is set to attach them
                if (($result['pdf_invoice'] || $result['pdf_delivery'])) {
                    $context = Context::getContext();
                    $invoice = $order->getInvoicesCollection();
                    $file_attachement = array();

                    if ($result['pdf_invoice'] && (int)Configuration::get('PS_INVOICE') && $order->invoice_number) {
                        Hook::exec('actionPDFInvoiceRender', array('order_invoice_list' => $invoice));
                        $pdf = new PDF($invoice, PDF::TEMPLATE_INVOICE, $context->smarty);
                        $file_attachement['invoice']['content'] = $pdf->render(false);
                        $file_attachement['invoice']['name'] = Configuration::get('PS_INVOICE_PREFIX', (int)$order->id_lang, null, $order->id_shop).sprintf('%06d', $order->invoice_number).'.pdf';
                        $file_attachement['invoice']['mime'] = 'application/pdf';
                    }
                    if ($result['pdf_delivery'] && $order->delivery_number) {
                        $pdf = new PDF($invoice, PDF::TEMPLATE_DELIVERY_SLIP, $context->smarty);
                        $file_attachement['delivery']['content'] = $pdf->render(false);
                        $file_attachement['delivery']['name'] = Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id, null, $order->id_shop).sprintf('%06d', $order->delivery_number).'.pdf';
                        $file_attachement['delivery']['mime'] = 'application/pdf';
                    }
                } else {
                    $file_attachement = null;
                }

                if (!Mail::Send((int)$order->id_lang, $result['template'], $topic, $data, $result['email'], $result['firstname'].' '.$result['lastname'],
                    null, null, $file_attachement, null, _PS_MAIL_DIR_, false, (int)$order->id_shop)) {
                    return false;
                }
            }

            ShopUrl::resetMainDomainCache();
        }

        return true;
    }

    public function add($autodate = true, $null_values = false)
    {
        if (!parent::add($autodate)) {
            return false;
        }

        $order = new Order((int)$this->id_order);
        // Update id_order_state attribute in Order
        $order->current_state = $this->id_order_state;
        $order->update();

        Hook::exec('actionOrderHistoryAddAfter', array('order_history' => $this), null, false, true, false, $order->id_shop);

        return true;
    }

    /**
     * @return int
     */
    public function isValidated()
    {
        return Db::getInstance()->getValue('
		SELECT COUNT(oh.`id_order_history`) AS nb
		FROM `'._DB_PREFIX_.'order_state` os
		LEFT JOIN `'._DB_PREFIX_.'order_history` oh ON (os.`id_order_state` = oh.`id_order_state`)
		WHERE oh.`id_order` = '.(int)$this->id_order.'
		AND os.`logable` = 1');
    }

    /**
     * Add method for webservice create resource Order History
     * If sendemail=1 GET parameter is present sends email to customer otherwise does not
     * @return bool
     */
    public function addWs()
    {
        $sendemail = (bool)Tools::getValue('sendemail', false);
        $this->changeIdOrderState($this->id_order_state, $this->id_order);

        if ($sendemail) {
            //Mail::Send requires link object on context and is not set when getting here
            $context = Context::getContext();
            if ($context->link == null) {
                $protocol_link = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
                $protocol_content = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
                $context->link = new Link($protocol_link, $protocol_content);
            }
            return $this->addWithemail();
        } else {
            return $this->add();
        }
    }
}
