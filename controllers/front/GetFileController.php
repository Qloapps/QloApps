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

class GetFileControllerCore extends FrontController
{
    protected $display_header = false;
    protected $display_footer = false;

    public function init()
    {
        if (isset($this->context->employee) && $this->context->employee->isLoggedBack() && Tools::getValue('file')) {
            // Admin can directly access to file
            $filename = Tools::getValue('file');
            if (!Validate::isSha1($filename)) {
                die(Tools::displayError());
            }
            $file = _PS_DOWNLOAD_DIR_.strval(preg_replace('/\.{2,}/', '.', $filename));
            $filename = ProductDownload::getFilenameFromFilename(Tools::getValue('file'));
            if (empty($filename)) {
                $newFileName = Tools::getValue('filename');
                if (!empty($newFileName)) {
                    $filename = Tools::getValue('filename');
                } else {
                    $filename = 'file';
                }
            }

            if (!file_exists($file)) {
                Tools::redirect('index.php');
            }
        } else {
            if (!($key = Tools::getValue('key'))) {
                $this->displayCustomError('Invalid key.');
            }

            Tools::setCookieLanguage();
            if (!$this->context->customer->isLogged() && !Tools::getValue('secure_key') && !Tools::getValue('id_order')) {
                Tools::redirect('index.php?controller=authentication&back=get-file.php&key='.$key);
            } elseif (!$this->context->customer->isLogged() && Tools::getValue('secure_key') && Tools::getValue('id_order')) {
                $order = new Order((int)Tools::getValue('id_order'));
                if (!Validate::isLoadedObject($order)) {
                    $this->displayCustomError('Invalid key.');
                }
                if ($order->secure_key != Tools::getValue('secure_key')) {
                    $this->displayCustomError('Invalid key.');
                }
            }

            /* Key format: <sha1-filename>-<hashOrder> */
            $tmp = explode('-', $key);
            if (count($tmp) != 2) {
                $this->displayCustomError('Invalid key.');
            }

            $filename = $tmp[0];
            $hash = $tmp[1];

            if (!($info = OrderDetail::getDownloadFromHash($hash))) {
                $this->displayCustomError('This product does not exist in our store.');
            }

            /* Product no more present in catalog */
            if (!isset($info['id_product_download']) || empty($info['id_product_download'])) {
                $this->displayCustomError('This product has been deleted.');
            }

            if (!Validate::isFileName($info['filename']) || !file_exists(_PS_DOWNLOAD_DIR_.$info['filename'])) {
                $this->displayCustomError('This file no longer exists.');
            }

            if (isset($info['product_quantity_refunded']) && isset($info['product_quantity_return']) &&
                ($info['product_quantity_refunded'] > 0 || $info['product_quantity_return'] > 0)) {
                $this->displayCustomError('This product has been refunded.');
            }

            $now = time();

            $product_deadline = strtotime($info['download_deadline']);
            if ($now > $product_deadline && $info['download_deadline'] != '0000-00-00 00:00:00') {
                $this->displayCustomError('The product deadline is in the past.');
            }

            $customer_deadline = strtotime($info['date_expiration']);
            if ($now > $customer_deadline && $info['date_expiration'] != '0000-00-00 00:00:00') {
                $this->displayCustomError('Expiration date has passed, you cannot download this product');
            }

            if ($info['download_nb'] >= $info['nb_downloadable'] && $info['nb_downloadable']) {
                $this->displayCustomError('You have reached the maximum number of allowed downloads.');
            }

            /* Access is authorized -> increment download value for the customer */
            OrderDetail::incrementDownload($info['id_order_detail']);

            $file = _PS_DOWNLOAD_DIR_.$info['filename'];
            $filename = $info['display_filename'];
        }

        /* Detect mime content type */
        $mimeType = false;
        if (function_exists('finfo_open')) {
            $finfo = @finfo_open(FILEINFO_MIME);
            $mimeType = @finfo_file($finfo, $file);
            @finfo_close($finfo);
        } elseif (function_exists('mime_content_type')) {
            $mimeType = @mime_content_type($file);
        } elseif (function_exists('exec')) {
            $mimeType = trim(@exec('file -b --mime-type '.escapeshellarg($file)));
            if (!$mimeType) {
                $mimeType = trim(@exec('file --mime '.escapeshellarg($file)));
            }
            if (!$mimeType) {
                $mimeType = trim(@exec('file -bi '.escapeshellarg($file)));
            }
        }

        if (empty($mimeType)) {
            $bName = basename($filename);
            $bName = explode('.', $bName);
            $bName = strtolower($bName[count($bName) - 1]);

            $mimeTypes = array(
            'ez' => 'application/andrew-inset',
            'hqx' => 'application/mac-binhex40',
            'cpt' => 'application/mac-compactpro',
            'doc' => 'application/msword',
            'oda' => 'application/oda',
            'pdf' => 'application/pdf',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            'smi' => 'application/smil',
            'smil' => 'application/smil',
            'wbxml' => 'application/vnd.wap.wbxml',
            'wmlc' => 'application/vnd.wap.wmlc',
            'wmlsc' => 'application/vnd.wap.wmlscriptc',
            'bcpio' => 'application/x-bcpio',
            'vcd' => 'application/x-cdlink',
            'pgn' => 'application/x-chess-pgn',
            'cpio' => 'application/x-cpio',
            'csh' => 'application/x-csh',
            'dcr' => 'application/x-director',
            'dir' => 'application/x-director',
            'dxr' => 'application/x-director',
            'dvi' => 'application/x-dvi',
            'spl' => 'application/x-futuresplash',
            'gtar' => 'application/x-gtar',
            'hdf' => 'application/x-hdf',
            'js' => 'application/x-javascript',
            'skp' => 'application/x-koan',
            'skd' => 'application/x-koan',
            'skt' => 'application/x-koan',
            'skm' => 'application/x-koan',
            'latex' => 'application/x-latex',
            'nc' => 'application/x-netcdf',
            'cdf' => 'application/x-netcdf',
            'sh' => 'application/x-sh',
            'shar' => 'application/x-shar',
            'swf' => 'application/x-shockwave-flash',
            'sit' => 'application/x-stuffit',
            'sv4cpio' => 'application/x-sv4cpio',
            'sv4crc' => 'application/x-sv4crc',
            'tar' => 'application/x-tar',
            'tcl' => 'application/x-tcl',
            'tex' => 'application/x-tex',
            'texinfo' => 'application/x-texinfo',
            'texi' => 'application/x-texinfo',
            't' => 'application/x-troff',
            'tr' => 'application/x-troff',
            'roff' => 'application/x-troff',
            'man' => 'application/x-troff-man',
            'me' => 'application/x-troff-me',
            'ms' => 'application/x-troff-ms',
            'ustar' => 'application/x-ustar',
            'src' => 'application/x-wais-source',
            'xhtml' => 'application/xhtml+xml',
            'xht' => 'application/xhtml+xml',
            'zip' => 'application/zip',
            'au' => 'audio/basic',
            'snd' => 'audio/basic',
            'mid' => 'audio/midi',
            'midi' => 'audio/midi',
            'kar' => 'audio/midi',
            'mpga' => 'audio/mpeg',
            'mp2' => 'audio/mpeg',
            'mp3' => 'audio/mpeg',
            'aif' => 'audio/x-aiff',
            'aiff' => 'audio/x-aiff',
            'aifc' => 'audio/x-aiff',
            'm3u' => 'audio/x-mpegurl',
            'ram' => 'audio/x-pn-realaudio',
            'rm' => 'audio/x-pn-realaudio',
            'rpm' => 'audio/x-pn-realaudio-plugin',
            'ra' => 'audio/x-realaudio',
            'wav' => 'audio/x-wav',
            'pdb' => 'chemical/x-pdb',
            'xyz' => 'chemical/x-xyz',
            'bmp' => 'image/bmp',
            'gif' => 'image/gif',
            'ief' => 'image/ief',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'jpe' => 'image/jpeg',
            'png' => 'image/png',
            'tiff' => 'image/tiff',
            'tif' => 'image/tif',
            'djvu' => 'image/vnd.djvu',
            'djv' => 'image/vnd.djvu',
            'wbmp' => 'image/vnd.wap.wbmp',
            'ras' => 'image/x-cmu-raster',
            'pnm' => 'image/x-portable-anymap',
            'pbm' => 'image/x-portable-bitmap',
            'pgm' => 'image/x-portable-graymap',
            'ppm' => 'image/x-portable-pixmap',
            'rgb' => 'image/x-rgb',
            'xbm' => 'image/x-xbitmap',
            'xpm' => 'image/x-xpixmap',
            'xwd' => 'image/x-windowdump',
            'igs' => 'model/iges',
            'iges' => 'model/iges',
            'msh' => 'model/mesh',
            'mesh' => 'model/mesh',
            'silo' => 'model/mesh',
            'wrl' => 'model/vrml',
            'vrml' => 'model/vrml',
            'css' => 'text/css',
            'html' => 'text/html',
            'htm' => 'text/html',
            'asc' => 'text/plain',
            'txt' => 'text/plain',
            'rtx' => 'text/richtext',
            'rtf' => 'text/rtf',
            'sgml' => 'text/sgml',
            'sgm' => 'text/sgml',
            'tsv' => 'text/tab-seperated-values',
            'wml' => 'text/vnd.wap.wml',
            'wmls' => 'text/vnd.wap.wmlscript',
            'etx' => 'text/x-setext',
            'xml' => 'text/xml',
            'xsl' => 'text/xml',
            'mpeg' => 'video/mpeg',
            'mpg' => 'video/mpeg',
            'mpe' => 'video/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            'mxu' => 'video/vnd.mpegurl',
            'avi' => 'video/x-msvideo',
            'movie' => 'video/x-sgi-movie',
            'ice' => 'x-conference-xcooltalk');

            if (isset($mimeTypes[$bName])) {
                $mimeType = $mimeTypes[$bName];
            } else {
                $mimeType = 'application/octet-stream';
            }
        }

        if (ob_get_level() && ob_get_length() > 0) {
            ob_end_clean();
        }

        /* Set headers for download */
        header('Content-Transfer-Encoding: binary');
        header('Content-Type: '.$mimeType);
        header('Content-Length: '.filesize($file));
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        //prevents max execution timeout, when reading large files
        @set_time_limit(0);
        $fp = fopen($file, 'rb');

        if ($fp && is_resource($fp)) {
            while (!feof($fp)) {
                echo fgets($fp, 16384);
            }
        }

        exit;
    }

    /**
     * Display an error message with js
     * and redirect using js function
     *
     * @param string $msg
     */
    protected function displayCustomError($msg)
    {
        $translations = array(
        'Invalid key.' => Tools::displayError('Invalid key.'),
        'This product does not exist in our store.' => Tools::displayError('This product does not exist in our store.'),
        'This product has been deleted.' => Tools::displayError('This product has been deleted.'),
        'This file no longer exists.'    => Tools::displayError('This file no longer exists.'),
        'This product has been refunded.' => Tools::displayError('This product has been refunded.'),
        'The product deadline is in the past.' => Tools::displayError('The product deadline is in the past.'),
        'Expiration date exceeded' => Tools::displayError('The product expiration date has passed, preventing you from download this product.'),
        'Expiration date has passed, you cannot download this product' => Tools::displayError('Expiration date has passed, you cannot download this product.'),
        'You have reached the maximum number of allowed downloads.' => Tools::displayError('You have reached the maximum number of downloads allowed.'));
        ?>
		<script type="text/javascript">
		//<![CDATA[
		alert("<?php echo isset($translations[$msg]) ? html_entity_decode($translations[$msg], ENT_QUOTES, 'utf-8') : html_entity_decode($msg, ENT_QUOTES, 'utf-8');
        ?>");
		window.location.href = '<?php echo __PS_BASE_URI__ ?>';
		//]]>
		</script>
		<?php
        exit();
    }
}
