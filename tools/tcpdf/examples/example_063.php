<?php
//============================================================+
// File name   : example_063.php
// Begin       : 2010-09-29
// Last Update : 2013-05-14
//
// Description : Example 063 for TCPDF class
//               Text stretching and spacing (tracking)
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
 * @abstract TCPDF - Example: Text stretching and spacing (tracking)
 * @author Nicola Asuni
 * @since 2010-09-29
 * @group text
 * @group pdf
 */

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Nicola Asuni');
$pdf->setTitle('TCPDF Example 063');
$pdf->setSubject('TCPDF Tutorial');
$pdf->setKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 063', PDF_HEADER_STRING);

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
$pdf->setFont('helvetica', 'B', 16);

// add a page
$pdf->AddPage();

$pdf->Write(0, 'Example of Text Stretching and Spacing (tracking)', '', 0, 'L', true, 0, false, false, 0);
$pdf->Ln(5);

// create several cells to display all cases of stretching and spacing combinations.

$fonts = array('times', 'dejavuserif');
$alignments = array('L' => 'LEFT', 'C' => 'CENTER', 'R' => 'RIGHT', 'J' => 'JUSTIFY');


// Test all cases using direct stretching/spacing methods
foreach ($fonts as $fkey => $font) {
	$pdf->setFont($font, '', 14);
	foreach ($alignments as $align_mode => $align_name) {
		for ($stretching = 90; $stretching <= 110; $stretching += 10) {
			for ($spacing = -0.254; $spacing <= 0.254; $spacing += 0.254) {
				$pdf->setFontStretching($stretching);
				$pdf->setFontSpacing($spacing);
				$txt = $align_name.' | Stretching = '.$stretching.'% | Spacing = '.sprintf('%+.3F', $spacing).'mm';
				$pdf->Cell(0, 0, $txt, 1, 1, $align_mode);
			}
		}
	}
	$pdf->AddPage();
}


// Test all cases using CSS stretching/spacing properties
foreach ($fonts as $fkey => $font) {
	$pdf->setFont($font, '', 11);
	foreach ($alignments as $align_mode => $align_name) {
		for ($stretching = 90; $stretching <= 110; $stretching += 10) {
			for ($spacing = -0.254; $spacing <= 0.254; $spacing += 0.254) {
				$html = '<span style="font-stretch:'.$stretching.'%;letter-spacing:'.$spacing.'mm;"><span style="color:red;">'.$align_name.'</span> | <span style="color:green;">Stretching = '.$stretching.'%</span> | <span style="color:blue;">Spacing = '.sprintf('%+.3F', $spacing).'mm</span><br />Lorem ipsum dolor sit amet, consectetur adipiscing elit. In sed imperdiet lectus. Phasellus quis velit velit, non condimentum quam. Sed neque urna, ultrices ac volutpat vel, laoreet vitae augue. Sed vel velit erat. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.</span>';
				$pdf->writeHTMLCell(0, 0, '', '', $html, 1, 1, false, true, $align_mode, false);
			}
		}
		if (!(($fkey == 1) AND ($align_mode == 'J'))) {
			$pdf->AddPage();
		}
	}
}


// reset font stretching
$pdf->setFontStretching(100);

// reset font spacing
$pdf->setFontSpacing(0);

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_063.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
