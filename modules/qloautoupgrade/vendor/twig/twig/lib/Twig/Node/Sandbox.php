<?php

use Twig\Node\SandboxNode;

class_exists('Twig\Node\SandboxNode');

if (\false) {
    class Twig_Node_Sandbox extends SandboxNode
    {
    }
}
