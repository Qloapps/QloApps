<?php

/**
 * Swift Mailer Send Event
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Events
 * @license GNU Lesser General Public License
 */


/**
 * Generated every time a message is sent with Swift
 * @package Swift_Events
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Events_SendEvent extends Swift_Events
{
  /**
   * A reference to the message being sent
   * @var Swift_Message
   */
  protected $message = null;
  /**
   * A reference to the sender address object
   * @var Swift_Address
   */
  protected $sender = null;
  /**
   * A reference to the recipients being sent to
   * @var Swift_RecipientList
   */
  protected $recipients = null;
  /**
   * The number of recipients sent to so
   * @var int
   */
  protected $sent = null;
  /**
   * Recipients we couldn't send to
   * @var array
   */
  protected $failed = array();
  
  /**
   * Constructor
   * @param Swift_Message The message being sent
   * @param Swift_RecipientList The recipients
   * @param Swift_Address The sender address
   * @param int The number of addresses sent to
   */
  public function __construct(Swift_Message $message, Swift_RecipientList $list, Swift_Address $from, $sent=0)
  {
    $this->message = $message;
    $this->recipients = $list;
    $this->sender = $from;
    $this->sent = $sent;
  }
  /**
   * Get the message being sent
   * @return Swift_Message
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Get the list of recipients
   * @return Swift_RecipientList
   */
  public function getRecipients()
  {
    return $this->recipients;
  }
  /**
   * Get the sender's address
   * @return Swift_Address
   */
  public function getSender()
  {
    return $this->sender;
  }
  /**
   * Set the number of recipients to how many were sent
   * @param int
   */
  public function setNumSent($sent)
  {
    $this->sent = (int) $sent;
  }
  /**
   * Get the total number of addresses to which the email sent successfully
   * @return int
   */
  public function getNumSent()
  {
    return $this->sent;
  }
  /**
   * Add an email address to the failed recipient list for this send
   * @var string The email address
   */
  public function addFailedRecipient($address)
  {
    $this->failed[] = $address;
  }
  /**
   * Get an array of failed recipients for this send
   * @return array
   */
  public function getFailedRecipients()
  {
    return $this->failed;
  }
}
