<?php
namespace Braintree;

use InvalidArgumentException;

/**
 * Braintree TransactionGateway processor
 * Creates and manages transactions
 *
 *
 * <b>== More information ==</b>
 *
 * For more detailed information on Transactions, see {@link http://www.braintreepayments.com/gateway/transaction-api http://www.braintreepaymentsolutions.com/gateway/transaction-api}
 *
 * @package    Braintree
 * @category   Resources
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */

final class TransactionGateway
{
    private $_gateway;
    private $_config;
    private $_http;

    public function __construct($gateway)
    {
        $this->_gateway = $gateway;
        $this->_config = $gateway->config;
        $this->_config->assertHasAccessTokenOrKeys();
        $this->_http = new Http($gateway->config);
    }

    public function cloneTransaction($transactionId, $attribs)
    {
        Util::verifyKeys(self::cloneSignature(), $attribs);
        return $this->_doCreate('/transactions/' . $transactionId . '/clone', ['transactionClone' => $attribs]);
    }

    /**
     * @ignore
     * @access private
     * @param array $attribs
     * @return object
     */
    private function create($attribs)
    {
        Util::verifyKeys(self::createSignature(), $attribs);
        return $this->_doCreate('/transactions', ['transaction' => $attribs]);
    }

    /**
     * @ignore
     * @access private
     * @param array $attribs
     * @return object
     * @throws Exception\ValidationError
     */
    private function createNoValidate($attribs)
    {
        $result = $this->create($attribs);
        return Util::returnObjectOrThrowException(__CLASS__, $result);
    }
    /**
     *
     * @deprecated since version 2.3.0
     * @access public
     * @param array $attribs
     * @return object
     */
    public function createFromTransparentRedirect($queryString)
    {
        trigger_error("DEPRECATED: Please use TransparentRedirectRequest::confirm", E_USER_NOTICE);
        $params = TransparentRedirect::parseAndValidateQueryString(
                $queryString
        );
        return $this->_doCreate(
                '/transactions/all/confirm_transparent_redirect_request',
                ['id' => $params['id']]
        );
    }
    /**
     *
     * @deprecated since version 2.3.0
     * @access public
     * @param none
     * @return string
     */
    public function createTransactionUrl()
    {
        trigger_error("DEPRECATED: Please use TransparentRedirectRequest::url", E_USER_NOTICE);
        return $this->_config->baseUrl() . $this->_config->merchantPath() .
                '/transactions/all/create_via_transparent_redirect_request';
    }

    public static function cloneSignature()
    {
        return ['amount', 'channel', ['options' => ['submitForSettlement']]];
    }

    /**
     * creates a full array signature of a valid gateway request
     * @return array gateway request signature format
     */
    public static function createSignature()
    {
        return [
            'amount',
            'billingAddressId',
            'channel',
            'customerId',
            'deviceData',
            'deviceSessionId',
            'fraudMerchantId',
            'merchantAccountId',
            'orderId',
            'paymentMethodNonce',
            'paymentMethodToken',
            'purchaseOrderNumber',
            'recurring',
            'serviceFeeAmount',
            'sharedPaymentMethodToken',
            'sharedCustomerId',
            'sharedShippingAddressId',
            'sharedBillingAddressId',
            'shippingAddressId',
            'taxAmount',
            'taxExempt',
            'threeDSecureToken',
            'type',
            'venmoSdkPaymentMethodCode',
            ['creditCard' =>
                ['token', 'cardholderName', 'cvv', 'expirationDate', 'expirationMonth', 'expirationYear', 'number'],
            ],
            ['customer' =>
                [
                    'id', 'company', 'email', 'fax', 'firstName',
                    'lastName', 'phone', 'website'],
            ],
            ['billing' =>
                [
                    'firstName', 'lastName', 'company', 'countryName',
                    'countryCodeAlpha2', 'countryCodeAlpha3', 'countryCodeNumeric',
                    'extendedAddress', 'locality', 'postalCode', 'region',
                    'streetAddress'],
            ],
            ['shipping' =>
                [
                    'firstName', 'lastName', 'company', 'countryName',
                    'countryCodeAlpha2', 'countryCodeAlpha3', 'countryCodeNumeric',
                    'extendedAddress', 'locality', 'postalCode', 'region',
                    'streetAddress'],
            ],
            ['options' =>
                [
                    'holdInEscrow',
                    'storeInVault',
                    'storeInVaultOnSuccess',
                    'submitForSettlement',
                    'addBillingAddressToPaymentMethod',
                    'venmoSdkSession',
                    'storeShippingAddressInVault',
                    'payeeEmail',
                    ['three_d_secure' =>
                        ['required']
                    ],
                    ['paypal' =>
                        [
                            'payeeEmail',
                            'customField',
                            'description',
                            ['supplementaryData' => ['_anyKey_']],
                        ]
                    ],
                    ['amexRewards' =>
                        [
                            'requestId',
                            'points',
                            'currencyAmount',
                            'currencyIsoCode'
                        ]
                    ]
                ],
            ],
            ['customFields' => ['_anyKey_']],
            ['descriptor' => ['name', 'phone', 'url']],
            ['paypalAccount' => ['payeeEmail']],
            ['apple_pay_card' => ['number', 'cardholder_name', 'cryptogram', 'expiration_month', 'expiration_year']],
            ['industry' =>
                ['industryType',
                    ['data' =>
                        [
                            'folioNumber',
                            'checkInDate',
                            'checkOutDate',
                            'travelPackage',
                            'departureDate',
                            'lodgingCheckInDate',
                            'lodgingCheckOutDate',
                            'lodgingName',
                            'roomRate'
                        ]
                    ]
                ]
            ]
        ];
    }

    public static function submitForSettlementSignature()
    {
        return ['orderId', ['descriptor' => ['name', 'phone', 'url']]];
    }

    /**
     *
     * @access public
     * @param array $attribs
     * @return Result\Successful|Result\Error
     */
    public function credit($attribs)
    {
        return $this->create(array_merge($attribs, ['type' => Transaction::CREDIT]));
    }

    /**
     *
     * @access public
     * @param array $attribs
     * @return Result\Successful|Result\Error
     * @throws Exception\ValidationError
     */
    public function creditNoValidate($attribs)
    {
        $result = $this->credit($attribs);
        return Util::returnObjectOrThrowException(__CLASS__, $result);
    }

    /**
     * @access public
     * @param string id
     * @return Transaction
     */
    public function find($id)
    {
        $this->_validateId($id);
        try {
            $path = $this->_config->merchantPath() . '/transactions/' . $id;
            $response = $this->_http->get($path);
            return Transaction::factory($response['transaction']);
        } catch (Exception\NotFound $e) {
            throw new Exception\NotFound(
            'transaction with id ' . $id . ' not found'
            );
        }
    }
    /**
     * new sale
     * @param array $attribs
     * @return array
     */
    public function sale($attribs)
    {
        return $this->create(array_merge(['type' => Transaction::SALE], $attribs));
    }

    /**
     * roughly equivalent to the ruby bang method
     * @access public
     * @param array $attribs
     * @return array
     * @throws Exception\ValidationsFailed
     */
    public function saleNoValidate($attribs)
    {
        $result = $this->sale($attribs);
        return Util::returnObjectOrThrowException(__CLASS__, $result);
    }

    /**
     * Returns a ResourceCollection of transactions matching the search query.
     *
     * If <b>query</b> is a string, the search will be a basic search.
     * If <b>query</b> is a hash, the search will be an advanced search.
     * For more detailed information and examples, see {@link http://www.braintreepayments.com/gateway/transaction-api#searching http://www.braintreepaymentsolutions.com/gateway/transaction-api}
     *
     * @param mixed $query search query
     * @param array $options options such as page number
     * @return ResourceCollection
     * @throws InvalidArgumentException
     */
    public function search($query)
    {
        $criteria = [];
        foreach ($query as $term) {
            $criteria[$term->name] = $term->toparam();
        }

        $path = $this->_config->merchantPath() . '/transactions/advanced_search_ids';
        $response = $this->_http->post($path, ['search' => $criteria]);
        if (array_key_exists('searchResults', $response)) {
            $pager = [
                'object' => $this,
                'method' => 'fetch',
                'methodArgs' => [$query]
                ];

            return new ResourceCollection($response, $pager);
        } else {
            throw new Exception\DownForMaintenance();
        }
    }

    public function fetch($query, $ids)
    {
        $criteria = [];
        foreach ($query as $term) {
            $criteria[$term->name] = $term->toparam();
        }
        $criteria["ids"] = TransactionSearch::ids()->in($ids)->toparam();
        $path = $this->_config->merchantPath() . '/transactions/advanced_search';
        $response = $this->_http->post($path, ['search' => $criteria]);

        return Util::extractattributeasarray(
            $response['creditCardTransactions'],
            'transaction'
        );
    }

    /**
     * void a transaction by id
     *
     * @param string $id transaction id
     * @return Result\Successful|Result\Error
     */
    public function void($transactionId)
    {
        $this->_validateId($transactionId);

        $path = $this->_config->merchantPath() . '/transactions/'. $transactionId . '/void';
        $response = $this->_http->put($path);
        return $this->_verifyGatewayResponse($response);
    }
    /**
     *
     */
    public function voidNoValidate($transactionId)
    {
        $result = $this->void($transactionId);
        return Util::returnObjectOrThrowException(__CLASS__, $result);
    }

    public function submitForSettlement($transactionId, $amount = null, $attribs = [])
    {
        $this->_validateId($transactionId);
        Util::verifyKeys(self::submitForSettlementSignature(), $attribs);
        $attribs['amount'] = $amount;

        $path = $this->_config->merchantPath() . '/transactions/'. $transactionId . '/submit_for_settlement';
        $response = $this->_http->put($path, ['transaction' => $attribs]);
        return $this->_verifyGatewayResponse($response);
    }

    public function submitForSettlementNoValidate($transactionId, $amount = null, $attribs = [])
    {
        $result = $this->submitForSettlement($transactionId, $amount, $attribs);
        return Util::returnObjectOrThrowException(__CLASS__, $result);
    }

    public function submitForPartialSettlement($transactionId, $amount, $attribs = [])
    {
        $this->_validateId($transactionId);
        Util::verifyKeys(self::submitForSettlementSignature(), $attribs);
        $attribs['amount'] = $amount;

        $path = $this->_config->merchantPath() . '/transactions/'. $transactionId . '/submit_for_partial_settlement';
        $response = $this->_http->post($path, ['transaction' => $attribs]);
        return $this->_verifyGatewayResponse($response);
    }

    public function holdInEscrow($transactionId)
    {
        $this->_validateId($transactionId);

        $path = $this->_config->merchantPath() . '/transactions/' . $transactionId . '/hold_in_escrow';
        $response = $this->_http->put($path, []);
        return $this->_verifyGatewayResponse($response);
    }

    public function releaseFromEscrow($transactionId)
    {
        $this->_validateId($transactionId);

        $path = $this->_config->merchantPath() . '/transactions/' . $transactionId . '/release_from_escrow';
        $response = $this->_http->put($path, []);
        return $this->_verifyGatewayResponse($response);
    }

    public function cancelRelease($transactionId)
    {
        $this->_validateId($transactionId);

        $path = $this->_config->merchantPath() . '/transactions/' . $transactionId . '/cancel_release';
        $response = $this->_http->put($path, []);
        return $this->_verifyGatewayResponse($response);
    }

    public function refund($transactionId, $amount = null)
    {
        self::_validateId($transactionId);

        $params = ['transaction' => ['amount' => $amount]];
        $path = $this->_config->merchantPath() . '/transactions/' . $transactionId . '/refund';
        $response = $this->_http->post($path, $params);
        return $this->_verifyGatewayResponse($response);
    }

    /**
     * sends the create request to the gateway
     *
     * @ignore
     * @param var $subPath
     * @param array $params
     * @return mixed
     */
    public function _doCreate($subPath, $params)
    {
        $fullPath = $this->_config->merchantPath() . $subPath;
        $response = $this->_http->post($fullPath, $params);

        return $this->_verifyGatewayResponse($response);
    }

    /**
     * verifies that a valid transaction id is being used
     * @ignore
     * @param string transaction id
     * @throws InvalidArgumentException
     */
    private function _validateId($id = null) {
        if (empty($id)) {
           throw new InvalidArgumentException(
                   'expected transaction id to be set'
                   );
        }
        if (!preg_match('/^[0-9a-z]+$/', $id)) {
            throw new InvalidArgumentException(
                    $id . ' is an invalid transaction id.'
                    );
        }
    }

    /**
     * generic method for validating incoming gateway responses
     *
     * creates a new Transaction object and encapsulates
     * it inside a Result\Successful object, or
     * encapsulates a Errors object inside a Result\Error
     * alternatively, throws an Unexpected exception if the response is invalid.
     *
     * @ignore
     * @param array $response gateway response values
     * @return Result\Successful|Result\Error
     * @throws Exception\Unexpected
     */
    private function _verifyGatewayResponse($response)
    {
        if (isset($response['transaction'])) {
            // return a populated instance of Transaction
            return new Result\Successful(
                    Transaction::factory($response['transaction'])
            );
        } else if (isset($response['apiErrorResponse'])) {
            return new Result\Error($response['apiErrorResponse']);
        } else {
            throw new Exception\Unexpected(
            "Expected transaction or apiErrorResponse"
            );
        }
    }
}
class_alias('Braintree\TransactionGateway', 'Braintree_TransactionGateway');
