SET NAMES 'utf8';

/* PHP:category_product_index_unique(); */;

CREATE TABLE IF NOT EXISTS `PREFIX_order_tax` (
  `id_order` int(10) NOT NULL,
  `tax_name` varchar(40) NOT NULL,
  `tax_rate` decimal(6,3) NOT NULL,
  `amount` decimal(20,6) NOT NULL
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_hook` (`name`, `title`, `description`, `position`, `live_edit`) VALUES
('frontCanonicalRedirect', 'Front Canonical Redirect', 'Check for 404 errors before canonical redirects', 0, 0);

/* PHP:update_module_pagesnotfound(); */;

ALTER TABLE `PREFIX_order_state` ADD COLUMN `deleted` tinyint(1) UNSIGNED NOT NULL default '0' AFTER `delivery`;