<?php
//============================================================+
// File name   : example_055.php
// Begin       : 2009-10-21
// Last Update : 2014-12-10
//
// Description : Example 055 for TCPDF class
//               Display all characters available on core fonts.
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
 * Display all characters available on core fonts.
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: XHTML Forms
 * @author Nicola Asuni
 * @since 2009-10-21
 * @group html
 * @group form
 * @group pdf
 */

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Nicola Asuni');
$pdf->setTitle('TCPDF Example 055');
$pdf->setSubject('TCPDF Tutorial');
$pdf->setKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 055', PDF_HEADER_STRING);

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
$pdf->setFont('helvetica', '', 14);

// array of font names
$core_fonts = array('courier', 'courierB', 'courierI', 'courierBI', 'helvetica', 'helveticaB', 'helveticaI', 'helveticaBI', 'times', 'timesB', 'timesI', 'timesBI', 'symbol', 'zapfdingbats');

// set fill color
$pdf->setFillColor(221,238,255);

// create one HTML table for each core font
foreach($core_fonts as $font) {
	// add a page
	$pdf->AddPage();

	// Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')

	// set font for title
	$pdf->setFont('helvetica', 'B', 16);

	// print font name
	$pdf->Cell(0, 10, 'FONT: '.$font, 1, 1, 'C', true, '', 0, false, 'T', 'M');

	// set font for chars
	$pdf->setFont($font, '', 16);

	// print each character
	for ($i = 0; $i < 256; ++$i) {
		if (($i > 0) AND (($i % 16) == 0)) {
			$pdf->Ln();
		}
		$pdf->Cell(11.25, 11.25, TCPDF_FONTS::unichr($i), 1, 0, 'C', false, '', 0, false, 'T', 'M');
	}

	$pdf->Ln(20);

	// print a pangram
	$pdf->Cell(0, 0, 'The quick brown fox jumps over the lazy dog', 0, 1, 'C', false, '', 0, false, 'T', 'M');
}

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_055.pdf', 'D');

//============================================================+
// END OF FILE
//============================================================+
