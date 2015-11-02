<?php

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// The header location has been commented because swift do a require() on each .php of this folder
// (and this relocation make it do it recursively on each PrestaShop folder and so PrestaShop PHP file)
//header("Location: ../");
exit;
