<?php
//============================================================+
// File name   : example_030.php
// Begin       : 2008-06-09
// Last Update : 2013-05-14
//
// Description : Example 030 for TCPDF class
//               Colour gradients
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
 * @abstract TCPDF - Example: Colour gradients
 * @author Nicola Asuni
 * @since 2008-06-09
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
$pdf->setTitle('TCPDF Example 030');
$pdf->setSubject('TCPDF Tutorial');
$pdf->setKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 030', PDF_HEADER_STRING);

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

// --- first page ------------------------------------------

// add a page
$pdf->AddPage();

$pdf->Cell(0, 0, 'TCPDF Gradients', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

// set colors for gradients (r,g,b) or (grey 0-255)
$red = array(255, 0, 0);
$blue = array(0, 0, 200);
$yellow = array(255, 255, 0);
$green = array(0, 255, 0);
$white = array(255);
$black = array(0);

// set the coordinates x1,y1,x2,y2 of the gradient (see linear_gradient_coords.jpg)
$coords = array(0, 0, 1, 0);

// paint a linear gradient
$pdf->LinearGradient(20, 45, 80, 80, $red, $blue, $coords);

// write label
$pdf->Text(20, 130, 'LinearGradient()');

// set the coordinates fx,fy,cx,cy,r of the gradient (see radial_gradient_coords.jpg)
$coords = array(0.5, 0.5, 1, 1, 1.2);

// paint a radial gradient
$pdf->RadialGradient(110, 45, 80, 80, $white, $black, $coords);

// write label
$pdf->Text(110, 130, 'RadialGradient()');

// paint a coons patch mesh with default coordinates
$pdf->CoonsPatchMesh(20, 155, 80, 80, $yellow, $blue, $green, $red);

// write label
$pdf->Text(20, 240, 'CoonsPatchMesh()');

// set the coordinates for the cubic Bézier points x1,y1 ... x12, y12 of the patch (see coons_patch_mesh_coords.jpg)
$coords = array(
	0.00,0.00, 0.33,0.20,             //lower left
	0.67,0.00, 1.00,0.00, 0.80,0.33,  //lower right
	0.80,0.67, 1.00,1.00, 0.67,0.80,  //upper right
	0.33,1.00, 0.00,1.00, 0.20,0.67,  //upper left
	0.00,0.33);                       //lower left
$coords_min = 0;   //minimum value of the coordinates
$coords_max = 1;   //maximum value of the coordinates

// paint a coons patch gradient with the above coordinates
$pdf->CoonsPatchMesh(110, 155, 80, 80, $yellow, $blue, $green, $red, $coords, $coords_min, $coords_max);

// write label
$pdf->Text(110, 240, 'CoonsPatchMesh()');

// --- second page -----------------------------------------
$pdf->AddPage();

// first patch: f = 0
$patch_array[0]['f'] = 0;
$patch_array[0]['points'] = array(
	0.00,0.00, 0.33,0.00,
	0.67,0.00, 1.00,0.00, 1.00,0.33,
	0.8,0.67, 1.00,1.00, 0.67,0.8,
	0.33,1.80, 0.00,1.00, 0.00,0.67,
	0.00,0.33);
$patch_array[0]['colors'][0] = array('r' => 255, 'g' => 255, 'b' => 0);
$patch_array[0]['colors'][1] = array('r' => 0, 'g' => 0, 'b' => 255);
$patch_array[0]['colors'][2] = array('r' => 0, 'g' => 255,'b' => 0);
$patch_array[0]['colors'][3] = array('r' => 255, 'g' => 0,'b' => 0);

// second patch - above the other: f = 2
$patch_array[1]['f'] = 2;
$patch_array[1]['points'] = array(
	0.00,1.33,
	0.00,1.67, 0.00,2.00, 0.33,2.00,
	0.67,2.00, 1.00,2.00, 1.00,1.67,
	1.5,1.33);
$patch_array[1]['colors'][0]=array('r' => 0, 'g' => 0, 'b' => 0);
$patch_array[1]['colors'][1]=array('r' => 255, 'g' => 0, 'b' => 255);

// third patch - right of the above: f = 3
$patch_array[2]['f'] = 3;
$patch_array[2]['points'] = array(
	1.33,0.80,
	1.67,1.50, 2.00,1.00, 2.00,1.33,
	2.00,1.67, 2.00,2.00, 1.67,2.00,
	1.33,2.00);
$patch_array[2]['colors'][0] = array('r' => 0, 'g' => 255, 'b' => 255);
$patch_array[2]['colors'][1] = array('r' => 0, 'g' => 0, 'b' => 0);

// fourth patch - below the above, which means left(?) of the above: f = 1
$patch_array[3]['f'] = 1;
$patch_array[3]['points'] = array(
	2.00,0.67,
	2.00,0.33, 2.00,0.00, 1.67,0.00,
	1.33,0.00, 1.00,0.00, 1.00,0.33,
	0.8,0.67);
$patch_array[3]['colors'][0] = array('r' => 0, 'g' => 0, 'b' => 0);
$patch_array[3]['colors'][1] = array('r' => 0, 'g' => 0, 'b' => 255);

$coords_min = 0;
$coords_max = 2;

$pdf->CoonsPatchMesh(10, 45, 190, 200, '', '', '', '', $patch_array, $coords_min, $coords_max);

// write label
$pdf->Text(10, 250, 'CoonsPatchMesh()');

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_030.pdf', 'D');

//============================================================+
// END OF FILE
//============================================================+
