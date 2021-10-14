<?php

/**
 * Validates shorthand CSS property font.
 */
class HTMLPurifier_AttrDef_CSS_Font extends HTMLPurifier_AttrDef
{

    /**
     * Local copy of validators
     * @type HTMLPurifier_AttrDef[]
     * @note If we moved specific CSS property definitions to their own
     *       classes instead of having them be assembled at run time by
     *       CSSDefinition, this wouldn't be necessary.  We'd instantiate
     *       our own copies.
     */
    protected $info = array();

    /**
     * @param HTMLPurifier_Config $config
     */
    public function __construct($config)
    {
        $def = $config->getCSSDefinition();
        $this->info['font-style'] = $def->info['font-style'];
        $this->info['font-variant'] = $def->info['font-variant'];
        $this->info['font-weight'] = $def->info['font-weight'];
        $this->info['font-size'] = $def->info['font-size'];
        $this->info['line-height'] = $def->info['line-height'];
        $this->info['font-family'] = $def->info['font-family'];
    }

    /**
     * @param string $string
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        static $system_fonts = array(
            'caption' => true,
            'icon' => true,
            'menu' => true,
            'message-box' => true,
            'small-caption' => true,
            'status-bar' => true
        );

        // regular pre-processing
        $string = $this->parseCDATA($string);
        if ($string === '') {
            return false;
        }

        // check if it's one of the keywords
        $lowercase_string = strtolower($string);
        if (isset($system_fonts[$lowercase_string])) {
            return $lowercase_string;
        }

        $bits = explode(' ', $string); // bits to process
        $stage = 0; // this indicates what we're looking for
        $caught = array(); // which stage 0 properties have we caught?
        $stage_1 = array('font-style', 'font-variant', 'font-weight');
        $final = ''; // output

        for ($i = 0, $size = count($bits); $i < $size; $i++) {
            if ($bits[$i] === '') {
                continue;
            }
            switch ($stage) {
                case 0: // attempting to catch font-style, font-variant or font-weight
                    foreach ($stage_1 as $validator_name) {
                        if (isset($caught[$validator_name])) {
                            continue;
                        }
                        $r = $this->info[$validator_name]->validate(
                            $bits[$i],
                            $config,
                            $context
                        );
                        if ($r !== false) {
                            $final .= $r . ' ';
                            $caught[$validator_name] = true;
                            break;
                        }
                    }
                    // all three caught, continue on
                    if (count($caught) >= 3) {
                        $stage = 1;
                    }
                    if ($r !== false) {
                        break;
                    }
                case 1: // attempting to catch font-size and perhaps line-height
                    $found_slash = false;
                    if (strpos($bits[$i], '/') !== false) {
                        list($font_size, $line_height) =
                            explode('/', $bits[$i]);
                        if ($line_height === '') {
                            // ooh, there's a space after the slash!
                            $line_height = false;
                            $found_slash = true;
                        }
                    } else {
                        $font_size = $bits[$i];
                        $line_height = false;
                    }
                    $r = $this->info['font-size']->validate(
                        $font_size,
                        $config,
                        $context
                    );
                    if ($r !== false) {
                        $final .= $r;
                        // attempt to catch line-height
                        if ($line_height === false) {
                            // we need to scroll forward
                            for ($j = $i + 1; $j < $size; $j++) {
                                if ($bits[$j] === '') {
                                    continue;
                                }
                                if ($bits[$j] === '/') {
                                    if ($found_slash) {
                                        return false;
                                    } else {
                                        $found_slash = true;
                                        continue;
                                    }
                                }
                                $line_height = $bits[$j];
                                break;
                            }
                        } else {
                            // slash already found
                            $found_slash = true;
                            $j = $i;
                        }
                        if ($found_slash) {
                            $i = $j;
                            $r = $this->info['line-height']->validate(
                                $line_height,
                                $config,
                                $context
                            );
                            if ($r !== false) {
                                $final .= '/' . $r;
                            }
                        }
                        $final .= ' ';
                        $stage = 2;
                        break;
                    }
                    return false;
                case 2: // attempting to catch font-family
                    $font_family =
                        implode(' ', array_slice($bits, $i, $size - $i));
                    $r = $this->info['font-family']->validate(
                        $font_family,
                        $config,
                        $context
                    );
                    if ($r !== false) {
                        $final .= $r . ' ';
                        // processing completed successfully
                        return rtrim($final);
                    }
                    return false;
            }
        }
        return false;
    }
}

// vim: et sw=4 sts=4
