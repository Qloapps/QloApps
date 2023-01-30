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
*  @version  Release: $Revision: 14001 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class WebserviceOutputJSON implements WebserviceOutputInterface
{
    public $docUrl = '';
    public $languages = array();
    protected $wsUrl;
    protected $schemaToDisplay;

    /**
     * Current entity
     */
    protected $currentEntity;

    /**
     * Current association
     */
    protected $currentAssociatedEntity = array();

    /**
     * Json content
     */
    protected $content = array();

    public function __construct($languages = array())
    {
        $this->languages = $languages;
    }

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
        return 'application/json';
    }

    public function renderErrors($message, $code = null)
    {
        $this->content['errors'][] = array('code' => $code, 'message' => $message);
        return '';
    }

    public function renderField($field)
    {
        $is_association = (isset($field['is_association']) && $field['is_association'] == true);

        if (is_array($field['value'])) {
            $tmp = array();
            foreach ($this->languages as $id_lang) {
                $tmp[] = array('id' => $id_lang, 'value' => $field['value'][$id_lang]);
            }
            if (count($tmp) == 1) {
                $field['value'] = $tmp[0]['value'];
            } else {
                $field['value'] = $tmp;
            }
        }
        // Case 1 : fields of the current entity (not an association)
        if (!$is_association) {
            $this->currentEntity[$field['sqlId']]  = $field['value'];
        } else { // Case 2 : fields of an associated entity to the current one
            $this->currentAssociatedEntity[] = array('name' => $field['entities_name'], 'key' => $field['sqlId'], 'value' => $field['value']);
        }
        return '';
    }

    public function renderNodeHeader($node_name, $params, $more_attr = null, $has_child = true)
    {
        // api ?
        static $isAPICall = false;
        if ($node_name == 'api' && ($isAPICall == false)) {
            $isAPICall = true;
        }
        if ($isAPICall && !in_array($node_name, array('description', 'schema', 'api'))) {
            $this->content[] = $node_name;
        }
        if (isset($more_attr, $more_attr['id'])) {
            $this->content[$params['objectsNodeName']][] = array('id' => $more_attr['id']);
        }
        return '';
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
        if (isset($params['objectNodeName']) && $params['objectNodeName'] == $node_name) {
            if (array_key_exists('display', $_GET)) {
                $this->content[$params['objectsNodeName']][] = $this->currentEntity;
            } else {
                $this->content[$params['objectNodeName']] = $this->currentEntity;
            }
            $this->currentEntity = array();
        }
        if (count($this->currentAssociatedEntity)) {
            $current = array();
            foreach ($this->currentAssociatedEntity as $element) {
                $current[$element['key']] = $element['value'];
            }
            //$this->currentEntity['associations'][$element['name']][][$element['key']] = $element['value'];
            $this->currentEntity['associations'][$element['name']][] = $current;
            $this->currentAssociatedEntity = array();
        }
    }

    public function overrideContent($content)
    {
        $content = '';
        $content .= json_encode($this->content);
        $content = preg_replace_callback("/\\\\u([a-f0-9]{4})/", function ($matches) {
            return iconv('UCS-4LE','UTF-8', pack('V', hexdec('U' . $matches[1])));
        }, $content);
        return $content;
    }

    public function setLanguages($languages)
    {
        $this->languages = $languages;
        return $this;
    }

    public function renderAssociationWrapperHeader()
    {
        return '';
    }
    public function renderAssociationWrapperFooter()
    {
        return '';
    }
    public function renderAssociationHeader($obj, $params, $assoc_name, $closed_tags = false)
    {
        return '';
    }
    public function renderAssociationFooter($obj, $params, $assoc_name)
    {
        return;
    }
    public function renderErrorsHeader()
    {
        return '';
    }
    public function renderErrorsFooter()
    {
        return '';
    }
    public function renderAssociationField($field)
    {
        return '';
    }
    public function renderi18nField($field)
    {
        return '';
    }
}
