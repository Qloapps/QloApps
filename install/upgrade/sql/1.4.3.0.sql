SET NAMES 'utf8';

UPDATE `PREFIX_address_format` SET `format`='firstname lastname
company
address1
address2
city
State:name
postcode
Country:name' 
WHERE `id_country` = (SELECT `id_country` FROM `PREFIX_country` WHERE `iso_code`='GB');

UPDATE `PREFIX_country` SET `contains_states` = 1 WHERE `id_country` = 145;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PS_LEGACY_IMAGES', '1', NOW(), NOW());