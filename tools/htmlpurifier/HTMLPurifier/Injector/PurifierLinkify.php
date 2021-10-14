<?php

/**
 * Injector that converts configuration directive syntax %Namespace.Directive
 * to links
 */
class HTMLPurifier_Injector_PurifierLinkify extends HTMLPurifier_Injector
{
    /**
     * @type string
     */
    public $name = 'PurifierLinkify';

    /**
     * @type string
     */
    public $docURL;

    /**
     * @type array
     */
    public $needed = array('a' => array('href'));

    /**
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return string
     */
    public function prepare($config, $context)
    {
        $this->docURL = $config->get('AutoFormat.PurifierLinkify.DocURL');
        return parent::prepare($config, $context);
    }

    /**
     * @param HTMLPurifier_Token $token
     */
    public function handleText(&$token)
    {
        if (!$this->allowsElement('a')) {
            return;
        }
        if (strpos($token->data, '%') === false) {
            return;
        }

        $bits = preg_split('#%([a-z0-9]+\.[a-z0-9]+)#Si', $token->data, -1, PREG_SPLIT_DELIM_CAPTURE);
        $token = array();

        // $i = index
        // $c = count
        // $l = is link
        for ($i = 0, $c = count($bits), $l = false; $i < $c; $i++, $l = !$l) {
            if (!$l) {
                if ($bits[$i] === '') {
                    continue;
                }
                $token[] = new HTMLPurifier_Token_Text($bits[$i]);
            } else {
                $token[] = new HTMLPurifier_Token_Start(
                    'a',
                    array('href' => str_replace('%s', $bits[$i], $this->docURL))
                );
                $token[] = new HTMLPurifier_Token_Text('%' . $bits[$i]);
                $token[] = new HTMLPurifier_Token_End('a');
            }
        }
    }
}

// vim: et sw=4 sts=4
