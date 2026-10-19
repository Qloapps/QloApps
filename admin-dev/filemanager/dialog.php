<?php
include('config/config.php');

$_SESSION["verify"] = "RESPONSIVEfilemanager";

if (isset($_POST['submit'])) {
    include('upload.php');
} else {
    include('include/utils.php');



    if (isset($_GET['fldr'])
        && !empty($_GET['fldr'])
        && preg_match('/\.{1,2}[\/|\\\]?/', urldecode($_GET['fldr'])) === 0
    ) {
        $subdir = str_replace("\0", '', urldecode(trim($_GET['fldr'], '/').'/'));
    } else {
        $subdir = '';
    }

//remember last position
    setcookie('last_position', $subdir, time() + (86400 * 7));

    if ($subdir == '') {
        if (!empty($_COOKIE['last_position'])
            && strpos($_COOKIE['last_position'], '.') === false
        ) {
            $subdir = trim($_COOKIE['last_position']);
        }
    }

    if ($subdir == '/') {
        $subdir = '';
    }


    /***
     *SUB-DIR CODE
     ***/
    if (!isset($_SESSION['subfolder'])) {
        $_SESSION['subfolder'] = '';
    }
    $subfolder = '';
    if (!empty($_SESSION['subfolder']) && strpos($_SESSION['subfolder'], '../') === false
        && strpos($_SESSION['subfolder'], './') === false && strpos($_SESSION['subfolder'], '/') !== 0
        && strpos($_SESSION['subfolder'], '.') === false
    ) {
        $subfolder = $_SESSION['subfolder'];
    }

    if ($subfolder != '' && $subfolder[strlen($subfolder) - 1] != '/') {
        $subfolder .= '/';
    }

    if (!file_exists($current_path.$subfolder.$subdir)) {
        $subdir = '';
        if (!file_exists($current_path.$subfolder.$subdir)) {
            $subfolder = '';
        }
    }

    if (trim($subfolder) == '') {
        $cur_dir = $upload_dir.$subdir;
        $cur_path = $current_path.$subdir;
        $thumbs_path = $thumbs_base_path;
        $parent = $subdir;
    } else {
        $cur_dir = $upload_dir.$subfolder.$subdir;
        $cur_path = $current_path.$subfolder.$subdir;
        $thumbs_path = $thumbs_base_path.$subfolder;
        $parent = $subfolder.$subdir;
    }

    $cycle = true;
    $max_cycles = 50;
    $i = 0;
    while ($cycle && $i < $max_cycles) {
        $i++;
        if ($parent == './') {
            $parent = '';
        }
        if (file_exists($current_path.$parent.'config.php')) {
            require_once($current_path.$parent.'config.php');
            $cycle = false;
        }

        if ($parent == '') {
            $cycle = false;
        } else {
            $parent = fix_dirname($parent).'/';
        }
    }

    if (!is_dir($thumbs_path.$subdir)) {
        create_folder(false, $thumbs_path.$subdir);
    }

    if (isset($_GET['popup'])) {
        $popup = $_GET['popup'];
    } else {
        $popup = 0;
    }
//Sanitize popup
    $popup = !!$popup;

//view type
    if (!isset($_SESSION['view_type'])) {
        $view = $default_view;
        $_SESSION['view_type'] = $view;
    }
    if (isset($_GET['view'])) {
        $view = $_GET['view'];
        $_SESSION['view_type'] = $view;
    }
    $view = $_SESSION['view_type'];

    if (isset($_GET['filter'])) {
        $filter = fix_filename($_GET['filter'], $transliteration);
    } else {
        $filter = '';
    }

    if (!isset($_SESSION['sort_by'])) {
        $_SESSION['sort_by'] = '';
    }
    if (isset($_GET['sort_by'])) {
        $sort_by = $_SESSION['sort_by'] = fix_filename($_GET['sort_by'], $transliteration);
    } else {
        $sort_by = $_SESSION['sort_by'];
    }

    if (!isset($_SESSION['descending'])) {
        $_SESSION['descending'] = false;
    }
    if (isset($_GET['descending'])) {
        $descending = $_SESSION['descending'] = fix_filename($_GET['descending'], $transliteration) === 'true';
    } else {
        $descending = $_SESSION['descending'];
    }


    $lang = $default_language;
    if (isset($_GET['lang']) && $_GET['lang'] != 'undefined' && $_GET['lang'] != '') {
        $lang = $_GET['lang'];
    }

    $language_file = 'lang/'.$default_language.'.php';
    if ($lang != $default_language) {
        $path_parts = pathinfo($lang);
        if (is_readable('lang/'.$path_parts['basename'].'.php')) {
            $language_file = 'lang/'.$path_parts['basename'].'.php';
        } else {
            $lang = $default_language;
        }
    }


    require_once $language_file;

    if (!isset($_GET['type'])) {
        $_GET['type'] = 0;
    }
    if (!isset($_GET['field_id'])) {
        $_GET['field_id'] = '';
    }

    $get_params = http_build_query(
        array(
            'type' => Tools::safeOutput($_GET['type']),
            'lang' => Tools::safeOutput($lang),
            'popup' => $popup,
            'field_id' => isset($_GET['field_id']) ? (int)$_GET['field_id'] : '',
            'fldr' => ''
        )
    );
    ?>

	<!DOCTYPE html>
	<html xmlns="https://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
		<meta name="robots" content="noindex,nofollow">
		<title>Responsive FileManager</title>
		<link rel="shortcut icon" href="img/ico/favicon.ico">
		<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
		<link href="css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>
		<link href="css/bootstrap-lightbox.min.css" rel="stylesheet" type="text/css"/>
		<link href="css/style.css" rel="stylesheet" type="text/css"/>
		<link href="css/dropzone.min.css" type="text/css" rel="stylesheet"/>
		<link href="css/jquery.contextMenu.min.css" rel="stylesheet" type="text/css"/>
		<link href="css/bootstrap-modal.min.css" rel="stylesheet" type="text/css"/>
		<link href="jPlayer/skin/blue.monday/jplayer.blue.monday.css" rel="stylesheet" type="text/css">
		<!--[if lt IE 8]>
		<style>
			.img-container span, .img-container-mini span {
				display: inline-block;
				height: 100%;
			}
		</style><![endif]-->
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script type="text/javascript">
			if (typeof jQuery === 'undefined') {
				document.write(unescape("%3Cscript src='js/jquery.js' type='text/javascript'%3E%3C/script%3E"));
			}
		</script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/bootstrap-lightbox.min.js"></script>
		<script type="text/javascript" src="js/dropzone.min.js"></script>
		<script type="text/javascript" src="js/jquery.touchSwipe.min.js"></script>
		<script type="text/javascript" src="js/modernizr.custom.js"></script>
		<script type="text/javascript" src="js/bootbox.min.js"></script>
		<script type="text/javascript" src="js/bootstrap-modal.min.js"></script>
		<script type="text/javascript" src="js/bootstrap-modalmanager.min.js"></script>
		<script type="text/javascript" src="jPlayer/jquery.jplayer.min.js"></script>
		<script type="text/javascript" src="js/imagesloaded.pkgd.min.js"></script>
		<script type="text/javascript" src="js/jquery.queryloader2.min.js"></script>
		<?php
        if ($aviary_active) {
            if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
                ?>
				<script type="text/javascript" src="https://dme0ih8comzn4.cloudfront.net/js/feather.js"></script>
			<?php
            } else {
                ?>
				<script type="text/javascript" src="http://feather.aviary.com/js/feather.js "></script>
			<?php

            }
        }
    ?>

		<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		<script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6.2/html5shiv.js"></script>
		<![endif]-->
		<script src="js/jquery.ui.position.min.js" type="text/javascript"></script>
		<script src="js/jquery.contextMenu.min.js" type="text/javascript"></script>

		<script>
			var ext_img = new Array('<?php echo implode("','", $ext_img)?>');
			var allowed_ext = new Array('<?php echo implode("','", $ext)?>');
			var loading_bar =<?php echo $loading_bar?"true":"false";
    ?>;
			var image_editor =<?php echo $aviary_active?"true":"false";
    ?>;
			//dropzone config
			Dropzone.options.myAwesomeDropzone = {
				dictInvalidFileType: "<?php echo lang_Error_extension;
    ?>",
				dictFileTooBig: "<?php echo lang_Error_Upload;
    ?>",
				dictResponseError: "SERVER ERROR",
				paramName: "file", // The name that will be used to transfer the file
				maxFilesize: <?php echo $MaxSizeUpload;
    ?>, // MB
				url: "upload.php",
				accept: function (file, done) {
					var extension = file.name.split('.').pop();
					extension = extension.toLowerCase();
					if ($.inArray(extension, allowed_ext) > -1) {
						done();
					}
					else {
						done("<?php echo lang_Error_extension;
    ?>");
					}
				}
			};
			if (image_editor) {
				var featherEditor = new Aviary.Feather({
					apiKey: "<?php echo $aviary_key;
    ?>",
					apiVersion: <?php echo $aviary_version;
    ?>,
					language: "<?php echo $aviary_language;
    ?>",
					theme: 'light',
					tools: 'all',
					onSave: function (imageID, newURL) {
						show_animation();
						var img = document.getElementById(imageID);
						img.src = newURL;
						$.ajax({
							type: "POST",
							url: "ajax_calls.php?action=save_img",
							data: { url: newURL, path: $('#sub_folder').val() + $('#fldr_value').val(), name: $('#aviary_img').data('name') }
						}).done(function (msg) {
							featherEditor.close();
							d = new Date();
							$("figure[data-name='" + $('#aviary_img').data('name') + "']").find('img').each(function () {
								$(this).attr('src', $(this).attr('src') + "?" + d.getTime());
							});
							$("figure[data-name='" + $('#aviary_img').data('name') + "']").find('figcaption a.preview').each(function () {
								$(this).data('url', $(this).data('url') + "?" + d.getTime());
							});
							hide_animation();
						});
						return false;
					},
					onError: function (errorObj) {
						bootbox.alert(errorObj.message);
					}

				});
			}
		</script>
		<script type="text/javascript" src="js/include.min.js"></script>
	</head>
	<body>
	<input type="hidden" id="popup" value="<?php echo Tools::safeOutput($popup);
    ?>"/>
	<input type="hidden" id="view" value="<?php echo Tools::safeOutput($view);
    ?>"/>
	<input type="hidden" id="cur_dir" value="<?php echo Tools::safeOutput($cur_dir);
    ?>"/>
	<input type="hidden" id="cur_dir_thumb" value="<?php echo Tools::safeOutput($subdir);
    ?>"/>
	<input type="hidden" id="insert_folder_name" value="<?php echo Tools::safeOutput(lang_Insert_Folder_Name);
    ?>"/>
	<input type="hidden" id="new_folder" value="<?php echo Tools::safeOutput(lang_New_Folder);
    ?>"/>
	<input type="hidden" id="ok" value="<?php echo Tools::safeOutput(lang_OK);
    ?>"/>
	<input type="hidden" id="cancel" value="<?php echo Tools::safeOutput(lang_Cancel);
    ?>"/>
	<input type="hidden" id="rename" value="<?php echo Tools::safeOutput(lang_Rename);
    ?>"/>
	<input type="hidden" id="lang_duplicate" value="<?php echo Tools::safeOutput(lang_Duplicate);
    ?>"/>
	<input type="hidden" id="duplicate" value="<?php if ($duplicate_files) {
    echo 1;
} else {
    echo 0;
}
    ?>"/>
	<input type="hidden" id="base_url" value="<?php echo Tools::safeOutput($base_url) ?>"/>
	<input type="hidden" id="base_url_true" value="<?php echo base_url();
    ?>"/>
	<input type="hidden" id="fldr_value" value="<?php echo Tools::safeOutput($subdir);
    ?>"/>
	<input type="hidden" id="sub_folder" value="<?php echo Tools::safeOutput($subfolder);
    ?>"/>
	<input type="hidden" id="file_number_limit_js" value="<?php echo Tools::safeOutput($file_number_limit_js);
    ?>"/>
	<input type="hidden" id="descending" value="<?php echo $descending ? "true" : "false";
    ?>"/>
	<?php $protocol = 'http';
    ?>
	<input type="hidden" id="current_url" value="<?php echo str_replace(array('&filter='.$filter), array(''), $protocol."://".$_SERVER['HTTP_HOST'].Tools::safeOutput($_SERVER['REQUEST_URI']));
    ?>"/>
	<input type="hidden" id="lang_show_url" value="<?php echo Tools::safeOutput(lang_Show_url);
    ?>"/>
	<input type="hidden" id="lang_extract" value="<?php echo Tools::safeOutput(lang_Extract);
    ?>"/>
	<input type="hidden" id="lang_file_info" value="<?php echo fix_strtoupper(lang_File_info);
    ?>"/>
	<input type="hidden" id="lang_edit_image" value="<?php echo Tools::safeOutput(lang_Edit_image);
    ?>"/>
	<input type="hidden" id="transliteration" value="<?php echo $transliteration ? "true" : "false";
    ?>"/>
	<?php if ($upload_files) {
    ?>
		<!----- uploader div start ------->

		<div class="uploader">
			<center>
				<button class="btn btn-inverse close-uploader">
					<i class="icon-backward icon-white"></i> <?php echo Tools::safeOutput(lang_Return_Files_List) ?></button>
			</center>
			<div class="space10"></div>
			<div class="space10"></div>
			<?php if ($java_upload) {
    ?>
			<div class="tabbable upload-tabbable"> <!-- Only required for left/right tabs -->
				<ul class="nav nav-tabs">
					<li class="active"><a href="#tab1" data-toggle="tab"><?php echo Tools::safeOutput(lang_Upload_base);
    ?></a></li>
					<li><a href="#tab2" id="uploader-btn" data-toggle="tab"><?php echo Tools::safeOutput(lang_Upload_java);
    ?></a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="tab1">
						<?php
}
    ?>
						<form action="dialog.php" method="post" enctype="multipart/form-data" id="myAwesomeDropzone" class="dropzone">
							<input type="hidden" name="path" value="<?php echo Tools::safeOutput($subfolder.$subdir);
    ?>"/>
							<input type="hidden" name="path_thumb" value="<?php echo Tools::safeOutput($subfolder.$subdir);
    ?>"/>

							<div class="fallback">
								<?php echo lang_Upload_file ?>:<br/>
								<input name="file" type="file"/>
								<input type="hidden" name="fldr" value="<?php echo Tools::safeOutput($subdir);
    ?>"/>
								<input type="hidden" name="view" value="<?php echo Tools::safeOutput($view);
    ?>"/>
								<input type="hidden" name="type" value="<?php echo Tools::safeOutput($_GET['type']);
    ?>"/>
								<input type="hidden" name="field_id" value="<?php echo (int)$_GET['field_id'];
    ?>"/>
								<input type="hidden" name="popup" value="<?php echo Tools::safeOutput($popup);
    ?>"/>
								<input type="hidden" name="lang" value="<?php echo Tools::safeOutput($lang);
    ?>"/>
								<input type="hidden" name="filter" value="<?php echo Tools::safeOutput($filter);
    ?>"/>
								<input type="submit" name="submit" value="<?php echo lang_OK ?>"/>
							</div>
						</form>
						<div class="upload-help"><?php echo Tools::safeOutput(lang_Upload_base_help);
    ?></div>
						<?php if ($java_upload) {
    ?>
					</div>
					<div class="tab-pane" id="tab2">
						<div id="iframe-container">

						</div>
						<div class="upload-help"><?php echo Tools::safeOutput(lang_Upload_java_help);
    ?></div>
					</div>
					<?php
}
    ?>
				</div>
			</div>

		</div>
		<!----- uploader div start ------->

	<?php
}
    ?>
	<div class="container-fluid">

	<?php

    $class_ext = '';
    $src = '';

    if ($_GET['type'] == 1) {
        $apply = 'apply_img';
    } elseif ($_GET['type'] == 2) {
        $apply = 'apply_link';
    } elseif ($_GET['type'] == 0 && $_GET['field_id'] == '') {
        $apply = 'apply_none';
    } elseif ($_GET['type'] == 3) {
        $apply = 'apply_video';
    } else {
        $apply = 'apply';
    }

    $files = scandir($current_path.$subfolder.$subdir);
    $n_files = count($files);

    //php sorting
    $sorted = array();
    $current_folder = array();
    $prev_folder = array();
    foreach ($files as $k => $file) {
        if ($file == ".") {
            $current_folder = array('file' => $file);
        } elseif ($file == "..") {
            $prev_folder = array('file' => $file);
        } elseif (is_dir($current_path.$subfolder.$subdir.$file)) {
            $date = filemtime($current_path.$subfolder.$subdir.$file);
            $size = foldersize($current_path.$subfolder.$subdir.$file);
            $file_ext = lang_Type_dir;
            $sorted[$k] = array('file' => $file, 'date' => $date, 'size' => $size, 'extension' => $file_ext);
        } else {
            $file_path = $current_path.$subfolder.$subdir.$file;
            $date = filemtime($file_path);
            $size = filesize($file_path);
            $file_ext = substr(strrchr($file, '.'), 1);
            $sorted[$k] = array('file' => $file, 'date' => $date, 'size' => $size, 'extension' => $file_ext);
        }
    }

    function filenameSort($x, $y)
    {
        return $x['file'] < $y['file'];
    }

    function dateSort($x, $y)
    {
        return $x['date'] < $y['date'];
    }

    function sizeSort($x, $y)
    {
        return $x['size'] - $y['size'];
    }

    function extensionSort($x, $y)
    {
        return $x['extension'] < $y['extension'];
    }

    switch ($sort_by) {
        case 'name':
            usort($sorted, 'filenameSort');
            break;
        case 'date':
            usort($sorted, 'dateSort');
            break;
        case 'size':
            usort($sorted, 'sizeSort');
            break;
        case 'extension':
            usort($sorted, 'extensionSort');
            break;
        default:
            break;

    }

    if ($descending) {
        $sorted = array_reverse($sorted);
    }

    $files = array();
    if (!empty($prev_folder)) {
        $files = array($prev_folder);
    }
    if (!empty($current_folder)) {
        $files = array_merge($files, array($current_folder));
    }
    $files = array_merge($files, $sorted);
    ?>
	<!----- header div start ------->
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container-fluid">
				<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<div class="brand"><?php echo Tools::safeOutput(lang_Toolbar);
    ?> -></div>
				<div class="nav-collapse collapse">
					<div class="filters">
						<div class="row-fluid">
							<div class="span3 half">
								<span><?php echo Tools::safeOutput(lang_Actions);
    ?>:</span>
								<?php if ($upload_files) {
    ?>
									<button class="tip btn upload-btn" title="<?php echo Tools::safeOutput(lang_Upload_file);
    ?>">
										<i class="icon-plus"></i><i class="icon-file"></i></button>
								<?php
}
    ?>
								<?php if ($create_folders) {
    ?>
									<button class="tip btn new-folder" title="<?php echo Tools::safeOutput(lang_New_Folder) ?>">
										<i class="icon-plus"></i><i class="icon-folder-open"></i></button>
								<?php
}
    ?>
							</div>
							<div class="span3 half view-controller">
								<span><?php echo lang_View;
    ?>:</span>
								<button class="btn tip<?php if ($view == 0) {
    echo " btn-inverse";
}
    ?>" id="view0" data-value="0" title="<?php echo Tools::safeOutput(lang_View_boxes);
    ?>">
									<i class="icon-th <?php if ($view == 0) {
    echo "icon-white";
}
    ?>"></i></button>
								<button class="btn tip<?php if ($view == 1) {
    echo " btn-inverse";
}
    ?>" id="view1" data-value="1" title="<?php echo Tools::safeOutput(lang_View_list);
    ?>">
									<i class="icon-align-justify <?php if ($view == 1) {
    echo "icon-white";
}
    ?>"></i>
								</button>
								<button class="btn tip<?php if ($view == 2) {
    echo " btn-inverse";
}
    ?>" id="view2" data-value="2" title="<?php echo Tools::safeOutput(lang_View_columns_list);
    ?>">
									<i class="icon-fire <?php if ($view == 2) {
    echo "icon-white";
}
    ?>"></i></button>
							</div>
							<div class="span6 types">
								<span><?php echo Tools::safeOutput(lang_Filters);
    ?>:</span>
								<?php if ($_GET['type'] != 1 && $_GET['type'] != 3) {
    ?>
									<input id="select-type-1" name="radio-sort" type="radio" data-item="ff-item-type-1" checked="checked" class="hide"/>
									<label id="ff-item-type-1" title="<?php echo Tools::safeOutput(lang_Files);
    ?>" for="select-type-1" class="tip btn ff-label-type-1"><i class="icon-file"></i></label>
									<input id="select-type-2" name="radio-sort" type="radio" data-item="ff-item-type-2" class="hide"/>
									<label id="ff-item-type-2" title="<?php echo Tools::safeOutput(lang_Images);
    ?>" for="select-type-2" class="tip btn ff-label-type-2"><i class="icon-picture"></i></label>
									<input id="select-type-3" name="radio-sort" type="radio" data-item="ff-item-type-3" class="hide"/>
									<label id="ff-item-type-3" title="<?php echo Tools::safeOutput(lang_Archives);
    ?>" for="select-type-3" class="tip btn ff-label-type-3"><i class="icon-inbox"></i></label>
									<input id="select-type-4" name="radio-sort" type="radio" data-item="ff-item-type-4" class="hide"/>
									<label id="ff-item-type-4" title="<?php echo Tools::safeOutput(lang_Videos);
    ?>" for="select-type-4" class="tip btn ff-label-type-4"><i class="icon-film"></i></label>
									<input id="select-type-5" name="radio-sort" type="radio" data-item="ff-item-type-5" class="hide"/>
									<label id="ff-item-type-5" title="<?php echo Tools::safeOutput(lang_Music);
    ?>" for="select-type-5" class="tip btn ff-label-type-5"><i class="icon-music"></i></label>
								<?php
}
    ?>
								<input accesskey="f" type="text" class="filter-input" id="filter-input" name="filter" placeholder="<?php echo fix_strtolower(lang_Text_filter);
    ?>..." value="<?php echo Tools::safeOutput($filter);
    ?>"/><?php if ($n_files > $file_number_limit_js) {
    ?><label id="filter" class="btn"><i class="icon-play"></i></label><?php
}
    ?>

								<input id="select-type-all" name="radio-sort" type="radio" data-item="ff-item-type-all" class="hide"/>
								<label id="ff-item-type-all" title="<?php echo Tools::safeOutput(lang_All);
    ?>" <?php if (Tools::getValue('type') == 1 || Tools::getValue('type') == 3) {
    ?>style="visibility: hidden;" <?php
}
    ?> data-item="ff-item-type-all" for="select-type-all" style="margin-rigth:0px;" class="tip btn btn-inverse ff-label-type-all"><i class="icon-align-justify icon-white"></i></label>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!----- header div end ------->

	<!----- breadcrumb div start ------->

	<div class="row-fluid">
		<?php
        $link = "dialog.php?".$get_params;
    ?>
		<ul class="breadcrumb">
			<li class="pull-left"><a href="<?php echo Tools::safeOutput($link) ?>/"><i class="icon-home"></i></a></li>
			<li><span class="divider">/</span></li>
			<?php
            $bc = explode("/", $subdir);
    $tmp_path = '';
    if (!empty($bc)) {
        foreach ($bc as $k => $b) {
            $tmp_path .= $b."/";
            if ($k == count($bc) - 2) {
                ?>
						<li class="active"><?php echo Tools::safeOutput($b) ?></li><?php

            } elseif ($b != "") {
                ?>
						<li><a href="<?php echo Tools::safeOutput($link.$tmp_path)?>"><?php echo Tools::safeOutput($b) ?></a></li>
						<li><span class="divider"><?php echo "/";
                ?></span></li>
					<?php

            }
        }
    }
    ?>
			<li class="pull-right">
				<a class="btn-small" href="javascript:void('')" id="info"><i class="icon-question-sign"></i></a></li>
			<li class="pull-right">
				<a id="refresh" class="btn-small" href="dialog.php?<?php echo Tools::safeOutput($get_params.$subdir."&".uniqid()) ?>"><i class="icon-refresh"></i></a>
			</li>

			<li class="pull-right">
				<div class="btn-group">
					<a class="btn dropdown-toggle sorting-btn" data-toggle="dropdown" href="#">
						<i class="icon-signal"></i>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu pull-left sorting">
						<li>
							<center><strong><?php echo Tools::safeOutput(lang_Sorting) ?></strong></center>
						</li>
						<li><a class="sorter sort-name <?php if ($sort_by == "name") {
    echo ($descending) ? "descending" : "ascending";
}
    ?>" href="javascript:void('')" data-sort="name"><?php echo Tools::safeOutput(lang_Filename);
    ?></a></li>
						<li><a class="sorter sort-date <?php if ($sort_by == "date") {
    echo ($descending) ? "descending" : "ascending";
}
    ?>" href="javascript:void('')" data-sort="date"><?php echo Tools::safeOutput(lang_Date);
    ?></a></li>
						<li><a class="sorter sort-size <?php if ($sort_by == "size") {
    echo ($descending) ? "descending" : "ascending";
}
    ?>" href="javascript:void('')" data-sort="size"><?php echo Tools::safeOutput(lang_Size);
    ?></a></li>
						<li><a class="sorter sort-extension <?php if ($sort_by == "extension") {
    echo ($descending) ? "descending" : "ascending";
}
    ?>" href="javascript:void('')" data-sort="extension"><?php echo Tools::safeOutput(lang_Type);
    ?></a></li>
					</ul>
				</div>
			</li>
		</ul>
	</div>
	<!----- breadcrumb div end ------->
	<div class="row-fluid ff-container">
	<div class="span12">
	<?php if (@opendir($current_path.$subfolder.$subdir) === false) {
    ?>
		<br/>
		<div class="alert alert-error">There is an error! The upload folder there isn't. Check your config.php file.
		</div>
	<?php
} else {
    ?>
	<h4 id="help"><?php echo Tools::safeOutput(lang_Swipe_help);
    ?></h4>
	<?php if (isset($folder_message)) {
    ?>
		<div class="alert alert-block"><?php echo Tools::safeOutput($folder_message);
    ?></div>
	<?php
}
    ?>
	<?php if ($show_sorting_bar) {
    ?>
		<!-- sorter -->
		<div class="sorter-container <?php echo "list-view".Tools::safeOutput($view);
    ?>">
			<div class="file-name"><a class="sorter sort-name <?php if ($sort_by == "name") {
    echo ($descending) ? "descending" : "ascending";
}
    ?>" href="javascript:void('')" data-sort="name"><?php echo Tools::safeOutput(lang_Filename);
    ?></a></div>
			<div class="file-date"><a class="sorter sort-date <?php if ($sort_by == "date") {
    echo ($descending) ? "descending" : "ascending";
}
    ?>" href="javascript:void('')" data-sort="date"><?php echo Tools::safeOutput(lang_Date);
    ?></a></div>
			<div class="file-size"><a class="sorter sort-size <?php if ($sort_by == "size") {
    echo ($descending) ? "descending" : "ascending";
}
    ?>" href="javascript:void('')" data-sort="size"><?php echo Tools::safeOutput(lang_Size);
    ?></a></div>
			<div class='img-dimension'><?php echo Tools::safeOutput(lang_Dimension);
    ?></div>
			<div class='file-extension'><a class="sorter sort-extension <?php if ($sort_by == "extension") {
    echo ($descending) ? "descending" : "ascending";
}
    ?>" href="javascript:void('')" data-sort="extension"><?php echo Tools::safeOutput(lang_Type);
    ?></a></div>
			<div class='file-operations'><?php echo Tools::safeOutput(lang_Operations);
    ?></div>
		</div>
	<?php
}
    ?>

	<input type="hidden" id="file_number" value="<?php echo Tools::safeOutput($n_files);
    ?>"/>
	<!--ul class="thumbnails ff-items"-->
	<ul class="grid cs-style-2 <?php echo "list-view".Tools::safeOutput($view);
    ?>">
	<?php

    $jplayer_ext = array(
        "mp4",
        "flv",
        "webmv",
        "webma",
        "webm",
        "m4a",
        "m4v",
        "ogv",
        "oga",
        "mp3",
        "midi",
        "mid",
        "ogg",
        "wav"
    );
    foreach ($files as $file_array) {
        $file = $file_array['file'];
        if ($file == '.' || (isset($file_array['extension']) && $file_array['extension'] != lang_Type_dir) || ($file == '..' && $subdir == '') || in_array($file, $hidden_folders) || ($filter != '' && $file != ".." && strpos($file, $filter) === false)) {
            continue;
        }
        $new_name = fix_filename($file, $transliteration);
        if ($file != '..' && $file != $new_name) {
            //rename
            rename_folder($current_path.$subdir.$new_name, $new_name, $transliteration);
            $file = $new_name;
        }
        //add in thumbs folder if not exist
        if (!file_exists($thumbs_path.$subdir.$file)) {
            create_folder(false, $thumbs_path.$subdir.$file);
        }
        $class_ext = 3;
        if ($file == '..' && trim($subdir) != '') {
            $src = explode("/", $subdir);
            unset($src[count($src) - 2]);
            $src = implode("/", $src);
            if ($src == '') {
                $src = "/";
            }
        } elseif ($file != '..') {
            $src = $subdir.$file."/";
        }

        ?>
		<li data-name="<?php echo Tools::safeOutput($file) ?>" <?php if ($file == '..') {
    echo 'class="back"';
} else {
    echo 'class="dir"';
}
        ?>>
			<figure data-name="<?php echo Tools::safeOutput($file) ?>" class="<?php if ($file == "..") {
    echo "back-";
}
        ?>directory" data-type="<?php if ($file != "..") {
    echo "dir";
}
        ?>">
				<a class="folder-link" href="dialog.php?<?php echo $get_params.rawurlencode($src)."&".uniqid() ?>">
					<div class="img-precontainer">
						<div class="img-container directory"><span></span>
							<img class="directory-img" src="img/<?php echo Tools::safeOutput($icon_theme);
        ?>/folder<?php if ($file == "..") {
    echo "_back";
}
        ?>.jpg" alt="folder"/>
						</div>
					</div>
					<div class="img-precontainer-mini directory">
						<div class="img-container-mini">
							<span></span>
							<img class="directory-img" src="img/<?php echo Tools::safeOutput($icon_theme);
        ?>/folder<?php if ($file == "..") {
    echo "_back";
}
        ?>.png" alt="folder"/>
						</div>
					</div>
					<?php if ($file == "..") {
    ?>
					<div class="box no-effect">
						<h4><?php echo Tools::safeOutput(lang_Back) ?></h4>
					</div>
				</a>

				<?php

} else {
    ?>
					</a>
					<div class="box">
						<h4 class="<?php if ($ellipsis_title_after_first_row) {
    echo "ellipsis";
}
    ?>">
							<a class="folder-link" data-file="<?php echo Tools::safeOutput($file) ?>" href="dialog.php?<?php echo Tools::safeOutput($get_params.rawurlencode($src)."&".uniqid()) ?>"><?php echo Tools::safeOutput($file);
    ?></a>
						</h4>
					</div>
					<input type="hidden" class="name" value=""/>
					<input type="hidden" class="date" value="<?php echo Tools::safeOutput($file_array['date']);
    ?>"/>
					<input type="hidden" class="size" value="<?php echo Tools::safeOutput($file_array['size']);
    ?>"/>
					<input type="hidden" class="extension" value="<?php echo lang_Type_dir;
    ?>"/>
					<div class="file-date"><?php echo date(lang_Date_type, $file_array['date']) ?></div>
					<?php if ($show_folder_size) {
    ?>
					<div class="file-size"><?php echo makeSize($file_array['size']) ?></div><?php
}
    ?>
					<div class='file-extension'><?php echo lang_Type_dir;
    ?></div>
					<figcaption>
						<a href="javascript:void('')" class="tip-left edit-button <?php if ($rename_folders) {
    echo "rename-folder";
}
    ?>" title="<?php echo lang_Rename ?>" data-path="<?php echo Tools::safeOutput($subfolder.$subdir.$file);
    ?>" data-thumb="<?php echo Tools::safeOutput($subdir.$file);
    ?>">
							<i class="icon-pencil <?php if (!$rename_folders) {
    echo 'icon-white';
}
    ?>"></i></a>
						<a href="javascript:void('')" class="tip-left erase-button <?php if ($delete_folders) {
    echo "delete-folder";
}
    ?>" title="<?php echo lang_Erase ?>" data-confirm="<?php echo lang_Confirm_Folder_del;
    ?>" data-path="<?php echo Tools::safeOutput($subfolder.$subdir.$file);
    ?>" data-thumb="<?php echo Tools::safeOutput($subdir.$file);
    ?>">
							<i class="icon-trash <?php if (!$delete_folders) {
    echo 'icon-white';
}
    ?>"></i>
						</a>
					</figcaption>
				<?php
}
        ?>
			</figure>
		</li>
	<?php

    }

    foreach ($files as $nu => $file_array) {
        $file = $file_array['file'];

        if ($file == '.' || $file == '..' || is_dir($current_path.$subfolder.$subdir.$file) || in_array($file, $hidden_files) || !in_array(fix_strtolower($file_array['extension']), $ext) || ($filter != '' && strpos($file, $filter) === false)) {
            continue;
        }

        $file_path = $current_path.$subfolder.$subdir.$file;
    //check if file have illegal caracter

    $filename = substr($file, 0, '-'.(strlen($file_array['extension']) + 1));

        if ($file != fix_filename($file, $transliteration)) {
            $file1 = fix_filename($file, $transliteration);
            $file_path1 = ($current_path.$subfolder.$subdir.$file1);
            if (file_exists($file_path1)) {
                $i = 1;
                $info = pathinfo($file1);
                while (file_exists($current_path.$subfolder.$subdir.$info['filename'].".[".$i."].".$info['extension'])) {
                    $i++;
                }
                $file1 = $info['filename'].".[".$i."].".$info['extension'];
                $file_path1 = ($current_path.$subfolder.$subdir.$file1);
            }

            $filename = substr($file1, 0, '-'.(strlen($file_array['extension']) + 1));
            rename_file($file_path, fix_filename($filename, $transliteration), $transliteration);
            $file = $file1;
            $file_array['extension'] = fix_filename($file_array['extension'], $transliteration);
            $file_path = $file_path1;
        }

        $is_img = false;
        $is_video = false;
        $is_audio = false;
        $show_original = false;
        $show_original_mini = false;
        $mini_src = "";
        $src_thumb = "";
        $extension_lower = fix_strtolower($file_array['extension']);
        if (in_array($extension_lower, $ext_img)) {
            $src = $base_url.$cur_dir.rawurlencode($file);
            $mini_src = $src_thumb = $thumbs_path.$subdir.$file;

        //add in thumbs folder if not exist
        if (!file_exists($src_thumb)) {
            try {
                create_img_gd($file_path, $src_thumb, 122, 91);
                new_thumbnails_creation($current_path.$subfolder.$subdir, $file_path, $file, $current_path, $relative_image_creation, $relative_path_from_current_pos, $relative_image_creation_name_to_prepend, $relative_image_creation_name_to_append, $relative_image_creation_width, $relative_image_creation_height, $fixed_image_creation, $fixed_path_from_filemanager, $fixed_image_creation_name_to_prepend, $fixed_image_creation_to_append, $fixed_image_creation_width, $fixed_image_creation_height);
            } catch (Exception $e) {
                $src_thumb = $mini_src = "";
            }
        }
            $is_img = true;
        //check if is smaller than thumb
        list($img_width, $img_height, $img_type, $attr) = getimagesize($file_path);
            if ($img_width < 122 && $img_height < 91) {
                $src_thumb = $current_path.$subfolder.$subdir.$file;
                $show_original = true;
            }

            if ($img_width < 45 && $img_height < 38) {
                $mini_src = $current_path.$subfolder.$subdir.$file;
                $show_original_mini = true;
            }
        }

        $is_icon_thumb = false;
        $is_icon_thumb_mini = false;
        $no_thumb = false;
        if ($src_thumb == "") {
            $no_thumb = true;
            if (file_exists('img/'.$icon_theme.'/'.$extension_lower.".jpg")) {
                $src_thumb = 'img/'.$icon_theme.'/'.$extension_lower.".jpg";
            } else {
                $src_thumb = "img/".$icon_theme."/default.jpg";
            }
            $is_icon_thumb = true;
        }
        if ($mini_src == "") {
            $is_icon_thumb_mini = false;
        }

        $class_ext = 0;
        if (in_array($extension_lower, $ext_video)) {
            $class_ext = 4;
            $is_video = true;
        } elseif (in_array($extension_lower, $ext_img)) {
            $class_ext = 2;
        } elseif (in_array($extension_lower, $ext_music)) {
            $class_ext = 5;
            $is_audio = true;
        } elseif (in_array($extension_lower, $ext_misc)) {
            $class_ext = 3;
        } else {
            $class_ext = 1;
        }

    /* PrestaShop */
    if (isset($src_thumb) && $src_thumb) {
        if (($src_thumb = preg_replace('#('.addslashes($current_path).')#ism', Tools::safeOutput(Context::getContext()->shop->physical_uri.'img/cms/'), $src_thumb)) == $src_thumb) {
            $src_thumb = preg_replace('#('.addslashes($thumbs_base_path).')#ism', Tools::safeOutput(Context::getContext()->shop->physical_uri.'img/tmp/cms/'), $src_thumb);
        }
    }
        if (isset($mini_src) && $mini_src) {
            if (($mini_src = preg_replace('#('.addslashes($current_path).')#ism', Tools::safeOutput(Context::getContext()->shop->physical_uri.'img/cms/'), $mini_src)) == $mini_src) {
                $mini_src = preg_replace('#('.addslashes($thumbs_base_path).')#ism', Tools::safeOutput(Context::getContext()->shop->physical_uri.'img/tmp/cms/'), $mini_src);
            }
        }
    /* END PrestaShop */

    if ((!(Tools::getValue('type') == 1 && !$is_img) && !((Tools::getValue('type') == 3 && !$is_video) && (Tools::getValue('type') == 3 && !$is_audio))) && $class_ext > 0) {
        ?>
	<li class="ff-item-type-<?php echo Tools::safeOutput($class_ext);
        ?> file" data-name="<?php echo Tools::safeOutput($file);
        ?>">
		<figure data-name="<?php echo Tools::safeOutput($file) ?>" data-type="<?php if ($is_img) {
    echo "img";
} else {
    echo "file";
}
        ?>">
			<a href="javascript:void('')" class="link" data-file="<?php echo Tools::safeOutput($file);
        ?>" data-field_id="" data-function="<?php echo Tools::safeOutput($apply);
        ?>">
				<div class="img-precontainer">
					<?php if ($is_icon_thumb) {
    ?>
						<div class="filetype"><?php echo $extension_lower ?></div><?php
}
        ?>
					<div class="img-container">
						<span></span>
						<img alt="<?php echo Tools::safeOutput($filename." thumbnails");
        ?>" class="<?php echo $show_original ? "original" : "" ?> <?php echo $is_icon_thumb ? "icon" : "" ?>" src="<?php echo Tools::safeOutput($src_thumb);
        ?>">
					</div>
				</div>
				<div class="img-precontainer-mini <?php if ($is_img) {
    echo 'original-thumb';
}
        ?>">
					<div class="filetype <?php echo $extension_lower ?> <?php if (!$is_icon_thumb) {
    echo "hide";
}
        ?>"><?php echo $extension_lower ?></div>
					<div class="img-container-mini">
						<span></span>
						<?php if ($mini_src != "") {
    ?>
							<img alt="<?php echo Tools::safeOutput($filename." thumbnails");
    ?>" class="<?php echo $show_original_mini ? "original" : "" ?> <?php echo $is_icon_thumb_mini ? "icon" : "" ?>" src="<?php echo Tools::safeOutput($mini_src);
    ?>">
						<?php
}
        ?>
					</div>
				</div>
				<?php if ($is_icon_thumb) {
    ?>
					<div class="cover"></div>
				<?php
}
        ?>
			</a>

			<div class="box">
				<h4 class="<?php if ($ellipsis_title_after_first_row) {
    echo "ellipsis";
}
        ?>">
					<a href="javascript:void('')" class="link" data-file="<?php echo Tools::safeOutput($file);
        ?>" data-field_id="" data-function="<?php echo Tools::safeOutput($apply);
        ?>">
						<?php echo Tools::safeOutput($filename);
        ?></a></h4>
			</div>
			<input type="hidden" class="date" value="<?php echo $file_array['date'];
        ?>"/>
			<input type="hidden" class="size" value="<?php echo $file_array['size'] ?>"/>
			<input type="hidden" class="extension" value="<?php echo $extension_lower;
        ?>"/>
			<input type="hidden" class="name" value=""/>

			<div class="file-date"><?php echo date(lang_Date_type, $file_array['date']) ?></div>
			<div class="file-size"><?php echo makeSize($file_array['size']) ?></div>
			<div class='img-dimension'><?php if ($is_img) {
    echo $img_width."x".$img_height;
}
        ?></div>
			<div class='file-extension'><?php echo Tools::safeOutput($extension_lower);
        ?></div>
			<figcaption>
				<form action="force_download.php" method="post" class="download-form" id="form<?php echo Tools::safeOutput($nu);
        ?>">
					<input type="hidden" name="path" value="<?php echo Tools::safeOutput($subfolder.$subdir) ?>"/>
					<input type="hidden" class="name_download" name="name" value="<?php echo Tools::safeOutput($file) ?>"/>

					<a title="<?php echo lang_Download ?>" class="tip-right" href="javascript:void('')" onclick="$('#form<?php echo Tools::safeOutput($nu);
        ?>').submit();"><i class="icon-download"></i></a>
					<?php if ($is_img && $src_thumb != "") {
    ?>
						<a class="tip-right preview" title="<?php echo lang_Preview ?>" data-url="<?php echo Tools::safeOutput($src);
    ?>" data-toggle="lightbox" href="#previewLightbox"><i class=" icon-eye-open"></i></a>
					<?php

} elseif (($is_video || $is_audio) && in_array($extension_lower, $jplayer_ext)) {
    ?>
						<a class="tip-right modalAV <?php if ($is_audio) {
    echo "audio";
} else {
    echo "video";
}
    ?>"
						   title="<?php echo lang_Preview ?>" data-url="ajax_calls.php?action=media_preview&title=<?php echo Tools::safeOutput($filename);
    ?>&file=<?php echo Tools::safeOutput(Context::getContext()->shop->physical_uri.'img/cms/'.$subfolder.$subdir.$file);
    ?>"
						   href="javascript:void('');"><i class=" icon-eye-open"></i></a>
					<?php

} else {
    ?>
						<a class="preview disabled"><i class="icon-eye-open icon-white"></i></a>
					<?php
}
        ?>
					<a href="javascript:void('')" class="tip-left edit-button <?php if ($rename_files) {
    echo "rename-file";
}
        ?>" title="<?php echo lang_Rename ?>" data-path="<?php echo Tools::safeOutput($subfolder.$subdir.$file);
        ?>" data-thumb="<?php echo Tools::safeOutput($subdir.$file);
        ?>">
						<i class="icon-pencil <?php if (!$rename_files) {
    echo 'icon-white';
}
        ?>"></i></a>

					<a href="javascript:void('')" class="tip-left erase-button <?php if ($delete_files) {
    echo "delete-file";
}
        ?>" title="<?php echo lang_Erase ?>" data-confirm="<?php echo lang_Confirm_del;
        ?>" data-path="<?php echo Tools::safeOutput($subfolder.$subdir.$file);
        ?>" data-thumb="<?php echo Tools::safeOutput($subdir.$file);
        ?>">
						<i class="icon-trash <?php if (!$delete_files) {
    echo 'icon-white';
}
        ?>"></i>
					</a>
				</form>
			</figcaption>
		</figure>
	</li>
	<?php

    }
    }

    ?></div>
	</ul>
	<?php
}
    ?>
	</div>
	</div>
	</div>

	<!----- lightbox div start ------->
	<div id="previewLightbox" class="lightbox hide fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class='lightbox-content'>
			<img id="full-img" src="">
		</div>
	</div>
	<!----- lightbox div end ------->

	<!----- loading div start ------->
	<div id="loading_container" style="display:none;">
		<div id="loading" style="background-color:#000; position:fixed; width:100%; height:100%; top:0px; left:0px;z-index:100000"></div>
		<img id="loading_animation" src="img/storing_animation.gif" alt="loading" style="z-index:10001; margin-left:-32px; margin-top:-32px; position:fixed; left:50%; top:50%"/>
	</div>
	<!----- loading div end ------->

	<!----- player div start ------->
	<div class="modal hide fade" id="previewAV">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3><?php echo lang_Preview;
    ?></h3>
		</div>
		<div class="modal-body">
			<div class="row-fluid body-preview">
			</div>
		</div>

	</div>
	<!----- player div end ------->
	<img id='aviary_img' src='' class="hide"/>
	</body>
	</html>
<?php
} ?>
