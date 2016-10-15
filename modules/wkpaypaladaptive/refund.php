<?php
include dirname(__FILE__).'/../../config/config.inc.php';

include dirname(__FILE__).'/wkpaypaladaptive.php';
define('DEBUG', 1);
define('LOG_FILE', './ipn.log');

$result = array();
$result['status'] = 'fail';
$result['msg'] = 'The Pay Key can no longer be used.'; // from paypal document
$payKey = Tools::getValue('wkPaypalTransactionPayKey');
if ($payKey) {
	//create request and add headers
	$headers = array(
	    'X-PAYPAL-SECURITY-USERID: '.Configuration::get('APP_USERNAME'),
	    'X-PAYPAL-SECURITY-PASSWORD: '.Configuration::get('APP_PASSWORD'),
	    'X-PAYPAL-SECURITY-SIGNATURE: '.Configuration::get('APP_SIGNATURE'),
	    'X-PAYPAL-REQUEST-DATA-FORMAT: JSON',
	    'X-PAYPAL-RESPONSE-DATA-FORMAT: JSON',
	    'X-PAYPAL-APPLICATION-ID: '.Configuration::get('APP_ID'),
	);
	$bodyparams = array(
		'currencyCode' => 'USD',
	    'payKey' => $payKey,
	    // 'transactionId' => '6JJ82360FE138411B',
		'requestEnvelope' => array(
            'errorLanguage' => 'en_US',
            'detailLevel' => 'ReturnAll'
        )
	);

	$url = trim("https://svcs.paypal.com/AdaptivePayments/Refund");
    if (Configuration::get('WK_PAYPAL_SANDBOX') == 1) {
    	$url = trim("https://svcs.sandbox.paypal.com/AdaptivePayments/Refund");
    }

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($bodyparams));
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$response = json_decode(curl_exec($ch), true);

	if ($response['responseEnvelope']['ack'] == 'Success') {
		$result['status'] = 'success';
		$result['msg'] = 'Payment refund request submited successfully. Please wait while we load refund information.'; // from paypal document
   	} elseif ($response['responseEnvelope']['ack'] == 'Failure') {
   		$result['status'] = 'fail';
		$result['msg'] = 'Payment refund failed.'; // from paypal document
   	} elseif ($response['responseEnvelope']['ack'] == 'SuccessWithWarning') {
   		$result['status'] = 'success';
		$result['msg'] = 'Payment refunded successfully. However, there is a warning in refund.'; // from paypal document
   	} elseif ($response['responseEnvelope']['ack'] == 'FailureWithWarning') {
   		$result['status'] = 'fail';
		$result['msg'] = 'Payment refund failed with a warning message.'; // from paypal document
   	}

   	$transactionId = Tools::getValue('wkPaypalTransactionId');
   	if ($transactionId) {
	   	$obj_txn = new WkPaypalTransaction($transactionId);
	   	$obj_txn->is_refunded = 1;
	   	$obj_txn->save();

	   	$obj_refund = new WkPaypalRefund();
	    $obj_refund->transaction_id = $transactionId;
	    $obj_refund->refund_details = json_encode($response['refundInfoList']);
	    $obj_refund->save();
	}
	WkPaypalTransaction::updateDelayedPaid($payKey);
}
die(json_encode($result));
