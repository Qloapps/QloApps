<?php
namespace Braintree;

use InvalidArgumentException;

/**
 * Braintree SubscriptionGateway module
 *
 * <b>== More information ==</b>
 *
 * For more detailed information on Subscriptions, see {@link http://www.braintreepayments.com/gateway/subscription-api http://www.braintreepaymentsolutions.com/gateway/subscription-api}
 *
 * PHP Version 5
 *
 * @package   Braintree
 * @copyright 2015 Braintree, a division of PayPal, Inc.
 */
class SubscriptionGateway
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

    public function create($attributes)
    {
        Util::verifyKeys(self::_createSignature(), $attributes);
        $path = $this->_config->merchantPath() . '/subscriptions';
        $response = $this->_http->post($path, ['subscription' => $attributes]);
        return $this->_verifyGatewayResponse($response);
    }

    public function find($id)
    {
        $this->_validateId($id);

        try {
            $path = $this->_config->merchantPath() . '/subscriptions/' . $id;
            $response = $this->_http->get($path);
            return Subscription::factory($response['subscription']);
        } catch (Exception\NotFound $e) {
            throw new Exception\NotFound('subscription with id ' . $id . ' not found');
        }

    }

    public function search($query)
    {
        $criteria = [];
        foreach ($query as $term) {
            $criteria[$term->name] = $term->toparam();
        }


        $path = $this->_config->merchantPath() . '/subscriptions/advanced_search_ids';
        $response = $this->_http->post($path, ['search' => $criteria]);
        $pager = [
            'object' => $this,
            'method' => 'fetch',
            'methodArgs' => [$query]
            ];

        return new ResourceCollection($response, $pager);
    }

    public function fetch($query, $ids)
    {
        $criteria = [];
        foreach ($query as $term) {
            $criteria[$term->name] = $term->toparam();
        }
        $criteria["ids"] = SubscriptionSearch::ids()->in($ids)->toparam();
        $path = $this->_config->merchantPath() . '/subscriptions/advanced_search';
        $response = $this->_http->post($path, ['search' => $criteria]);

        return Util::extractAttributeAsArray(
            $response['subscriptions'],
            'subscription'
        );
    }

    public function update($subscriptionId, $attributes)
    {
        Util::verifyKeys(self::_updateSignature(), $attributes);
        $path = $this->_config->merchantPath() . '/subscriptions/' . $subscriptionId;
        $response = $this->_http->put($path, ['subscription' => $attributes]);
        return $this->_verifyGatewayResponse($response);
    }

    public function retryCharge($subscriptionId, $amount = null)
    {
        $transaction_params = ['type' => Transaction::SALE,
            'subscriptionId' => $subscriptionId];
        if (isset($amount)) {
            $transaction_params['amount'] = $amount;
        }

        $path = $this->_config->merchantPath() . '/transactions';
        $response = $this->_http->post($path, ['transaction' => $transaction_params]);
        return $this->_verifyGatewayResponse($response);
    }

    public function cancel($subscriptionId)
    {
        $path = $this->_config->merchantPath() . '/subscriptions/' . $subscriptionId . '/cancel';
        $response = $this->_http->put($path);
        return $this->_verifyGatewayResponse($response);
    }

    private static function _createSignature()
    {
        return array_merge(
            [
                'billingDayOfMonth',
                'firstBillingDate',
                'createdAt',
                'updatedAt',
                'id',
                'merchantAccountId',
                'neverExpires',
                'numberOfBillingCycles',
                'paymentMethodToken',
                'paymentMethodNonce',
                'planId',
                'price',
                'trialDuration',
                'trialDurationUnit',
                'trialPeriod',
                ['descriptor' => ['name', 'phone', 'url']],
                ['options' => ['doNotInheritAddOnsOrDiscounts', 'startImmediately']],
            ],
            self::_addOnDiscountSignature()
        );
    }

    private static function _updateSignature()
    {
        return array_merge(
            [
                'merchantAccountId', 'numberOfBillingCycles', 'paymentMethodToken', 'planId',
                'paymentMethodNonce', 'id', 'neverExpires', 'price',
                ['descriptor' => ['name', 'phone', 'url']],
                ['options' => ['prorateCharges', 'replaceAllAddOnsAndDiscounts', 'revertSubscriptionOnProrationFailure']],
            ],
            self::_addOnDiscountSignature()
        );
    }

    private static function _addOnDiscountSignature()
    {
        return [
            [
                'addOns' => [
                    ['add' => ['amount', 'inheritedFromId', 'neverExpires', 'numberOfBillingCycles', 'quantity']],
                    ['update' => ['amount', 'existingId', 'neverExpires', 'numberOfBillingCycles', 'quantity']],
                    ['remove' => ['_anyKey_']],
                ]
            ],
            [
                'discounts' => [
                    ['add' => ['amount', 'inheritedFromId', 'neverExpires', 'numberOfBillingCycles', 'quantity']],
                    ['update' => ['amount', 'existingId', 'neverExpires', 'numberOfBillingCycles', 'quantity']],
                    ['remove' => ['_anyKey_']],
                ]
            ]
        ];
    }

    /**
     * @ignore
     */
    private function _validateId($id = null) {
        if (empty($id)) {
           throw new InvalidArgumentException(
                   'expected subscription id to be set'
                   );
        }
        if (!preg_match('/^[0-9A-Za-z_-]+$/', $id)) {
            throw new InvalidArgumentException(
                    $id . ' is an invalid subscription id.'
                    );
        }
    }

    /**
     * @ignore
     */
    private function _verifyGatewayResponse($response)
    {
        if (isset($response['subscription'])) {
            return new Result\Successful(
                Subscription::factory($response['subscription'])
            );
        } else if (isset($response['transaction'])) {
            // return a populated instance of Transaction, for subscription retryCharge
            return new Result\Successful(
                Transaction::factory($response['transaction'])
            );
        } else if (isset($response['apiErrorResponse'])) {
            return new Result\Error($response['apiErrorResponse']);
        } else {
            throw new Exception\Unexpected(
            "Expected subscription, transaction, or apiErrorResponse"
            );
        }
    }
}
class_alias('Braintree\SubscriptionGateway', 'Braintree_SubscriptionGateway');
