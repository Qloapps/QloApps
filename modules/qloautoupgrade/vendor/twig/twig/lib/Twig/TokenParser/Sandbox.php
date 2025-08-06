<?php

use Twig\TokenParser\SandboxTokenParser;

class_exists('Twig\TokenParser\SandboxTokenParser');

if (\false) {
    class Twig_TokenParser_Sandbox extends SandboxTokenParser
    {
    }
}
