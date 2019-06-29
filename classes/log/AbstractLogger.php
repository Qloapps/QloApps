<?php
/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

abstract class AbstractLoggerCore
{
    public $level;
    protected $level_value = array(
        0 => 'DEBUG',
        1 => 'INFO',
        2 => 'WARNING',
        3 => 'ERROR',
    );

    const DEBUG = 0;
    const INFO = 1;
    const WARNING = 2;
    const ERROR = 3;

    public function __construct($level = self::INFO)
    {
        if (array_key_exists((int)$level, $this->level_value)) {
            $this->level = $level;
        } else {
            $this->level = self::INFO;
        }
    }

    /**
    * Log the message
    *
    * @param string message
    * @param level
    */
    abstract protected function logMessage($message, $level);

    /**
     * Check the level and log the message if needed
     *
     * @param string message
     * @param level
     */
    public function log($message, $level = self::DEBUG)
    {
        if ($level >= $this->level) {
            $this->logMessage($message, $level);
        }
    }

    /**
    * Log a debug message
    *
    * @param string message
    */
    public function logDebug($message)
    {
        $this->log($message, self::DEBUG);
    }

    /**
    * Log an info message
    *
    * @param string message
    */
    public function logInfo($message)
    {
        $this->log($message, self::INFO);
    }

    /**
    * Log a warning message
    *
    * @param string message
    */
    public function logWarning($message)
    {
        $this->log($message, self::WARNING);
    }

    /**
    * Log an error message
    *
    * @param string message
    */
    public function logError($message)
    {
        $this->log($message, self::ERROR);
    }
}
