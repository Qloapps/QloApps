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

class OurPropertiesControllerCore extends FrontController
{
    public $php_self = 'our-properties';

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;

        parent::initContent();
        $hotelsInfo = array();
        $hotelLocationArray = 0;
        $pageLimit = 0;
        $displayHotelMap = Configuration::get('WK_DISPLAY_PROPERTIES_PAGE_GOOGLE_MAP');
        if (Module::isInstalled('hotelreservationsystem') && Module::isEnabled('hotelreservationsystem')) {
            $objModule = Module::getInstanceByName('hotelreservationsystem');
            $objHotelInfo = new HotelBranchInformation();
            if ($hotelsInfo = $objHotelInfo->hotelBranchesInfo(false, 1, 1)) {
                $hotelIconDefault = $this->context->link->getMediaLink($objModule->getPathUri().'views/img/Slices/hotel-default-icon.png');
                foreach ($hotelsInfo as $hotelKey => $hotel) {
                    if (isset($hotel['id_cover_img'])
                        && $hotel['id_cover_img']
                        && Validate::isLoadedObject($objHotelImage = new HotelImage($hotel['id_cover_img']))
                    ) {
                        $htlImgLink = $this->context->link->getMediaLink($objHotelImage->getImageLink($hotel['id_cover_img'], ImageType::getFormatedName('medium')));
                        if ((bool)Tools::file_get_contents($htlImgLink)) {
                            $hotelsInfo[$hotelKey]['image_url'] = $htlImgLink;
                        } else {
                            $hotelsInfo[$hotelKey]['image_url'] = $hotelIconDefault;
                        }
                    } else {
                        $hotelsInfo[$hotelKey]['image_url'] = $hotelIconDefault;
                    }

                    $hotelsInfo[$hotelKey]['view_rooms_link'] = $this->context->link->getCategoryLink(
                        new Category($hotel['id_category'], $this->context->language->id),
                        null,
                        $this->context->language->id
                    );
                }

                // To store max number pages.
                $pageLimit = ceil(count($hotelsInfo)/10);
                if (!($page = Tools::getValue('pagination'))
                    || (!$pageLimit || ($page > $pageLimit))
                ) {
                    $page = 1;
                }

                $pagination = array();
                if ($pageLimit) {
                    $pageNumber = $page - 2;
                    if ($pageNumber < 1) {
                        $pageNumber = 1;
                    }

                    while ($page + 4 >= $pageNumber && count($pagination) < 5) {
                        if ($pageNumber > $pageLimit) {
                            $firstItem = reset($pagination) - 1;
                            if ($firstItem > 0) {
                                $pagination[$firstItem] = $firstItem;
                            }
                        } else {
                            $pagination[$pageNumber] = $pageNumber;
                        }

                        $pageNumber++;
                    }
                }

                ksort($pagination);

                $this->context->smarty->assign(
                    array(
                        'pagination' => $pagination,
                        'currentPage' => $page,
                        'pageLimit' => $pageLimit,
                        'currentPageUrl' => $this->context->link->getPageLink($this->php_self)
                    )
                );

                $page--;
                // To show 10 Hotel per page
                $hotelsInfo = array_slice($hotelsInfo, $page * 10, 10);
            }

            if ($displayHotelMap && Configuration::get('PS_API_KEY') && Configuration::get('WK_GOOGLE_ACTIVE_MAP') && Configuration::get('PS_MAP_ID')) {
                if ($hotelLocations = $objHotelInfo->getMapFormatHotelsInfo(Configuration::get('WK_MAP_HOTEL_ACTIVE_ONLY'))) {
                    $hotelLocationArray = str_replace(array('\n', '\r'), '', json_encode($hotelLocations));
                }
            }
        }

        Media::addJsDef(
            array(
                'hotelLocationArray' => $hotelLocationArray
            )
        );

        $this->context->smarty->assign(
            array(
                'hotelsInfo' => $hotelsInfo,
                'hotelLocationArray' => $hotelLocationArray,
                'viewOnMap' => Configuration::get('WK_GOOGLE_ACTIVE_MAP'),
                'displayHotelMap' => $displayHotelMap,
                'WK_HTL_SHORT_DESC' => Configuration::get('WK_HTL_SHORT_DESC', $this->context->language->id),
                'currentIndex' => $this->context->link->getPageLink('our-properties')
            )
        );

        $this->setTemplate(_PS_THEME_DIR_.'our-properties.tpl');
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJS(_THEME_JS_DIR_.'our-properties.js');
        $this->addCSS(_THEME_CSS_DIR_.'our-properties.css');
        // GOOGLE MAP
        if (($PS_API_KEY = Configuration::get('PS_API_KEY')) && ($PS_MAP_ID = Configuration::get('PS_MAP_ID')) && Configuration::get('WK_GOOGLE_ACTIVE_MAP')) {
            Media::addJsDef(
                array(
                    'PS_STORES_ICON' => $this->context->link->getMediaLink(_PS_IMG_.Configuration::get('PS_STORES_ICON')),
                    'PS_MAP_ID' => $PS_MAP_ID
                )
            );

            $this->addJS(
                'https://maps.googleapis.com/maps/api/js?key='.$PS_API_KEY.
                '&libraries=places,marker&loading=async&callback=initMap&language='.$this->context->language->iso_code.'&region='.$this->context->country->iso_code
            );
        }
    }
}
