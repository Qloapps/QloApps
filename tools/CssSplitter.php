<?php
/**
 * PHP CSS Splitter
 *
 * Based on https://github.com/zweilove/css_splitter but written for PHP
 *
 * @homepage  http://www.github.com/dlundgren/php-css-splitter
 * @author    David Lundgren
 * @copyright 2014, David Lundgren. All Rights Reserved.
 * @license   MIT
 */

/**
 * Splitter of the CSS
 *
 * This class is responsible for counting and splitting a CSS file into given chunks.
 *
 * This is most useful for IE9 and lower which have a selector limit of 4096 per stylesheet
 *
 * @package CssSplitter
 */
class CssSplitter
{
    /**
     * @var integer The default number of selectors to split on
     */
    const MAX_SELECTORS_DEFAULT = 4095;

    /**
     * Counts the selectors in the given css
     *
     * @param string $css
     * @return int The count of selectors in the CSS
     */
    public function countSelectors($css)
    {
        $count = 0;
        foreach ($this->splitIntoBlocks($css) as $rules) {
            $count += $rules['count'];
        }

        return $count;
    }

    /**
     * Returns the requested part of the split css
     *
     * @param string $css
     * @param int    $part
     * @param int    $maxSelectors
     * @return null|string
     */
    public function split($css, $part = 1, $maxSelectors = self::MAX_SELECTORS_DEFAULT)
    {
        if (empty($css)) {
            return null;
        }

        $charset = $this->extractCharset($css);
        isset($charset) && $css = str_replace($charset, '', $css);
        if (empty($css)) {
            return null;
        }

        $blocks = $this->splitIntoBlocks($css);
        if (empty($blocks)) {
            return null;
        }

        $output    = $charset ? : '';
        $count     = 0;
        $partCount = 1;
        foreach ($blocks as $block) {
            $appliedMedia = false;
            foreach ($block['rules'] as $rule) {
                $tmpCount = $rule['count'];

                // we have a new part so let's reset and increase
                if (($count + $tmpCount) > $maxSelectors) {
                    $partCount++;
                    $count = 0;
                }

                $count += $tmpCount;
                if ($partCount < $part) {
                    continue;
                }
                if ($partCount > $part) {
                    break;
                }

                if (!$appliedMedia && isset($block['at-rule'])) {
                    $output .= $block['at-rule'] . ' {';
                    $appliedMedia = true;
                }
                $output .= $rule['rule'];
            }
            $appliedMedia && $output .= '}';
        }

        return $output;
    }

    /**
     * Summarizes the block of CSS
     *
     * This splits the block into it's css rules and then counts the selectors in the rule
     *
     * @param $block
     * @return array Array(rules,count)
     */
    private function summarizeBlock($block)
    {
        $block = array(
            'rules' => is_array($block) ? $block : $this->splitIntoRules(trim($block)),
            'count' => 0
        );
        foreach ($block['rules'] as $key => $rule) {
            $block['rules'][$key] = array(
                'rule'  => $rule,
                'count' => $this->countSelectorsInRule($rule),
            );
            $block['count'] += $block['rules'][$key]['count'];
        }

        return $block;
    }

    /**
     * Splits the css into blocks maintaining the order of the rules and media queries
     *
     * This makes it easier to split the CSS into files when a media query might be split
     *
     * @param $css
     * @return array
     */
    private function splitIntoBlocks($css)
    {
        if (is_array($css)) {
            return $css;
        }

        $blocks = array();
        $css    = $this->stripComments($css);
        $offset = 0;

        // catch all @feature {...} blocks
        if (preg_match_all('/(@[^{]+){([^{}]*{[^}]*})*\s*}/ism', $css, $matches, PREG_OFFSET_CAPTURE) > 0) {
            foreach ($matches[0] as $key => $match) {
                $atRule              = trim($matches[1][$key][0]);
                list($rules, $start) = $match;

                if ($start > $offset) {
                    // make previous selectors into their own block
                    $block = trim(substr($css, $offset, $start - $offset));
                    if (!empty($block)) {
                        $blocks[] = $this->summarizeBlock($block);
                    }
                }
                $offset = $start + strlen($rules);

                // currently only @media rules need to be parsed and counted for IE9 selectors
                if (strpos($atRule, '@media') === 0) {
                    $block  = $this->summarizeBlock(substr($rules, strpos($rules, '{') + 1, -1));
                }
                else {
                    $block  = array(
                        'count'   => 1,
                        'rules'   => array(
                            array(
                                'rule' => substr($rules, strpos($rules, '{') + 1, -1),
                                'count' => 1,
                            )
                        ),

                    );
                }

                $block['at-rule'] = $atRule;
                $blocks[]               = $block;
            }

            // catch any remaining as it's own block
            $block = trim(substr($css, $offset));
            if (!empty($block)) {
                $blocks[] = $this->summarizeBlock($block);
            }
        }
        else {
            $blocks[] = $this->summarizeBlock($css);
        }

        return $blocks;
    }

    /**
     * Splits the css into it's rules
     *
     * @param $css
     * @return array
     */
    private function splitIntoRules($css)
    {
        $rules = preg_split('/}/', trim($this->stripComments($css)));

        // complete any rules by append } to them
        array_walk($rules,
            function (&$s) {
                !empty($s) && $s = trim("$s}");
            });

        // clears out any empty rules
        return array_filter($rules);
    }

    /**
     * Counts the selectors in the rule
     *
     * @param $rule
     * @return int
     */
    private function countSelectorsInRule($rule)
    {
        $lines = explode('{', $this->stripComments($rule));

        return substr_count($lines[0], ',') + 1;
    }

    /**
     * Extracts the charset from the rule
     *
     * @param $css
     * @return null|string
     */
    private function extractCharset($css)
    {
        if (preg_match('/^(\@charset[^;]+;)/is', $css, $match)) {
            return $match[1];
        }

        return null;
    }

    /**
     * Strips the comment
     *
     * @param $string
     * @return mixed
     */
    private function stripComments($string)
    {
        return preg_replace('~/\*.*?\*/~si', '', $string);
    }
}
