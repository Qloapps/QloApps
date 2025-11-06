<?php

/**
 * Definition for list containers ul and ol.
 *
 * What does this do?  The big thing is to handle ol/ul at the top
 * level of list nodes, which should be handled specially by /folding/
 * them into the previous list node.  We generally shouldn't ever
 * see other disallowed elements, because the autoclose behavior
 * in MakeWellFormed handles it.
 */
class HTMLPurifier_ChildDef_List extends HTMLPurifier_ChildDef
{
    /**
     * @type string
     */
    public $type = 'list';
    /**
     * @type array
     */
    // lying a little bit, so that we can handle ul and ol ourselves
    // XXX: This whole business with 'wrap' is all a bit unsatisfactory
    public $elements = array('li' => true, 'ul' => true, 'ol' => true);

    public $whitespace;

    /**
     * @param array $children
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function validateChildren($children, $config, $context)
    {
        // Flag for subclasses
        $this->whitespace = false;

        // if there are no tokens, delete parent node
        if (empty($children)) {
            return false;
        }

        // if li is not allowed, delete parent node
        if (!isset($config->getHTMLDefinition()->info['li'])) {
            trigger_error("Cannot allow ul/ol without allowing li", E_USER_WARNING);
            return false;
        }

        // the new set of children
        $result = array();

        // a little sanity check to make sure it's not ALL whitespace
        $all_whitespace = true;

        $current_li = null;

        foreach ($children as $node) {
            if (!empty($node->is_whitespace)) {
                $result[] = $node;
                continue;
            }
            $all_whitespace = false; // phew, we're not talking about whitespace

            if ($node->name === 'li') {
                // good
                $current_li = $node;
                $result[] = $node;
            } else {
                // we want to tuck this into the previous li
                // Invariant: we expect the node to be ol/ul
                // ToDo: Make this more robust in the case of not ol/ul
                // by distinguishing between existing li and li created
                // to handle non-list elements; non-list elements should
                // not be appended to an existing li; only li created
                // for non-list. This distinction is not currently made.
                if ($current_li === null) {
                    $current_li = new HTMLPurifier_Node_Element('li');
                    $result[] = $current_li;
                }
                $current_li->children[] = $node;
                $current_li->empty = false; // XXX fascinating! Check for this error elsewhere ToDo
            }
        }
        if (empty($result)) {
            return false;
        }
        if ($all_whitespace) {
            return false;
        }
        return $result;
    }
}

// vim: et sw=4 sts=4
