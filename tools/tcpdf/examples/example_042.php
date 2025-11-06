<?php
//============================================================+
// File name   : example_042.php
// Begin       : 2008-12-23
// Last Update : 2013-05-14
//
// Description : Example 042 for TCPDF class
//               Test Image with alpha channel
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
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Test Image with alpha channel
 * @author Nicola Asuni
 * @since 2008-12-23
 * @group image
 * @group pdf
 */

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Nicola Asuni');
$pdf->setTitle('TCPDF Example 042');
$pdf->setSubject('TCPDF Tutorial');
$pdf->setKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 042', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
$pdf->setFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set JPEG quality
//$pdf->setJPEGQuality(75);

$pdf->setFont('helvetica', '', 18);

// add a page
$pdf->AddPage();

// create background text
$background_text = str_repeat('TCPDF test PNG Alpha Channel ', 50);
$pdf->MultiCell(0, 5, $background_text, 0, 'J', 0, 2, '', '', true, 0, false);

// --- Method (A) ------------------------------------------
// the Image() method recognizes the alpha channel embedded on the image:

$pdf->Image('images/image_with_alpha.png', 50, 50, 100, '', '', 'http://www.tcpdf.org', '', false, 300);

// --- Method (B) ------------------------------------------
// provide image + separate 8-bit mask

// first embed mask image (w, h, x and y will be ignored, the image will be scaled to the target image's size)
$mask = $pdf->Image('images/alpha.png', 50, 140, 100, '', '', '', '', false, 300, '', true);

// embed image, masked with previously embedded mask
$pdf->Image('images/img.png', 50, 140, 100, '', '', 'http://www.tcpdf.org', '', false, 300, '', false, $mask);

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_042.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
