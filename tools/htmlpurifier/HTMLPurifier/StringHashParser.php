<?php

/**
 * Parses string hash files. File format is as such:
 *
 *      DefaultKeyValue
 *      KEY: Value
 *      KEY2: Value2
 *      --MULTILINE-KEY--
 *      Multiline
 *      value.
 *
 * Which would output something similar to:
 *
 *      array(
 *          'ID' => 'DefaultKeyValue',
 *          'KEY' => 'Value',
 *          'KEY2' => 'Value2',
 *          'MULTILINE-KEY' => "Multiline\nvalue.\n",
 *      )
 *
 * We use this as an easy to use file-format for configuration schema
 * files, but the class itself is usage agnostic.
 *
 * You can use ---- to forcibly terminate parsing of a single string-hash;
 * this marker is used in multi string-hashes to delimit boundaries.
 */
class HTMLPurifier_StringHashParser
{

    /**
     * @type string
     */
    public $default = 'ID';

    /**
     * Parses a file that contains a single string-hash.
     * @param string $file
     * @return array
     */
    public function parseFile($file)
    {
        if (!file_exists($file)) {
            return false;
        }
        $fh = fopen($file, 'r');
        if (!$fh) {
            return false;
        }
        $ret = $this->parseHandle($fh);
        fclose($fh);
        return $ret;
    }

    /**
     * Parses a file that contains multiple string-hashes delimited by '----'
     * @param string $file
     * @return array
     */
    public function parseMultiFile($file)
    {
        if (!file_exists($file)) {
            return false;
        }
        $ret = array();
        $fh = fopen($file, 'r');
        if (!$fh) {
            return false;
        }
        while (!feof($fh)) {
            $ret[] = $this->parseHandle($fh);
        }
        fclose($fh);
        return $ret;
    }

    /**
     * Internal parser that acepts a file handle.
     * @note While it's possible to simulate in-memory parsing by using
     *       custom stream wrappers, if such a use-case arises we should
     *       factor out the file handle into its own class.
     * @param resource $fh File handle with pointer at start of valid string-hash
     *            block.
     * @return array
     */
    protected function parseHandle($fh)
    {
        $state   = false;
        $single  = false;
        $ret     = array();
        do {
            $line = fgets($fh);
            if ($line === false) {
                break;
            }
            $line = rtrim($line, "\n\r");
            if (!$state && $line === '') {
                continue;
            }
            if ($line === '----') {
                break;
            }
            if (strncmp('--#', $line, 3) === 0) {
                // Comment
                continue;
            } elseif (strncmp('--', $line, 2) === 0) {
                // Multiline declaration
                $state = trim($line, '- ');
                if (!isset($ret[$state])) {
                    $ret[$state] = '';
                }
                continue;
            } elseif (!$state) {
                $single = true;
                if (strpos($line, ':') !== false) {
                    // Single-line declaration
                    list($state, $line) = explode(':', $line, 2);
                    $line = trim($line);
                } else {
                    // Use default declaration
                    $state  = $this->default;
                }
            }
            if ($single) {
                $ret[$state] = $line;
                $single = false;
                $state  = false;
            } else {
                $ret[$state] .= "$line\n";
            }
        } while (!feof($fh));
        return $ret;
    }
}

// vim: et sw=4 sts=4
