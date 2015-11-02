<?php
//============================================================+
// File name   : spotcolors.php
// Version     : 1.0.001
// Begin       : 2010-11-11
// Last Update : 2011-10-03
// Author      : Nicola Asuni - Tecnick.com LTD - Manor Coach House, Church Hill, Aldershot, Hants, GU12 4RQ, UK - www.tecnick.com - info@tecnick.com
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
// Copyright (C) 2002-2012  Nicola Asuni - Tecnick.com LTD
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
// Description : Array of Spot Colors for TCPDF library
//
//============================================================+

/**
 * @file
 * Arrays of Spot Colors for TCPDF library
 * @author Nicola Asuni
 * @package com.tecnick.tcpdf
 * @since 5.9.012 (2010-11-11)
*/

/**
 * Array of Spot colors (C,M,Y,K,name)
 * Color keys must be in lowercase and without spaces.
 * As long as no open standard for spot colours exists, you have to buy a colour book by one of the colour manufacturers and insert the values and names of spot colours directly.
 * Common industry standard spot colors are: ANPA-COLOR, DIC, FOCOLTONE, GCMI, HKS, PANTONE, TOYO, TRUMATCH.
 */
$spotcolor = array(
	// the following are just examples, fill the array with your own values
	'mytcpdfblack' => array(0, 0, 0, 100, 'My TCPDF Black'),
	'mytcpdfred' => array(30, 100, 90, 10, 'My TCPDF Red'),
	'mytcpdfgreen' => array(100, 30, 100, 0, 'My TCPDF Green'),
	'mytcpdfblue' => array(100, 60, 10, 5, 'My TCPDF Blue'),
	'mytcpdfyellow' => array(0, 20, 100, 0, 'My TCPDF Yellow'),
	// ...
);

//============================================================+
// END OF FILE
//============================================================+

