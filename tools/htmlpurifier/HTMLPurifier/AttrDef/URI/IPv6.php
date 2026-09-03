<?php

/**
 * Validates an IPv6 address.
 * @author Feyd @ forums.devnetwork.net (public domain)
 * @note This function requires brackets to have been removed from address
 *       in URI.
 */
class HTMLPurifier_AttrDef_URI_IPv6 extends HTMLPurifier_AttrDef_URI_IPv4
{

    /**
     * @param string $aIP
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($aIP, $config, $context)
    {
        if (!$this->ip4) {
            $this->_loadRegex();
        }

        $original = $aIP;

        $hex = '[0-9a-fA-F]';
        $blk = '(?:' . $hex . '{1,4})';
        $pre = '(?:/(?:12[0-8]|1[0-1][0-9]|[1-9][0-9]|[0-9]))'; // /0 - /128

        //      prefix check
        if (strpos($aIP, '/') !== false) {
            if (preg_match('#' . $pre . '$#s', $aIP, $find)) {
                $aIP = substr($aIP, 0, 0 - strlen($find[0]));
                unset($find);
            } else {
                return false;
            }
        }

        //      IPv4-compatibility check
        if (preg_match('#(?<=:' . ')' . $this->ip4 . '$#s', $aIP, $find)) {
            $aIP = substr($aIP, 0, 0 - strlen($find[0]));
            $ip = explode('.', $find[0]);
            $ip = array_map('dechex', $ip);
            $aIP .= $ip[0] . $ip[1] . ':' . $ip[2] . $ip[3];
            unset($find, $ip);
        }

        //      compression check
        $aIP = explode('::', $aIP);
        $c = count($aIP);
        if ($c > 2) {
            return false;
        } elseif ($c == 2) {
            list($first, $second) = $aIP;
            $first = explode(':', $first);
            $second = explode(':', $second);

            if (count($first) + count($second) > 8) {
                return false;
            }

            while (count($first) < 8) {
                array_push($first, '0');
            }

            array_splice($first, 8 - count($second), 8, $second);
            $aIP = $first;
            unset($first, $second);
        } else {
            $aIP = explode(':', $aIP[0]);
        }
        $c = count($aIP);

        if ($c != 8) {
            return false;
        }

        //      All the pieces should be 16-bit hex strings. Are they?
        foreach ($aIP as $piece) {
            if (!preg_match('#^[0-9a-fA-F]{4}$#s', sprintf('%04s', $piece))) {
                return false;
            }
        }
        return $original;
    }
}

// vim: et sw=4 sts=4
