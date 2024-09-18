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

class WkHotelFeaturesData extends ObjectModel
{
    public $feature_title;
    public $feature_description;
    public $active;
    public $position;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_features_block_data',
        'primary' => 'id_features_block',
        'multilang' => true,
        'fields' => array(
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            /* Lang fields */
            'feature_title' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true),
            'feature_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true),
        )
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);

        $this->image_dir = _PS_MODULE_DIR_.'wkhotelfeaturesblock/views/img/hotels_features_img/';
    }

    public function getHotelAmenities($active = 2, $idLang = false)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        $sql = 'SELECT fb.*, fbl.`feature_title`, fbl.`feature_description`
				FROM `'._DB_PREFIX_.'htl_features_block_data` fb
				INNER JOIN `'._DB_PREFIX_.'htl_features_block_data_lang` fbl
                ON (fbl.`id_features_block` = fb.`id_features_block`)
                WHERE fbl.`id_lang` = '.(int)$idLang;
        if ($active != 2) {
            $sql .= ' AND `active` = '.(int) $active;
        }
        $sql .= ' ORDER BY `position`';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Deletes current featuer block from the database
     * @return bool `true` if delete was successful
     */
    public function delete()
    {
        if (!parent::delete()
            || !$this->deleteImage(true)
            || !$this->cleanPositions()
        ) {
            return false;
        }
        return true;
    }

    public static function getHigherPosition()
    {
        $position = DB::getInstance()->getValue(
            'SELECT MAX(`position`) FROM `'._DB_PREFIX_.'htl_features_block_data`'
        );
        $result = (is_numeric($position)) ? $position : -1;
        return $result + 1;
    }

    public function updatePosition($way, $position)
    {
        if (!$result = Db::getInstance()->executeS(
            'SELECT htb.`id_features_block`, htb.`position` FROM `'._DB_PREFIX_.'htl_features_block_data` htb
            WHERE htb.`id_features_block` = '.(int) $this->id.' ORDER BY `position` ASC'
        )
        ) {
            return false;
        }

        $movedBlock = false;
        foreach ($result as $block) {
            if ((int)$block['id_features_block'] == (int)$this->id) {
                $movedBlock = $block;
            }
        }

        if ($movedBlock === false) {
            return false;
        }
        return (Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_features_block_data` SET `position`= `position` '.($way ? '- 1' : '+ 1').
            ' WHERE `position`'.($way ? '> '.
            (int)$movedBlock['position'].' AND `position` <= '.(int)$position : '< '
            .(int)$movedBlock['position'].' AND `position` >= '.(int)$position)
        ) && Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_features_block_data`
            SET `position` = '.(int)$position.'
            WHERE `id_features_block`='.(int)$movedBlock['id_features_block']
        ));
    }

    /**
     * Reorder blocks position
     * Call it after deleting a blocks.
     * @return bool $return
     */
    public function cleanPositions()
    {
        Db::getInstance()->execute('SET @i = -1', false);
        $sql = 'UPDATE `'._DB_PREFIX_.'htl_features_block_data` SET `position` = @i:=@i+1 ORDER BY `position` ASC';
        return (bool) Db::getInstance()->execute($sql);
    }

    // enter the default demo data of the module
    public function insertModuleDemoData()
    {
        $languages = Language::getLanguages(false);
        $htlAmenitiesHeading = array(
            'en' => 'Amenities',
            'nl' => 'Voorzieningen',
            'fr' => 'Équipements',
            'de' => 'Ausstattung',
            'ru' => 'Удобства',
            'es' => 'Servicios',
        );
        $htlAmenitiesDescription = array(
            'en' => 'Experience luxury at our hotel with top-notch amenities. Enjoy our fitness center, rejuvenating spa, serene outdoor pool, and exquisite dining.',
            'nl' => 'Ervaar luxe in ons hotel met eersteklas voorzieningen. Geniet van ons fitnesscentrum, verjongende spa, rustige buitenzwembad en voortreffelijk dineren.',
            'fr' => 'Découvrez le luxe dans notre hôtel avec des équipements de premier ordre. Profitez de notre centre de remise en forme, de notre spa revitalisant, de notre piscine extérieure paisible et de notre restaurant exquis.',
            'de' => 'Erleben Sie Luxus in unserem Hotel mit erstklassigen Annehmlichkeiten. Genießen Sie unser Fitnesscenter, das belebende Spa, den ruhigen Außenpool und das exquisite Essen.',
            'ru' => 'Испытайте роскошь в нашем отеле с первоклассными удобствами. Наслаждайтесь нашим фитнес-центром, омолаживающим спа, спокойным открытым бассейном и изысканной кухней.',
            'es' => 'Experimenta el lujo en nuestro hotel con servicios de primera clase. Disfruta de nuestro gimnasio, spa rejuvenecedor, tranquila piscina al aire libre y exquisita gastronomía.',
        );

        $HOTEL_AMENITIES_HEADING = array();
        $HOTEL_AMENITIES_DESCRIPTION = array();
        foreach ($languages as $lang) {
            if (isset($htlAmenitiesHeading[$lang['iso_code']])) {
                $HOTEL_AMENITIES_HEADING[$lang['id_lang']] = $htlAmenitiesHeading[$lang['iso_code']];
                $HOTEL_AMENITIES_DESCRIPTION[$lang['id_lang']] = $htlAmenitiesDescription[$lang['iso_code']];
            } else {
                $HOTEL_AMENITIES_HEADING[$lang['id_lang']] = $htlAmenitiesHeading['en'];
                $HOTEL_AMENITIES_DESCRIPTION[$lang['id_lang']] = $htlAmenitiesDescription['en'];
            }
        }

        Configuration::updateValue('HOTEL_AMENITIES_HEADING', $HOTEL_AMENITIES_HEADING);
        Configuration::updateValue('HOTEL_AMENITIES_DESCRIPTION', $HOTEL_AMENITIES_DESCRIPTION);
        $amenityDemoData = array(
            array(
                'name' => array(
                    'en' => 'Luxurious Rooms',
                    'nl' => 'Luxe kamers',
                    'fr' => 'Chambres luxueuses',
                    'de' => 'Luxuriöse Zimmer',
                    'ru' => 'Роскошные номера',
                    'es' => 'Habitaciones lujosas',
                ),
                'description' => array(
                    'en' => 'Experience unparalleled comfort in our luxurious rooms, featuring premium amenities and stunning views of the lake or cityscape.',
                    'nl' => 'Ervaar ongeëvenaard comfort in onze luxe kamers, met eersteklas voorzieningen en adembenemend uitzicht op het meer of de stad.',
                    'fr' => 'Vivez un confort inégalé dans nos chambres luxueuses, dotées d\'équipements haut de gamme et offrant une vue imprenable sur le lac ou la ville.',
                    'de' => 'Erleben Sie unvergleichlichen Komfort in unseren luxuriösen Zimmern mit erstklassigen Annehmlichkeiten und atemberaubendem Blick auf den See oder die Stadt.',
                    'ru' => 'Испытайте непревзойденный комфорт в наших роскошных номерах, оборудованных первоклассными удобствами и с захватывающим видом на озеро или городской пейзаж.',
                    'es' => 'Disfruta de un confort inigualable en nuestras lujosas habitaciones, que cuentan con servicios premium y vistas impresionantes al lago o a la ciudad.',
                )
            ),
            array(
                'name' => array(
                    'en' => 'World class chefs',
                    'nl' => 'Topkoks',
                    'fr' => 'Chefs de renommée mondiale',
                    'de' => 'Weltklasse-Köche',
                    'ru' => 'Шеф-повара мирового класса',
                    'es' => 'Chefs de clase mundial',
                ),
                'description' => array(
                    'en' => 'Experience culinary excellence with world-class chefs, where gourmet dishes are crafted from the finest local ingredients.',
                    'nl' => 'Ervaar culinaire excellentie met topkoks, waar gourmetgerechten worden bereid met de beste lokale ingrediënten.',
                    'fr' => 'Découvrez l\'excellence culinaire avec des chefs de renommée mondiale, où des plats gastronomiques sont élaborés à partir des meilleurs ingrédients locaux.',
                    'de' => 'Erleben Sie kulinarische Exzellenz mit Weltklasse-Köchen, wo Gourmetgerichte aus den feinsten lokalen Zutaten kreiert werden.',
                    'ru' => 'Исследуйте кулинарное искусство с шеф-поварами мирового класса, где гурманские блюда создаются из лучших местных ингредиентов.',
                    'es' => 'Experimenta la excelencia culinaria con chefs de clase mundial, donde se elaboran platos gourmet con los mejores ingredientes locales.',
                )
            ),
            array(
                'name' => array(
                    'en' => 'Restaurants',
                    'nl' => 'Restaurants',
                    'fr' => 'Restaurants',
                    'de' => 'Restaurants',
                    'ru' => 'Рестораны',
                    'es' => 'Restaurantes',
                ),
                'description' => array(
                    'en' => 'Savor exquisite dining at our restaurant, where a delightful menu is crafted from the finest local ingredients.',
                    'nl' => 'Geniet van voortreffelijk dineren in ons restaurant, waar een heerlijk menu wordt bereid met de beste lokale ingrediënten.',
                    'fr' => 'Dégustez une cuisine exquise dans notre restaurant, où un menu délicieux est élaboré à partir des meilleurs ingrédients locaux.',
                    'de' => 'Genießen Sie exquisites Essen in unserem Restaurant, wo ein köstliches Menü aus den feinsten lokalen Zutaten kreiert wird.',
                    'ru' => 'Наслаждайтесь изысканным ужином в нашем ресторане, где прекрасное меню создано из лучших местных ингредиентов.',
                    'es' => 'Disfruta de una comida exquisita en nuestro restaurante, donde se elabora un delicioso menú con los mejores ingredientes locales.',
                )
            ),
            array(
                'name' => array(
                    'en' => 'Gym & Spa',
                    'nl' => 'Fitness & Spa',
                    'fr' => 'Gym & Spa',
                    'de' => 'Fitness & Spa',
                    'ru' => 'Фитнес и Спа',
                    'es' => 'Gimnasio y Spa',
                ),
                'description' => array(
                    'en' => 'Rejuvenate with our state-of-the-art gym and spa, offering a sanctuary for relaxation and fitness.',
                    'nl' => 'Kom tot rust in onze moderne fitness en spa, waar een oase van ontspanning en fitness wordt geboden.',
                    'fr' => 'Revitalisez-vous avec notre salle de sport et spa dernier cri, offrant un sanctuaire pour la relaxation et la remise en forme.',
                    'de' => 'Erholen Sie sich in unserem hochmodernen Fitnessstudio und Spa, das einen Rückzugsort für Entspannung und Fitness bietet.',
                    'ru' => 'Восстановитесь в нашем современном фитнес-центре и спа, предлагающем убежище для релаксации и фитнеса.',
                    'es' => 'Rejuvenezca con nuestro gimnasio y spa de última generación, que ofrece un santuario para la relajación y el fitness.',
                )
            )
        );

        for ($i = 0; $i < 4; $i++) {
            $objFeatureData = new WkHotelFeaturesData();
            foreach ($languages as $lang) {
                if (isset($amenityDemoData[$i]['name'][$lang['iso_code']])) {
                    $objFeatureData->feature_title[$lang['id_lang']] = $amenityDemoData[$i]['name'][$lang['iso_code']];
                    $objFeatureData->feature_description[$lang['id_lang']] = $amenityDemoData[$i]['description'][$lang['iso_code']];
                } else {
                    $objFeatureData->feature_title[$lang['id_lang']] = $amenityDemoData[$i]['name']['en'];
                    $objFeatureData->feature_description[$lang['id_lang']] = $amenityDemoData[$i]['description']['en'];
                }
            }

            $objFeatureData->active = 1;
            $objFeatureData->position = WkHotelFeaturesData::getHigherPosition();
            if ($objFeatureData->save()) {
                $srcPath = _PS_MODULE_DIR_.'wkhotelfeaturesblock/views/img/dummy_img/'.$objFeatureData->id.'.jpg';
                if (file_exists($srcPath)) {
                    if (ImageManager::isRealImage($srcPath)
                        && ImageManager::isCorrectImageFileExt($srcPath)
                    ) {
                        ImageManager::resize(
                            $srcPath,
                            _PS_MODULE_DIR_.'wkhotelfeaturesblock/views/img/hotels_features_img/'.$objFeatureData->id.'.jpg'
                        );
                    }
                }
            }
        }
        return true;
    }
}
