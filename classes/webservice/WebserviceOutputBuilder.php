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

/**
 * @todo : Create typed exception for more finer errors check
 */
class WebserviceOutputBuilderCore
{
    /**
     * @var int constant
     */
    const VIEW_LIST = 1;
    const VIEW_DETAILS = 2;

    protected $wsUrl;
    protected $output;

    /** @var WebserviceOutputInterface|WebserviceOutputXML|WebserviceOutputJSON */
    public $objectRender;
    protected $wsResource;
    protected $depth = 0;
    protected $schemaToDisplay;
    protected $fieldsToDisplay;
    protected $specificFields = array();
    protected $virtualFields = array();
    protected $statusInt;
    protected $wsParamOverrides;

    protected static $_cache_ws_parameters = array();

    /* Header properties */
    protected $headerParams = array(
        'Access-Time'    => 0,
        'X-Powered-By'    => 0,
        'PSWS-Version'    => 0,
        'Content-Type'    => 0,
    );

    /**
     * @var string Status header sent at return
     */
    protected $status;

    public function __construct($ws_url)
    {
        $this->statusInt = 200;
        $this->status = $_SERVER['SERVER_PROTOCOL'].' 200 OK';
        $this->wsUrl = $ws_url;
        $this->wsParamOverrides = array();
    }

    /**
     * Set the render object for set the output format.
     * Set the Content-type for the http header.
     *
     * @param WebserviceOutputInterface $obj_render
     * @throw WebserviceException if the object render is not an instance of WebserviceOutputInterface
     *
     * @return WebserviceOutputBuilder
     * @throws WebserviceException
     */
    public function setObjectRender(WebserviceOutputInterface $obj_render)
    {
        if (!$obj_render instanceof WebserviceOutputInterface) {
            throw new WebserviceException('Obj_render param must be an WebserviceOutputInterface object type', array(83, 500));
        }

        $this->objectRender = $obj_render;
        $this->objectRender->setWsUrl($this->wsUrl);
        if ($this->objectRender->getContentType()) {
            $this->setHeaderParams('Content-Type', $this->objectRender->getContentType());
        }
        return $this;
    }

    /**
     * getter
     * @return WebserviceOutputInterface
     */
    public function getObjectRender()
    {
        return $this->objectRender;
    }

    /**
     * Need to have the resource list to get the class name for an entity,
     * To build
     *
     * @param array $resources
     *
     * @return WebserviceOutputBuilder
     */
    public function setWsResources($resources)
    {
        $this->wsResource = $resources;
        return $this;
    }

    /**
     * This method return an array with each http header params for a content.
     * This check each required params.
     *
     * If this method is overrided don't forget to check required specific params (for xml etc...)
     *
     * @return array
     */
    public function buildHeader()
    {
        $return = array();
        $return[] = $this->status;
        foreach ($this->headerParams as $key => $param) {
            $return[] = trim($key).': '.$param;
        }
        return $return;
    }

    /**
     * @param string $key The normalized key expected for an http response
     * @param string $value
     *
     * @return WebserviceOutputBuilder
     * @throws WebserviceException If the key or the value are corrupted (use Validate::isCleanHtml method)
     */
    public function setHeaderParams($key, $value)
    {
        if (!Validate::isCleanHtml($key) || !Validate::isCleanHtml($value)) {
            throw new WebserviceException('the key or your value is corrupted.', array(94, 500));
        }
        $this->headerParams[$key] = $value;
        return $this;
    }

    /**
     * @param null|string $key if null get all header params otherwise the params specified by the key
     * @throw WebserviceException if the key is corrupted (use Validate::isCleanHtml method)
     * @throw WebserviceException if the asked key does'nt exists.
     * @return array|string
     */
    public function getHeaderParams($key = null)
    {
        $return = '';

        if (!is_null($key)) {
            if (!Validate::isCleanHtml($key)) {
                throw new WebserviceException('the key you write is a corrupted text.', array(95, 500));
            }
            if (!array_key_exists($key, $this->headerParams)) {
                throw new WebserviceException(sprintf('The key %s does\'nt exist', $key), array(96, 500));
            }
            $return = $this->headerParams[$key];
        } else {
            $return = $this->headerParams;
        }

        return $return;
    }

    /**
     * Delete all Header parameters previously set.
     *
     * @return WebserviceOutputBuilder
     */
    public function resetHeaderParams()
    {
        $this->headerParams = array();
        return $this;
    }

    /**
     * @return string the normalized status for http request
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function getStatusInt()
    {
        return $this->statusInt;
    }
    /**
     * Set the return header status
     *
     * @param int $num the Http status code
     * @return void
     */
    public function setStatus($num)
    {
        $this->statusInt = (int)$num;
        switch ($num) {
            case 200 :
                $this->status = $_SERVER['SERVER_PROTOCOL'].' 200 OK';
                break;
            case 201 :
                $this->status = $_SERVER['SERVER_PROTOCOL'].' 201 Created';
                break;
            case 204 :
                $this->status = $_SERVER['SERVER_PROTOCOL'].' 204 No Content';
                break;
            case 304 :
                $this->status = $_SERVER['SERVER_PROTOCOL'].' 304 Not Modified';
                break;
            case 400 :
                $this->status = $_SERVER['SERVER_PROTOCOL'].' 400 Bad Request';
                break;
            case 401 :
                $this->status = $_SERVER['SERVER_PROTOCOL'].' 401 Unauthorized';
                break;
            case 403 :
                $this->status = $_SERVER['SERVER_PROTOCOL'].' 403 Forbidden';
                break;
            case 404 :
                $this->status = $_SERVER['SERVER_PROTOCOL'].' 404 Not Found';
                break;
            case 405 :
                $this->status = $_SERVER['SERVER_PROTOCOL'].' 405 Method Not Allowed';
                break;
            case 500 :
                $this->status = $_SERVER['SERVER_PROTOCOL'].' 500 Internal Server Error';
                break;
            case 501 :
                $this->status = $_SERVER['SERVER_PROTOCOL'].' 501 Not Implemented';
                break;
            case 503 :
                $this->status = $_SERVER['SERVER_PROTOCOL'].' 503 Service Unavailable';
                break;
        }
    }

    /**
     * Build errors output using an error array
     *
     * @param array $errors
     * @return string output in the format specified by WebserviceOutputBuilder::objectRender
     */
    public function getErrors($errors)
    {
        if (!empty($errors)) {
            if (isset($this->objectRender)) {
                $str_output = $this->objectRender->renderErrorsHeader();
                foreach ($errors as $error) {
                    if (is_array($error)) {
                        $str_output .= $this->objectRender->renderErrors($error[1], $error[0]);
                    } else {
                        $str_output .= $this->objectRender->renderErrors($error);
                    }
                }
                $str_output .= $this->objectRender->renderErrorsFooter();
                $str_output = $this->objectRender->overrideContent($str_output);
            } else {
                $str_output = '<pre>'.print_r($errors, true).'</pre>';
            }
        }
        return $str_output;
    }

    /**
     * Build the resource list in the output format specified by WebserviceOutputBuilder::objectRender
     * @param $key_permissions
     * @return string
     */
    public function getResourcesList($key_permissions)
    {
        if (is_null($this->wsResource)) {
            throw new WebserviceException('You must set web service resource for get the resources list.', array(82, 500));
        }
        $output = '';
        $more_attr = array('shopName' => htmlspecialchars(Configuration::get('PS_SHOP_NAME')));
        $output .= $this->objectRender->renderNodeHeader('api', array(), $more_attr);
        foreach ($this->wsResource as $resourceName => $resource) {
            if (in_array($resourceName, array_keys($key_permissions))) {
                $more_attr = array(
                    'xlink_resource'    => $this->wsUrl.$resourceName,
                    'get'                => (in_array('GET', $key_permissions[$resourceName]) ? 'true' : 'false'),
                    'put'                => (in_array('PUT', $key_permissions[$resourceName]) ? 'true' : 'false'),
                    'post'                => (in_array('POST', $key_permissions[$resourceName]) ? 'true' : 'false'),
                    'delete'            => (in_array('DELETE', $key_permissions[$resourceName]) ? 'true' : 'false'),
                    'head'                => (in_array('HEAD', $key_permissions[$resourceName]) ? 'true' : 'false'),
                );
                $output .= $this->objectRender->renderNodeHeader($resourceName, array(), $more_attr);

                $output .= $this->objectRender->renderNodeHeader('description', array(), $more_attr);
                $output .= $resource['description'];
                $output .= $this->objectRender->renderNodeFooter('description', array());

                if (!isset($resource['specific_management']) || !$resource['specific_management']) {
                    $more_attr_schema = array(
                        'xlink_resource'    => $this->wsUrl.$resourceName.'?schema=blank',
                        'type'                => 'blank',
                    );
                    $output .= $this->objectRender->renderNodeHeader('schema', array(), $more_attr_schema, false);
                    $more_attr_schema = array(
                        'xlink_resource'    => $this->wsUrl.$resourceName.'?schema=synopsis',
                        'type'                => 'synopsis',
                    );
                    $output .= $this->objectRender->renderNodeHeader('schema', array(), $more_attr_schema, false);
                }
                $output .= $this->objectRender->renderNodeFooter($resourceName, array());
            }
        }
        $output .= $this->objectRender->renderNodeFooter('api', array());
        $output = $this->objectRender->overrideContent($output);
        return $output;
    }

    public function registerOverrideWSParameters($wsrObject, $method)
    {
        $this->wsParamOverrides[] = array('object' => $wsrObject, 'method' => $method);
    }

    /**
     * Method is used for each content type
     * Different content types are :
     * 		- list of entities,
     * 		- tree diagram of entity details (full or minimum),
     * 		- schema (synopsis & blank),
     *
     * @param array $objects each object created by entity asked
     * 		  @see WebserviceOutputBuilder::executeEntityGetAndHead
     * @param null|string $schema_to_display if null display the entities list or entity details.
     * @param string|array $fields_to_display the fields allow for the output
     * @param int $depth depth for the tree diagram output.
     * @param int $type_of_view use the 2 constants WebserviceOutputBuilder::VIEW_LIST WebserviceOutputBuilder::VIEW_DETAILS
     * @return string in the output format specified by WebserviceOutputBuilder::objectRender
     */
    public function getContent($objects, $schema_to_display = null, $fields_to_display = 'minimum', $depth = 0, $type_of_view = self::VIEW_LIST, $override = true)
    {
        $this->fieldsToDisplay = $fields_to_display;
        $this->depth = $depth;
        $output = '';

        if ($schema_to_display != null) {
            $this->schemaToDisplay = $schema_to_display;
            $this->objectRender->setSchemaToDisplay($this->schemaToDisplay);

            // If a shema is asked the view must be an details type
            $type_of_view = self::VIEW_DETAILS;
        }

        $class = get_class($objects['empty']);
        if (!isset(WebserviceOutputBuilder::$_cache_ws_parameters[$class])) {
            WebserviceOutputBuilder::$_cache_ws_parameters[$class] = $objects['empty']->getWebserviceParameters();
        }
        $ws_params = WebserviceOutputBuilder::$_cache_ws_parameters[$class];

        foreach ($this->wsParamOverrides as $p) {
            $object = $p['object'];
            $method = $p['method'];
            $ws_params = $object->$method($ws_params);
        }

        // If a list is asked, need to wrap with a plural node
        if ($type_of_view === self::VIEW_LIST) {
            $output .= $this->setIndent($depth).$this->objectRender->renderNodeHeader($ws_params['objectsNodeName'], $ws_params);
        }

        if (is_null($this->schemaToDisplay)) {
            foreach ($objects as $key => $object) {
                if ($key !== 'empty') {
                    if ($this->fieldsToDisplay === 'minimum') {
                        $output .= $this->renderEntityMinimum($object, $depth);
                    } else {
                        $output .= $this->renderEntity($object, $depth);
                    }
                }
            }
        } else {
            $output .= $this->renderSchema($objects['empty'], $ws_params);
        }

        // If a list is asked, need to wrap with a plural node
        if ($type_of_view === self::VIEW_LIST) {
            $output .= $this->setIndent($depth).$this->objectRender->renderNodeFooter($ws_params['objectsNodeName'], $ws_params);
        }

        if ($override) {
            $output = $this->objectRender->overrideContent($output);
        }
        return $output;
    }

    /**
     * Create the tree diagram with no details
     *
     * @param ObjectModel $object create by the entity
     * @param int $depth the depth for the tree diagram
     * @return string
     */
    public function renderEntityMinimum($object, $depth)
    {
        $class = get_class($object);
        if (!isset(WebserviceOutputBuilder::$_cache_ws_parameters[$class])) {
            WebserviceOutputBuilder::$_cache_ws_parameters[$class] = $object->getWebserviceParameters();
        }
        $ws_params = WebserviceOutputBuilder::$_cache_ws_parameters[$class];

        $more_attr['id'] = $object->id;
        $more_attr['xlink_resource'] = $this->wsUrl.$ws_params['objectsNodeName'].'/'.$object->id;
        $output = $this->setIndent($depth).$this->objectRender->renderNodeHeader($ws_params['objectNodeName'], $ws_params, $more_attr, false);
        return $output;
    }

    /**
     * Build a schema blank or synopsis
     *
     * @param ObjectModel $object create by the entity
     * @param array $ws_params webserviceParams from the entity
     * @return string
     */
    protected function renderSchema($object, $ws_params)
    {
        $output = $this->objectRender->renderNodeHeader($ws_params['objectNodeName'], $ws_params);
        foreach ($ws_params['fields'] as $field_name => $field) {
            $output .= $this->renderField($object, $ws_params, $field_name, $field, 0);
        }
        if (isset($ws_params['associations']) && count($ws_params['associations']) > 0) {
            $this->fieldsToDisplay = 'full';
            $output .= $this->renderAssociations($object, 0, $ws_params['associations'], $ws_params);
        }
        $output .= $this->objectRender->renderNodeFooter($ws_params['objectNodeName'], $ws_params);
        return $output;
    }

    /**
     * Build the entity detail.
     *
     * @param ObjectModel $object create by the entity
     * @param int $depth the depth for the tree diagram
     * @return string
     */
    public function renderEntity($object, $depth)
    {
        $output = '';

        $class = get_class($object);
        if (!isset(WebserviceOutputBuilder::$_cache_ws_parameters[$class])) {
            WebserviceOutputBuilder::$_cache_ws_parameters[$class] = $object->getWebserviceParameters();
        }
        $ws_params = WebserviceOutputBuilder::$_cache_ws_parameters[$class];

        foreach ($this->wsParamOverrides as $p) {
            $o = $p['object'];
            $method = $p['method'];
            $ws_params = $o->$method($ws_params);
        }
        $output .= $this->setIndent($depth).$this->objectRender->renderNodeHeader($ws_params['objectNodeName'], $ws_params);

        if ($object->id != 0) {
            // This to add virtual Fields for a particular entity.
            $virtual_fields = $this->addVirtualFields($ws_params['objectsNodeName'], $object);
            if (!empty($virtual_fields)) {
                $ws_params['fields'] = array_merge($ws_params['fields'], $virtual_fields);
            }

            foreach ($ws_params['fields'] as $field_name => $field) {
                if ($this->fieldsToDisplay === 'full' || array_key_exists($field_name, $this->fieldsToDisplay)) {
                    $field['object_id'] = $object->id;
                    $field['entity_name'] = $ws_params['objectNodeName'];
                    $field['entities_name'] = $ws_params['objectsNodeName'];
                    $output .= $this->renderField($object, $ws_params, $field_name, $field, $depth);
                }
            }
        }
        $subexists = false;
        if (is_array($this->fieldsToDisplay)) {
            foreach ($this->fieldsToDisplay as $fields) {
                if (is_array($fields)) {
                    $subexists = true;
                }
            }
        }

        if (isset($ws_params['associations'])
            && ($this->fieldsToDisplay == 'full'
            || $subexists)) {
            $output .= $this->renderAssociations($object, $depth, $ws_params['associations'], $ws_params);
        }

        $output .= $this->setIndent($depth).$this->objectRender->renderNodeFooter($ws_params['objectNodeName'], $ws_params);
        return $output;
    }

    /**
     * Build a field and use recursivity depend on the depth parameter.
     *
     * @param ObjectModel $object create by the entity
     * @param array $ws_params webserviceParams from the entity
     * @param string $field_name
     * @param array $field
     * @param int $depth
     * @return string
     */
    protected function renderField($object, $ws_params, $field_name, $field, $depth)
    {
        $output = '';
        $show_field = true;

        if (isset($ws_params['hidden_fields']) && in_array($field_name, $ws_params['hidden_fields'])) {
            return;
        }

        if ($this->schemaToDisplay === 'synopsis') {
            $field['synopsis_details'] = $this->getSynopsisDetails($field);
            if ($field_name === 'id') {
                $show_field = false;
            }
        }
        if ($this->schemaToDisplay === 'blank') {
            if (isset($field['setter']) && !$field['setter']) {
                $show_field = false;
            }
        }

        // don't set any value for a schema
        if (isset($field['synopsis_details']) || $this->schemaToDisplay === 'blank') {
            $field['value'] = '';
            if (isset($field['xlink_resource'])) {
                unset($field['xlink_resource']);
            }
        } elseif (isset($field['getter']) && $object != null && method_exists($object, $field['getter'])) {
            $field_getter = $field['getter'];
            $field['value'] = $object->$field_getter();
        } elseif (!isset($field['value'])) {
            $field['value'] = $object->$field_name;
        }

        // this apply specific function for a particular field on a choosen entity
        $field = $this->overrideSpecificField($ws_params['objectsNodeName'], $field_name, $field, $object, $ws_params);

        // don't display informations for a not existant id
        if (substr($field['sqlId'], 0, 3) == 'id_' && !$field['value']) {
            if ($field['value'] === null) {
                $field['value'] = '';
            }
            // delete the xlink except for schemas
            if (isset($field['xlink_resource']) && is_null($this->schemaToDisplay)) {
                unset($field['xlink_resource']);
            }
        }
        // set "id" for each node name which display the id of the entity
        if ($field_name === 'id') {
            $field['sqlId'] = 'id';
        }

        // don't display the node id for a synopsis schema
        if ($show_field) {
            $output .= $this->setIndent($depth - 1).$this->objectRender->renderField($field);
        }
        return $output;
    }

    /**
     *
     *
     * @param $object
     * @param $depth
     * @param $associations
     * @param $ws_params
     * @return string
     */
    protected function renderAssociations($object, $depth, $associations, $ws_params)
    {
        $output = $this->objectRender->renderAssociationWrapperHeader();
        foreach ($associations as $assoc_name => $association) {
            if ($this->fieldsToDisplay == 'full' || is_array($this->fieldsToDisplay) && array_key_exists($assoc_name, $this->fieldsToDisplay)) {
                $getter = $association['getter'];
                $objects_assoc = array();

                $fields_assoc = array();
                if (isset($association['fields'])) {
                    $fields_assoc = $association['fields'];
                }

                $parent_details = array(
                    'object_id'    => $object->id,
                    'entity_name'    => $ws_params['objectNodeName'],
                    'entities_name'    => $ws_params['objectsNodeName'],
                );

                if (is_array($getter)) {
                    $association_resources = call_user_func($getter, $object);
                    if (is_array($association_resources) && !empty($association_resources)) {
                        foreach ($association_resources as $association_resource) {
                            $objects_assoc[] = $association_resource;
                        }
                    }
                } else {
                    if (method_exists($object, $getter) && is_null($this->schemaToDisplay)) {
                        $association_resources = $object->$getter();
                        if (is_array($association_resources) && !empty($association_resources)) {
                            foreach ($association_resources as $association_resource) {
                                $objects_assoc[] = $association_resource;
                            }
                        }
                    } else {
                        $objects_assoc[] = '';
                    }
                }

                $class_name = null;
                if (isset($this->wsResource[$assoc_name]['class']) && class_exists($this->wsResource[$assoc_name]['class'], true)) {
                    $class_name = $this->wsResource[$assoc_name]['class'];
                }
                $output_details = '';
                foreach ($objects_assoc as $object_assoc) {
                    if ($depth == 0 || $class_name === null) {
                        $value = null;
                        if (!empty($object_assoc)) {
                            $value = $object_assoc;
                        }
                        if (empty($fields_assoc)) {
                            $fields_assoc = array(array('id' => $value['id']));
                        }
                        $output_details .= $this->renderFlatAssociation($object, $depth, $assoc_name, $association['resource'], $fields_assoc, $value, $parent_details);
                    } else {
                        foreach ($object_assoc as $id) {
                            if ($class_name !== null) {
                                $child_object = new $class_name($id);
                                $output_details .= $this->renderEntity($child_object, ($depth - 2 ? 0 : $depth - 2));
                            }
                        }
                    }
                }
                if ($output_details != '') {
                    $output .= $this->setIndent($depth).$this->objectRender->renderAssociationHeader($object, $ws_params, $assoc_name);
                    $output .= $output_details;
                    $output .= $this->setIndent($depth).$this->objectRender->renderAssociationFooter($object, $ws_params, $assoc_name);
                } else {
                    $output .= $this->setIndent($depth).$this->objectRender->renderAssociationHeader($object, $ws_params, $assoc_name, true);
                }
            }
        }
        $output .= $this->objectRender->renderAssociationWrapperFooter();
        return $output;
    }

    protected function renderFlatAssociation($object, $depth, $assoc_name, $resource_name, $fields_assoc, $object_assoc, $parent_details)
    {
        $output = '';
        $more_attr = array();
        if (isset($this->wsResource[$assoc_name]) && is_null($this->schemaToDisplay)) {
            if ($assoc_name == 'images') {
                if ($parent_details['entities_name'] == 'combinations') {
                    $more_attr['xlink_resource'] = $this->wsUrl.$assoc_name.'/products/'.$object->id_product.'/'.$object_assoc['id'];
                } else {
                    $more_attr['xlink_resource'] = $this->wsUrl.$assoc_name.'/'.$parent_details['entities_name'].'/'.$parent_details['object_id'].'/'.$object_assoc['id'];
                }
            } else {
                $more_attr['xlink_resource'] = $this->wsUrl.$assoc_name.'/'.$object_assoc['id'];
            }
        }
        $output .= $this->setIndent($depth - 1).$this->objectRender->renderNodeHeader($resource_name, array(), $more_attr);

        foreach ($fields_assoc as $field_name => $field) {
            if (!is_array($this->fieldsToDisplay) || in_array($field_name, $this->fieldsToDisplay[$assoc_name])) {
                if ($field_name == 'id' && !isset($field['sqlId'])) {
                    $field['sqlId'] = 'id';
                    $field['value'] = $object_assoc['id'];
                } elseif (!isset($field['sqlId'])) {
                    $field['sqlId'] = $field_name;
                    $field['value'] = $object_assoc[$field_name];
                }
                $field['entities_name'] = $assoc_name;
                $field['entity_name'] = $resource_name;

                if (!is_null($this->schemaToDisplay)) {
                    $field['synopsis_details'] = $this->getSynopsisDetails($field);
                }
                $field['is_association'] = true;
                $output .= $this->setIndent($depth - 1).$this->objectRender->renderField($field);
            }
        }
        $output .= $this->setIndent($depth - 1).$this->objectRender->renderNodeFooter($resource_name, array());
        return $output;
    }

    public function setIndent($depth)
    {
        $string = '';
        $number_of_tabs = $this->depth - $depth;
        for ($i = 0; $i < $number_of_tabs; $i++) {
            $string .= "\t";
        }
        return $string;
    }

    public function getSynopsisDetails($field)
    {
        $arr_details = array();
        if (array_key_exists('required', $field) && $field['required']) {
            $arr_details['required'] = 'true';
        }
        if (array_key_exists('maxSize', $field) && $field['maxSize']) {
            $arr_details['maxSize'] = $field['maxSize'];
        }
        if (array_key_exists('validateMethod', $field) && $field['validateMethod']) {
            $arr_details['format'] = $field['validateMethod'];
        }
        if (array_key_exists('setter', $field) && !$field['setter']) {
            $arr_details['readOnly'] = 'true';
        }
        return $arr_details;
    }

    /**
     * @param string|object $object
     * @param string $method
     * @param $field_name
     * @param $entity_name
     *
     * @return WebserviceOutputBuilder
     * @throws Exception
     * @throws WebserviceException
     */
    public function setSpecificField($object, $method, $field_name, $entity_name)
    {
        try {
            $this->validateObjectAndMethod($object, $method);
        } catch (WebserviceException $e) {
            throw $e;
        }

        $this->specificFields[$field_name] = array('entity'=>$entity_name, 'object' => $object, 'method' => $method, 'type' => gettype($object));
        return $this;
    }
    protected function validateObjectAndMethod($object, $method)
    {
        if (is_string($object) && !class_exists($object)) {
            throw new WebserviceException('The object you want to set in '.__METHOD__.' is not allowed.', array(98, 500));
        }
        if (!method_exists($object, $method)) {
            throw new WebserviceException('The method you want to set in '.__METHOD__.' is not allowed.', array(99, 500));
        }
    }
    public function getSpecificField()
    {
        return $this->specificFields;
    }

    protected function overrideSpecificField($entity_name, $field_name, $field, $entity_object, $ws_params)
    {
        if (array_key_exists($field_name, $this->specificFields)) {
            $specific_fields_field_name = $this->specificFields[$field_name];
            if ($specific_fields_field_name['entity'] == $entity_name) {

                if ($specific_fields_field_name['type'] == 'string') {
                    $object = new $specific_fields_field_name['object']();
                } elseif ($specific_fields_field_name['type'] == 'object') {
                    $object = $specific_fields_field_name['object'];
                }

                if (isset($object)) {
                    $method = $specific_fields_field_name['method'];
                    $field = $object->$method($field, $entity_object, $ws_params);
                }
            }
        }

        return $field;
    }

    public function setVirtualField($object, $method, $entity_name, $parameters)
    {
        try {
            $this->validateObjectAndMethod($object, $method);
        } catch (WebserviceException $e) {
            throw $e;
        }

        $this->virtualFields[$entity_name][] = array('parameters' => $parameters, 'object' => $object, 'method' => $method, 'type' => gettype($object));
    }

    public function getVirtualFields()
    {
        return $this->virtualFields;
    }

    public function addVirtualFields($entity_name, $entity_object)
    {
        $arr_return = array();
        $virtual_fields = $this->getVirtualFields();
        if (array_key_exists($entity_name, $virtual_fields)) {
            foreach ($virtual_fields[$entity_name] as $function_infos) {
                if ($function_infos['type'] == 'string') {
                    $object = new $function_infos['object']();
                } elseif ($function_infos['type'] == 'object') {
                    $object = $function_infos['object'];
                }

                $method = $function_infos['method'];
                $return_fields = $object->$method($entity_object, $function_infos['parameters']);
                foreach ($return_fields as $field_name => $value) {
                    if (Validate::isConfigName($field_name)) {
                        $arr_return[$field_name] = $value;
                    } else {
                        throw new WebserviceException('Name for the virtual field is not allow', array(128, 400));
                    }
                }
            }
        }
        return $arr_return;
    }

    public function setFieldsToDisplay($fields)
    {
        $this->fieldsToDisplay = $fields;
    }
}
