<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/classes/QgsHelper.php';

class Qlogsitemap extends Module
{
    protected $pagesBefore = array(
        'index' => 'home',
        'our-properties' => 'meta',
    );

    protected $pagesAfter = array(
        'contact' => 'contact',
        'authentication' => 'login',
    );

    protected $priorities = array(
        'home' => 1.0,
        'meta' => 0.8,
        'contact' => 0.2,
        'category' => 0.6,
        'roomType' => 0.4,
        'cms' => 0.2,
        'login' => 0.2,
    );
    protected $fixedFrequencies = array(
        'cms' => 'monthly',
        'login' => 'yearly',
    );

    protected $fileHandle = null;
    protected $fileUrlCount = 0;
    protected $fileIndex = 0;
    protected $currentIso = '';

    public function __construct()
    {
        $this->name = 'qlogsitemap';
        $this->tab = 'seo';
        $this->version = '1.0.0';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6');
        $this->qloapps_versions_compliancy = array('min' => '1.7.0', 'max' => _QLOAPPS_VERSION_);
        $this->author = 'Webkul';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Sitemap');
        $this->description = $this->l('Generates XML sitemaps for your hotel website and keeps them up to date. Compatible with all major search engines.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayAdminMetaOptions')
            && $this->registerHook('registerCronTasks');
    }

    public function uninstall()
    {
        QgsHelper::deleteSitemapFiles();

        Configuration::deleteByName('QLOGSITEMAP_LAST_EXPORT');

        return parent::uninstall();
    }

    public function hookRegisterCronTasks()
    {
        return array(
            array(
                'name' => 'generate_sitemap',
                'description' => $this->l('Regenerate the XML sitemaps'),
                'cron' => '0 0 * * 0',
                'callback' => 'cronGenerateSitemap',
            ),
        );
    }

    public function cronGenerateSitemap()
    {
        if (!$this->createSitemap()) {
            throw new Exception('Unable to generate the sitemaps.');
        }
    }

    public function hookDisplayAdminMetaOptions()
    {
        if ($this->context->controller) {
            if (Tools::isSubmit('submitQloGsitemap')) {
                if ($this->createSitemap()) {
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminMeta').'&qgsGenerated=1');
                } elseif (!count($this->context->controller->errors)) {
                    $this->context->controller->errors[] = $this->l('Unable to generate the sitemaps.');
                }
            } elseif (Tools::getValue('qgsGenerated')) {
                $this->context->controller->confirmations[] = $this->l('Your sitemaps were successfully created.');
            }
        }

        $this->context->smarty->assign(array(
            'qgs_index_url' => $this->context->link->getBaseLink().QgsHelper::QGS_INDEX_FILE_NAME,
            'qgs_store_url' => $this->context->link->getBaseLink(),
            'qgs_sitemap_links' => QgsHelper::getSitemapFiles(),
            'qgs_last_export' => Configuration::get('QLOGSITEMAP_LAST_EXPORT'),
            'qgs_cron_manager_enabled' => Module::isEnabled('qlocrontaskmanager'),
            'qgs_cron_manager_installed' => Module::isInstalled('qlocrontaskmanager'),
            'qgs_cron_manager_link' => $this->getCronManagerLink(),
        ));

        return $this->display(__FILE__, 'views/templates/hook/sitemap-panel.tpl');
    }

    protected function getCronManagerLink()
    {
        $link = $this->context->link;
        if (Module::isEnabled('qlocrontaskmanager')) {
            return $link->getAdminLink('AdminCronTaskManager');
        }

        $module = Module::getInstanceByName('qlocrontaskmanager');
        if (!$module) {
            return '';
        }

        return $link->getAdminLink('AdminModules').'&module_name='.$module->name.'&tab_module='.$module->tab;
    }

    public function createSitemap()
    {
        if (!is_writable(_PS_ROOT_DIR_)) {
            if ($this->context->controller) {
                $this->context->controller->errors[] = $this->l('Unable to write in the root directory. Please adjust the permissions to allow this module to create the sitemap files.');
            }

            return false;
        }

        QgsHelper::deleteSitemapFiles();

        foreach (Language::getLanguages(true, (int) $this->context->shop->id) as $lang) {
            $this->startSitemapSet($lang['iso_code']);
            $this->addPageUrls($lang, $this->pagesBefore);
            $this->addCategoryUrls($lang);
            $this->addRoomTypeUrls($lang);
            $this->addCmsUrls($lang);
            $this->addPageUrls($lang, $this->pagesAfter);
            $this->closeSitemapFile();
        }

        $result = QgsHelper::createIndexFile();
        if ($result) {
            Configuration::updateValue('QLOGSITEMAP_LAST_EXPORT', date('Y-m-d H:i:s'));
        }

        return $result;
    }

    protected function startSitemapSet($iso)
    {
        $this->closeSitemapFile();
        $this->currentIso = $iso;
        $this->fileIndex = 0;
    }

    protected function openSitemapFile()
    {
        $fileName = $this->currentIso.'_'.$this->fileIndex.'_sitemap.xml';
        $this->fileHandle = @fopen(_PS_ROOT_DIR_.'/'.$fileName, 'wb');
        if (!$this->fileHandle) {
            return false;
        }

        fwrite($this->fileHandle, '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL.
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL);
        ++$this->fileIndex;
        $this->fileUrlCount = 0;

        return true;
    }

    protected function closeSitemapFile()
    {
        if ($this->fileHandle) {
            fwrite($this->fileHandle, '</urlset>'.PHP_EOL);
            fclose($this->fileHandle);
            $this->fileHandle = null;
        }
    }

    protected function addUrl($loc, $type, $lastMod = null)
    {
        if (!$loc) {
            return false;
        }
        if ($this->fileHandle && $this->fileUrlCount >= QgsHelper::QGS_MAX_URLS_PER_FILE) {
            $this->closeSitemapFile();
        }
        if (!$this->fileHandle && !$this->openSitemapFile()) {
            return false;
        }

        $priority = $this->priorities[$type];
        $frequency = isset($this->fixedFrequencies[$type]) ? $this->fixedFrequencies[$type] : QgsHelper::QGS_DEFAULT_FREQUENCY;
        $xml = '<url>'.PHP_EOL.'<loc>'.QgsHelper::escapeXml($loc).'</loc>'.PHP_EOL;
        if ($lastMod && strtotime($lastMod) > 0) {
            $xml .= '<lastmod>'.date('Y-m-d', strtotime($lastMod)).'</lastmod>'.PHP_EOL;
        }
        $xml .= '<changefreq>'.QgsHelper::escapeXml($frequency).'</changefreq>'.PHP_EOL.
            '<priority>'.number_format($priority, 1, '.', '').'</priority>'.PHP_EOL;
        $xml .= '</url>'.PHP_EOL;

        fwrite($this->fileHandle, $xml);
        ++$this->fileUrlCount;

        return true;
    }

    protected function addPageUrls($lang, array $pages)
    {
        foreach ($pages as $page => $type) {
            $this->addUrl($this->context->link->getPageLink($page, null, (int) $lang['id_lang']), $type);
        }
    }

    protected function addRoomTypeUrls($lang)
    {
        $idLang = (int) $lang['id_lang'];
        $idShop = (int) $this->context->shop->id;

        foreach (QgsHelper::getRoomTypeIds($idShop) as $row) {
            $product = new Product((int) $row['id_product'], false, $idLang);
            $this->addUrl(
                $this->context->link->getProductLink($product, $product->link_rewrite, null, null, $idLang, $idShop),
                'roomType',
                $product->date_upd
            );
        }
    }

    protected function addCategoryUrls($lang)
    {
        $idLang = (int) $lang['id_lang'];
        $idShop = (int) $this->context->shop->id;

        foreach (QgsHelper::getCategoryIds($idShop) as $row) {
            $category = new Category((int) $row['id_category'], $idLang);
            $this->addUrl(
                $this->context->link->getCategoryLink($category, $category->link_rewrite, $idLang, null, $idShop),
                'category'
            );
        }
    }

    protected function addCmsUrls($lang)
    {
        $idLang = (int) $lang['id_lang'];
        $idShop = (int) $this->context->shop->id;

        foreach (QgsHelper::getCmsIds($idLang, $idShop) as $row) {
            $cms = new CMS((int) $row['id_cms'], $idLang);
            $this->addUrl($this->context->link->getCMSLink($cms, null, null, $idLang, $idShop), 'cms');
        }
    }
}
