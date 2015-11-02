SET NAMES 'utf8';

/* PHP:p15014_copy_missing_images_tab_from_installer(); */;

/* PHP:p15014_add_missing_columns(); */;

/* PHP:p15014_upgrade_sekeywords(); */;


UPDATE `PREFIX_orders` SET `reference` = LPAD(reference, 9 , '0');

INSERT INTO `PREFIX_hook_alias` (`name`, `alias`) VALUES ('displayMyAccountBlock', 'myAccountBlock');


UPDATE `PREFIX_image_type` SET height = 189, width = 520 WHERE name = 'large_scene';

CREATE TABLE IF NOT EXISTS `PREFIX_order_invoice_tax` (
  `id_order_invoice` int(11) NOT NULL,
  `type` varchar(15) NOT NULL,
  `id_tax` int(11) NOT NULL,
  `amount` decimal(10,6) NOT NULL DEFAULT '0.000000'
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

ALTER TABLE  `PREFIX_order_invoice`
ADD  `shipping_tax_computation_method` INT NOT NULL AFTER `total_shipping_tax_incl`;

INSERT INTO `PREFIX_configuration`(`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_SEARCH_INDEXATION', '1', NOW(), NOW());
