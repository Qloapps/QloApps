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

namespace Twig;

use Twig\Error\SyntaxError;

/**
 * Represents a token stream.
 *
 * @final
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TokenStream
{
    protected $tokens;
    protected $current = 0;
    protected $filename;

    private $source;

    /**
     * @param array       $tokens An array of tokens
     * @param string|null $name   The name of the template which tokens are associated with
     * @param string|null $source The source code associated with the tokens
     */
    public function __construct(array $tokens, $name = null, $source = null)
    {
        if (!$name instanceof Source) {
            if (null !== $name || null !== $source) {
                @trigger_error(sprintf('Passing a string as the $name argument of %s() is deprecated since version 1.27. Pass a \Twig\Source instance instead.', __METHOD__), E_USER_DEPRECATED);
            }
            $this->source = new Source($source, $name);
        } else {
            $this->source = $name;
        }

        $this->tokens = $tokens;

        // deprecated, not used anymore, to be removed in 2.0
        $this->filename = $this->source->getName();
    }

    public function __toString()
    {
        return implode("\n", $this->tokens);
    }

    public function injectTokens(array $tokens)
    {
        $this->tokens = array_merge(\array_slice($this->tokens, 0, $this->current), $tokens, \array_slice($this->tokens, $this->current));
    }

    /**
     * Sets the pointer to the next token and returns the old one.
     *
     * @return Token
     */
    public function next()
    {
        if (!isset($this->tokens[++$this->current])) {
            throw new SyntaxError('Unexpected end of template.', $this->tokens[$this->current - 1]->getLine(), $this->source);
        }

        return $this->tokens[$this->current - 1];
    }

    /**
     * Tests a token, sets the pointer to the next one and returns it or throws a syntax error.
     *
     * @return Token|null The next token if the condition is true, null otherwise
     */
    public function nextIf($primary, $secondary = null)
    {
        if ($this->tokens[$this->current]->test($primary, $secondary)) {
            return $this->next();
        }
    }

    /**
     * Tests a token and returns it or throws a syntax error.
     *
     * @return Token
     */
    public function expect($type, $value = null, $message = null)
    {
        $token = $this->tokens[$this->current];
        if (!$token->test($type, $value)) {
            $line = $token->getLine();
            throw new SyntaxError(sprintf('%sUnexpected token "%s"%s ("%s" expected%s).',
                $message ? $message.'. ' : '',
                Token::typeToEnglish($token->getType()),
                $token->getValue() ? sprintf(' of value "%s"', $token->getValue()) : '',
                Token::typeToEnglish($type), $value ? sprintf(' with value "%s"', $value) : ''),
                $line,
                $this->source
            );
        }
        $this->next();

        return $token;
    }

    /**
     * Looks at the next token.
     *
     * @param int $number
     *
     * @return Token
     */
    public function look($number = 1)
    {
        if (!isset($this->tokens[$this->current + $number])) {
            throw new SyntaxError('Unexpected end of template.', $this->tokens[$this->current + $number - 1]->getLine(), $this->source);
        }

        return $this->tokens[$this->current + $number];
    }

    /**
     * Tests the current token.
     *
     * @return bool
     */
    public function test($primary, $secondary = null)
    {
        return $this->tokens[$this->current]->test($primary, $secondary);
    }

    /**
     * Checks if end of stream was reached.
     *
     * @return bool
     */
    public function isEOF()
    {
        return Token::EOF_TYPE === $this->tokens[$this->current]->getType();
    }

    /**
     * @return Token
     */
    public function getCurrent()
    {
        return $this->tokens[$this->current];
    }

    /**
     * Gets the name associated with this stream (null if not defined).
     *
     * @return string|null
     *
     * @deprecated since 1.27 (to be removed in 2.0)
     */
    public function getFilename()
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->source->getName();
    }

    /**
     * Gets the source code associated with this stream.
     *
     * @return string
     *
     * @internal Don't use this as it might be empty depending on the environment configuration
     *
     * @deprecated since 1.27 (to be removed in 2.0)
     */
    public function getSource()
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->source->getCode();
    }

    /**
     * Gets the source associated with this stream.
     *
     * @return Source
     *
     * @internal
     */
    public function getSourceContext()
    {
        return $this->source;
    }
}

class_alias('Twig\TokenStream', 'Twig_TokenStream');
