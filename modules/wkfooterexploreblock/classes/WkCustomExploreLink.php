<?php
/**
* 2010-2018 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkCustomExploreLink extends ObjectModel
{
    public $name;
    public $id_cms;
    public $show_at_navigation;
    public $show_at_footer;
    public $link;
    public $active;
    public $position;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_custom_explore_link',
        'primary' => 'id_explore_link',
        'multilang' => true,
        'fields' => array(
            'id_cms' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
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
    public function getCustomExploreLinks($active = 2, $idLang = false, $showAtNavigation = 2, $showAtFooter = 2)
    {
        $context = Context::getContext();
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        $sql = 'SELECT el.*, ell.`name` FROM `'._DB_PREFIX_.'htl_custom_explore_link` el
        INNER JOIN `'._DB_PREFIX_.'htl_custom_explore_link_lang` AS ell ON
        (ell.`id_explore_link` = el.`id_explore_link`)
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
            foreach ($result as &$exploreLink) {
                if ($exploreLink['id_cms']) {
                    if (Validate::isLoadedObject($objCMS = new CMS($exploreLink['id_cms']))) {
                        $exploreLink['link'] = $context->link->getCMSLink((int)$exploreLink['id_cms']);
                        $exploreLink['name'] = $objCMS->meta_title[$context->language->id];
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
            'SELECT MAX(`position`) FROM `'._DB_PREFIX_.'htl_custom_explore_link`'
        );
        $result = (is_numeric($position)) ? $position : -1;
        return $result + 1;
    }

    public function updatePosition($way, $position)
    {
        if (!$result = Db::getInstance()->executeS(
            'SELECT htb.`id_explore_link`, htb.`position` FROM `'._DB_PREFIX_.'htl_custom_explore_link` htb
            WHERE htb.`id_explore_link` = '.(int) $this->id.' ORDER BY `position` ASC'
        )
        ) {
            return false;
        }

        $movedBlock = false;
        foreach ($result as $block) {
            if ((int)$block['id_explore_link'] == (int)$this->id) {
                $movedBlock = $block;
            }
        }

        if ($movedBlock === false) {
            return false;
        }
        return (Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_custom_explore_link` SET `position`= `position` '.($way ? '- 1' : '+ 1').
            ' WHERE `position`'.($way ? '> '.
            (int)$movedBlock['position'].' AND `position` <= '.(int)$position : '< '
            .(int)$movedBlock['position'].' AND `position` >= '.(int)$position)
        ) && Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_custom_explore_link`
            SET `position` = '.(int)$position.'
            WHERE `id_explore_link`='.(int)$movedBlock['id_explore_link']
        ));
    }

    /**
     * Reorder explore link position
     * Call it after deleting a explore link.
     * @return bool $return
     */
    public function cleanPositions()
    {
        Db::getInstance()->execute('SET @i = -1', false);
        $sql = 'UPDATE `'._DB_PREFIX_.'htl_custom_explore_link` SET `position` = @i:=@i+1 ORDER BY `position` ASC';
        return (bool) Db::getInstance()->execute($sql);
    }

    public function insertDemoData()
    {
        if ($cmsPagesCMS = CMS::getCMSPages(Configuration::get('PS_LANG_DEFAULT'), 1)) {
            $showAtNavigation = 0;
            foreach ($cmsPagesCMS as $cmsPage) {
                $objCustomExploreLink = new WkCustomExploreLink();
                $objCustomExploreLink->position = $this->getHigherPosition();
                $objCustomExploreLink->id_cms = $cmsPage['id_cms'];
                $objCustomExploreLink->show_at_navigation = $showAtNavigation;
                $objCustomExploreLink->show_at_footer = 1;
                $objCustomExploreLink->active = 1;
                $objCustomExploreLink->save();
                $showAtNavigation = !$showAtNavigation;
            }
        }
        return true;
    }
}
