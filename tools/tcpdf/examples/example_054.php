<?php
//============================================================+
// File name   : example_054.php
// Begin       : 2009-09-07
// Last Update : 2013-05-14
//
// Description : Example 054 for TCPDF class
//               XHTML Forms
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
 * @abstract TCPDF - Example: XHTML Forms
 * @author Nicola Asuni
 * @since 2009-09-07
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
$pdf->setTitle('TCPDF Example 054');
$pdf->setSubject('TCPDF Tutorial');
$pdf->setKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 054', PDF_HEADER_STRING);

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

// IMPORTANT: disable font subsetting to allow users editing the document
$pdf->setFontSubsetting(false);

// set font. 'helvetica' MUST be used to avoid a PHP notice from PHP 7.4+
$pdf->setFont('helvetica', '', 10, '', false);

// add a page
$pdf->AddPage();

// create some HTML content
$html = <<<EOD
<h1>XHTML Form Example</h1>
<form method="post" action="http://localhost/printvars.php" enctype="multipart/form-data">
<label for="name">name:</label> <input type="text" name="name" value="" size="20" maxlength="30" /><br />
<label for="password">password:</label> <input type="password" name="password" value="" size="20" maxlength="30" /><br /><br />
<label for="infile">file:</label> <input type="file" name="userfile" size="20" /><br /><br />
<input type="checkbox" name="agree" value="1" checked="checked" /> <label for="agree">I agree </label><br /><br />
<input type="radio" name="radioquestion" id="rqa" value="1" /> <label for="rqa">one</label><br />
<input type="radio" name="radioquestion" id="rqb" value="2" checked="checked"/> <label for="rqb">two</label><br />
<input type="radio" name="radioquestion" id="rqc" value="3" /> <label for="rqc">three</label><br /><br />
<label for="selection">select:</label>
<select name="selection" size="0">
	<option value="0">zero</option>
	<option value="1">one</option>
	<option value="2">two</option>
	<option value="3">three</option>
</select><br /><br />
<label for="selection">select:</label>
<select name="multiselection" size="2" multiple="multiple">
	<option value="0">zero</option>
	<option value="1">one</option>
	<option value="2">two</option>
	<option value="3">three</option>
</select><br /><br /><br />
<label for="text">text area:</label><br />
<textarea cols="40" rows="3" name="text">line one
line two</textarea><br />
<br /><br /><br />
<input type="reset" name="reset" value="Reset" />
<input type="submit" name="submit" value="Submit" />
<input type="button" name="print" value="Print" onclick="print()" />
<input type="hidden" name="hiddenfield" value="OK" />
<br />
</form>
EOD;

// output the HTML content
$pdf->writeHTML($html, true, 0, true, 0);

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_054.pdf', 'D');

//============================================================+
// END OF FILE
//============================================================+
