<?php
/**
* 2010-2018 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class ChannelManagerServices
{
    public function __construct()
    {
    }

    public function getResponseFromChannelManagerApi($requestParams)
    {
        $curlInit = curl_init();
        curl_setopt($curlInit, CURLOPT_URL, $requestParams['url']);
        curl_setopt($curlInit, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curlInit, CURLOPT_HTTPHEADER, $requestParams['headers']);
        curl_setopt($curlInit, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlInit, CURLOPT_CUSTOMREQUEST, $requestParams['GET']);
        curl_setopt($curlInit, CURLOPT_POSTFIELDS, $requestParams['post_data']);
        $response = curl_exec($curlInit);
        return $response;
    }

    public function associateUserToPMS($params)
    {
        $requestParams = array();
        $requestParams['method'] = 'GET';
        $requestParams['url'] = 'https://api.myallocator.com/pms/v201408/json/AssociateUserToPMS';
        $requestParams['headers'] = array(
                                'Content-Type: application/json',
                            );
        $postData = array(
                        "Auth/UserId" => "qloapps",
                        "Auth/UserPassword" => "webkul12#",
                        "Auth/VendorId" => "qloapps",
                        "Auth/VendorPassword" => "nwR64TXpXKdD",
                    );
        $requestParams['post_data'] = $postData;
        $response = $this->getResponseFromChannelManagerApi($requestParams);
        return $response;
    }

    public function AssociatePropertyToPMS($params)
    {
        $requestParams = array();
        $requestParams['method'] = 'GET';
        $requestParams['url'] = 'https://api.myallocator.com/pms/v201408/json/AssociatePropertyToPMS';
        $requestParams['headers'] = array(
                                'Content-Type: application/json',
                            );
        $postData = array(
                        "Auth/UserId" => "qloapps",
                        "Auth/UserPassword" => "webkul12#",
                        "Auth/VendorId" => "qloapps",
                        "Auth/VendorPassword" => "nwR64TXpXKdD",
                        "Auth/PropertyId" => "19140",
                        /*"PMSPropertyId" => "username-on-the-remote-pms-system"*/
                    );
        $requestParams['post_data'] = $postData;
        $response = $this->getResponseFromChannelManagerApi($requestParams);
        return $response;
    }
}
