<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade QloApps to newer
* versions in the future. If you wish to customize QloApps for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/


class AdminSourcesControllerCore extends AdminController
{
    protected $position_identifier = 'id_source_type';

    /** @var int Total order count, cached once per list render for the Orders % column */
    protected $ordersTotal = 0;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'business_source';
        $this->className = 'BusinessSource';
        $this->identifier = 'id_source_type';
        $this->list_id = 'business_source';
        $this->lang = true;
        $this->deleted = false;
        $this->_defaultOrderBy = 'position';
        $this->_defaultOrderWay = 'ASC';
        $this->context = Context::getContext();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );

        parent::__construct();
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->addJS(_PS_JS_DIR_.'admin/sources.js');
    }

    public function init()
    {
        if (Tools::isSubmit('addsource') || Tools::isSubmit('addbusiness_source')) {
            $this->display = 'add';
        }
        if (Tools::isSubmit('updatesource') || Tools::isSubmit('updatebusiness_source')) {
            $this->display = 'edit';
        }
        if (Tools::isSubmit('submitAddsource') || Tools::isSubmit('submitAddsourceAndStay')) {
            $this->display = Tools::getValue('id_source') ? 'edit' : 'add';
        }
        if (Tools::isSubmit('submitAddbusiness_source') || Tools::isSubmit('submitAddbusiness_sourceAndStay')) {
            $this->display = Tools::getValue('id_source_type') ? 'edit' : 'add';
        }

        return parent::init();
    }

    public function initContent()
    {
        if (Tools::isSubmit('submitFiltersource') || Tools::isSubmit('submitResetsource')) {
            $this->list_id = 'source';
            $this->display = 'view';
            
            $idSourceType = (int) Tools::getValue('id_source_type');
            self::$currentIndex .= '&id_source_type=' . $idSourceType . '&viewbusiness_source';
            if (Tools::isSubmit('submitResetsource')) {
                $this->processResetFilters('source');
            }
        }
        parent::initContent();
    }
    /**
     * @see AdminController::initPageHeaderToolbar()
     */
    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_business_source'] = array(
                'href' => self::$currentIndex.'&addbusiness_source&token='.$this->token,
                'desc' => $this->l('Add new Business Source', null, null, false),
                'icon' => 'process-icon-new'
            );
        } elseif ($this->display == 'view') {
            $idSourceType = (int)Tools::getValue('id_source_type');
            if ($idSourceType) {
                $this->page_header_toolbar_btn['new_source'] = array(
                    'href' => self::$currentIndex.'&addsource&id_source_type='.$idSourceType.'&token='.$this->token,
                    'desc' => $this->l('Add new Booking Source', null, null, false),
                    'icon' => 'process-icon-new'
                );
                $this->page_header_toolbar_btn['back_to_business'] = array(
                    'href' => self::$currentIndex.'&token='.$this->token,
                    'desc' => $this->l('Back To Business Source', null, null, false),
                    'icon' => 'process-icon-back'
                );
            }
        }
        
        unset($this->toolbar_btn['back']);
        parent::initPageHeaderToolbar();
    }



    protected function initBusinessSourcesList()
    {
        $this->fields_list = array(
            'id_source_type' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 'auto',
                'filter_key' => 'b!name'
            ),
            'source_type_code' => array(
                'title' => $this->l('Code'),
                'align' => 'text-center'
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'filter_key' => 'a!position',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'position' => 'position'
            ),
            'orders_count' => array(
                'title' => $this->l('Orders (%)'),
                'align' => 'text-center',
                'orderby' => false,
                'search' => false,
                'callback' => 'formatOrdersPercentage',
            ),
            'active' => array(
                'title' => $this->l('Enabled'),
                'active' => 'status',
                'type' => 'bool',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'orderby' => false
            ),
        );
    }

    protected function initSourcesList()
    {
        $this->fields_list = array(
            'id_source' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 'auto',
                'filter_key' => 'b!name'
            ),
            'source_type_name' => array(
                'title' => $this->l('Business Source'),
                'orderby' => false,
                'search' => false,
            ),
            'source_code' => array(
                'title' => $this->l('Code'),
                'align' => 'text-center'
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'filter_key' => 'a!position',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'position' => 'position'
            ),
            'orders_count' => array(
                'title' => $this->l('Orders (%)'),
                'align' => 'text-center',
                'orderby' => false,
                'search' => false,
                'callback' => 'formatOrdersPercentage',
            ),
            'active' => array(
                'title' => $this->l('Enabled'),
                'active' => 'status',
                'type' => 'bool',
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'orderby' => false
            ),
        );
    }

    public function renderList()
    {
        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->addRowActionSkipList('delete', BusinessSource::getUnremovableIds());
        $this->_where = 'AND a.`deleted` = 0';
        $this->_select = '(SELECT COUNT(*) FROM `'._DB_PREFIX_.'orders` o
            INNER JOIN `'._DB_PREFIX_.'source` s ON s.`id_source` = o.`id_source`
            WHERE s.`id_source_type` = a.`id_source_type`) AS `orders_count`';
        $this->ordersTotal = Source::getTotalOrderCount();
        $this->initBusinessSourcesList();
        $this->toolbar_title = $this->l('Business Source');

        return parent::renderList();
    }

    public function renderView()
    {
        $idSourceType = (int)Tools::getValue('id_source_type');
        $objBusinessSource = new BusinessSource($idSourceType, (int)$this->context->language->id);
        if (!Validate::isLoadedObject($objBusinessSource)) {
            Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
        }

        $this->addRowAction('edit');
        $this->addRowAction('merge');
        $this->addRowAction('delete');
        $this->addRowActionSkipList('delete', Source::getUnremovableIds());

        $this->table = 'source';
        $this->className = 'Source';
        $this->identifier = 'id_source';
        $this->list_id = 'source';
        $this->_select = 'bsl.`name` AS `source_type_name`,
            (SELECT COUNT(*) FROM `'._DB_PREFIX_.'orders` o WHERE o.`id_source` = a.`id_source`) AS `orders_count`';
        $this->ordersTotal = $objBusinessSource->getOrderCount();
        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'business_source` bs ON (bs.`id_source_type` = a.`id_source_type`)';
        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'business_source_lang` bsl ON (bsl.`id_source_type` = bs.`id_source_type` AND bsl.`id_lang` = '.(int)$this->context->language->id.')';
        $this->_where = 'AND a.`deleted` = 0 AND a.`id_source_type` = '.(int)$objBusinessSource->id;
        $this->initSourcesList();

        $this->toolbar_title = sprintf($this->l('Booking Source - %s'),$objBusinessSource->name);
        $this->processFilter();
        return parent::renderList();
    }

    public function renderForm()
    {
        if (Tools::isSubmit('updatesource')
            || Tools::isSubmit('addsource')
            || Tools::isSubmit('submitAddsource')
            || Tools::isSubmit('submitAddsourceAndStay')
        ) {
            return $this->renderSourceForm();
        }

        return $this->renderBusinessSourceForm();
    }

    protected function renderBusinessSourceForm()
    {
        if (!($objBusinessSource = $this->loadObject(true))) {
            return;
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Business Source'),
                'icon' => 'icon-tags'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Code'),
                    'name' => 'source_type_code',
                    'required' => true,
                    'hint' => $this->l('A short unique machine code, e.g. "OTA". Letters, numbers, underscores and hyphens only.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enabled'),
                    'name' => 'active',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array('id' => 'active_on', 'value' => 1, 'label' => $this->l('Enabled')),
                        array('id' => 'active_off', 'value' => 0, 'label' => $this->l('Disabled')),
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'submitAddbusiness_source'
            ),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->l('Save and stay'),
                    'name' => 'submitAddbusiness_sourceAndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save',
                ),
            ),
        );

        return parent::renderForm();
    }

    protected function renderSourceForm()
    {
        $this->table = 'source';
        $this->className = 'Source';
        $this->identifier = 'id_source';
        $this->object = null;

        if (!($objSource = $this->loadObject(true))) {
            return;
        }

        $businessSources = BusinessSource::getActiveList((int)$this->context->language->id);

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Booking Source'),
                'icon' => 'icon-tags'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->l('Invalid characters:').' &lt;&gt;;=#{}'
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Business Source'),
                    'name' => 'id_source_type',
                    'required' => true,
                    'options' => array(
                        'query' => $businessSources,
                        'id' => 'id_source_type',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Source Code'),
                    'name' => 'source_code',
                    'required' => true,
                    'hint' => $this->l('A short unique machine code, e.g. "BOOKING_COM". Letters, numbers, underscores and hyphens only.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enabled'),
                    'name' => 'active',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array('id' => 'active_on', 'value' => 1, 'label' => $this->l('Enabled')),
                        array('id' => 'active_off', 'value' => 0, 'label' => $this->l('Disabled')),
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'submitAddsource'
            ),
            'buttons' => array(
                'save-and-stay' => array(
                    'title' => $this->l('Save and stay'),
                    'name' => 'submitAddsourceAndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save',
                ),
            ),
        );

        return parent::renderForm();
    }


    public function postProcess()
    {
        if (Tools::isSubmit('submitAddsource') || Tools::isSubmit('submitAddsourceAndStay')) {
            return $this->postProcessSource();
        } elseif (Tools::isSubmit('submitMergeSource')) {
            return $this->postProcessMergeSource();
        } elseif (Tools::isSubmit('deletesource')) {
            $objSource = new Source((int)Tools::getValue('id_source'));
            if (!$objSource->isRemovable()) {
                $this->errors[] = $this->l('For security reasons, you cannot delete a built-in Booking Source.');
            } elseif (!$objSource->delete()) {
                $this->errors[] = Tools::displayError('An error occurred while deleting the Booking Source.');
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&viewbusiness_source&id_source_type='.(int)$objSource->id_source_type.'&conf=1&token='.$this->token);
            }
        } else if(Tools::isSubmit('statussource')){
            if(Validate::isLoadedObject($objSource = new Source((int)Tools::getValue('id_source')))){
                $idSourceType = $objSource->id_source_type;
                $objSource->active = !$objSource->active;
                if($objSource->save()){
                    $this->redirect_after = self::$currentIndex.'&viewbusiness_source&id_source_type='.(int)$idSourceType.'&conf=5&token='.$this->token;
                }

            }

        } else if(Tools::isSubmit('submitBulkenableSelectionsource')){
                $this->processUpdateStatusBulk(1);
        } else if(Tools::isSubmit('submitBulkdisableSelectionsource')){
                $this->processUpdateStatusBulk(0);
        } elseif (Tools::isSubmit('submitBulkdeletesource')) {
            $idSourceType = 0;
            foreach (Tools::getValue('sourceBox') as $selection) {
                $objSource = new Source((int)$selection);
                $idSourceType = $objSource->id_source_type;
                if (!$objSource->isRemovable()) {
                    $this->errors[] = $this->l('For security reasons, you cannot delete a built-in Booking Source.');
                    break;
                }
            }

            if (!count($this->errors)) {
                $this->className = 'Source';
                $this->table = 'source';
                $this->boxes = Tools::getValue('sourceBox');
                parent::processBulkDelete();
                $this->redirect_after = self::$currentIndex.'&viewbusiness_source&id_source_type='.(int)$idSourceType.'&conf=1&token='.$this->token;
            }
        } elseif (Tools::isSubmit('submitAddbusiness_source') || Tools::isSubmit('submitAddbusiness_sourceAndStay')) {
            return $this->postProcessBusinessSource();
        } elseif (Tools::isSubmit('delete'.$this->table)) {
            $objBusinessSource = new BusinessSource((int)Tools::getValue('id_source_type'));
            if (!$objBusinessSource->isRemovable()) {
                $this->errors[] = $this->l('For security reasons, you cannot delete a built-in Business Source.');
            } else {
                return parent::postProcess();
            }
        } elseif (Tools::isSubmit('submitBulkdelete'.$this->table)) {
            foreach (Tools::getValue($this->table.'Box') as $selection) {
                $objBusinessSource = new BusinessSource((int)$selection);
                if (!$objBusinessSource->isRemovable()) {
                    $this->errors[] = $this->l('For security reasons, you cannot delete a built-in Business Source.');
                    break;
                }
            }

            if (!count($this->errors)) {
                return parent::postProcess();
            }
        } else {
            return parent::postProcess();
        }
    }

    protected function processUpdateStatusBulk($status = 1)
    {
        $idSourceType = (int)Tools::getValue('id_source_type');
        $this->className = 'Source';
        $this->table = 'source';
        $this->boxes = Tools::getValue('sourceBox');    
        if(empty($this->boxes)){
            $this->errors[] = $this->l('You must select at least one item to perform a bulk action.');
        }
        $this->display = 'view';
        if(!$this->errors){
            parent::processBulkStatusSelection($status);
            $this->redirect_after = self::$currentIndex.'&viewbusiness_source&id_source_type='.(int)$idSourceType.'&conf=5&token='.$this->token;
        }
    }

    protected function postProcessSource()
    {
        $idSource = (int)Tools::getValue('id_source');
        $idSourceType = (int)Tools::getValue('id_source_type');
        $sourceCode = trim(Tools::getValue('source_code'));

        $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
        $objDefaultLanguage = Language::getLanguage((int) $defaultLangId);
        $languages = Language::getLanguages(false);
        if (!trim(Tools::getValue('name_'.$defaultLangId))) {
            $this->errors[] = $this->l('Name is required at least in ').
            $objDefaultLanguage['name'];
        } else {
            foreach ($languages as $lang) {
                if (trim(Tools::getValue('name_'.$lang['id_lang']))) {
                    if (!Validate::isGenericName(Tools::getValue('name_'.$lang['id_lang']))) {
                        $this->errors[] = $this->l('Invalid name in ').$lang['name'];
                    }
                }
            }
        }

        $objBusinessSource = new BusinessSource($idSourceType);
        if (!Validate::isLoadedObject($objBusinessSource) || $objBusinessSource->deleted) {
            $this->errors[] = $this->l('Please select a valid Business Source.');
        }

        if (!$sourceCode || !Validate::isModuleName($sourceCode)) {
            $this->errors[] = $this->l('Source Code is invalid. Only letters, numbers, underscores and hyphens are allowed.');
        } elseif (Source::codeExists($sourceCode, $idSource)) {
            $this->errors[] = $this->l('This Source Code is already used by another Booking Source.');
        }

        if (count($this->errors)) {
            $this->display = $idSource ? 'edit' : 'add';
            return false;
        }

        $objSource = new Source($idSource);
        $objSource->id_source_type = $idSourceType;
        $objSource->source_code = $sourceCode;
        $objSource->active = (int)Tools::getValue('active');
        $objSource->name = array();
        foreach (Language::getIDs(false) as $idLang) {
            $objSource->name[$idLang] = Tools::getValue('name_'.$idLang);
        }

        if (!$objSource->save()) {
            $this->errors[] = Tools::displayError('An error occurred while saving the Booking Source.');
            $this->display = $idSource ? 'edit' : 'add';
            return false;
        }

        if (Tools::isSubmit('submitAddsourceAndStay')) {
            Tools::redirectAdmin(self::$currentIndex.'&updatesource&id_source='.$objSource->id.'&conf='.($idSource ? 4 : 3).'&token='.$this->token);
        }

        Tools::redirectAdmin(self::$currentIndex.'&viewbusiness_source&id_source_type='.$idSourceType.'&conf='.($idSource ? 4 : 3).'&token='.$this->token);
    }

    protected function postProcessMergeSource()
    {
        $idSourceType = (int)Tools::getValue('id_source_type');
        $idCurrentSource = (int)Tools::getValue('current_source');
        $idTargetSource = (int)Tools::getValue('target_source');

        $objCurrent = new Source($idCurrentSource);
        $objTarget = new Source($idTargetSource);

        if (!Validate::isLoadedObject($objCurrent) || !Validate::isLoadedObject($objTarget)
            || (int)$objCurrent->id_source_type !== $idSourceType || (int)$objTarget->id_source_type !== $idSourceType
        ) {
            $this->errors[] = $this->l('Please select valid Current and Target Booking Sources.');
        } elseif ($idCurrentSource === $idTargetSource) {
            $this->errors[] = $this->l('Current and Target Booking Source cannot be the same.');
        } else {
            $objCurrent->mergeOrdersInto($idTargetSource);
        }
        $this->display = 'view';
        if (!count($this->errors)) {
            Tools::redirectAdmin(self::$currentIndex.'&viewbusiness_source&id_source_type='.$idSourceType.'&conf=4&token='.$this->token);
        }
    }

    protected function postProcessBusinessSource()
    {
        $idSourceType = (int)Tools::getValue('id_source_type');
        $sourceTypeCode = trim(Tools::getValue('source_type_code'));

        $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
        $objDefaultLanguage = Language::getLanguage((int) $defaultLangId);
        $languages = Language::getLanguages(false);
        if (!trim(Tools::getValue('name_'.$defaultLangId))) {
            $this->errors[] = $this->l('Name is required at least in ').
            $objDefaultLanguage['name'];
        } else {
            foreach ($languages as $lang) {
                if (trim(Tools::getValue('name_'.$lang['id_lang']))) {
                    if (!Validate::isGenericName(Tools::getValue('name_'.$lang['id_lang']))) {
                        $this->errors[] = $this->l('Invalid name in ').$lang['name'];
                    }
                }
            }
        }
        if (!$sourceTypeCode || !Validate::isModuleName($sourceTypeCode)) {
            $this->errors[] = $this->l('Code is invalid. Only letters, numbers, underscores and hyphens are allowed.');
        } elseif (BusinessSource::codeExists($sourceTypeCode, $idSourceType)) {
            $this->errors[] = $this->l('This Code is already used by another Business Source.');
        }

        if (count($this->errors)) {
            $this->display = $idSourceType ? 'edit' : 'add';
            return false;
        }

        return parent::postProcess();
    }

    public function formatOrdersPercentage($count, $row)
    {
        $percentage = $this->ordersTotal ? round(((int)$count / $this->ordersTotal) * 100, 1) : 0;
        return $percentage.'%';
    }

    public function displayMergeLink($token, $id, $name = null)
    {
        $idSourceType = (int) Tools::getValue('id_source_type');

        return '<a href="#"
                class="merge-source-btn"
                title="' . $this->l('Merge') . '"
                data-id-source="' . (int) $id . '"
                data-id-source-type="' . $idSourceType . '"
                data-ajax-url="' . self::$currentIndex . '&token=' . ($token ?: $this->token) . '">
                <i class="icon-random"></i> ' . $this->l('Merge') . '
            </a>';
    }

    public function ajaxProcessInitMergeSourceModal()
    {
        $response = array('hasError' => 1);

        if ($this->tabAccess['edit'] === 1) {
            $idSourceType = (int)Tools::getValue('id_source_type');
            $idSource = (int)Tools::getValue('id_source');
            $objSource = new Source($idSource);

            if (Validate::isLoadedObject($objSource) && (int)$objSource->id_source_type === $idSourceType) {
                $this->context->smarty->assign(array(
                    'sources' => Source::getActiveListForType($idSourceType, (int)$this->context->language->id),
                    'id_current_source' => $idSource,
                    'id_source_type' => $idSourceType,
                    'current_index' => self::$currentIndex,
                    'token' => $this->token,
                ));

                $this->context->smarty->assign(array(
                    'modal_id' => 'merge_source_modal',
                    'modal_class' => 'modal-md',
                    'modal_title' => '<i class="icon-random"></i> &nbsp ' .$this->l('Merge Booking Source'),
                    'modal_content' => $this->context->smarty->fetch('controllers/sources/modals/_merge_source_form.tpl'),
                ));

                $response['hasError'] = 0;
                $response['modalHtml'] = $this->context->smarty->fetch('modal.tpl');
            }
        }

        die(Tools::jsonEncode($response));
    }

    protected function filterToField($key, $filter)
    {
        if ($this->table == 'source') {
            $this->initSourcesList();
        } else {
            $this->initBusinessSourcesList();
        }

        return parent::filterToField($key, $filter);
    }

    public function ajaxProcessUpdatePositions()
    {
        if ($this->tabAccess['edit'] !== 1) {
            die(json_encode(array('hasError' => true, 'errors' => $this->l('You do not have permission to edit this.'))));
        }

        $way = (int)Tools::getValue('way');
        $id = (int)Tools::getValue('id');

        if ($positions = Tools::getValue('source')) {
            $className = 'Source';
        } elseif ($positions = Tools::getValue('source_type')) {
            $className = 'BusinessSource';
        } else {
            die(json_encode(array('hasError' => true, 'errors' => 'Unknown table for position update.')));
        }

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int)$pos[2] === $id) {
                if ($object = new $className((int)$pos[2])) {
                    if (isset($position) && $object->updatePosition($way, $position)) {
                        echo 'ok position '.(int)$position.' for '.$className.' '.$id;
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update tab '.(int)$id.' to position '.(int)$position.' "}';
                    }
                } else {
                        echo json_encode(array(
                                'hasError' => true,
                                'errors' => 'Can not update '.$className.' '.$id.' to position '.(int)$position,
                        ));                
                }

                break;
            }
        }
    }
}
