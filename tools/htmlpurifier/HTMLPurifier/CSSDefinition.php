<?php

/**
 * Defines allowed CSS attributes and what their values are.
 * @see HTMLPurifier_HTMLDefinition
 */
class HTMLPurifier_CSSDefinition extends HTMLPurifier_Definition
{

    public $type = 'CSS';

    /**
     * Assoc array of attribute name to definition object.
     * @type HTMLPurifier_AttrDef[]
     */
    public $info = [];

    /**
     * Constructs the info array.  The meat of this class.
     * @param HTMLPurifier_Config $config
     */
    protected function doSetup($config)
    {
        $this->info['text-align'] = new HTMLPurifier_AttrDef_Enum(
            ['left', 'right', 'center', 'justify'],
            false
        );

        $this->info['direction'] = new HTMLPurifier_AttrDef_Enum(
            ['ltr', 'rtl'],
            false
        );

        $border_style =
            $this->info['border-bottom-style'] =
            $this->info['border-right-style'] =
            $this->info['border-left-style'] =
            $this->info['border-top-style'] = new HTMLPurifier_AttrDef_Enum(
                [
                    'none',
                    'hidden',
                    'dotted',
                    'dashed',
                    'solid',
                    'double',
                    'groove',
                    'ridge',
                    'inset',
                    'outset'
                ],
                false
            );

        $this->info['border-style'] = new HTMLPurifier_AttrDef_CSS_Multiple($border_style);

        $this->info['clear'] = new HTMLPurifier_AttrDef_Enum(
            ['none', 'left', 'right', 'both'],
            false
        );
        $this->info['float'] = new HTMLPurifier_AttrDef_Enum(
            ['none', 'left', 'right'],
            false
        );
        $this->info['font-style'] = new HTMLPurifier_AttrDef_Enum(
            ['normal', 'italic', 'oblique'],
            false
        );
        $this->info['font-variant'] = new HTMLPurifier_AttrDef_Enum(
            ['normal', 'small-caps'],
            false
        );

        $uri_or_none = new HTMLPurifier_AttrDef_CSS_Composite(
            [
                new HTMLPurifier_AttrDef_Enum(['none']),
                new HTMLPurifier_AttrDef_CSS_URI()
            ]
        );

        $this->info['list-style-position'] = new HTMLPurifier_AttrDef_Enum(
            ['inside', 'outside'],
            false
        );
        $this->info['list-style-type'] = new HTMLPurifier_AttrDef_Enum(
            [
                'disc',
                'circle',
                'square',
                'decimal',
                'lower-roman',
                'upper-roman',
                'lower-alpha',
                'upper-alpha',
                'none'
            ],
            false
        );
        $this->info['list-style-image'] = $uri_or_none;

        $this->info['list-style'] = new HTMLPurifier_AttrDef_CSS_ListStyle($config);

        $this->info['text-transform'] = new HTMLPurifier_AttrDef_Enum(
            ['capitalize', 'uppercase', 'lowercase', 'none'],
            false
        );
        $this->info['color'] = new HTMLPurifier_AttrDef_CSS_Color();

        $this->info['background-image'] = $uri_or_none;
        $this->info['background-repeat'] = new HTMLPurifier_AttrDef_Enum(
            ['repeat', 'repeat-x', 'repeat-y', 'no-repeat']
        );
        $this->info['background-attachment'] = new HTMLPurifier_AttrDef_Enum(
            ['scroll', 'fixed']
        );
        $this->info['background-position'] = new HTMLPurifier_AttrDef_CSS_BackgroundPosition();

        $this->info['background-size'] = new HTMLPurifier_AttrDef_CSS_Composite(
            [
                new HTMLPurifier_AttrDef_Enum(
                    [
                        'auto',
                        'cover',
                        'contain',
                    ]
                ),
                new HTMLPurifier_AttrDef_CSS_Percentage(),
                new HTMLPurifier_AttrDef_CSS_Length()
            ]
        );

        $border_color =
            $this->info['border-top-color'] =
            $this->info['border-bottom-color'] =
            $this->info['border-left-color'] =
            $this->info['border-right-color'] =
            $this->info['background-color'] = new HTMLPurifier_AttrDef_CSS_Composite(
                [
                    new HTMLPurifier_AttrDef_Enum(['transparent']),
                    new HTMLPurifier_AttrDef_CSS_Color()
                ]
            );

        $this->info['background'] = new HTMLPurifier_AttrDef_CSS_Background($config);

        $this->info['border-color'] = new HTMLPurifier_AttrDef_CSS_Multiple($border_color);

        $border_width =
            $this->info['border-top-width'] =
            $this->info['border-bottom-width'] =
            $this->info['border-left-width'] =
            $this->info['border-right-width'] = new HTMLPurifier_AttrDef_CSS_Composite(
                [
                    new HTMLPurifier_AttrDef_Enum(['thin', 'medium', 'thick']),
                    new HTMLPurifier_AttrDef_CSS_Length('0') //disallow negative
                ]
            );

        $this->info['border-width'] = new HTMLPurifier_AttrDef_CSS_Multiple($border_width);

        $this->info['letter-spacing'] = new HTMLPurifier_AttrDef_CSS_Composite(
            [
                new HTMLPurifier_AttrDef_Enum(['normal']),
                new HTMLPurifier_AttrDef_CSS_Length()
            ]
        );

        $this->info['word-spacing'] = new HTMLPurifier_AttrDef_CSS_Composite(
            [
                new HTMLPurifier_AttrDef_Enum(['normal']),
                new HTMLPurifier_AttrDef_CSS_Length()
            ]
        );

        $this->info['font-size'] = new HTMLPurifier_AttrDef_CSS_Composite(
            [
                new HTMLPurifier_AttrDef_Enum(
                    [
                        'xx-small',
                        'x-small',
                        'small',
                        'medium',
                        'large',
                        'x-large',
                        'xx-large',
                        'larger',
                        'smaller'
                    ]
                ),
                new HTMLPurifier_AttrDef_CSS_Percentage(),
                new HTMLPurifier_AttrDef_CSS_Length()
            ]
        );

        $this->info['line-height'] = new HTMLPurifier_AttrDef_CSS_Composite(
            [
                new HTMLPurifier_AttrDef_Enum(['normal']),
                new HTMLPurifier_AttrDef_CSS_Number(true), // no negatives
                new HTMLPurifier_AttrDef_CSS_Length('0'),
                new HTMLPurifier_AttrDef_CSS_Percentage(true)
            ]
        );

        $margin =
            $this->info['margin-top'] =
            $this->info['margin-bottom'] =
            $this->info['margin-left'] =
            $this->info['margin-right'] = new HTMLPurifier_AttrDef_CSS_Composite(
                [
                    new HTMLPurifier_AttrDef_CSS_Length(),
                    new HTMLPurifier_AttrDef_CSS_Percentage(),
                    new HTMLPurifier_AttrDef_Enum(['auto'])
                ]
            );

        $this->info['margin'] = new HTMLPurifier_AttrDef_CSS_Multiple($margin);

        // non-negative
        $padding =
            $this->info['padding-top'] =
            $this->info['padding-bottom'] =
            $this->info['padding-left'] =
            $this->info['padding-right'] = new HTMLPurifier_AttrDef_CSS_Composite(
                [
                    new HTMLPurifier_AttrDef_CSS_Length('0'),
                    new HTMLPurifier_AttrDef_CSS_Percentage(true)
                ]
            );

        $this->info['padding'] = new HTMLPurifier_AttrDef_CSS_Multiple($padding);

        $this->info['text-indent'] = new HTMLPurifier_AttrDef_CSS_Composite(
            [
                new HTMLPurifier_AttrDef_CSS_Length(),
                new HTMLPurifier_AttrDef_CSS_Percentage()
            ]
        );

        $trusted_wh = new HTMLPurifier_AttrDef_CSS_Composite(
            [
                new HTMLPurifier_AttrDef_CSS_Length('0'),
                new HTMLPurifier_AttrDef_CSS_Percentage(true),
                new HTMLPurifier_AttrDef_Enum(['auto'])
            ]
        );
        $trusted_min_wh = new HTMLPurifier_AttrDef_CSS_Composite(
            [
                new HTMLPurifier_AttrDef_CSS_Length('0'),
                new HTMLPurifier_AttrDef_CSS_Percentage(true),
            ]
        );
        $trusted_max_wh = new HTMLPurifier_AttrDef_CSS_Composite(
            [
                new HTMLPurifier_AttrDef_CSS_Length('0'),
                new HTMLPurifier_AttrDef_CSS_Percentage(true),
                new HTMLPurifier_AttrDef_Enum(['none'])
            ]
        );
        $max = $config->get('CSS.MaxImgLength');

        $this->info['width'] =
        $this->info['height'] =
            $max === null ?
                $trusted_wh :
                new HTMLPurifier_AttrDef_Switch(
                    'img',
                    // For img tags:
                    new HTMLPurifier_AttrDef_CSS_Composite(
                        [
                            new HTMLPurifier_AttrDef_CSS_Length('0', $max),
                            new HTMLPurifier_AttrDef_Enum(['auto'])
                        ]
                    ),
                    // For everyone else:
                    $trusted_wh
                );
        $this->info['min-width'] =
        $this->info['min-height'] =
            $max === null ?
                $trusted_min_wh :
                new HTMLPurifier_AttrDef_Switch(
                    'img',
                    // For img tags:
                    new HTMLPurifier_AttrDef_CSS_Length('0', $max),
                    // For everyone else:
                    $trusted_min_wh
                );
        $this->info['max-width'] =
        $this->info['max-height'] =
            $max === null ?
                $trusted_max_wh :
                new HTMLPurifier_AttrDef_Switch(
                    'img',
                    // For img tags:
                    new HTMLPurifier_AttrDef_CSS_Composite(
                        [
                            new HTMLPurifier_AttrDef_CSS_Length('0', $max),
                            new HTMLPurifier_AttrDef_Enum(['none'])
                        ]
                    ),
                    // For everyone else:
                    $trusted_max_wh
                );

        $this->info['aspect-ratio'] = new HTMLPurifier_AttrDef_CSS_Multiple(
            new HTMLPurifier_AttrDef_CSS_Composite([
                new HTMLPurifier_AttrDef_CSS_Ratio(),
                new HTMLPurifier_AttrDef_Enum(['auto']),
            ])
        );

        // text-decoration and related shorthands
        $this->info['text-decoration'] = new HTMLPurifier_AttrDef_CSS_TextDecoration();

        $this->info['text-decoration-line'] = new HTMLPurifier_AttrDef_Enum(
            ['none', 'underline', 'overline', 'line-through']
        );

        $this->info['text-decoration-style'] = new HTMLPurifier_AttrDef_Enum(
            ['solid', 'double', 'dotted', 'dashed', 'wavy']
        );

        $this->info['text-decoration-color'] = new HTMLPurifier_AttrDef_CSS_Color();

        $this->info['text-decoration-thickness'] = new HTMLPurifier_AttrDef_CSS_Composite([
            new HTMLPurifier_AttrDef_CSS_Length(),
            new HTMLPurifier_AttrDef_CSS_Percentage(),
            new HTMLPurifier_AttrDef_Enum(['auto', 'from-font'])
        ]);

        $this->info['font-family'] = new HTMLPurifier_AttrDef_CSS_FontFamily();

        // this could use specialized code
        $this->info['font-weight'] = new HTMLPurifier_AttrDef_Enum(
            [
                'normal',
                'bold',
                'bolder',
                'lighter',
                '100',
                '200',
                '300',
                '400',
                '500',
                '600',
                '700',
                '800',
                '900'
            ],
            false
        );

        // MUST be called after other font properties, as it references
        // a CSSDefinition object
        $this->info['font'] = new HTMLPurifier_AttrDef_CSS_Font($config);

        // same here
        $this->info['border'] =
        $this->info['border-bottom'] =
        $this->info['border-top'] =
        $this->info['border-left'] =
        $this->info['border-right'] = new HTMLPurifier_AttrDef_CSS_Border($config);

        $this->info['border-collapse'] = new HTMLPurifier_AttrDef_Enum(
            ['collapse', 'separate']
        );

        $this->info['caption-side'] = new HTMLPurifier_AttrDef_Enum(
            ['top', 'bottom']
        );

        $this->info['table-layout'] = new HTMLPurifier_AttrDef_Enum(
            ['auto', 'fixed']
        );

        $this->info['vertical-align'] = new HTMLPurifier_AttrDef_CSS_Composite(
            [
                new HTMLPurifier_AttrDef_Enum(
                    [
                        'baseline',
                        'sub',
                        'super',
                        'top',
                        'text-top',
                        'middle',
                        'bottom',
                        'text-bottom'
                    ]
                ),
                new HTMLPurifier_AttrDef_CSS_Length(),
                new HTMLPurifier_AttrDef_CSS_Percentage()
            ]
        );

        $this->info['border-spacing'] = new HTMLPurifier_AttrDef_CSS_Multiple(new HTMLPurifier_AttrDef_CSS_Length(), 2);

        // These CSS properties don't work on many browsers, but we live
        // in THE FUTURE!
        $this->info['white-space'] = new HTMLPurifier_AttrDef_Enum(
            ['nowrap', 'normal', 'pre', 'pre-wrap', 'pre-line']
        );

        if ($config->get('CSS.Proprietary')) {
            $this->doSetupProprietary($config);
        }

        if ($config->get('CSS.AllowTricky')) {
            $this->doSetupTricky($config);
        }

        if ($config->get('CSS.Trusted')) {
            $this->doSetupTrusted($config);
        }

        $allow_important = $config->get('CSS.AllowImportant');
        // wrap all attr-defs with decorator that handles !important
        foreach ($this->info as $k => $v) {
            $this->info[$k] = new HTMLPurifier_AttrDef_CSS_ImportantDecorator($v, $allow_important);
        }

        $this->setupConfigStuff($config);
    }

    /**
     * @param HTMLPurifier_Config $config
     */
    protected function doSetupProprietary($config)
    {
        // Internet Explorer only scrollbar colors
        $this->info['scrollbar-arrow-color'] = new HTMLPurifier_AttrDef_CSS_Color();
        $this->info['scrollbar-base-color'] = new HTMLPurifier_AttrDef_CSS_Color();
        $this->info['scrollbar-darkshadow-color'] = new HTMLPurifier_AttrDef_CSS_Color();
        $this->info['scrollbar-face-color'] = new HTMLPurifier_AttrDef_CSS_Color();
        $this->info['scrollbar-highlight-color'] = new HTMLPurifier_AttrDef_CSS_Color();
        $this->info['scrollbar-shadow-color'] = new HTMLPurifier_AttrDef_CSS_Color();

        // vendor specific prefixes of opacity
        $this->info['-moz-opacity'] = new HTMLPurifier_AttrDef_CSS_AlphaValue();
        $this->info['-khtml-opacity'] = new HTMLPurifier_AttrDef_CSS_AlphaValue();

        // only opacity, for now
        $this->info['filter'] = new HTMLPurifier_AttrDef_CSS_Filter();

        // more CSS3
        $this->info['page-break-after'] =
        $this->info['page-break-before'] = new HTMLPurifier_AttrDef_Enum(
            [
                'auto',
                'always',
                'avoid',
                'left',
                'right'
            ]
        );
        $this->info['page-break-inside'] = new HTMLPurifier_AttrDef_Enum(['auto', 'avoid']);

        $border_radius = new HTMLPurifier_AttrDef_CSS_Composite(
            [
                new HTMLPurifier_AttrDef_CSS_Percentage(true), // disallow negative
                new HTMLPurifier_AttrDef_CSS_Length('0') // disallow negative
            ]);

        $this->info['border-top-left-radius'] =
        $this->info['border-top-right-radius'] =
        $this->info['border-bottom-right-radius'] =
        $this->info['border-bottom-left-radius'] = new HTMLPurifier_AttrDef_CSS_Multiple($border_radius, 2);
        // TODO: support SLASH syntax
        $this->info['border-radius'] = new HTMLPurifier_AttrDef_CSS_Multiple($border_radius, 4);

    }

    /**
     * @param HTMLPurifier_Config $config
     */
    protected function doSetupTricky($config)
    {
        $this->info['display'] = new HTMLPurifier_AttrDef_Enum(
            [
                'inline',
                'block',
                'list-item',
                'run-in',
                'compact',
                'marker',
                'table',
                'inline-block',
                'inline-table',
                'table-row-group',
                'table-header-group',
                'table-footer-group',
                'table-row',
                'table-column-group',
                'table-column',
                'table-cell',
                'table-caption',
                'none'
            ]
        );
        $this->info['visibility'] = new HTMLPurifier_AttrDef_Enum(
            ['visible', 'hidden', 'collapse']
        );
        $this->info['overflow'] = new HTMLPurifier_AttrDef_Enum(['visible', 'hidden', 'auto', 'scroll']);
        $this->info['opacity'] = new HTMLPurifier_AttrDef_CSS_AlphaValue();
    }

    /**
     * @param HTMLPurifier_Config $config
     */
    protected function doSetupTrusted($config)
    {
        $this->info['position'] = new HTMLPurifier_AttrDef_Enum(
            ['static', 'relative', 'absolute', 'fixed']
        );
        $this->info['top'] =
        $this->info['left'] =
        $this->info['right'] =
        $this->info['bottom'] = new HTMLPurifier_AttrDef_CSS_Composite(
            [
                new HTMLPurifier_AttrDef_CSS_Length(),
                new HTMLPurifier_AttrDef_CSS_Percentage(),
                new HTMLPurifier_AttrDef_Enum(['auto']),
            ]
        );
        $this->info['z-index'] = new HTMLPurifier_AttrDef_CSS_Composite(
            [
                new HTMLPurifier_AttrDef_Integer(),
                new HTMLPurifier_AttrDef_Enum(['auto']),
            ]
        );
    }

    /**
     * Performs extra config-based processing. Based off of
     * HTMLPurifier_HTMLDefinition.
     * @param HTMLPurifier_Config $config
     * @todo Refactor duplicate elements into common class (probably using
     *       composition, not inheritance).
     */
    protected function setupConfigStuff($config)
    {
        // setup allowed elements
        $support = "(for information on implementing this, see the " .
            "support forums) ";
        $allowed_properties = $config->get('CSS.AllowedProperties');
        if ($allowed_properties !== null) {
            foreach ($this->info as $name => $d) {
                if (!isset($allowed_properties[$name])) {
                    unset($this->info[$name]);
                }
                unset($allowed_properties[$name]);
            }
            // emit errors
            foreach ($allowed_properties as $name => $d) {
                // :TODO: Is this htmlspecialchars() call really necessary?
                $name = htmlspecialchars($name);
                trigger_error("Style attribute '$name' is not supported $support", E_USER_WARNING);
            }
        }

        $forbidden_properties = $config->get('CSS.ForbiddenProperties');
        if ($forbidden_properties !== null) {
            foreach ($this->info as $name => $d) {
                if (isset($forbidden_properties[$name])) {
                    unset($this->info[$name]);
                }
            }
        }
    }
}

// vim: et sw=4 sts=4
