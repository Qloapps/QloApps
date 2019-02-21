<?php
namespace Braintree;

class TextNode extends PartialMatchNode
{
    public function contains($value)
    {
        $this->searchTerms["contains"] = strval($value);
        return $this;
    }
}
class_alias('Braintree\TextNode', 'Braintree_TextNode');
