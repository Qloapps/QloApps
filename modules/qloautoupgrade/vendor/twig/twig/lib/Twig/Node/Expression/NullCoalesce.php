<?php

use Twig\Node\Expression\NullCoalesceExpression;

class_exists('Twig\Node\Expression\NullCoalesceExpression');

if (\false) {
    class Twig_Node_Expression_NullCoalesce extends NullCoalesceExpression
    {
    }
}
