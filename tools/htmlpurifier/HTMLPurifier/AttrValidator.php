<?php

/**
 * Validates the attributes of a token. Doesn't manage required attributes
 * very well. The only reason we factored this out was because RemoveForeignElements
 * also needed it besides ValidateAttributes.
 */
class HTMLPurifier_AttrValidator
{

    /**
     * Validates the attributes of a token, mutating it as necessary.
     * that has valid tokens
     * @param HTMLPurifier_Token $token Token to validate.
     * @param HTMLPurifier_Config $config Instance of HTMLPurifier_Config
     * @param HTMLPurifier_Context $context Instance of HTMLPurifier_Context
     */
    public function validateToken($token, $config, $context)
    {
        $definition = $config->getHTMLDefinition();
        $e =& $context->get('ErrorCollector', true);

        // initialize IDAccumulator if necessary
        $ok =& $context->get('IDAccumulator', true);
        if (!$ok) {
            $id_accumulator = HTMLPurifier_IDAccumulator::build($config, $context);
            $context->register('IDAccumulator', $id_accumulator);
        }

        // initialize CurrentToken if necessary
        $current_token =& $context->get('CurrentToken', true);
        if (!$current_token) {
            $context->register('CurrentToken', $token);
        }

        if (!$token instanceof HTMLPurifier_Token_Start &&
            !$token instanceof HTMLPurifier_Token_Empty
        ) {
            return;
        }

        // create alias to global definition array, see also $defs
        // DEFINITION CALL
        $d_defs = $definition->info_global_attr;

        // don't update token until the very end, to ensure an atomic update
        $attr = $token->attr;

        // do global transformations (pre)
        // nothing currently utilizes this
        foreach ($definition->info_attr_transform_pre as $transform) {
            $attr = $transform->transform($o = $attr, $config, $context);
            if ($e) {
                if ($attr != $o) {
                    $e->send(E_NOTICE, 'AttrValidator: Attributes transformed', $o, $attr);
                }
            }
        }

        // do local transformations only applicable to this element (pre)
        // ex. <p align="right"> to <p style="text-align:right;">
        foreach ($definition->info[$token->name]->attr_transform_pre as $transform) {
            $attr = $transform->transform($o = $attr, $config, $context);
            if ($e) {
                if ($attr != $o) {
                    $e->send(E_NOTICE, 'AttrValidator: Attributes transformed', $o, $attr);
                }
            }
        }

        // create alias to this element's attribute definition array, see
        // also $d_defs (global attribute definition array)
        // DEFINITION CALL
        $defs = $definition->info[$token->name]->attr;

        $attr_key = false;
        $context->register('CurrentAttr', $attr_key);

        // iterate through all the attribute keypairs
        // Watch out for name collisions: $key has previously been used
        foreach ($attr as $attr_key => $value) {

            // call the definition
            if (isset($defs[$attr_key])) {
                // there is a local definition defined
                if ($defs[$attr_key] === false) {
                    // We've explicitly been told not to allow this element.
                    // This is usually when there's a global definition
                    // that must be overridden.
                    // Theoretically speaking, we could have a
                    // AttrDef_DenyAll, but this is faster!
                    $result = false;
                } else {
                    // validate according to the element's definition
                    $result = $defs[$attr_key]->validate(
                        $value,
                        $config,
                        $context
                    );
                }
            } elseif (isset($d_defs[$attr_key])) {
                // there is a global definition defined, validate according
                // to the global definition
                $result = $d_defs[$attr_key]->validate(
                    $value,
                    $config,
                    $context
                );
            } else {
                // system never heard of the attribute? DELETE!
                $result = false;
            }

            // put the results into effect
            if ($result === false || $result === null) {
                // this is a generic error message that should replaced
                // with more specific ones when possible
                if ($e) {
                    $e->send(E_ERROR, 'AttrValidator: Attribute removed');
                }

                // remove the attribute
                unset($attr[$attr_key]);
            } elseif (is_string($result)) {
                // generally, if a substitution is happening, there
                // was some sort of implicit correction going on. We'll
                // delegate it to the attribute classes to say exactly what.

                // simple substitution
                $attr[$attr_key] = $result;
            } else {
                // nothing happens
            }

            // we'd also want slightly more complicated substitution
            // involving an array as the return value,
            // although we're not sure how colliding attributes would
            // resolve (certain ones would be completely overriden,
            // others would prepend themselves).
        }

        $context->destroy('CurrentAttr');

        // post transforms

        // global (error reporting untested)
        foreach ($definition->info_attr_transform_post as $transform) {
            $attr = $transform->transform($o = $attr, $config, $context);
            if ($e) {
                if ($attr != $o) {
                    $e->send(E_NOTICE, 'AttrValidator: Attributes transformed', $o, $attr);
                }
            }
        }

        // local (error reporting untested)
        foreach ($definition->info[$token->name]->attr_transform_post as $transform) {
            $attr = $transform->transform($o = $attr, $config, $context);
            if ($e) {
                if ($attr != $o) {
                    $e->send(E_NOTICE, 'AttrValidator: Attributes transformed', $o, $attr);
                }
            }
        }

        $token->attr = $attr;

        // destroy CurrentToken if we made it ourselves
        if (!$current_token) {
            $context->destroy('CurrentToken');
        }

    }


}

// vim: et sw=4 sts=4
