<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.6.0
 */
class AdminBlockCategoriesController extends ModuleAdminController
{
	public function postProcess()
	{
		if (($id_thumb = Tools::getValue('deleteThumb', false)) !== false)
		{
			if (file_exists(_PS_CAT_IMG_DIR_.(int)Tools::getValue('id_category').'-'.(int)$id_thumb.'_thumb.jpg')
				&& !unlink(_PS_CAT_IMG_DIR_.(int)Tools::getValue('id_category').'-'.(int)$id_thumb.'_thumb.jpg'))
				$this->context->controller->errors[] = Tools::displayError('Error while delete');

			if (empty($this->context->controller->errors))
				Tools::clearSmartyCache();

			Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminCategories').'&id_category='
				.(int)Tools::getValue('id_category').'&updatecategory');
		}

		parent::postProcess();
	}

	public function ajaxProcessuploadThumbnailImages()
	{
		$category = new Category((int)Tools::getValue('id_category'));

		if (isset($_FILES['thumbnail']))
		{
			//Get total of image already present in directory
			$files = scandir(_PS_CAT_IMG_DIR_);
			$assigned_keys = array();
			$allowed_keys  = array(0, 1, 2);

			foreach ($files as $file) {
				$matches = array();

				if (preg_match('/^'.$category->id.'-([0-9])?_thumb.jpg/i', $file, $matches) === 1)
					$assigned_keys[] = (int)$matches[1];
			}

			$available_keys = array_diff($allowed_keys, $assigned_keys);
			$helper = new HelperImageUploader('thumbnail');
			$files  = $helper->process();
			$total_errors = array();

			if (count($available_keys) < count($files))
			{
				$total_errors['name'] = sprintf(Tools::displayError('An error occurred while uploading the image :'));
				$total_errors['error'] = sprintf(Tools::displayError('You cannot upload more files'));
				die(json_encode(array('thumbnail' => array($total_errors))));
			}

			foreach ($files as $key => &$file)
			{
				$id = array_shift($available_keys);
				$errors = array();
				// Evaluate the memory required to resize the image: if it's too much, you can't resize it.
				if (isset($file['save_path']) && !ImageManager::checkImageMemoryLimit($file['save_path']))
					$errors[] = Tools::displayError('Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings. ');
				// Copy new image
				if (!isset($file['save_path']) || (empty($errors) && !ImageManager::resize($file['save_path'], _PS_CAT_IMG_DIR_
					.(int)Tools::getValue('id_category').'-'.$id.'_thumb.jpg')))
					$errors[] = Tools::displayError('An error occurred while uploading the image.');

				if (count($errors))
					$total_errors = array_merge($total_errors, $errors);

				if (isset($file['save_path']) && is_file($file['save_path']))
					unlink($file['save_path']);
				//Necesary to prevent hacking
				if (isset($file['save_path']))
					unset($file['save_path']);

				if (isset($file['tmp_name']))
					unset($file['tmp_name']);

				//Add image preview and delete url
				$file['image'] = ImageManager::thumbnail(_PS_CAT_IMG_DIR_.(int)$category->id.'-'.$id.'_thumb.jpg',
					$this->context->controller->table.'_'.(int)$category->id.'-'.$id.'_thumb.jpg', 100, 'jpg', true, true);
				$file['delete_url'] = Context::getContext()->link->getAdminLink('AdminBlockCategories').'&deleteThumb='
					.$id.'&id_category='.(int)$category->id.'&updatecategory';
			}

			if (count($total_errors))
				$this->context->controller->errors = array_merge($this->context->controller->errors, $total_errors);
			else
				Tools::clearSmartyCache();

			die(json_encode(array('thumbnail' => $files)));
		}
	}
}
