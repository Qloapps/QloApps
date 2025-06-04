<?php
//============================================================+
// File name   : example_053.php
// Begin       : 2009-09-02
// Last Update : 2013-05-14
//
// Description : Example 053 for TCPDF class
//               Javascript example.
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
 * @abstract TCPDF - Example: Javascript example.
 * @author Nicola Asuni
 * @since 2009-09-02
 * @group javascript
 * @group pdf
 */

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Nicola Asuni');
$pdf->setTitle('TCPDF Example 053');
$pdf->setSubject('TCPDF Tutorial');
$pdf->setKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 053', PDF_HEADER_STRING);

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
$pdf->setFont('times', '', 14);

// add a page
$pdf->AddPage();

// print a some of text
$text = 'This is an example of <strong>JavaScript</strong> usage on PDF documents.<br /><br />For more information check the source code of this example, the source code documentation for the <i>IncludeJS()</i> method and the <i>JavaScript for Acrobat API Reference</i> guide.<br /><br /><a href="http://www.tcpdf.org">www.tcpdf.org</a>';
$pdf->writeHTML($text, true, 0, true, 0);

// write some JavaScript code
$js = <<<EOD
app.alert('JavaScript Popup Example', 3, 0, 'Welcome');
var cResponse = app.response({
	cQuestion: 'How are you today?',
	cTitle: 'Your Health Status',
	cDefault: 'Fine',
	cLabel: 'Response:'
});
if (cResponse == null) {
	app.alert('Thanks for trying anyway.', 3, 0, 'Result');
} else {
	app.alert('You responded, "'+cResponse+'", to the health question.', 3, 0, 'Result');
}
EOD;

// force print dialog
$js .= 'print(true);';

// set javascript
$pdf->IncludeJS($js);

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_053.pdf', 'D');

//============================================================+
// END OF FILE
//============================================================+
