<?php
/**
 * Visual Inspection & Image Quality Assessment Module for Housekeeping Governance
 *
 * @author    QloApps Engineering
 * @copyright Since 2026 QloApps
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class QloVisualInspection extends Module
{
    public function __construct()
    {
        $this->name = 'qlovisualinspection';
        $this->tab = 'hotel_reservation';
        $this->version = '1.0.0';
        $this->author = 'QloApps Engineering';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Inspeção Visual de Quartos');
        $this->description = $this->l('Métricas objetivas de luminância e nitidez para governança.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Module installation
     *
     * @return bool
     */
    public function install()
    {
        return parent::install() && $this->installTab();
    }

    /**
     * Module uninstallation
     *
     * @return bool
     */
    public function uninstall()
    {
        return $this->uninstallTab() && parent::uninstall();
    }

    /**
     * Install administrative menu tab
     *
     * @return bool
     */
    private function installTab()
    {
        $idParent = (int) Tab::getIdFromClassName('AdminParentOrders');
        if (!$idParent) {
            $idParent = (int) Tab::getIdFromClassName('AdminOrders');
        }

        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminVisualInspection';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Inspeção de Quartos';
        }
        $tab->id_parent = $idParent;
        $tab->module = $this->name;
        return (bool) $tab->add();
    }

    /**
     * Uninstall administrative menu tab
     *
     * @return bool
     */
    private function uninstallTab()
    {
        $idTab = (int) Tab::getIdFromClassName('AdminVisualInspection');
        if ($idTab) {
            $tab = new Tab($idTab);
            return (bool) $tab->delete();
        }
        return true;
    }
}
