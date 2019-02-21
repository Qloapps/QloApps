<?php
namespace Braintree;

/**
 * Braintree OAuthGateway module
 * PHP Version 5
 * Creates and manages Braintree Addresses
 *
 * @package   Braintree
 * @copyright 2015 Braintree, a division of PayPal, Inc.
 */
class OAuthGateway
{
    private $_gateway;
    private $_config;
    private $_http;

    public function __construct($gateway)
    {
        $this->_gateway = $gateway;
        $this->_config = $gateway->config;
        $this->_http = new Http($gateway->config);
        $this->_http->useClientCredentials();

        $this->_config->assertHasClientCredentials();
    }

    public function createTokenFromCode($params)
    {
        $params['grantType'] = "authorization_code";
        return $this->_createToken($params);
    }

    public function createTokenFromRefreshToken($params)
    {
        $params['grantType'] = "refresh_token";
        return $this->_createToken($params);
    }

    private function _createToken($params)
    {
        $params = ['credentials' => $params];
        $response = $this->_http->post('/oauth/access_tokens', $params);
        return $this->_verifyGatewayResponse($response);
    }

    private function _verifyGatewayResponse($response)
    {
        if (isset($response['credentials'])) {
            $result =  new Result\Successful(
                OAuthCredentials::factory($response['credentials'])
            );
            return $this->_mapSuccess($result);
        } else if (isset($response['apiErrorResponse'])) {
            $result = new Result\Error($response['apiErrorResponse']);
            return $this->_mapError($result);
        } else {
            throw new Exception\Unexpected(
                "Expected credentials or apiErrorResponse"
            );
        }
    }

    public function _mapError($result)
    {
        $error = $result->errors->deepAll()[0];

        if ($error->code == Error\Codes::OAUTH_INVALID_GRANT) {
            $result->error = 'invalid_grant';
        } else if ($error->code == Error\Codes::OAUTH_INVALID_CREDENTIALS) {
            $result->error = 'invalid_credentials';
        } else if ($error->code == Error\Codes::OAUTH_INVALID_SCOPE) {
            $result->error = 'invalid_scope';
        }
        $result->errorDescription = explode(': ', $error->message)[1];
        return $result;
    }

    public function _mapSuccess($result)
    {
        $credentials = $result->credentials;
        $result->accessToken = $credentials->accessToken;
        $result->refreshToken = $credentials->refreshToken;
        $result->tokenType = $credentials->tokenType;
        $result->expiresAt = $credentials->expiresAt;
        return $result;
    }

    public function connectUrl($params = [])
    {
        $query = Util::camelCaseToDelimiterArray($params, '_');
        $query['client_id'] = $this->_config->getClientId();
        $queryString = preg_replace('/\%5B\d+\%5D/', '%5B%5D', http_build_query($query));
        $url = $this->_config->baseUrl() . '/oauth/connect?' . $queryString;

        return $url . '&signature=' . $this->computeSignature($url) . '&algorithm=SHA256';
    }

    public function computeSignature($url)
    {
        $key = hash('sha256', $this->_config->getClientSecret(), true);
        return hash_hmac('sha256', $url, $key);
    }
}
class_alias('Braintree\OAuthGateway', 'Braintree_OAuthGateway');
