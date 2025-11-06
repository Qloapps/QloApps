<?php

class HTMLPurifier_HTMLModule_Tidy_XHTMLAndHTML4 extends HTMLPurifier_HTMLModule_Tidy
{

    /**
     * @return array
     */
    public function makeFixes()
    {
        $r = array();

        // == deprecated tag transforms ===================================

        $r['font'] = new HTMLPurifier_TagTransform_Font();
        $r['menu'] = new HTMLPurifier_TagTransform_Simple('ul');
        $r['dir'] = new HTMLPurifier_TagTransform_Simple('ul');
        $r['center'] = new HTMLPurifier_TagTransform_Simple('div', 'text-align:center;');
        $r['u'] = new HTMLPurifier_TagTransform_Simple('span', 'text-decoration:underline;');
        $r['s'] = new HTMLPurifier_TagTransform_Simple('span', 'text-decoration:line-through;');
        $r['strike'] = new HTMLPurifier_TagTransform_Simple('span', 'text-decoration:line-through;');

        // == deprecated attribute transforms =============================

        $r['caption@align'] =
            new HTMLPurifier_AttrTransform_EnumToCSS(
                'align',
                array(
                    // we're following IE's behavior, not Firefox's, due
                    // to the fact that no one supports caption-side:right,
                    // W3C included (with CSS 2.1). This is a slightly
                    // unreasonable attribute!
                    'left' => 'text-align:left;',
                    'right' => 'text-align:right;',
                    'top' => 'caption-side:top;',
                    'bottom' => 'caption-side:bottom;' // not supported by IE
                )
            );

        // @align for img -------------------------------------------------
        $r['img@align'] =
            new HTMLPurifier_AttrTransform_EnumToCSS(
                'align',
                array(
                    'left' => 'float:left;',
                    'right' => 'float:right;',
                    'top' => 'vertical-align:top;',
                    'middle' => 'vertical-align:middle;',
                    'bottom' => 'vertical-align:baseline;',
                )
            );

        // @align for table -----------------------------------------------
        $r['table@align'] =
            new HTMLPurifier_AttrTransform_EnumToCSS(
                'align',
                array(
                    'left' => 'float:left;',
                    'center' => 'margin-left:auto;margin-right:auto;',
                    'right' => 'float:right;'
                )
            );

        // @align for hr -----------------------------------------------
        $r['hr@align'] =
            new HTMLPurifier_AttrTransform_EnumToCSS(
                'align',
                array(
                    // we use both text-align and margin because these work
                    // for different browsers (IE and Firefox, respectively)
                    // and the melange makes for a pretty cross-compatible
                    // solution
                    'left' => 'margin-left:0;margin-right:auto;text-align:left;',
                    'center' => 'margin-left:auto;margin-right:auto;text-align:center;',
                    'right' => 'margin-left:auto;margin-right:0;text-align:right;'
                )
            );

        // @align for h1, h2, h3, h4, h5, h6, p, div ----------------------
        // {{{
        $align_lookup = array();
        $align_values = array('left', 'right', 'center', 'justify');
        foreach ($align_values as $v) {
            $align_lookup[$v] = "text-align:$v;";
        }
        // }}}
        $r['h1@align'] =
        $r['h2@align'] =
        $r['h3@align'] =
        $r['h4@align'] =
        $r['h5@align'] =
        $r['h6@align'] =
        $r['p@align'] =
        $r['div@align'] =
            new HTMLPurifier_AttrTransform_EnumToCSS('align', $align_lookup);

        // @bgcolor for table, tr, td, th ---------------------------------
        $r['table@bgcolor'] =
        $r['tr@bgcolor'] =
        $r['td@bgcolor'] =
        $r['th@bgcolor'] =
            new HTMLPurifier_AttrTransform_BgColor();

        // @border for img ------------------------------------------------
        $r['img@border'] = new HTMLPurifier_AttrTransform_Border();

        // @clear for br --------------------------------------------------
        $r['br@clear'] =
            new HTMLPurifier_AttrTransform_EnumToCSS(
                'clear',
                array(
                    'left' => 'clear:left;',
                    'right' => 'clear:right;',
                    'all' => 'clear:both;',
                    'none' => 'clear:none;',
                )
            );

        // @height for td, th ---------------------------------------------
        $r['td@height'] =
        $r['th@height'] =
            new HTMLPurifier_AttrTransform_Length('height');

        // @hspace for img ------------------------------------------------
        $r['img@hspace'] = new HTMLPurifier_AttrTransform_ImgSpace('hspace');

        // @noshade for hr ------------------------------------------------
        // this transformation is not precise but often good enough.
        // different browsers use different styles to designate noshade
        $r['hr@noshade'] =
            new HTMLPurifier_AttrTransform_BoolToCSS(
                'noshade',
                'color:#808080;background-color:#808080;border:0;'
            );

        // @nowrap for td, th ---------------------------------------------
        $r['td@nowrap'] =
        $r['th@nowrap'] =
            new HTMLPurifier_AttrTransform_BoolToCSS(
                'nowrap',
                'white-space:nowrap;'
            );

        // @size for hr  --------------------------------------------------
        $r['hr@size'] = new HTMLPurifier_AttrTransform_Length('size', 'height');

        // @type for li, ol, ul -------------------------------------------
        // {{{
        $ul_types = array(
            'disc' => 'list-style-type:disc;',
            'square' => 'list-style-type:square;',
            'circle' => 'list-style-type:circle;'
        );
        $ol_types = array(
            '1' => 'list-style-type:decimal;',
            'i' => 'list-style-type:lower-roman;',
            'I' => 'list-style-type:upper-roman;',
            'a' => 'list-style-type:lower-alpha;',
            'A' => 'list-style-type:upper-alpha;'
        );
        $li_types = $ul_types + $ol_types;
        // }}}

        $r['ul@type'] = new HTMLPurifier_AttrTransform_EnumToCSS('type', $ul_types);
        $r['ol@type'] = new HTMLPurifier_AttrTransform_EnumToCSS('type', $ol_types, true);
        $r['li@type'] = new HTMLPurifier_AttrTransform_EnumToCSS('type', $li_types, true);

        // @vspace for img ------------------------------------------------
        $r['img@vspace'] = new HTMLPurifier_AttrTransform_ImgSpace('vspace');

        // @width for table, hr, td, th, col ------------------------------------------
        $r['table@width'] =
        $r['td@width'] =
        $r['th@width'] =
        $r['col@width'] =
        $r['hr@width'] = new HTMLPurifier_AttrTransform_Length('width');

        return $r;
    }
}

// vim: et sw=4 sts=4
