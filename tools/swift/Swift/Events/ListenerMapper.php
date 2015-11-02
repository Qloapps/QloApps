<?php

/**
 * Swift Mailer Mapper for Event Listeners
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Events
 * @license GNU Lesser General Public License
 */

/**
 * Maps event listener names to the methods they implement
 * @package Swift_Events
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Events_ListenerMapper
{
  /**
   * Get the mapped names (Class => Method(s))
   * @return array
   */
  public static function getMap()
  {
    $map = array(
      "SendListener" => "sendPerformed",
      "BeforeSendListener" => "beforeSendPerformed",
      "CommandListener" => "commandSent",
      "BeforeCommandListener" => "beforeCommandSent",
      "ResponseListener" => "responseReceived",
      "ConnectListener" => "connectPerformed",
      "DisconnectListener" => "disconnectPerformed"
    );
    return $map;
  }
  
  /**
   * Get the name of the method which needs running based upon the listener name
   * @return string
   */
  public static function getNotifyMethod($listener)
  {
    $map = self::getMap();
    if (isset($map[$listener])) return $map[$listener];
    else return false;
  }
}
