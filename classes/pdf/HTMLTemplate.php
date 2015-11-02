<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @since 1.5
 */
abstract class HTMLTemplateCore
{
	public $title;
	public $date;
	public $available_in_your_account = true;

	/** @var Smarty */
	public $smarty;

	/** @var Shop */
	public $shop;

	/**
	 * Returns the template's HTML header
	 *
	 * @return string HTML header
	 */
	public function getHeader()
	{
		$this->assignCommonHeaderData();

		return $this->smarty->fetch($this->getTemplate('header'));
	}

	/**
	 * Returns the template's HTML footer
	 *
	 * @return string HTML footer
	 */
	public function getFooter()
	{
		$shop_address = $this->getShopAddress();

		$id_shop = (int)$this->shop->id;

		$this->smarty->assign(array(
			'available_in_your_account' => $this->available_in_your_account,
			'shop_address' => $shop_address,
			'shop_fax' => Configuration::get('PS_SHOP_FAX', null, null, $id_shop),
			'shop_phone' => Configuration::get('PS_SHOP_PHONE', null, null, $id_shop),
			'shop_email' => Configuration::get('PS_SHOP_EMAIL', null, null, $id_shop),
			'free_text' => Configuration::get('PS_INVOICE_FREE_TEXT', (int)Context::getContext()->language->id, null, $id_shop)
		));

		return $this->smarty->fetch($this->getTemplate('footer'));
	}

	/**
	 * Returns the shop address
	 *
	 * @return string
	 */
	protected function getShopAddress()
	{
		$shop_address = '';

		$shop_address_obj = $this->shop->getAddress();
		if (isset($shop_address_obj) && $shop_address_obj instanceof Address)
			$shop_address = AddressFormat::generateAddress($shop_address_obj, array(), ' - ', ' ');

		return $shop_address;
	}

	/**
	 * Returns the invoice logo
	 */
	protected function getLogo()
	{
		$logo = '';

		$id_shop = (int)$this->shop->id;

		if (Configuration::get('PS_LOGO_INVOICE', null, null, $id_shop) != false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE', null, null, $id_shop)))
			$logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE', null, null, $id_shop);
		elseif (Configuration::get('PS_LOGO', null, null, $id_shop) != false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $id_shop)))
			$logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $id_shop);
		return $logo;
	}

	/**
	 * Assign common header data to smarty variables
	 */

	public function assignCommonHeaderData()
	{
		$this->setShopId();
		$id_shop = (int)$this->shop->id;
		$shop_name = Configuration::get('PS_SHOP_NAME', null, null, $id_shop);

		$path_logo = $this->getLogo();

		$width = 0;
		$height = 0;
		if (!empty($path_logo))
			list($width, $height) = getimagesize($path_logo);

		// Limit the height of the logo for the PDF render
		$maximum_height = 100;
		if ($height > $maximum_height)
		{
			$ratio = $maximum_height / $height;
			$height *= $ratio;
			$width *= $ratio;
		}

		$this->smarty->assign(array(
			'logo_path' => $path_logo,
			'img_ps_dir' => 'http://'.Tools::getMediaServer(_PS_IMG_)._PS_IMG_,
			'img_update_time' => Configuration::get('PS_IMG_UPDATE_TIME'),
			'date' => $this->date,
			'title' => $this->title,
			'shop_name' => $shop_name,
			'shop_details' => Configuration::get('PS_SHOP_DETAILS', null, null, (int)$id_shop),
			'width_logo' => $width,
			'height_logo' => $height
		));
	}

	/**
	 * Assign hook data
	 *
	 * @param ObjectModel $object generally the object used in the constructor
	 */
	public function assignHookData($object)
	{
		$template = ucfirst(str_replace('HTMLTemplate', '', get_class($this)));
		$hook_name = 'displayPDF'.$template;

		$this->smarty->assign(array(
			'HOOK_DISPLAY_PDF' => Hook::exec($hook_name, array('object' => $object))
		));
	}

	/**
	 * Returns the template's HTML content
	 *
	 * @return string HTML content
	 */
	abstract public function getContent();


	/**
	 * Returns the template filename
	 *
	 * @return string filename
	 */
	abstract public function getFilename();

	/**
	 * Returns the template filename when using bulk rendering
	 *
	 * @return string filename
	 */
	abstract public function getBulkFilename();

	/**
	 * If the template is not present in the theme directory, it will return the default template
	 * in _PS_PDF_DIR_ directory
	 *
	 * @param $template_name
	 *
	 * @return string
	 */
	protected function getTemplate($template_name)
	{
		$template = false;
		$default_template = rtrim(_PS_PDF_DIR_, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$template_name.'.tpl';
		$overridden_template = _PS_ALL_THEMES_DIR_.$this->shop->getTheme().DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.$template_name.'.tpl';
		if (file_exists($overridden_template))
			$template = $overridden_template;
		elseif (file_exists($default_template))
			$template = $default_template;

		return $template;
	}


	/**
	 * Translation method
	 *
	 * @param string $string
	 *
	 * @return string translated text
	 */
	protected static function l($string)
	{
		return Translate::getPdfTranslation($string);
	}

	protected function setShopId()
	{
		if (isset($this->order) && Validate::isLoadedObject($this->order))
			$id_shop = (int)$this->order->id_shop;
		else
			$id_shop = (int)Context::getContext()->shop->id;

		$this->shop = new Shop($id_shop);
		if (Validate::isLoadedObject($this->shop))
			Shop::setContext(Shop::CONTEXT_SHOP, (int)$this->shop->id);
	}
}
