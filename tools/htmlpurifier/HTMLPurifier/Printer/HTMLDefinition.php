<?php

class HTMLPurifier_Printer_HTMLDefinition extends HTMLPurifier_Printer
{

    /**
     * @type HTMLPurifier_HTMLDefinition, for easy access
     */
    protected $def;

    /**
     * @param HTMLPurifier_Config $config
     * @return string
     */
    public function render($config)
    {
        $ret = '';
        $this->config =& $config;

        $this->def = $config->getHTMLDefinition();

        $ret .= $this->start('div', array('class' => 'HTMLPurifier_Printer'));

        $ret .= $this->renderDoctype();
        $ret .= $this->renderEnvironment();
        $ret .= $this->renderContentSets();
        $ret .= $this->renderInfo();

        $ret .= $this->end('div');

        return $ret;
    }

    /**
     * Renders the Doctype table
     * @return string
     */
    protected function renderDoctype()
    {
        $doctype = $this->def->doctype;
        $ret = '';
        $ret .= $this->start('table');
        $ret .= $this->element('caption', 'Doctype');
        $ret .= $this->row('Name', $doctype->name);
        $ret .= $this->row('XML', $doctype->xml ? 'Yes' : 'No');
        $ret .= $this->row('Default Modules', implode(', ', $doctype->modules));
        $ret .= $this->row('Default Tidy Modules', implode(', ', $doctype->tidyModules));
        $ret .= $this->end('table');
        return $ret;
    }


    /**
     * Renders environment table, which is miscellaneous info
     * @return string
     */
    protected function renderEnvironment()
    {
        $def = $this->def;

        $ret = '';

        $ret .= $this->start('table');
        $ret .= $this->element('caption', 'Environment');

        $ret .= $this->row('Parent of fragment', $def->info_parent);
        $ret .= $this->renderChildren($def->info_parent_def->child);
        $ret .= $this->row('Block wrap name', $def->info_block_wrapper);

        $ret .= $this->start('tr');
        $ret .= $this->element('th', 'Global attributes');
        $ret .= $this->element('td', $this->listifyAttr($def->info_global_attr), null, 0);
        $ret .= $this->end('tr');

        $ret .= $this->start('tr');
        $ret .= $this->element('th', 'Tag transforms');
        $list = array();
        foreach ($def->info_tag_transform as $old => $new) {
            $new = $this->getClass($new, 'TagTransform_');
            $list[] = "<$old> with $new";
        }
        $ret .= $this->element('td', $this->listify($list));
        $ret .= $this->end('tr');

        $ret .= $this->start('tr');
        $ret .= $this->element('th', 'Pre-AttrTransform');
        $ret .= $this->element('td', $this->listifyObjectList($def->info_attr_transform_pre));
        $ret .= $this->end('tr');

        $ret .= $this->start('tr');
        $ret .= $this->element('th', 'Post-AttrTransform');
        $ret .= $this->element('td', $this->listifyObjectList($def->info_attr_transform_post));
        $ret .= $this->end('tr');

        $ret .= $this->end('table');
        return $ret;
    }

    /**
     * Renders the Content Sets table
     * @return string
     */
    protected function renderContentSets()
    {
        $ret = '';
        $ret .= $this->start('table');
        $ret .= $this->element('caption', 'Content Sets');
        foreach ($this->def->info_content_sets as $name => $lookup) {
            $ret .= $this->heavyHeader($name);
            $ret .= $this->start('tr');
            $ret .= $this->element('td', $this->listifyTagLookup($lookup));
            $ret .= $this->end('tr');
        }
        $ret .= $this->end('table');
        return $ret;
    }

    /**
     * Renders the Elements ($info) table
     * @return string
     */
    protected function renderInfo()
    {
        $ret = '';
        $ret .= $this->start('table');
        $ret .= $this->element('caption', 'Elements ($info)');
        ksort($this->def->info);
        $ret .= $this->heavyHeader('Allowed tags', 2);
        $ret .= $this->start('tr');
        $ret .= $this->element('td', $this->listifyTagLookup($this->def->info), array('colspan' => 2));
        $ret .= $this->end('tr');
        foreach ($this->def->info as $name => $def) {
            $ret .= $this->start('tr');
            $ret .= $this->element('th', "<$name>", array('class' => 'heavy', 'colspan' => 2));
            $ret .= $this->end('tr');
            $ret .= $this->start('tr');
            $ret .= $this->element('th', 'Inline content');
            $ret .= $this->element('td', $def->descendants_are_inline ? 'Yes' : 'No');
            $ret .= $this->end('tr');
            if (!empty($def->excludes)) {
                $ret .= $this->start('tr');
                $ret .= $this->element('th', 'Excludes');
                $ret .= $this->element('td', $this->listifyTagLookup($def->excludes));
                $ret .= $this->end('tr');
            }
            if (!empty($def->attr_transform_pre)) {
                $ret .= $this->start('tr');
                $ret .= $this->element('th', 'Pre-AttrTransform');
                $ret .= $this->element('td', $this->listifyObjectList($def->attr_transform_pre));
                $ret .= $this->end('tr');
            }
            if (!empty($def->attr_transform_post)) {
                $ret .= $this->start('tr');
                $ret .= $this->element('th', 'Post-AttrTransform');
                $ret .= $this->element('td', $this->listifyObjectList($def->attr_transform_post));
                $ret .= $this->end('tr');
            }
            if (!empty($def->auto_close)) {
                $ret .= $this->start('tr');
                $ret .= $this->element('th', 'Auto closed by');
                $ret .= $this->element('td', $this->listifyTagLookup($def->auto_close));
                $ret .= $this->end('tr');
            }
            $ret .= $this->start('tr');
            $ret .= $this->element('th', 'Allowed attributes');
            $ret .= $this->element('td', $this->listifyAttr($def->attr), array(), 0);
            $ret .= $this->end('tr');

            if (!empty($def->required_attr)) {
                $ret .= $this->row('Required attributes', $this->listify($def->required_attr));
            }

            $ret .= $this->renderChildren($def->child);
        }
        $ret .= $this->end('table');
        return $ret;
    }

    /**
     * Renders a row describing the allowed children of an element
     * @param HTMLPurifier_ChildDef $def HTMLPurifier_ChildDef of pertinent element
     * @return string
     */
    protected function renderChildren($def)
    {
        $context = new HTMLPurifier_Context();
        $ret = '';
        $ret .= $this->start('tr');
        $elements = array();
        $attr = array();
        if (isset($def->elements)) {
            if ($def->type == 'strictblockquote') {
                $def->validateChildren(array(), $this->config, $context);
            }
            $elements = $def->elements;
        }
        if ($def->type == 'chameleon') {
            $attr['rowspan'] = 2;
        } elseif ($def->type == 'empty') {
            $elements = array();
        } elseif ($def->type == 'table') {
            $elements = array_flip(
                array(
                    'col',
                    'caption',
                    'colgroup',
                    'thead',
                    'tfoot',
                    'tbody',
                    'tr'
                )
            );
        }
        $ret .= $this->element('th', 'Allowed children', $attr);

        if ($def->type == 'chameleon') {

            $ret .= $this->element(
                'td',
                '<em>Block</em>: ' .
                $this->escape($this->listifyTagLookup($def->block->elements)),
                null,
                0
            );
            $ret .= $this->end('tr');
            $ret .= $this->start('tr');
            $ret .= $this->element(
                'td',
                '<em>Inline</em>: ' .
                $this->escape($this->listifyTagLookup($def->inline->elements)),
                null,
                0
            );

        } elseif ($def->type == 'custom') {

            $ret .= $this->element(
                'td',
                '<em>' . ucfirst($def->type) . '</em>: ' .
                $def->dtd_regex
            );

        } else {
            $ret .= $this->element(
                'td',
                '<em>' . ucfirst($def->type) . '</em>: ' .
                $this->escape($this->listifyTagLookup($elements)),
                null,
                0
            );
        }
        $ret .= $this->end('tr');
        return $ret;
    }

    /**
     * Listifies a tag lookup table.
     * @param array $array Tag lookup array in form of array('tagname' => true)
     * @return string
     */
    protected function listifyTagLookup($array)
    {
        ksort($array);
        $list = array();
        foreach ($array as $name => $discard) {
            if ($name !== '#PCDATA' && !isset($this->def->info[$name])) {
                continue;
            }
            $list[] = $name;
        }
        return $this->listify($list);
    }

    /**
     * Listifies a list of objects by retrieving class names and internal state
     * @param array $array List of objects
     * @return string
     * @todo Also add information about internal state
     */
    protected function listifyObjectList($array)
    {
        ksort($array);
        $list = array();
        foreach ($array as $obj) {
            $list[] = $this->getClass($obj, 'AttrTransform_');
        }
        return $this->listify($list);
    }

    /**
     * Listifies a hash of attributes to AttrDef classes
     * @param array $array Array hash in form of array('attrname' => HTMLPurifier_AttrDef)
     * @return string
     */
    protected function listifyAttr($array)
    {
        ksort($array);
        $list = array();
        foreach ($array as $name => $obj) {
            if ($obj === false) {
                continue;
            }
            $list[] = "$name&nbsp;=&nbsp;<i>" . $this->getClass($obj, 'AttrDef_') . '</i>';
        }
        return $this->listify($list);
    }

    /**
     * Creates a heavy header row
     * @param string $text
     * @param int $num
     * @return string
     */
    protected function heavyHeader($text, $num = 1)
    {
        $ret = '';
        $ret .= $this->start('tr');
        $ret .= $this->element('th', $text, array('colspan' => $num, 'class' => 'heavy'));
        $ret .= $this->end('tr');
        return $ret;
    }
}

// vim: et sw=4 sts=4
