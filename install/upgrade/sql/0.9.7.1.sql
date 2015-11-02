/* STRUCTURE */

ALTER TABLE `PREFIX_module` ADD INDEX (`name`);

/*  CONTENTS */

INSERT INTO `PREFIX_hook` (`name` , `title`, `description`, `position`) VALUES
('footer', 'Footer', 'Add block in footer', 1),
('PDFInvoice', 'PDF Invoice', 'Allow the display of extra informations into the PDF invoice', 0);
UPDATE `PREFIX_hook` SET `description` = 'Add blocks in the header', `position` = '1' WHERE `name` = 'header' LIMIT 1 ;
UPDATE `PREFIX_currency` SET `iso_code` = 'XXX' WHERE `iso_code` IS NULL;


/* CONFIGURATION VARIABLE */