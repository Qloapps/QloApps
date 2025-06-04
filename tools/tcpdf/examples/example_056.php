<?php
//============================================================+
// File name   : example_056.php
// Begin       : 2010-03-26
// Last Update : 2013-09-30
//
// Description : Example 056 for TCPDF class
//               Crop marks and color registration bars
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
 * @abstract TCPDF - Example: Crop marks and color registration bars
 * @author Nicola Asuni
 * @since 2010-03-26
 * @group color
 * @group pdf
 */

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Nicola Asuni');
$pdf->setTitle('TCPDF Example 056');
$pdf->setSubject('TCPDF Tutorial');
$pdf->setKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 056', PDF_HEADER_STRING);

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
$pdf->setFont('helvetica', '', 18);

// add a page
$pdf->AddPage();

$pdf->Write(0, 'Example of Registration Marks, Crop Marks and Color Bars', '', 0, 'L', true, 0, false, false, 0);

$pdf->Ln(5);

// color registration bars

// A,W,R,G,B,C,M,Y,K,RGB,CMYK,ALL,ALLSPOT,<SPOT_COLOR_NAME>
$pdf->colorRegistrationBar(50, 70, 40, 40, true, false, 'A,R,G,B,C,M,Y,K');
$pdf->colorRegistrationBar(90, 70, 40, 40, true, true, 'A,R,G,B,C,M,Y,K');
$pdf->colorRegistrationBar(50, 115, 80, 5, false, true, 'A,W,R,G,B,C,M,Y,K,ALL');
$pdf->colorRegistrationBar(135, 70, 5, 50, false, false, 'A,W,R,G,B,C,M,Y,K,ALL');

// corner crop marks

$pdf->cropMark(50, 70, 10, 10, 'TL');
$pdf->cropMark(140, 70, 10, 10, 'TR');
$pdf->cropMark(50, 120, 10, 10, 'BL');
$pdf->cropMark(140, 120, 10, 10, 'BR');

// various crop marks

$pdf->cropMark(95, 65, 5, 5, 'LEFT,TOP,RIGHT', array(255,0,0));
$pdf->cropMark(95, 125, 5, 5, 'LEFT,BOTTOM,RIGHT', array(255,0,0));

$pdf->cropMark(45, 95, 5, 5, 'TL,BL', array(0,255,0));
$pdf->cropMark(145, 95, 5, 5, 'TR,BR', array(0,255,0));

$pdf->cropMark(95, 140, 5, 5, 'A,D', array(0,0,255));

// registration marks

$pdf->registrationMark(40, 60, 5, false);
$pdf->registrationMark(150, 60, 5, true, array(0,0,0), array(255,255,0));
$pdf->registrationMark(40, 130, 5, true, array(0,0,0), array(255,255,0));
$pdf->registrationMark(150, 130, 5, false, array(100,100,100,100,'All'), array(0,0,0,0,'None'));

// test registration bar with spot colors

$pdf->AddSpotColor('My TCPDF Dark Green', 100, 50, 80, 45);
$pdf->AddSpotColor('My TCPDF Light Yellow', 0, 0, 55, 0);
$pdf->AddSpotColor('My TCPDF Black', 0, 0, 0, 100);
$pdf->AddSpotColor('My TCPDF Red', 30, 100, 90, 10);
$pdf->AddSpotColor('My TCPDF Green', 100, 30, 100, 0);
$pdf->AddSpotColor('My TCPDF Blue', 100, 60, 10, 5);
$pdf->AddSpotColor('My TCPDF Yellow', 0, 20, 100, 0);

$pdf->colorRegistrationBar(50, 150, 80, 10, false, true, 'ALLSPOT');

// CMYK registration mark
$pdf->registrationMarkCMYK(150, 155, 8);

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_056.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
