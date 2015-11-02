<?php

/**
 * Swift Mailer Message Attachment
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Message
 * @license GNU Lesser General Public License
 */


/**
 * Attachment component for Swift Mailer
 * @package Swift_Message
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Message_Attachment extends Swift_Message_Mime
{
  /**
   * A numeric counter, incremented by 1 when a filename is made.
   * @var int
   */
  protected static $fileId = 0;
  
  /**
   * Constructor
   * @param mixed The data to use in the body
   * @param string Mime type
   * @param string The encoding format used
   * @param string The charset used
   */
  public function __construct($data=null, $name=null, $type="application/octet-stream", $encoding="base64", $disposition="attachment")
  {
    parent::__construct();
    
    $this->setContentType($type);
    $this->setEncoding($encoding);
    $this->setDescription($name);
    $this->setDisposition($disposition);
    $this->setFileName($name);
    
    if ($data !== null) $this->setData($data, ($name === null));
  }
  /**
   * Get a unique filename (just a sequence)
   * @param string the prefix for the filename
   * @return string
   */
  public static function generateFileName($prefix="file")
  {
    return $prefix . (self::$fileId++);
  }
  /**
   * Get the level in the MIME hierarchy at which this section should appear.
   * @return string
   */
  public function getLevel()
  {
    return Swift_Message_Mime::LEVEL_MIXED;
  }
  /**
   * Overrides setData() in MIME so that a filename can be set
   * @param mixed The data to set for the body
   * @param boolean If the stream is a file, should it's filename be used?
   * @throws Swift_FileException If the stream cannot be read
   */
  public function setData($data, $read_filename=true)
  {
    parent::setData($data);
    if ($read_filename && ($data instanceof Swift_file))
    {
      $this->setFileName($data->getFileName());
    }
  }
  /**
   * Set the name (and description) used to identify the file
   * This method overrides any value previously set with setDescription()
   * @param string The filename including it's extension if any
   * @throws Swift_Message_MimeException If some required headers have been deliberately removed
   */
  public function setFileName($name)
  {
    $this->headers->setAttribute("Content-Type", "name", $name);
    $this->setDescription($name);
    if ($this->headers->has("Content-Disposition"))
    {
      $this->headers->setAttribute("Content-Disposition", "filename", $name);
    }
  }
  /**
   * Get the filename of this attachment
   * @return string
   * @throws Swift_Message_MimeException If some vital headers have been removed
   */
  public function getFileName()
  {
    if ($this->headers->hasAttribute("Content-Type", "name"))
    {
      return $this->headers->getAttribute("Content-Type", "name");
    }
    else return null;
  }
  /**
   * Set the Content-Description header
   * @param string The description in the header (filename usually!)
   */
  public function setDescription($desc)
  {
    $this->headers->set("Content-Description", $desc);
  }
  /**
   * Return the description in the headers
   * @return string
   */
  public function getDescription()
  {
    if ($this->headers->has("Content-Description"))
    {
      return $this->headers->get("Content-Description");
    }
    else return null;
  }
  /**
   * Set the disposition of the attachment (usually inline or attachment)
   * @param string The value to use in the Content-Disposition field
   */
  public function setDisposition($disposition)
  {
    $this->headers->set("Content-Disposition", $disposition);
  }
  /**
   * Get the disposition used in the attachment (usually inline or attachment)
   * @return string
   */
  public function getDisposition()
  {
    if ($this->headers->has("Content-Disposition"))
    {
      return $this->headers->get("Content-Disposition");
    }
    else return null;
  }
  /**
   * Execute needed logic prior to building
   */
  public function preBuild()
  {
    if ($this->getFileName() === null)
    {
      if ($this->getData() instanceof Swift_File)
      {
        $this->setFileName($this->getData()->getFileName());
      }
      else
      {
        $this->setFileName(self::generateFileName("file.att."));
      }
    }
  }
}
