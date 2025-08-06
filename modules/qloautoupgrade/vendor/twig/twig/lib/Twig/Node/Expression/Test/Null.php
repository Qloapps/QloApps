<?php

use Twig\Node\Expression\Test\NullTest;

class_exists('Twig\Node\Expression\Test\NullTest');

if (\false) {
    class Twig_Node_Expression_Test_Null extends NullTest
    {
    }
}
