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

/**
 * Generates XML sitemaps (one set per language) plus a sitemap index file.
 */
class Qlogsitemap extends Module
{
    /**
     * Front pages listed before the categories and room types (page name => URL type).
     *
     * @var array
     */
    protected $pagesBefore = array(
        'index' => 'home',
        'our-properties' => 'meta',
    );

    protected $pagesAfter = array(
        'contact' => 'contact',
        'authentication' => 'login',
        'sitemap' => 'sitemapPage',
    );

    protected $priorities = array(
        'home' => 1.0,
        'meta' => 0.8,
        'contact' => 0.2,
        'category' => 0.6,
        'roomType' => 0.4,
        'standalone' => 0.4,
        'cms' => 0.2,
        'login' => 0.2,
        'modulePage' => 0.4,
        'sitemapPage' => 0.2,
    );

    protected $cronOptions = array(
        'weekly' => '0 0 * * 0',
        'monthly' => '0 0 1 * *',
        'yearly' => '0 0 1 1 *',
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

        $this->displayName = $this->l('QloApps Sitemap Generator');
        $this->description = $this->l('Generates XML sitemaps for your hotel website and keeps them up to date. Compatible with all major search engines.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerHook('displayAdminMetaOptions')
            || !$this->registerHook('registerCronTasks')
        ) {
            return false;
        }
        $this->sitemapPageMeta();
        return true;
    }

    public function uninstall()
    {
        QgsHelper::deleteSitemapFiles();

        Configuration::deleteByName('QLOGSITEMAP_LAST_EXPORT');
        Configuration::deleteByName('QLOGSITEMAP_FREQUENCY');
        Configuration::deleteByName('QLOGSITEMAP_DISABLED_PAGES');

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

        $this->assignSitemapPanelVars();

        return $this->display(__FILE__, 'views/templates/hook/sitemap-panel.tpl');
    }
    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitQloGsitemap')) {
            $saved = true;
            if (Tools::getIsset('QLOGSITEMAP_FREQUENCY')) {
                $configOutput = $this->postProcessConfig();
                $saved = !strpos($configOutput, 'alert-danger');
                $output .= $configOutput;
            }
            if ($saved && $this->createSitemap()) {
                $output .= $this->displayConfirmation($this->l('Your sitemaps were successfully created.'));
            } elseif ($saved) {
                $output .= $this->displayError($this->l('Unable to generate the sitemaps.'));
            }
        } elseif (Tools::isSubmit('submitQloGsitemapConfig')) {
            $output .= $this->postProcessConfig();
        } elseif (Tools::isSubmit('submitQloGsitemapCron')) {
            $output .= $this->postProcessCron();
        }

        $this->assignSitemapPanelVars();
        $cronLabels = array(
            'weekly' => $this->l('Weekly'),
            'monthly' => $this->l('Monthly'),
            'yearly' => $this->l('Yearly'),
            'never' => $this->l('Never'),
        );
        $frequencies = array();
        foreach (QgsHelper::getFrequencies() as $frequency) {
            $frequencies[$frequency] = $this->getFrequencyLabel($frequency);
        }

        $disabledPages = QgsHelper::getDisabledPages();
        $pages = array('core' => array(), 'module' => array());
        $idLang = (int) $this->context->language->id;
        foreach (array_keys($this->getExcludablePages()) as $page) {
            $meta = Meta::getMetaByPage($page, $idLang);
            $pages['core'][] = array(
                'page' => $page,
                'title' => !empty($meta['title']) ? $meta['title'] : $page,
                'show_key' => true,
                'excluded' => in_array($page, $disabledPages),
            );
        }
        foreach (QgsHelper::getModulePages() as $page) {
            $meta = Meta::getMetaByPage($page, $idLang);
            $pages['module'][] = array(
                'page' => $page,
                'title' => !empty($meta['title']) ? $meta['title'] : $page,
                'show_key' => true,
                'excluded' => in_array($page, $disabledPages),
            );
        }
        foreach ($this->getExcludableTypes() as $type => $title) {
            $pages['core'][] = array(
                'page' => $type,
                'title' => $title,
                'show_key' => false,
                'excluded' => in_array($type, $disabledPages),
            );
        }

        $this->context->smarty->assign(array(
            'qgs_form_action' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'qgs_cron_options' => $cronLabels,
            'qgs_frequencies' => $frequencies,
            'qgs_frequency' => QgsHelper::getFrequency(),
            'qgs_pages' => $pages,
        ));

        return $output.$this->display(__FILE__, 'views/templates/admin/config.tpl');
    }

    protected function getExcludablePages()
    {
        return array_diff_key(array_merge($this->pagesBefore, $this->pagesAfter), array('index' => true));
    }

    protected function getExcludableTypes()
    {
        $types = array(
            QgsHelper::TYPE_ROOM_TYPES => $this->l('Room types'),
            QgsHelper::TYPE_PROPERTIES => $this->l('Properties'),
        );
        if (QgsHelper::getStandaloneProductIds((int) $this->context->shop->id)) {
            $types[QgsHelper::TYPE_STANDALONE] = $this->l('Standalone products');
        }
        if (QgsHelper::getCmsIds((int) $this->context->language->id, (int) $this->context->shop->id)) {
            $types[QgsHelper::TYPE_CMS] = $this->l('CMS pages');
        }

        return $types;
    }

    protected function postProcessConfig()
    {
        $frequency = Tools::getValue('QLOGSITEMAP_FREQUENCY');
        if (!in_array($frequency, QgsHelper::getFrequencies(), true)) {
            return $this->displayError($this->l('Invalid update frequency.'));
        }

        $knownPages = array_merge(array_keys($this->getExcludablePages()), array_keys($this->getExcludableTypes()), QgsHelper::getModulePages());
        $disabledPages = array_values(array_intersect($knownPages, (array) Tools::getValue('QLOGSITEMAP_DISABLED_PAGES', array())));

        Configuration::updateValue('QLOGSITEMAP_FREQUENCY', $frequency);
        Configuration::updateValue('QLOGSITEMAP_DISABLED_PAGES', implode(',', $disabledPages));

        return $this->displayConfirmation($this->l('Settings updated successfully. Generate the sitemap to apply them.'));
    }

    protected function postProcessCron()
    {
        $option = Tools::getValue('QLOGSITEMAP_CRON');
        if ($option !== 'never' && !isset($this->cronOptions[$option])) {
            return $this->displayError($this->l('Invalid cron schedule.'));
        }

        $cronTask = $this->getCronTask();
        if (!$cronTask) {
            return $this->displayError($this->l('The sitemap cron task was not found in the Cron Task Manager.'));
        }

        if ($option === 'never') {
            $cronTask->active = 0;
        } else {
            $cronTask->cron_expression = $this->cronOptions[$option];
            $cronTask->active = 1;
        }

        if (!$cronTask->save()) {
            return $this->displayError($this->l('Unable to update the cron task.'));
        }

        return $this->displayConfirmation($this->l('Cron schedule updated successfully.'));
    }

    protected function getFrequencyLabel($frequency)
    {
        $labels = array(
            'always' => $this->l('always'),
            'hourly' => $this->l('hourly'),
            'daily' => $this->l('daily'),
            'weekly' => $this->l('weekly'),
            'monthly' => $this->l('monthly'),
            'yearly' => $this->l('yearly'),
            'never' => $this->l('never'),
        );

        return $labels[$frequency];
    }

    protected function assignSitemapPanelVars()
    {
        $this->context->smarty->assign(array(
            'qgs_template_dir' => _PS_MODULE_DIR_.$this->name.'/views/templates/',
            'qgs_config_link' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name,
            'qgs_index_url' => $this->context->link->getBaseLink().QgsHelper::QGS_INDEX_FILE_NAME,
            'qgs_store_url' => $this->context->link->getBaseLink(),
            'qgs_sitemap_links' => QgsHelper::getSitemapFiles(),
            'qgs_last_export' => Configuration::get('QLOGSITEMAP_LAST_EXPORT'),
            'qgs_cron_manager_enabled' => Module::isEnabled('qlocrontaskmanager'),
            'qgs_cron_manager_installed' => Module::isInstalled('qlocrontaskmanager'),
            'qgs_cron_manager_link' => $this->getCronManagerLink(),
            'qgs_cron_task' => $this->getCronTaskSchedule(),
        ));
    }

    protected function getCronTask()
    {
        if (!Module::isEnabled('qlocrontaskmanager')) {
            return false;
        }

        require_once _PS_MODULE_DIR_.'qlocrontaskmanager/classes/QctmRequiredClasses.php';

        $idTask = QctmCronTask::getByModuleAndName((int) $this->id, 'generate_sitemap');
        if (!$idTask) {
            return false;
        }

        $cronTask = new QctmCronTask((int) $idTask);

        return Validate::isLoadedObject($cronTask) ? $cronTask : false;
    }

    protected function getCronTaskSchedule()
    {
        $cronTask = $this->getCronTask();
        if (!$cronTask) {
            return false;
        }

        $readable = $cronTask->cron_expression;
        if (\Cron\CronExpression::isValidExpression($cronTask->cron_expression)) {
            $readable = (new QctmCronExpressionTranslator())->getCronExpressionTranslation($cronTask->cron_expression);
        }

        $option = '';
        if (!$cronTask->active) {
            $option = 'never';
        } elseif (($found = array_search($cronTask->cron_expression, $this->cronOptions, true)) !== false) {
            $option = $found;
        }

        return array(
            'active' => (bool) $cronTask->active,
            'expression' => $cronTask->cron_expression,
            'readable' => $readable,
            'option' => $option,
        );
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

        $disabledPages = QgsHelper::getDisabledPages();
        foreach (Language::getLanguages(true, (int) $this->context->shop->id) as $lang) {
            $this->startSitemapSet($lang['iso_code']);
            $this->addPageUrls($lang, $this->pagesBefore);
            if (!in_array(QgsHelper::TYPE_PROPERTIES, $disabledPages)) {
                $this->addCategoryUrls($lang);
            }
            if (!in_array(QgsHelper::TYPE_ROOM_TYPES, $disabledPages)) {
                $this->addRoomTypeUrls($lang);
            }
            if (!in_array(QgsHelper::TYPE_STANDALONE, $disabledPages)) {
                $this->addStandaloneProductUrls($lang);
            }
            if (!in_array(QgsHelper::TYPE_CMS, $disabledPages)) {
                $this->addCmsUrls($lang);
            }
            $this->addPageUrls($lang, $this->pagesAfter);
            $this->addPageUrls($lang, array_fill_keys(array_diff(QgsHelper::getModulePages(), $disabledPages), 'modulePage'));
            $this->closeSitemapFile();
        }

        $result = QgsHelper::createIndexFile();
        if ($result) {
            Configuration::updateValue('QLOGSITEMAP_LAST_EXPORT', date('Y-m-d H:i:s'));
        }

        return $result;
    }
    protected function sitemapPageMeta()
    {
        $idMeta = QgsHelper::getMetaIdByPage('sitemap');
        $objMeta = new Meta($idMeta ?: null);
        if (!$idMeta) {
            $objMeta->page = 'sitemap';
            $objMeta->configurable = 1;
        }

        $missing = false;
        foreach (Language::getLanguages(false) as $lang) {
            $idLang = (int) $lang['id_lang'];
            if ($idMeta && Meta::getMetaByPage('sitemap', $idLang)) {
                continue;
            }
            $objMeta->title[$idLang] = $this->l('Sitemap');
            $objMeta->description[$idLang] = $this->l('Lost? Find what you are looking for');
            $objMeta->keywords[$idLang] = '';
            $objMeta->url_rewrite[$idLang] = 'sitemap';
            $missing = true;
        }

        return $missing ? (bool) $objMeta->save() : true;
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
        $frequency = QgsHelper::getFrequency();
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
        $disabledPages = QgsHelper::getDisabledPages();
        foreach ($pages as $page => $type) {
            if ($page !== 'index' && in_array($page, $disabledPages)) {
                continue;
            }
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

    protected function addStandaloneProductUrls($lang)
    {
        $idLang = (int) $lang['id_lang'];
        $idShop = (int) $this->context->shop->id;

        foreach (QgsHelper::getStandaloneProductIds($idShop) as $row) {
            $product = new Product((int) $row['id_product'], false, $idLang);
            // The product has no content in this language
            if (!$product->link_rewrite) {
                continue;
            }

            $this->addUrl(
                $this->context->link->getProductLink($product, $product->link_rewrite, null, null, $idLang, $idShop),
                'standalone',
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
