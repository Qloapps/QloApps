<?php

use Twig\Extension\SandboxExtension;

class_exists('Twig\Extension\SandboxExtension');

if (\false) {
    class Twig_Extension_Sandbox extends SandboxExtension
    {
    }
}
