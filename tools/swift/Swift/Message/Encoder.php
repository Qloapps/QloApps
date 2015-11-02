<?php

/**
 * Swift Mailer Message Encoder
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Message
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_File");

/**
 * Encodes strings in a variety of formats and detects some encoding formats
 * @package Swift_Message
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Message_Encoder
{
  /**
   * A regular expression which matches valid e-mail addresses (including some unlikely ones)
   */
  const CHEAP_ADDRESS_RE = '(?#Start of dot-atom
    )[-!#\$%&\'\*\+\/=\?\^_`{}\|~0-9A-Za-z]+(?:\.[-!#\$%&\'\*\+\/=\?\^_`{}\|~0-9A-Za-z]+)*(?#
    End of dot-atom)(?:@(?#Start of domain)[-0-9A-Za-z]+(?:\.[-0-9A-Za-z]+)*(?#End of domain))?';
  /**
   * A singleton of this class
   * @var Swift_Message_Encoder
   */
  protected static $instance = null;
  /**
   * Retreive an instance of the encoder as a singleton.
   * New instances are never ever needed since it's monostatic.
   * @return Message_Encoder
   */
  public static function instance()
  {
    if (self::$instance === null)
    {
      self::$instance = new Swift_Message_Encoder();
    }
    return self::$instance;
  }
  /**
   * Break a string apart at every occurence of <add@ress> and return an array
   * This method does NOT remove any characters like a preg_split() would do.
   * Elements matching an address start with "a" followed by the numeric index
   * @param string The input string to separate
   * @return array
   */
  public function addressChunk($input)
  {
    $elements = 0;
    while (preg_match('/^(.*?)(<' . self::CHEAP_ADDRESS_RE . '>)/s', $input, $matches))
    {
      if (!empty($matches[1])) $ret[($elements++)] = $matches[1];
      $ret[('a' . ($elements++))] = $matches[2];
      $input = substr($input, strlen($matches[0]));
    }
    if ($input != "") $ret[($elements++)] = $input; //Whatever is left over
        
    return $ret;
  }
  /**
   * Break a string apart at every occurence of <xxxyyy> and return an array
   * This method does NOT remove any characters like a preg_split() would do.
   * Elements matching a quoted string start with "a" followed by the numeric index
   * @param string The input string to separate
   * @return array
   */
  public function quoteChunk($input)
  {
    $elements = 0;
    while (preg_match('/^(.*?)(<[\x20-\x3A\x3C-\x7E]*>)/s', $input, $matches))
    {
      if (!empty($matches[1])) $ret[($elements++)] = $matches[1];
      $ret[('a' . ($elements++))] = $matches[2];
      $input = substr($input, strlen($matches[0]));
    }
    if ($input != "") $ret[($elements++)] = $input; //Whatever is left over
        
    return $ret;
  }
  /**
   * Return the base64 encoded version of the string
   * @param string The input string to encode
   * @param int The maximum length of each line of output (inc CRLF)
   * @param int The maximum length of the first line in the output (for headers)
   * @param boolean Whether email addresses between < and > chars should be preserved or not
   * @param string The line ending
   * @return string
   */
  public function base64Encode($data, $chunk=76, $init_chunk=0, $headers=false, $le="\r\n")
  {
    $ret = "";
    $chunk -= 2;
    $chunk = $this->getHcf($chunk, 4);
    
    if ($init_chunk >= 2)
    {
      $init_chunk -= 2;
      $init_chunk = $this->getHcf($init_chunk, 4);
    }
    
    if ($headers) $data = $this->quoteChunk($data);
    else $data = array($data);
    
    foreach ($data as $key => $string)
    {
      $key = (string) $key;
      if ($key{0} == 'a') //This is an address
      {
        if ($init_chunk && $init_chunk < (strlen($string)+2)) $ret .= $le;
        $ret .= $le . $string;
      }
      else
      {
        $string = $this->rawBase64Encode($string);
        if ($init_chunk > 2)
        {
          $ret .= substr($string, 0, $init_chunk) . $le;
          $string = substr($string, $init_chunk);
        }
        elseif ($init_chunk) $ret .= $le;
        
        $ret .= trim(chunk_split($string, $chunk, $le)) . $le;
      }
      $init_chunk = 0;
    }
    
    return trim($ret);
  }
  /**
   * Return the base64 encoded version of a string with no breaks
   * @param The input string to encode
   * @return string
   */
  public function rawBase64Encode($string)
  {
    return $string = base64_encode($string);
  }
  /**
   * Return the base64 encoded version of a file
   * @param Swift_File The file input stream
   * @param int Max line length
   * @param string The line ending
   * @return Swift_Cache_OutputStream
   * @throws Swift_FileException If the file cannot be read
   */
  public function base64EncodeFile(Swift_File $file, $chunk=76, $le="\r\n")
  {
    Swift_ClassLoader::load("Swift_CacheFactory");
    $cache = Swift_CacheFactory::getCache();
    $chunk -= 2;
    $chunk = $this->getHcf($chunk, 4);
    $loop = false;
    //We have to read in multiples of 3 bytes but avoid doing such small chunks that it takes too long
    while (false !== $bytes = $file->read(8190))
    {
      if ($loop) $cache->write("b64", $le);
      $loop = true;
      $next = chunk_split($this->rawBase64Encode($bytes), $chunk, $le);
      $next = trim($next);
      $cache->write("b64", $next);
    }
    $file->reset();
    return $cache->getOutputStream("b64");
  }
  /**
   * Return the quoted printable version of the input string
   * @param string The input string to encode
   * @param int The maximum length of each line of output (inc CRLF)
   * @param int The maximum length of the first line in the output (for headers)
   * @param boolean Whether email addresses between < and > chars should be preserved or not
   * @param string The line ending
   * @return string
   */
  public function QPEncode($data, $chunk=76, $init_chunk=0, $headers=false, $le="\r\n")
  {
    $ret = "";
    if ($headers) $data = $this->quoteChunk($data);
    else $data = array($data);
    
    $trailing_spaces = chr(9) . chr(32);
    foreach ($data as $key => $string)
    {
      $key = (string) $key;
      if ($key{0} == 'a') //An address
      {
        if ($init_chunk && $init_chunk < (strlen($string)+3)) $ret .= "=";
        $ret .= $le . $string;
      }
      else
      {
        $lines = explode($le, $string);
        foreach ($lines as $n => $line)
          $lines[$n] = $this->rawQPEncode(rtrim($line, $trailing_spaces));
        $string = implode($le, $lines);
        if ($init_chunk > 3)
        {
          if (preg_match('/^.{1,'.($init_chunk-5).'}[^=]{2}(?!=[A-F0-9]{2})/', $string, $matches)
            || preg_match('/^.{1,'.($init_chunk-6).'}([^=]{0,3})?/', $string, $matches))
          {
            $ret .= $this->fixLE($matches[0] . "=", $le); //fixLE added 24/08/07
            $string = substr($string, strlen($matches[0]));
          }
        }
        elseif ($init_chunk) $ret .= "=";
        
        while (preg_match('/^.{1,'.($init_chunk-5).'}[^=]{2}(?!=[A-F0-9]{2})/', $string, $matches)
          || preg_match('/^.{1,'.($chunk-6).'}([^=]{0,3})?/', $string, $matches)
          || (strlen($string) > 0 && $matches = array($string)))
        {
          $ret .= $this->fixLE($le . $matches[0] . "=", $le); //fixLE added 24/08/07
          $string = substr($string, strlen($matches[0]));
        }
      }
      $init_chunk = 0;
    }
    
    if (substr($ret, -1) == "=") return trim(substr($ret, 0, -1));
    else return trim($ret);
  }
  /**
   * Return the QP encoded version of a string with no breaks
   * @param string The input to encode
   * @param boolean True if the data we're encoding is binary
   * @return string
   */
  public function rawQPEncode($string, $bin=false)
  {
    $ret = "";
    if (!$bin)
    {
      $string = str_replace(array("\r\n", "\r"), "\n", $string);
      $string = str_replace("\n", "\r\n", $string);
    }
    $len = strlen($string);
    for ($i = 0; $i < $len; $i++)
    {
      $val = ord($string{$i});
      //9, 32 = HT, SP; 10, 13 = CR, LF; 33-60 & 62-126 are ok
      // 63 = '?'; 95 = '_' and need encoding to go in the headers
      if ((!$bin && ($val == 32 || $val == 9 || $val == 10 || $val == 13))
        || ($val >= 33 && $val <= 60) || ($val >= 62 && $val <= 126)
        && $val != 63)
      {
        $ret .= $string{$i};
      }
      else
      {
        $ret .= sprintf("=%02X", $val);
      }
    }
    return $ret;
  }
  /**
   * Return a file as a quoted printable encoded string
   * @param Swift_File The file to encode
   * @param int Max line length
   * @param string The line ending
   * @return Swift_Cache_OutputStream
   * @throws Swift_FileException If the file cannot be read
   */
  public function QPEncodeFile(Swift_File $file, $chunk=76, $le="\r\n")
  {
    Swift_ClassLoader::load("Swift_CacheFactory");
    $cache = Swift_CacheFactory::getCache();
    while (false !== $bytes = $file->readln())
    {
      $next = $this->rawQPEncode($bytes, true);
      preg_match_all('/.{1,'.($chunk-6).'}([^=]{0,3})?/', $next, $next);
      if (count($next[0])) $cache->write("qp", $this->fixLE(implode("=" . $le, $next[0]), $le));
    }
    return $cache->getOutputStream("qp");
  }
  /**
   * Encode a string as 7bit ascii
   * @param string Input data to encode
   * @param int Max line length
   * @param string The line ending
   * @return string
   */
  public function encode7Bit($data, $chunk=76, $le="\r\n")
  {
    return $this->fixLE(wordwrap($data, $chunk-2, $le, 1), $le);
  }
  /**
   * Return a 7bit string from a file
   * @param Swift_File The file stream to read from
   * @param int The max line length
   * @param string The line ending
   * @return Swift_Cache_OutputStream
   * @throws Swift_FileException If the file cannot be read
   */
  public function encode7BitFile(Swift_File $file, $chunk=76, $le="\r\n")
  {
    Swift_ClassLoader::load("Swift_CacheFactory");
    $cache = Swift_CacheFactory::getCache();
    $ret = "";
    while (false !== $bytes = $file->read(8192)) $ret .= $bytes;
    $cache->write("7b", $this->fixLE(wordwrap($ret, $chunk-2, $le, 1), $le));
    return $cache->getOutputStream("7b");
  }
  /**
   * Return the 8bit encoded form of a string (unchanged there-abouts)
   * @param string Input data to encode
   * @param int Maximum line length
   * @param string The line ending
   * @return string
   */
  public function encode8Bit($data, $chunk=76, $le="\r\n")
  {
    return $this->fixLE(wordwrap($data, $chunk-2, $le, 1), $le);
  }
  /**
   * Return a 8bit string from a file
   * @param Swift_File The file stream to read from
   * @param int Max line length (including CRLF)
   * @param string The line ending
   * @return Swift_Cache_OutputStream
   * @throws Swift_FileException If the file cannot be read
   */
  public function encode8BitFile(Swift_File $file, $chunk=76, $le="\r\n")
  {
    Swift_ClassLoader::load("Swift_CacheFactory");
    $cache = Swift_CacheFactory::getCache();
    $ret = "";
    while (false !== $bytes = $file->read(8192)) $ret .= $bytes;
    $cache->write("8b", $this->fixLE(wordwrap($ret, $chunk-2, $le, 1), $le));
    return $cache->getOutputStream("8b");
  }
  /**
   * Keeps lines longer than 76 characters trimmed down to size
   * This currently does not convert other string encodings into 7bit
   * @param string The data to make safe for headers (defaults to RFC 2822 standards)
   * @param int maximum length of lines returned
   * @param int The maximum length of the first line
   * @param string The Line ending
   * @return string
   */
  public function header7BitEncode($data, $chunk=76, $init_chunk=0, $le="\r\n")
  {
    $data = $this->encode7BitPrintable($data);
    $ret = "";
    if ($init_chunk > 2)
    {
      $data_wrapped = wordwrap($data, $init_chunk, $le);
      $lines = explode($le, $data_wrapped);
      $first_line = array_shift($lines);
      $ret .= $first_line . $le;
      $data = preg_replace("~^[ \t]~D", "", substr($data, strlen($first_line)));
    }
    elseif ($init_chunk) $ret .= $le;
    $ret .= wordwrap($data, $chunk-2, $le);
    return trim($ret);
  }
  /**
   * Strip out any characters which are not in the ASCII 7bit printable range
   * @param string The string to clean
   * @return string
   */
  public function encode7BitPrintable($data)
  {
    return preg_replace('/[^\x20-\x7E]/', '', $data);
  }
  /**
   * Detect if a string contains multi-byte non-ascii chars that fall in the UTF-8 ranges
   * @param string Data to detect UTF-8 sequences in
   * @return boolean
   */
  public function isUTF8($data)
  {
    return preg_match('%(?:
    [\xC2-\xDF][\x80-\xBF]				# non-overlong 2-byte
    |\xE0[\xA0-\xBF][\x80-\xBF]			# excluding overlongs
    |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}	# straight 3-byte
    |\xED[\x80-\x9F][\x80-\xBF]			# excluding surrogates
    |\xF0[\x90-\xBF][\x80-\xBF]{2}		# planes 1-3
    |[\xF1-\xF3][\x80-\xBF]{3}			# planes 4-15
    |\xF4[\x80-\x8F][\x80-\xBF]{2}		# plane 16
    )+%xs', $data);
  }
  /**
   * This function checks for 7bit *printable* characters
   * which excludes \r \n \t etc and so, is safe for use in mail headers
   * Actual permitted chars [\ !"#\$%&'\(\)\*\+,-\.\/0123456789:;<=>\?@ABCDEFGHIJKLMNOPQRSTUVWXYZ\[\\\]\^_`abcdefghijklmnopqrstuvwxyz{\|}~]
   * Ranges \x00-\x1F are printer control sequences
   * \x7F is the ascii delete character
   * @param string Data to check against
   * @return boolean
   */
  public function is7BitPrintable($data)
  {
    return (!preg_match('/[^\x20-\x7E]/', $data));
  }
  /**
   * Check that a string does not contain any evil characters for headers.
   * @param string The string to check
   * @return boolean
   */
  public function isHeaderSafe($data)
  {
    return ($this->is7BitPrintable($data) && strpos($data, ";") === false);
  }
  /**
   * If the characters fall exclusively in the 7bit ascii range, return true
   * @param string Input to check
   * @return boolean
   */
  public function is7BitAscii($data)
  {
    return (!preg_match('/[^\x01-\x7F]/', $data));
  }
  /**
   * Encode a string for RFC 2047 compatability (url-encode)
   * @param string The input for encoding
   * @param string The charset used
   * @param string The language used
   * @param int The maximum line length
   * @param int The maximum length of the first line
   * @param string The line ending
   * @return string
   */
  public function rfc2047Encode($str, $charset="iso-8859-1", $language="en-us", $chunk=76, $le="\r\n")
  {
    $lang_spec = "";
    if (!$this->is7BitPrintable($str))
    {
      $lang_spec = $charset . "'" . $language . "'";
      $str = $lang_spec . str_replace("+", "%20", urlencode($str));
    }
    preg_match_all('~.{1,'.($chunk-6).'}([^%]{0,3})~', $str, $matches);
    if (count($matches[0])) return implode($le, $matches[0]);
  }
  /**
   * Fixes line endings to be whatever is specified by the user
   * SMTP requires the CRLF be used, but using sendmail in -t mode uses LF
   * This method also escapes dots on a start of line to avoid injection
   * @param string The data to fix
   * @return string
   */
  protected function fixLE($data, $le)
  {
    $data = str_replace(array("\r\n", "\r"), "\n", $data);
    if ($le != "\n") $data = str_replace("\n", $le, $data);
    return $data = str_replace($le . ".", $le . "..", $data);
  }
  protected function getHcf($value, $factor)
  {
    return ($value - ($value % $factor));
  }
}
