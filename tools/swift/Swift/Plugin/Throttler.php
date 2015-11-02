<?php

/**
 * Swift Mailer Throttling Plugin.
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Plugin
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Plugin_BandwidthMonitor");
Swift_ClassLoader::load("Swift_Events_SendListener");

/**
 * Throttler plugin for Swift Mailer.
 * Restricts the speed at which Swift will operate.
 * @package Swift_Plugin
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Plugin_Throttler extends Swift_Plugin_BandwidthMonitor implements Swift_Events_SendListener
{
  /**
   * The rate in byte-per-minute
   * @var int
   */
  protected $bpm = null;
  /**
   * The rate as emails-per-minute
   * @var int
   */
  protected $epm = null;
  /**
   * The number of emails sent so far
   * @var int
   */
  protected $sent = 0;
  /**
   * The time at the start of overall execution
   * @var int
   */
  protected $time = null;
  
  /**
   * Part of the interface which is notified after a command is sent.
   * @param Swift_Events_CommandEvent
   */
  public function commandSent(Swift_Events_CommandEvent $e)
  {
    parent::commandSent($e);
    if (null === $rate = $this->getBytesPerMinute()) return;
    
    $duration = $this->getTimeLapse();
    $bytes_sent = $this->getBytesOut();
    $bytes_per_sec = $rate / 60;
    $seconds_allowed_so_far = ceil($bytes_sent / $bytes_per_sec);
    $overrun = $seconds_allowed_so_far - $duration;
    if ($overrun > 0)
    {
      $this->wait($overrun);
    }
  }
  /**
   * Part of the interface which is notified when a message has been sent.
   * @param Swift_Events_SendEvent
   */
  public function sendPerformed(Swift_Events_SendEvent $e)
  {
    $this->setSent($this->getSent() + 1);
    if (null === $rate = $this->getEmailsPerMinute()) return;
    
    $duration = $this->getTimeLapse();
    $emails_sent = $this->getSent();
    $emails_per_sec = $rate / 60;
    $seconds_allowed_so_far = ceil($emails_sent / $emails_per_sec);
    $overrun = $seconds_allowed_so_far - $duration;
    if ($overrun > 0)
    {
      $this->wait($overrun);
    }
  }
  /**
   * Wait for $seconds before continuing
   * @param int The number of seconds to wait
   */
  public function wait($secs)
  {
    sleep($secs);
  }
  /**
   * Set the time if it's not already set
   */
  protected function setTime()
  {
    if ($this->time === null) $this->time = time();
  }
  /**
   * Get the time taken thus far (full seconds).
   * @return int
   */
  public function getTimeLapse()
  {
    $this->setTime();
    return time() - $this->time;
  }
  /**
   * Set the number of emails sent
   * @param int Emails sent so far
   */
  public function setSent($num)
  {
    $this->sent = (int)$num;
  }
  /**
   * Get the number of emails sent
   * @return int
   */
  public function getSent()
  {
    return $this->sent;
  }
  /**
   * Set the throttling rate as bytes per minute
   * @param int The maximum number of outgoing bytes in 60 seconds.
   */
  public function setBytesPerMinute($bpm)
  {
    if ($bpm === null)
    {
      $this->bpm = null;
      return;
    }
    $this->setEmailsPerMinute(null);
    $this->bpm = abs((int)$bpm);
  }
  /**
   * Get the number of bytes allowed per minute.
   * Reurns NULL if not used.
   * @return int
   */
  public function getBytesPerMinute()
  {
    return $this->bpm;
  }
  /**
   * Set the rate as emails-per-minute.
   * @param int The max number of emails to send in a minute.
   */
  public function setEmailsPerMinute($epm)
  {
    if ($epm === null)
    {
      $this->epm = null;
      return;
    }
    $this->setBytesPerMinute(null);
    $this->epm = abs((int)$epm);
  }
  /**
   * Get the rate as number of emails per minute.
   * Returns null if not used.
   * @return int
   */
  public function getEmailsPerMinute()
  {
    return $this->epm;
  }
}
