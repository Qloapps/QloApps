<?php

/**
 * Swift Mailer Core Component.
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift
 * @version 3.3.2
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/Swift/ClassLoader.php";
Swift_ClassLoader::load("Swift_LogContainer");
Swift_ClassLoader::load("Swift_ConnectionBase");
Swift_ClassLoader::load("Swift_BadResponseException");
Swift_ClassLoader::load("Swift_Cache");
Swift_ClassLoader::load("Swift_CacheFactory");
Swift_ClassLoader::load("Swift_Message");
Swift_ClassLoader::load("Swift_RecipientList");
Swift_ClassLoader::load("Swift_BatchMailer");
Swift_ClassLoader::load("Swift_Events");
Swift_ClassLoader::load("Swift_Events_Listener");

/**
 * Swift is the central component in the Swift library.
 * @package Swift
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @version 3.3.2
 */
class Swift
{
  /**
   * The version number.
   */
  const VERSION = "3.3.2";
  /**
   * Constant to flag Swift not to try and connect upon instantiation
   */
  const NO_START = 2;
  /**
   * Constant to tell Swift not to perform the standard SMTP handshake upon connect
   */
  const NO_HANDSHAKE = 4;
  /**
   * Constant to ask Swift to start logging
   */
  const ENABLE_LOGGING = 8;
  /**
   * Constant to prevent postConnect() being run in the connection
   */
  const NO_POST_CONNECT = 16;
  /**
   * The connection object currently active
   * @var Swift_Connection
   */
  public $connection = null;
  /**
   * The domain name of this server (should technically be a FQDN)
   * @var string
   */
  protected $domain = null;
  /**
   * Flags to change the behaviour of Swift
   * @var int
   */
  protected $options;
  /**
   * Loaded plugins, separated into containers according to roles
   * @var array
   */
  protected $listeners = array();
  
  /**
   * Constructor
   * @param Swift_Connection The connection object to deal with I/O
   * @param string The domain name of this server (the client) as a FQDN
   * @param int Optional flags
   * @throws Swift_ConnectionException If a connection cannot be established or the connection is behaving incorrectly
   */
  public function __construct(Swift_Connection $conn, $domain=false, $options=null)
  {
    $this->initializeEventListenerContainer();
    $this->setOptions($options);
    
    $log = Swift_LogContainer::getLog();
    
    if ($this->hasOption(self::ENABLE_LOGGING) && !$log->isEnabled())
    {
      $log->setLogLevel(Swift_Log::LOG_NETWORK);
    }
    
    if (!$domain) $domain = !empty($_SERVER["SERVER_ADDR"]) ? "[" . $_SERVER["SERVER_ADDR"] . "]" : "localhost.localdomain";
    
    $this->setDomain($domain);
    $this->connection = $conn;
    
    if ($conn && !$this->hasOption(self::NO_START))
    {
      if ($log->hasLevel(Swift_Log::LOG_EVERYTHING)) $log->add("Trying to connect...", Swift_Log::NORMAL);
      $this->connect();
    }
  }
  /**
   * Populate the listeners array with the defined listeners ready for plugins
   */
  protected function initializeEventListenerContainer()
  {
    Swift_ClassLoader::load("Swift_Events_ListenerMapper");
    foreach (Swift_Events_ListenerMapper::getMap() as $interface => $method)
    {
      if (!isset($this->listeners[$interface]))
        $this->listeners[$interface] = array();
    }
  }
  /**
   * Add a new plugin to Swift
   * Plugins must implement one or more event listeners
   * @param Swift_Events_Listener The plugin to load
   */
  public function attachPlugin(Swift_Events_Listener $plugin, $id)
  {
    foreach (array_keys($this->listeners) as $key)
    {
      $listener = "Swift_Events_" . $key;
      Swift_ClassLoader::load($listener);
      if ($plugin instanceof $listener) $this->listeners[$key][$id] = $plugin;
    }
  }
  /**
   * Get an attached plugin if it exists
   * @param string The id of the plugin
   * @return Swift_Event_Listener
   */
  public function getPlugin($id)
  {
    foreach ($this->listeners as $type => $arr)
    {
      if (isset($arr[$id])) return $this->listeners[$type][$id];
    }
    return null; //If none found
  }
  /**
   * Remove a plugin attached under the ID of $id
   * @param string The ID of the plugin
   */
  public function removePlugin($id)
  {
    foreach ($this->listeners as $type => $arr)
    {
      if (isset($arr[$id]))
      {
        $this->listeners[$type][$id] = null;
        unset($this->listeners[$type][$id]);
      }
    }
  }
  /**
   * Send a new type of event to all objects which are listening for it
   * @param Swift_Events The event to send
   * @param string The type of event
   */
  public function notifyListeners($e, $type)
  {
    Swift_ClassLoader::load("Swift_Events_ListenerMapper");
    if (!empty($this->listeners[$type]) && $notifyMethod = Swift_Events_ListenerMapper::getNotifyMethod($type))
    {
      $e->setSwift($this);
      foreach ($this->listeners[$type] as $k => $listener)
      {
        $listener->$notifyMethod($e);
      }
    }
    else $e = null;
  }
  /**
   * Check if an option flag has been set
   * @param string Option name
   * @return boolean
   */
  public function hasOption($option)
  {
    return ($this->options & $option);
  }
  /**
   * Adjust the options flags
   * E.g. $obj->setOptions(Swift::NO_START | Swift::NO_HANDSHAKE)
   * @param int The bits to set
   */
  public function setOptions($options)
  {
    $this->options = (int) $options;
  }
  /**
   * Get the current options set (as bits)
   * @return int
   */
  public function getOptions()
  {
    return (int) $this->options;
  }
  /**
   * Set the FQDN of this server as it will identify itself
   * @param string The FQDN of the server
   */
  public function setDomain($name)
  {
    $this->domain = (string) $name;
  }
  /**
   * Attempt to establish a connection with the service
   * @throws Swift_ConnectionException If the connection cannot be established or behaves oddly
   */
  public function connect()
  {
    $this->connection->start();
    $greeting = $this->command("", 220);
    if (!$this->hasOption(self::NO_HANDSHAKE))
    {
      $this->handshake($greeting);
    }
    Swift_ClassLoader::load("Swift_Events_ConnectEvent");
    $this->notifyListeners(new Swift_Events_ConnectEvent($this->connection), "ConnectListener");
  }
  /**
   * Disconnect from the MTA
   * @throws Swift_ConnectionException If the connection will not stop
   */
  public function disconnect()
  {
    $this->command("QUIT");
    $this->connection->stop();
    Swift_ClassLoader::load("Swift_Events_DisconnectEvent");
    $this->notifyListeners(new Swift_Events_DisconnectEvent($this->connection), "DisconnectListener");
  }
  /**
   * Throws an exception if the response code wanted does not match the one returned
   * @param Swift_Event_ResponseEvent The full response from the service
   * @param int The 3 digit response code wanted
   * @throws Swift_BadResponseException If the code does not match
   */
  protected function assertCorrectResponse(Swift_Events_ResponseEvent $response, $codes)
  {
    $codes = (array)$codes;
    if (!in_array($response->getCode(), $codes))
    {
      $log = Swift_LogContainer::getLog();
      $error = "Expected response code(s) [" . implode(", ", $codes) . "] but got response [" . $response->getString() . "]";
      if ($log->hasLevel(Swift_Log::LOG_ERRORS)) $log->add($error, Swift_Log::ERROR);
      throw new Swift_BadResponseException($error);
    }
  }
  /**
   * Have a polite greeting with the server and work out what it's capable of
   * @param Swift_Events_ResponseEvent The initial service line respoonse
   * @throws Swift_ConnectionException If conversation is not going very well
   */
  protected function handshake(Swift_Events_ResponseEvent $greeting)
  {
    if ($this->connection->getRequiresEHLO() || strpos($greeting->getString(), "ESMTP"))
      $this->setConnectionExtensions($this->command("EHLO " . $this->domain, 250));
    else $this->command("HELO " . $this->domain, 250);
    //Connection might want to do something like authenticate now
    if (!$this->hasOption(self::NO_POST_CONNECT)) $this->connection->postConnect($this);
  }
  /**
   * Set the extensions which the service reports in the connection object
   * @param Swift_Events_ResponseEvent The list of extensions as reported by the service
   */
  protected function setConnectionExtensions(Swift_Events_ResponseEvent $list)
  {
    $le = (strpos($list->getString(), "\r\n") !== false) ? "\r\n" : "\n";
    $list = explode($le, $list->getString());
    for ($i = 1, $len = count($list); $i < $len; $i++)
    {
      $extension = substr($list[$i], 4);
      $attributes = preg_split("![ =]!", $extension);
      $this->connection->setExtension($attributes[0], (isset($attributes[1]) ? array_slice($attributes, 1) : array()));
    }
  }
  /**
   * Execute a command against the service and get the response
   * @param string The command to execute (leave off any CRLF!!!)
   * @param int The code to check for in the response, if any. -1 indicates that no response is wanted.
   * @return Swift_Events_ResponseEvent The server's response (could be multiple lines)
   * @throws Swift_ConnectionException If a code was expected but does not match the one returned
   */
  public function command($command, $code=null)
  {
    $log = Swift_LogContainer::getLog();
    Swift_ClassLoader::load("Swift_Events_CommandEvent");
    if ($command !== "")
    {
      $command_event = new Swift_Events_CommandEvent($command, $code);
      $command = null; //For memory reasons
      $this->notifyListeners($command_event, "BeforeCommandListener");
      if ($log->hasLevel(Swift_Log::LOG_NETWORK) && $code != -1) $log->add($command_event->getString(), Swift_Log::COMMAND);
      $end = ($code != -1) ? "\r\n" : null;
      $this->connection->write($command_event->getString(), $end);
      $this->notifyListeners($command_event, "CommandListener");
    }
    
    if ($code == -1) return null;
    
    Swift_ClassLoader::load("Swift_Events_ResponseEvent");
    $response_event = new Swift_Events_ResponseEvent($this->connection->read());
    $this->notifyListeners($response_event, "ResponseListener");
    if ($log->hasLevel(Swift_Log::LOG_NETWORK)) $log->add($response_event->getString(), Swift_Log::RESPONSE);
    if ($command !== "" && $command_event->getCode() !== null)
      $this->assertCorrectResponse($response_event, $command_event->getCode());
    return $response_event;
  }
  /**
   * Reset a conversation which has gone badly
   * @throws Swift_ConnectionException If the service refuses to reset
   */
  public function reset()
  {
    $this->command("RSET", 250);
  }
  /**
   * Send a message to any number of recipients
   * @param Swift_Message The message to send.  This does not need to (and shouldn't really) have any of the recipient headers set.
   * @param mixed The recipients to send to.  Can be a string, Swift_Address or Swift_RecipientList. Note that all addresses apart from Bcc recipients will appear in the message headers
   * @param mixed The address to send the message from.  Can either be a string or an instance of Swift_Address.
   * @return int The number of successful recipients
   * @throws Swift_ConnectionException If sending fails for any reason.
   */
  public function send(Swift_Message $message, $recipients, $from)
  {
    Swift_ClassLoader::load("Swift_Message_Encoder");
    if (is_string($recipients) && preg_match("/^" . Swift_Message_Encoder::CHEAP_ADDRESS_RE . "\$/", $recipients))
    {
      $recipients = new Swift_Address($recipients);
    }
    elseif (!($recipients instanceof Swift_AddressContainer))
      throw new Exception("The recipients parameter must either be a valid string email address, ".
      "an instance of Swift_RecipientList or an instance of Swift_Address.");
      
    if (is_string($from) && preg_match("/^" . Swift_Message_Encoder::CHEAP_ADDRESS_RE . "\$/", $from))
    {
      $from = new Swift_Address($from);
    }
    elseif (!($from instanceof Swift_Address))
      throw new Exception("The sender parameter must either be a valid string email address or ".
      "an instance of Swift_Address.");
    
    $log = Swift_LogContainer::getLog();
    
    if (!$message->getEncoding() && !$this->connection->hasExtension("8BITMIME"))
    {
      $message->setEncoding("QP", true, true);
    }
    
    $list = $recipients;
    if ($recipients instanceof Swift_Address)
    {
      $list = new Swift_RecipientList();
      $list->addTo($recipients);
    }
    
    Swift_ClassLoader::load("Swift_Events_SendEvent");
    $send_event = new Swift_Events_SendEvent($message, $list, $from, 0);
    
    $this->notifyListeners($send_event, "BeforeSendListener");
    
    $to = $cc = array();
    if (!($has_from = $message->getFrom())) $message->setFrom($from);
    if (!($has_return_path = $message->getReturnPath())) $message->setReturnPath($from->build(true));
    if (!($has_reply_to = $message->getReplyTo())) $message->setReplyTo($from);
    if (!($has_message_id = $message->getId())) $message->generateId();
    
    $this->command("MAIL FROM: " . $message->getReturnPath(true), 250);
    
    $failed = 0;
    $sent = 0;
    $tmp_sent = 0;
    
    $it = $list->getIterator("to");
    while ($it->hasNext())
    {
      $it->next();
      $address = $it->getValue();
      $to[] = $address->build();
      try {
        $this->command("RCPT TO: " . $address->build(true), 250);
        $tmp_sent++;
      } catch (Swift_BadResponseException $e) {
        $failed++;
        $send_event->addFailedRecipient($address->getAddress());
        if ($log->hasLevel(Swift_Log::LOG_FAILURES)) $log->addfailedRecipient($address->getAddress());
      }
    }
    $it = $list->getIterator("cc");
    while ($it->hasNext())
    {
      $it->next();
      $address = $it->getValue();
      $cc[] = $address->build();
      try {
        $this->command("RCPT TO: " . $address->build(true), 250);
        $tmp_sent++;
      } catch (Swift_BadResponseException $e) {
        $failed++;
        $send_event->addFailedRecipient($address->getAddress());
        if ($log->hasLevel(Swift_Log::LOG_FAILURES)) $log->addfailedRecipient($address->getAddress());
      }
    }
    
    if ($failed == (count($to) + count($cc)))
    {
      $this->reset();
      $this->notifyListeners($send_event, "SendListener");
      return 0;
    }
    
    if (!($has_to = $message->getTo()) && !empty($to)) $message->setTo($to);
    if (!($has_cc = $message->getCc()) && !empty($cc)) $message->setCc($cc);
    
    $this->command("DATA", 354);
    $data = $message->build();
    
    while (false !== $bytes = $data->read())
      $this->command($bytes, -1);
    if ($log->hasLevel(Swift_Log::LOG_NETWORK)) $log->add("<MESSAGE DATA>", Swift_Log::COMMAND);
    try {
      $this->command("\r\n.", 250);
      $sent += $tmp_sent;
    } catch (Swift_BadResponseException $e) {
      $failed += $tmp_sent;
    }
    
    $tmp_sent = 0;
    $has_bcc = $message->getBcc();
    $it = $list->getIterator("bcc");
    while ($it->hasNext())
    {
      $it->next();
      $address = $it->getValue();
      if (!$has_bcc) $message->setBcc($address->build());
      try {
        $this->command("MAIL FROM: " . $message->getReturnPath(true), 250);
        $this->command("RCPT TO: " . $address->build(true), 250);
        $this->command("DATA", 354);
        $data = $message->build();
        while (false !== $bytes = $data->read())
          $this->command($bytes, -1);
        if ($log->hasLevel(Swift_Log::LOG_NETWORK)) $log->add("<MESSAGE DATA>", Swift_Log::COMMAND);
        $this->command("\r\n.", 250);
        $sent++;
      } catch (Swift_BadResponseException $e) {
        $failed++;
        $send_event->addFailedRecipient($address->getAddress());
        if ($log->hasLevel(Swift_Log::LOG_FAILURES)) $log->addfailedRecipient($address->getAddress());
        $this->reset();
      }
    }
    
    $total = count($to) + count($cc) + count($list->getBcc());
    
    $send_event->setNumSent($sent);
    $this->notifyListeners($send_event, "SendListener");
    
    if (!$has_return_path) $message->setReturnPath("");
    if (!$has_from) $message->setFrom("");
    if (!$has_to) $message->setTo("");
    if (!$has_reply_to) $message->setReplyTo(null);
    if (!$has_cc) $message->setCc(null);
    if (!$has_bcc) $message->setBcc(null);
    if (!$has_message_id) $message->setId(null);
    
    if ($log->hasLevel(Swift_Log::LOG_NETWORK)) $log->add("Message sent to " . $sent . "/" . $total . " recipients", Swift_Log::NORMAL);
    
    return $sent;
  }
  /**
   * Send a message to a batch of recipients.
   * Unlike send() this method ignores Cc and Bcc recipients and does not reveal every recipients' address in the headers
   * @param Swift_Message The message to send (leave out the recipient headers unless you are deliberately overriding them)
   * @param Swift_RecipientList The addresses to send to
   * @param Swift_Address The address the mail is from (sender)
   * @return int The number of successful recipients
   */
  public function batchSend(Swift_Message $message, Swift_RecipientList $to, $from)
  {
    $batch = new Swift_BatchMailer($this);
    return $batch->send($message, $to, $from);
  }
}
