CREATE TABLE IF NOT EXISTS `PREFIX_wkpaypal_transaction` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_cart` int(10) unsigned NOT NULL,
  `currency_code`  varchar(3) NOT NULL DEFAULT '0',
  `pay_key` varchar(255) character set utf8 NOT NULL,
  `status` varchar(255) character set utf8 NOT NULL,
  `sender_email` varchar(255) character set utf8 NOT NULL,
  `action_type` varchar(255) character set utf8 NOT NULL,
  `memo` varchar(255) character set utf8,
  `payment_method` int(3) unsigned NOT NULL,
  `payment_info` text(1000) character set utf8,
  `is_delayed_paid` tinyint(1) unsigned NOT NULL,
  `is_refunded` tinyint(1) unsigned NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_wkpaypal_refund` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `transaction_id` int(10) unsigned NOT NULL,
  `refund_details` text(1000) character set utf8,
  `date_add` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;