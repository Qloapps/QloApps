<?php

/**
 * A Swift Mailer plugin to download remote images and stylesheets then embed them.
 * This also embeds local files from disk.
 * Please read the LICENSE file
 * @package Swift_Plugin
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Events_BeforeSendListener");

/**
 * Swift FileEmbedder Plugin to embed remote files.
 * Scans a Swift_Message instance for remote files and then embeds them before sending.
 * This also embeds local files from disk.
 * @package Swift_Plugin
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Plugin_FileEmbedder implements Swift_Events_BeforeSendListener
{
  /**
   * True if remote files will be embedded.
   * @var boolean
   */
  protected $embedRemoteFiles = true;
  /**
   * True if local files will be embedded.
   * @var boolean
   */
  protected $embedLocalFiles = true;
  /**
   * (X)HTML tag defintions listing allowed attributes and extensions.
   * @var array
   */
  protected $definitions = array(
    "img" => array(
      "attributes" => array("src"),
      "extensions" => array("gif", "png", "jpg",  "jpeg", "pjpeg")
    ),
    "link" => array(
      "attributes" => array("href"),
      "extensions" => array("css")
    ),
    "script" => array(
      "attributes" => array("src"),
      "extensions" => array("js")
    ));
  /**
   * Protocols which may be used to download a remote file.
   * @var array
   */
  protected $protocols = array(
    "http" => "http",
    "https" => "https",
    "ftp" => "ftp"
  );
  /**
   * A PCRE regexp which will be passed via sprintf() to produce a complete pattern.
   * @var string
   */
  protected $remoteFilePatternFormat = "~
    (<(?:%s)\\s+[^>]*?                   #Opening tag followed by (possible) attributes
    (?:%s)=((?:\"|')?))                  #Permitted attributes followed by (possible) quotation marks
    ((?:%s)://[\\x01-\\x7F]*?(?:%s)?)    #Remote URL (matching a permitted protocol)
    (\\2[^>]*>)                          #Remaining attributes followed by end of tag
    ~isx";
  /**
   * A PCRE regexp which will be passed via sprintf() to produce a complete pattern.
   * @var string
   */
  protected $localFilePatternFormat = "~
    (<(?:%s)\\s+[^>]*?                               #Opening tag followed by (possible) attributes
    (?:%s)=((?:\"|')?))                              #Permitted attributes followed by (possible) quotation marks
    ((?:/|[a-z]:\\\\|[a-z]:/)[\\x01-\\x7F]*?(?:%s)?) #Local, absolute path
    (\\2[^>]*>)                                      #Remaining attributes followed by end of tag
    ~isx";
  /**
   * A list of extensions mapping to their usual MIME types.
   * @var array
   */
  protected $mimeTypes = array(
    "gif" => "image/gif",
    "png" => "image/png",
    "jpeg" => "image/jpeg",
    "jpg" => "image/jpeg",
    "pjpeg" => "image/pjpeg",
    "js" => "text/javascript",
    "css" => "text/css");
  /**
   * Child IDs of files already embedded.
   * @var array
   */
  protected $registeredFiles = array();
  
  /**
   * Get the MIME type based upon the extension.
   * @param string The extension (sans the dot).
   * @return string
   */
  public function getType($ext)
  {
    $ext = strtolower($ext);
    if (isset($this->mimeTypes[$ext]))
    {
      return $this->mimeTypes[$ext];
    }
    else return null;
  }
  /**
   * Add a new MIME type defintion (or overwrite an existing one).
   * @param string The extension (sans the dot)
   * @param string The MIME type (e.g. image/jpeg)
   */
  public function addType($ext, $type)
  {
    $this->mimeTypes[strtolower($ext)] = strtolower($type);
  }
  /**
   * Set the PCRE pattern which finds -full- HTML tags and copies the path for a local file into a backreference.
   * The pattern contains three %s replacements for sprintf().
   * First replacement is the tag name (e.g. img)
   * Second replacement is the attribute name (e.g. src)
   * Third replacement is the file extension (e.g. jpg)
   * This pattern should contain the full URL in backreference index 3.
   * @param string sprintf() format string containing a PCRE regexp.
   */
  public function setLocalFilePatternFormat($format)
  {
    $this->localFilePatternFormat = $format;
  }
  /**
   * Gets the sprintf() format string for the PCRE pattern to scan for remote files.
   * @return string
   */
  public function getLocalFilePatternFormat()
  {
    return $this->localFilePatternFormat;
  }
  /**
   * Set the PCRE pattern which finds -full- HTML tags and copies the URL for the remote file into a backreference.
   * The pattern contains four %s replacements for sprintf().
   * First replacement is the tag name (e.g. img)
   * Second replacement is the attribute name (e.g. src)
   * Third replacement is the protocol (e.g. http)
   * Fourth replacement is the file extension (e.g. jpg)
   * This pattern should contain the full URL in backreference index 3.
   * @param string sprintf() format string containing a PCRE regexp.
   */
  public function setRemoteFilePatternFormat($format)
  {
    $this->remoteFilePatternFormat = $format;
  }
  /**
   * Gets the sprintf() format string for the PCRE pattern to scan for remote files.
   * @return string
   */
  public function getRemoteFilePatternFormat()
  {
    return $this->remoteFilePatternFormat;
  }
  /**
   * Add a new protocol which can be used to download files.
   * Protocols should not include the "://" portion.  This method expects alphanumeric characters only.
   * @param string The protocol name (e.g. http or ftp)
   */
  public function addProtocol($prot)
  {
    $prot = strtolower($prot);
    $this->protocols[$prot] = $prot;
  }
  /**
   * Remove a protocol from the list of allowed protocols once added.
   * @param string The name of the protocol (e.g. http)
   */
  public function removeProtocol($prot)
  {
    unset($this->protocols[strtolower($prot)]);
  }
  /**
   * Get a list of all registered protocols.
   * @return array
   */
  public function getProtocols()
  {
    return array_values($this->protocols);
  }
  /**
   * Add, or modify a tag definition.
   * This affects how the plugins scans for files to download.
   * @param string The name of a tag to search for (e.g. img)
   * @param string The name of attributes to look for (e.g. src).  You can pass an array if there are multiple possibilities.
   * @param array A list of extensions to allow (sans dot). If there's only one you can just pass a string.
   */
  public function setTagDefinition($tag, $attributes, $extensions)
  {
    $tag = strtolower($tag);
    $attributes = (array)$attributes;
    $extensions = (array)$extensions;
    
    if (empty($tag) || empty($attributes) || empty($extensions))
    {
      return null;
    }
    
    $this->definitions[$tag] = array("attributes" => $attributes, "extensions" => $extensions);
    return true;
  }
  /**
   * Remove a tag definition for remote files.
   * @param string The name of the tag
   */
  public function removeTagDefinition($tag)
  {
    unset($this->definitions[strtolower($tag)]);
  }
  /**
   * Get a tag definition.
   * Returns an array with indexes "attributes" and "extensions".
   * Each element is an array listing the values within it.
   * @param string The name of the tag
   * @return array
   */
  public function getTagDefinition($tag)
  {
    $tag = strtolower($tag);
    if (isset($this->definitions[$tag])) return $this->definitions[$tag];
    else return null;
  }
  /**
   * Get the PCRE pattern for a remote file based on the tag name.
   * @param string The name of the tag
   * @return string
   */
  public function getRemoteFilePattern($tag_name)
  {
    $tag_name = strtolower($tag_name);
    $pattern_format = $this->getRemoteFilePatternFormat();
    if ($def = $this->getTagDefinition($tag_name))
    {
      $pattern = sprintf($pattern_format, $tag_name, implode("|", $def["attributes"]),
        implode("|", $this->getProtocols()), implode("|", $def["extensions"]));
      return $pattern;
    }
    else return null;
  }
  /**
   * Get the PCRE pattern for a local file based on the tag name.
   * @param string The name of the tag
   * @return string
   */
  public function getLocalFilePattern($tag_name)
  {
    $tag_name = strtolower($tag_name);
    $pattern_format = $this->getLocalFilePatternFormat();
    if ($def = $this->getTagDefinition($tag_name))
    {
      $pattern = sprintf($pattern_format, $tag_name, implode("|", $def["attributes"]),
        implode("|", $def["extensions"]));
      return $pattern;
    }
    else return null;
  }
  /**
   * Register a file which has been downloaded so it doesn't need to be downloaded twice.
   * @param string The remote URL
   * @param string The ID as attached in the message
   * @param Swift_Message_EmbeddedFile The file object itself
   */
  public function registerFile($url, $cid, $file)
  {
    $url = strtolower($url);
    if (!isset($this->registeredFiles[$url])) $this->registeredFiles[$url] = array("cids" => array(), "obj" => null);
    $this->registeredFiles[$url]["cids"][] = $cid;
    if (empty($this->registeredFiles[$url]["obj"])) $this->registeredFiles[$url]["obj"] = $file;
  }
  /**
   * Turn on or off remote file embedding.
   * @param boolean
   */
  public function setEmbedRemoteFiles($set)
  {
    $this->embedRemoteFiles = (bool)$set;
  }
  /**
   * Returns true if remote files can be embedded, or false if not.
   * @return boolean
   */
  public function getEmbedRemoteFiles()
  {
    return $this->embedRemoteFiles;
  }
  /**
   * Turn on or off local file embedding.
   * @param boolean
   */
  public function setEmbedLocalFiles($set)
  {
    $this->embedLocalFiles = (bool)$set;
  }
  /**
   * Returns true if local files can be embedded, or false if not.
   * @return boolean
   */
  public function getEmbedLocalFiles()
  {
    return $this->embedLocalFiles;
  }
  /**
   * Callback method for preg_replace().
   * Embeds files which have been found during scanning.
   * @param array Backreferences from preg_replace()
   * @return string The tag with it's URL replaced with a CID
   */
  protected function embedRemoteFile($matches)
  {
    $url = preg_replace("~^([^#]+)#.*\$~s", "\$1", $matches[3]);
    $bits = parse_url($url);
    $ext = preg_replace("~^.*?\\.([^\\.]+)\$~s", "\$1", $bits["path"]);
    
    $lower_url = strtolower($url);
    if (array_key_exists($lower_url, $this->registeredFiles))
    {
      $registered = $this->registeredFiles[$lower_url];
      foreach ($registered["cids"] as $cid)
      {
        if ($this->message->hasChild($cid))
        {
          return $matches[1] . $cid . $matches[4];
        }
      }
      //If we get here the file is downloaded, but not embedded
      $cid = $this->message->attach($registered["obj"]);
      $this->registerFile($url, $cid, $registered["obj"]);
      return $matches[1] . $cid . $matches[4];
    }
    $filedata = @file_get_contents($url);
    if (!$filedata)
    {
      return $matches[1] . $matches[3] . $matches[4];
    }
    $filename = preg_replace("~^.*/([^/]+)\$~s", "\$1", $url);
    $att = new Swift_Message_EmbeddedFile($filedata, $filename, $this->getType($ext));
    $id = $this->message->attach($att);
    $this->registerFile($url, $id, $att);
    return $matches[1] . $id . $matches[4];
  }
  /**
   * Callback method for preg_replace().
   * Embeds files which have been found during scanning.
   * @param array Backreferences from preg_replace()
   * @return string The tag with it's path replaced with a CID
   */
  protected function embedLocalFile($matches)
  {
    $path = realpath($matches[3]);
    if (!$path)
    {
      return $matches[1] . $matches[3] . $matches[4];
    }
    $ext = preg_replace("~^.*?\\.([^\\.]+)\$~s", "\$1", $path);
    
    $lower_path = strtolower($path);
    if (array_key_exists($lower_path, $this->registeredFiles))
    {
      $registered = $this->registeredFiles[$lower_path];
      foreach ($registered["cids"] as $cid)
      {
        if ($this->message->hasChild($cid))
        {
          return $matches[1] . $cid . $matches[4];
        }
      }
      //If we get here the file is downloaded, but not embedded
      $cid = $this->message->attach($registered["obj"]);
      $this->registerFile($path, $cid, $registered["obj"]);
      return $matches[1] . $cid . $matches[4];
    }
    $filename = basename($path);
    $att = new Swift_Message_EmbeddedFile(new Swift_File($path), $filename, $this->getType($ext));
    $id = $this->message->attach($att);
    $this->registerFile($path, $id, $att);
    return $matches[1] . $id . $matches[4];
  }
  /**
   * Empty out the cache of registered files.
   */
  public function clearCache()
  {
    $this->registeredFiles = null;
    $this->registeredFiles = array();
  }
  /**
   * Swift's BeforeSendListener required method.
   * Runs just before Swift sends a message.  Here is where we do all the replacements.
   * @param Swift_Events_SendEvent
   */
  public function beforeSendPerformed(Swift_Events_SendEvent $e)
  {
    $this->message = $e->getMessage();
    
    foreach ($this->message->listChildren() as $id)
    {
      $part = $this->message->getChild($id);
      $body = $part->getData();
      if (!is_string($body) || substr(strtolower($part->getContentType()), 0, 5) != "text/") continue;
      
      foreach ($this->definitions as $tag_name => $def)
      {
        if ($this->getEmbedRemoteFiles())
        {
          $re = $this->getRemoteFilePattern($tag_name);
          $body = preg_replace_callback($re, array($this, "embedRemoteFile"), $body);
        }
        
        if ($this->getEmbedLocalFiles())
        {
          $re = $this->getLocalFilePattern($tag_name);
          $body = preg_replace_callback($re, array($this, "embedLocalFile"), $body);
        }
      }
      
      $part->setData($body);
    }
  }
}
