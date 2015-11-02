<?php

/**
 * Swift File Exception
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/ClassLoader.php";
Swift_ClassLoader::load("Swift_Exception");

/**
 * Swift File Exception
 * @package Swift
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_FileException extends Swift_Exception
{
}
