<?php
namespace Braintree;

class KeyValueNode
{
    public function __construct($name)
    {
        $this->name = $name;
        $this->searchTerm = True;
    }

    public function is($value)
    {
        $this->searchTerm = $value;
        return $this;
    }

    public function toParam()
    {
        return $this->searchTerm;
    }
}
class_alias('Braintree\KeyValueNode', 'Braintree_KeyValueNode');
