<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Twig\Extension;

use Twig\NodeVisitor\OptimizerNodeVisitor;

/**
 * @final
 */
class OptimizerExtension extends AbstractExtension
{
    protected $optimizers;

    public function __construct($optimizers = -1)
    {
        $this->optimizers = $optimizers;
    }

    public function getNodeVisitors()
    {
        return [new OptimizerNodeVisitor($this->optimizers)];
    }

    public function getName()
    {
        return 'optimizer';
    }
}

class_alias('Twig\Extension\OptimizerExtension', 'Twig_Extension_Optimizer');
