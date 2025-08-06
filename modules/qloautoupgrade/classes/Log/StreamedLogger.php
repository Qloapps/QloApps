<?php

/*
 * 2007-2018 PrestaShop
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
 *  @copyright  2007-2018 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\Module\AutoUpgrade\Log;

/**
 * Logger to use when the messages can be seen as soon as they are created.
 * For instance, in a CLI context.
 */
class StreamedLogger extends Logger
{
    /**
     * @var int Minimum criticity of level to display
     */
    protected $filter = self::INFO;

    /**
     * @var resource File handler of standard output
     */
    protected $out;

    /**
     * @var resource File handler of standard error
     */
    protected $err;

    public function __construct()
    {
        $this->out = fopen('php://stdout', 'w');
        $this->err = fopen('php://stderr', 'w');
    }

    public function __destruct()
    {
        fclose($this->out);
        fclose($this->err);
    }

    /**
     * Check the verbosity allows the message to be displayed.
     *
     * @param int $level
     *
     * @return bool
     */
    public function isFiltered($level)
    {
        return $level < $this->filter;
    }

    /**
     * {@inherit}.
     */
    public function log($level, $message, array $context = array())
    {
        if (empty($message)) {
            return;
        }

        $log = self::$levels[$level] . ' - ' . $message . PHP_EOL;

        if ($level > self::ERROR) {
            fwrite($this->err, $log);
        }

        if (!$this->isFiltered($level)) {
            fwrite($this->out, $log);
        }
    }

    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Set the verbosity of the logger.
     *
     * @param int $filter
     *
     * @return $this
     */
    public function setFilter($filter)
    {
        if (!array_key_exists($filter, self::$levels)) {
            throw new \Exception('Unknown level ' . $filter);
        }
        $this->filter = $filter;

        return $this;
    }
}
