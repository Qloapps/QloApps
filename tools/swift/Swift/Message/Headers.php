<?php

/**
 * Swift Mailer MIME Library Headers component
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Message
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";

/**
 * Contains and constructs the headers for a MIME document
 * @package Swift_Message
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Message_Headers
{
  /**
   * Headers which may contain email addresses, and therefore should take notice when encoding
   * @var array headers
   */
  protected $emailContainingHeaders = array(
    "To", "From", "Reply-To", "Cc", "Bcc", "Return-Path", "Sender");
  /**
   * The encoding format used for the body of the document
   * @var string format
   */
  protected $encoding = "B";
  /**
   * The charset used in the headers
   * @var string
   */
  protected $charset = false;
  /**
   * A collection of headers
   * @var array headers
   */
  protected $headers = array();
  /**
   * A container of references to the headers
   * @var array
   */
  protected $lowerHeaders = array();
  /**
   * Attributes appended to headers
   * @var array
   */
  protected $attributes = array();
  /**
   * If QP or Base64 encoding should be forced
   * @var boolean
   */
  protected $forceEncoding = false;
  /**
   * The language used in the headers (doesn't really matter much)
   * @var string
   */
  protected $language = "en-us";
  /**
   * Cached, pre-built headers
   * @var string
   */
  protected $cached = array();
  /**
   * The line ending used in the headers
   * @var string
   */
  protected $LE = "\r\n";
  
  /**
   * Set the line ending character to use
   * @param string The line ending sequence
   * @return boolean
   */
  public function setLE($le)
  {
    if (in_array($le, array("\r", "\n", "\r\n")))
    {
      foreach (array_keys($this->cached) as $k) $this->cached[$k] = null;
      $this->LE = $le;
      return true;
    }
    else return false;
  }
  /**
   * Get the line ending sequence
   * @return string
   */
  public function getLE()
  {
    return $this->LE;
  }
  /**
   * Reset the cache state in these headers
   */
  public function uncacheAll()
  {
    foreach (array_keys($this->cached) as $k)
    {
      $this->cached[$k] = null;
    }
  }
  /**
   * Add a header or change an existing header value
   * @param string The header name, for example "From" or "Subject"
   * @param string The value to be inserted into the header.  This is safe from header injection.
   */
  public function set($name, $value)
  {
    $lname = strtolower($name);
    if (!isset($this->lowerHeaders[$lname]))
    {
      $this->headers[$name] = null;
      $this->lowerHeaders[$lname] =& $this->headers[$name];
    }
    $this->cached[$lname] = null;
    Swift_ClassLoader::load("Swift_Message_Encoder");
    if (is_array($value))
    {
      foreach ($value as $v)
      {
        if (!$this->getCharset() && Swift_Message_Encoder::instance()->isUTF8($v))
        {
          $this->setCharset("utf-8");
          break;
        }
      }
    }
    elseif ($value !== null)
    {
      if (!$this->getCharset() && Swift_Message_Encoder::instance()->isUTF8($value))
      {
        $this->setCharset("utf-8");
      }
    }
    if (!is_array($value) && $value !== null) $this->lowerHeaders[$lname] = (string) $value;
    else $this->lowerHeaders[$lname] = $value;
  }
  /**
   * Get the value at a given header
   * @param string The name of the header, for example "From" or "Subject"
   * @return string
   * @throws Swift_Message_MimeException If no such header exists
   * @see hasHeader
   */
  public function get($name)
  {
    $lname = strtolower($name);
    if ($this->has($name))
    {
      return $this->lowerHeaders[$lname];
    }
  }
  /**
   * Remove a header from the list
   * @param string The name of the header
   */
  public function remove($name)
  {
    $lname = strtolower($name);
    if ($this->has($name))
    {
      unset($this->headers[$name]);
      unset($this->lowerHeaders[$lname]);
      unset($this->cached[$lname]);
      if (isset($this->attributes[$lname])) unset($this->attributes[$lname]);
    }
  }
  /**
   * Just fetch the array containing the headers
   * @return array
   */
  public function getList()
  {
    return $this->headers;
  }
  /**
   * Check if a header has been set or not
   * @param string The name of the header, for example "From" or "Subject"
   * @return boolean
   */
  public function has($name)
  {
    $lname = strtolower($name);
    return (array_key_exists($lname, $this->lowerHeaders) && $this->lowerHeaders[$lname] !== null);
  }
  /**
   * Set the language used in the headers to $lang (e.g. en-us, en-gb, sv etc)
   * @param string The language to use
   */
  public function setLanguage($lang)
  {
    $this->language = (string) $lang;
  }
  /**
   * Get the language used in the headers to $lang (e.g. en-us, en-gb, sv etc)
   * @return string
   */
  public function getLanguage()
  {
    return $this->language;
  }
  /**
   * Set the charset used in the headers
   * @param string The charset name
   */
  public function setCharset($charset)
  {
    $this->charset = (string) $charset;
  }
  /**
   * Get the current charset used
   * @return string
   */
  public function getCharset()
  {
    return $this->charset;
  }
  /**
   * Specify the encoding to use for the headers if characters outside the 7-bit-printable ascii range are found
   * This encoding will never be used if only 7-bit-printable characters are found in the headers.
   * Possible values are:
   *  - QP
   *  - Q
   *  - Quoted-Printable
   *  - B
   *  - Base64
   * NOTE: Q, QP, Quoted-Printable are all the same; as are B and Base64
   * @param string The encoding format to use
   * @return boolean
   */
  public function setEncoding($encoding)
  {
    switch (strtolower($encoding))
    {
      case "qp": case "q": case "quoted-printable":
      $this->encoding = "Q";
      return true;
      case "base64": case "b":
      $this->encoding = "B";
      return true;
      default: return false;
    }
  }
  /**
   * Get the encoding format used in this document
   * @return string
   */
  public function getEncoding()
  {
    return $this->encoding;
  }
  /**
   * Turn on or off forced header encoding
   * @param boolean On/Off
   */
  public function forceEncoding($force=true)
  {
    $this->forceEncoding = (boolean) $force;
  }
  /**
   * Set an attribute in a major header
   * For example $headers->setAttribute("Content-Type", "format", "flowed")
   * @param string The main header these values exist in
   * @param string The name for this value
   * @param string The value to set
   * @throws Swift_Message_MimeException If no such header exists
   */
  public function setAttribute($header, $name, $value)
  {
    $name = strtolower($name);
    $lheader = strtolower($header);
    $this->cached[$lheader] = null;
    if (!$this->has($header))
    {
      throw new Swift_Message_MimeException(
      "Cannot set attribute '" . $name . "' for header '" . $header . "' as the header does not exist. " .
      "Consider using Swift_Message_Headers-&gt;has() to check.");
    }
    else
    {
      Swift_ClassLoader::load("Swift_Message_Encoder");
      if (!$this->getCharset() && Swift_Message_Encoder::instance()->isUTF8($value)) $this->setCharset("utf-8");
      if (!isset($this->attributes[$lheader])) $this->attributes[$lheader] = array();
      if ($value !== null) $this->attributes[$lheader][$name] = (string) $value;
      else $this->attributes[$lheader][$name] = $value;
    }
  }
  /**
   * Check if a header has a given attribute applied to it
   * @param string The name of the main header
   * @param string The name of the attribute
   * @return boolean
   */
  public function hasAttribute($header, $name)
  {
    $name = strtolower($name);
    $lheader = strtolower($header);
    if (!$this->has($header))
    {
      return false;
    }
    else
    {
      return (isset($this->attributes[$lheader]) && isset($this->attributes[$lheader][$name]) && ($this->attributes[$lheader][$name] !== null));
    }
  }
  /**
   * Get the value for a given attribute on a given header
   * @param string The name of the main header
   * @param string The name of the attribute
   * @return string
   * @throws Swift_Message_MimeException If no header is set
   */
  public function getAttribute($header, $name)
  {
    if (!$this->has($header))
    {
      throw new Swift_Message_MimeException(
      "Cannot locate attribute '" . $name . "' for header '" . $header . "' as the header does not exist. " .
      "Consider using Swift_Message_Headers-&gt;has() to check.");
    }
    
    $name = strtolower($name);
    $lheader = strtolower($header);
    
    if ($this->hasAttribute($header, $name))
    {
      return $this->attributes[$lheader][$name];
    }
  }
  /**
   * Remove an attribute from a header
   * @param string The name of the header to remove the attribute from
   * @param string The name of the attribute to remove
   */
  public function removeAttribute($header, $name)
  {
    $name = strtolower($name);
    $lheader = strtolower($header);
    if ($this->has($header))
    {
      unset($this->attributes[$lheader][$name]);
    }
  }
  /**
   * Get a list of all the attributes in the given header.
   * @param string The name of the header
   * @return array
   */
  public function listAttributes($header)
  {
    $header = strtolower($header);
    if (array_key_exists($header, $this->attributes))
    {
      return $this->attributes[$header];
    }
    else return array();
  }
  /**
   * Get the header in it's compliant, encoded form
   * @param string The name of the header
   * @return string
   * @throws Swift_Message_MimeException If the header doesn't exist
   */
  public function getEncoded($name)
  {
    if (!$this->getCharset()) $this->setCharset("iso-8859-1");
    Swift_ClassLoader::load("Swift_Message_Encoder");
    //I'll try as best I can to walk through this...
    
    $lname = strtolower($name);
    
    if ($this->cached[$lname] !== null) return $this->cached[$lname];
    
    $value = $this->get($name);
    
    $is_email = in_array($name, $this->emailContainingHeaders);
    
    $encoded_value = (array) $value; //Turn strings into arrays (just to make the following logic simpler)
    
    //Look at each value in this header
    // There will only be 1 value if it was a string to begin with, and usually only address lists will be multiple
    foreach ($encoded_value as $key => $row)
    {
      $spec = ""; //The bit which specifies the encoding of the header (if any)
      $end = ""; //The end delimiter for an encoded header
      
      //If the header is 7-bit printable it's at no risk of injection
      if (Swift_Message_Encoder::instance()->isHeaderSafe($row) && !$this->forceEncoding)
      {
        //Keeps the total line length at less than 76 chars, taking into account the Header name length
        $encoded_value[$key] = Swift_Message_Encoder::instance()->header7BitEncode(
          $row, 72, ($key > 0 ? 0 : (75-(strlen($name)+5))), $this->LE);
      }
      elseif ($this->encoding == "Q") //QP encode required
      {
        $spec = "=?" . $this->getCharset() . "?Q?"; //e.g. =?iso-8859-1?Q?
        $end = "?=";
        //Calculate the length of, for example: "From: =?iso-8859-1?Q??="
        $used_length = strlen($name) + 2 + strlen($spec) + 2;
        
        //Encode to QP, excluding the specification for now but keeping the lines short enough to be compliant
        $encoded_value[$key] = str_replace(" ", "_", Swift_Message_Encoder::instance()->QPEncode(
          $row, (75-(strlen($spec)+6)), ($key > 0 ? 0 : (75-$used_length)), true, $this->LE));
        
      }
      elseif ($this->encoding == "B") //Need to Base64 encode
      {
        //See the comments in the elseif() above since the logic is the same (refactor?)
        $spec = "=?" . $this->getCharset() . "?B?";
        $end = "?=";
        $used_length = strlen($name) + 2 + strlen($spec) + 2;
        $encoded_value[$key] = Swift_Message_Encoder::instance()->base64Encode(
          $row, (75-(strlen($spec)+5)), ($key > 0 ? 0 : (76-($used_length+3))), true, $this->LE);
      }
      
      if (false !== $p = strpos($encoded_value[$key], $this->LE))
      {
        $encoded_value[$key] = preg_replace_callback("/<([^>]+)>/", array($this, 'prestaShopReplace'), $encoded_value[$key]);
      }
      
      //Turn our header into an array of lines ready for wrapping around the encoding specification
      $lines = explode($this->LE, $encoded_value[$key]);
      
      for ($i = 0, $len = count($lines); $i < $len; $i++)
      {
        //Don't allow commas in address fields without quotes unless they're encoded
        if (empty($spec) && $is_email && (false !== $p = strpos($lines[$i], ",")))
        {
          $s = strpos($lines[$i], " <");
          $e = strpos($lines[$i], ">");
          if ($s < $e)
          {
            $addr = substr($lines[$i], $s);
            $lines[$i] = "\"" . substr($lines[$i], 0, $s) . "\"" . $addr;
          }
          else
          {
            $lines[$i] = "\"" . $lines[$i] . "\"";
          }
        }
        
        if ($this->encoding == "Q") $lines[$i] = rtrim($lines[$i], "=");
        
        if ($lines[$i] == "" && $i > 0)
        {
          unset($lines[$i]); //Empty line, we'd rather not have these in the headers thank you!
          continue;
        }
        if ($i > 0)
        {
          //Don't stick the specification part around the line if it's an address
          if (substr($lines[$i], 0, 1) == '<' && substr($lines[$i], -1) == '>') $lines[$i] = " " . $lines[$i];
          else $lines[$i] = " " . $spec . $lines[$i] . $end;
        }
        else
        {
          if (substr($lines[$i], 0, 1) != '<' || substr($lines[$i], -1) != '>') $lines[$i] = $spec . $lines[$i] . $end;
        }
      }
      //Build back into a string, now includes the specification
      $encoded_value[$key] = implode($this->LE, $lines);
      $lines = null;
    }
    
    //If there are multiple values in this header, put them on separate lines, cleared by commas
    $this->cached[$lname] = implode("," . $this->LE . " ", $encoded_value);
    
    //Append attributes if there are any
    if (!empty($this->attributes[$lname])) $this->cached[$lname] .= $this->buildAttributes($this->cached[$lname], $lname);
    
    return $this->cached[$lname];
  }
  /**
   * Build the list of attributes for appending to the given header
   * This is RFC 2231 & 2047 compliant.
   * A HUGE thanks to Joaquim Homrighausen for heaps of help, advice
   * and testing to get this working rock solid.
   * @param string The header built without attributes
   * @param string The lowercase name of the header
   * @return string
   * @throws Swift_Message_MimeException If no such header exists or there are no attributes
   */
  protected function buildAttributes($header_line, $header_name)
  {
    Swift_ClassLoader::load("Swift_Message_Encoder");
    $lines = explode($this->LE, $header_line);
    $used_len = strlen($lines[count($lines)-1]);
    $lines= null;
    $ret = "";
    foreach ($this->attributes[$header_name] as $attribute => $att_value)
    {
      if ($att_value === null) continue;
      // 70 to account for LWSP, CRLF, quotes and a semi-colon
      // + length of attribute
      // + 4 for a 2 digit number and 2 asterisks
      $avail_len = 70 - (strlen($attribute) + 4);
      $encoded = Swift_Message_Encoder::instance()->rfc2047Encode($att_value, $this->charset, $this->language, $avail_len, $this->LE);
      $lines = explode($this->LE, $encoded);
      foreach ($lines as $i => $line)
      {
        //Add quotes if needed (RFC 2045)
        if (preg_match("~[\\s\";,<>\\(\\)@:\\\\/\\[\\]\\?=]~", $line)) $lines[$i] = '"' . $line . '"';
      }
      $encoded = implode($this->LE, $lines);
      
      //If we can fit this entire attribute onto the same line as the header then do it!
      if ((strlen($encoded) + $used_len + strlen($attribute) + 4) < 74)
      {
        if (strpos($encoded, "'") !== false) $attribute .= "*";
        $append = "; " . $attribute . "=" . $encoded;
        $ret .= $append;
        $used_len += strlen($append);
      }
      else //... otherwise list of underneath
      {
        $ret .= ";";
        if (count($lines) > 1)
        {
          $loop = false;
          $add_asterisk = false;
          foreach ($lines as $i => $line)
          {
            $att_copy = $attribute; //Because it's multi-line it needs asterisks with decimal indices
            $att_copy .= "*" . $i;
            if ($add_asterisk || strpos($encoded, "'") !== false)
            {
              $att_copy .= "*"; //And if it's got a ' then it needs another asterisk
              $add_asterisk = true;
            }
            $append = "";
            if ($loop) $append .= ";";
            $append .= $this->LE . " " . $att_copy . "=" . $line;
            $ret .= $append;
            $used_len = strlen($append)+1;
            $loop = true;
          }
        }
        else
        {
          if (strpos($encoded, "'") !== false) $attribute .= "*";
          $append = $this->LE . " " . $attribute . "=" . $encoded;
          $used_len = strlen($append)+1;
          $ret .= $append;
        }
      }
      $lines= null;
    }
    return $ret;
  }
  
  public function prestaShopReplace($matches)
  {
		return str_replace("' . $this->LE . '", "", "<$matches[1]>");
  }
  
  /**
   * Compile the list of headers which have been set and return an ascii string
   * The return value should always be 7-bit ascii and will have been cleaned for header injection
   * If this looks complicated it's probably because it is!!  Keeping everything compliant is not easy.
   * This is RFC 2822 compliant
   * @return string
   */
  public function build()
  {
    $ret = "";
    foreach ($this->headers as $name => $value) //Look at each header
    {
      if ($value === null) continue;
      $ret .= ltrim($name, ".") . ": " . $this->getEncoded($name) . $this->LE;
    }
    return trim($ret);
  }
}
