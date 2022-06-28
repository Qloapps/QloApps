<?php

/**
 * 2007-2017 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\Module\AutoUpgrade\Twig\Form;

use PrestaShop\Module\AutoUpgrade\Parameters\UpgradeConfiguration;
use PrestaShop\Module\AutoUpgrade\UpgradeTools\Translator;
use Twig_Environment;

class FormRenderer
{
    private $config;

    private $translator;

    private $twig;

    public function __construct(
        UpgradeConfiguration $configuration,
        Twig_Environment $twig,
        Translator $translator
    ) {
        $this->config = $configuration;
        $this->twig = $twig;
        $this->translator = $translator;
    }

    public function render($name, $fields, $tabname, $size, $icon)
    {
        $required = false;

        $formFields = array();

        foreach ($fields as $key => $field) {
            $html = '';
            $required = !empty($field['required']);
            $disabled = !empty($field['disabled']);

            $val = $this->config->get(
                $key,
                isset($field['defaultValue']) ? $field['defaultValue'] : false
            );

            if (!in_array($field['type'], array('image', 'radio', 'select', 'container', 'bool', 'container_end')) || isset($field['show'])) {
                $html .= '<div style="clear: both; padding-top:15px">'
                    . ($field['title'] ? '<label >' . $field['title'] . '</label>' : '')
                    . '<div class="margin-form" style="padding-top:5px">';
            }

            // Display the appropriate input type for each field
            switch ($field['type']) {
                case 'disabled':
                    $html .= $field['disabled'];
                    break;

                case 'bool':
                    $html .= $this->renderBool($field, $key, $val);
                    break;

                case 'radio':
                    $html .= $this->renderRadio($field, $key, $val, $disabled);
                    break;

                case 'select':
                    $html .= $this->renderSelect($field, $key, $val);
                    break;

                case 'textarea':
                    $html .= $this->renderTextarea($field, $key, $val, $disabled);
                    break;

                case 'container':
                    $html .= '<div id="' . $key . '">';
                    break;

                case 'container_end':
                    $html .= (isset($field['content']) ? $field['content'] : '') . '</div>';
                    break;

                case 'text':
                default:
                    $html .= $this->renderTextField($field, $key, $val, $disabled);
            }

            if ($required && !in_array($field['type'], array('image', 'radio'))) {
                $html .= ' <sup>*</sup>';
            }

            if (isset($field['desc']) && !in_array($field['type'], array('bool', 'select'))) {
                $html .= '<p style="clear:both">';
                if (!empty($field['thumb']) && $field['thumb']['pos'] == 'after') {
                    $html .= $this->renderThumb($field);
                }
                $html .= $field['desc'] . '</p>';
            }

            if (!in_array($field['type'], array('image', 'radio', 'select', 'container', 'bool', 'container_end')) || isset($field['show'])) {
                $html .= '</div></div>';
            }

            $formFields[] = $html;
        }

        return $this->twig->render(
            '@ModuleAutoUpgrade/form.twig',
            array(
                'name' => $name,
                'tabName' => $tabname,
                'fields' => $formFields,
            )
        );
    }

    private function renderBool($field, $key, $val)
    {
        return '<div class="form-group">
                <label class="col-lg-3 control-label">' . $field['title'] . '</label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="' . $key . '" id="' . $key . '_on" value="1" ' . ($val ? ' checked="checked"' : '') . (isset($field['js']['on']) ? $field['js']['on'] : '') . ' />
                            <label for="' . $key . '_on" class="radioCheck">
                                <i class="color_success"></i> '
                            . $this->translator->trans('Yes', array(), 'Admin.Global') . '
                            </label>
                            <input type="radio" name="' . $key . '" id="' . $key . '_off" value="0" ' . (!$val ? 'checked="checked"' : '') . (isset($field['js']['off']) ? $field['js']['off'] : '') . '/>
                            <label for="' . $key . '_off" class="radioCheck">
                                <i class="color_danger"></i> ' . $this->translator->trans('No', array(), 'Admin.Global') . '
                            </label>
                            <a class="slide-button btn"></a>
                        </span>
                        <div class="help-block">' . $field['desc'] . '</div>
                    </div>
                </div>';
    }

    private function renderRadio($field, $key, $val, $disabled)
    {
        $html = '';
        foreach ($field['choices'] as $cValue => $cKey) {
            $html .= '<input ' . ($disabled ? 'disabled="disabled"' : '') . ' type="radio" name="' . $key . '" id="' . $key . $cValue . '_on" value="' . (int) ($cValue) . '"' . (($cValue == $val) ? ' checked="checked"' : '') . (isset($field['js'][$cValue]) ? ' ' . $field['js'][$cValue] : '') . ' /><label class="t" for="' . $key . $cValue . '_on"> ' . $cKey . '</label><br />';
        }
        $html .= '<br />';

        return $html;
    }

    private function renderSelect($field, $key, $val)
    {
        $html = '<div class="form-group">
                    <label class="col-lg-3 control-label">' . $field['title'] . '</label>
                        <div class="col-lg-9">
                            <select name="' . $key . '">';

        foreach ($field['choices'] as $cValue => $cKey) {
            $html .= '<option value="' . (int) $cValue . '"'
                . (($cValue == $val) ? ' selected' : '')
                . '>'
                . $cKey
                . '</option>';
        }

        $html .= '</select>
                <div class="help-block">' . $field['desc'] . '</div>
            </div>
        </div>';

        return $html;
    }

    private function renderTextarea($field, $key, $val, $disabled)
    {
        return '<textarea '
            . ($disabled ? 'disabled="disabled"' : '')
            . ' name="' . $key
            . '" cols="' . $field['cols']
            . '" rows="' . $field['rows']
            . '">'
            . htmlentities($val, ENT_COMPAT, 'UTF-8')
            . '</textarea>';
    }

    private function renderTextField($field, $key, $val, $disabled)
    {
        return '<input '
            . ($disabled ? 'disabled="disabled"' : '')
            . ' type="' . $field['type'] . '"'
            . (isset($field['id']) ? ' id="' . $field['id'] . '"' : '')
            . ' size="' . (isset($field['size']) ? (int) ($field['size']) : 5)
            . '" name="' . $key
            . '" value="' . ($field['type'] == 'password' ? '' : htmlentities($val, ENT_COMPAT, 'UTF-8'))
            . '" />'
            . (isset($field['next']) ? '&nbsp;' . $field['next'] : '');
    }

    private function renderThumb($field)
    {
        return "<img src=\"{$field['thumb']['file']}\" alt=\"{$field['title']}\" title=\"{$field['title']}\" style=\"float:left;\">";
    }
}
