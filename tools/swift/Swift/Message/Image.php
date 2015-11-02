<?php

/**
 * Swift Mailer Image Component
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Message
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Message_EmbeddedFile");

/**
 * Embedded Image component for Swift Mailer
 * @package Swift_Message
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Message_Image extends Swift_Message_EmbeddedFile
{
  /**
   * Constructor
   * @param Swift_File The input source file
   * @param string The filename to use, optional
   * @param string The MIME type to use, optional
   * @param string The Content-ID to use, optional
   * @param string The encoding format to use, optional
   */
  public function __construct(Swift_File $data=null, $name=null, $type="application/octet-stream", $cid=null, $encoding="base64")
  {
    parent::__construct($data, $name, $type, $cid, $encoding);
  }
  /**
   * Set data for the image
   * This overrides setData() in Swift_Message_Attachment
   * @param Swift_File The data to set, as a file
   * @throws Swift_Message_MimeException If the image cannot be used, or the file is not
   */
  public function setData($data, $read_filename=true)
  {
    if (!($data instanceof Swift_File)) throw new Exception("Parameter 1 of " . __METHOD__ . " must be instance of Swift_File");
    parent::setData($data, $read_filename);
    $img_data = @getimagesize($data->getPath());
    if (!$img_data)
    {
      throw new Swift_Message_MimeException(
      "Cannot use file '" . $data->getPath() . "' as image since getimagesize() was unable to detect a file format. " .
      "Try using Swift_Message_EmbeddedFile instead");
    }
    $type = image_type_to_mime_type($img_data[2]);
    $this->setContentType($type);
    if (!$this->getFileName()) $this->setFileName($data->getFileName());
  }
}
