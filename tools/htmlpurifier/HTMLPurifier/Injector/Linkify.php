<?php

/**
 * Injector that converts http, https and ftp text URLs to actual links.
 */
class HTMLPurifier_Injector_Linkify extends HTMLPurifier_Injector
{
    /**
     * @type string
     */
    public $name = 'Linkify';

    /**
     * @type array
     */
    public $needed = array('a' => array('href'));

    /**
     * @param HTMLPurifier_Token $token
     */
    public function handleText(&$token)
    {
        if (!$this->allowsElement('a')) {
            return;
        }

        if (strpos($token->data, '://') === false) {
            // our really quick heuristic failed, abort
            // this may not work so well if we want to match things like
            // "google.com", but then again, most people don't
            return;
        }

        // there is/are URL(s). Let's split the string.
        // We use this regex:
        // https://gist.github.com/gruber/249502
        // but with @cscott's backtracking fix and also
        // the Unicode characters un-Unicodified.
        $bits = preg_split(
            '/\\b((?:[a-z][\\w\\-]+:(?:\\/{1,3}|[a-z0-9%])|www\\d{0,3}[.]|[a-z0-9.\\-]+[.][a-z]{2,4}\\/)(?:[^\\s()<>]|\\((?:[^\\s()<>]|(?:\\([^\\s()<>]+\\)))*\\))+(?:\\((?:[^\\s()<>]|(?:\\([^\\s()<>]+\\)))*\\)|[^\\s`!()\\[\\]{};:\'".,<>?\x{00ab}\x{00bb}\x{201c}\x{201d}\x{2018}\x{2019}]))/iu',
            $token->data, -1, PREG_SPLIT_DELIM_CAPTURE);

        if ($bits === false) {
            return;
        }

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
                $token[] = new HTMLPurifier_Token_Start('a', array('href' => $bits[$i]));
                $token[] = new HTMLPurifier_Token_Text($bits[$i]);
                $token[] = new HTMLPurifier_Token_End('a');
            }
        }
    }
}

// vim: et sw=4 sts=4
