<?php
//============================================================+
// File name   : example_037.php
// Begin       : 2008-09-12
// Last Update : 2013-09-30
//
// Description : Example 037 for TCPDF class
//               Spot colors
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
 * @abstract TCPDF - Example: Spot colors.
 * @author Nicola Asuni
 * @since 2008-09-12
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
$pdf->setTitle('TCPDF Example 037');
$pdf->setSubject('TCPDF Tutorial');
$pdf->setKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 037', PDF_HEADER_STRING);

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
$pdf->setFont('helvetica', '', 11);

// add a page
$pdf->AddPage();

$html = '<h1>Example of Spot Colors</h1>Spot colors are single ink colors, rather than colors produced by four (CMYK), six (CMYKOG) or more inks in the printing process (process colors). They can be obtained by special vendors, but often the printers have found their own way of mixing inks to match defined colors.<br /><br />As long as no open standard for spot colours exists, TCPDF users will have to buy a colour book by one of the colour manufacturers and insert the values and names of spot colours directly into the $spotcolor array in <b><em>include/tcpdf_colors.php</em></b> file, or define them using the <b><em>AddSpotColor()</em></b> method.<br /><br />Common industry standard spot colors are:<br /><span color="#008800">ANPA-COLOR, DIC, FOCOLTONE, GCMI, HKS, PANTONE, TOYO, TRUMATCH</span>.';

// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'J', true);


$pdf->setFont('helvetica', '', 10);

// Define some new spot colors
// $c, $m, $y and $k (2nd, 3rd, 4th and 5th parameter) are the CMYK color components.
// AddSpotColor($name, $c, $m, $y, $k)

$pdf->AddSpotColor('My TCPDF Dark Green', 100, 50, 80, 45);
$pdf->AddSpotColor('My TCPDF Light Yellow', 0, 0, 55, 0);
$pdf->AddSpotColor('My TCPDF Black', 0, 0, 0, 100);
$pdf->AddSpotColor('My TCPDF Red', 30, 100, 90, 10);
$pdf->AddSpotColor('My TCPDF Green', 100, 30, 100, 0);
$pdf->AddSpotColor('My TCPDF Blue', 100, 60, 10, 5);
$pdf->AddSpotColor('My TCPDF Yellow', 0, 20, 100, 0);

// Select the spot color
// $tint (the second parameter) is the intensity of the color (0-100).
// setTextSpotColor($name, $tint=100)
// setDrawSpotColor($name, $tint=100)
// setFillSpotColor($name, $tint=100)

$pdf->setTextSpotColor('My TCPDF Black', 100);
$pdf->setDrawSpotColor('My TCPDF Black', 100);

$starty = 100;

// print some spot colors

$pdf->setFillSpotColor('My TCPDF Dark Green', 100);
$pdf->Rect(30, $starty, 40, 20, 'DF');
$pdf->Text(73, $starty + 8, 'My TCPDF Dark Green');

$starty += 24;
$pdf->setFillSpotColor('My TCPDF Light Yellow', 100);
$pdf->Rect(30, $starty, 40, 20, 'DF');
$pdf->Text(73, $starty + 8, 'My TCPDF Light Yellow');


// --- default values defined on spotcolors.php ---

$starty += 24;
$pdf->setFillSpotColor('My TCPDF Red', 100);
$pdf->Rect(30, $starty, 40, 20, 'DF');
$pdf->Text(73, $starty + 8, 'My TCPDF Red');

$starty += 24;
$pdf->setFillSpotColor('My TCPDF Green', 100);
$pdf->Rect(30, $starty, 40, 20, 'DF');
$pdf->Text(73, $starty + 8, 'My TCPDF Green');

$starty += 24;
$pdf->setFillSpotColor('My TCPDF Blue', 100);
$pdf->Rect(30, $starty, 40, 20, 'DF');
$pdf->Text(73, $starty + 8, 'My TCPDF Blue');

$starty += 24;
$pdf->setFillSpotColor('My TCPDF Yellow', 100);
$pdf->Rect(30, $starty, 40, 20, 'DF');
$pdf->Text(73, $starty + 8, 'My TCPDF Yellow');

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_037.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
