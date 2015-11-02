SET NAMES 'utf8';

ALTER TABLE `PREFIX_product` ADD `visibility` ENUM('both', 'catalog', 'search', 'none') NOT NULL default 'both' AFTER `indexed`;

CREATE TABLE IF NOT EXISTS `PREFIX_accounting_export` (
  `id_accounting_export` int(11) NOT NULL AUTO_INCREMENT,
  `begin_to` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_to` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type` int(11) NOT NULL,
  `file` varchar(256) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_accounting_export`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
