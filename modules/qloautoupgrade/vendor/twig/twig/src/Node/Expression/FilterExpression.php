<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 * (c) Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Twig\Node\Expression;

use Twig\Compiler;
use Twig\TwigFilter;

class FilterExpression extends CallExpression
{
    public function __construct(\Twig_NodeInterface $node, ConstantExpression $filterName, \Twig_NodeInterface $arguments, $lineno, $tag = null)
    {
        parent::__construct(['node' => $node, 'filter' => $filterName, 'arguments' => $arguments], [], $lineno, $tag);
    }

    public function compile(Compiler $compiler)
    {
        $name = $this->getNode('filter')->getAttribute('value');
        $filter = $compiler->getEnvironment()->getFilter($name);

        $this->setAttribute('name', $name);
        $this->setAttribute('type', 'filter');
        $this->setAttribute('thing', $filter);
        $this->setAttribute('needs_environment', $filter->needsEnvironment());
        $this->setAttribute('needs_context', $filter->needsContext());
        $this->setAttribute('arguments', $filter->getArguments());
        if ($filter instanceof \Twig_FilterCallableInterface || $filter instanceof TwigFilter) {
            $this->setAttribute('callable', $filter->getCallable());
        }
        if ($filter instanceof TwigFilter) {
            $this->setAttribute('is_variadic', $filter->isVariadic());
        }

        $this->compileCallable($compiler);
    }
}

class_alias('Twig\Node\Expression\FilterExpression', 'Twig_Node_Expression_Filter');
