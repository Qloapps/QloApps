<?php
namespace Braintree;

class EqualityNode extends IsNode
{
    function isNot($value)
    {
        $this->searchTerms['is_not'] = strval($value);
        return $this;
    }
}
class_alias('Braintree\EqualityNode', 'Braintree_EqualityNode');
