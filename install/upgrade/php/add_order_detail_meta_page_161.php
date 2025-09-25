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

function add_order_detail_meta_page_161()
{

    if (!Db::getInstance()->getValue('
        SELECT id_meta
        FROM `'._DB_PREFIX_.'meta` m
        WHERE m.`page` = "order-detail"')
    ) {
        // meta
        Db::getInstance()->insert(
            'meta',
            array(
                'page' => 'order-detail',
                'configurable' => 1,
            )
        );
        if ($last_id = Db::getInstance()->Insert_ID()) {
            // meta lang
            $pageDetail = array(
                'en' => array(
                    'title' => 'Booking details',
                    'url_rewrite' => 'order-detail'
                ),
                'fr' => array(
                    'title' => 'Détails de réservation',
                    'url_rewrite' => 'details-de-reservation'
                )
            );
            if ($languages = Db::getInstance()->executeS('SELECT id_lang, iso_code FROM `'._DB_PREFIX_.'lang`')) {
                $row = array();
                foreach ($languages as $lang) {
                    if (in_array(strtolower($lang['iso_code']), array_keys($pageDetail))) {
                        $row[] = array(
                            'id_meta' => (int)$last_id,
                            'id_shop' => 1,
                            'id_lang' => (int)$lang['id_lang'],
                            'title' => $pageDetail[strtolower($lang['iso_code'])]['title'],
                            'description' => '',
                            'keywords' => '',
                            'url_rewrite' => $pageDetail[strtolower($lang['iso_code'])]['url_rewrite']
                        );
                    } else {
                        $row[] = array(
                            'id_meta' => (int)$last_id,
                            'id_shop' => 1,
                            'id_lang' => (int)$lang['id_lang'],
                            'title' => $pageDetail['en']['title'],
                            'description' => '',
                            'keywords' => '',
                            'url_rewrite' => $pageDetail['en']['url_rewrite']
                        );
                    }
                }
                Db::getInstance()->insert(
                    'meta_lang',
                    $row
                );
            }

            // meta themes
            $row = array();
            if ($themes = Db::getInstance()->executeS('SELECT id_theme FROM `'._DB_PREFIX_.'theme`')) {
                foreach ($themes as $theme) {
                    $row[] = array(
                        'id_theme' => (int)$theme,
                        'id_meta' => (int)$last_id,
                        'left_column' => 0,
                        'right_column' => 0
                    );
                }
                Db::getInstance()->insert(
                    'theme_meta',
                    $row
                );
            }

            return true;
        } else {
            return false;
        }
    }
}
