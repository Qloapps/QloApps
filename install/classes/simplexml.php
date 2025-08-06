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

class InstallSimplexmlElement extends SimpleXMLElement
{
    /**
     * Can add SimpleXMLElement values in XML tree
     *
     * @see SimpleXMLElement::addChild()
     */
    public function addChild($name, $value = null, $namespace = null)
    {
        if ($value instanceof SimplexmlElement) {
            $content = trim((string)$value);
            if (strlen($content) > 0) {
                $new_element = parent::addChild($name, str_replace('&', '&amp;', $content), $namespace);
            } else {
                $new_element = parent::addChild($name);
                foreach ($value->attributes() as $k => $v) {
                    $new_element->addAttribute($k, $v);
                }
            }

            foreach ($value->children() as $child) {
                $new_element->addChild($child->getName(), $child);
            }
        } else {
            return parent::addChild($name, str_replace('&', '&amp;', $value), $namespace);
        }
    }

    /**
     * Generate nice and sweet XML
     *
     * @see SimpleXMLElement::asXML()
     */
    public function asXML($filename = null)
    {
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML(parent::asXML());

        if ($filename) {
            return (bool)file_put_contents($filename, $dom->saveXML());
        }
        return $dom->saveXML();
    }
}
