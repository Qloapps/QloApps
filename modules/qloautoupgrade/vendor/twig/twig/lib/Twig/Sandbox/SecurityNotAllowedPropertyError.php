<?php

use Twig\Sandbox\SecurityNotAllowedPropertyError;

class_exists('Twig\Sandbox\SecurityNotAllowedPropertyError');

if (\false) {
    class Twig_Sandbox_SecurityNotAllowedPropertyError extends SecurityNotAllowedPropertyError
    {
    }
}
