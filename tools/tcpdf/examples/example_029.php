<?php
//============================================================+
// File name   : example_029.php
// Begin       : 2008-06-09
// Last Update : 2013-05-14
//
// Description : Example 029 for TCPDF class
//               Set PDF viewer display preferences.
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
 * @abstract TCPDF - Example: Set PDF viewer display preferences.
 * @author Nicola Asuni
 * @since 2008-06-09
 * @group viewer
 * @group pdf
 */

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Nicola Asuni');
$pdf->setTitle('TCPDF Example 029');
$pdf->setSubject('TCPDF Tutorial');
$pdf->setKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 029', PDF_HEADER_STRING);

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

// set array for viewer preferences
$preferences = array(
	'HideToolbar' => true,
	'HideMenubar' => true,
	'HideWindowUI' => true,
	'FitWindow' => true,
	'CenterWindow' => true,
	'DisplayDocTitle' => true,
	'NonFullScreenPageMode' => 'UseNone', // UseNone, UseOutlines, UseThumbs, UseOC
	'ViewArea' => 'CropBox', // CropBox, BleedBox, TrimBox, ArtBox
	'ViewClip' => 'CropBox', // CropBox, BleedBox, TrimBox, ArtBox
	'PrintArea' => 'CropBox', // CropBox, BleedBox, TrimBox, ArtBox
	'PrintClip' => 'CropBox', // CropBox, BleedBox, TrimBox, ArtBox
	'PrintScaling' => 'AppDefault', // None, AppDefault
	'Duplex' => 'DuplexFlipLongEdge', // Simplex, DuplexFlipShortEdge, DuplexFlipLongEdge
	'PickTrayByPDFSize' => true,
	'PrintPageRange' => array(1,1,2,3),
	'NumCopies' => 2
);

// Check the example n. 60 for advanced page settings

// set pdf viewer preferences
$pdf->setViewerPreferences($preferences);

// set font
$pdf->setFont('times', '', 14);

// add a page
$pdf->AddPage();

// print a line
$pdf->Cell(0, 12, 'DISPLAY PREFERENCES - PAGE 1', 1, 1, 'C');

$pdf->Ln(5);

$pdf->Write(0, 'You can use the setViewerPreferences() method to change viewer preferences.', '', 0, 'L', true, 0, false, false, 0);

// add a page
$pdf->AddPage();
// print a line
$pdf->Cell(0, 12, 'DISPLAY PREFERENCES - PAGE 2', 0, 0, 'C');

// add a page
$pdf->AddPage();
// print a line
$pdf->Cell(0, 12, 'DISPLAY PREFERENCES - PAGE 3', 0, 0, 'C');

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_029.pdf', 'D');

//============================================================+
// END OF FILE
//============================================================+
