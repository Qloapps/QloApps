<?php
namespace Braintree;

class SettlementBatchSummaryGateway
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
     * @param string $settlement_date
     * @param string $groupByCustomField
     * @return SettlementBatchSummary|Result\Error
     */
    public function generate($settlement_date, $groupByCustomField = NULL)
    {
        $criteria = ['settlement_date' => $settlement_date];
        if (isset($groupByCustomField))
        {
            $criteria['group_by_custom_field'] = $groupByCustomField;
        }
        $params = ['settlement_batch_summary' => $criteria];
        $path = $this->_config->merchantPath() . '/settlement_batch_summary';
        $response = $this->_http->post($path, $params);

        if (isset($groupByCustomField))
        {
            $response['settlementBatchSummary']['records'] = $this->_underscoreCustomField(
                $groupByCustomField,
                $response['settlementBatchSummary']['records']
            );
        }

        return $this->_verifyGatewayResponse($response);
    }

    /**
     *
     * @param string $groupByCustomField
     * @param array $records
     * @return array
    */
    private function _underscoreCustomField($groupByCustomField, $records)
    {
        $updatedRecords = [];

        foreach ($records as $record)
        {
            $camelized = Util::delimiterToCamelCase($groupByCustomField);
            $record[$groupByCustomField] = $record[$camelized];
            unset($record[$camelized]);
            $updatedRecords[] = $record;
        }

        return $updatedRecords;
    }

    /**
     *
     * @param array $response
     * @return Result\Successful|Result\Error
     * @throws Exception\Unexpected
     */
    private function _verifyGatewayResponse($response)
    {
        if (isset($response['settlementBatchSummary'])) {
            return new Result\Successful(
                SettlementBatchSummary::factory($response['settlementBatchSummary'])
            );
        } else if (isset($response['apiErrorResponse'])) {
            return new Result\Error($response['apiErrorResponse']);
        } else {
            throw new Exception\Unexpected(
                "Expected settlementBatchSummary or apiErrorResponse"
            );
        }
    }
}
class_alias('Braintree\SettlementBatchSummaryGateway', 'Braintree_SettlementBatchSummaryGateway');
