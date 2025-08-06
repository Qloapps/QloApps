<?php

use Twig\Sandbox\SecurityNotAllowedTagError;

class_exists('Twig\Sandbox\SecurityNotAllowedTagError');

if (\false) {
    class Twig_Sandbox_SecurityNotAllowedTagError extends SecurityNotAllowedTagError
    {
    }
}
