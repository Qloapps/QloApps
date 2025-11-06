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

class WkHotelTestimonialData extends ObjectModel
{
    public $name;
    public $designation;
    public $testimonial_content;
    public $active;
    public $position;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_testimonials_block_data',
        'primary' => 'id_testimonial_block',
        'multilang' => true,
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING),
            'designation' => array('type' => self::TYPE_STRING),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            /* Lang fields */
            'testimonial_content' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true),
    ));

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);

        $this->image_dir = _PS_MODULE_DIR_.'wktestimonialblock/views/img/hotels_testimonials_img/';
    }

    public function getTestimonialData($active = 2, $idLang = false)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        $sql = 'SELECT tm.*, tml.`testimonial_content` FROM `'._DB_PREFIX_.'htl_testimonials_block_data` tm
        INNER JOIN `'._DB_PREFIX_.'htl_testimonials_block_data_lang` AS tml ON
        (tml.`id_testimonial_block` = tm.`id_testimonial_block`)
        WHERE tml.`id_lang` = '.(int)$idLang;

        if ($active != 2) {
            $sql .= ' AND `active` = '.(int) $active;
        }
        $sql .= ' ORDER BY `position`';

        $result = Db::getInstance()->executeS($sql);
        return $result;
    }

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

    public function getHigherPosition()
    {
        $position = DB::getInstance()->getValue(
            'SELECT MAX(`position`) FROM `'._DB_PREFIX_.'htl_testimonials_block_data`'
        );
        $result = (is_numeric($position)) ? $position : -1;
        return $result + 1;
    }

    public function updatePosition($way, $position)
    {
        if (!$result = Db::getInstance()->executeS(
            'SELECT htb.`id_testimonial_block`, htb.`position` FROM `'._DB_PREFIX_.'htl_testimonials_block_data` htb
            WHERE htb.`id_testimonial_block` = '.(int) $this->id.' ORDER BY `position` ASC'
        )
        ) {
            return false;
        }

        $movedBlock = false;
        foreach ($result as $block) {
            if ((int)$block['id_testimonial_block'] == (int)$this->id) {
                $movedBlock = $block;
            }
        }

        if ($movedBlock === false) {
            return false;
        }
        return (Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_testimonials_block_data` SET `position`= `position` '.($way ? '- 1' : '+ 1').
            ' WHERE `position`'.($way ? '> '.
            (int)$movedBlock['position'].' AND `position` <= '.(int)$position : '< '
            .(int)$movedBlock['position'].' AND `position` >= '.(int)$position)
        ) && Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_testimonials_block_data`
            SET `position` = '.(int)$position.'
            WHERE `id_testimonial_block`='.(int)$movedBlock['id_testimonial_block']
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
        $sql = 'UPDATE `'._DB_PREFIX_.'htl_testimonials_block_data` SET `position` = @i:=@i+1 ORDER BY `position` ASC';
        return (bool) Db::getInstance()->execute($sql);
    }

    // enter the default demo data of the module
    public function insertModuleDemoData()
    {
        $languages = Language::getLanguages(false);
        $HOTEL_TESIMONIAL_BLOCK_HEADING = array();
        $HOTEL_TESIMONIAL_BLOCK_CONTENT = array();
        $htlTestimonialHeading = array(
            'en' => 'What our guests say?',
            'nl' => 'Wat zeggen onze gasten?',
            'fr' => 'Que disent nos clients?',
            'de' => 'Was sagen unsere Gäste?',
            'ru' => 'Что говорят наши гости?',
            'es' => '¿Qué dicen nuestros huéspedes?',
        );
        $htlTestimonialContent = array(
            'en' => 'Here are some valuable feedbacks from our guests.',
            'nl' => 'Hier zijn enkele waardevolle feedback van onze gasten.',
            'fr' => 'Voici quelques retours précieux de nos clients.',
            'de' => 'Hier sind einige wertvolle Rückmeldungen von unseren Gästen.',
            'ru' => 'Вот некоторые ценные отзывы от наших гостей.',
            'es' => 'Aquí hay algunos comentarios valiosos de nuestros huéspedes.',
        );

        foreach ($languages as $lang) {
            if (isset($htlTestimonialHeading[$lang['iso_code']])) {
                $HOTEL_TESIMONIAL_BLOCK_HEADING[$lang['id_lang']] = $htlTestimonialHeading[$lang['iso_code']];
                $HOTEL_TESIMONIAL_BLOCK_CONTENT[$lang['id_lang']] = $htlTestimonialContent[$lang['iso_code']];
            } else {
                $HOTEL_TESIMONIAL_BLOCK_HEADING[$lang['id_lang']] = $htlTestimonialHeading['en'];
                $HOTEL_TESIMONIAL_BLOCK_CONTENT[$lang['id_lang']] = $htlTestimonialContent['en'];
            }
        }

        // update global configuration values in multilang
        Configuration::updateValue('HOTEL_TESIMONIAL_BLOCK_HEADING', $HOTEL_TESIMONIAL_BLOCK_HEADING);
        Configuration::updateValue('HOTEL_TESIMONIAL_BLOCK_CONTENT', $HOTEL_TESIMONIAL_BLOCK_CONTENT);

        $testimonialDemoContent = array(
            0 => array(
                'name' => 'Steve Rogers',
                'designation' => 'Eon Comics CEO',
                'content' => array(
                    'en' => 'As a frequent traveler, I can confidently say Hotel Prime stands out for its tranquil environment and exceptional hospitality. The View Room provided a perfect retreat with its stunning cityscape views and comfortable accommodations.',
                    'nl' => 'Als frequente reiziger kan ik met vertrouwen zeggen dat Hotel Prime opvalt door zijn rustige omgeving en uitzonderlijke gastvrijheid. De View Room bood een perfecte toevluchtsoord met een prachtig uitzicht op de stad en comfortabele accommodaties.',
                    'fr' => 'En tant que voyageur fréquent, je peux dire avec confiance que l\'Hôtel Prime se distingue par son environnement tranquille et son hospitalité exceptionnelle. La View Room offrait un refuge parfait avec sa vue imprenable sur la ville et ses aménagements confortables.',
                    'de' => 'Als häufiger Reisender kann ich mit Zuversicht sagen, dass das Hotel Prime durch seine ruhige Umgebung und außergewöhnliche Gastfreundschaft hervorsticht. Das View Room bot einen perfekten Rückzugsort mit atemberaubendem Stadtblick und komfortablen Unterkünften.',
                    'ru' => 'Как частый путешественник, я могу с уверенностью сказать, что отель Prime выделяется своим спокойным окружением и исключительным гостеприимством. Номер с видом стал идеальным убежищем с потрясающими видами на город и комфортными условиями проживания.',
                    'es' => 'Como viajero frecuente, puedo decir con confianza que el Hotel Prime se destaca por su entorno tranquilo y su hospitalidad excepcional. La View Room proporcionó un refugio perfecto con sus impresionantes vistas de la ciudad y cómodas habitaciones.',
                ),
            ),
            1 => array(
                'name' => 'Calrk Kent',
                'designation' => 'Ken Comics Kal',
                'content' => array(
                    'en' => 'Hotel Prime surpassed my expectations in every way. The Executive Room was not only luxurious but also provided a peaceful retreat with its stunning lake views. The attention to detail and personalized service made my stay truly memorable. I highly recommend Hotel Prime for both business and leisure travelers looking for a blend of comfort and sophistication.',
                    'nl' => 'Hotel Prime overtrof mijn verwachtingen op alle vlakken. De Executive Room was niet alleen luxe, maar bood ook een rustig toevluchtsoord met een prachtig uitzicht op het meer. De aandacht voor detail en de persoonlijke service maakten mijn verblijf echt onvergetelijk. Ik beveel Hotel Prime ten zeerste aan voor zowel zakenreizigers als vakantiegangers die op zoek zijn naar een combinatie van comfort en verfijning.',
                    'fr' => 'L\'Hôtel Prime a dépassé mes attentes à tous égards. La Chambre Exécutive était non seulement luxueuse mais offrait également un refuge paisible avec sa vue imprenable sur le lac. L\'attention aux détails et le service personnalisé ont rendu mon séjour vraiment mémorable. Je recommande vivement l\'Hôtel Prime aux voyageurs d\'affaires et de loisirs à la recherche d\'un mélange de confort et de sophistication.',
                    'de' => 'Das Hotel Prime hat meine Erwartungen in jeder Hinsicht übertroffen. Das Executive Zimmer war nicht nur luxuriös, sondern bot auch einen friedlichen Rückzugsort mit atemberaubendem Seeblick. Die Liebe zum Detail und der persönliche Service machten meinen Aufenthalt wirklich unvergesslich. Ich empfehle das Hotel Prime sowohl Geschäftsreisenden als auch Urlaubern, die Komfort und Eleganz suchen, wärmstens.',
                    'ru' => 'Отель Prime превзошел все мои ожидания. Номер Executive был не только роскошным, но и предоставлял спокойное убежище с потрясающим видом на озеро. Внимание к деталям и персонализированный сервис сделали мое пребывание по-настоящему незабываемым. Я настоятельно рекомендую отель Prime как деловым путешественникам, так и туристам, ищущим сочетание комфорта и утонченности.',
                    'es' => 'El Hotel Prime superó mis expectativas en todos los sentidos. La Habitación Ejecutiva no solo era lujosa, sino que también ofrecía un refugio tranquilo con sus impresionantes vistas al lago. La atención al detalle y el servicio personalizado hicieron que mi estancia fuera realmente memorable. Recomiendo encarecidamente el Hotel Prime tanto para viajeros de negocios como de placer que buscan una combinación de comodidad y sofisticación.',
                ),
            ),
            2 => array(
                'name' => 'John Doe',
                'designation' => 'Jan Comics Joe',
                'content' => array(
                    'en' => 'My stay at Hotel Prime was absolutely rejuvenating. The Executive Room was spacious, elegant, and offered breathtaking lake views. The staff’s attention to detail and impeccable service made my business trip both productive and relaxing.',
                    'nl' => 'Mijn verblijf in Hotel Prime was absoluut verkwikkend. De Executive Room was ruim, elegant en bood een adembenemend uitzicht op het meer. De aandacht voor detail en de onberispelijke service van het personeel maakten mijn zakenreis zowel productief als ontspannend.',
                    'fr' => 'Mon séjour à l\'Hôtel Prime a été absolument revitalisant. La Chambre Exécutive était spacieuse, élégante et offrait une vue imprenable sur le lac. L\'attention aux détails et le service impeccable du personnel ont rendu mon voyage d\'affaires à la fois productif et relaxant.',
                    'de' => 'Mein Aufenthalt im Hotel Prime war absolut erholsam. Das Executive Zimmer war geräumig, elegant und bot einen atemberaubenden Blick auf den See. Die Liebe zum Detail und der tadellose Service des Personals machten meine Geschäftsreise sowohl produktiv als auch entspannend.',
                    'ru' => 'Мое пребывание в отеле Prime было абсолютно восстанавливающим. Номер Executive был просторным, элегантным и предлагал захватывающий вид на озеро. Внимание к деталям и безупречное обслуживание персонала сделали мою деловую поездку как продуктивной, так и расслабляющей.',
                    'es' => 'Mi estancia en el Hotel Prime fue absolutamente rejuvenecedora. La Habitación Ejecutiva era espaciosa, elegante y ofrecía impresionantes vistas al lago. La atención al detalle y el servicio impecable del personal hicieron que mi viaje de negocios fuera productivo y relajante.',
                ),
            )
        );

        for ($i = 0; $i < 3; $i++) {
            $srcPath = _PS_MODULE_DIR_.'wktestimonialblock/views/img/dummy_img/'.($i+1).'.png';
            if (file_exists($srcPath)) {
                if (ImageManager::isRealImage($srcPath)
                    && ImageManager::isCorrectImageFileExt($srcPath)
                ) {
                    if (ImageManager::resize(
                        $srcPath,
                        _PS_MODULE_DIR_.'wktestimonialblock/views/img/hotels_testimonials_img/'.($i+1).'.jpg'
                    )) {

                        $objTestimonialData = new WkHotelTestimonialData();
                        $objTestimonialData->name = $testimonialDemoContent[$i]['name'];
                        $objTestimonialData->designation = $testimonialDemoContent[$i]['designation'];
                        foreach ($languages as $lang) {
                            if (isset($testimonialDemoContent[$i]['content'][$lang['iso_code']])) {
                                $objTestimonialData->testimonial_content[$lang['id_lang']] = $testimonialDemoContent[$i]['content'][$lang['iso_code']];
                            } else {
                                $objTestimonialData->testimonial_content[$lang['id_lang']] = $testimonialDemoContent[$i]['content']['en'];
                            }
                        }
                        $objTestimonialData->position = $this->getHigherPosition();
                        $objTestimonialData->active = 1;
                        $objTestimonialData->save();
                    }
                }
            }
        }
        return true;
    }
}
