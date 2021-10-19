#!/usr/bin/env php
<?php
//============================================================+
// File name   : tcpdf_addfont.php
// Version     : 1.0.002
// Begin       : 2013-05-13
// Last Update : 2013-08-05
// Authors     : Nicola Asuni - Tecnick.com LTD - www.tecnick.com - info@tecnick.com
//               Remi Collet
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
// Copyright (C) 2011-2013 Nicola Asuni - Tecnick.com LTD
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
// You should have received a copy of the License
// along with TCPDF. If not, see
// <http://www.tecnick.com/pagefiles/tcpdf/LICENSE.TXT>.
//
// See LICENSE.TXT file for more information.
// -------------------------------------------------------------------
//
// Description : This is a command line script to generate TCPDF fonts.
//
//============================================================+

/**
 * @file
 * This is a command line script to generate TCPDF fonts.<br>
 * @package com.tecnick.tcpdf
 * @version 1.0.000
 */

if (php_sapi_name() != 'cli') {
  echo 'You need to run this command from console.';
  exit(1);
}

$tcpdf_include_dirs = array(realpath(dirname(__FILE__).'/../tcpdf.php'), '/usr/share/php/tcpdf/tcpdf.php', '/usr/share/tcpdf/tcpdf.php', '/usr/share/php-tcpdf/tcpdf.php', '/var/www/tcpdf/tcpdf.php', '/var/www/html/tcpdf/tcpdf.php', '/usr/local/apache2/htdocs/tcpdf/tcpdf.php');
foreach ($tcpdf_include_dirs as $tcpdf_include_path) {
	if (@file_exists($tcpdf_include_path)) {
		require_once($tcpdf_include_path);
		break;
	}
}

/**
 * Display help guide for this command.
 */
function showHelp() {
	$help = <<<EOD
tcpdf_addfont - command line tool to convert fonts for the TCPDF library.

Usage: tcpdf_addfont.php [ options ] -i fontfile[,fontfile]...

Options:

	-t
	--type      Font type. Leave empty for autodetect mode.
	            Valid values are:
					TrueTypeUnicode
					TrueType
					Type1
					CID0JP = CID-0 Japanese
					CID0KR = CID-0 Korean
					CID0CS = CID-0 Chinese Simplified
					CID0CT = CID-0 Chinese Traditional

	-e
	--enc       Name of the encoding table to use. Leave empty for
	            default mode. Omit this parameter for TrueType Unicode
	            and symbolic fonts like Symbol or ZapfDingBats.

	-f
	--flags     Unsigned 32-bit integer containing flags specifying
	            various characteristics of the font (PDF32000:2008 -
	            9.8.2 Font Descriptor Flags): +1 for fixed font; +4 for
	            symbol or +32 for non-symbol; +64 for italic. Fixed and
	            Italic mode are generally autodetected so you have to
	            set it to 32 = non-symbolic font (default) or 4 =
	            symbolic font.

	-o
	--outpath   Output path for generated font files (must be writeable
	            by the web server). Leave empty for default font folder.

	-p
	--platid    Platform ID for CMAP table to extract (when building a
	            Unicode font for Windows this value should be 3, for
	            Macintosh should be 1).

	-n
	--encid     Encoding ID for CMAP table to extract (when building a
	            Unicode font for Windows this value should be 1, for
	            Macintosh should be 0). When Platform ID is 3, legal
	            values for Encoding ID are: 0=Symbol, 1=Unicode,
	            2=ShiftJIS, 3=PRC, 4=Big5, 5=Wansung, 6=Johab,
	            7=Reserved, 8=Reserved, 9=Reserved, 10=UCS-4.

	-b
	--addcbbox  Includes the character bounding box information on the
	            php font file.

	-l
	--link      Link to system font instead of copying the font data #
	            (not transportable) - Note: do not work with Type1 fonts.

	-i
	--fonts     Comma-separated list of input font files.

	-h
	--help      Display this help and exit.
EOD;
	echo $help."\n\n";
	exit(0);
}

// remove the name of the executing script
array_shift($argv);

// no options chosen
if (!is_array($argv)) {
  showHelp();
}

// initialize the array of options
$options = array('type'=>'', 'enc'=>'', 'flags'=>32, 'outpath'=>K_PATH_FONTS, 'platid'=>3, 'encid'=>1, 'addcbbox'=>false, 'link'=>false);

// short input options
$sopt = '';
$sopt .= 't:';
$sopt .= 'e:';
$sopt .= 'f:';
$sopt .= 'o:';
$sopt .= 'p:';
$sopt .= 'n:';
$sopt .= 'b';
$sopt .= 'l';
$sopt .= 'i:';
$sopt .= 'h';

// long input options
$lopt = array();
$lopt[] = 'type:';
$lopt[] = 'enc:';
$lopt[] = 'flags:';
$lopt[] = 'outpath:';
$lopt[] = 'platid:';
$lopt[] = 'encid:';
$lopt[] = 'addcbbox';
$lopt[] = 'link';
$lopt[] = 'fonts:';
$lopt[] = 'help';

// parse input options
$inopt = getopt($sopt, $lopt);

// import options (with some sanitization)
foreach ($inopt as $opt => $val) {
	switch ($opt) {
		case 't':
		case 'type': {
			if (in_array($val, array('TrueTypeUnicode', 'TrueType', 'Type1', 'CID0JP', 'CID0KR', 'CID0CS', 'CID0CT'))) {
				$options['type'] = $val;
			}
			break;
		}
		case 'e':
		case 'enc': {
			$options['enc'] = $val;
			break;
		}
		case 'f':
		case 'flags': {
			$options['flags'] = intval($val);
			break;
		}
		case 'o':
		case 'outpath': {
			$options['outpath'] = realpath($val);
			if (substr($options['outpath'], -1) != '/') {
				$options['outpath'] .= '/';
			}
			break;
		}
		case 'p':
		case 'platid': {
			$options['platid'] = min(max(1, intval($val)), 3);
			break;
		}
		case 'n':
		case 'encid': {
			$options['encid'] = min(max(0, intval($val)), 10);
			break;
		}
		case 'b':
		case 'addcbbox': {
			$options['addcbbox'] = true;
			break;
		}
		case 'l':
		case 'link': {
			$options['link'] = true;
			break;
		}
		case 'i':
		case 'fonts': {
			$options['fonts'] = explode(',', $val);
			break;
		}
		case 'h':
		case 'help':
		default: {
			showHelp();
			break;
		}
	} // end of switch
} // end of while loop

if (empty($options['fonts'])) {
	echo "ERROR: missing input fonts (try --help for usage)\n\n";
	exit(2);
}

// check the output path
if (!is_dir($options['outpath']) OR !is_writable($options['outpath'])) {
	echo "ERROR: Can't write to ".$options['outpath']."\n\n";
	exit(3);
}

echo "\n>>> Converting fonts for TCPDF:\n";

echo '*** Output dir set to '.$options['outpath']."\n";

// check if there are conversion errors
$errors = false;

foreach ($options['fonts'] as $font) {
	$fontfile = realpath($font);
	$fontname = TCPDF_FONTS::addTTFfont($fontfile, $options['type'], $options['enc'], $options['flags'], $options['outpath'], $options['platid'], $options['encid'], $options['addcbbox'], $options['link']);
	if ($fontname === false) {
		$errors = true;
		echo "--- ERROR: can't add ".$font."\n";
	} else {
		echo "+++ OK   : ".$fontfile.' added as '.$fontname."\n";
	}
}

if ($errors) {
	echo "--- Process completed with ERRORS!\n\n";
	exit(4);
}

echo ">>> Process successfully completed!\n\n";
exit(0);

//============================================================+
// END OF FILE
//============================================================+
