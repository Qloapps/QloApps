<?php
/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class WebserviceOutputXMLCore implements WebserviceOutputInterface
{
    public $docUrl = '';
    public $languages = array();
    protected $wsUrl;
    protected $schemaToDisplay;

    public function setSchemaToDisplay($schema)
    {
        if (is_string($schema)) {
            $this->schemaToDisplay = $schema;
        }
        return $this;
    }

    public function getSchemaToDisplay()
    {
        return $this->schemaToDisplay;
    }

    public function setWsUrl($url)
    {
        $this->wsUrl = $url;
        return $this;
    }
    public function getWsUrl()
    {
        return $this->wsUrl;
    }
    public function getContentType()
    {
        return 'text/xml';
    }
    public function __construct($languages = array())
    {
        $this->languages = $languages;
    }
    public function setLanguages($languages)
    {
        $this->languages = $languages;
        return $this;
    }
    public function renderErrorsHeader()
    {
        return '<errors>'."\n";
    }
    public function renderErrorsFooter()
    {
        return '</errors>'."\n";
    }
    public function renderErrors($message, $code = null)
    {
        $str_output = '<error>'."\n";
        if ($code !== null) {
            $str_output .= '<code><![CDATA['.$code.']]></code>'."\n";
        }
        $str_output .= '<message><![CDATA['.$message.']]></message>'."\n";
        $str_output .= '</error>'."\n";
        return $str_output;
    }
    public function renderField($field)
    {
        $ret = '';
        $node_content = '';
        $ret .= '<'.$field['sqlId'];
        // display i18n fields
        if (isset($field['i18n']) && $field['i18n']) {
            foreach ($this->languages as $language) {
                $more_attr = '';
                if (isset($field['synopsis_details']) || (isset($field['value']) && is_array($field['value']))) {
                    $more_attr .= ' xlink:href="'.$this->getWsUrl().'languages/'.$language.'"';
                    if (isset($field['synopsis_details']) && $this->schemaToDisplay != 'blank') {
                        $more_attr .= ' format="isUnsignedId" ';
                    }
                }
                $node_content .= '<language id="'.$language.'"'.$more_attr.'>';
                if (isset($field['value']) &&  is_array($field['value']) &&  isset($field['value'][$language])) {
                    $node_content .= '<![CDATA['.$field['value'][$language].']]>';
                }
                $node_content .= '</language>';
            }
        }
        // display not i18n fields value
        else {
            if (array_key_exists('xlink_resource', $field) && $this->schemaToDisplay != 'blank') {
                if (!is_array($field['xlink_resource'])) {
                    $ret .= ' xlink:href="'.$this->getWsUrl().$field['xlink_resource'].'/'.$field['value'].'"';
                } else {
                    $ret .= ' xlink:href="'.$this->getWsUrl().$field['xlink_resource']['resourceName'].'/'.
                    (isset($field['xlink_resource']['subResourceName']) ? $field['xlink_resource']['subResourceName'].'/'.$field['object_id'].'/' : '').$field['value'].'"';
                }
            }

            if (isset($field['getter']) && $this->schemaToDisplay != 'blank') {
                $ret .= ' notFilterable="true"';
            }

            if (isset($field['setter']) && $field['setter'] == false && $this->schemaToDisplay == 'synopsis') {
                $ret .= ' read_only="true"';
            }

            // value is converted to string to be safe from strange php comparisions (string with integer)
            // php-8 converts integer to string before comparison so we are also type casting value to string
            // https://www.php.net/releases/8.0/en.php#saner-string-to-number-comparisons
            if ((string)$field['value'] != '') {
                $node_content .= '<![CDATA['.$field['value'].']]>';
            }
        }

        if (isset($field['encode'])) {
            $ret .= ' encode="'.$field['encode'].'"';
        }

        if (isset($field['synopsis_details']) && !empty($field['synopsis_details']) && $this->schemaToDisplay !== 'blank') {
            foreach ($field['synopsis_details'] as $name => $detail) {
                $ret .= ' '.$name.'="'.(is_array($detail) ? implode(' ', $detail) : $detail).'"';
            }
        }
        $ret .= '>';
        $ret .= $node_content;
        $ret .= '</'.$field['sqlId'].'>'."\n";
        return $ret;
    }
    public function renderNodeHeader($node_name, $params, $more_attr = null, $has_child = true)
    {
        $string_attr = '';
        if (is_array($more_attr)) {
            foreach ($more_attr as $key => $attr) {
                if ($key === 'xlink_resource') {
                    $string_attr .= ' xlink:href="'.$attr.'"';
                } else {
                    $string_attr .= ' '.$key.'="'.$attr.'"';
                }
            }
        }
        $end_tag = (!$has_child) ? '/>' : '>';
        return '<'.$node_name.$string_attr.$end_tag."\n";
    }
    public function getNodeName($params)
    {
        $node_name = '';
        if (isset($params['objectNodeName'])) {
            $node_name = $params['objectNodeName'];
        }
        return $node_name;
    }
    public function renderNodeFooter($node_name, $params)
    {
        return '</'.$node_name.'>'."\n";
    }
    public function overrideContent($content)
    {
        // changed QloApps in place of prestashop
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<qloapps xmlns:xlink="http://www.w3.org/1999/xlink">'."\n";
        $xml .= $content;
        $xml .= '</qloapps>'."\n";
        return $xml;
    }
    public function renderAssociationWrapperHeader()
    {
        return '<associations>'."\n";
    }
    public function renderAssociationWrapperFooter()
    {
        return '</associations>'."\n";
    }
    public function renderAssociationHeader($obj, $params, $assoc_name, $closed_tags = false)
    {
        $end_tag = ($closed_tags) ? '/>' : '>';
        $more = '';
        if ($this->schemaToDisplay != 'blank') {
            if (array_key_exists('setter', $params['associations'][$assoc_name]) && !$params['associations'][$assoc_name]['setter']) {
                $more .= ' readOnly="true"';
            }
            $more .= ' nodeType="'.$params['associations'][$assoc_name]['resource'].'"';
            if (isset($params['associations'][$assoc_name]['virtual_entity']) && $params['associations'][$assoc_name]['virtual_entity']) {
                $more .= ' virtualEntity="true"';
            } else {
                if (isset($params['associations'][$assoc_name]['api'])) {
                    $more .= ' api="'.$params['associations'][$assoc_name]['api'].'"';
                } else {
                    $more .= ' api="'.$assoc_name.'"';
                }
            }
        }
        return '<'.$assoc_name.$more.$end_tag."\n";
    }
    public function renderAssociationFooter($obj, $params, $assoc_name)
    {
        return '</'.$assoc_name.'>'."\n";
    }
}
