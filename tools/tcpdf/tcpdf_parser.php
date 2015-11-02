<?php
//============================================================+
// File name   : tcpdf_parser.php
// Version     : 1.0.001
// Begin       : 2011-05-23
// Last Update : 2012-05-03
// Author      : Nicola Asuni - Tecnick.com LTD - Manor Coach House, Church Hill, Aldershot, Hants, GU12 4RQ, UK - www.tecnick.com - info@tecnick.com
// License     : http://www.tecnick.com/pagefiles/tcpdf/LICENSE.TXT GNU-LGPLv3
// -------------------------------------------------------------------
// Copyright (C) 2011-2012  Nicola Asuni - Tecnick.com LTD
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
// Description : This is a PHP class for parsing PDF documents.
//
//============================================================+

/**
 * @file
 * This is a PHP class for parsing PDF documents.<br>
 * @package com.tecnick.tcpdf
 * @author Nicola Asuni
 * @version 1.0.001
 */

// include class for decoding filters
require_once(dirname(__FILE__).'/tcpdf_filters.php');

/**
 * @class TCPDF_PARSER
 * This is a PHP class for parsing PDF documents.<br>
 * @package com.tecnick.tcpdf
 * @brief This is a PHP class for parsing PDF documents..
 * @version 1.0.001
 * @author Nicola Asuni - info@tecnick.com
 */
class TCPDF_PARSER {

	/**
	 * Raw content of the PDF document.
	 * @private
	 */
	private $pdfdata = '';

	/**
	 * XREF data.
	 * @protected
	 */
	protected $xref = array();

	/**
	 * Array of PDF objects.
	 * @protected
	 */
	protected $objects = array();

	/**
	 * Class object for decoding filters.
	 * @private
	 */
	private $FilterDecoders;

// -----------------------------------------------------------------------------

	/**
	 * Parse a PDF document an return an array of objects.
	 * @param $data (string) PDF data to parse.
	 * @public
	 * @since 1.0.000 (2011-05-24)
	 */
	public function __construct($data) {
		if (empty($data)) {
			$this->Error('Empty PDF data.');
		}
		$this->pdfdata = $data;
		// get length
		$pdflen = strlen($this->pdfdata);
		// initialize class for decoding filters
		$this->FilterDecoders = new TCPDF_FILTERS();
		// get xref and trailer data
		$this->xref = $this->getXrefData();
		// parse all document objects
		$this->objects = array();
		foreach ($this->xref['xref'] as $obj => $offset) {
			if (!isset($this->objects[$obj])) {
				$this->objects[$obj] = $this->getIndirectObject($obj, $offset, true);
			}
		}
		// release some memory
		unset($this->pdfdata);
		$this->pdfdata = '';
	}

	/**
	 * Return an array of parsed PDF document objects.
	 * @return (array) Array of parsed PDF document objects.
	 * @public
	 * @since 1.0.000 (2011-06-26)
	 */
	public function getParsedData() {
		return array($this->xref, $this->objects);
	}

	/**
	 * Get xref (cross-reference table) and trailer data from PDF document data.
	 * @param $offset (int) xref offset (if know).
	 * @param $xref (array) previous xref array (if any).
	 * @return Array containing xref and trailer data.
	 * @protected
	 * @since 1.0.000 (2011-05-24)
	 */
	protected function getXrefData($offset=0, $xref=array()) {
		if ($offset == 0) {
			// find last startxref
			if (preg_match_all('/[\r\n]startxref[\s]*[\r\n]+([0-9]+)[\s]*[\r\n]+%%EOF/i', $this->pdfdata, $matches, PREG_SET_ORDER, $offset) == 0) {
				$this->Error('Unable to find startxref');
			}
			$matches = array_pop($matches);
			$startxref = $matches[1];
		} else {
			// get the first xref at the specified offset
			if (preg_match('/[\r\n]startxref[\s]*[\r\n]+([0-9]+)[\s]*[\r\n]+%%EOF/i', $this->pdfdata, $matches, PREG_OFFSET_CAPTURE, $offset) == 0) {
				$this->Error('Unable to find startxref');
			}
			$startxref = $matches[1][0];
		}
		// check xref position
		if (strpos($this->pdfdata, 'xref', $startxref) != $startxref) {
			$this->Error('Unable to find xref');
		}
		// extract xref data (object indexes and offsets)
		$xoffset = $startxref + 5;
		// initialize object number
		$obj_num = 0;
		$offset = $xoffset;
		while (preg_match('/^([0-9]+)[\s]([0-9]+)[\s]?([nf]?)/im', $this->pdfdata, $matches, PREG_OFFSET_CAPTURE, $offset) > 0) {
			$offset = (strlen($matches[0][0]) + $matches[0][1]);
			if ($matches[3][0] == 'n') {
				// create unique object index: [object number]_[generation number]
				$index = $obj_num.'_'.intval($matches[2][0]);
				// check if object already exist
				if (!isset($xref['xref'][$index])) {
					// store object offset position
					$xref['xref'][$index] = intval($matches[1][0]);
				}
				++$obj_num;
				$offset += 2;
			} elseif ($matches[3][0] == 'f') {
				++$obj_num;
				$offset += 2;
			} else {
				// object number (index)
				$obj_num = intval($matches[1][0]);
			}
		}
		// get trailer data
		if (preg_match('/trailer[\s]*<<(.*)>>[\s]*[\r\n]+startxref[\s]*[\r\n]+/isU', $this->pdfdata, $matches, PREG_OFFSET_CAPTURE, $xoffset) > 0) {
			$trailer_data = $matches[1][0];
			if (!isset($xref['trailer'])) {
				// get only the last updated version
				$xref['trailer'] = array();
				// parse trailer_data
				if (preg_match('/Size[\s]+([0-9]+)/i', $trailer_data, $matches) > 0) {
					$xref['trailer']['size'] = intval($matches[1]);
				}
				if (preg_match('/Root[\s]+([0-9]+)[\s]+([0-9]+)[\s]+R/i', $trailer_data, $matches) > 0) {
					$xref['trailer']['root'] = intval($matches[1]).'_'.intval($matches[2]);
				}
				if (preg_match('/Encrypt[\s]+([0-9]+)[\s]+([0-9]+)[\s]+R/i', $trailer_data, $matches) > 0) {
					$xref['trailer']['encrypt'] = intval($matches[1]).'_'.intval($matches[2]);
				}
				if (preg_match('/Info[\s]+([0-9]+)[\s]+([0-9]+)[\s]+R/i', $trailer_data, $matches) > 0) {
					$xref['trailer']['info'] = intval($matches[1]).'_'.intval($matches[2]);
				}
				if (preg_match('/ID[\s]*[\[][\s]*[<]([^>]*)[>][\s]*[<]([^>]*)[>]/i', $trailer_data, $matches) > 0) {
					$xref['trailer']['id'] = array();
					$xref['trailer']['id'][0] = $matches[1];
					$xref['trailer']['id'][1] = $matches[2];
				}
			}
			if (preg_match('/Prev[\s]+([0-9]+)/i', $trailer_data, $matches) > 0) {
				// get previous xref
				$xref = $this->getXrefData(intval($matches[1]), $xref);
			}
		} else {
			$this->Error('Unable to find trailer');
		}
		return $xref;
	}

	/**
	 * Get object type, raw value and offset to next object
	 * @param $offset (int) Object offset.
	 * @return array containing object type, raw value and offset to next object
	 * @protected
	 * @since 1.0.000 (2011-06-20)
	 */
	protected function getRawObject($offset=0) {
		$objtype = ''; // object type to be returned
		$objval = ''; // object value to be returned
		// skip initial white space chars: \x00 null (NUL), \x09 horizontal tab (HT), \x0A line feed (LF), \x0C form feed (FF), \x0D carriage return (CR), \x20 space (SP)
		$offset += strspn($this->pdfdata, "\x00\x09\x0a\x0c\x0d\x20", $offset);
		// get first char
		$char = $this->pdfdata{$offset};
		// get object type
		switch ($char) {
			case '%': { // \x25 PERCENT SIGN
				// skip comment and search for next token
				$next = strcspn($this->pdfdata, "\r\n", $offset);
				if ($next > 0) {
					$offset += $next;
					return $this->getRawObject($this->pdfdata, $offset);
				}
				break;
			}
			case '/': { // \x2F SOLIDUS
				// name object
				$objtype = $char;
				++$offset;
				if (preg_match('/^([^\x00\x09\x0a\x0c\x0d\x20\s\x28\x29\x3c\x3e\x5b\x5d\x7b\x7d\x2f\x25]+)/', substr($this->pdfdata, $offset, 256), $matches) == 1) {
					$objval = $matches[1]; // unescaped value
					$offset += strlen($objval);
				}
				break;
			}
			case '(':   // \x28 LEFT PARENTHESIS
			case ')': { // \x29 RIGHT PARENTHESIS
				// literal string object
				$objtype = $char;
				++$offset;
				$strpos = $offset;
				if ($char == '(') {
					$open_bracket = 1;
					while ($open_bracket > 0) {
						if (!isset($this->pdfdata{$strpos})) {
							break;
						}
						$ch = $this->pdfdata{$strpos};
						switch ($ch) {
							case '\\': { // REVERSE SOLIDUS (5Ch) (Backslash)
								// skip next character
								++$strpos;
								break;
							}
							case '(': { // LEFT PARENHESIS (28h)
								++$open_bracket;
								break;
							}
							case ')': { // RIGHT PARENTHESIS (29h)
								--$open_bracket;
								break;
							}
						}
						++$strpos;
					}
					$objval = substr($this->pdfdata, $offset, ($strpos - $offset - 1));
					$offset = $strpos;
				}
				break;
			}
			case '[':   // \x5B LEFT SQUARE BRACKET
			case ']': { // \x5D RIGHT SQUARE BRACKET
				// array object
				$objtype = $char;
				++$offset;
				if ($char == '[') {
					// get array content
					$objval = array();
					do {
						// get element
						$element = $this->getRawObject($offset);
						$offset = $element[2];
						$objval[] = $element;
					} while ($element[0] != ']');
					// remove closing delimiter
					array_pop($objval);
				}
				break;
			}
			case '<':   // \x3C LESS-THAN SIGN
			case '>': { // \x3E GREATER-THAN SIGN
				if (isset($this->pdfdata{($offset + 1)}) and ($this->pdfdata{($offset + 1)} == $char)) {
					// dictionary object
					$objtype = $char.$char;
					$offset += 2;
					if ($char == '<') {
						// get array content
						$objval = array();
						do {
							// get element
							$element = $this->getRawObject($offset);
							$offset = $element[2];
							$objval[] = $element;
						} while ($element[0] != '>>');
						// remove closing delimiter
						array_pop($objval);
					}
				} else {
					// hexadecimal string object
					$objtype = $char;
					++$offset;
					if (($char == '<') and (preg_match('/^([0-9A-Fa-f]+)[>]/iU', substr($this->pdfdata, $offset), $matches) == 1)) {
						$objval = $matches[1];
						$offset += strlen($matches[0]);
					}
				}
				break;
			}
			default: {
				if (substr($this->pdfdata, $offset, 6) == 'endobj') {
					// indirect object
					$objtype = 'endobj';
					$offset += 6;
				} elseif (substr($this->pdfdata, $offset, 4) == 'null') {
					// null object
					$objtype = 'null';
					$offset += 4;
					$objval = 'null';
				} elseif (substr($this->pdfdata, $offset, 4) == 'true') {
					// boolean true object
					$objtype = 'boolean';
					$offset += 4;
					$objval = 'true';
				} elseif (substr($this->pdfdata, $offset, 5) == 'false') {
					// boolean false object
					$objtype = 'boolean';
					$offset += 5;
					$objval = 'false';
				} elseif (substr($this->pdfdata, $offset, 6) == 'stream') {
					// start stream object
					$objtype = 'stream';
					$offset += 6;
					if (preg_match('/^[\r\n]+(.*)[\r\n]*endstream/isU', substr($this->pdfdata, $offset), $matches) == 1) {
						$objval = $matches[1];
						$offset += strlen($matches[0]);
					}
				} elseif (substr($this->pdfdata, $offset, 9) == 'endstream') {
					// end stream object
					$objtype = 'endstream';
					$offset += 9;
				} elseif (preg_match('/^([0-9]+)[\s]+([0-9]+)[\s]+R/iU', substr($this->pdfdata, $offset, 33), $matches) == 1) {
					// indirect object reference
					$objtype = 'ojbref';
					$offset += strlen($matches[0]);
					$objval = intval($matches[1]).'_'.intval($matches[2]);
				} elseif (preg_match('/^([0-9]+)[\s]+([0-9]+)[\s]+obj/iU', substr($this->pdfdata, $offset, 33), $matches) == 1) {
					// object start
					$objtype = 'ojb';
					$objval = intval($matches[1]).'_'.intval($matches[2]);
					$offset += strlen($matches[0]);
				} elseif (($numlen = strspn($this->pdfdata, '+-.0123456789', $offset)) > 0) {
					// numeric object
					$objtype = 'numeric';
					$objval = substr($this->pdfdata, $offset, $numlen);
					$offset += $numlen;
				}
				break;
			}
		}
		return array($objtype, $objval, $offset);
	}

	/**
	 * Get content of indirect object.
	 * @param $obj_ref (string) Object number and generation number separated by underscore character.
	 * @param $offset (int) Object offset.
	 * @param $decoding (boolean) If true decode streams.
	 * @return array containing object data.
	 * @protected
	 * @since 1.0.000 (2011-05-24)
	 */
	protected function getIndirectObject($obj_ref, $offset=0, $decoding=true) {
		$obj = explode('_', $obj_ref);
		if (($obj === false) or (count($obj) != 2)) {
			$this->Error('Invalid object reference: '.$obj);
			return;
		}
		$objref = $obj[0].' '.$obj[1].' obj';
		if (strpos($this->pdfdata, $objref, $offset) != $offset) {
			// an indirect reference to an undefined object shall be considered a reference to the null object
			return array('null', 'null', $offset);
		}
		// starting position of object content
		$offset += strlen($objref);
		// get array of object content
		$objdata = array();
		$i = 0; // object main index
		do {
			// get element
			$element = $this->getRawObject($offset);
			$offset = $element[2];
			// decode stream using stream's dictionary information
			if ($decoding and ($element[0] == 'stream') and (isset($objdata[($i - 1)][0])) and ($objdata[($i - 1)][0] == '<<')) {
				$element[3] = $this->decodeStream($objdata[($i - 1)][1], substr($element[1], 1));
			}
			$objdata[$i] = $element;
			++$i;
		} while ($element[0] != 'endobj');
		// remove closing delimiter
		array_pop($objdata);
		// return raw object content
		return $objdata;
	}

	/**
	 * Get the content of object, resolving indect object reference if necessary.
	 * @param $obj (string) Object value.
	 * @return array containing object data.
	 * @protected
	 * @since 1.0.000 (2011-06-26)
	 */
	protected function getObjectVal($obj) {
		if ($obj[0] == 'objref') {
			// reference to indirect object
			if (isset($this->objects[$obj[1]])) {
				// this object has been already parsed
				return $this->objects[$obj[1]];
			} elseif (isset($this->xref[$obj[1]])) {
				// parse new object
				$this->objects[$obj[1]] = $this->getIndirectObject($obj[1], $this->xref[$obj[1]], false);
				return $this->objects[$obj[1]];
			}
		}
		return $obj;
	}

	/**
	 * Decode the specified stream.
	 * @param $sdic (array) Stream's dictionary array.
	 * @param $stream (string) Stream to decode.
	 * @return array containing decoded stream data and remaining filters.
	 * @protected
	 * @since 1.0.000 (2011-06-22)
	 */
	protected function decodeStream($sdic, $stream) {
		// get stream lenght and filters
		$slength = strlen($stream);
		$filters = array();
		foreach ($sdic as $k => $v) {
			if ($v[0] == '/') {
				if (($v[1] == 'Length') and (isset($sdic[($k + 1)])) and ($sdic[($k + 1)][0] == 'numeric')) {
					// get declared stream lenght
					$declength = intval($sdic[($k + 1)][1]);
					if ($declength < $slength) {
						$stream = substr($stream, 0, $declength);
						$slength = $declength;
					}
				} elseif (($v[1] == 'Filter') and (isset($sdic[($k + 1)]))) {
					// resolve indirect object
					$objval = $this->getObjectVal($sdic[($k + 1)]);
					if ($objval[0] == '/') {
						// single filter
						$filters[] = $objval[1];
					} elseif ($objval[0] == '[') {
						// array of filters
						foreach ($objval[1] as $flt) {
							if ($flt[0] == '/') {
								$filters[] = $flt[1];
							}
						}
					}
				}
			}
		}
		// decode the stream
		$remaining_filters = array();
		foreach ($filters as $filter) {
			if (in_array($filter, $this->FilterDecoders->getAvailableFilters())) {
				$stream = $this->FilterDecoders->decodeFilter($filter, $stream);
			} else {
				// add missing filter to array
				$remaining_filters[] = $filter;
			}
		}
		return array($stream, $remaining_filters);
	}

	/**
	 * This method is automatically called in case of fatal error; it simply outputs the message and halts the execution.
	 * @param $msg (string) The error message
	 * @public
	 * @since 1.0.000 (2011-05-23)
	 */
	public function Error($msg) {
		// exit program and print error
		die('<strong>TCPDF_PARSER ERROR: </strong>'.$msg);
	}

} // END OF TCPDF_PARSER CLASS

//============================================================+
// END OF FILE
//============================================================+

