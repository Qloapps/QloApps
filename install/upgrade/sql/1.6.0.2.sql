SET NAMES 'utf8';

INSERT INTO `PREFIX_hook` (`id_hook` , `name` , `title` , `description` , `position` , `live_edit`)
VALUES (NULL , 'actionAdminControllerSetMedia', 'Admin action setMedia', '', '0', '0');

INSERT INTO `PREFIX_hook_module` (`id_module`, `id_shop`, `id_hook`, `position`) (
	SELECT m.id_module, s.id_shop, h.id_hook, 0 FROM `PREFIX_module` m, `PREFIX_shop` s, `PREFIX_hook` h WHERE m.name IN ('graphnvd3') AND h.name IN ('GraphEngine', 'actionAdminControllerSetMedia')
);

INSERT INTO `PREFIX_hook_module` (`id_module`, `id_shop`, `id_hook`, `position`) (
  SELECT m.id_module, s.id_shop, h.id_hook, 0
  FROM `PREFIX_module` m, `PREFIX_shop` s, `PREFIX_hook` h
  WHERE m.name IN ('dashgoals', 'dashactivity', 'dashtrends', 'dashproducts')
        AND h.name IN ('actionAdminControllerSetMedia')
);

ALTER TABLE  `PREFIX_configuration` CHANGE  `name`  `name` VARCHAR(254) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE  `PREFIX_module_shop` ADD  `enable_device` TINYINT(1) NOT NULL DEFAULT  '7' AFTER  `id_shop`;

ALTER TABLE `PREFIX_theme` ADD `responsive` TINYINT( 1 ) NOT NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS `PREFIX_theme_meta` (
  `id_theme_meta` int(11) NOT NULL AUTO_INCREMENT,
  `id_theme` int(11) NOT NULL,
  `id_meta` int(10) unsigned NOT NULL,
  `left_column` tinyint(1) NOT NULL DEFAULT '0',
  `right_column` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_theme_meta`),
  UNIQUE KEY `id_theme_2` (`id_theme`,`id_meta`),
  KEY `id_theme` (`id_theme`),
  KEY `id_meta` (`id_meta`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_meta` ADD `configurable` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' AFTER `page`;
