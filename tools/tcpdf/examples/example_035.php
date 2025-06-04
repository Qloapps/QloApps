<?php
//============================================================+
// File name   : example_035.php
// Begin       : 2008-07-22
// Last Update : 2013-05-14
//
// Description : Example 035 for TCPDF class
//               Line styles with cells and multicells
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
 * @abstract TCPDF - Example: Line styles with cells and multicells
 * @author Nicola Asuni
 * @since 2008-03-04
 * @group cell
 * @group pdf
 */

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Nicola Asuni');
$pdf->setTitle('TCPDF Example 035');
$pdf->setSubject('TCPDF Tutorial');
$pdf->setKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 035', PDF_HEADER_STRING);

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
$pdf->setFont('times', 'BI', 16);

// add a page
$pdf->AddPage();

$pdf->Write(0, 'Example of SetLineStyle() method', '', 0, 'L', true, 0, false, false, 0);

$pdf->Ln();

$pdf->setLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 4, 'color' => array(255, 0, 0)));
$pdf->setFillColor(255,255,128);
$pdf->setTextColor(0,0,128);

$text="DUMMY";

$pdf->Cell(0, 0, $text, 1, 1, 'L', 1, 0);

$pdf->Ln();

$pdf->setLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 255)));
$pdf->setFillColor(255,255,0);
$pdf->setTextColor(0,0,255);
$pdf->MultiCell(60, 4, $text, 1, 'C', 1, 0);

$pdf->setLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 255, 0)));
$pdf->setFillColor(0,0,255);
$pdf->setTextColor(255,255,0);
$pdf->MultiCell(60, 4, $text, 'TB', 'C', 1, 0);

$pdf->setLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 0, 255)));
$pdf->setFillColor(0,255,0);
$pdf->setTextColor(255,0,255);
$pdf->MultiCell(60, 4, $text, 1, 'C', 1, 1);

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_035.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
