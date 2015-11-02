<?php
include('config/config.php');
if ($_SESSION['verify'] != 'RESPONSIVEfilemanager') {
    die('forbiden');
}
include('include/utils.php');

if (preg_match('/\.{1,2}[\/|\\\]/', $_POST['path']) !== 0) {
    die('wrong path');
}

if (strpos($_POST['name'], '/') !== false || strpos($_POST['name'], '\\') !== false) {
    die('wrong path');
}

$path = $current_path.$_POST['path'];
$name = $_POST['name'];

$info = pathinfo($name);
if (!in_array(fix_strtolower($info['extension']), $ext)) {
    die('wrong extension');
}

header('Pragma: private');
header('Cache-control: private, must-revalidate');
header('Content-Type: application/octet-stream');
header('Content-Length: '.(string)filesize($path.$name));
header('Content-Disposition: attachment; filename="'.($name).'"');
readfile($path.$name);

exit;
