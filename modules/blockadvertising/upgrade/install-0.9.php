<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_0_9($module)
{
	$module_path = $module->getLocalPath();
	$img_folder_path = $module->getLocalPath().'img';
	$fixture_img_path = $module->getLocalPath().'img'.DIRECTORY_SEPARATOR.'fixtures';

	if (!Tools::file_exists_cache($img_folder_path))
		mkdir($img_folder_path);

	if (!Tools::file_exists_cache($fixture_img_path))
		mkdir($fixture_img_path);

	$files = scandir($module->getLocalPath());

	foreach ($files as $file)
	{
		if (strncmp($file, 'advertising', 11) == 0)
		{
			if ($file == 'advertising.jpg')
				copy($module_path.$file, $fixture_img_path.DIRECTORY_SEPARATOR.$file);
			else
				copy($module_path.$file, $img_folder_path.DIRECTORY_SEPARATOR.$file);

			unlink($module_path.$file);
		}
	}

	Tools::clearCache(Context::getContext()->smarty, $module->getTemplatePath('blockadvertising.tpl'));

	return true;
}
