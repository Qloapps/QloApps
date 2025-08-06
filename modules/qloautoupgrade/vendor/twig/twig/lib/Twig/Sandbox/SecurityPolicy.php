<?php

use Twig\Sandbox\SecurityPolicy;

class_exists('Twig\Sandbox\SecurityPolicy');

if (\false) {
    class Twig_Sandbox_SecurityPolicy extends SecurityPolicy
    {
    }
}
