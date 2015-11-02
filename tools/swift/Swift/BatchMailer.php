<?php

/**
 * Handles batch mailing with Swift Mailer with fail-safe support.
 * Restarts the connection if it dies and then continues where it left off.
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift
 * @license GNU Lesser General Public License
 */
class Swift_BatchMailer
{
  /**
   * The current instance of Swift.
   * @var Swift
   */
  protected $swift;
  /**
   * The maximum number of times a single recipient can be attempted before giving up.
   * @var int
   */
  protected $maxTries = 2;
  /**
   * The number of seconds to sleep for if an error occurs.
   * @var int
   */
  protected $sleepTime = 0;
  /**
   * Failed recipients (undeliverable)
   * @var array
   */
  protected $failed = array();
  /**
   * The maximum number of successive failures before giving up.
   * @var int
   */
  protected $maxFails = 0;
  /**
   * A temporary copy of some message headers.
   * @var array
   */
  protected $headers = array();
  
  /**
   * Constructor.
   * @param Swift The current instance of Swift
   */
  public function __construct(Swift $swift)
  {
    $this->setSwift($swift);
  }
  /**
   * Set the current Swift instance.
   * @param Swift The instance
   */
  public function setSwift(Swift $swift)
  {
    $this->swift = $swift;
  }
  /**
   * Get the Swift instance which is running.
   * @return Swift
   */
  public function getSwift()
  {
    return $this->swift;
  }
  /**
   * Set the maximum number of times a single address is allowed to be retried.
   * @param int The maximum number of tries.
   */
  public function setMaxTries($max)
  {
    $this->maxTries = abs($max);
  }
  /**
   * Get the number of times a single address will be attempted in a batch.
   * @return int
   */
  public function getMaxTries()
  {
    return $this->maxTries;
  }
  /**
   * Set the amount of time to sleep for if an error occurs.
   * @param int Number of seconds
   */
  public function setSleepTime($secs)
  {
    $this->sleepTime = abs($secs);
  }
  /**
   * Get the amount of time to sleep for on errors.
   * @return int
   */
  public function getSleepTime()
  {
    return $this->sleepTime;
  }
  /**
   * Log a failed recipient.
   * @param string The email address.
   */
  public function addFailedRecipient($address)
  {
    $this->failed[] = $address;
    $this->failed = array_unique($this->failed);
  }
  /**
   * Get all recipients which failed in this batch.
   * @return array
   */
  public function getFailedRecipients()
  {
    return $this->failed;
  }
  /**
   * Clear out the list of failed recipients.
   */
  public function flushFailedRecipients()
  {
    $this->failed = null;
    $this->failed = array();
  }
  /**
   * Set the maximum number of times an error can be thrown in succession and still be hidden.
   * @param int
   */
  public function setMaxSuccessiveFailures($fails)
  {
    $this->maxFails = abs($fails);
  }
  /**
   * Get the maximum number of times an error can be thrown and still be hidden.
   * @return int
   */
  public function getMaxSuccessiveFailures()
  {
    return $this->maxFails;
  }
  /**
   * Restarts Swift forcibly.
   */
  protected function forceRestartSwift()
  {
    //Pre-empting problems trying to issue "QUIT" to a dead connection
    $this->swift->connection->stop();
    $this->swift->connection->start();
    $this->swift->disconnect();
    //Restart swift
    $this->swift->connect();
  }
  /**
   * Takes a temporary copy of original message headers in case an error occurs and they need restoring.
   * @param Swift_Message The message object
   */
  protected function copyMessageHeaders(&$message)
  {
    $this->headers["To"] = $message->headers->has("To") ?
      $message->headers->get("To") : null;
    $this->headers["Reply-To"] = $message->headers->has("Reply-To") ?
      $message->headers->get("Reply-To") : null;
    $this->headers["Return-Path"] = $message->headers->has("Return-Path") ?
      $message->headers->get("Return-Path") : null;
    $this->headers["From"] = $message->headers->has("From") ?
      $message->headers->get("From") : null;
  }
  /**
   * Restore message headers to original values in the event of a failure.
   * @param Swift_Message The message
   */
  protected function restoreMessageHeaders(&$message)
  {
    foreach ($this->headers as $name => $value)
    {
      $message->headers->set($name, $value);
    }
  }
  /**
   * Run a batch send in a fail-safe manner.
   * This operates as Swift::batchSend() except it deals with errors itself.
   * @param Swift_Message To send
   * @param Swift_RecipientList Recipients (To: only)
   * @param Swift_Address The sender's address
   * @return int The number sent to
   */
  public function send(Swift_Message $message, Swift_RecipientList $recipients, $sender)
  {
    $sent = 0;
    $successive_fails = 0;
    
    $it = $recipients->getIterator("to");
    while ($it->hasNext())
    {
      $it->next();
      $recipient = $it->getValue();
      $tried = 0;
      $loop = true;
      while ($loop && $tried < $this->getMaxTries())
      {
        try {
          $tried++;
          $loop = false;
          $this->copyMessageHeaders($message);
          $sent += ($n = $this->swift->send($message, $recipient, $sender));
          if (!$n) $this->addFailedRecipient($recipient->getAddress());
          $successive_fails = 0;
        } catch (Exception $e) {
          $successive_fails++;
          $this->restoreMessageHeaders($message);
          if (($max = $this->getMaxSuccessiveFailures())
            && $successive_fails > $max)
          {
            throw new Exception(
              "Too many successive failures. BatchMailer is configured to allow no more than " . $max .
              " successive failures.");
          }
          //If an exception was thrown, give it one more go
          if ($t = $this->getSleepTime()) sleep($t);
          $this->forceRestartSwift();
          $loop = true;
        }
      }
    }
    
    return $sent;
  }
}
