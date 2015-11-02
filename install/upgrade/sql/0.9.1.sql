/* STRUCTURE */
ALTER TABLE `PREFIX_product` CHANGE `price` `price` DECIMAL(13,6) NOT NULL DEFAULT '0.000000';

/*  CONTENTS */
DELETE FROM `PREFIX_carrier_lang` WHERE `id_carrier` = (SELECT c.`id_carrier` FROM `PREFIX_carrier` c WHERE c.`name` = 'My download manager' LIMIT 1);
DELETE FROM `PREFIX_carrier` WHERE `name` = 'My download manager';

/* Conf vars */
INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_TAX_NO', '0', NOW(), NOW());