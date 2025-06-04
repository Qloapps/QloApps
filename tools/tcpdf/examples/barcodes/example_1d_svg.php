<?php
//============================================================+
// File name   : example_1d_svg.php
// Version     : 1.0.000
// Begin       : 2011-07-21
// Last Update : 2013-03-19
// Author      : Nicola Asuni - Tecnick.com LTD - www.tecnick.com - info@tecnick.com
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
// Copyright (C) 2009-2013 Nicola Asuni - Tecnick.com LTD
//
// This file is part of TCPDF software library.
//
// TCPDF is free software: you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// TCPDF is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with TCPDF.  If not, see <http://www.gnu.org/licenses/>.
//
// See LICENSE.TXT file for more information.
// -------------------------------------------------------------------
//
// Description : Example for tcpdf_barcodes_1d.php class
//
//============================================================+

/**
 * @file
 * Example for tcpdf_barcodes_1d.php class
 * @package com.tecnick.tcpdf
 * @author Nicola Asuni
 * @version 1.0.000
 * @group barcode
 * @group 1d
 * @group svg
 * @group comparable
 */

// include 1D barcode class (search for installation path)
require_once(dirname(__FILE__).'/tcpdf_barcodes_1d_include.php');

// set the barcode content and type
$barcodeobj = new TCPDFBarcode('http://www.tcpdf.org', 'C128');

// output the barcode as SVG image
$barcodeobj->getBarcodeSVG(2, 30, 'black');

//============================================================+
// END OF FILE
//============================================================+
