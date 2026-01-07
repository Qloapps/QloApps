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

class QhoHotelOfferingDb
{
    public function createTables()
    {
        if ($sqls = $this->getModuleSqls()) {
            foreach ($sqls as $query) {
                if ($query) {
                    if (!DB::getInstance()->execute(trim($query))) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function getModuleSqls()
    {
        return array(
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."qho_offering` (
                `id_offering` int(11) NOT NULL AUTO_INCREMENT,
                `active` tinyint(1) NOT NULL,
                `position` int(10) unsigned NOT NULL DEFAULT '0',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_offering`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."qho_offering_lang` (
                `id_offering` int(11) NOT NULL,
                `id_lang` int(11) NOT NULL,
                `name` text NOT NULL,
                `description` text NOT NULL,
                PRIMARY KEY (`id_offering`, `id_lang`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;"
        );
    }

    public function dropTables()
    {
        return DB::getInstance()->execute(
            'DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'qho_offering`,
            `'._DB_PREFIX_.'qho_offering_lang`
        ');
    }


    public function insertModuleDemoData()
    {
        $languages = Language::getLanguages(false);
        $OFFERING_BLOCK_HEADING = array();
        $OFFERING_BLOCK_CONTENT = array();
        $offeringHeading = array(
            'en' => 'Our Offerings',
            'nl' => 'Onze Aanbiedingen',
            'fr' => 'Nos Offres',
            'de' => 'Unsere Angebote',
            'ru' => 'Наши предложения',
            'es' => 'Nuestras Ofertas',
        );
        $offeringContent = array(
            'en' => 'Explore our wide range of services and facilities designed to make your stay comfortable and memorable.',
            'nl' => 'Ontdek ons brede aanbod aan diensten en faciliteiten die zijn ontworpen om uw verblijf comfortabel en onvergetelijk te maken.',
            'fr' => 'Découvrez notre large gamme de services et d’installations conçus pour rendre votre séjour confortable et inoubliable.',
            'de' => 'Entdecken Sie unser breites Angebot an Dienstleistungen und Einrichtungen, die Ihren Aufenthalt komfortabel und unvergesslich machen.',
            'ru' => 'Ознакомьтесь с нашим широким спектром услуг и удобств, созданных для комфортного и незабываемого пребывания.',
            'es' => 'Descubra nuestra amplia gama de servicios e instalaciones diseñados para que su estancia sea cómoda e inolvidable.',
        );

        foreach ($languages as $lang) {
            if (isset($offeringHeading[$lang['iso_code']])) {
                $OFFERING_BLOCK_HEADING[$lang['id_lang']] = $offeringHeading[$lang['iso_code']];
                $OFFERING_BLOCK_CONTENT[$lang['id_lang']] = $offeringContent[$lang['iso_code']];
            } else {
                $OFFERING_BLOCK_HEADING[$lang['id_lang']] = $offeringHeading['en'];
                $OFFERING_BLOCK_CONTENT[$lang['id_lang']] = $offeringContent['en'];
            }
        }

        // update global configuration values in multilang
        Configuration::updateValue('OFFERING_BLOCK_HEADING', $OFFERING_BLOCK_HEADING);
        Configuration::updateValue('OFFERING_BLOCK_CONTENT', $OFFERING_BLOCK_CONTENT);

        $offeringDemoContent = array(
            'en' => array(
                array(
                    'name' => 'Food',
                    'content' => 'Enjoy delicious meals in the comfort of your room with our prompt and convenient dining service.',
                ),
                array(
                    'name' => 'Spa',
                    'content' => 'Relax and unwind with professional spa therapies designed to refresh your body and mind.',
                ),
                array(
                    'name' => 'Swimming Pool',
                    'content' => 'Take a refreshing dip in our clean and well-maintained swimming pool.',
                ),
                array(
                    'name' => 'Candle Light Dinner',
                    'content' => 'Enjoy a romantic candle light dinner with a beautifully arranged private setting.',
                ),
                array(
                    'name' => 'Free Wi-Fi',
                    'content' => 'Stay connected with complimentary high-speed Wi-Fi available throughout the hotel.',
                ),
            ),
            'nl' => array(
                array(
                    'name' => 'Eten',
                    'content' => 'Geniet van heerlijke maaltijden in het comfort van uw kamer met onze snelle en gemakkelijke eetservice.',
                ),
                array(
                    'name' => 'Spa',
                    'content' => 'Ontspan en kom tot rust met professionele spabehandelingen voor lichaam en geest.',
                ),
                array(
                    'name' => 'Zwembad',
                    'content' => 'Neem een verfrissende duik in ons schone en goed onderhouden zwembad.',
                ),
                array(
                    'name' => 'Diner bij Kaarslicht',
                    'content' => 'Geniet van een romantisch diner bij kaarslicht in een sfeervolle privéomgeving.',
                ),
                array(
                    'name' => 'Gratis Wi-Fi',
                    'content' => 'Blijf verbonden met gratis snelle Wi-Fi in het hele hotel.',
                ),
            ),
            'fr' => array(
                array(
                    'name' => 'Restauration',
                    'content' => 'Savourez de délicieux repas dans le confort de votre chambre grâce à notre service rapide.',
                ),
                array(
                    'name' => 'Spa',
                    'content' => 'Détendez-vous avec des soins spa professionnels conçus pour revitaliser le corps et l’esprit.',
                ),
                array(
                    'name' => 'Piscine',
                    'content' => 'Profitez d’un moment de détente dans notre piscine propre et bien entretenue.',
                ),
                array(
                    'name' => 'Dîner aux Chandelles',
                    'content' => 'Profitez d’un dîner romantique aux chandelles dans un cadre privé élégant.',
                ),
                array(
                    'name' => 'Wi-Fi Gratuit',
                    'content' => 'Restez connecté grâce au Wi-Fi haut débit gratuit dans tout l’hôtel.',
                ),
            ),
            'de' => array(
                array(
                    'name' => 'Essen',
                    'content' => 'Genießen Sie köstliche Mahlzeiten bequem in Ihrem Zimmer mit unserem schnellen Service.',
                ),
                array(
                    'name' => 'Spa',
                    'content' => 'Entspannen Sie sich mit professionellen Spa-Behandlungen für Körper und Geist.',
                ),
                array(
                    'name' => 'Schwimmbad',
                    'content' => 'Genießen Sie eine erfrischende Auszeit in unserem gepflegten Swimmingpool.',
                ),
                array(
                    'name' => 'Candle-Light-Dinner',
                    'content' => 'Erleben Sie ein romantisches Candle-Light-Dinner in privater Atmosphäre.',
                ),
                array(
                    'name' => 'Kostenloses WLAN',
                    'content' => 'Bleiben Sie mit kostenlosem Highspeed-WLAN im gesamten Hotel verbunden.',
                ),
            ),
            'ru' => array(
                array(
                    'name' => 'Питание',
                    'content' => 'Наслаждайтесь вкусными блюдами прямо в номере с удобным обслуживанием.',
                ),
                array(
                    'name' => 'Спа',
                    'content' => 'Расслабьтесь с профессиональными спа-процедурами для тела и разума.',
                ),
                array(
                    'name' => 'Бассейн',
                    'content' => 'Освежитесь в нашем чистом и ухоженном бассейне.',
                ),
                array(
                    'name' => 'Романтический ужин',
                    'content' => 'Насладитесь романтическим ужином при свечах в уютной обстановке.',
                ),
                array(
                    'name' => 'Бесплатный Wi-Fi',
                    'content' => 'Оставайтесь на связи с бесплатным высокоскоростным Wi-Fi на всей территории отеля.',
                ),
            ),
            'es' => array(
                array(
                    'name' => 'Comida',
                    'content' => 'Disfrute de deliciosas comidas en la comodidad de su habitación con un servicio eficiente.',
                ),
                array(
                    'name' => 'Spa',
                    'content' => 'Relájese con terapias de spa profesionales diseñadas para renovar cuerpo y mente.',
                ),
                array(
                    'name' => 'Piscina',
                    'content' => 'Disfrute de un refrescante baño en nuestra piscina limpia y bien cuidada.',
                ),
                array(
                    'name' => 'Cena a la Luz de las Velas',
                    'content' => 'Disfrute de una cena romántica a la luz de las velas en un ambiente privado.',
                ),
                array(
                    'name' => 'Wi-Fi Gratis',
                    'content' => 'Manténgase conectado con Wi-Fi de alta velocidad gratuito en todo el hotel.',
                ),
            ),
        );

        for ($i = 0; $i < 5; $i++) {
            $objModule = Module::getInstanceByName('qlohotelofferings');
            $srcPath = $objModule->getLocalPath().'views/img/dummy_img/'.($i+1).'.jpg';
            if (file_exists($srcPath)) {
                if (ImageManager::isRealImage($srcPath)
                    && ImageManager::isCorrectImageFileExt($srcPath)
                ) {
                    if (ImageManager::resize(
                        $srcPath,
                        $objModule->getLocalPath().'views/img/offering_img/'.($i+1).'.jpg',
                        720,
                        540
                    )) {

                        $objHotelOffering = new QhoHotelOffering();
                        foreach ($languages as $lang) {
                            if (isset($offeringDemoContent[$i]['content'][$lang['iso_code']])) {
                                $objHotelOffering->name[$lang['id_lang']] = $offeringDemoContent[$lang['iso_code']][$i]['name'];
                                $objHotelOffering->description[$lang['id_lang']] = $offeringDemoContent[$lang['iso_code']][$i]['content'];
                            } else {
                                $objHotelOffering->name[$lang['id_lang']] = $offeringDemoContent['en'][$i]['name'];
                                $objHotelOffering->description[$lang['id_lang']] = $offeringDemoContent['en'][$i]['content'];
                            }
                        }

                        $objHotelOffering->position = $objHotelOffering->getHigherPosition();
                        $objHotelOffering->active = 1;
                        $objHotelOffering->save();
                    }
                }
            }
        }
        return true;
    }
}
