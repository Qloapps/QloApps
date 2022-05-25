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

namespace Twig\Node;

use Twig\Compiler;
use Twig\Source;

/**
 * Represents a node in the AST.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Node implements \Twig_NodeInterface
{
    protected $nodes;
    protected $attributes;
    protected $lineno;
    protected $tag;

    private $name;
    private $sourceContext;

    /**
     * @param array  $nodes      An array of named nodes
     * @param array  $attributes An array of attributes (should not be nodes)
     * @param int    $lineno     The line number
     * @param string $tag        The tag name associated with the Node
     */
    public function __construct(array $nodes = [], array $attributes = [], $lineno = 0, $tag = null)
    {
        foreach ($nodes as $name => $node) {
            if (!$node instanceof \Twig_NodeInterface) {
                @trigger_error(sprintf('Using "%s" for the value of node "%s" of "%s" is deprecated since version 1.25 and will be removed in 2.0.', \is_object($node) ? \get_class($node) : (null === $node ? 'null' : \gettype($node)), $name, \get_class($this)), E_USER_DEPRECATED);
            }
        }
        $this->nodes = $nodes;
        $this->attributes = $attributes;
        $this->lineno = $lineno;
        $this->tag = $tag;
    }

    public function __toString()
    {
        $attributes = [];
        foreach ($this->attributes as $name => $value) {
            $attributes[] = sprintf('%s: %s', $name, str_replace("\n", '', var_export($value, true)));
        }

        $repr = [\get_class($this).'('.implode(', ', $attributes)];

        if (\count($this->nodes)) {
            foreach ($this->nodes as $name => $node) {
                $len = \strlen($name) + 4;
                $noderepr = [];
                foreach (explode("\n", (string) $node) as $line) {
                    $noderepr[] = str_repeat(' ', $len).$line;
                }

                $repr[] = sprintf('  %s: %s', $name, ltrim(implode("\n", $noderepr)));
            }

            $repr[] = ')';
        } else {
            $repr[0] .= ')';
        }

        return implode("\n", $repr);
    }

    /**
     * @deprecated since 1.16.1 (to be removed in 2.0)
     */
    public function toXml($asDom = false)
    {
        @trigger_error(sprintf('%s is deprecated since version 1.16.1 and will be removed in 2.0.', __METHOD__), E_USER_DEPRECATED);

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->appendChild($xml = $dom->createElement('twig'));

        $xml->appendChild($node = $dom->createElement('node'));
        $node->setAttribute('class', \get_class($this));

        foreach ($this->attributes as $name => $value) {
            $node->appendChild($attribute = $dom->createElement('attribute'));
            $attribute->setAttribute('name', $name);
            $attribute->appendChild($dom->createTextNode($value));
        }

        foreach ($this->nodes as $name => $n) {
            if (null === $n) {
                continue;
            }

            $child = $n->toXml(true)->getElementsByTagName('node')->item(0);
            $child = $dom->importNode($child, true);
            $child->setAttribute('name', $name);

            $node->appendChild($child);
        }

        return $asDom ? $dom : $dom->saveXML();
    }

    public function compile(Compiler $compiler)
    {
        foreach ($this->nodes as $node) {
            $node->compile($compiler);
        }
    }

    public function getTemplateLine()
    {
        return $this->lineno;
    }

    /**
     * @deprecated since 1.27 (to be removed in 2.0)
     */
    public function getLine()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getTemplateLine() instead.', E_USER_DEPRECATED);

        return $this->lineno;
    }

    public function getNodeTag()
    {
        return $this->tag;
    }

    /**
     * @return bool
     */
    public function hasAttribute($name)
    {
        return \array_key_exists($name, $this->attributes);
    }

    /**
     * @return mixed
     */
    public function getAttribute($name)
    {
        if (!\array_key_exists($name, $this->attributes)) {
            throw new \LogicException(sprintf('Attribute "%s" does not exist for Node "%s".', $name, \get_class($this)));
        }

        return $this->attributes[$name];
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function removeAttribute($name)
    {
        unset($this->attributes[$name]);
    }

    /**
     * @return bool
     */
    public function hasNode($name)
    {
        return \array_key_exists($name, $this->nodes);
    }

    /**
     * @return Node
     */
    public function getNode($name)
    {
        if (!\array_key_exists($name, $this->nodes)) {
            throw new \LogicException(sprintf('Node "%s" does not exist for Node "%s".', $name, \get_class($this)));
        }

        return $this->nodes[$name];
    }

    public function setNode($name, $node = null)
    {
        if (!$node instanceof \Twig_NodeInterface) {
            @trigger_error(sprintf('Using "%s" for the value of node "%s" of "%s" is deprecated since version 1.25 and will be removed in 2.0.', \is_object($node) ? \get_class($node) : (null === $node ? 'null' : \gettype($node)), $name, \get_class($this)), E_USER_DEPRECATED);
        }

        $this->nodes[$name] = $node;
    }

    public function removeNode($name)
    {
        unset($this->nodes[$name]);
    }

    public function count()
    {
        return \count($this->nodes);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->nodes);
    }

    public function setTemplateName($name)
    {
        $this->name = $name;
        foreach ($this->nodes as $node) {
            if (null !== $node) {
                $node->setTemplateName($name);
            }
        }
    }

    public function getTemplateName()
    {
        return $this->name;
    }

    public function setSourceContext(Source $source)
    {
        $this->sourceContext = $source;
        foreach ($this->nodes as $node) {
            if ($node instanceof Node) {
                $node->setSourceContext($source);
            }
        }
    }

    public function getSourceContext()
    {
        return $this->sourceContext;
    }

    /**
     * @deprecated since 1.27 (to be removed in 2.0)
     */
    public function setFilename($name)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use setTemplateName() instead.', E_USER_DEPRECATED);

        $this->setTemplateName($name);
    }

    /**
     * @deprecated since 1.27 (to be removed in 2.0)
     */
    public function getFilename()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getTemplateName() instead.', E_USER_DEPRECATED);

        return $this->name;
    }
}

class_alias('Twig\Node\Node', 'Twig_Node');

// Ensure that the aliased name is loaded to keep BC for classes implementing the typehint with the old aliased name.
class_exists('Twig\Compiler');
