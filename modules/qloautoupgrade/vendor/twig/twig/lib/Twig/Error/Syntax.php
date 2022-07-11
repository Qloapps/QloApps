<?php

use Twig\Error\SyntaxError;

class_exists('Twig\Error\SyntaxError');

if (\false) {
    class Twig_Error_Syntax extends SyntaxError
    {
    }
}
