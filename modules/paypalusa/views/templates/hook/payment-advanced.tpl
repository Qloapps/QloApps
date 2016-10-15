{*
** @author PrestaShop SA <contact@prestashop.com>
** @copyright  2007-2014 PrestaShop SA
** @version  Release: $Revision: 1.2.0 $
**
** International Registered Trademark & Property of PrestaShop SA
**
** Description: "PayPal Payments Advanced" payment template
**
** This template is displayed on the payment page and called by the Payment hook
**
** Step 1: You have to create a PayPal Manager account at https://manager.paypal.com/
** Step 2: You have to sign up for PayPal Payments Advanced and a gateway (either PayFlow Link or PayFlow Pro)
** Step 3: In the PayPal Manager, enable the Hosted Page Checkout and the "Secure token" option
** Step 4: Configure this Addon in the Back-office with your credentials for the PayPal Manager
** Step 5: On the payment page, we will display an <iframe> loading your hosted checkout page
** Step 6: The customer will proceed to payment inside this <iframe>
** Step 7: The transaction success or failure is sent to you by PayPal at the following URL: http://www.mystore.com/modules/paypalusa/controllers/front/validation.php
** Step 8: Customer is redirected to the Order confirmation page
*}

{* Negative margin is used to compensate the 15px margin added by PayPal inside the <iframe>, you can alter the CSS styles below if needed *}
<iframe src="{$paypal_usa_advanced_iframe_url|escape:'htmlall':'UTF-8'}" name="paypal_advanced" scrolling="no" style="width: 570px; height: 540px; margin-left: -15px; border: none;" border="0"></iframe>