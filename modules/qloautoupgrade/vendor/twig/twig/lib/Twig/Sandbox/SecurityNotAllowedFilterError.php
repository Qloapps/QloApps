<?php

use Twig\Sandbox\SecurityNotAllowedFilterError;

class_exists('Twig\Sandbox\SecurityNotAllowedFilterError');

if (\false) {
    class Twig_Sandbox_SecurityNotAllowedFilterError extends SecurityNotAllowedFilterError
    {
    }
}
