<?php
/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class HotelRoomTypeDemand extends ObjectModel
{
    public $idProduct;
    public $idGlobalDemand;
    public $date_add;
    public $date_upd;

    protected static $_prices = array();

    public static $definition = array(
        'table' => 'htl_room_type_demand',
        'primary' => 'id_room_type_demand',
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_global_demand' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'room_type_demands',
        'objectNodeName' => 'room_type_demand',
        'fields' => array(
            'id_product' => array(
                'xlink_resource' => array(
                    'resourceName' => 'room_types',
                )
            ),
        ),
    );

    public function getRoomTypeDemands($idProduct, $idLang = 0, $useTax = null)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        $roomTypeDemandInfo = array();
        if ($roomTypeDemands = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'htl_room_type_demand` rd
            LEFT JOIN `'._DB_PREFIX_.'htl_room_type_global_demand` rgd
            ON (rd.`id_global_demand` = rgd.`id_global_demand`)
            LEFT JOIN `'._DB_PREFIX_.'htl_room_type_global_demand_lang` rgdl
            ON (rgd.`id_global_demand` = rgdl.`id_global_demand` AND rgdl.`id_lang` = '.(int)$idLang.')
            WHERE rd.`id_product`='.(int)$idProduct
        )) {
            $objAdvOption = new HotelRoomTypeGlobalDemandAdvanceOption();
            $objRoomDemandPrice = new HotelRoomTypeDemandPrice();
            $context = Context::getContext();
            if (isset($context->currency->id)
                && Validate::isLoadedObject($context->currency)
            ) {
                $idCurrency = (int)$context->currency->id;
            } else {
                $idCurrency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
            }
            if ($useTax === null) {
                $useTax = HotelBookingDetail::useTax();
            }
            foreach ($roomTypeDemands as &$demand) {
                $idGlobalDemand = $demand['id_global_demand'];
                $roomTypeDemandInfo[$idGlobalDemand]['name'] = $demand['name'];
                $roomTypeDemandInfo[$idGlobalDemand]['price_calc_method'] = $demand['price_calc_method'];

                $roomTypeDemandInfo[$idGlobalDemand]['price'] = HotelRoomTypeDemand::getPriceStatic(
                    $idProduct,
                    $idGlobalDemand,
                    0,
                    $useTax
                );
                if ($advOptions = $objAdvOption->getGlobalDemandAdvanceOptions($idGlobalDemand, $idLang)) {
                    foreach ($advOptions as &$option) {
                        $idOption = $option['id'];
                        $roomTypeDemandInfo[$idGlobalDemand]['adv_option'][$idOption]['price'] = HotelRoomTypeDemand::getPriceStatic(
                            $idProduct,
                            $idGlobalDemand,
                            $idOption,
                            $useTax
                        );
                        $roomTypeDemandInfo[$idGlobalDemand]['adv_option'][$idOption]['name'] = $option['name'];
                    }
                }
            }
            return $roomTypeDemandInfo;
        }
        return false;
    }

    public static function getPriceStatic(
        $idProduct,
        $idGlobalDemand,
        $idOption = 0,
        $useTax = true,
        $decimals = 6,
        $idCustomer = null,
        $idCart = null,
        $id_address = null,
        Context $context = null
    ) {
        if (!$context) {
            $context = Context::getContext();
        }

        $curCart = $context->cart;

        if (!Validate::isBool($useTax) || !Validate::isUnsignedId($idProduct)) {
            die(Tools::displayError());
        }

        // If there is cart in context or if the specified id_cart is different from the context cart id
        if (!is_object($curCart) || (Validate::isUnsignedInt($idCart) && $idCart && $curCart->id != $idCart)) {
            if (!$idCart && !isset($context->employee)) {
                die(Tools::displayError());
            }
            $curCart = new Cart($idCart);
            // Store cart in context to avoid multiple instantiations in BO
            if (!Validate::isLoadedObject($context->cart)) {
                $context->cart = $curCart;
            }
        }

        $idCurrency = Validate::isLoadedObject($context->currency) ? (int)$context->currency->id : (int)Configuration::get('PS_CURRENCY_DEFAULT');

        // retrieve address informations
        $idCountry = (int)$context->country->id;
        $idState = 0;
        $zipcode = 0;

        if (!$id_address && Validate::isLoadedObject($curCart)) {
            $id_address = $curCart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
        }

        if ($id_address) {
            $addressInfos = Address::getCountryAndState($id_address);
            if ($addressInfos['id_country']) {
                $idCountry = (int)$addressInfos['id_country'];
                $idState = (int)$addressInfos['id_state'];
                $zipcode = $addressInfos['postcode'];
            }
        } elseif (isset($context->customer->geoloc_id_country)) {
            $idCountry = (int)$context->customer->geoloc_id_country;
            $idState = (int)$context->customer->id_state;
            $zipcode = $context->customer->postcode;
        }

        if (Tax::excludeTaxeOption()) {
            $useTax = false;
        }

        if ($useTax != false
            && !empty($addressInfos['vat_number'])
            && $addressInfos['id_country'] != Configuration::get('VATNUMBER_COUNTRY')
            && Configuration::get('VATNUMBER_MANAGEMENT')) {
            $useTax = false;
        }

        if (is_null($idCustomer) && Validate::isLoadedObject($context->customer)) {
            $idCustomer = $context->customer->id;
        }

        return static::priceCalculation(
            $context->shop->id,
            $idProduct,
            $idGlobalDemand,
            $idOption,
            $idCountry,
            $idState,
            $zipcode,
            $idCurrency,
            $useTax,
            $decimals,
            $idCustomer,
            $idCart
        );
    }

    public static function priceCalculation(
        $idShop,
        $idProduct,
        $idGlobalDemand,
        $idOption,
        $idCountry,
        $idState,
        $zipcode,
        $idCurrency,
        $useTax,
        $decimals,
        $idCustomer = 0,
        $idCart = 0
    ) {
        static $address = null;
        static $context = null;

        if ($address === null) {
            $address = new Address();
        }

        if ($context == null) {
            $context = Context::getContext()->cloneContext();
        }

        if ($idShop !== null && $context->shop->id != (int)$idShop) {
            $context->shop = new Shop((int)$idShop);
        }

        $cacheId = (int)$idProduct.'-'.(int)$idGlobalDemand.'-'.(int)$idShop.'-'.(int)$idCurrency.'-'.
        (int)$idCountry.'-'.$idState.'-'.$zipcode.'-'.(int)$idOption.'-'.(int)$idCustomer.'-'.(int)$idCart.'-'.
        ($useTax?'1':'0').'-'.(int)$decimals;

        if (isset(self::$_prices[$cacheId])) {
            /* Affect reference before returning cache */
            return self::$_prices[$cacheId];
        }

        // here get the price of global demand
        $objRoomDmdPrice = new HotelRoomTypeDemandPrice();
        if ($idOption) {
            $objOption = new HotelRoomTypeGlobalDemandAdvanceOption($idOption);
            $price = $objRoomDmdPrice->getRoomTypeDemandPrice(
                $idProduct,
                $idGlobalDemand,
                $idOption
            );
            if (!Validate::isPrice($price)) {
                $price = $objOption->price;
            }
        } else {
            $objGlobalDemand = new HotelRoomTypeGlobalDemand($idGlobalDemand);
            $price = $objRoomDmdPrice->getRoomTypeDemandPrice(
                $idProduct,
                $idGlobalDemand
            );
            if (!Validate::isPrice($price)) {
                $price = $objGlobalDemand->price;
            }
        }
        $price = Tools::convertPrice($price, $idCurrency);
        // Tax calculation section
        $address->id_country = $idCountry;
        $address->id_state = $idState;
        $address->postcode = $zipcode;

        $tax_manager = TaxManagerFactory::getManager(
            $address,
            HotelRoomTypeGlobalDemand::getIdTaxRulesGroupByIdGlobalDemanu((int)$idGlobalDemand)
        );
        $product_tax_calculator = $tax_manager->getTaxCalculator();

        // Add Tax
        if ($useTax) {
            $price = $product_tax_calculator->addTaxes($price);
        }

        $price = Tools::ps_round($price, $decimals);

        if ($price < 0) {
            $price = 0;
        }

        self::$_prices[$cacheId] = $price;
        return self::$_prices[$cacheId];
    }

    public function deleteRoomTypeDemands($idProduct = 0, $idGlobalDemand = 0)
    {
        $where = '1';
        if ($idProduct) {
            $where .= ' AND `id_product`='.(int)$idProduct;
        }
        if ($idGlobalDemand) {
            $where .= ' AND `id_global_demand`='.(int)$idGlobalDemand;
        }
        return Db::getInstance()->delete(
            'htl_room_type_demand',
            $where
        );
    }
}

