<?php
namespace Braintree;

class AddOnGateway
{
    /**
     *
     * @var Gateway
     */
    private $_gateway;

    /**
     *
     * @var Configuration
     */
    private $_config;

    /**
     *
     * @var Http
     */
    private $_http;

    /**
     *
     * @param Gateway $gateway
     */
    public function __construct($gateway)
    {
        $this->_gateway = $gateway;
        $this->_config = $gateway->config;
        $this->_config->assertHasAccessTokenOrKeys();
        $this->_http = new Http($gateway->config);
    }

    /**
     *
     * @return AddOn[]
     */
    public function all()
    {
        $path = $this->_config->merchantPath() . '/add_ons';
        $response = $this->_http->get($path);

        $addOns = ["addOn" => $response['addOns']];

        return Util::extractAttributeAsArray(
            $addOns,
            'addOn'
        );
    }
}
class_alias('Braintree\AddOnGateway', 'Braintree_AddOnGateway');
