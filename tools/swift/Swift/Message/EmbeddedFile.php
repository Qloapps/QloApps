<?php

/**
 * Swift Mailer Embedded File (like an image or a midi file)
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Message
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Message_Attachment");

/**
 * Embedded File component for Swift Mailer
 * @package Swift_Message
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Message_EmbeddedFile extends Swift_Message_Attachment
{
  /**
   * The content-id in the headers (used in <img src=...> values)
   * @var string
   */
  protected $cid = null;
  
  /**
   * Constructor
   * @param mixed The input source.  Can be a file or a string
   * @param string The filename to use, optional
   * @param string The MIME type to use, optional
   * @param string The Content-ID to use, optional
   * @param string The encoding format to use, optional
   */
  public function __construct($data=null, $name=null, $type="application/octet-stream", $cid=null, $encoding="base64")
  {
    parent::__construct($data, $name, $type, $encoding, "inline");
    
    if ($cid === null)
    {
      $cid = self::generateFileName("swift-" . uniqid(time()) . ".");
      $cid = urlencode($cid) . "@" . (!empty($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : "swift");
    }
    $this->setContentId($cid);
    
    if ($name === null && !($data instanceof Swift_File)) $this->setFileName($cid);
    
    $this->headers->set("Content-Description", null);
  }
  /**
   * Get the level in the MIME hierarchy at which this section should appear.
   * @return string
   */
  public function getLevel()
  {
    return Swift_Message_Mime::LEVEL_RELATED;
  }
  /**
   * Set the Content-Id to use
   * @param string The content-id
   */
  public function setContentId($id)
  {
    $id = (string) $id;
    $this->cid = $id;
    $this->headers->set("Content-ID", "<" . $id . ">");
  }
  /**
   * Get the content-id of this file
   * @return string
   */
  public function getContentId()
  {
    return $this->cid;
  }
}
