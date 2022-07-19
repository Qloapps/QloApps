<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Twig;

/**
 * Represents a template test.
 *
 * @final
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TwigTest
{
    protected $name;
    protected $callable;
    protected $options;

    private $arguments = [];

    public function __construct($name, $callable, array $options = [])
    {
        $this->name = $name;
        $this->callable = $callable;
        $this->options = array_merge([
            'is_variadic' => false,
            'node_class' => '\Twig\Node\Expression\TestExpression',
            'deprecated' => false,
            'alternative' => null,
        ], $options);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCallable()
    {
        return $this->callable;
    }

    public function getNodeClass()
    {
        return $this->options['node_class'];
    }

    public function isVariadic()
    {
        return $this->options['is_variadic'];
    }

    public function isDeprecated()
    {
        return (bool) $this->options['deprecated'];
    }

    public function getDeprecatedVersion()
    {
        return $this->options['deprecated'];
    }

    public function getAlternative()
    {
        return $this->options['alternative'];
    }

    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }

    public function getArguments()
    {
        return $this->arguments;
    }
}

class_alias('Twig\TwigTest', 'Twig_SimpleTest');
