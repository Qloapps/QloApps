<?php

/**
 * Converts a stream of HTMLPurifier_Token into an HTMLPurifier_Node,
 * and back again.
 *
 * @note This transformation is not an equivalence.  We mutate the input
 * token stream to make it so; see all [MUT] markers in code.
 */
class HTMLPurifier_Arborize
{
    public static function arborize($tokens, $config, $context) {
        $definition = $config->getHTMLDefinition();
        $parent = new HTMLPurifier_Token_Start($definition->info_parent);
        $stack = array($parent->toNode());
        foreach ($tokens as $token) {
            $token->skip = null; // [MUT]
            $token->carryover = null; // [MUT]
            if ($token instanceof HTMLPurifier_Token_End) {
                $token->start = null; // [MUT]
                $r = array_pop($stack);
                //assert($r->name === $token->name);
                //assert(empty($token->attr));
                $r->endCol = $token->col;
                $r->endLine = $token->line;
                $r->endArmor = $token->armor;
                continue;
            }
            $node = $token->toNode();
            $stack[count($stack)-1]->children[] = $node;
            if ($token instanceof HTMLPurifier_Token_Start) {
                $stack[] = $node;
            }
        }
        //assert(count($stack) == 1);
        return $stack[0];
    }

    public static function flatten($node, $config, $context) {
        $level = 0;
        $nodes = array($level => new HTMLPurifier_Queue(array($node)));
        $closingTokens = array();
        $tokens = array();
        do {
            while (!$nodes[$level]->isEmpty()) {
                $node = $nodes[$level]->shift(); // FIFO
                list($start, $end) = $node->toTokenPair();
                if ($level > 0) {
                    $tokens[] = $start;
                }
                if ($end !== NULL) {
                    $closingTokens[$level][] = $end;
                }
                if ($node instanceof HTMLPurifier_Node_Element) {
                    $level++;
                    $nodes[$level] = new HTMLPurifier_Queue();
                    foreach ($node->children as $childNode) {
                        $nodes[$level]->push($childNode);
                    }
                }
            }
            $level--;
            if ($level && isset($closingTokens[$level])) {
                while ($token = array_pop($closingTokens[$level])) {
                    $tokens[] = $token;
                }
            }
        } while ($level > 0);
        return $tokens;
    }
}
