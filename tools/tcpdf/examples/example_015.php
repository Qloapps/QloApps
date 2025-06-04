<?php
//============================================================+
// File name   : example_015.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 015 for TCPDF class
//               Bookmarks (Table of Content)
//               and Named Destinations.
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
 * @abstract TCPDF - Example: Bookmarks (Table of Content)
 * @author Nicola Asuni
 * @since 2008-03-04
 * @group toc
 * @group bookmark
 * @group pdf
 */

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Nicola Asuni');
$pdf->setTitle('TCPDF Example 015');
$pdf->setSubject('TCPDF Tutorial');
$pdf->setKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 015', PDF_HEADER_STRING);

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

// Bookmark($txt, $level=0, $y=-1, $page='', $style='', $color=array(0,0,0))

// set font
$pdf->setFont('times', 'B', 20);

// add a page
$pdf->AddPage();

// set a bookmark for the current position
$pdf->Bookmark('Chapter 1', 0, 0, '', 'B', array(0,64,128));

// print a line using Cell()
$pdf->Cell(0, 10, 'Chapter 1', 0, 1, 'L');

$pdf->setFont('times', 'I', 14);
$pdf->Write(0, 'You can set PDF Bookmarks using the Bookmark() method.
You can set PDF Named Destinations using the setDestination() method.');

$pdf->setFont('times', 'B', 20);

// add other pages and bookmarks

$pdf->AddPage();
$pdf->Bookmark('Paragraph 1.1', 1, 0, '', '', array(0,0,0));
$pdf->Cell(0, 10, 'Paragraph 1.1', 0, 1, 'L');

$pdf->AddPage();
$pdf->Bookmark('Paragraph 1.2', 1, 0, '', '', array(0,0,0));
$pdf->Cell(0, 10, 'Paragraph 1.2', 0, 1, 'L');

$pdf->AddPage();
$pdf->Bookmark('Sub-Paragraph 1.2.1', 2, 0, '', 'I', array(0,0,0));
$pdf->Cell(0, 10, 'Sub-Paragraph 1.2.1', 0, 1, 'L');

$pdf->AddPage();
$pdf->Bookmark('Paragraph 1.3', 1, 0, '', '', array(0,0,0));
$pdf->Cell(0, 10, 'Paragraph 1.3', 0, 1, 'L');

$pdf->AddPage();
// add a named destination so you can open this document at this page using the link: "example_015.pdf#chapter2"
$pdf->setDestination('chapter2', 0, '');
// add a bookmark that points to a named destination
$pdf->Bookmark('Chapter 2', 0, 0, '', 'BI', array(128,0,0), -1, '#chapter2');
$pdf->Cell(0, 10, 'Chapter 2', 0, 1, 'L');
$pdf->setFont('times', 'I', 14);
$pdf->Write(0, 'Once saved, you can open this document at this page using the link: "example_015.pdf#chapter2".');

$pdf->AddPage();
$pdf->setDestination('chapter3', 0, '');
$pdf->setFont('times', 'B', 20);
$pdf->Bookmark('Chapter 3', 0, 0, '', 'B', array(0,64,128));
$pdf->Cell(0, 10, 'Chapter 3', 0, 1, 'L');

$pdf->AddPage();
$pdf->setDestination('chapter4', 0, '');
$pdf->setFont('times', 'B', 20);
$pdf->Bookmark('Chapter 4', 0, 0, '', 'B', array(0,64,128));
$pdf->Cell(0, 10, 'Chapter 4', 0, 1, 'L');

$pdf->AddPage();
$pdf->Bookmark('Chapter 5', 0, 0, '', 'B', array(0,128,0));
$pdf->Cell(0, 10, 'Chapter 5', 0, 1, 'L');
$txt = 'Example of File Attachment.
Double click on the icon to open the attached file.';
$pdf->setFont('helvetica', '', 10);
$pdf->Write(0, $txt, '', 0, 'L', true, 0, false, false, 0);

// attach an external file TXT file
$pdf->Annotation(20, 50, 5, 5, 'TXT file', array('Subtype'=>'FileAttachment', 'Name' => 'PushPin', 'FS' => 'data/utf8test.txt'));

// attach an external file
$pdf->Annotation(50, 50, 5, 5, 'PDF file', array('Subtype'=>'FileAttachment', 'Name' => 'PushPin', 'FS' => 'example_012.pdf'));

// add a bookmark that points to an embedded file
// NOTE: prefix the file name with the * character for generic file and with % character for PDF file
$pdf->Bookmark('TXT file', 0, 0, '', 'B', array(128,0,255), -1, '*utf8test.txt');

// add a bookmark that points to an embedded file
// NOTE: prefix the file name with the * character for generic file and with % character for PDF file
$pdf->Bookmark('PDF file', 0, 0, '', 'B', array(128,0,255), -1, '%example_012.pdf');

// add a bookmark that points to an external URL
$pdf->Bookmark('External URL', 0, 0, '', 'B', array(0,0,255), -1, 'http://www.tcpdf.org');

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_015.pdf', 'D');

//============================================================+
// END OF FILE
//============================================================+
