<?php
namespace Braintree\Dispute;

use Braintree\Instance;

/**
 * Transaction details for a dispute
 *
 * @package    Braintree
 * @copyright  2010 Braintree Payment Solutions
 */

/**
 * Creates an instance of DisbursementDetails as returned from a transaction
 *
 *
 * @package    Braintree
 * @copyright  2010 Braintree Payment Solutions
 *
 * @property-read string $amount
 * @property-read string $id
 */
class TransactionDetails extends Instance
{
}

class_alias('Braintree\Dispute\TransactionDetails', 'Braintree_Dispute_TransactionDetails');
