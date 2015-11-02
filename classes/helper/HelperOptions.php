<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * Use this helper to generate preferences forms, with values stored in the configuration table
 */
class HelperOptionsCore extends Helper
{
    public $required = false;

    public function __construct()
    {
        $this->base_folder = 'helpers/options/';
        $this->base_tpl = 'options.tpl';
        parent::__construct();
    }

    /**
     * Generate a form for options
     * @param array $option_list
     * @return string html
     */
    public function generateOptions($option_list)
    {
        $this->tpl = $this->createTemplate($this->base_tpl);
        $tab = Tab::getTab($this->context->language->id, $this->id);
        if (!isset($languages)) {
            $languages = Language::getLanguages(false);
        }

        $use_multishop = false;
        $hide_multishop_checkbox = (Shop::getTotalShops(false, null) < 2) ? true : false;
        foreach ($option_list as $category => $category_data) {
            if (!is_array($category_data)) {
                continue;
            }

            if (!isset($category_data['image'])) {
                $category_data['image'] = (!empty($tab['module']) && file_exists($_SERVER['DOCUMENT_ROOT']._MODULE_DIR_.$tab['module'].'/'.$tab['class_name'].'.gif') ? _MODULE_DIR_.$tab['module'].'/' : '../img/t/').$tab['class_name'].'.gif';
            }

            if (!isset($category_data['fields'])) {
                $category_data['fields'] = array();
            }

            $category_data['hide_multishop_checkbox'] = true;

            if (isset($category_data['tabs'])) {
                $tabs[$category] = $category_data['tabs'];
                $tabs[$category]['misc'] = $this->l('Miscellaneous');
            }

            foreach ($category_data['fields'] as $key => $field) {
                if (empty($field['no_multishop_checkbox']) && !$hide_multishop_checkbox) {
                    $category_data['hide_multishop_checkbox'] = false;
                }

                // Set field value unless explicitly denied
                if (!isset($field['auto_value']) || $field['auto_value']) {
                    $field['value'] = $this->getOptionValue($key, $field);
                }

                // Check if var is invisible (can't edit it in current shop context), or disable (use default value for multishop)
                $is_disabled = $is_invisible = false;
                if (Shop::isFeatureActive()) {
                    if (isset($field['visibility']) && $field['visibility'] > Shop::getContext()) {
                        $is_disabled = true;
                        $is_invisible = true;
                    } elseif (Shop::getContext() != Shop::CONTEXT_ALL && !Configuration::isOverridenByCurrentContext($key)) {
                        $is_disabled = true;
                    }
                }
                $field['is_disabled'] = $is_disabled;
                $field['is_invisible'] = $is_invisible;

                $field['required'] = isset($field['required']) ? $field['required'] : $this->required;

                if ($field['type'] == 'color') {
                    $this->context->controller->addJqueryPlugin('colorpicker');
                }

                if ($field['type'] == 'texarea' || $field['type'] == 'textareaLang') {
                    $this->context->controller->addJqueryPlugin('autosize');
                }

                if ($field['type'] == 'file') {
                    $uploader = new HelperUploader();
                    $uploader->setId(isset($field['id'])?$field['id']:null);
                    $uploader->setName($field['name']);
                    $uploader->setUrl(isset($field['url'])?$field['url']:null);
                    $uploader->setMultiple(isset($field['multiple'])?$field['multiple']:false);
                    $uploader->setUseAjax(isset($field['ajax'])?$field['ajax']:false);
                    $uploader->setMaxFiles(isset($field['max_files'])?$field['max_files']:null);

                    if (isset($field['files']) && $field['files']) {
                        $uploader->setFiles($field['files']);
                    } elseif (isset($field['image']) && $field['image']) { // Use for retrocompatibility
                        $uploader->setFiles(array(
                            0 => array(
                            'type'       => HelperUploader::TYPE_IMAGE,
                            'image'      => isset($field['image'])?$field['image']:null,
                            'size'       => isset($field['size'])?$field['size']:null,
                            'delete_url' => isset($field['delete_url'])?$field['delete_url']:null
                        )));
                    }

                    if (isset($field['file']) && $field['file']) { // Use for retrocompatibility
                        $uploader->setFiles(array(
                            0 => array(
                            'type'       => HelperUploader::TYPE_FILE,
                            'size'       => isset($field['size'])?$field['size']:null,
                            'delete_url' => isset($field['delete_url'])?$field['delete_url']:null,
                            'download_url' => isset($field['file'])?$field['file']:null
                        )));
                    }

                    if (isset($field['thumb']) && $field['thumb']) { // Use for retrocompatibility
                        $uploader->setFiles(array(
                            0 => array(
                            'type'       => HelperUploader::TYPE_IMAGE,
                            'image'      => isset($field['thumb'])?'<img src="'.$field['thumb'].'" alt="'.$field['title'].'" title="'.$field['title'].'" />':null,
                        )));
                    }

                    $uploader->setTitle(isset($field['title'])?$field['title']:null);
                    $field['file'] = $uploader->render();
                }

                // Cast options values if specified
                if ($field['type'] == 'select' && isset($field['cast'])) {
                    foreach ($field['list'] as $option_key => $option) {
                        $field['list'][$option_key][$field['identifier']] = $field['cast']($option[$field['identifier']]);
                    }
                }

                // Fill values for all languages for all lang fields
                if (substr($field['type'], -4) == 'Lang') {
                    foreach ($languages as $language) {
                        if ($field['type'] == 'textLang') {
                            $value = Tools::getValue($key.'_'.$language['id_lang'], Configuration::get($key, $language['id_lang']));
                        } elseif ($field['type'] == 'textareaLang') {
                            $value = Configuration::get($key, $language['id_lang']);
                        } elseif ($field['type'] == 'selectLang') {
                            $value = Configuration::get($key, $language['id_lang']);
                        }
                        $field['languages'][$language['id_lang']] = $value;
                        $field['value'][$language['id_lang']] = $this->getOptionValue($key.'_'.strtoupper($language['iso_code']), $field);
                    }
                }

                // pre-assign vars to the tpl
                // @todo move this
                if ($field['type'] == 'maintenance_ip') {
                    $field['script_ip'] = '
						<script type="text/javascript">
							function addRemoteAddr()
							{
								var length = $(\'input[name=PS_MAINTENANCE_IP]\').attr(\'value\').length;
								if (length > 0)
									$(\'input[name=PS_MAINTENANCE_IP]\').attr(\'value\',$(\'input[name=PS_MAINTENANCE_IP]\').attr(\'value\') +\','.Tools::getRemoteAddr().'\');
								else
									$(\'input[name=PS_MAINTENANCE_IP]\').attr(\'value\',\''.Tools::getRemoteAddr().'\');
							}
						</script>';
                    $field['link_remove_ip'] = '<button type="button" class="btn btn-default" onclick="addRemoteAddr();"><i class="icon-plus"></i> '.$this->l('Add my IP', 'Helper').'</button>';
                }

                // Multishop default value
                $field['multishop_default'] = false;
                if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL && !$is_invisible) {
                    $field['multishop_default'] = true;
                    $use_multishop = true;
                }

                // Assign the modifications back to parent array
                $category_data['fields'][$key] = $field;

                // Is at least one required field present?
                if (isset($field['required']) && $field['required']) {
                    $category_data['required_fields'] = true;
                }
            }
            // Assign the modifications back to parent array
            $option_list[$category] = $category_data;
        }

        $this->tpl->assign(array(
            'title' => $this->title,
            'toolbar_btn' => $this->toolbar_btn,
            'show_toolbar' => $this->show_toolbar,
            'toolbar_scroll' => $this->toolbar_scroll,
            'current' => $this->currentIndex,
            'table' => $this->table,
            'token' => $this->token,
            'tabs' => (isset($tabs)) ? $tabs : null,
            'option_list' => $option_list,
            'current_id_lang' => $this->context->language->id,
            'languages' => isset($languages) ? $languages : null,
            'currency_left_sign' => $this->context->currency->getSign('left'),
            'currency_right_sign' => $this->context->currency->getSign('right'),
            'use_multishop' => $use_multishop,
        ));

        return parent::generate();
    }

    /**
     * Type = image
     */
    public function displayOptionTypeImage($key, $field, $value)
    {
        echo '<table cellspacing="0" cellpadding="0">';
        echo '<tr>';

        $i = 0;
        foreach ($field['list'] as $theme) {
            echo '<td class="center" style="width: 180px; padding:0px 20px 20px 0px;">';
            echo '<input type="radio" name="'.$key.'" id="'.$key.'_'.$theme['name'].'_on" style="vertical-align: text-bottom;" value="'.$theme['name'].'"'.(_THEME_NAME_ == $theme['name'] ? 'checked="checked"' : '').' />';
            echo '<label class="t" for="'.$key.'_'.$theme['name'].'_on"> '.Tools::strtolower($theme['name']).'</label>';
            echo '<br />';
            echo '<label class="t" for="'.$key.'_'.$theme['name'].'_on">';
            echo '<img src="../themes/'.$theme['name'].'/preview.jpg" alt="'.Tools::strtolower($theme['name']).'">';
            echo '</label>';
            echo '</td>';
            if (isset($field['max']) && ($i + 1) % $field['max'] == 0) {
                echo '</tr><tr>';
            }
            $i++;
        }
        echo '</tr>';
        echo '</table>';
    }

    /**
     * Type = price
     */
    public function displayOptionTypePrice($key, $field, $value)
    {
        echo $this->context->currency->getSign('left');
        $this->displayOptionTypeText($key, $field, $value);
        echo $this->context->currency->getSign('right').' '.$this->l('(tax excl.)', 'Helper');
    }

    /**
     * Type = disabled
     */
    public function displayOptionTypeDisabled($key, $field, $value)
    {
        echo $field['disabled'];
    }

    public function getOptionValue($key, $field)
    {
        $value = Tools::getValue($key, Configuration::get($key));
        if (!Validate::isCleanHtml($value)) {
            $value = Configuration::get($key);
        }

        if (isset($field['defaultValue']) && !$value) {
            $value = $field['defaultValue'];
        }
        return Tools::purifyHTML($value);
    }
}
