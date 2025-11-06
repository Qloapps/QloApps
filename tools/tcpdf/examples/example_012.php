<?php
//============================================================+
// File name   : example_012.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 012 for TCPDF class
//               Graphic Functions
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
 * @abstract TCPDF - Example: Graphic Functions
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
$pdf->setTitle('TCPDF Example 012');
$pdf->setSubject('TCPDF Tutorial');
$pdf->setKeywords('TCPDF, PDF, example, test, guide');

// disable header and footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

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
$pdf->setFont('helvetica', '', 10);

// add a page
$pdf->AddPage();

$style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => '10,20,5,10', 'phase' => 10, 'color' => array(255, 0, 0));
$style2 = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 0, 0));
$style3 = array('width' => 1, 'cap' => 'round', 'join' => 'round', 'dash' => '2,10', 'color' => array(255, 0, 0));
$style4 = array('L' => 0,
                'T' => array('width' => 0.25, 'cap' => 'butt', 'join' => 'miter', 'dash' => '20,10', 'phase' => 10, 'color' => array(100, 100, 255)),
                'R' => array('width' => 0.50, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(50, 50, 127)),
                'B' => array('width' => 0.75, 'cap' => 'square', 'join' => 'miter', 'dash' => '30,10,5,10'));
$style5 = array('width' => 0.25, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 64, 128));
$style6 = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => '10,10', 'color' => array(0, 128, 0));
$style7 = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 128, 0));

// Line
$pdf->Text(5, 4, 'Line examples');
$pdf->Line(5, 10, 80, 30, $style);
$pdf->Line(5, 10, 5, 30, $style2);
$pdf->Line(5, 10, 80, 10, $style3);

// Rect
$pdf->Text(100, 4, 'Rectangle examples');
$pdf->Rect(100, 10, 40, 20, 'DF', $style4, array(220, 220, 200));
$pdf->Rect(145, 10, 40, 20, 'D', array('all' => $style3));

// Curve
$pdf->Text(5, 34, 'Curve examples');
$pdf->Curve(5, 40, 30, 55, 70, 45, 60, 75, '', $style6);
$pdf->Curve(80, 40, 70, 75, 150, 45, 100, 75, 'F', $style6);
$pdf->Curve(140, 40, 150, 55, 180, 45, 200, 75, 'DF', $style6, array(200, 220, 200));

// Circle and ellipse
$pdf->Text(5, 79, 'Circle and ellipse examples');
$pdf->setLineStyle($style5);
$pdf->Circle(25,105,20);
$pdf->Circle(25,105,10, 90, 180, '', $style6);
$pdf->Circle(25,105,10, 270, 360, 'F');
$pdf->Circle(25,105,10, 270, 360, 'C', $style6);

$pdf->setLineStyle($style5);
$pdf->Ellipse(100,103,40,20);
$pdf->Ellipse(100,105,20,10, 0, 90, 180, '', $style6);
$pdf->Ellipse(100,105,20,10, 0, 270, 360, 'DF', $style6);

$pdf->setLineStyle($style5);
$pdf->Ellipse(175,103,30,15,45);
$pdf->Ellipse(175,105,15,7.50, 45, 90, 180, '', $style6);
$pdf->Ellipse(175,105,15,7.50, 45, 270, 360, 'F', $style6, array(220, 200, 200));

// Polygon
$pdf->Text(5, 129, 'Polygon examples');
$pdf->setLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
$pdf->Polygon(array(5,135,45,135,15,165));
$pdf->Polygon(array(60,135,80,135,80,155,70,165,50,155), 'DF', array($style6, $style7, $style7, 0, $style6), array(220, 200, 200));
$pdf->Polygon(array(120,135,140,135,150,155,110,155), 'D', array($style6, 0, $style7, $style6));
$pdf->Polygon(array(160,135,190,155,170,155,200,160,160,165), 'DF', array('all' => $style6), array(220, 220, 220));

// Polygonal Line
$pdf->setLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 164)));
$pdf->PolyLine(array(80,165,90,160,100,165,110,160,120,165,130,160,140,165), 'D', array(), array());

// Regular polygon
$pdf->Text(5, 169, 'Regular polygon examples');
$pdf->setLineStyle($style5);
$pdf->RegularPolygon(20, 190, 15, 6, 0, 1, 'F');
$pdf->RegularPolygon(55, 190, 15, 6);
$pdf->RegularPolygon(55, 190, 10, 6, 45, false, 'DF', array($style6, 0, $style7, 0, $style7, $style7));
$pdf->RegularPolygon(90, 190, 15, 3, 0, true, 'DF', array('all' => $style5), array(200, 220, 200), 'F', array(255, 200, 200));
$pdf->RegularPolygon(125, 190, 15, 4, 30, true, '', array('all' => $style5), array(), '', $style6);
$pdf->RegularPolygon(160, 190, 15, 10);

// Star polygon
$pdf->Text(5, 209, 'Star polygon examples');
$pdf->setLineStyle($style5);
$pdf->StarPolygon(20, 230, 15, 20, 3, 0, 1, 'F');
$pdf->StarPolygon(55, 230, 15, 12, 5);
$pdf->StarPolygon(55, 230, 7, 12, 5, 45, false, 'DF', array('all' => $style7), array(220, 220, 200), 'F', array(255, 200, 200));
$pdf->StarPolygon(90, 230, 15, 20, 6, 0, true, 'DF', array('all' => $style5), array(220, 220, 200), 'F', array(255, 200, 200));
$pdf->StarPolygon(125, 230, 15, 5, 2, 30, true, '', array('all' => $style5), array(), '', $style6);
$pdf->StarPolygon(160, 230, 15, 10, 3);
$pdf->StarPolygon(160, 230, 7, 50, 26);

// Rounded rectangle
$pdf->Text(5, 249, 'Rounded rectangle examples');
$pdf->setLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
$pdf->RoundedRect(5, 255, 40, 30, 3.50, '1111', 'DF');
$pdf->RoundedRect(50, 255, 40, 30, 6.50, '1000');
$pdf->RoundedRect(95, 255, 40, 30, 10.0, '1111', '', $style6);
$pdf->RoundedRect(140, 255, 40, 30, 8.0, '0101', 'DF', $style6, array(200, 200, 200));

// Arrows
$pdf->Text(185, 249, 'Arrows');
$pdf->setLineStyle($style5);
$pdf->setFillColor(255, 0, 0);
$pdf->Arrow(200, 280, 185, 266, 0, 5, 15);
$pdf->Arrow(200, 280, 190, 263, 1, 5, 15);
$pdf->Arrow(200, 280, 195, 261, 2, 5, 15);
$pdf->Arrow(200, 280, 200, 260, 3, 5, 15);

// - . - . - . - . - . - . - . - . - . - . - . - . - . - . -

// ellipse

// add a page
$pdf->AddPage();

$pdf->Cell(0, 0, 'Arc of Ellipse');

// center of ellipse
$xc=100;
$yc=100;

// X Y axis
$pdf->setDrawColor(200, 200, 200);
$pdf->Line($xc-50, $yc, $xc+50, $yc);
$pdf->Line($xc, $yc-50, $xc, $yc+50);

// ellipse axis
$pdf->setDrawColor(200, 220, 255);
$pdf->Line($xc-50, $yc-50, $xc+50, $yc+50);
$pdf->Line($xc-50, $yc+50, $xc+50, $yc-50);

// ellipse
$pdf->setDrawColor(200, 255, 200);
$pdf->Ellipse($xc, $yc, 30, 15, 45, 0, 360, 'D', array(), array(), 2);

// ellipse arc
$pdf->setDrawColor(255, 0, 0);
$pdf->Ellipse($xc, $yc, 30, 15, 45, 45, 90, 'D', array(), array(), 2);


// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_012.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
