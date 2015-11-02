SET NAMES 'utf8';

ALTER TABLE `PREFIX_stock_mvt_reason` ADD `sign` TINYINT(1) NOT NULL DEFAULT '1' AFTER `id_stock_mvt_reason`;
UPDATE `PREFIX_stock_mvt_reason` SET `sign`=-1;
UPDATE `PREFIX_stock_mvt_reason` SET `sign`=1 WHERE `id_stock_mvt_reason`=3;
UPDATE `PREFIX_stock_mvt_reason` SET `id_stock_mvt_reason`=`id_stock_mvt_reason`+2 ORDER BY `id_stock_mvt_reason` DESC;
UPDATE `PREFIX_stock_mvt` SET `id_stock_mvt_reason`=`id_stock_mvt_reason`+2;
UPDATE `PREFIX_stock_mvt_reason_lang` SET `id_stock_mvt_reason`=`id_stock_mvt_reason`+2 ORDER BY `id_stock_mvt_reason` DESC;
INSERT INTO `PREFIX_stock_mvt_reason` (`id_stock_mvt_reason` ,`sign` ,`date_add` ,`date_upd`) VALUES ('1', '1', NOW(), NOW()), ('2', '-1', NOW(), NOW());

INSERT INTO `PREFIX_stock_mvt_reason_lang` (`id_stock_mvt_reason` ,`id_lang` ,`name`) VALUES 
('1', '1', 'Increase'), 
('1', '2', 'Augmenter'), 
('1', '3', 'Aumentar'), 
('1', '4', 'Erhöhen'), 
('1', '5', 'Aumento'), 
('2', '1', 'Decrease'), 
('2', '2', 'Diminuer'), 
('2', '3', 'Disminuir'), 
('2', '4', 'Reduzieren'), 
('2', '5', 'Diminuzione');

