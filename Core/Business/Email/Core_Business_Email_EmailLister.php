<?php
/**
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class Core_Business_Email_EmailLister
{
    private $filesystem;

    public function __construct(Core_Foundation_FileSystem_FileSystem $fs)
    {
        // Register dependencies
        $this->filesystem = $fs;
    }

    /**
     * Return the list of available mails
     * @param null $lang
     * @param null $dir
     * @return array|null
     */
    public function getAvailableMails($dir)
    {
        if (!is_dir($dir)) {
            return null;
        }

        $mail_directory = $this->filesystem->listEntriesRecursively($dir);
        $mail_list = array();

        // Remove unwanted .html / .txt / .tpl / .php / . / ..
        foreach ($mail_directory as $mail) {
            if (strpos($mail->getFilename(), '.') !== false) {
                $tmp = explode('.', $mail->getFilename());

                // Check for filename existence (left part) and if extension is html (right part)
                if (($tmp === false || !isset($tmp[0])) || (isset($tmp[1]) && $tmp[1] !== 'html')) {
                    continue;
                }

                $mail_name_no_ext = $tmp[0];
                if (!in_array($mail_name_no_ext, $mail_list)) {
                    $mail_list[] = $mail_name_no_ext;
                }
            }
        }

        return $mail_list;
    }


    /**
     * Give in input getAvailableMails(), will output a human readable and proper string name
     * @return string
     */
    public function getCleanedMailName($mail_name)
    {
        if (strpos($mail_name, '.') !== false) {
            $tmp = explode('.', $mail_name);

            if ($tmp === false || !isset($tmp[0])) {
                return $mail_name;
            }

            $mail_name = $tmp[0];
        }

        return ucfirst(str_replace(array('_', '-'), ' ', $mail_name));
    }
}
