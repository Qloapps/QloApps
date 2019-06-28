<?php
namespace Braintree;

class PartialMatchNode extends EqualityNode
{
    public function startsWith($value)
    {
        $this->searchTerms["starts_with"] = strval($value);
        return $this;
    }

    public function endsWith($value)
    {
        $this->searchTerms["ends_with"] = strval($value);
        return $this;
    }
}
class_alias('Braintree\PartialMatchNode', 'Braintree_PartialMatchNode');
