<?php
include('config/config.php');
if ($_SESSION['verify'] != 'RESPONSIVEfilemanager') {
    die('forbiden');
}
include('include/utils.php');

$_POST['path'] = $current_path.str_replace('\0', '', $_POST['path']);
$_POST['path_thumb'] = $thumbs_base_path.str_replace("\0", '', $_POST['path_thumb']);

$storeFolder = $_POST['path'];
$storeFolderThumb = $_POST['path_thumb'];

$path_pos = strpos($storeFolder, $current_path);
$thumb_pos = strpos($_POST['path_thumb'], $thumbs_base_path);

if ($path_pos === false || $thumb_pos === false
    || preg_match('/\.{1,2}[\/|\\\]/', $_POST['path_thumb']) !== 0
    || preg_match('/\.{1,2}[\/|\\\]/', $_POST['path']) !== 0) {
    die('wrong path');
}

$path = $storeFolder;
$cycle = true;
$max_cycles = 50;
$i = 0;
while ($cycle && $i < $max_cycles) {
    $i++;
    if ($path == $current_path) {
        $cycle = false;
    }
    if (file_exists($path.'config.php')) {
        require_once($path.'config.php');
        $cycle = false;
    }
    $path = fix_dirname($path).'/';
}

if (!empty($_FILES)) {
    $info = pathinfo($_FILES['file']['name']);
    if (isset($info['extension'])
            && in_array(fix_strtolower($info['extension']), $ext)
            // If fileinfo extension is installed, check the mime type too
            && (!function_exists('mime_content_type') || in_array(mime_content_type($_FILES['file']['tmp_name']), $mime))
    ) {
        $tempFile = $_FILES['file']['tmp_name'];

        $targetPath = $storeFolder;
        $targetPathThumb = $storeFolderThumb;
        $_FILES['file']['name'] = fix_filename($_FILES['file']['name'], $transliteration);

        $file_name_splitted = explode('.', $_FILES['file']['name']);
        array_pop($file_name_splitted);
        $_FILES['file']['name'] = implode('-', $file_name_splitted).'.'.$info['extension'];

        if (file_exists($targetPath.$_FILES['file']['name'])) {
            $i = 1;
            $info = pathinfo($_FILES['file']['name']);
            while (file_exists($targetPath.$info['filename'].'_'.$i.'.'.$info['extension'])) {
                $i++;
            }
            $_FILES['file']['name'] = $info['filename'].'_'.$i.'.'.$info['extension'];
        }
        $targetFile = $targetPath.$_FILES['file']['name'];
        $targetFileThumb = $targetPathThumb.$_FILES['file']['name'];

        if (in_array(fix_strtolower($info['extension']), $ext_img) && @getimagesize($tempFile) != false) {
            $is_img = true;
        } else {
            $is_img = false;
        }

        if ($is_img) {
            move_uploaded_file($tempFile, $targetFile);
            chmod($targetFile, 0755);

            $memory_error = false;
            if (!create_img_gd($targetFile, $targetFileThumb, 122, 91)) {
                $memory_error = false;
            } else {
                if (!new_thumbnails_creation($targetPath, $targetFile, $_FILES['file']['name'], $current_path, $relative_image_creation, $relative_path_from_current_pos, $relative_image_creation_name_to_prepend, $relative_image_creation_name_to_append, $relative_image_creation_width, $relative_image_creation_height, $fixed_image_creation, $fixed_path_from_filemanager, $fixed_image_creation_name_to_prepend, $fixed_image_creation_to_append, $fixed_image_creation_width, $fixed_image_creation_height)) {
                    $memory_error = false;
                } else {
                    $imginfo = getimagesize($targetFile);
                    $srcWidth = $imginfo[0];
                    $srcHeight = $imginfo[1];

                    if ($image_resizing) {
                        if ($image_resizing_width == 0) {
                            if ($image_resizing_height == 0) {
                                $image_resizing_width = $srcWidth;
                                $image_resizing_height = $srcHeight;
                            } else {
                                $image_resizing_width = $image_resizing_height * $srcWidth / $srcHeight;
                            }
                        } elseif ($image_resizing_height == 0) {
                            $image_resizing_height = $image_resizing_width * $srcHeight / $srcWidth;
                        }
                        $srcWidth = $image_resizing_width;
                        $srcHeight = $image_resizing_height;
                        create_img_gd($targetFile, $targetFile, $image_resizing_width, $image_resizing_height);
                    }
                    //max resizing limit control
                    $resize = false;
                    if ($image_max_width != 0 && $srcWidth > $image_max_width) {
                        $resize = true;
                        $srcHeight = $image_max_width * $srcHeight / $srcWidth;
                        $srcWidth = $image_max_width;
                    }
                    if ($image_max_height != 0 && $srcHeight > $image_max_height) {
                        $resize = true;
                        $srcWidth = $image_max_height * $srcWidth / $srcHeight;
                        $srcHeight = $image_max_height;
                    }
                    if ($resize) {
                        create_img_gd($targetFile, $targetFile, $srcWidth, $srcHeight);
                    }
                }
            }
            if ($memory_error) {
                //error
                unlink($targetFile);
                header('HTTP/1.1 406 Not enought Memory', true, 406);
                exit();
            }
        } else {
            move_uploaded_file($tempFile, $targetFile);
            chmod($targetFile, 0755);
        }
    } else {
        header('HTTP/1.1 406 file not permitted', true, 406);
        exit();
    }
} else {
    header('HTTP/1.1 405 Bad Request', true, 405);
    exit();
}
if (isset($_POST['submit'])) {
    $query = http_build_query(
        array(
            'type' => $_POST['type'],
            'lang' => $_POST['lang'],
            'popup' => $_POST['popup'],
            'field_id' => $_POST['field_id'],
            'fldr' => $_POST['fldr'],
        )
    );
    header('location: dialog.php?'.$query);
}
