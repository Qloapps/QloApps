<?php

use Twig\Sandbox\SecurityError;

class_exists('Twig\Sandbox\SecurityError');

if (\false) {
    class Twig_Sandbox_SecurityError extends SecurityError
    {
    }
}
