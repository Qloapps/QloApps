<?php
//============================================================+
// File name   : example_025.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 025 for TCPDF class
//               Object Transparency
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
 * @abstract TCPDF - Example: Object Transparency
 * @author Nicola Asuni
 * @since 2008-03-04
 * @group drawing
 * @group pdf
 */

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Nicola Asuni');
$pdf->setTitle('TCPDF Example 025');
$pdf->setSubject('TCPDF Tutorial');
$pdf->setKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 025', PDF_HEADER_STRING);

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

// set font
$pdf->setFont('helvetica', '', 12);

// add a page
$pdf->AddPage();

$txt = 'You can set the transparency of PDF objects using the setAlpha() method.';
$pdf->Write(0, $txt, '', 0, '', true, 0, false, false, 0);

/*
 * setAlpha() gives transparency support. You can set the
 * alpha channel from 0 (fully transparent) to 1 (fully
 * opaque). It applies to all elements (text, drawings,
 * images).
 */

$pdf->setLineWidth(2);

// draw opaque red square
$pdf->setFillColor(255, 0, 0);
$pdf->setDrawColor(127, 0, 0);
$pdf->Rect(30, 40, 60, 60, 'DF');

// set alpha to semi-transparency
$pdf->setAlpha(0.5);

// draw green square
$pdf->setFillColor(0, 255, 0);
$pdf->setDrawColor(0, 127, 0);
$pdf->Rect(50, 60, 60, 60, 'DF');

// draw blue square
$pdf->setFillColor(0, 0, 255);
$pdf->setDrawColor(0, 0, 127);
$pdf->Rect(70, 80, 60, 60, 'DF');

// draw jpeg image
$pdf->Image('images/image_demo.jpg', 90, 100, 60, 60, '', 'http://www.tcpdf.org', '', true, 72);

// restore full opacity
$pdf->setAlpha(1);

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_025.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
