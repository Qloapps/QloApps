<?php
namespace Braintree;

/**
 * Creates an instance of AccountUpdaterDailyReport
 *
 *
 * @package    Braintree
 * @copyright  2016 Braintree, a division of PayPal, Inc.
 *
 * @property-read string $reportUrl
 * @property-read date   $reportDate
 * @property-read date   $receivedDate
 */
final class AccountUpdaterDailyReport extends Base
{
    protected $_attributes = [];

    protected function _initialize($disputeAttribs)
    {
        $this->_attributes = $disputeAttribs;
    }

    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);
        return $instance;
    }

    public function  __toString()
    {
        $display = [
            'reportDate', 'reportUrl'
            ];

        $displayAttributes = [];
        foreach ($display AS $attrib) {
            $displayAttributes[$attrib] = $this->$attrib;
        }
        return __CLASS__ . '[' .
                Util::attributesToString($displayAttributes) .']';
    }
}
class_alias('Braintree\AccountUpdaterDailyReport', 'Braintree_AccountUpdaterDailyReport');
