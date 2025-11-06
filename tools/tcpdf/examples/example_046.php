<?php
//============================================================+
// File name   : example_046.php
// Begin       : 2009-02-28
// Last Update : 2013-05-14
//
// Description : Example 046 for TCPDF class
//               Text Hyphenation
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
 * @abstract TCPDF - Example: text Hyphenation
 * @author Nicola Asuni
 * @since 2009-02-28
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
$pdf->setTitle('TCPDF Example 046');
$pdf->setSubject('TCPDF Tutorial');
$pdf->setKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 046', PDF_HEADER_STRING);

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

$pdf->Write(0, 'Example of Text Hyphenation', '', 0, 'L', true, 0, false, false, 0);

$pdf->Ln(10);

/*
Unicode Data for SHY:
	Name : SOFT HYPHEN, commonly abbreviated as SHY
	HTML Entity (decimal): &#173;
	HTML Entity (hex): &#xad;
	HTML Entity (named): &shy;
	How to type in Microsoft Windows: [Alt +00AD] or [Alt 0173]
	UTF-8 (hex): 0xC2 0xAD (c2ad)
*/

/*
// You can automatically add SOFT HYPHENS to your text using
// the hyphenateText() method, but this requires either an
// hyphenation pattern array of a hyphenation pattern TEX file.
// You can download hyphenation TEX patterns from:
// http://www.ctan.org/tex-archive/language/hyph-utf8/tex/generic/hyph-utf8/patterns/

// EXAMPLE:

$html = 'On the other hand, we denounce with righteous indignation and dislike men who are so beguiled and demoralized by the charms of pleasure of the moment, so blinded by desire, that they cannot foresee the pain and trouble that are bound to ensue; and equal blame belongs to those who fail in their duty through weakness of will, which is the same as saying through shrinking from toil and pain. These cases are perfectly simple and easy to distinguish. In a free hour, when our power of choice is untrammelled and when nothing prevents our being able to do what we like best, every pleasure is to be welcomed and every pain avoided. But in certain circumstances and owing to the claims of duty or the obligations of business it will frequently occur that pleasures have to be repudiated and annoyances accepted. The wise man therefore always holds in these matters to this principle of selection: he rejects pleasures to secure other greater pleasures, or else he endures pains to avoid worse pains.';

$hyphen_patterns = $pdf->getHyphenPatternsFromTEX('hyphens/hyph-en-gb.tex');

$html = $pdf->hyphenateText($html, $hyphen_patterns, array(), 1, 2, 1, 8);
*/


// HTML text with soft hyphens (&shy;)
$html = 'On the other hand, we de&shy;nounce with righ&shy;teous in&shy;dig&shy;na&shy;tion and dis&shy;like men who are so be&shy;guiled and de&shy;mo&shy;r&shy;al&shy;ized by the charms of plea&shy;sure of the mo&shy;ment, so blind&shy;ed by de&shy;sire, that they can&shy;not fore&shy;see the pain and trou&shy;ble that are bound to en&shy;sue; and equal blame be&shy;longs to those who fail in their du&shy;ty through weak&shy;ness of will, which is the same as say&shy;ing through shrink&shy;ing from toil and pain. Th&shy;ese cas&shy;es are per&shy;fect&shy;ly sim&shy;ple and easy to distin&shy;guish. In a free hour, when our pow&shy;er of choice is un&shy;tram&shy;melled and when noth&shy;ing pre&shy;vents our be&shy;ing able to do what we like best, ev&shy;ery plea&shy;sure is to be wel&shy;comed and ev&shy;ery pain avoid&shy;ed. But in cer&shy;tain cir&shy;cum&shy;s&shy;tances and ow&shy;ing to the claims of du&shy;ty or the obli&shy;ga&shy;tions of busi&shy;ness it will fre&shy;quent&shy;ly oc&shy;cur that plea&shy;sures have to be re&shy;pu&shy;di&shy;at&shy;ed and an&shy;noy&shy;ances ac&shy;cept&shy;ed. The wise man there&shy;fore al&shy;ways holds in th&shy;ese mat&shy;ters to this prin&shy;ci&shy;ple of se&shy;lec&shy;tion: he re&shy;jects plea&shy;sures to se&shy;cure other greater plea&shy;sures, or else he en&shy;dures pains to avoid worse pains.';

$pdf->setFont('times', '', 10);
$pdf->setDrawColor(255,0,0);
$pdf->setTextColor(0,63,127);

// print a cell
$pdf->writeHTMLCell(50, 0, '', '', $html, 1, 1, 0, true, 'J');

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_046.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
