<?php
//============================================================+
// File name   : tcpdf_barcodes_2d_include.php
// Begin       : 2013-05-19
// Last Update : 2013-05-19
//
// Description : Search and include the TCPDF Barcode 1D class.
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Search and include the TCPDF Barcode 2D class.
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Include the main class.
 * @author Nicola Asuni
 * @since 2013-05-19
 */

// Include the TCPDF 2D barcode class (search the class on the following directories).
$tcpdf_barcodes_2d_include_dirs = array(
    realpath(dirname(__FILE__) . '/../../tcpdf_barcodes_2d.php'),// True source file
	realpath('../../tcpdf_barcodes_2d.php'),// Relative from $PWD
	'/usr/share/php/tcpdf/tcpdf_barcodes_2d.php',
	'/usr/share/tcpdf/tcpdf_barcodes_2d.php',
	'/usr/share/php-tcpdf/tcpdf_barcodes_2d.php',
	'/var/www/tcpdf/tcpdf_barcodes_2d.php',
	'/var/www/html/tcpdf/tcpdf_barcodes_2d.php',
	'/usr/local/apache2/htdocs/tcpdf/tcpdf_barcodes_2d.php'
);
foreach ($tcpdf_barcodes_2d_include_dirs as $tcpdf_barcodes_2d_include_path) {
	if (@file_exists($tcpdf_barcodes_2d_include_path)) {
		require_once($tcpdf_barcodes_2d_include_path);
		break;
	}
}

//============================================================+
// END OF FILE
//============================================================+
