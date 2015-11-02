<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class    AddConfToFile
{
    public $fd;
    public $file;
    public $mode;
    public $error = false;

    public function __construct($file, $mode = 'r+')
    {
        $this->file = $file;
        $this->mode = $mode;
        $this->checkFile($file);
        if ($mode == 'w' and !$this->error) {
            if (!$res = @fwrite($this->fd, '<?php'."\n")) {
                $this->error = 6;
            }
        }
    }

    public function __destruct()
    {
        if (!$this->error) {
            @fclose($this->fd);
        }
    }

    private function checkFile($file)
    {
        if (!$fd = @fopen($this->file, $this->mode)) {
            $this->error = 5;
        } elseif (!is_writable($this->file)) {
            $this->error = 6;
        }
        $this->fd = $fd;
    }

    public function writeInFile($name, $data)
    {
        if ($name == '_PS_VERSION_' && strpos($this->file, 'settings.inc') !== false) {
            $string = 'if (!defined(\''.$name.'\'))'."\n\t".'define(\''.$name.'\', \''.$this->checkString($data).'\');'."\n";
        } else {
            $string = 'define(\''.$name.'\', \''.$this->checkString($data).'\');'."\n";
        }

        if (!$res = @fwrite($this->fd, $string)) {
            $this->error = 6;
            return false;
        }
        return true;
    }

    public function writeEndTagPhp()
    {
        if (!$res = @fwrite($this->fd, '?>'."\n")) {
            $this->error = 6;
            return false;
        }
        return true;
    }

    public function checkString($string)
    {
        if (get_magic_quotes_gpc()) {
            $string = stripslashes($string);
        }
        if (!is_numeric($string)) {
            $string = addslashes($string);
            $string = str_replace(array("\n", "\r"), '', $string);
        }
        return $string;
    }
}
