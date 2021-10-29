<?php

/**
 * XHTML 1.1 Forms module, defines all form-related elements found in HTML 4.
 */
class HTMLPurifier_HTMLModule_Forms extends HTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'Forms';

    /**
     * @type bool
     */
    public $safe = false;

    /**
     * @type array
     */
    public $content_sets = array(
        'Block' => 'Form',
        'Inline' => 'Formctrl',
    );

    /**
     * @param HTMLPurifier_Config $config
     */
    public function setup($config)
    {
        if ($config->get('HTML.Forms')) {
            $this->safe = true;
        }

        $form = $this->addElement(
            'form',
            'Form',
            'Required: Heading | List | Block | fieldset',
            'Common',
            array(
                'accept' => 'ContentTypes',
                'accept-charset' => 'Charsets',
                'action*' => 'URI',
                'method' => 'Enum#get,post',
                // really ContentType, but these two are the only ones used today
                'enctype' => 'Enum#application/x-www-form-urlencoded,multipart/form-data',
            )
        );
        $form->excludes = array('form' => true);

        $input = $this->addElement(
            'input',
            'Formctrl',
            'Empty',
            'Common',
            array(
                'accept' => 'ContentTypes',
                'accesskey' => 'Character',
                'alt' => 'Text',
                'checked' => 'Bool#checked',
                'disabled' => 'Bool#disabled',
                'maxlength' => 'Number',
                'name' => 'CDATA',
                'readonly' => 'Bool#readonly',
                'size' => 'Number',
                'src' => 'URI#embedded',
                'tabindex' => 'Number',
                'type' => 'Enum#text,password,checkbox,button,radio,submit,reset,file,hidden,image',
                'value' => 'CDATA',
            )
        );
        $input->attr_transform_post[] = new HTMLPurifier_AttrTransform_Input();

        $this->addElement(
            'select',
            'Formctrl',
            'Required: optgroup | option',
            'Common',
            array(
                'disabled' => 'Bool#disabled',
                'multiple' => 'Bool#multiple',
                'name' => 'CDATA',
                'size' => 'Number',
                'tabindex' => 'Number',
            )
        );

        $this->addElement(
            'option',
            false,
            'Optional: #PCDATA',
            'Common',
            array(
                'disabled' => 'Bool#disabled',
                'label' => 'Text',
                'selected' => 'Bool#selected',
                'value' => 'CDATA',
            )
        );
        // It's illegal for there to be more than one selected, but not
        // be multiple. Also, no selected means undefined behavior. This might
        // be difficult to implement; perhaps an injector, or a context variable.

        $textarea = $this->addElement(
            'textarea',
            'Formctrl',
            'Optional: #PCDATA',
            'Common',
            array(
                'accesskey' => 'Character',
                'cols*' => 'Number',
                'disabled' => 'Bool#disabled',
                'name' => 'CDATA',
                'readonly' => 'Bool#readonly',
                'rows*' => 'Number',
                'tabindex' => 'Number',
            )
        );
        $textarea->attr_transform_pre[] = new HTMLPurifier_AttrTransform_Textarea();

        $button = $this->addElement(
            'button',
            'Formctrl',
            'Optional: #PCDATA | Heading | List | Block | Inline',
            'Common',
            array(
                'accesskey' => 'Character',
                'disabled' => 'Bool#disabled',
                'name' => 'CDATA',
                'tabindex' => 'Number',
                'type' => 'Enum#button,submit,reset',
                'value' => 'CDATA',
            )
        );

        // For exclusions, ideally we'd specify content sets, not literal elements
        $button->excludes = $this->makeLookup(
            'form',
            'fieldset', // Form
            'input',
            'select',
            'textarea',
            'label',
            'button', // Formctrl
            'a', // as per HTML 4.01 spec, this is omitted by modularization
            'isindex',
            'iframe' // legacy items
        );

        // Extra exclusion: img usemap="" is not permitted within this element.
        // We'll omit this for now, since we don't have any good way of
        // indicating it yet.

        // This is HIGHLY user-unfriendly; we need a custom child-def for this
        $this->addElement('fieldset', 'Form', 'Custom: (#WS?,legend,(Flow|#PCDATA)*)', 'Common');

        $label = $this->addElement(
            'label',
            'Formctrl',
            'Optional: #PCDATA | Inline',
            'Common',
            array(
                'accesskey' => 'Character',
                // 'for' => 'IDREF', // IDREF not implemented, cannot allow
            )
        );
        $label->excludes = array('label' => true);

        $this->addElement(
            'legend',
            false,
            'Optional: #PCDATA | Inline',
            'Common',
            array(
                'accesskey' => 'Character',
            )
        );

        $this->addElement(
            'optgroup',
            false,
            'Required: option',
            'Common',
            array(
                'disabled' => 'Bool#disabled',
                'label*' => 'Text',
            )
        );
        // Don't forget an injector for <isindex>. This one's a little complex
        // because it maps to multiple elements.
    }
}

// vim: et sw=4 sts=4
