Hello,

Please read the documentation before trying to override something here.
http://doc.prestashop.com/display/PS16/Overriding+default+behaviors

Frequently Asked Questions

Q: I added an override file but it seems to be ignored by PrestaShop
A: You need to trigger the regeneration of the /cache/class_index.php file. This is done simply by deleting the file. It is the same when manually removing an override: in order to reinstate the default behavior, you must delete the /cache/class_index.php file.
