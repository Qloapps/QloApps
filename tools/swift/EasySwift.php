<?php

/**
 * EasySwift: Swift Mailer Facade
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package EasySwift
 * @version 1.0.3
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/Swift/ClassLoader.php";
Swift_ClassLoader::load("Swift");
Swift_ClassLoader::load("Swift_Connection_SMTP");
Swift_ClassLoader::load("Swift_Connection_Sendmail");

//Some constants for backwards compatibility with v2 code
if (!defined("SWIFT_TLS")) define("SWIFT_TLS", Swift_Connection_SMTP::ENC_TLS);
if (!defined("SWIFT_SSL")) define("SWIFT_SSL", Swift_Connection_SMTP::ENC_SSL);
if (!defined("SWIFT_OPEN")) define("SWIFT_OPEN", Swift_Connection_SMTP::ENC_OFF);
if (!defined("SWIFT_SECURE_PORT")) define("SWIFT_SECURE_PORT", Swift_Connection_SMTP::PORT_SECURE);
if (!defined("SWIFT_DEFAULT_PORT")) define("SWIFT_DEFAULT_PORT", Swift_Connection_SMTP::PORT_DEFAULT);

/**
 * EasySwift: Facade for Swift Mailer Version 3.
 * Provides (most of) the API from older versions of Swift, wrapped around the new version 3 API.
 * Due to the popularity of the new API, EasySwift will not be around indefinitely.
 * @package EasySwift
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @deprecated
 */
class EasySwift
{
  /**
   * The instance of Swift this class wrappers
   * @var Swift
   */
  public $swift = null;
  /**
   * This value becomes set to true when Swift fails
   * @var boolean
   */
  public $failed = false;
  /**
   * The number of loaded plugins
   * @var int
   */
  protected $pluginCount = 0;
  /**
   * An instance of Swift_Message
   * @var Swift_Message
   */
  public $message = null;
  /**
   * An address list to send to (Cc, Bcc, To..)
   * @var Swift_RecipientList
   */
  public $recipients = null;
  /**
   * If all recipients should get the same copy of the message, including headers
   * This is already implied if any Cc or Bcc recipients are set
   * @var boolean
   */
  protected $exactCopy = false;
  /**
   * If EasySwift should get rid of the message and recipients once it's done sending
   * @var boolean
   */
  protected $autoFlush = true;
  /**
   * A list of the IDs of all parts added to the message
   * @var array
   */
  protected $partIds = array();
  /**
   * A list of all the IDs of the attachments add to the message
   * @var array
   */
  protected $attachmentIds = array();
  /**
   * The last response received from the server
   * @var string
   */
  public $lastResponse = "";
  /**
   * The 3 digit code in the last response received from the server
   * @var int
   */
  public $responseCode = 0;
  /**
   * The list of errors handled at runtime
   * @var array
   */
  public $errors = array();
  /**
   * The last error received
   * @var string
   */
  public $lastError = null;
  
  /**
   * Constructor
   * @param Swift_Connection The connection to use
   * @param string The domain name of this server (not the SMTP server)
   */
  public function __construct(Swift_Connection $connection, $domain=null)
  {
    try {
      $this->swift = new Swift($connection, $domain, Swift::ENABLE_LOGGING);
      Swift_ClassLoader::load("Swift_Plugin_EasySwiftResponseTracker");
      $this->swift->attachPlugin(new Swift_Plugin_EasySwiftResponseTracker($this), "_ResponseTracker");
    } catch (Swift_ConnectionException $e) {
      $this->failed = true;
      $this->setError("The connection failed to start.  An exception was thrown:<br />" . $e->getMessage());
    }
    $this->newMessage();
    $this->newRecipientList();
  }
  /**
   * Set an error message
   * @param string Error message
   */
  public function setError($msg)
  {
    $this->errors[] = ($this->lastError = $msg);
  }
  /**
   * Get the full list of errors
   * @return array
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Get the last error that occurred
   * @return string
   */
  public function getLastError()
  {
    return $this->lastError;
  }
  /**
   * Clear the current list of errors
   */
  public function flushErrors()
  {
    $this->errors = null;
    $this->errors = array();
  }
  /**
   * Turn automatic flsuhing on or off.
   * This in ON by deault.  It removes the message and all parts after sending.
   * @param boolean
   */
  public function autoFlush($flush=true)
  {
    $this->autoFlush = $flush;
  }
  /**
   * Set the maximum size of the log
   * @param int
   */
  public function setMaxLogSize($size)
  {
    $log = Swift_LogContainer::getLog();
    $log->setMaxSize($size);
  }
  /**
   * Turn logging on or off (saves memory)
   * @param boolean
   */
  public function useLogging($use=true)
  {
    $log = Swift_LogContainer::getLog();
    if ($use) $log->setLogLevel(Swift_Log::LOG_NETWORK);
    else $log->setLogLevel(Swift_Log::LOG_NOTHING);
  }
  /**
   * Enable line resizing (on 1000 by default)
   * @param int The number of characters allowed on a line
   */
  public function useAutoLineResizing($size=1000)
  {
    $this->message->setLineWrap($size);
  }
  /**
   * Dump the log contents
   * @deprecated
   */
  public function getTransactions()
  {
    return $this->dumpLog();
  }
  /**
   * Dump the contents of the log to the browser
   * The log contains some &lt; and &gt; characters so you may need to view source
   * Note that this method dumps data to the browser, it does NOT return anything.
   */
  public function dumpLog()
  {
    $log = Swift_LogContainer::getLog();
    $log->dump();
  }
  /**
   * This method should be called if you do not wish to send messages in batch mode (i.e. if all recipients should see each others' addresses)
   * @param boolean If this mode should be used
   */
  public function useExactCopy($bool=true)
  {
    $this->exactCopy = $bool;
  }
  /**
   * Reset the current message and start a fresh one
   */
  public function newMessage($msg=false)
  {
    if (!$msg) $msg = new Swift_Message();
    $this->message = $msg;
    $this->partIds = array();
    $this->attachmentIds = array();
  }
  /**
   * Clear out all message parts
   * @return boolean
   */
  public function flushParts()
  {
    $success = true;
    foreach ($this->partIds as $id)
    {
      try {
        $this->message->detach($id);
      } catch (Swift_Message_MimeException $e) {
        $success = false;
        $this->setError("A MIME part failed to detach due to the error:<br />" . $e->getMessage());
      }
    }
    $this->partIds = array();
    return $success;
  }
  /**
   * Clear out all attachments
   * @return boolean
   */
  public function flushAttachments()
  {
    $success = true;
    foreach ($this->attachmentIds as $id)
    {
      try {
        $this->message->detach($id);
      } catch (Swift_Message_MimeException $e) {
        $success = false;
        $this->setError("An attachment failed to detach due to the error:<br />" . $e->getMessage());
      }
    }
    $this->attachmentIds = array();
    return $success;
  }
  /**
   * Clear out all message headers
   * @deprecated
   */
  public function flushHeaders()
  {
    $this->newMessage();
  }
  /**
   * Reset the current list of recipients and start a new one
   */
  public function newRecipientList($list=false)
  {
    if (!$list) $list = new Swift_RecipientList();
    $this->recipients = $list;
  }
  /**
   * Check if Swift has failed or not
   * This facade stops processing if so
   * @return boolean
   */
  public function hasFailed()
  {
    return $this->failed;
  }
  /**
   * Check if the current connection is open or not
   * @return boolean
   */
  public function isConnected()
  {
    return (($this->swift !== null) && $this->swift->connection->isAlive());
  }
  /**
   * Connect to the MTA if not already connected
   */
  public function connect()
  {
    if (!$this->isConnected())
    {
      try {
        $this->swift->connect();
        return true;
      } catch (Swift_ConnectionException $e) {
        $this->failed = true;
        $this->setError("Swift failed to run the connection process:<br />" . $e->getMessage());
      }
    }
    return false;
  }
  /**
   * Perform the SMTP greeting process (don't do this unless you understand why you're doing it)
   */
  public function handshake()
  {
    $this->swift->handshake();
  }
  /**
   * Close the connection to the MTA
   * @return boolean
   */
  public function close()
  {
    if ($this->isConnected())
    {
      try {
        $this->swift->disconnect();
        return true;
      } catch (Swift_ConnectionException $e) {
        $this->setError("Disconnect failed:<br />" . $e->getMessage());
      }
    }
    return false;
  }
  /**
   * Send a command to Swift and get a response
   * @param string The command to send (leave of CRLF)
   * @return string
   */
  public function command($command)
  {
    if (substr($command, -2) == "\r\n") $command = substr($command, 0, -2);
    
    try {
      $rs = $this->swift->command($command);
      return $rs->getString();
    } catch (Swift_ConnectionException $e) {
      $this->setError("Command failed:<br />" . $e->getMessage());
      return false;
    }
  }
  /**
   * Add a new plugin to respond to events
   * @param Swift_Events_Listener The plugin to load
   * @param string The ID to identify the plugin by if needed
   * @return string The ID of the plugin
   */
  public function loadPlugin(Swift_Events_Listener $plugin, $name=null)
  {
    $this->pluginCount++;
    if (!$name) $name = "p" . $this->pluginCount;
    $this->swift->attachPlugin($plugin, $name);
    return $name;
  }
  /**
   * Get a reference to the plugin identified by $name
   * @param string the ID of the plugin
   * @return Swift_Events_Listener
   */
  public function getPlugin($name)
  {
    try {
      $plugin = $this->swift->getPlugin($name);
      return $plugin;
    } catch (Exception $e) {
      return null;
    }
  }
  /**
   * Remove the plugin identified by $name
   * @param string The ID of the plugin
   * @return boolean
   */
  public function removePlugin($name)
  {
    try {
      $this->swift->removePlugin($name);
      return true;
    } catch (Exception $e) {
      return false;
    }
  }
  /**
   * Load in a new authentication mechanism for SMTP
   * This needn't be called since Swift will locate any available in Swift/Authenticator/*.php
   * @param Swift_Authenticator The authentication mechanism to load
   * @throws Exception If the wrong connection is used
   */
  public function loadAuthenticator(Swift_Authenticator $auth)
  {
    if (method_exists($this->swift->connection, "attachAuthenticator"))
    {
      $this->swift->connection->attachAuthenticator($auth);
    }
    else throw new Exception("SMTP authentication cannot be used with connection class '" . get_class($this->connection) . "'. Swift_Connection_SMTP is needed");
  }
  /**
   * Authenticate with SMTP authentication
   * @param string The SMTP username
   * @param string The SMTP password
   * @return boolean
   * @throws Exception If the wrong connection is used
   */
  public function authenticate($username, $password)
  {
    if (method_exists($this->swift->connection, "runAuthenticators"))
    {
      try {
        $this->swift->connection->runAuthenticators($username, $password, $this->swift);
        return true;
      } catch (Swift_ConnectionException $e) {
        $this->setError("Authentication failed:<br />" . $e->getMessage());
        return false;
      }
    }
    else throw new Exception("SMTP authentication cannot be used with connection class '" . get_class($this->connection) . "'. Swift_Connection_SMTP is needed");
  }
  /**
   * Turn a string representation of an email address into a Swift_Address object
   * @paramm string The email address
   * @return Swift_Address
   */
  public function stringToAddress($string)
  {
    $name = null;
    $address = null;
    // Foo Bar <foo@bar>
    // or: "Foo Bar" <foo@bar>
    // or: <foo@bar>
    Swift_ClassLoader::load("Swift_Message_Encoder");
    if (preg_match("/^\\s*(\"?)(.*?)\\1 *<(" . Swift_Message_Encoder::CHEAP_ADDRESS_RE . ")>\\s*\$/", $string, $matches))
    {
      if (!empty($matches[2])) $name = $matches[2];
      $address = $matches[3];
    }
    elseif (preg_match("/^\\s*" . Swift_Message_Encoder::CHEAP_ADDRESS_RE . "\\s*\$/", $string))
    {
      $address = trim($string);
    }
    else return false;
    
    $swift_address = new Swift_Address($address, $name);
    return $swift_address;
  }
  /**
   * Set the encoding used in the message header
   * The encoding can be one of Q (quoted-printable) or B (base64)
   * @param string The encoding to use
   */
  public function setHeaderEncoding($mode="B")
  {
    switch (strtoupper($mode))
    {
      case "Q": case "QP": case "QUOTED-PRINTABLE":
        $this->message->headers->setEncoding("Q");
        break;
      default:
        $this->message->headers->setEncoding("B");
    }
  }
  /**
   * Set the return path address (where bounces go to)
   * @param mixed The address as a string or Swift_Address
   */
  public function setReturnPath($address)
  {
    return $this->message->setReturnPath($address);
  }
  /**
   * Request for a read recipient to be sent to the reply-to address
   * @param boolean
   */
  public function requestReadReceipt($request=true)
  {
    //$this->message->requestReadReceipt(true);
  }
  /**
   * Set the message priority
   * This is an integer between 1 (high) and 5 (low)
   * @param int The level of priority to use
   */
  public function setPriority($priority)
  {
    $this->message->setPriority($priority);
  }
  /**
   * Get the return-path address as a string
   * @return string
   */
  public function getReturnPath()
  {
    try {
      return $this->message->getReturnPath();
    } catch (Swift_Message_MimeException $e) {
      return false;
    }
  }
  /**
   * Set the reply-to header
   * @param mixed The address replies come to. String, or Swift_Address, or an array of either.
   */
  public function setReplyTo($address)
  {
    return $this->message->setReplyTo($address);
  }
  /**
   * Get the reply-to address(es) as an array of strings
   * @return array
   */
  public function getReplyTo()
  {
    try {
      return $this->message->getReplyTo();
    } catch (Swift_Message_MimeException $e) {
      return false;
    }
  }
  /**
   * Add To: recipients to the email
   * @param mixed To address(es)
   * @return boolean
   */
  public function addTo($address)
  {
    return $this->addRecipients($address, "To");
  }
  /**
   * Get an array of To addresses
   * This currently returns an array of Swift_Address objects and may be simplified to an array of strings in later versions
   * @return array
   */
  public function getToAddresses()
  {
    return $this->recipients->getTo();
  }
  /**
   * Clear out all To: recipients
   */
  public function flushTo()
  {
    $this->recipients->flushTo();
  }
  /**
   * Add Cc: recipients to the email
   * @param mixed Cc address(es)
   * @return boolean
   */
  public function addCc($address)
  {
    return $this->addRecipients($address, "Cc");
  }
  /**
   * Get an array of Cc addresses
   * This currently returns an array of Swift_Address objects and may be simplified to an array of strings in later versions
   * @return array
   */
  public function getCcAddresses()
  {
    return $this->recipients->getCc();
  }
  /**
   * Clear out all Cc: recipients
   */
  public function flushCc()
  {
    $this->recipients->flushCc();
  }
  /**
   * Add Bcc: recipients to the email
   * @param mixed Bcc address(es)
   * @return boolean
   */
  public function addBcc($address)
  {
    return $this->addRecipients($address, "Bcc");
  }
  /**
   * Get an array of Bcc addresses
   * This currently returns an array of Swift_Address objects and may be simplified to an array of strings in later versions
   * @return array
   */
  public function getBccAddresses()
  {
    return $this->recipients->getBcc();
  }
  /**
   * Clear out all Bcc: recipients
   */
  public function flushBcc()
  {
    $this->recipients->flushBcc();
  }
  /**
   * Add recipients to the email
   * @param mixed Address(es)
   * @param string Recipient type (To, Cc, Bcc)
   * @return boolean
   */
  protected function addRecipients($address, $type)
  {
    if (!in_array($type, array("To", "Cc", "Bcc"))) return false;
    $method = "add" . $type;
    
    if ($address instanceof Swift_Address)
    {
      $this->recipients->$method($address);
      return true;
    }
    else
    {
      $added = 0;
      foreach ((array)$address as $addr)
      {
        if (is_array($addr))
        {
          $addr = array_values($addr);
          if (count($addr) >= 2)
          {
            $this->recipients->$method($addr[0], $addr[1]);
            $added++;
            continue;
          }
          elseif (count($addr) == 1) $addr = $addr[0];
          else continue;
        }
        
        if (is_string($addr))
        {
          $addr = $this->stringToAddress($addr);
          $this->recipients->$method($addr);
          $added++;
        }
      }
      return ($added > 0);
    }
  }
  /**
   * Flush message, recipients and headers
   */
  public function flush()
  {
    $this->newMessage();
    $this->newRecipientList();
  }
  /**
   * Get a list of any addresses which have failed since instantiation
   * @return array
   */
  public function getFailedRecipients()
  {
    $log = Swift_LogContainer::getLog();
    return $log->getFailedRecipients();
  }
  /**
   * Set the multipart MIME warning message (only seen by old clients)
   * @param string The message to show
   */
  public function setMimeWarning($text)
  {
    $this->message->setMimeWarning($text);
  }
  /**
   * Get the currently set MIME warning (seen by old clients)
   * @return string
   */
  public function getMimeWarning()
  {
    return $this->message->getMimeWarning();
  }
  /**
   * Set the charset of the charset to use in the message
   * @param string The charset (e.g. utf-8, iso-8859-1 etc)
   * @return boolean
   */
  public function setCharset($charset)
  {
    try {
      $this->message->setCharset($charset);
      return true;
    } catch (Swift_Message_MimeException $e) {
      $this->setError("Unable to set the message charset:<br />" . $e->getMessage());
      return false;
    }
  }
  /**
   * Get the charset of the charset to use in the message
   * @return string
   */
  public function getCharset()
  {
    return $this->message->getCharset();
  }
  /**
   * Add a new MIME part to the message
   * @param mixed The part to add.  If this is a string it's used as the body.  If it's an instance of Swift_Message_Part it's used as the entire part
   * @param string Content-type, default text/plain
   * @param string The encoding used (default is to let Swift decide)
   * @param string The charset to use (default is to let swift decide)
   */
  public function addPart($body, $type="text/plain", $encoding=null, $charset=null)
  {
    if ($body instanceof Swift_Message_Mime)
    {
      try {
        $this->partIds[] = $this->message->attach($body);
      } catch (Swift_Message_MimeException $e) {
        $this->setError("A MIME part failed to attach:<br />" . $e->getMessage());
        return false;
      }
    }
    else
    {
      try {
        $this->partIds[] = $this->message->attach(new Swift_Message_Part($body, $type, $encoding, $charset));
      } catch (Swift_Message_MimeException $e) {
        $this->setError("A MIME part failed to attach:<br />" . $e->getMessage());
        return false;
      }
    }
  }
  /**
   * Add a new attachment to the message
   * @param mixed The attachment to add.  If this is a string it's used as the file contents.  If it's an instance of Swift_Message_Attachment it's used as the entire part.  If it's an instance of Swift_File it's used as the contents.
   * @param string Filename, optional
   * @param string Content-type. Default application/octet-stream
   * @param string The encoding used (default is base64)
   * @return boolean
   */
  public function addAttachment($data, $filename=null, $type="application/octet-stream", $encoding=null)
  {
    if ($data instanceof Swift_Message_Mime)
    {
      try {
        $this->attachmentIds[] = $this->message->attach($data);
      } catch (Swift_Message_MimeException $e) {
        $this->setError("An attachment failed to attach:<br />" . $e->getMessage());
        return false;
      }
    }
    else
    {
      try {
        $this->attachmentIds[] = $this->message->attach(new Swift_Message_Attachment($data, $filename, $type, $encoding));
      } catch (Swift_Message_MimeException $e) {
        $this->setError("An attachment failed to attach<br />" . $e->getMessage());
        return false;
      } catch (Swift_FileException $e) {
        $this->setError("An attachment failed to attach:<br />" . $e->getMessage());
        return false;
      }
    }
    return true;
  }
  /**
   * Embed an image into the message and get the src attribute for HTML
   * Returns FALSE on failure
   * @param mixed The path to the image, a Swift_Message_Image object or a Swift_File object
   * @return string
   */
  public function addImage($input)
  {
    $ret = false;
    if ($input instanceof Swift_Message_Image)
    {
      $ret = $this->message->attach($input);
      $this->attachmentIds[] = $ret;
      return $ret;
    }
    elseif ($input instanceof Swift_File)
    {
      try {
        $ret = $this->message->attach(new Swift_Message_Image($input));
        $this->attachmentIds[] = $ret;
        return $ret;
      } catch (Swift_Message_MimeException $e) {
        $this->setError("An attachment failed to attach:<br />" . $e->getMessage());
        return false;
      } catch (Swift_FileException $e) {
        $this->setError("An attachment failed to attach:<br />" . $e->getMessage());
        return false;
      }
    }
    else
    {
      try {
        $ret = $this->message->attach(new Swift_Message_Image(new Swift_File($input)));
        $this->attachmentIds[] = $ret;
        return $ret;
      } catch (Swift_Message_MimeException $e) {
        $this->setError("An attachment failed to attach:<br />" . $e->getMessage());
        return false;
      } catch (Swift_FileException $e) {
        $this->setError("An attachment failed to attach:<br />" . $e->getMessage());
        return false;
      }
    }
  }
  /**
   * Embed an inline file into the message, such as a Image or MIDI file
   * @param mixed The file contents, Swift_File object or Swift_Message_EmbeddedFile object
   * @param string The content-type of the file, optional
   * @param string The filename to use, optional
   * @param string the Content-ID to use, optional
   * @return string
   */
  public function embedFile($data, $type="application/octet-stream", $filename=null, $cid=null)
  {
    $ret = false;
    if ($data instanceof Swift_Message_EmbeddedFile)
    {
      $ret = $this->message->attach($data);
      $this->attachmentIds[] = $ret;
      return $ret;
    }
    elseif ($data instanceof Swift_File)
    {
      try {
        $ret = $this->message->attach(new Swift_Message_EmbeddedFile($data, $filename, $type, $cid));
        $this->attachmentIds[] = $ret;
        return $ret;
      } catch (Swift_Message_MimeException $e) {
        $this->setError("An attachment failed to attach:<br />" . $e->getMessage());
        return false;
      } catch (Swift_FileException $e) {
        $this->setError("An attachment failed to attach:<br />" . $e->getMessage());
        return false;
      }
    }
    else
    {
      try {
        $ret = $this->message->attach(new Swift_Message_EmbeddedFile($data, $filename, $type, $cid));
        $this->attachmentIds[] = $ret;
        return $ret;
      } catch (Swift_Message_MimeException $e) {
        $this->setError("An attachment failed to attach:<br />" . $e->getMessage());
        return false;
      } catch (Swift_FileException $e) {
        $this->setError("An attachment failed to attach:<br />" . $e->getMessage());
        return false;
      }
    }
  }
  /**
   * Add headers to the message
   * @param string The message headers to append, separated by CRLF
   * @deprecated
   */
  public function addHeaders($string)
  {
    //Split at the line ending only if it's not followed by LWSP (as in, a full header)
    $headers = preg_split("~\r?\n(?![ \t])~", $string);
    foreach ($headers as $header)
    {
      if (empty($header)) continue;
      //Get the bit before the colon
      $header_name = substr($header, 0, ($c_pos = strpos($header, ": ")));
      // ... and trim it away
      $header = substr($header, $c_pos+2);
      //Try splitting at "; " for attributes
      $attribute_pairs = preg_split("~\\s*;\\s+~", $header);
      //The value would always be right after the colon
      $header_value = $attribute_pairs[0];
      $this->message->headers->set($header_name, $header_value);
      unset($attribute_pairs[0]);
      foreach ($attribute_pairs as $pair)
      {
        //Now try finding the attribute name, and it's value (removing quotes)
        if (preg_match("~^(.*?)=(\"?)(.*?)\\2\\s*\$~", $pair, $matches))
        {
          try {
            $this->message->headers->setAttribute($header_name, $matches[1], $matches[3]);
          } catch (Swift_Message_MimeException $e) {
            $this->setError("There was a problem parsing or setting a header attribute:<br />" . $e->getMessage());
            //Ignored... it's EasySwift... C'mon ;)
          }
        }
      }
    }
  }
  /**
   * Set a header in the message
   * @param string The name of the header
   * @param string The value of the header (without attributes)
   * @see {addHeaderAttribute}
   */
  public function setHeader($name, $value)
  {
    $this->message->headers->set($name, $value);
  }
  /**
   * Set an attribute in the message headers
   * For example charset in Content-Type: text/html; charset=utf-8 set by $swift->setHeaderAttribute("Content-Type", "charset", "utf-8")
   * @param string The name of the header
   * @param string The name of the attribute
   * @param string The value of the attribute
   */
  public function setHeaderAttribute($name, $attribute, $value)
  {
    if ($this->message->headers->has($name))
      $this->message->headers->setAttribute($name, $attribute, $value);
  }
  /**
   * Send an email to a number of recipients
   * Returns the number of successful recipients, or FALSE on failure
   * @param mixed The recipients to send to.  One of string, array, 2-dimensional array or Swift_Address
   * @param mixed The address to send from. string or Swift_Address
   * @param string The message subject
   * @param string The message body, optional
   * @return int
   */
  public function send($recipients, $from, $subject, $body=null)
  {
    $this->addTo($recipients);
    
    $sender = false;
    if (is_string($from)) $sender = $this->stringToAddress($from);
    elseif ($from instanceof Swift_Address) $sender = $from;
    if (!$sender) return false;
    
    $this->message->setSubject($subject);
    if ($body) $this->message->setBody($body);
    try {
      if (!$this->exactCopy && !$this->recipients->getCc() && !$this->recipients->getBcc())
      {
        $sent = $this->swift->batchSend($this->message, $this->recipients, $sender);
      }
      else
      {
        $sent = $this->swift->send($this->message, $this->recipients, $sender);
      }
      if ($this->autoFlush) $this->flush();
      return $sent;
    } catch (Swift_ConnectionException $e) {
      $this->setError("Sending failed:<br />" . $e->getMessage());
      return false;
    }
  }
}
