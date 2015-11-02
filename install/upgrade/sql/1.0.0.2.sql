
/* STRUCTURE */
SET NAMES 'utf8';

ALTER TABLE PREFIX_currency CHANGE COLUMN conversion_rate conversion_rate DECIMAL(10,6) NOT NULL;

/*  CONTENTS */

INSERT INTO PREFIX_image_type (`name`, `width`, `height`, `products`, `categories`, `manufacturers`, `suppliers`) VALUES ('thickbox', 600, 600, 1, 0, 0, 0);
INSERT INTO PREFIX_image_type (`name`, `width`, `height`, `products`, `categories`, `manufacturers`, `suppliers`) VALUES ('category', 600, 150, 0, 1, 0, 0);
INSERT INTO PREFIX_image_type (`name`, `width`, `height`, `products`, `categories`, `manufacturers`, `suppliers`) VALUES ('thickbox', 129, 129, 1, 0, 0, 0);

/* CONFIGURATION VARIABLE */
