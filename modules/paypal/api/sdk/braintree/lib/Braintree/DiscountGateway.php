<?php
namespace Braintree;

class DiscountGateway
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

    public function all()
    {
        $path = $this->_config->merchantPath() . '/discounts';
        $response = $this->_http->get($path);

        $discounts = ["discount" => $response['discounts']];

        return Util::extractAttributeAsArray(
            $discounts,
            'discount'
        );
    }
}
class_alias('Braintree\DiscountGateway', 'Braintree_DiscountGateway');
