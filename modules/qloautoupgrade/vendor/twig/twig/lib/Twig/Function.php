<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Twig\Node\Node;

@trigger_error('The Twig_Function class is deprecated since version 1.12 and will be removed in 2.0. Use \Twig\TwigFunction instead.', E_USER_DEPRECATED);

/**
 * Represents a template function.
 *
 * Use \Twig\TwigFunction instead.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @deprecated since 1.12 (to be removed in 2.0)
 */
abstract class Twig_Function implements Twig_FunctionInterface, Twig_FunctionCallableInterface
{
    protected $options;
    protected $arguments = [];

    public function __construct(array $options = [])
    {
        $this->options = array_merge([
            'needs_environment' => false,
            'needs_context' => false,
            'callable' => null,
        ], $options);
    }

    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function needsEnvironment()
    {
        return $this->options['needs_environment'];
    }

    public function needsContext()
    {
        return $this->options['needs_context'];
    }

    public function getSafe(Node $functionArgs)
    {
        if (isset($this->options['is_safe'])) {
            return $this->options['is_safe'];
        }

        if (isset($this->options['is_safe_callback'])) {
            return \call_user_func($this->options['is_safe_callback'], $functionArgs);
        }

        return [];
    }

    public function getCallable()
    {
        return $this->options['callable'];
    }
}
