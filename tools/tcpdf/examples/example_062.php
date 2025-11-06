<?php
//============================================================+
// File name   : example_062.php
// Begin       : 2010-08-25
// Last Update : 2013-05-14
//
// Description : Example 062 for TCPDF class
//               XObject Template
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
 * @abstract TCPDF - Example: XObject Template
 * @author Nicola Asuni
 * @since 2010-08-25
 * @group object
 * @group pdf
 */

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Nicola Asuni');
$pdf->setTitle('TCPDF Example 062');
$pdf->setSubject('TCPDF Tutorial');
$pdf->setKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 062', PDF_HEADER_STRING);

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
$pdf->setFont('helvetica', 'B', 20);

// add a page
$pdf->AddPage();

$pdf->Write(0, 'XObject Templates', '', 0, 'C', 1, 0, false, false, 0);

/*
 * An XObject Template is a PDF block that is a self-contained
 * description of any sequence of graphics objects (including path
 * objects, text objects, and sampled images).
 * An XObject Template may be painted multiple times, either on
 * several pages or at several locations on the same page and produces
 * the same results each time, subject only to the graphics state at
 * the time it is invoked.
 */


// start a new XObject Template and set transparency group option
$template_id = $pdf->startTemplate(60, 60, true);

// create Template content
// ...................................................................
//Start Graphic Transformation
$pdf->StartTransform();

// set clipping mask
$pdf->StarPolygon(30, 30, 29, 10, 3, 0, 1, 'CNZ');

// draw jpeg image to be clipped
$pdf->Image('images/image_demo.jpg', 0, 0, 60, 60, '', '', '', true, 72, '', false, false, 0, false, false, false);

//Stop Graphic Transformation
$pdf->StopTransform();

$pdf->setXY(0, 0);

$pdf->setFont('times', '', 40);

$pdf->setTextColor(255, 0, 0);

// print a text
$pdf->Cell(60, 60, 'Template', 0, 0, 'C', false, '', 0, false, 'T', 'M');
// ...................................................................

// end the current Template
$pdf->endTemplate();


// print the selected Template various times using various transparencies

$pdf->setAlpha(0.4);
$pdf->printTemplate($template_id, 15, 50, 20, 20, '', '', false);

$pdf->setAlpha(0.6);
$pdf->printTemplate($template_id, 27, 62, 40, 40, '', '', false);

$pdf->setAlpha(0.8);
$pdf->printTemplate($template_id, 55, 85, 60, 60, '', '', false);

$pdf->setAlpha(1);
$pdf->printTemplate($template_id, 95, 125, 80, 80, '', '', false);

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_062.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
