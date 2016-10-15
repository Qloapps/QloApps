<?php
class Cart extends CartCore
{
    /*
    * module: zipcodewiseshipping
    * date: 2016-07-06 06:24:29
    * version: 1.0.1
    */
    public function getPackageShippingCost($id_carrier = null, $use_tax = true, Country $default_country = null, $product_list = null, $id_zone = null)
    {
        if ($this->isVirtualCart()) {
            return 0;
        }
        if (!$default_country) {
            $default_country = Context::getContext()->country;
        }
        if (!is_null($product_list)) {
            foreach ($product_list as $key => $value) {
                if ($value['is_virtual'] == 1) {
                    unset($product_list[$key]);
                }
            }
        }
        if (is_null($product_list)) {
            $products = $this->getProducts();
        } else {
            $products = $product_list;
        }
        if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
            $address_id = (int) $this->id_address_invoice;
        } elseif (count($product_list)) {
            $prod = current($product_list);
            $address_id = (int) $prod['id_address_delivery'];
        } else {
            $address_id = null;
        }
        if (!Address::addressExists($address_id)) {
            $address_id = null;
        }
        if (is_null($id_carrier) && !empty($this->id_carrier)) {
            $id_carrier = (int) $this->id_carrier;
        }
        $cache_id = 'getPackageShippingCost_'.(int) $this->id.'_'.(int) $address_id.'_'.(int) $id_carrier.'_'.(int) $use_tax.'_'.(int) $default_country->id;
        if ($products) {
            foreach ($products as $product) {
                $cache_id .= '_'.(int) $product['id_product'].'_'.(int) $product['id_product_attribute'];
            }
        }
        if (Cache::isStored($cache_id)) {
            return Cache::retrieve($cache_id);
        }
        $order_total = $this->getOrderTotal(true, self::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING, $product_list);
        $shipping_cost = 0;
        if (!count($products)) {
            Cache::store($cache_id, $shipping_cost);
            return $shipping_cost;
        }
        if (!isset($id_zone)) {
            if (!$this->isMultiAddressDelivery()
                && isset($this->id_address_delivery) // Be carefull, id_address_delivery is not usefull one 1.5
                && $this->id_address_delivery
                && Customer::customerHasAddress($this->id_customer, $this->id_address_delivery
            )) {
                $id_zone = Address::getZoneById((int) $this->id_address_delivery);
            } else {
                if (!Validate::isLoadedObject($default_country)) {
                    $default_country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'), Configuration::get('PS_LANG_DEFAULT'));
                }
                $id_zone = (int) $default_country->id_zone;
            }
        }
        if ($id_carrier && !$this->isCarrierInRange((int) $id_carrier, (int) $id_zone)) {
            $id_carrier = '';
        }
        if (empty($id_carrier) && $this->isCarrierInRange((int) Configuration::get('PS_CARRIER_DEFAULT'), (int) $id_zone)) {
            $id_carrier = (int) Configuration::get('PS_CARRIER_DEFAULT');
        }
        $total_package_without_shipping_tax_inc = $this->getOrderTotal(true, self::BOTH_WITHOUT_SHIPPING, $product_list);
        if (empty($id_carrier)) {
            if ((int) $this->id_customer) {
                $customer = new Customer((int) $this->id_customer);
                $result = Carrier::getCarriers((int) Configuration::get('PS_LANG_DEFAULT'), true, false, (int) $id_zone, $customer->getGroups());
                unset($customer);
            } else {
                $result = Carrier::getCarriers((int) Configuration::get('PS_LANG_DEFAULT'), true, false, (int) $id_zone);
            }
            foreach ($result as $k => $row) {
                if ($row['id_carrier'] == Configuration::get('PS_CARRIER_DEFAULT')) {
                    continue;
                }
                if (!isset(self::$_carriers[$row['id_carrier']])) {
                    self::$_carriers[$row['id_carrier']] = new Carrier((int) $row['id_carrier']);
                }
                $carrier = self::$_carriers[$row['id_carrier']];
                $shipping_method = $carrier->getShippingMethod();
                if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && $carrier->getMaxDeliveryPriceByWeight((int) $id_zone) === false)
                || ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && $carrier->getMaxDeliveryPriceByPrice((int) $id_zone) === false)) {
                    unset($result[$k]);
                    continue;
                }
                if ($row['range_behavior']) {
                    $check_delivery_price_by_weight = Carrier::checkDeliveryPriceByWeight($row['id_carrier'], $this->getTotalWeight(), (int) $id_zone);
                    $total_order = $total_package_without_shipping_tax_inc;
                    $check_delivery_price_by_price = Carrier::checkDeliveryPriceByPrice($row['id_carrier'], $total_order, (int) $id_zone, (int) $this->id_currency);
                    if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && !$check_delivery_price_by_weight)
                    || ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && !$check_delivery_price_by_price)) {
                        unset($result[$k]);
                        continue;
                    }
                }
                if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
                    $shipping = $carrier->getDeliveryPriceByWeight($this->getTotalWeight($product_list), (int) $id_zone);
                } else {
                    $shipping = $carrier->getDeliveryPriceByPrice($order_total, (int) $id_zone, (int) $this->id_currency);
                }
                $min_shipping_price = 0;
                if (!isset($min_shipping_price)) {
                    $min_shipping_price = $shipping;
                }
                if ($shipping <= $min_shipping_price) {
                    $id_carrier = (int) $row['id_carrier'];
                    $min_shipping_price = $shipping;
                }
            }
        }
        if (empty($id_carrier)) {
            $id_carrier = Configuration::get('PS_CARRIER_DEFAULT');
        }
        if (!isset(self::$_carriers[$id_carrier])) {
            self::$_carriers[$id_carrier] = new Carrier((int) $id_carrier, Configuration::get('PS_LANG_DEFAULT'));
        }
        $carrier = self::$_carriers[$id_carrier];
        if (!Validate::isLoadedObject($carrier)) {
            Cache::store($cache_id, 0);
            return 0;
        }
        $shipping_method = $carrier->getShippingMethod();
        if (!$carrier->active) {
            Cache::store($cache_id, $shipping_cost);
            return $shipping_cost;
        }
        if ($use_tax && !Tax::excludeTaxeOption()) {
            $address = Address::initialize((int) $address_id);
            if (Configuration::get('PS_ATCP_SHIPWRAP')) {
                $carrier_tax = 0;
            } else {
                $carrier_tax = $carrier->getTaxesRate($address);
            }
        }
        if (Module::isInstalled('zipcodewiseshipping') && Module::isEnabled('zipcodewiseshipping')) {
            include_once dirname(__FILE__).'/../../modules/zipcodewiseshipping/classes/ZipCodeShippingImpact.php';
            $delivery_add_obj = new Address($this->id_address_delivery);
            $impactPrice = 0;
            if (isset($delivery_add_obj->id_country) && $delivery_add_obj->id_country && isset($delivery_add_obj->postcode) && $delivery_add_obj->postcode) {
                $zipcode = $delivery_add_obj->postcode;
                $chk_id_country = $delivery_add_obj->id_country;
                $chk_id_zone = Country::getIdZone($chk_id_country);
                $obj_impact_zip = new ZipCodeShippingImpact();
                $impactPrice_arr = $obj_impact_zip->getZipcodeImpactPrice($chk_id_zone, $chk_id_country, $zipcode);
                if ($impactPrice_arr) {
                    $impact_behave = Configuration::get('ZIP_IMPACT_BEHAVE');
                    $impact_prices = array();
                    foreach ($impactPrice_arr as $value) {
                        if (!$value['impact_type']) {
                            $impact_prices[] = (int) -$value['impact_price'];
                        } else {
                            $impact_prices[] = (int) $value['impact_price'];
                        }
                    }
                    if ($impact_behave == 'min') {
                        $impactPrice = min($impact_prices);
                    } elseif ($impact_behave == 'max') {
                        $impactPrice = max($impact_prices);
                    } elseif ($impact_behave == 'add_all') {
                        $impactPrice = array_sum($impact_prices);
                    }
                    $impactPrice = Tools::convertPrice($impactPrice, Currency::getCurrencyInstance((int) $this->id_currency));
                }
            }
        }
        if ($carrier->is_free == 1) {
            Cache::store($cache_id, 0);
            if (Module::isInstalled('zipcodewiseshipping') && Module::isEnabled('zipcodewiseshipping')) {
                if (Configuration::get('ZIPCODE_ADD_TAX') && isset($carrier_tax)) {
                    $impactPrice *= 1 + ($carrier_tax / 100);
                }
                if ($impactPrice < 0) {
                    $impactPrice = 0;
                }
                Cache::store($cache_id, $impactPrice);
                return $impactPrice;
            }
            return 0;
        }
        $configuration = Configuration::getMultiple(array(
            'PS_SHIPPING_FREE_PRICE',
            'PS_SHIPPING_HANDLING',
            'PS_SHIPPING_METHOD',
            'PS_SHIPPING_FREE_WEIGHT',
        ));
        $free_fees_price = 0;
        if (isset($configuration['PS_SHIPPING_FREE_PRICE'])) {
            $free_fees_price = Tools::convertPrice((float) $configuration['PS_SHIPPING_FREE_PRICE'], Currency::getCurrencyInstance((int) $this->id_currency));
        }
        $orderTotalwithDiscounts = $this->getOrderTotal(true, self::BOTH_WITHOUT_SHIPPING, null, null, false);
        if ($orderTotalwithDiscounts >= (float) ($free_fees_price) && (float) ($free_fees_price) > 0) {
            Cache::store($cache_id, $shipping_cost);
            return $shipping_cost;
        }
        if (isset($configuration['PS_SHIPPING_FREE_WEIGHT'])
            && $this->getTotalWeight() >= (float) $configuration['PS_SHIPPING_FREE_WEIGHT']
            && (float) $configuration['PS_SHIPPING_FREE_WEIGHT'] > 0) {
            Cache::store($cache_id, $shipping_cost);
            return $shipping_cost;
        }
        if ($carrier->range_behavior) {
            if (!isset($id_zone)) {
                if (isset($this->id_address_delivery)
                    && $this->id_address_delivery
                    && Customer::customerHasAddress($this->id_customer, $this->id_address_delivery)) {
                    $id_zone = Address::getZoneById((int) $this->id_address_delivery);
                } else {
                    $id_zone = (int) $default_country->id_zone;
                }
            }
            if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && !Carrier::checkDeliveryPriceByWeight($carrier->id, $this->getTotalWeight(), (int) $id_zone))
            || ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && !Carrier::checkDeliveryPriceByPrice($carrier->id, $total_package_without_shipping_tax_inc, $id_zone, (int) $this->id_currency)
            )) {
                $shipping_cost += 0;
            } else {
                if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
                    $shipping_cost += $carrier->getDeliveryPriceByWeight($this->getTotalWeight($product_list), $id_zone);
                } else { // by price
                    $shipping_cost += $carrier->getDeliveryPriceByPrice($order_total, $id_zone, (int) $this->id_currency);
                }
            }
        } else {
            if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
                $shipping_cost += $carrier->getDeliveryPriceByWeight($this->getTotalWeight($product_list), $id_zone);
            } else {
                $shipping_cost += $carrier->getDeliveryPriceByPrice($order_total, $id_zone, (int) $this->id_currency);
            }
        }
        if (isset($configuration['PS_SHIPPING_HANDLING']) && $carrier->shipping_handling) {
            $shipping_cost += (float) $configuration['PS_SHIPPING_HANDLING'];
        }
        foreach ($products as $product) {
            if (!$product['is_virtual']) {
                $shipping_cost += $product['additional_shipping_cost'] * $product['cart_quantity'];
            }
        }
        $shipping_cost = Tools::convertPrice($shipping_cost, Currency::getCurrencyInstance((int) $this->id_currency));
        if ($carrier->shipping_external) {
            $module_name = $carrier->external_module_name;
            $module = Module::getInstanceByName($module_name);
            if (Validate::isLoadedObject($module)) {
                if (array_key_exists('id_carrier', $module)) {
                    $module->id_carrier = $carrier->id;
                }
                if ($carrier->need_range) {
                    if (method_exists($module, 'getPackageShippingCost')) {
                        $shipping_cost = $module->getPackageShippingCost($this, $shipping_cost, $products);
                    } else {
                        $shipping_cost = $module->getOrderShippingCost($this, $shipping_cost);
                    }
                } else {
                    $shipping_cost = $module->getOrderShippingCostExternal($this);
                }
                if ($shipping_cost === false) {
                    Cache::store($cache_id, false);
                    return false;
                }
            } else {
                Cache::store($cache_id, false);
                return false;
            }
        }
        if (Configuration::get('PS_ATCP_SHIPWRAP')) {
            if (!$use_tax) {
                $shipping_cost /= (1 + $this->getAverageProductsTaxRate());
            }
        } else {
            if ($use_tax && isset($carrier_tax)) {
                $shipping_cost *= 1 + ($carrier_tax / 100);
            }
        }
        if (Module::isInstalled('zipcodewiseshipping') && Module::isEnabled('zipcodewiseshipping')) {
            if (Configuration::get('ZIPCODE_ADD_TAX') && isset($carrier_tax)) {
                $impactPrice *= 1 + ($carrier_tax / 100);
            }
            if ($impactPrice) {
                $shipping_cost += $impactPrice;
            }
            if ($shipping_cost < 0) {
                $shipping_cost = 0;
            }
        }
        $shipping_cost = (float) Tools::ps_round((float) $shipping_cost, (Currency::getCurrencyInstance((int) $this->id_currency)->decimals * _PS_PRICE_DISPLAY_PRECISION_));
        Cache::store($cache_id, $shipping_cost);
        return $shipping_cost;
    }
}
