SET NAMES 'utf8';

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
	('PS_ALLOW_EMP', '0', NOW(), NOW()),
	('PS_ALLOW_EMP_MAX_ATTEMPTS', '0', NOW(), NOW()),
  ('PS_KPI_BEST_SELLING_ROOM_TYPE_NB_DAYS', '30', NOW(), NOW()),
  ('PS_ORDER_LIST_PRICE_DISPLAY_CURRENCY', '1', NOW(), NOW()),
  ('PS_OVERBOOKING_ORDER_ACTION', '1', NOW(), NOW()),
  ('PS_MAX_OVERBOOKING_PER_HOTEL_PER_DAY', '2', NOW(), NOW()),

  ('PS_CUSTOMER_BIRTHDATE', '1', NOW(), NOW()),

  ('PS_KPI_FREQUENT_CUSTOMER_NB_ORDERS', '5', NOW(), NOW()),
  ('PS_KPI_REVPAC_NB_DAYS', '30', NOW(), NOW()),
  ('PS_KPI_CONVERSION_RATE_NB_DAYS', '30', NOW(), NOW()),
  ('PS_ORDER_KPI_AVG_ORDER_VALUE_NB_DAYS', '30', NOW(), NOW()),
  ('PS_ORDER_KPI_PER_VISITOR_PROFIT_NB_DAYS', '30', NOW(), NOW()),
  ('PS_KPI_NEW_CUSTOMERS_NB_DAYS', '30', NOW(), NOW()),

  ('PS_BACKDATE_ORDER_SUPERADMIN', '1', NOW(), NOW()),
  ('PS_BACKDATE_ORDER_EMPLOYEES', '0', NOW(), NOW());

UPDATE `PREFIX_configuration`
set `value` = 1
WHERE `name` = 'PS_TAX_DISPLAY';

UPDATE `PREFIX_configuration`
set `value` = 'id_address_invoice'
WHERE `name` = 'PS_TAX_ADDRESS_TYPE';


UPDATE `PREFIX_configuration`
set `value` = STR_TO_DATE(`value`, '%d-%m-%Y')
WHERE `name` = 'MAX_GLOBAL_BOOKING_DATE';

UPDATE `PREFIX_product`
set `show_at_front` = 1
WHERE `booking_product` = 1;
UPDATE `PREFIX_product_shop` ps
INNER JOIN `PREFIX_product` p on(ps.`id_product` = p.`id_product`)
set ps.`show_at_front` = 1
WHERE p.`booking_product` = 1;

INSERT INTO `PREFIX_cart_customer_guest_detail`
(`phone`, `id_gender`, `firstname`, `lastname`, `email`, `id_cart`, `date_add`, `date_upd`)
SELECT
    IF(a.phone_mobile IS NOT NULL AND a.phone_mobile != '', a.phone_mobile, a.phone) AS phone,
    c.id_gender, c.firstname, c.lastname, c.email, 0 AS id_cart,
    NOW() AS date_add, NOW() AS date_upd
FROM
    `PREFIX_address` a
INNER JOIN
    `PREFIX_customer` c ON a.`id_customer` = c.`id_customer`
WHERE
    a.`auto_generated` = 0
GROUP BY
    c.`id_customer`;



/* PHP:add_order_detail_meta_page_161(); */;
/* PHP:clean_product_description_161(); */;
/* PHP:update_tabs_161(); */;
/* PHP:update_order_states_161(); */;

