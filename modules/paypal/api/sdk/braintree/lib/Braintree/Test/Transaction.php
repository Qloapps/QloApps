<?php
namespace Braintree\Test;

use Braintree\Configuration;

/**
 * Transaction amounts used for testing purposes
 *
 * The constants in this class can be used to create transactions with
 * the desired status in the sandbox environment.
 *
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */
final class Transaction
{
    /**
     * settle a transaction by id in sandbox
     *
     * @param string $id transaction id
     * @param Configuration $config gateway config
     * @return Transaction
     */
    public static function settle($transactionId)
    {
        return Configuration::gateway()->testing()->settle($transactionId);
    }

    /**
     * settlement confirm a transaction by id in sandbox
     *
     * @param string $id transaction id
     * @param Configuration $config gateway config
     * @return Transaction
     */
    public static function settlementConfirm($transactionId)
    {
        return Configuration::gateway()->testing()->settlementConfirm($transactionId);
    }

    /**
     * settlement decline a transaction by id in sandbox
     *
     * @param string $id transaction id
     * @param Configuration $config gateway config
     * @return Transaction
     */
    public static function settlementDecline($transactionId)
    {
        return Configuration::gateway()->testing()->settlementDecline($transactionId);
    }

    /**
     * settlement pending a transaction by id in sandbox
     *
     * @param string $id transaction id
     * @param Configuration $config gateway config
     * @return Transaction
     */
    public static function settlementPending($transactionId)
    {
        return Configuration::gateway()->testing()->settlementPending($transactionId);
    }
}
class_alias('Braintree\Test\Transaction', 'Braintree_Test_Transaction');
