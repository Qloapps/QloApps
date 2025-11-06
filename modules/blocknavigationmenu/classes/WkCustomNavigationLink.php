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
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

class WkCustomNavigationLink extends ObjectModel
{
    public $name;
    public $id_cms;
    public $is_custom_link;
    public $show_at_navigation;
    public $show_at_footer;
    public $link;
    public $active;
    public $position;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_custom_navigation_link',
        'primary' => 'id_navigation_link',
        'multilang' => true,
        'fields' => array(
            'id_cms' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'is_custom_link' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'link' => array('type' => self::TYPE_STRING),
            'show_at_navigation' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'show_at_footer' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            /* Lang fields */
            'name' => array('type' => self::TYPE_STRING, 'lang' => true),
        )
    );

    // value 2 for parameters means all (0 and 1)
    public function getCustomNavigationLinks($active = 2, $idLang = false, $showAtNavigation = 2, $showAtFooter = 2)
    {
        $context = Context::getContext();
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        $sql = 'SELECT el.*, ell.`name` FROM `'._DB_PREFIX_.'htl_custom_navigation_link` el
        INNER JOIN `'._DB_PREFIX_.'htl_custom_navigation_link_lang` AS ell ON
        (ell.`id_navigation_link` = el.`id_navigation_link`)
        WHERE ell.`id_lang` = '.(int)$idLang;

        if ($active != 2) {
            $sql .= ' AND `active` = '.(int) $active;
        }
        if ($showAtNavigation != 2) {
            $sql .= ' AND `show_at_navigation` = '.(int) $showAtNavigation;
        }
        if ($showAtFooter != 2) {
            $sql .= ' AND `show_at_footer` = '.(int) $showAtFooter;
        }
        $sql .= ' ORDER BY `position`';

        if ($result = Db::getInstance()->executeS($sql)) {
            foreach ($result as &$navigationLink) {
                if ($navigationLink['id_cms']) {
                    if (Validate::isLoadedObject($objCMS = new CMS($navigationLink['id_cms']))) {
                        $navigationLink['link'] = $context->link->getCMSLink((int)$navigationLink['id_cms']);
                        $navigationLink['name'] = $objCMS->meta_title[$context->language->id];
                    }
                }
            }
        }
        return $result;
    }

    public function delete()
    {
        $return = parent::delete();
        /* Reinitializing position */
        $this->cleanPositions();
        return $return;
    }

    public function getHigherPosition()
    {
        $position = DB::getInstance()->getValue(
            'SELECT MAX(`position`) FROM `'._DB_PREFIX_.'htl_custom_navigation_link`'
        );
        $result = (is_numeric($position)) ? $position : -1;
        return $result + 1;
    }

    public function updatePosition($way, $position)
    {
        if (!$result = Db::getInstance()->executeS(
            'SELECT htb.`id_navigation_link`, htb.`position` FROM `'._DB_PREFIX_.'htl_custom_navigation_link` htb
            WHERE htb.`id_navigation_link` = '.(int) $this->id.' ORDER BY `position` ASC'
        )
        ) {
            return false;
        }

        $movedBlock = false;
        foreach ($result as $block) {
            if ((int)$block['id_navigation_link'] == (int)$this->id) {
                $movedBlock = $block;
            }
        }

        if ($movedBlock === false) {
            return false;
        }
        return (Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_custom_navigation_link` SET `position`= `position` '.($way ? '- 1' : '+ 1').
            ' WHERE `position`'.($way ? '> '.
            (int)$movedBlock['position'].' AND `position` <= '.(int)$position : '< '
            .(int)$movedBlock['position'].' AND `position` >= '.(int)$position)
        ) && Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_custom_navigation_link`
            SET `position` = '.(int)$position.'
            WHERE `id_navigation_link`='.(int)$movedBlock['id_navigation_link']
        ));
    }

    /**
     * Reorder navigation link position
     * Call it after deleting a navigation link.
     * @return bool $return
     */
    public function cleanPositions()
    {
        Db::getInstance()->execute('SET @i = -1', false);
        $sql = 'UPDATE `'._DB_PREFIX_.'htl_custom_navigation_link` SET `position` = @i:=@i+1 ORDER BY `position` ASC';
        return (bool) Db::getInstance()->execute($sql);
    }

    public function insertDemoData($populateData = null)
    {
        // enter modules links
        $modsElems = array();
        // if module is installing or resetting
        if (is_null($populateData)) {
            if (Module::isEnabled('wkabouthotelblock')) {
                $modsElems['Interior'] = 'hotelInteriorBlock';
            }
            if (Module::isEnabled('wkhotelfeaturesblock')) {
                $modsElems['Amenities'] = 'hotelAmenitiesBlock';
            }
            if (Module::isEnabled('wkhotelroom')) {
                $modsElems['Rooms'] = 'hotelRoomsBlock';
            }
            if (Module::isEnabled('wktestimonialblock')) {
                $modsElems['Testimonials'] = 'hotelTestimonialBlock';
            }
        } else {
            // if QloApps is installing and $populateData = 1 then enter modules links directly
            if ($populateData) {
                $modsElems['Interior'] = 'hotelInteriorBlock';
                $modsElems['Amenities'] = 'hotelAmenitiesBlock';
                $modsElems['Rooms'] = 'hotelRoomsBlock';
                $modsElems['Testimonials'] = 'hotelTestimonialBlock';
            }
        }

        //insert home link to the list
        $languages = Language::getLanguages(false);
        $https_link = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
        $objLink = new Link($https_link, $https_link);

        $navigationLinks = array(
            array(
                'name' => 'Home',
                'id_cms' => 0,
                'show_at_navigation' => 1,
                'show_at_footer' => 0,
                'is_custom_link' => 0,
                'link' => 'index',
            ),
            array(
                'name' => 'Our Properties',
                'id_cms' => 0,
                'show_at_navigation' => 1,
                'show_at_footer' => 0,
                'is_custom_link' => 1,
                'link' => $objLink->getPageLink('our-properties'),
            ),
        );

        if ($modsElems) {
            $indexLink = Context::getContext()->shop->getBaseURI();
            foreach ($modsElems as $name => $modElm) {
                $navigationLinks[] = array(
                    'name' => $name,
                    'id_cms' => 0,
                    'show_at_navigation' => 1,
                    'show_at_footer' => 0,
                    'is_custom_link' => 1,
                    'link' => $indexLink.'#'.$modElm
                );
            }
        }

        // enter CMS pages data
        if ($cmsPagesCMS = CMS::getCMSPages(Configuration::get('PS_LANG_DEFAULT'), 1)) {
            $showAtNavigation = 0;
            foreach ($cmsPagesCMS as $cmsPage) {
                $navigationLinks[] = array(
                    'id_cms' => $cmsPage['id_cms'],
                    'show_at_navigation' => $showAtNavigation,
                    'show_at_footer' => 1,
                    'is_custom_link' => 0,
                );
                $showAtNavigation = !$showAtNavigation;
            }
        }

        $navigationLinks[] = array(
            'name' => 'Contact Us',
            'id_cms' => 0,
            'show_at_navigation' => 1,
            'show_at_footer' => 0,
            'is_custom_link' => 0,
            'link' => 'contact',
        );
        foreach ($navigationLinks as $navigationLink) {
            $objCustomNavigationLink = new WkCustomNavigationLink();
            if (isset($navigationLink['name'])) {
                foreach ($languages as $language) {
                    $objCustomNavigationLink->name[$language['id_lang']] = $navigationLink['name'];
                }
            }

            $objCustomNavigationLink->position = $this->getHigherPosition();
            $objCustomNavigationLink->id_cms = $navigationLink['id_cms'];
            $objCustomNavigationLink->show_at_navigation = $navigationLink['show_at_navigation'];
            $objCustomNavigationLink->show_at_footer = $navigationLink['show_at_footer'];
            $objCustomNavigationLink->active = 1;
            if (isset($navigationLink['link'])) {
                $objCustomNavigationLink->link = $navigationLink['link'];
            }

            $objCustomNavigationLink->is_custom_link = $navigationLink['is_custom_link'];
            $objCustomNavigationLink->save();
        }

        return true;
    }

    public function getNavigationLinksByIdCMS($idCMS)
    {
        return Db::getInstance()->executeS('
            SELECT * FROM `'._DB_PREFIX_.'htl_custom_navigation_link`
            WHERE `id_cms`='.(int) $idCMS
        );
    }

    public function disableNavigationLinksByIdCMS($idCMS)
    {
        $objCustomNavigationLink = new WkCustomNavigationLink();
        if ($navLinks = $objCustomNavigationLink->getNavigationLinksByIdCMS($idCMS)) {
            foreach ($navLinks as $navLink) {
                $objCustomNavigationLink = new WkCustomNavigationLink((int) $navLink['id_navigation_link']);
                $objCustomNavigationLink->active = 0;
                $objCustomNavigationLink->save();
            }
        }
    }

    public function deleteNavigationLinksByIdCMS($idCMS)
    {
        $objCustomNavigationLink = new WkCustomNavigationLink();
        if ($navLinks = $objCustomNavigationLink->getNavigationLinksByIdCMS($idCMS)) {
            foreach ($navLinks as $navLink) {
                $objCustomNavigationLink = new WkCustomNavigationLink((int) $navLink['id_navigation_link']);
                $objCustomNavigationLink->delete();
            }
        }
    }

}
