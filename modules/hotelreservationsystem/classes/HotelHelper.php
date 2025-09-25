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

class HotelHelper
{
    public static function assignDataTableVariables()
    {
        $objModule = new HotelreservationSystem();
        $jsVars = array(
                'display_name' => $objModule->l('Display', 'HotelHelper', true),
                'records_name' => $objModule->l('records per page', 'HotelHelper', true),
                'no_product' => $objModule->l('No records found', 'HotelHelper', true),
                'show_page' => $objModule->l('Showing page', 'HotelHelper', true),
                'show_of' => $objModule->l('of', 'HotelHelper', true),
                'no_record' => $objModule->l('No records available', 'HotelHelper',true),
                'filter_from' => $objModule->l('filtered from', 'HotelHelper', true),
                't_record' => $objModule->l('total records', 'HotelHelper', true),
                'search_item' => $objModule->l('Search', 'HotelHelper', true),
                'p_page' => $objModule->l('Previous', 'HotelHelper', true),
                'n_page' => $objModule->l('Next', 'HotelHelper', true),
            );

        Media::addJsDef($jsVars);
    }

    public function insertHotelCommonFeatures()
    {
        $parent_features_arr = array(
            'Business Services' => array(
                'name' => array(
                    'en' => 'Business Services',
                    'nl' => 'Zakelijke diensten',
                    'fr' => 'Services aux entreprises',
                    'de' => 'Geschäftsdienstleistungen',
                    'ru' => 'Бизнес-услуги',
                    'es' => 'Servicios empresariales',
                ),
                'features' => array(
                    array(
                        'en' => 'Business Center',
                        'nl' => 'Businesscentrum',
                        'fr' => 'Centre d\'affaires',
                        'de' => 'Business Center',
                        'ru' => 'Бизнес-центр',
                        'es' => 'Centro de negocios',
                    ),
                    array(
                        'en' => 'Audio-Visual Equipment',
                        'nl' => 'Audiovisuele apparatuur',
                        'fr' => 'Équipement audiovisuel',
                        'de' => 'Audiovisuelle Ausstattung',
                        'ru' => 'Аудио-визуальное оборудование',
                        'es' => 'Equipamiento audiovisual',
                    ),
                    array(
                        'en' => 'Board room',
                        'nl' => 'Bestuurskamer',
                        'fr' => 'Salle de réunion',
                        'de' => 'Besprechungsraum',
                        'ru' => 'Переговорная',
                        'es' => 'Sala de reuniones',
                    ),
                    array(
                        'en' => 'Conference Facilities',
                        'nl' => 'Conferentiefaciliteiten',
                        'fr' => 'Installations de conférence',
                        'de' => 'Konferenzeinrichtungen',
                        'ru' => 'Конференц-услуги',
                        'es' => 'Instalaciones de conferencias',
                    ),
                    array(
                        'en' => 'Secretarial Services',
                        'nl' => 'Secretariële diensten',
                        'fr' => 'Services secrétariaux',
                        'de' => 'Sekretariatsdienste',
                        'ru' => 'Секретариат',
                        'es' => 'Servicios secretariales',
                    ),
                    array(
                        'en' => 'Fax Machine',
                        'nl' => 'Fax Machine',
                        'fr' => 'Fax',
                        'de' => 'Faxgerät',
                        'ru' => 'Факс',
                        'es' => 'Máquina de fax',
                    ),
                    array(
                        'en' => 'Internet Access',
                        'nl' => 'Internettoegang',
                        'fr' => 'Accès Internet',
                        'de' => 'Internetzugang',
                        'ru' => 'Доступ в интернет',
                        'es' => 'Acceso a Internet',
                    ),
                ),
            ),
           'Complementary' => array(
                'name' => array(
                    'en' => 'Complementary',
                    'nl' => 'Aanvullende diensten',
                    'fr' => 'Services complémentaires',
                    'de' => 'Zusätzliche Dienstleistungen',
                    'ru' => 'Дополнительные услуги',
                    'es' => 'Servicios complementarios',
                ),
                'features' => array(
                    array(
                        'en' => 'Internet Access Free',
                        'nl' => 'Gratis internettoegang',
                        'fr' => 'Accès Internet gratuit',
                        'de' => 'Kostenloser Internetzugang',
                        'ru' => 'Бесплатный доступ в интернет',
                        'es' => 'Acceso gratuito a Internet',
                    ),
                    array(
                        'en' => 'Transfer Available',
                        'nl' => 'Transfer beschikbaar',
                        'fr' => 'Transfert disponible',
                        'de' => 'Transfer verfügbar',
                        'ru' => 'Трансфер доступен',
                        'es' => 'Transfer disponible',
                    ),
                    array(
                        'en' => 'NewsPaper In Lobby',
                        'nl' => 'Krant in de lobby',
                        'fr' => 'Journal dans le hall',
                        'de' => 'Zeitung in der Lobby',
                        'ru' => 'Газеты в холле',
                        'es' => 'Periódicos en el vestíbulo',
                    ),
                    array(
                        'en' => 'Shopping Drop Facility',
                        'nl' => 'Winkel afzetmogelijkheid',
                        'fr' => 'Service de dépôt de shopping',
                        'de' => 'Einkaufsabgabemöglichkeit',
                        'ru' => 'Сервис "Доставка покупок"',
                        'es' => 'Instalaciones de entrega de compras',
                    ),
                    array(
                        'en' => 'Welcome Drinks',
                        'nl' => 'Welkomstdrankjes',
                        'fr' => 'Boissons de bienvenue',
                        'de' => 'Willkommensgetränke',
                        'ru' => 'Приветственные напитки',
                        'es' => 'Bebidas de bienvenida',
                    ),
                ),
            ),
            'Entertainment' => array(
                'name' => array(
                    'en' => 'Entertainment',
                    'nl' => 'Entertainment',
                    'fr' => 'Divertissement',
                    'de' => 'Unterhaltung',
                    'ru' => 'Развлечения',
                    'es' => 'Entretenimiento',
                ),
                'features' => array(
                    array(
                        'en' => 'DiscoTheatre',
                        'nl' => 'Discotheek/theater',
                        'fr' => 'Discothèque/théâtre',
                        'de' => 'Discotheater',
                        'ru' => 'Диско-театр',
                        'es' => 'Discoteca/teatro',
                    ),
                    array(
                        'en' => 'Casino',
                        'nl' => 'Casino',
                        'fr' => 'Casino',
                        'de' => 'Kasino',
                        'ru' => 'Казино',
                        'es' => 'Casino',
                    ),
                    array(
                        'en' => 'Amphitheatre',
                        'nl' => 'Amphitheater',
                        'fr' => 'Amphithéâtre',
                        'de' => 'Amphitheater',
                        'ru' => 'Амфитеатр',
                        'es' => 'Anfiteatro',
                    ),
                    array(
                        'en' => 'Dance Performances(On Demand)',
                        'nl' => 'Dansvoorstellingen (op aanvraag)',
                        'fr' => 'Spectacles de danse (sur demande)',
                        'de' => 'Tanzaufführungen (auf Anfrage)',
                        'ru' => 'Танцевальные выступления (по запросу)',
                        'es' => 'Actuaciones de baile (bajo demanda)',
                    ),
                    array(
                        'en' => 'Karoke',
                        'nl' => 'Karaoke',
                        'fr' => 'Karaoké',
                        'de' => 'Karaoke',
                        'ru' => 'Караоке',
                        'es' => 'Karaoke',
                    ),
                    array(
                        'en' => 'Mini Theatre',
                        'nl' => 'Minitheater',
                        'fr' => 'Mini-théâtre',
                        'de' => 'Minitheater',
                        'ru' => 'Мини-театр',
                        'es' => 'Mini teatro',
                    ),
                    array(
                        'en' => 'Night Club',
                        'nl' => 'Nachtklub',
                        'fr' => 'Nightclub',
                        'de' => 'Nachtclub',
                        'ru' => 'Ночной клуб',
                        'es' => 'Club nocturno',
                    ),
                ),
            ),
            'Facilities' => array(
                'name' => array(
                    'en' => 'Facilities',
                    'nl' => 'Faciliteiten',
                    'fr' => 'Installations',
                    'de' => 'Einrichtungen',
                    'ru' => 'Удобства',
                    'es' => 'Instalaciones',
                ),
                'features' => array(
                    array(
                        'en' => 'Laundry Service',
                        'nl' => 'Wasservice',
                        'fr' => 'Service de blanchisserie',
                        'de' => 'Wäscheservice',
                        'ru' => 'Услуги прачечной',
                        'es' => 'Servicio de lavandería',
                    ),
                    array(
                        'en' => 'Power Backup',
                        'nl' => 'Noodstroomvoorziening',
                        'fr' => 'Alimentation de secours',
                        'de' => 'Stromausfall',
                        'ru' => 'Резервное питание',
                        'es' => 'Respaldo de energía',
                    ),
                    array(
                        'en' => 'ATM/Banking',
                        'nl' => 'Geldautomaat / Bankieren',
                        'fr' => 'Guichet automatique / Banque',
                        'de' => 'Geldautomat / Bankwesen',
                        'ru' => 'Банкомат / Банковское дело',
                        'es' => 'Cajero automático / Banca',
                    ),
                    array(
                        'en' => 'Currency Exchange',
                        'nl' => 'Valuta wissel',
                        'fr' => 'Change de devises',
                        'de' => 'Währungsumtausch',
                        'ru' => 'Обмен валюты',
                        'es' => 'Cambio de divisas',
                    ),
                    array(
                        'en' => 'Dry Cleaning',
                        'nl' => 'Stomerij',
                        'fr' => 'Nettoyage à sec',
                        'de' => 'Chemische Reinigung',
                        'ru' => 'Химчистка',
                        'es' => 'Limpieza en seco',
                    ),
                    array(
                        'en' => 'Library',
                        'nl' => 'Bibliotheek',
                        'fr' => 'Bibliothèque',
                        'de' => 'Bibliothek',
                        'ru' => 'Библиотека',
                        'es' => 'Biblioteca',
                    ),
                    array(
                        'en' => 'Doctor On Call',
                        'nl' => 'Dokter op oproep',
                        'fr' => 'Médecin de garde',
                        'de' => 'Arzt auf Abruf',
                        'ru' => 'Доктор на вызов',
                        'es' => 'Médico de guardia',
                    ),
                    array(
                        'en' => 'Party Hall',
                        'nl' => 'Feestzaal',
                        'fr' => 'Salle de fête',
                        'de' => 'Partyhalle',
                        'ru' => 'Вечеринка',
                        'es' => 'Sala de fiestas',
                    ),
                    array(
                        'en' => 'Yoga Hall',
                        'nl' => 'Yoga zaal',
                        'fr' => 'Salle de yoga',
                        'de' => 'Yoga-Halle',
                        'ru' => 'Зал йоги',
                        'es' => 'Sala de yoga',
                    ),
                    array(
                        'en' => 'Pets Allowed',
                        'nl' => 'Huisdieren toegestaan',
                        'fr' => 'Animaux autorisés',
                        'de' => 'Haustiere erlaubt',
                        'ru' => 'Разрешены домашние животные',
                        'es' => 'Se admiten mascotas',
                    ),
                    array(
                        'en' => 'Kids Play Zone',
                        'nl' => 'Kinderspeelzone',
                        'fr' => 'Zone de jeu pour enfants',
                        'de' => 'Kinder-Spielzone',
                        'ru' => 'Зона игр для детей',
                        'es' => 'Zona de juegos para niños',
                    ),
                    array(
                        'en' => 'Wedding Services Facilities',
                        'nl' => 'Bruiloftsdiensten faciliteiten',
                        'fr' => 'Installations pour services de mariage',
                        'de' => 'Hochzeitsdienstleistungen Einrichtungen',
                        'ru' => 'Свадебные услуги Удобства',
                        'es' => 'Instalaciones de servicios de bodas',
                    ),
                    array(
                        'en' => 'Fire Place Available',
                        'nl' => 'Openhaard beschikbaar',
                        'fr' => 'Cheminée disponible',
                        'de' => 'Kamin verfügbar',
                        'ru' => 'Камин доступен',
                        'es' => 'Chimenea disponible',
                    ),
                ),
            ),
            'General Services' => array(
                'name' => array(
                    'en' => 'General Services',
                    'nl' => 'Algemene diensten',
                    'fr' => 'Services généraux',
                    'de' => 'Allgemeine Dienstleistungen',
                    'ru' => 'Общие услуги',
                    'es' => 'Servicios generales',
                ),
                'features' => array(
                    array(
                        'en' => 'Room Service',
                        'nl' => 'Roomservice',
                        'fr' => 'Service en chambre',
                        'de' => 'Zimmerservice',
                        'ru' => 'Обслуживание номеров',
                        'es' => 'Servicio de habitaciones',
                    ),
                    array(
                        'en' => 'Cook Service',
                        'nl' => 'Kookservies',
                        'fr' => 'Service de cuisson',
                        'de' => 'Kochservice',
                        'ru' => 'Кулинарное обслуживание',
                        'es' => 'Servicio de cocina',
                    ),
                    array(
                        'en' => 'Car Rental',
                        'nl' => 'Autoverhuur',
                        'fr' => 'Location de voiture',
                        'de' => 'Autovermietung',
                        'ru' => 'Прокат автомобилей',
                        'es' => 'Alquiler de coches',
                    ),
                    array(
                        'en' => 'Door Man',
                        'nl' => 'Deurman',
                        'fr' => 'Portier',
                        'de' => 'Türmann',
                        'ru' => 'Дверной человек',
                        'es' => 'Portero',
                    ),
                    array(
                        'en' => 'Grocery',
                        'nl' => 'Kruidenier',
                        'fr' => 'Épicerie',
                        'de' => 'Lebensmittelgeschäft',
                        'ru' => 'Бакалея',
                        'es' => 'Tienda de comestibles',
                    ),
                    array(
                        'en' => 'Medical Assistance',
                        'nl' => 'Medische hulp',
                        'fr' => 'Assistance médicale',
                        'de' => 'Medizinische Hilfe',
                        'ru' => 'Медицинская помощь',
                        'es' => 'Asistencia médica',
                    ),
                    array(
                        'en' => 'Postal Services',
                        'nl' => 'Postdiensten',
                        'fr' => 'Services postaux',
                        'de' => 'Postdienste',
                        'ru' => 'Почтовые услуги',
                        'es' => 'Servicios postales',
                    ),
                    array(
                        'en' => 'Spa Services',
                        'nl' => 'Spadiensten',
                        'fr' => 'Services de spa',
                        'de' => 'Spa-Dienstleistungen',
                        'ru' => 'Услуги спа',
                        'es' => 'Servicios de spa',
                    ),
                    array(
                        'en' => 'Multilingual Staff',
                        'nl' => 'Meertalig personeel',
                        'fr' => 'Personnel multilingue',
                        'de' => 'Mehrsprachiges Personal',
                        'ru' => 'Многоязычный персонал',
                        'es' => 'Personal multilingüe',
                    ),
                ),
            ),
            'Indoors' => array(
                'name' => array(
                    'en' => 'Indoors',
                    'nl' => 'Binnenshuis',
                    'fr' => 'À l\'intérieur',
                    'de' => 'Innenbereich',
                    'ru' => 'В помещении',
                    'es' => 'Interior',
                ),
                'features' => array(
                    array(
                        'en' => 'Parking',
                        'nl' => 'Parkeren',
                        'fr' => 'Parking',
                        'de' => 'Parken',
                        'ru' => 'Парковка',
                        'es' => 'Aparcamiento',
                    ),
                    array(
                        'en' => 'Solarium',
                        'nl' => 'Solarium',
                        'fr' => 'Solarium',
                        'de' => 'Solarium',
                        'ru' => 'Солярий',
                        'es' => 'Solárium',
                    ),
                    array(
                        'en' => 'Veranda',
                        'nl' => 'Veranda',
                        'fr' => 'Véranda',
                        'de' => 'Veranda',
                        'ru' => 'Веранда',
                        'es' => 'Veranda',
                    ),
                ),
            ),
            'Internet' => array(
                'name' => array(
                    'en' => 'Internet',
                    'nl' => 'Internet',
                    'fr' => 'Internet',
                    'de' => 'Internet',
                    'ru' => 'Интернет',
                    'es' => 'Internet',
                ),
                'features' => array(
                    array(
                        'en' => 'Internet Access-Surcharge',
                        'nl' => 'Internettoegang - toeslag',
                        'fr' => 'Accès à Internet - supplément',
                        'de' => 'Internetzugang - Aufpreis',
                        'ru' => 'Доступ в Интернет - плата',
                        'es' => 'Acceso a Internet - Recargo',
                    ),
                    array(
                        'en' => 'Internet / Fax (Reception area only)',
                        'nl' => 'Internet / Fax (alleen receptie)',
                        'fr' => 'Internet / Fax (zone de réception uniquement)',
                        'de' => 'Internet / Fax (nur Empfangsbereich)',
                        'ru' => 'Интернет / Факс (только в зоне рецепции)',
                        'es' => 'Internet / Fax (solo en el área de recepción)',
                    ),
                ),
            ),
            'Outdoors' => array(
                'name' => array(
                    'en' => 'Outdoors',
                    'nl' => 'Buitenshuis',
                    'fr' => 'Extérieur',
                    'de' => 'Außenbereich',
                    'ru' => 'На открытом воздухе',
                    'es' => 'Al aire libre',
                ),
                'features' => array(
                    array(
                        'en' => 'Gardens',
                        'nl' => 'Tuinen',
                        'fr' => 'Jardins',
                        'de' => 'Gärten',
                        'ru' => 'Сады',
                        'es' => 'Jardines',
                    ),
                    array(
                        'en' => 'Outdoor Parking - Secured',
                        'nl' => 'Buitenparking - Beveiligd',
                        'fr' => 'Parking extérieur - Sécurisé',
                        'de' => 'Außenparkplatz - Gesichert',
                        'ru' => 'Открытая парковка - Обеспеченная безопасностью',
                        'es' => 'Aparcamiento al aire libre - Seguro',
                    ),
                    array(
                        'en' => 'Barbecue AreaCampfire / Bon Fire',
                        'nl' => 'Barbecueplaats / Kampvuur',
                        'fr' => 'Aire de barbecue / Feu de camp',
                        'de' => 'Grillplatz / Lagerfeuer',
                        'ru' => 'Место для барбекю / Костер',
                        'es' => 'Área de barbacoa / Fogata',
                    ),
                    array(
                        'en' => 'Childrens Park',
                        'nl' => 'Kinderpark',
                        'fr' => 'Parc pour enfants',
                        'de' => 'Kinderpark',
                        'ru' => 'Детский парк',
                        'es' => 'Parque infantil',
                    ),
                    array(
                        'en' => 'Fishing',
                        'nl' => 'Vissen',
                        'fr' => 'Pêche',
                        'de' => 'Angeln',
                        'ru' => 'Рыбалка',
                        'es' => 'Pesca',
                    ),
                    array(
                        'en' => 'Golf Course',
                        'nl' => 'Golfbaan',
                        'fr' => 'Terrain de golf',
                        'de' => 'Golfplatz',
                        'ru' => 'Поле для гольфа',
                        'es' => 'Campo de golf',
                    ),
                    array(
                        'en' => 'Outdoor Parking - Non Secured',
                        'nl' => 'Buitenparking - Niet beveiligd',
                        'fr' => 'Parking extérieur - Non sécurisé',
                        'de' => 'Außenparkplatz - Nicht gesichert',
                        'ru' => 'Открытая парковка - Небезопасная',
                        'es' => 'Aparcamiento al aire libre - No seguro',
                    ),
                    array(
                        'en' => 'Private Beach',
                        'nl' => 'Privéstrand',
                        'fr' => 'Plage privée',
                        'de' => 'Privatstrand',
                        'ru' => 'Частный пляж',
                        'es' => 'Playa privada',
                    ),
                    array(
                        'en' => 'Rooftop Garden',
                        'nl' => 'Daktuin',
                        'fr' => 'Jardin sur le toit',
                        'de' => 'Dachgarten',
                        'ru' => 'Крыша с садом',
                        'es' => 'Jardín en la azotea',
                    ),
                ),
            ),
            'Parking' => array(
                'name' => array(
                    'en' => 'Parking',
                    'nl' => 'Parkeren',
                    'fr' => 'Stationnement',
                    'de' => 'Parken',
                    'ru' => 'Парковка',
                    'es' => 'Aparcamiento',
                ),
                'features' => array(
                    array(
                        'en' => 'Parking (Surcharge)',
                        'nl' => 'Parkeren (toeslag)',
                        'fr' => 'Stationnement (payant)',
                        'de' => 'Parken (Aufpreis)',
                        'ru' => 'Парковка (платная)',
                        'es' => 'Aparcamiento (cargo adicional)',
                    ),
                    array(
                        'en' => 'Parking Facilities Available',
                        'nl' => 'Parkeervoorzieningen beschikbaar',
                        'fr' => 'Installations de stationnement disponibles',
                        'de' => 'Parkmöglichkeiten vorhanden',
                        'ru' => 'Доступные парковочные услуги',
                        'es' => 'Instalaciones de aparcamiento disponibles',
                    ),
                    array(
                        'en' => 'Valet service',
                        'nl' => 'Valetservice',
                        'fr' => 'Service de voiturier',
                        'de' => 'Parkservice',
                        'ru' => 'Сервис вахты',
                        'es' => 'Servicio de aparcacoches',
                    ),
                ),
            ),
            'Sports And Recreation' => array(
                'name' => array(
                    'en' => 'Sports And Recreation',
                    'nl' => 'Sport en recreatie',
                    'fr' => 'Sports et loisirs',
                    'de' => 'Sport und Freizeit',
                    'ru' => 'Спорт и отдых',
                    'es' => 'Deportes y recreación',
                ),
                'features' => array(
                    array(
                        'en' => 'Health Club / Gym Facility Available',
                        'nl' => 'Health Club / Sportschool beschikbaar',
                        'fr' => 'Club de santé / Installation de gym disponible',
                        'de' => 'Fitnessstudio / Fitnessanlage verfügbar',
                        'ru' => 'Фитнес-клуб / Тренажерный зал доступен',
                        'es' => 'Club de salud / Instalaciones de gimnasio disponibles',
                    ),
                    array(
                        'en' => 'Bike on Rent',
                        'nl' => 'Fietsverhuur',
                        'fr' => 'Location de vélo',
                        'de' => 'Fahrradverleih',
                        'ru' => 'Прокат велосипедов',
                        'es' => 'Alquiler de bicicletas',
                    ),
                    array(
                        'en' => 'Badminttion Court',
                        'nl' => 'Badmintonveld',
                        'fr' => 'Court de badminton',
                        'de' => 'Badmintonplatz',
                        'ru' => 'Бадминтонный корт',
                        'es' => 'Cancha de bádminton',
                    ),
                    array(
                        'en' => 'Basketball Court',
                        'nl' => 'Basketbalveld',
                        'fr' => 'Terrain de basket-ball',
                        'de' => 'Basketballplatz',
                        'ru' => 'Баскетбольная площадка',
                        'es' => 'Cancha de baloncesto',
                    ),
                    array(
                        'en' => 'Billiards',
                        'nl' => 'Biljart',
                        'fr' => 'Billard',
                        'de' => 'Billard',
                        'ru' => 'Бильярд',
                        'es' => 'Billar',
                    ),
                    array(
                        'en' => 'Boating',
                        'nl' => 'Varen',
                        'fr' => 'Navigation',
                        'de' => 'Bootfahren',
                        'ru' => 'Парусный спорт',
                        'es' => 'Navegación',
                    ),
                    array(
                        'en' => 'Bowling',
                        'nl' => 'Bowlen',
                        'fr' => 'Bowling',
                        'de' => 'Bowling',
                        'ru' => 'Боулинг',
                        'es' => 'Bolos',
                    ),
                    array(
                        'en' => 'Camel Ride',
                        'nl' => 'Kameel rijden',
                        'fr' => 'Balade à dos de chameau',
                        'de' => 'Kamelreiten',
                        'ru' => 'Прогулка на верблюде',
                        'es' => 'Paseo en camello',
                    ),
                    array(
                        'en' => 'Clubhouse',
                        'nl' => 'Clubhuis',
                        'fr' => 'Clubhouse',
                        'de' => 'Clubhaus',
                        'ru' => 'Клубный дом',
                        'es' => 'Club social',
                    ),
                    array(
                        'en' => 'Fitness Equipment',
                        'nl' => 'Fitnessapparatuur',
                        'fr' => 'Équipement de fitness',
                        'de' => 'Fitnessgeräte',
                        'ru' => 'Фитнес-оборудование',
                        'es' => 'Equipamiento de fitness',
                    ),
                    array(
                        'en' => 'Fun Floats',
                        'nl' => 'Plezier vlotten',
                        'fr' => 'Flotteurs amusants',
                        'de' => 'Spaßflöße',
                        'ru' => 'Плавучие аттракционы',
                        'es' => 'Flotadores divertidos',
                    ),
                    array(
                        'en' => 'Games Zone',
                        'nl' => 'Spelzone',
                        'fr' => 'Zone de jeux',
                        'de' => 'Spielezone',
                        'ru' => 'Игровая зона',
                        'es' => 'Zona de juegos',
                    ),
                    array(
                        'en' => 'Horse Ride ( Chargeable )',
                        'nl' => 'Paardrijden (tegen betaling)',
                        'fr' => 'Balade à cheval (payant)',
                        'de' => 'Reiten (gebührenpflichtig)',
                        'ru' => 'Прогулка на лошади (платно)',
                        'es' => 'Paseo a caballo (de pago)',
                    ),
                    array(
                        'en' => 'Marina On Site',
                        'nl' => 'Jachthaven ter plaatse',
                        'fr' => 'Marina sur place',
                        'de' => 'Marina vor Ort',
                        'ru' => 'Марина на месте',
                        'es' => 'Marina en el lugar',
                    ),
                    array(
                        'en' => 'Nature Walk',
                        'nl' => 'Natuurwandeling',
                        'fr' => 'Promenade dans la nature',
                        'de' => 'Naturwanderung',
                        'ru' => 'Прогулка по природе',
                        'es' => 'Paseo por la naturaleza',
                    ),
                    array(
                        'en' => 'Pool Table',
                        'nl' => 'Pooltafel',
                        'fr' => 'Table de billard',
                        'de' => 'Billardtisch',
                        'ru' => 'Бильярдный стол',
                        'es' => 'Mesa de billar',
                    ),
                    array(
                        'en' => 'Safari',
                        'nl' => 'Safari',
                        'fr' => 'Safari',
                        'de' => 'Safari',
                        'ru' => 'Сафари',
                        'es' => 'Safari',
                    ),
                    array(
                        'en' => 'Skiing Facility',
                        'nl' => 'Skifaciliteiten',
                        'fr' => 'Installations de ski',
                        'de' => 'Skianlage',
                        'ru' => 'Лыжные удобства',
                        'es' => 'Instalaciones de esquí',
                    ),
                    array(
                        'en' => 'Available Spa Services',
                        'nl' => 'Beschikbare spa-diensten',
                        'fr' => 'Services de spa disponibles',
                        'de' => 'Verfügbare Spa-Dienstleistungen',
                        'ru' => 'Доступные спа-услуги',
                        'es' => 'Servicios de spa disponibles',
                    ),
                    array(
                        'en' => 'Nearby Squash court',
                        'nl' => 'Dichtbij Squashbaan',
                        'fr' => 'Court de squash à proximité',
                        'de' => 'Nahe gelegener Squashplatz',
                        'ru' => 'Рядом с кортом для сквоша',
                        'es' => 'Pista de squash cercana',
                    ),
                    array(
                        'en' => 'Table Tennis',
                        'nl' => 'Tafeltennis',
                        'fr' => 'Tennis de table',
                        'de' => 'Tischtennis',
                        'ru' => 'Настольный теннис',
                        'es' => 'Tenis de mesa',
                    ),
                    array(
                        'en' => 'Tennis Court',
                        'nl' => 'Tennisveld',
                        'fr' => 'Court de tennis',
                        'de' => 'Tennisplatz',
                        'ru' => 'Теннисный корт',
                        'es' => 'Pista de tenis',
                    ),
                    array(
                        'en' => 'Virtual Golf',
                        'nl' => 'Virtueel golf',
                        'fr' => 'Golf virtuel',
                        'de' => 'Virtuelles Golf',
                        'ru' => 'Виртуальный гольф',
                        'es' => 'Golf virtual',
                    ),
                ),
            ),
            'Water Amenities' => array(
                'name' => array(
                    'en' => 'Water Amenities',
                    'nl' => 'Waterfaciliteiten',
                    'fr' => 'Équipements aquatiques',
                    'de' => 'Wasseranlagen',
                    'ru' => 'Водные удобства',
                    'es' => 'Instalaciones acuáticas',
                ),
                'features' => array(
                    array(
                        'en' => 'Swimming Pool',
                        'nl' => 'Zwembad',
                        'fr' => 'Piscine',
                        'de' => 'Schwimmbad',
                        'ru' => 'Бассейн',
                        'es' => 'Piscina',
                    ),
                    array(
                        'en' => 'Jacuzzi',
                        'nl' => 'Jacuzzi',
                        'fr' => 'Jacuzzi',
                        'de' => 'Jacuzzi',
                        'ru' => 'Джакузи',
                        'es' => 'Jacuzzi',
                    ),
                    array(
                        'en' => 'Private / Plunge Pool',
                        'nl' => 'Privé / Plonsbad',
                        'fr' => 'Piscine privée / à débordement',
                        'de' => 'Privat- / Tauchbecken',
                        'ru' => 'Частный / погружной бассейн',
                        'es' => 'Piscina privada / de inmersión',
                    ),
                    array(
                        'en' => 'Sauna',
                        'nl' => 'Sauna',
                        'fr' => 'Sauna',
                        'de' => 'Sauna',
                        'ru' => 'Сауна',
                        'es' => 'Sauna',
                    ),
                    array(
                        'en' => 'Whirlpool Bath / Shower Cubicle',
                        'nl' => 'Whirlpoolbad / Douche cabine',
                        'fr' => 'Bain à remous / Cabine de douche',
                        'de' => 'Whirlpool-Bad / Duschabtrennung',
                        'ru' => 'Ванна с гидромассажем / Душевая кабина',
                        'es' => 'Baño de hidromasaje / Cabina de ducha',
                    ),
                ),
            ),
            'Wine And Dine' => array(
                'name' => array(
                    'en' => 'Wine And Dine',
                    'nl' => 'Wijn en dineren',
                    'fr' => 'Vin et dîner',
                    'de' => 'Wein und Essen',
                    'ru' => 'Вино и ужин',
                    'es' => 'Vino y cena',
                ),
                'features' => array(
                    array(
                        'en' => 'Bar / Lounge',
                        'nl' => 'Bar / Lounge',
                        'fr' => 'Bar / Salon',
                        'de' => 'Bar / Lounge',
                        'ru' => 'Бар / Лаундж',
                        'es' => 'Bar / Salón',
                    ),
                    array(
                        'en' => 'Multi Cuisine Restaurant',
                        'nl' => 'Restaurant met meerdere keukens',
                        'fr' => 'Restaurant multi-cuisine',
                        'de' => 'Restaurant mit mehreren Küchen',
                        'ru' => 'Мультикультурный ресторан',
                        'es' => 'Restaurante de cocina variada',
                    ),
                    array(
                        'en' => 'Catering',
                        'nl' => 'Catering',
                        'fr' => 'Service traiteur',
                        'de' => 'Catering',
                        'ru' => 'Кейтеринг',
                        'es' => 'Catering',
                    ),
                    array(
                        'en' => 'Coffee Shop / Cafe',
                        'nl' => 'Koffieshop / Café',
                        'fr' => 'Café / Salon de thé',
                        'de' => 'Coffee Shop / Café',
                        'ru' => 'Кофейня / Кафе',
                        'es' => 'Cafetería / Café',
                    ),
                    array(
                        'en' => 'Food Facility',
                        'nl' => 'Eetgelegenheid',
                        'fr' => 'Installations alimentaires',
                        'de' => 'Essensmöglichkeit',
                        'ru' => 'Питание',
                        'es' => 'Instalaciones de comida',
                    ),
                    array(
                        'en' => 'Hookah Lounge',
                        'nl' => 'Shisha Lounge',
                        'fr' => 'Salon de narguilé',
                        'de' => 'Shisha-Lounge',
                        'ru' => 'Кальянная',
                        'es' => 'Sala de shisha',
                    ),
                    array(
                        'en' => 'Kitchen available (home cook food on request)',
                        'nl' => 'Keuken beschikbaar (thuis kookvoedsel op aanvraag)',
                        'fr' => 'Cuisine disponible (cuisine maison sur demande)',
                        'de' => 'Küche verfügbar (Hausmannskost auf Anfrage)',
                        'ru' => 'Доступная кухня (домашняя еда по запросу)',
                        'es' => 'Cocina disponible (comida casera bajo petición)',
                    ),
                    array(
                        'en' => 'Open Air Restaurant / Dining',
                        'nl' => 'Openluchtrestaurant / Dineren',
                        'fr' => 'Restaurant / Salle à manger en plein air',
                        'de' => 'Open-Air-Restaurant / Speisen',
                        'ru' => 'Открытый ресторан / Обед',
                        'es' => 'Restaurante al aire libre / Comedor',
                    ),
                    array(
                        'en' => 'Pool Cafe',
                        'nl' => 'Zwembadcafé',
                        'fr' => 'Café de la piscine',
                        'de' => 'Poolcafé',
                        'ru' => 'Кафе у бассейна',
                        'es' => 'Café de la piscina',
                    ),
                    array(
                        'en' => 'Poolside Bar',
                        'nl' => 'Zwembadbar',
                        'fr' => 'Bar de la piscine',
                        'de' => 'Poolbar',
                        'ru' => 'Бар у бассейна',
                        'es' => 'Bar de la piscina',
                    ),
                    array(
                        'en' => 'Restaurant Veg / Non Veg Kitchens Separate',
                        'nl' => 'Restaurant Veg / Non Veg Keukens Apart',
                        'fr' => 'Restaurant cuisines végétariennes / non végétariennes séparées',
                        'de' => 'Restaurant Veg / Non Veg Küchen getrennt',
                        'ru' => 'Рестораны Вег / Невег кухни отдельно',
                        'es' => 'Restaurante cocinas vegetales / no vegetarianas separadas',
                    ),
                    array(
                        'en' => 'Vegetarian Food / Jain Food Available',
                        'nl' => 'Vegetarisch eten / Jain eten beschikbaar',
                        'fr' => 'Nourriture végétarienne / Jain disponible',
                        'de' => 'Vegetarisches Essen / Jain Essen verfügbar',
                        'ru' => 'Вегетарианская пища / Джайн пища доступна',
                        'es' => 'Comida vegetariana / Comida jain disponible',
                    ),
                ),
            ),
        );
        // lang fields
        $languages = Language::getLanguages(false);
        $i = 1;
        foreach ($parent_features_arr as $key => $value) {
            $obj_feature = new HotelFeatures();
            foreach ($languages as $lang) {
                if (isset($value['name'][$lang['iso_code']])) {
                    $obj_feature->name[$lang['id_lang']] = $value['name'][$lang['iso_code']];
                } else {
                    $obj_feature->name[$lang['id_lang']] = $value['name']['en'];
                }
            }

            $obj_feature->active = 1;
            $obj_feature->position = $i;
            $obj_feature->parent_feature_id = 0;
            $obj_feature->save();
            $parent_feature_id = $obj_feature->id;
            foreach ($value['features'] as $val) {
                $obj_feature = new HotelFeatures();
                foreach ($languages as $lang) {
                    if (isset($val[$lang['iso_code']])) {
                        $obj_feature->name[$lang['id_lang']] = $val[$lang['iso_code']];
                    } else {
                        $obj_feature->name[$lang['id_lang']] = $val['en'];
                    }
                }
                $obj_feature->active = 1;
                $obj_feature->parent_feature_id = $parent_feature_id;
                $obj_feature->save();
            }
            ++$i;
        }

        return true;
    }

    public function insertDefaultHotelEntries()
    {
        //from setting tab
        $homeBannerContentLang = array(
            'en' => 'Our hotel is the perfect destination for both business and leisure travelers seeking a memorable stay.',
            'nl' => 'Ons hotel is de perfecte bestemming voor zowel zakenreizigers als vakantiegangers die op zoek zijn naar een onvergetelijk verblijf.',
            'fr' => 'Notre hôtel est la destination idéale pour les voyageurs d\'affaires et de loisirs à la recherche d\'un séjour mémorable.',
            'de' => 'Unser Hotel ist das perfekte Ziel für Geschäfts- und Urlaubsreisende, die einen unvergesslichen Aufenthalt wünschen.',
            'ru' => 'Наш отель является идеальным местом как для деловых путешественников, так и для туристов, желающих провести незабываемый отдых.',
            'es' => 'Nuestro hotel es el destino perfecto tanto para viajeros de negocios como de placer que buscan una estadía memorable.',
        );
        $homeBannerTitleLang = array(
            'en' => 'Hotel Prime',
            'nl' => 'Hotel Prime',
            'fr' => 'Hôtel Prime',
            'de' => 'Hotel Prime',
            'ru' => 'Отель Prime',
            'es' => 'Hotel Prime',
        );

        $objDefaultLanguage = new Language(Configuration::get('PS_LANG_DEFAULT'));
        $home_banner_default_content = $homeBannerContentLang['en'];
        $home_banner_default_title = $homeBannerTitleLang['en'];
        if (Validate::isLoadedObject($objDefaultLanguage) && isset($homeBannerContentLang[$objDefaultLanguage->iso_code])) {
            $home_banner_default_content = $homeBannerContentLang[$objDefaultLanguage->iso_code];
            $home_banner_default_title = $homeBannerTitleLang[$objDefaultLanguage->iso_code];
        }

        Configuration::updateValue('WK_HOTEL_LOCATION_ENABLE', 1);
        Configuration::updateValue('WK_HOTEL_NAME_ENABLE', 1);
        Configuration::updateValue('WK_HOTEL_NAME_SEARCH_THRESHOLD', 5);
        Configuration::updateValue('WK_SEARCH_AUTO_FOCUS_NEXT_FIELD', 1);
        Configuration::updateValue('WK_ROOM_LEFT_WARNING_NUMBER', 10);
        Configuration::updateValue('WK_HTL_ESTABLISHMENT_YEAR', 2010);

        Configuration::updateValue('PS_SHOP_ADDR1', 'The Hotel Prime, Monticello Dr, Montgomery, 10010');
        Configuration::updateValue('PS_SHOP_PHONE', '0987654321');
        Configuration::updateValue('PS_SHOP_EMAIL', 'hotelprime@htl.com');

        Configuration::updateValue('WK_CUSTOMER_SUPPORT_PHONE_NUMBER', '0987654321');
        Configuration::updateValue('WK_CUSTOMER_SUPPORT_EMAIL', 'hotelprime@htl.com');
        Configuration::updateValue('WK_DISPLAY_CONTACT_PAGE_HOTEL_LIST', 0);

        Configuration::updateValue('WK_TITLE_HEADER_BLOCK', $home_banner_default_title);
        Configuration::updateValue('WK_CONTENT_HEADER_BLOCK', $home_banner_default_content);
        Configuration::updateValue('WK_HOTEL_HEADER_IMAGE', 'hotel_header_image.jpg');
        Configuration::updateValue('WK_ALLOW_ADVANCED_PAYMENT', 1);
        Configuration::updateValue('WK_ADVANCED_PAYMENT_GLOBAL_MIN_AMOUNT', 10);
        Configuration::updateValue('WK_ADVANCED_PAYMENT_INC_TAX', 1);

        Configuration::updateValue('WK_GLOBAL_CHILD_MAX_AGE', 15);
        Configuration::updateValue('WK_GLOBAL_MAX_CHILD_IN_ROOM', 0);

        Configuration::updateValue('PS_MAX_CHECKOUT_OFFSET', 365);
        Configuration::updateValue('PS_MIN_BOOKING_OFFSET', 0);

        Configuration::updateValue('HTL_FEATURE_PRICING_PRIORITY', 'specific_date;special_day;date_range');
        Configuration::updateValue('WK_GOOGLE_ACTIVE_MAP', 0);
        Configuration::updateValue('WK_MAP_HOTEL_ACTIVE_ONLY', 1);

        // Prestashop logo's
        Configuration::updateValue('PS_LOGO', 'logo.jpg');
        Configuration::updateValue('PS_STORES_ICON', 'logo_stores.gif');
        Configuration::updateValue('PS_LOGO_MAIL', 'logo_mail.jpg');
        Configuration::updateValue('PS_LOGO_INVOICE', 'logo_invoice.jpg');

        // lang fields
        $languages = Language::getLanguages(false);
        $htlTagLineLang = array(
            'en' => 'A place where comfort and luxury are blended with nature!',
            'nl' => 'Een plek waar comfort en luxe worden gecombineerd met de natuur!',
            'fr' => 'Un endroit où le confort et le luxe se mêlent à la nature!',
            'de' => 'Ein Ort, an dem Komfort und Luxus mit der Natur verschmelzen!',
            'ru' => 'Место, где комфорт и роскошь сочетаются с природой!',
            'es' => '¡Un lugar donde el confort y el lujo se mezclan con la naturaleza!',
        );

        $htlShortDescLang = array(
            'en' => 'We offer elegant rooms, gourmet dining, and attentive service for a memorable stay.',
            'nl' => 'Wij bieden elegante kamers, gastronomisch dineren en attente service voor een onvergetelijk verblijf.',
            'fr' => 'Nous proposons des chambres élégantes, une cuisine gastronomique et un service attentionné pour un séjour mémorable.',
            'de' => 'Wir bieten elegante Zimmer, Gourmet-Restaurants und aufmerksamen Service für einen unvergesslichen Aufenthalt.',
            'ru' => 'Мы предлагаем элегантные номера, изысканную кухню и внимательное обслуживание для незабываемого отдыха.',
            'es' => 'Ofrecemos habitaciones elegantes, comidas gourmet y un servicio atento para una estadía inolvidable.',
        );
        $defaultDimensionUnitLang = array(
            'en' => 'ft',
            'nl' => 'ft',
            'fr' => 'pi',
            'de' => 'ft',
            'ru' => 'фут',
            'es' => 'ft',
        );
        $WK_HTL_CHAIN_NAME = array();
        $WK_HTL_TAG_LINE = array();
        $WK_HTL_SHORT_DESC = array();
        $defaultDimensionUnit = array();
        foreach ($languages as $lang) {
            if (isset($htlTagLineLang[$lang['iso_code']])) {
                $WK_HTL_TAG_LINE[$lang['id_lang']] = $htlTagLineLang[$lang['iso_code']];
                $WK_HTL_SHORT_DESC[$lang['id_lang']] = $htlShortDescLang[$lang['iso_code']];
                $WK_HTL_CHAIN_NAME[$lang['id_lang']] = $homeBannerTitleLang[$lang['iso_code']];
                $defaultDimensionUnit[$lang['id_lang']] = $defaultDimensionUnitLang[$lang['iso_code']];
            } else {
                $defaultDimensionUnit[$lang['id_lang']] = $defaultDimensionUnitLang['en'];
                $WK_HTL_CHAIN_NAME[$lang['id_lang']] = $homeBannerTitleLang['en'];
                $WK_HTL_TAG_LINE[$lang['id_lang']] = $htlTagLineLang['en'];
                $WK_HTL_SHORT_DESC[$lang['id_lang']] = $htlShortDescLang['en'];
            }
        }

        Configuration::updateValue('WK_HTL_CHAIN_NAME', $WK_HTL_CHAIN_NAME);
        Configuration::updateValue('WK_HTL_TAG_LINE', $WK_HTL_TAG_LINE);
        Configuration::updateValue('WK_HTL_SHORT_DESC', $WK_HTL_SHORT_DESC);
        Configuration::updateValue('WK_DIMENSION_UNIT', $defaultDimensionUnit);

        // Search Fields
        Configuration::updateValue('PS_FRONT_SEARCH_TYPE', HotelBookingDetail::SEARCH_TYPE_OWS);
        Configuration::updateValue('PS_FRONT_OWS_SEARCH_ALGO_TYPE', HotelBookingDetail::SEARCH_EXACT_ROOM_TYPE_ALGO);
        Configuration::updateValue('PS_FRONT_ROOM_UNIT_SELECTION_TYPE', HotelBookingDetail::PS_ROOM_UNIT_SELECTION_TYPE_OCCUPANCY);
        Configuration::updateValue('PS_BACKOFFICE_SEARCH_TYPE', HotelBookingDetail::SEARCH_TYPE_OWS);
        Configuration::updateValue('PS_BACKOFFICE_OWS_SEARCH_ALGO_TYPE', HotelBookingDetail::SEARCH_ALL_ROOM_TYPE_ALGO);
        Configuration::updateValue('PS_BACKOFFICE_ROOM_BOOKING_TYPE', HotelBookingDetail::PS_ROOM_UNIT_SELECTION_TYPE_OCCUPANCY);

        return true;
    }

    public function createHotelRoomDefaultFeatures()
    {
        $htl_room_ftrs = array(
            'Wi-Fi' => array(
                'en' => 'Wi-Fi',
                'nl' => 'Wi-Fi',
                'fr' => 'Wi-Fi',
                'de' => 'Wi-Fi',
                'ru' => 'Wi-Fi',
                'es' => 'Wi-Fi'
            ),
            'News Paper' => array(
                'en' => 'News Paper',
                'nl' => 'Krant',
                'fr' => 'Journal',
                'de' => 'Zeitung',
                'ru' => 'Газета',
                'es' => 'Periódico'
            ),
            'Power BackUp' => array(
                'en' => 'Power BackUp',
                'nl' => 'Stroomvoorziening',
                'fr' => 'Alimentation de secours',
                'de' => 'Notstromversorgung',
                'ru' => 'Резервное электропитание',
                'es' => 'Energía de respaldo'
            ),
            'Refrigerator' => array(
                'en' => 'Refrigerator',
                'nl' => 'Koelkast',
                'fr' => 'Réfrigérateur',
                'de' => 'Kühlschrank',
                'ru' => 'Холодильник',
                'es' => 'Refrigerador'
            ),
            'Restaurant' => array(
                'en' => 'Restaurant',
                'nl' => 'Restaurant',
                'fr' => 'Restaurant',
                'de' => 'Restaurant',
                'ru' => 'Ресторан',
                'es' => 'Restaurante'
            ),
            'Room Service' => array(
                'en' => 'Room Service',
                'nl' => 'Roomservice',
                'fr' => 'Service de chambre',
                'de' => 'Zimmerservice',
                'ru' => 'Обслуживание номеров',
                'es' => 'Servicio de habitaciones'
            ),
            'Gym' => array(
                'en' => 'Gym',
                'nl' => 'Fitnessruimte',
                'fr' => 'Salle de sport',
                'de' => 'Fitnessraum',
                'ru' => 'Фитнес',
                'es' => 'Gimnasio'
            )
        );

        // image value in rf/ folder
        $pos = 1;
        $languages = Language::getLanguages(true);
        foreach ($htl_room_ftrs as $room_ftr_k => $room_ftr_v) {
            $obj_feature = new Feature();
            foreach ($languages as $lang) {
                if (isset($room_ftr_v[$lang['iso_code']])) {
                    $obj_feature->name[$lang['id_lang']] = $room_ftr_v[$lang['iso_code']];
                } else {
                    $obj_feature->name[$lang['id_lang']] = $room_ftr_v['en'];
                }
            }
            $obj_feature->position = $pos-1;
            $obj_feature->save();
            if ($obj_feature->id) {
                $obj_feature_value = new FeatureValue();
                $obj_feature_value->id_feature = $obj_feature->id;

                foreach ($languages as $lang) {
                    $obj_feature_value->value[$lang['id_lang']] = $obj_feature->id.'.jpg';
                }

                $obj_feature_value->save();
                if ($obj_feature_value->id) {
                    if (file_exists(_PS_IMG_DIR_.'rf/'.$pos.'.jpg')) {
                        rename(_PS_IMG_DIR_.'rf/'.$pos.'.jpg', _PS_IMG_DIR_.'rf/'.$obj_feature->id.'.jpg');
                    }
                }
            }

            $pos++;
        }

        return true;
    }

    public function createHotelDefaultBedTypes()
    {
        $htlBedTypes = array(
            array(
                'length' => '6.25',
                'width'  => '3.16',
                'name' => array(
                    'en' => 'Twin Bed',
                    'nl' => 'Eenpersoonsbed',
                    'fr' => 'Lit simple',
                    'de' => 'Einzelbett',
                    'ru' => 'Односпальная кровать',
                    'es' => 'Cama individual',
                ),
            ),
            array(
                'length' => '6.66',
                'width'  => '3.16',
                'name' => array(
                    'en' => 'Twin XL Bed',
                    'nl' => 'Eenpersoonsbed XL',
                    'fr' => 'Lit simple XL',
                    'de' => 'Einzelbett XL',
                    'ru' => 'Односпальная кровать XL',
                    'es' => 'Cama individual XL',
                ),
            ),
            array(
                'length' => '6.25',
                'width'  => '4.5',
                'name' => array(
                    'en' => 'Full Bed',
                    'nl' => 'Tweepersoonsbed',
                    'fr' => 'Lit double',
                    'de' => 'Doppelbett',
                    'ru' => 'Двуспальная кровать',
                    'es' => 'Cama doble',
                ),
            ),
            array(
                'length' => '6.66',
                'width'  => '5',
                'name' => array(
                    'en' => 'Queen Bed',
                    'nl' => 'Queen size bed',
                    'fr' => 'Lit Queen',
                    'de' => 'Queen-Size-Bett',
                    'ru' => 'Кровать Queen Size',
                    'es' => 'Cama Queen',
                ),
            ),
            array(
                'length' => '6.66',
                'width'  => '6.33',
                'name' => array(
                    'en' => 'King Bed',
                    'nl' => 'King size bed',
                    'fr' => 'Lit King',
                    'de' => 'King-Size-Bett',
                    'ru' => 'Кровать King Size',
                    'es' => 'Cama King',
                ),
            ),
            array(
                'length' => '7',
                'width'  => '6',
                'name' => array(
                    'en' => 'California King Bed',
                    'nl' => 'California King bed',
                    'fr' => 'Lit California King',
                    'de' => 'California King-Bett',
                    'ru' => 'Калифорнийская кровать King Size',
                    'es' => 'Cama California King',
                ),
            ),
            array(
                'length' => '6.25',
                'width'  => '3.16',
                'name' => array(
                    'en' => 'Bunk Bed',
                    'nl' => 'Stapelbed',
                    'fr' => 'Lit superposé',
                    'de' => 'Etagenbett',
                    'ru' => 'Двухъярусная кровать',
                    'es' => 'Litera',
                ),
            ),
            array(
                'length' => '6.25',
                'width'  => '4.5',
                'name' => array(
                    'en' => 'Sofa Bed',
                    'nl' => 'Slaapbank',
                    'fr' => 'Canapé-lit',
                    'de' => 'Schlafsofa',
                    'ru' => 'Диван-кровать',
                    'es' => 'Sofá cama',
                ),
            ),
            array(
                'length' => '6.66',
                'width'  => '5',
                'name' => array(
                    'en' => 'Murphy Bed',
                    'nl' => 'Inklapbed',
                    'fr' => 'Lit escamotable',
                    'de' => 'Klappbett',
                    'ru' => 'Откидная кровать',
                    'es' => 'Cama abatible',
                ),
            ),
        );

        $languages = Language::getLanguages(true);
        foreach ($htlBedTypes as $htlBedType) {
            $objBedType = new HotelBedType();
            foreach ($languages as $lang) {
                if (isset($htlBedType['name'][$lang['iso_code']])) {
                    $objBedType->name[$lang['id_lang']] = $htlBedType['name'][$lang['iso_code']];
                } else {
                    $objBedType->name[$lang['id_lang']] = $htlBedType['name']['en'];
                }

                $objBedType->width = $htlBedType['width'];
                $objBedType->length = $htlBedType['length'];
                $objBedType->save();
            }
        }

        return true;
    }

    public static function getPsProducts($id_lang, $start = 0, $limit = 0, $booking_product = null)
    {
        $sql = 'SELECT p.`id_product`, pl.`name`, p.`booking_product`
            FROM `'._DB_PREFIX_.'product` p
            '.Shop::addSqlAssociation('product', 'p').'
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.
            Shop::addSqlRestrictionOnLang('pl').')
            WHERE pl.`id_lang` = '.(int)$id_lang.
            (isset($booking_product) ? ' AND p.`booking_product` = '.(int) $booking_product : '').'
            ORDER BY pl.`name`'.
            ($limit > 0 ? ' LIMIT '.(int)$start.','.(int)$limit : '');
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function saveDummyHotelBranchInfo()
    {
        $obj_hotel_info = new HotelBranchInformation();
        $obj_hotel_info->active = 1;
        $obj_hotel_info->email = 'hotelprime@htl.com';
        $obj_hotel_info->check_in = '12:00';
        $obj_hotel_info->check_out = '11:00';

        // lang fields
        $languages = Language::getLanguages(false);

        $htlShortDescLang = array(
            'en' => 'The Hotel Prime is the perfect destination for both business and leisure travelers seeking a memorable stay.',
            'nl' => 'Hotel Prime is de perfecte bestemming voor zowel zakenreizigers als vakantiegangers die op zoek zijn naar een onvergetelijk verblijf.',
            'fr' => 'L\'Hôtel Prime est la destination idéale pour les voyageurs d\'affaires et de loisirs à la recherche d\'un séjour mémorable.',
            'de' => 'Das Hotel Prime ist das perfekte Ziel für Geschäfts- und Urlaubsreisende, die einen unvergesslichen Aufenthalt wünschen.',
            'ru' => 'Отель Prime является идеальным местом для деловых путешественников и туристов, желающих провести незабываемый отдых.',
            'es' => 'El Hotel Prime es el destino perfecto tanto para viajeros de negocios como de placer que buscan una estancia memorable.',
        );

        $htlDescLang = array(
            'en' => '<div>
                <h4><strong>Welcome to The Hotel Prime!</strong></h4>
                <br />
                <div>Our hotel is the perfect destination for both business and leisure travelers seeking a memorable stay.</div>
                <br />
                <div>Benefits of staying at The Hotel Prime!</div>
                <br />
                <div><strong>Accommodation:</strong></div>
                <div>Indulge in our well-appointed rooms and suites, exquisitely designed to provide a tranquil haven after a long
                    day of exploration or meetings. Each room is tastefully furnished with modern amenities, including a plush bed,
                    a spacious work desk, high-speed Wi-Fi, a TV, and a private bathroom adorned with luxurious toiletries.</div>
                <br />
                <div><strong>Dining:</strong></div>
                <div>Savor a delightful culinary experience at our onsite restaurant, where our world-class chefs craft delectable
                    dishes using the finest ingredients. Whether you crave international flavors or local specialties, our diverse
                    menu is sure to satisfy every palate. Enjoy a romantic dinner for two or gather with friends and family in our
                    inviting dining ambiance.</div>
                <br />
                <div><strong>Facilities:</strong></div>
                <div>We believe in providing our guests with a range of facilities to enhance their stay. Take a refreshing dip in
                    our sparkling swimming pool, work up a sweat in our state-of-the-art fitness center, or unwind with a
                    rejuvenating spa treatment. Our attentive staff is always on hand to ensure your needs are met with utmost care
                    and professionalism.</div>
                <br />
                <div><strong>Events and Meetings:</strong></div>
                <div>Host your next corporate event or special occasion in our versatile event spaces, equipped with the latest
                    audiovisual technology and flexible seating arrangements. From intimate boardroom meetings to grand
                    celebrations, our dedicated event planners will assist you in creating a seamless and successful gathering.
                </div>
                <br />
                <div><strong>Location:</strong></div>
                <div>Conveniently located close to major attractions and business districts, our hotel offers easy access to
                    Alabamas vibrant shopping districts, cultural landmarks, and entertainment venues. Whether you\'re here for
                    business or leisure, our prime location ensures that you\'re never far from the action.</div>
                <br />
                <div><strong>Exceptional Service:</strong></div>
                <div>At The Hotel Prime, we take pride in delivering exceptional service to our guests. From the moment you step
                    through our doors, our friendly and knowledgeable staff will cater to your every need, ensuring a memorable and
                    personalized stay.</div>
                <br />
                <div><strong>Book Your Stay:</strong></div>
                <div>Ready to experience the epitome of luxury and comfort? Book your stay at The Hotel Prime today and let us
                    create an unforgettable experience for you. Whether you\'re traveling solo, with a loved one, or with a group,
                    our hotel is dedicated to surpassing your expectations and making your stay truly exceptional.</div></div>',
            'nl' => '<div>
                <h4><strong>Welkom bij Hotel Prime!</strong></h4>
                <br />
                <div>Ons hotel is de perfecte bestemming voor zowel zakenreizigers als vakantiegangers die op zoek zijn naar een onvergetelijk verblijf.</div>
                <br />
                <div>Voordelen van een verblijf in The Hotel Prime!</div>
                <br />
                <div><strong>Accommodatie:</strong></div>
                <div>Geniet van onze goed ingerichte kamers en suites, prachtig ontworpen om een ​​oase van rust te bieden na een lange tijd
                    dag van verkenning of ontmoetingen. Elke kamer is smaakvol ingericht met moderne voorzieningen, waaronder een zacht bed,
                    een ruim bureau, snelle WiFi, een televisie en een eigen badkamer versierd met luxe toiletartikelen.</div>
                <br />
                <div><strong>Eten:</strong></div>
                <div>Geniet van een heerlijke culinaire ervaring in ons restaurant, waar onze chef-koks van wereldklasse verrukkelijke gerechten bereiden
                    gerechten met de beste ingrediënten. Of u nu trek heeft in internationale smaken of lokale specialiteiten, ons assortiment is divers
                    menu zal zeker ieders smaak bevredigen. Geniet van een romantisch diner voor twee of kom samen met vrienden en familie in onze
                    uitnodigende eetsfeer.</div>
                <br />
                <div><strong>Faciliteiten:</strong></div>
                <div>Wij geloven in het bieden van een scala aan faciliteiten aan onze gasten om hun verblijf te verbeteren. Neem een ​​verfrissende duik
                    ons sprankelende zwembad, ga zweten in ons ultramoderne fitnesscentrum of ontspan met een
                    verjongende spabehandeling. Ons attente personeel staat altijd voor u klaar om ervoor te zorgen dat uw wensen met de grootste zorg worden vervuld
                    en professionaliteit.</div>
                <br />
                <div><strong>Evenementen en bijeenkomsten:</strong></div>
                <div>Organiseer uw volgende bedrijfsevenement of speciale gelegenheid in onze veelzijdige evenementenruimtes, uitgerust met de nieuwste apparatuur
                    audiovisuele technologie en flexibele zitopstellingen. Van intieme boardroom meetings tot groots
                    vieringen, zullen onze toegewijde evenementenplanners u helpen bij het creëren van een naadloze en succesvolle bijeenkomst.
                </div>
                <br />
                <div><strong>Locatie:</strong></div>
                <div>Ons hotel is gunstig gelegen dicht bij de belangrijkste bezienswaardigheden en zakenwijken en biedt gemakkelijke toegang
                    De levendige winkelwijken, culturele bezienswaardigheden en uitgaansgelegenheden van Alabama. Of je hier nu voor bent
                    zaken of vrije tijd, onze toplocatie zorgt ervoor dat u nooit ver verwijderd bent van de actie.</div>
                <br />
                <div><strong>Uitzonderlijke service:</strong></div>
                <div>Bij The Hotel Prime zijn we er trots op dat we onze gasten uitzonderlijke service kunnen bieden. Vanaf het moment dat je stapt
                    Via onze deuren zal ons vriendelijke en deskundige personeel aan al uw wensen voldoen en een onvergetelijke ervaring garanderen
                    gepersonaliseerd verblijf.</div>
                <br />
                <div><strong>Boek uw verblijf:</strong></div>
                <div>Klaar om het toppunt van luxe en comfort te ervaren? Boek vandaag nog uw verblijf in The Hotel Prime en laat het ons weten
                    een onvergetelijke ervaring voor u creëren. Of u nu alleen reist, met een geliefde of met een groep,
                    ons hotel doet er alles aan om uw verwachtingen te overtreffen en uw verblijf echt uitzonderlijk te maken.</div></div>',
            'fr' => '<div>
                <h4><strong>Bienvenue à l\'Hôtel Prime !</strong></h4>
                <br />
                <div>Notre hôtel est la destination idéale pour les voyageurs d\'affaires et de loisirs à la recherche d\'un séjour mémorable.</div>
                <br />
                <div>Avantages de séjourner à l\'Hôtel Prime !</div>
                <br />
                <div><strong>Hébergement :</strong></div>
                <div> Laissez-vous tenter par nos chambres et suites bien aménagées, superbement conçues pour offrir un havre de paix après une longue journée.
                    journée d\'exploration ou de rencontres. Chaque chambre est meublée avec goût et dotée d\'équipements modernes, notamment un lit moelleux,
                    un bureau de travail spacieux, une connexion Wi-Fi haut débit, une télévision et une salle de bains privative ornée d\'articles de toilette de luxe.</div>
                <br />
                <div><strong>Restauration :</strong></div>
                <div>Savourez une délicieuse expérience culinaire dans notre restaurant sur place, où nos chefs de classe mondiale préparent de délicieux plats.
                    des plats utilisant les meilleurs ingrédients. Que vous ayez envie de saveurs internationales ou de spécialités locales, nos diverses
                    Le menu saura satisfaire tous les palais. Profitez d\'un dîner romantique à deux ou réunissez-vous entre amis et en famille dans notre
                    ambiance de restauration invitante.</div>
                <br />
                <div><strong>Installations :</strong></div>
                <div>Nous croyons qu\'il est important de fournir à nos clients une gamme d\'installations pour améliorer leur séjour. Faites un plongeon rafraîchissant
                    notre piscine étincelante, transpirez dans notre centre de remise en forme ultramoderne ou détendez-vous avec un
                    soin spa rajeunissant. Notre personnel attentif est toujours disponible pour veiller à ce que vos besoins soient satisfaits avec le plus grand soin
                    et professionnalisme.</div>
                <br />
                <div><strong>Événements et réunions :</strong></div>
                <div>Organisez votre prochain événement d\'entreprise ou occasion spéciale dans nos espaces événementiels polyvalents, équipés des dernières technologies
                    technologie audiovisuelle et disposition flexible des sièges. Des réunions de conseil intimes aux grandes
                    célébrations, nos organisateurs d’événements dédiés vous aideront à créer un rassemblement fluide et réussi.
                </div>
                <br />
                <div><strong>Emplacement :</strong></div>
                <div>Idéalement situé à proximité des principales attractions et des quartiers d\'affaires, notre hôtel offre un accès facile à
                    Les quartiers commerçants animés, les monuments culturels et les lieux de divertissement de l\'Alabama. Que vous soyez ici pour
                    affaires ou loisirs, notre emplacement privilégié garantit que vous ne serez jamais loin de l\'action.</div>
                <br />
                <div><strong>Service exceptionnel :</strong></div>
                <div>À l\'Hôtel Prime, nous sommes fiers d\'offrir un service exceptionnel à nos clients. A partir du moment où tu fais un pas
                    franchissez nos portes, notre personnel amical et compétent répondra à tous vos besoins, vous assurant un séjour mémorable et
                    séjour personnalisé.</div>
                <br />
                <div><strong>Réservez votre séjour :</strong></div>
                <div>Prêt à découvrir la quintessence du luxe et du confort ? Réservez votre séjour à l\'Hôtel Prime dès aujourd\'hui et laissez-nous
                    créez une expérience inoubliable pour vous. Que vous voyagiez seul, en amoureux ou en groupe,
                    notre hôtel se consacre à dépasser vos attentes et à rendre votre séjour vraiment exceptionnel.</div></div>',
            'de' => '<div>
                <h4><strong>Willkommen im Hotel Prime!</strong></h4>
                <br />
                <div>Unser Hotel ist das perfekte Ziel für Geschäfts- und Urlaubsreisende, die einen unvergesslichen Aufenthalt suchen.</div>
                <br />
                <div>Vorteile eines Aufenthalts im Hotel Prime!</div>
                <br />
                <div><strong>Unterkunft:</strong></div>
                <div>Verwöhnen Sie sich in unseren gut ausgestatteten Zimmern und Suiten, die exquisit gestaltet sind, um nach einem langen Tag eine Oase der Ruhe zu bieten
                    Tag der Erkundung oder Treffen. Jedes Zimmer ist geschmackvoll eingerichtet und mit modernen Annehmlichkeiten ausgestattet, darunter ein bequemes Bett,
                    einen geräumigen Schreibtisch, Highspeed-WLAN, einen Fernseher und ein eigenes Badezimmer mit luxuriösen Toilettenartikeln.</div>
                <br />
                <div><strong>Essen:</strong></div>
                <div>Genießen Sie ein köstliches kulinarisches Erlebnis in unserem hauseigenen Restaurant, wo unsere erstklassigen Köche köstliche Gerichte zubereiten
                    Gerichte mit den besten Zutaten. Egal, ob Sie sich nach internationalen Aromen oder lokalen Spezialitäten sehnen, unser Angebot ist vielfältig
                    Das Menü wird mit Sicherheit jeden Gaumen zufrieden stellen. Genießen Sie ein romantisches Abendessen zu zweit oder treffen Sie sich mit Freunden und Familie in unserem
                    einladendes Speiseambiente.</div>
                <br />
                <div><strong>Einrichtungen:</strong></div>
                <div>Wir glauben daran, unseren Gästen eine Reihe von Einrichtungen zu bieten, um ihren Aufenthalt zu verbessern. Gönnen Sie sich ein erfrischendes Bad
                    Entspannen Sie sich in unserem glitzernden Swimmingpool, kommen Sie in unserem hochmodernen Fitnesscenter ins Schwitzen oder entspannen Sie sich bei einem
                    verjüngende Spa-Behandlung. Unser aufmerksames Personal ist stets für Sie da, um sicherzustellen, dass Ihre Bedürfnisse mit größter Sorgfalt erfüllt werden
                    und Professionalität.</div>
                <br />
                <div><strong>Veranstaltungen und Tagungen:</strong></div>
                <div>Veranstalten Sie Ihre nächste Firmenveranstaltung oder Ihren nächsten besonderen Anlass in unseren vielseitigen Veranstaltungsräumen, die mit der neuesten Ausstattung ausgestattet sind
                    audiovisuelle Technik und flexible Sitzordnung. Von intimen Vorstandssitzungen bis hin zu großen
                    Feierlichkeiten: Unsere engagierten Veranstaltungsplaner unterstützen Sie bei der Organisation einer reibungslosen und erfolgreichen Zusammenkunft.
                </div>
                <br />
                <div><strong>Standort:</strong></div>
                <div>Unser Hotel liegt günstig in der Nähe wichtiger Sehenswürdigkeiten und Geschäftsviertel und bietet einfachen Zugang zu diesen
                    Alabamas lebhafte Einkaufsviertel, kulturelle Sehenswürdigkeiten und Unterhaltungsmöglichkeiten. Ob Sie hier sind
                    Ob geschäftlich oder privat, unsere erstklassige Lage sorgt dafür, dass Sie nie weit vom Geschehen entfernt sind.</div>
                <br />
                <div><strong>Außergewöhnlicher Service:</strong></div>
                <div>Im Hotel Prime sind wir stolz darauf, unseren Gästen außergewöhnlichen Service zu bieten. Von dem Moment an, in dem Sie treten
                    Unser freundliches und sachkundiges Personal kümmert sich vor unseren Türen um alle Ihre Wünsche und sorgt für einen unvergesslichen und unvergesslichen Aufenthalt
                    personalisierter Aufenthalt.</div>
                <br />
                <div><strong>Buchen Sie Ihren Aufenthalt:</strong></div>
                <div>Sind Sie bereit, den Inbegriff von Luxus und Komfort zu erleben? Buchen Sie noch heute Ihren Aufenthalt im Hotel Prime und überlassen Sie es uns
                    Schaffen Sie ein unvergessliches Erlebnis für Sie. Egal, ob Sie alleine, mit einem geliebten Menschen oder mit einer Gruppe reisen,
                    Unser Hotel ist bestrebt, Ihre Erwartungen zu übertreffen und Ihren Aufenthalt wirklich außergewöhnlich zu machen.</div></div>',
            'ru' => '<div>
                <h4><strong>Добро пожаловать в отель Prime!</strong></h4>
                <br />
                <div>Наш отель — идеальное место как для деловых путешественников, так и для туристов, желающих провести незабываемый отдых.</div>
                <br />
                <div>Преимущества проживания в отеле Prime!</div>
                <br />
                <div><strong>Проживание:</strong></div>
                <div>Наслаждайтесь нашими хорошо оборудованными номерами и люксами, изысканно оформленными и создающими спокойную гавань после долгого отдыха.
                    день исследований или встреч. Каждый номер со вкусом обставлен и оснащен современными удобствами, включая роскошную кровать,
                    просторный рабочий стол, высокоскоростной Wi-Fi, телевизор и собственная ванная комната с роскошными туалетно-косметическими принадлежностями.</div>
                <br />
                <div><strong>Обед:</strong></div>
                <div>Наслаждайтесь восхитительными кулинарными впечатлениями в нашем ресторане, где наши повара мирового класса готовят восхитительные блюда.
                    блюда из лучших ингредиентов. Если вы жаждете блюд интернациональной кухни или местных деликатесов, наши разнообразные
                    Меню обязательно удовлетворит любой вкус. Наслаждайтесь романтическим ужином на двоих или соберитесь с друзьями и семьей в нашем ресторане.
                    уютная обеденная атмосфера.</div>
                <br />
                <div><strong>Удобства:</strong></div>
                <div>Мы стремимся предоставить нашим гостям широкий спектр услуг, которые сделают их пребывание еще лучше. Окунитесь в освежающую воду
                    сверкающий бассейн, потренируйтесь в современном фитнес-центре или расслабьтесь в
                    омолаживающая санаторно-курортная процедура. Наш внимательный персонал всегда готов позаботиться о том, чтобы ваши потребности были выполнены с максимальной заботой.
                    и профессионализм.</div>
                <br />
                <div><strong>Мероприятия и встречи:</strong></div>
                <div>Проведите свое следующее корпоративное мероприятие или особый случай в наших универсальных помещениях для проведения мероприятий, оснащенных новейшим оборудованием.
                    аудиовизуальные технологии и гибкая рассадка. От камерных заседаний совета директоров до грандиозных
                    Наши специалисты по организации мероприятий помогут вам организовать незабываемое и успешное мероприятие.
                </div>
                <br />
                <div><strong>Местоположение:</strong></div>
                <div>Наш отель удобно расположен недалеко от основных достопримечательностей и деловых районов.
                    Оживленные торговые районы Алабамы, культурные достопримечательности и развлекательные заведения. Независимо от того, пришли ли вы сюда
                    Для бизнеса или отдыха, наше превосходное расположение гарантирует, что вы всегда будете в центре событий.</div>
                <br />
                <div><strong>Исключительный сервис:</strong></div>
                <div>В отеле Prime мы гордимся тем, что предоставляем нашим гостям исключительный сервис. С того момента, как ты шагнешь
                    Через наши двери наш дружелюбный и знающий персонал удовлетворит все ваши потребности, гарантируя незабываемый и
                    индивидуальное пребывание.</div>
                <br />
                <div><strong>Забронируйте проживание:</strong></div>
                <div>Готовы ощутить воплощение роскоши и комфорта? Забронируйте проживание в отеле Prime сегодня и позвольте нам
                    подарит вам незабываемые впечатления. Путешествуете ли вы в одиночку, с любимым человеком или с группой,
                    Наш отель стремится превзойти ваши ожидания и сделать ваше пребывание по-настоящему исключительным.</div></div>',
            'es' => '<div>
                <h4><strong>¡Bienvenido a The Hotel Prime!</strong></h4>
                <br />
                <div>Nuestro hotel es el destino perfecto tanto para viajeros de negocios como de placer que buscan una estancia memorable.</div>
                <br />
                <div>¡Beneficios de alojarse en The Hotel Prime!</div>
                <br />
                <div><strong>Alojamiento:</strong></div>
                <div>Disfrute de nuestras habitaciones y suites bien equipadas, exquisitamente diseñadas para brindarle un refugio tranquilo después de un largo tiempo.
                    Día de exploración o reuniones. Cada habitación está elegantemente amueblada con comodidades modernas, que incluyen una cama lujosa,
                    un amplio escritorio, Wi-Fi de alta velocidad, TV y baño privado adornado con artículos de tocador de lujo.</div>
                <br />
                <div><strong>Comedor:</strong></div>
                <div>Saborea una deliciosa experiencia culinaria en nuestro restaurante, donde nuestros chefs de talla mundial elaboran platos deliciosos.
                    platos elaborados con los mejores ingredientes. Ya sea que le apetezcan sabores internacionales o especialidades locales, nuestra diversa
                    El menú seguramente satisfará todos los paladares. Disfrute de una cena romántica para dos o reúnase con amigos y familiares en nuestro
                    ambiente acogedor para cenar.</div>
                <br />
                <div><strong>Instalaciones:</strong></div>
                <div>Creemos en brindar a nuestros huéspedes una variedad de instalaciones para mejorar su estadía. Date un refrescante chapuzón en
                    nuestra espectacular piscina, haga ejercicio en nuestro gimnasio de última generación o relájese con un
                    Tratamiento de spa rejuvenecedor. Nuestro atento personal está siempre disponible para garantizar que sus necesidades se satisfagan con el máximo cuidado.
                    y profesionalismo.</div>
                <br />
                <div><strong>Eventos y reuniones:</strong></div>
                <div>Organiza tu próximo evento corporativo u ocasión especial en nuestros versátiles espacios para eventos, equipados con lo último
                    tecnología audiovisual y disposición flexible de los asientos. Desde reuniones íntimas en la sala de juntas hasta grandes
                    celebraciones, nuestros dedicados planificadores de eventos lo ayudarán a crear una reunión perfecta y exitosa.
                </div>
                <br />
                <div><strong>Ubicación:</strong></div>
                <div>Convenientemente ubicado cerca de las principales atracciones y distritos comerciales, nuestro hotel ofrece fácil acceso a
                    Los vibrantes distritos comerciales, lugares de interés cultural y lugares de entretenimiento de Alabama. Ya sea que estés aquí para
                    Por negocios o por placer, nuestra ubicación privilegiada garantiza que nunca estará lejos de la acción.</div>
                <br />
                <div><strong>Servicio excepcional:</strong></div>
                <div>En The Hotel Prime, nos enorgullecemos de brindar un servicio excepcional a nuestros huéspedes. Desde el momento en que das un paso
                    A través de nuestras puertas, nuestro personal amable y capacitado atenderá todas sus necesidades, garantizando una experiencia memorable y
                    estancia personalizada.</div>
                <br />
                <div><strong>Reserve su estancia:</strong></div>
                <div>¿Listo para experimentar el epítome del lujo y la comodidad? Reserva hoy tu estancia en The Hotel Prime y déjanos
                    Crea una experiencia inolvidable para ti. Ya sea que viaje solo, con un ser querido o en grupo,
                    nuestro hotel está dedicado a superar sus expectativas y hacer que su estadía sea realmente excepcional.</div></div>',
        );

        $htlPolicyLang = array(
            'en' => '<div>
                <div>- Accommodation will only be provided to guests whose details are registered with the hotel front desk.</div>
                <div>- Guests are required to show a valid photo identification during check-in.</div>
                <div>- GST / Taxes are charged extra and applicable as per government directives.</div>
                <div>- 100 % advance Payment deposit at the time of Check-in.</div>
                <div>- The check-in time is 12:00 PM &amp; check-out time is 11:00 AM. (Subject to availability, early check-in, and late check-out will be considered)</div>
                <div>- The hotel may deny further accommodation to a guest who does not prove to be decent and comply with the hotel policy and rules.</div>
                <div>- The guest has to bear any loss caused by them to the hotel property.</div></div>',
            'nl' => '<div>
                <div>- Accommodatie wordt alleen verstrekt aan gasten van wie de gegevens zijn geregistreerd bij de hotelreceptie.</div>
                <div>- Gasten zijn verplicht om tijdens het inchecken een geldig identiteitsbewijs met pasfoto te tonen.</div>
                <div>- GST/belastingen worden extra in rekening gebracht en zijn van toepassing volgens de richtlijnen van de overheid.</div>
                <div>- 100% vooruitbetaling bij het inchecken.</div>
                <div>- De inchecktijd is 12:00 uur en de uitchecktijd is 11:00 uur. (Onder voorbehoud van beschikbaarheid wordt vroeg inchecken en laat uitchecken in overweging genomen)</div>
                <div>- Het hotel kan verdere accommodatie weigeren aan een gast die niet fatsoenlijk blijkt te zijn en zich niet aan het hotelbeleid en de regels houdt.</div>
                <div>- De gast moet alle schade die hij veroorzaakt aan de eigendommen van het hotel zelf dragen.</div></div>',
            'fr' => '<div>
                <div>- L\'hébergement ne sera fourni qu\'aux clients dont les coordonnées sont enregistrées auprès de la réception de l\'hôtel.</div>
                <div>- Les clients doivent présenter une pièce d\'identité valide avec photo lors de l\'enregistrement.</div>
                <div>- La TPS/taxes sont facturées en sus et applicables selon les directives gouvernementales.</div>
                <div>- Dépôt de paiement anticipé de 100 % au moment de l\'enregistrement.</div>
                <div>- L\'heure d\'arrivée est à 12h00 et l\'heure de départ est à 11h00. (Sous réserve de disponibilité, l\'enregistrement anticipé et le départ tardif seront pris en compte)</div>
                <div>- L\'hôtel peut refuser un hébergement supplémentaire à un client qui ne s\'avère pas décent et ne respecte pas la politique et les règles de l\'hôtel.</div>
                <div>- Le client doit supporter toute perte causée par lui à la propriété de l\'hôtel.</div></div>',
            'de' => '<div>
                <div>- Die Unterbringung erfolgt nur an Gäste, deren Daten an der Hotelrezeption registriert sind.</div>
                <div>- Gäste müssen beim Check-in einen gültigen Lichtbildausweis vorlegen.</div>
                <div>- GST/Steuern werden zusätzlich berechnet und gelten gemäß den Regierungsrichtlinien.</div>
                <div>- 100 % Anzahlung beim Check-in.</div>
                <div>- Die Check-in-Zeit ist 12:00 Uhr und die Check-out-Zeit ist 11:00 Uhr. (Je nach Verfügbarkeit werden früher Check-in und später Check-out berücksichtigt)</div>
                <div>- Das Hotel kann einem Gast, der sich nicht anständig benimmt und sich nicht an die Hotelrichtlinien und -regeln hält, die weitere Unterbringung verweigern.</div>
                <div>- Der Gast hat den von ihm verursachten Schaden am Hoteleigentum zu tragen.</div></div>',
            'ru' => '<div>
                <div>- Проживание будет предоставлено только гостям, данные которых зарегистрированы на стойке регистрации отеля.</div>
                <div>- При регистрации заезда гостям необходимо предъявить действительное удостоверение личности с фотографией.</div>
                        <div>– GST/налоги взимаются дополнительно и применяются в соответствии с постановлениями правительства.</div>
                <div>- 100 % предоплата при заселении.</div>
                <div>- Время заезда — 12:00, время выезда — 11:00. (При наличии возможности рассматривается ранний заезд и поздний выезд)</div>
                <div>- Отель может отказать в дальнейшем размещении гостю, который не докажет себя порядочным и не соблюдает политику и правила отеля.</div>
                <div>- Гость должен нести любой ущерб, причиненный им имуществу отеля.</div></div>',
            'es' => '<div>
                <div>- Solo se proporcionará alojamiento a los huéspedes cuyos datos estén registrados en la recepción del hotel.</div>
                <div>- Los huéspedes deben mostrar una identificación con fotografía válida durante el check-in.</div>
                <div>- GST/Impuestos se cobran de forma adicional y se aplican según las directivas gubernamentales.</div>
                <div>- Depósito del 100 % del pago por adelantado al momento del Check-in.</div>
                <div>- La hora de entrada es a las 12:00 p. m. y la hora de salida es a las 11:00 a. m. (Sujeto a disponibilidad, se considerará check-in anticipado y check-out tardío)</div>
                <div>- El hotel puede negar alojamiento adicional a un huésped que no demuestre ser decente y no cumpla con la política y las reglas del hotel.</div>
                <div>- El huésped debe asumir cualquier pérdida que cause a la propiedad del hotel.</div></div>',
        );

        foreach ($languages as $lang) {
            $obj_hotel_info->hotel_name[$lang['id_lang']] = 'The Hotel Prime';
            if (isset($htlShortDescLang[$lang['iso_code']])) {
                $obj_hotel_info->policies[$lang['id_lang']] = $htlPolicyLang[$lang['iso_code']];
                $obj_hotel_info->description[$lang['id_lang']] = $htlDescLang[$lang['iso_code']];
                $obj_hotel_info->short_description[$lang['id_lang']] = $htlShortDescLang[$lang['iso_code']];
            } else {
                $obj_hotel_info->policies[$lang['id_lang']] = $htlPolicyLang['en'];
                $obj_hotel_info->description[$lang['id_lang']] = $htlDescLang['en'];
                $obj_hotel_info->short_description[$lang['id_lang']] = $htlShortDescLang['en'];
            }
        }

        $obj_hotel_info->rating = 3;

        $obj_hotel_info->save();
        $htl_id = $obj_hotel_info->id;

        // add hotel address info
        $def_cont_id = Configuration::get('PS_COUNTRY_DEFAULT');

        if ($states = State::getStatesByIdCountry($def_cont_id)) {
            $state_id = $states[0]['id_state'];
        } else {
            $state_id = 0;
        }
        $objAddress = new Address();
        $objAddress->id_hotel = $htl_id;
        $objAddress->phone = '0987654321';
        $objAddress->city = 'Demo City';
        $objAddress->id_state = $state_id;
        $objAddress->id_country = $def_cont_id;
        $objAddress->postcode = Tools::generateRandomZipcode($def_cont_id);
        $objAddress->address1 = 'Monticello Dr, Montgomery, 10010';
        $objAddress->alias = 'The Hotel Prime';
        $objAddress->lastname = 'The Hotel Prime';
        $objAddress->firstname = 'The Hotel Prime';

        $objAddress->save();

        $grp_ids = array();
        $obj_grp = new Group();
        $data_grp_ids = $obj_grp->getGroups(1, $id_shop = false);

        foreach ($data_grp_ids as $key => $value) {
            $grp_ids[] = $value['id_group'];
        }
        $obj_country = new Country();
        $country_name = $obj_country->getNameById(Configuration::get('PS_LANG_DEFAULT'), $def_cont_id);

        if ($cat_country = $this->addCategory(array('name' => $country_name, 'group_ids' => $grp_ids, 'parent_category' => false))) {
            $states = State::getStatesByIdCountry($def_cont_id);
            if (count($states) > 0) {
                $state_name = $states[0]['name'];
                $cat_state = $this->addCategory(array('name' => $state_name, 'group_ids' => $grp_ids, 'parent_category' => $cat_country));
            }
        }
        if (count($states) > 0) {
            if (!empty($cat_state)) {
                $cat_city = $this->addCategory(array('name' => 'Demo City', 'group_ids' => $grp_ids, 'parent_category' => $cat_state));
            }
        } else {
            $cat_city = $this->addCategory(array('name' => 'Demo City', 'group_ids' => $grp_ids, 'parent_category' => $cat_country));
        }
        if (!empty($cat_city)) {
            if ($cat_hotel = $this->addCategory(array(
                    'name' => 'The Hotel Prime',
                    'group_ids' => $grp_ids,
                    'parent_category' => $cat_city,
                    'is_hotel' => 1,
                    'id_hotel' => $htl_id
                )
            )) {
                $obj_hotel_info = new HotelBranchInformation($htl_id);
                $obj_hotel_info->id_category = $cat_hotel;
                $obj_hotel_info->save();
            }
        }
        // save dummy hotel as primary hotel
        Configuration::updateValue('WK_PRIMARY_HOTEL', $htl_id);

        return $htl_id;
    }

    public function saveDummyHotelFeatures($id_hotel)
    {
        $branch_ftr_ids = array(1, 2, 4, 7, 8, 9, 11, 12, 14, 16, 17, 18, 21);
        foreach ($branch_ftr_ids as $value_ftr) {
            $htl_ftr_obj = new HotelBranchFeatures();
            $htl_ftr_obj->id_hotel = $id_hotel;
            $htl_ftr_obj->feature_id = $value_ftr;
            $htl_ftr_obj->save();
        }
    }

    public function saveDummyProductsAndRelatedInfo($id_hotel)
    {
        $roomTypeDemoDataLang = array(
            array(
                'price' => 1000,
                'id_bed_types' => array(4),
                'en' => array(
                    'name' => 'General Rooms',
                    'description_short' => 'Our General Rooms offer space and comfort with multiple bedrooms and a cozy living area. Enjoy flat-screen TVs, complimentary Wi-Fi, and a kitchenette for a perfect family getaway.',
                    'description' => 'Make yourself at home in our spacious General Rooms, tailored for families or groups seeking comfort and convenience. This accommodation offers multiple bedrooms, ensuring everyone has their own space to relax and recharge. The cozy living area is perfect for bonding over games or movie nights, while the well-equipped kitchenette allows for effortless meal preparation. Each room is equipped with flat-screen TVs and complimentary Wi-Fi, ensuring everyone stays entertained and connected throughout their stay. Ideal for creating lasting memories together in a setting of warmth and hospitality.'
                ),
                'nl' => array(
                    'name' => 'Algemene Kamers',
                    'description_short' => 'Onze Algemene Kamers bieden ruimte en comfort met meerdere slaapkamers en een gezellig woongedeelte. Geniet van flatscreen-tv\'s, gratis wifi en een kitchenette voor een perfect uitje met het gezin.',
                    'description' => 'Doe alsof u thuis bent in onze ruime Algemene Kamers, op maat gemaakt voor gezinnen of groepen die op zoek zijn naar comfort en gemak. Deze accommodatie beschikt over meerdere slaapkamers, zodat iedereen zijn eigen ruimte heeft om te ontspannen en op te laden. De gezellige woonkamer is perfect voor een gezellig samenzijn tijdens spelletjes of filmavonden, terwijl de goed uitgeruste keuken een moeiteloze maaltijdbereiding mogelijk maakt. Elke kamer is uitgerust met een flatscreen-tv en gratis WiFi, zodat iedereen zich tijdens zijn verblijf kan vermaken en verbonden blijft. Ideaal om samen blijvende herinneringen te creëren in een sfeer van warmte en gastvrijheid.'
                ),
                'fr' => array(
                    'name' => 'Salle générale',
                    'description_short' => 'Nos chambres générales offrent espace et confort avec plusieurs chambres et un salon confortable. Profitez d\'une télévision à écran plat, d\'une connexion Wi-Fi gratuite et d\'une kitchenette pour une escapade parfaite en famille.',
                    'description' => 'Faites comme chez vous dans nos chambres générales spacieuses, conçues pour les familles ou les groupes en quête de confort et de commodité. Cet hébergement propose plusieurs chambres, garantissant à chacun son propre espace pour se détendre et se ressourcer. Le salon confortable est parfait pour créer des liens lors de jeux ou de soirées cinéma, tandis que la kitchenette bien équipée permet de préparer des repas sans effort. Chaque chambre est équipée d\'une télévision à écran plat et d\'une connexion Wi-Fi gratuite, garantissant que chacun reste diverti et connecté tout au long de son séjour. Idéal pour créer ensemble des souvenirs impérissables dans un cadre chaleureux et hospitalier.'
                ),
                'de' => array(
                    'name' => 'Allgemeine Räume',
                    'description_short' => 'Unsere Allgemeinzimmer bieten Platz und Komfort mit mehreren Schlafzimmern und einem gemütlichen Wohnbereich. Genießen Sie Flachbildfernseher, kostenloses WLAN und eine Küchenzeile für einen perfekten Familienurlaub.',
                    'description' => 'Fühlen Sie sich in unseren geräumigen Allgemeinzimmern wie zu Hause, die auf Familien oder Gruppen zugeschnitten sind, die Komfort und Bequemlichkeit suchen. Diese Unterkunft verfügt über mehrere Schlafzimmer, sodass jeder seinen eigenen Raum zum Entspannen und Erholen hat. Der gemütliche Wohnbereich eignet sich perfekt zum geselligen Beisammensein bei Spielen oder Filmabenden, während die gut ausgestattete Küchenzeile die mühelose Zubereitung von Mahlzeiten ermöglicht. Jedes Zimmer ist mit Flachbildfernsehern und kostenfreiem WLAN ausgestattet, sodass jeder während seines gesamten Aufenthalts unterhalten und verbunden bleibt. „Ideal, um gemeinsam bleibende Erinnerungen in einer Atmosphäre voller Herzlichkeit und Gastfreundschaft zu schaffen.“',
                ),
                'ru' => array(
                    'name' => 'Общие помещения',
                    'description_short' => 'Наши общие номера отличаются простором и комфортом, они состоят из нескольких спален и уютной гостиной. Наслаждайтесь телевизорами с плоским экраном, бесплатным Wi-Fi и мини-кухней для идеального семейного отдыха.',
                    'description' => 'Чувствуйте себя как дома в наших просторных общих номерах, специально предназначенных для семей или групп, ищущих комфорт и удобство. В этом отеле есть несколько спален, поэтому у каждого будет свое пространство для отдыха и восстановления сил. Уютная гостиная идеально подходит для общения за играми или просмотра кино, а хорошо оборудованная мини-кухня позволяет без труда приготовить еду. В каждом номере есть телевизор с плоским экраном и бесплатный Wi-Fi, благодаря чему каждый сможет развлечься и оставаться на связи на протяжении всего своего пребывания. Идеально подходит для создания незабываемых воспоминаний вместе в обстановке тепла и гостеприимства».'
                ),
                'es' => array(
                    'name' => 'Habitaciones Generales',
                    'description_short' => 'Nuestras habitaciones generales ofrecen espacio y comodidad con múltiples dormitorios y una acogedora sala de estar. Disfrute de televisores de pantalla plana, Wi-Fi gratuito y una pequeña cocina para una escapada familiar perfecta.',
                    'description' => 'Siéntase como en casa en nuestras espaciosas habitaciones generales, diseñadas para familias o grupos que buscan comodidad y conveniencia. Este alojamiento ofrece varias habitaciones, lo que garantiza que todos tengan su propio espacio para relajarse y recargar energías. La acogedora sala de estar es perfecta para compartir juegos o noches de cine, mientras que la cocina bien equipada permite preparar comidas sin esfuerzo. Cada habitación está equipada con televisores de pantalla plana y Wi-Fi gratuito, lo que garantiza que todos se mantengan entretenidos y conectados durante su estancia. Ideal para crear juntos recuerdos duraderos en un ambiente de calidez y hospitalidad.'
                ),
            ),
            array(
                'price' => 1500,
                'id_bed_types' => array(4, 5),
                'en' => array(
                    'name' => 'Delux Rooms',
                    'description_short' => 'Enjoy lake views from our Deluxe Rooms with a king-sized bed, elegant furnishings, and a spacious sitting area. Perfect for guests seeking comfort, luxury, and modern amenities.',
                    'description' => 'Experience the epitome of luxury in our Deluxe Room. Gaze out over serene waters from the comfort of your spacious accommodation, furnished with a plush king-sized bed and elegant decor. Whether unwinding in the sitting area or enjoying modern amenities like complimentary Wi-Fi and a flat-screen TV, every detail ensures your stay is both relaxing and indulgent. Perfect for guests seeking a peaceful retreat with breathtaking views and refined comfort.'
                ),
                'nl' => array(
                    'name' => 'Deluxe Kamers',
                    'description_short' => 'Geniet van uitzicht op het meer vanuit onze Deluxe kamers met een kingsize bed, elegant meubilair en een ruime zithoek. Perfect voor gasten die op zoek zijn naar comfort, luxe en moderne voorzieningen.',
                    'description' => 'Ervaar het toppunt van luxe in onze Deluxe Kamer. Kijk uit over het serene water vanuit het comfort van uw ruime accommodatie, ingericht met een luxe kingsize bed en een elegante inrichting. Of u nu ontspant in de zithoek of geniet van moderne voorzieningen zoals gratis Wi-Fi en een flatscreen-tv, elk detail zorgt ervoor dat uw verblijf zowel ontspannend als toegeeflijk is. Perfect voor gasten die op zoek zijn naar een rustig toevluchtsoord met een adembenemend uitzicht en verfijnd comfort.'
                ),
                'fr' => array(
                    'name' => 'Chambres Delux',
                    'description_short' => 'Profitez de la vue sur le lac depuis nos chambres de luxe dotées d\'un lit king-size, d\'un mobilier élégant et d\'un coin salon spacieux. Parfait pour les clients à la recherche de confort, de luxe et d\'équipements modernes.',
                    'description' => 'Découvrez le summum du luxe dans notre chambre Deluxe. Admirez les eaux sereines depuis le confort de votre hébergement spacieux, doté d\'un lit king-size moelleux et d\'une décoration élégante. Que vous vous détendiez dans le coin salon ou profitiez d\'équipements modernes comme une connexion Wi-Fi gratuite et une télévision à écran plat, chaque détail garantit que votre séjour soit à la fois relaxant et indulgent. Parfait pour les clients recherchant une retraite paisible avec des vues à couper le souffle et un confort raffiné.'
                ),
                'de' => array(
                    'name' => 'Delux-Zimmer',
                    'description_short' => 'Genießen Sie den Seeblick von unseren Deluxe-Zimmern mit einem Kingsize-Bett, eleganten Möbeln und einem geräumigen Sitzbereich. Perfekt für Gäste, die Komfort, Luxus und moderne Annehmlichkeiten suchen.',
                    'description' => 'Erleben Sie den Inbegriff von Luxus in unserem Deluxe-Zimmer. Von Ihrer geräumigen Unterkunft aus, die mit einem bequemen Kingsize-Bett und elegantem Dekor ausgestattet ist, blicken Sie auf das ruhige Wasser. Ob Sie sich in der Sitzecke entspannen oder moderne Annehmlichkeiten wie kostenloses WLAN und einen Flachbildfernseher genießen – jedes Detail sorgt dafür, dass Ihr Aufenthalt sowohl entspannend als auch genussvoll ist. „Perfekt für Gäste, die einen ruhigen Rückzugsort mit atemberaubender Aussicht und raffiniertem Komfort suchen.“'
                ),
                'ru' => array(
                    'name' => 'Номера Делюкс',
                    'description_short' => 'Наслаждайтесь видом на озеро из наших номеров Делюкс с кроватью размера «king-size», элегантной мебелью и просторной зоной отдыха. Идеально подходит для гостей, которые ищут комфорт, роскошь и современные удобства.',
                    'description' => 'Ощутите воплощение роскоши в нашем номере Делюкс. Полюбуйтесь безмятежными водами, не выходя из просторного номера с роскошной кроватью размера «king-size» и элегантным декором. Независимо от того, расслабляетесь ли вы в зоне отдыха или наслаждаетесь современными удобствами, такими как бесплатный Wi-Fi и телевизор с плоским экраном, каждая деталь гарантирует, что ваше пребывание будет одновременно расслабляющим и приятным. Идеально подходит для гостей, ищущих спокойный отдых с захватывающими дух видами и изысканным комфортом».'
                ),
                'es' => array(
                    'name' => 'Habitaciones Delux',
                    'description_short' => 'Disfrute de la vista al lago desde nuestras habitaciones Deluxe con una cama King, muebles elegantes y una amplia sala de estar. Perfecto para huéspedes que buscan comodidad, lujo y comodidades modernas.',
                    'description' => 'Experimenta el máximo lujo en nuestra habitación Deluxe. Contemple las aguas serenas desde la comodidad de su espacioso alojamiento, amueblado con una lujosa cama tamaño king y una decoración elegante. Ya sea que se relaje en la sala de estar o disfrute de comodidades modernas como Wi-Fi de cortesía y un televisor de pantalla plana, cada detalle garantiza que su estadía sea relajante e indulgente. Perfecto para huéspedes que buscan un retiro tranquilo con vistas impresionantes y un confort refinado.'
                ),
            ),
            array(
                'price' => 2000,
                'id_bed_types' => array(5, 6),
                'en' => array(
                    'name' => 'Executive Rooms',
                    'description_short' => 'Indulge in our Executive Rooms, featuring separate living and sleeping areas, a luxurious bathroom, and exclusive lounge access. Ideal for business travelers seeking privacy',
                    'description' => 'Elevate your stay with the expansive comfort of our Executive Room. Designed for discerning travelers, this room features separate living and sleeping areas adorned with sophisticated furnishings and luxurious touches. Pamper yourself in the deluxe bathroom, complete with a soaking tub and premium toiletries. Enjoy exclusive access to our executive lounge for unwinding with complimentary refreshments and tranquil surroundings. Ideal for business travelers or those seeking extra space and privacy in a setting of unparalleled sophistication.'
                ),
                'nl' => array(
                    'name' => 'Executive Kamers',
                    'description_short' => 'Verwen uzelf met onze Executive Kamers, met aparte woon- en slaapgedeeltes, een luxe badkamer en exclusieve toegang tot de lounge. Ideaal voor zakenreizigers die op zoek zijn naar privacy',
                    'description' => 'Verhoog uw verblijf met het uitgebreide comfort van onze Executive Kamer. Deze kamer is ontworpen voor veeleisende reizigers en beschikt over aparte woon- en slaapgedeeltes, versierd met verfijnd meubilair en luxe accenten. Verwen uzelf in de luxe badkamer, compleet met een ligbad en luxe toiletartikelen. Geniet van exclusieve toegang tot onze executive lounge om te ontspannen met gratis drankjes en een rustige omgeving. Ideaal voor zakenreizigers of mensen die op zoek zijn naar extra ruimte en privacy in een omgeving van ongeëvenaarde verfijning.'
                ),
                'fr' => array(
                    'name' => 'Chambres Exécutives',
                    'description_short' => 'Offrez-vous nos chambres exécutives, dotées d\'espaces de vie et de couchage séparés, d\'une salle de bains luxueuse et d\'un accès exclusif au salon. Idéal pour les voyageurs d\'affaires en quête d\'intimité',
                    'description' => 'Élevez votre séjour avec le grand confort de notre chambre exécutive. Conçue pour les voyageurs exigeants, cette chambre dispose d\'espaces de vie et de couchage séparés ornés d\'un mobilier sophistiqué et de touches luxueuses. Prenez soin de vous dans la salle de bains de luxe dotée d\'une baignoire profonde et d\'articles de toilette haut de gamme. Profitez d\'un accès exclusif à notre salon exécutif pour vous détendre avec des rafraîchissements gratuits et un cadre tranquille. Idéal pour les voyageurs d\'affaires ou ceux qui recherchent plus d\'espace et d\'intimité dans un cadre d\'une sophistication sans précédent.'
                ),
                'de' => array(
                    'name' => 'Executive Rooms',
                    'description_short' => 'Verwöhnen Sie sich in unseren Executive-Zimmern mit separaten Wohn- und Schlafbereichen, einem luxuriösen Badezimmer und exklusivem Zugang zur Lounge. „Ideal für Geschäftsreisende, die Privatsphäre suchen“',
                    'description' => 'Erhöhen Sie Ihren Aufenthalt mit dem großzügigen Komfort unseres Executive-Zimmers. Dieses für anspruchsvolle Reisende konzipierte Zimmer verfügt über separate Wohn- und Schlafbereiche, die mit anspruchsvollen Möbeln und luxuriösen Details ausgestattet sind. Verwöhnen Sie sich im luxuriösen Badezimmer mit Badewanne und hochwertigen Pflegeprodukten. Genießen Sie exklusiven Zugang zu unserer Executive Lounge zum Entspannen bei kostenlosen Erfrischungen und einer ruhigen Umgebung. „Ideal für Geschäftsreisende oder diejenigen, die zusätzlichen Platz und Privatsphäre in einer Umgebung von unvergleichlicher Eleganz suchen.“',
                ),
                'ru' =>  array(
                    'name' => 'Представительские номера',
                    'description_short' => 'Наслаждайтесь нашими представительскими номерами с отдельной гостиной и спальней, роскошной ванной комнатой и эксклюзивным доступом в лаунж-зону. Идеально подходит для деловых путешественников, стремящихся к конфиденциальности»',
                    'description' => 'Повысьте качество вашего пребывания благодаря исключительному комфорту нашего представительского номера. Этот номер, предназначенный для взыскательных путешественников, располагает отдельной гостиной и спальной зоной, украшенной изысканной мебелью и роскошными деталями. Побалуйте себя в роскошной ванной комнате с глубокой ванной и туалетными принадлежностями премиум-класса. Воспользуйтесь эксклюзивным доступом в представительский лаундж, чтобы расслабиться с бесплатными закусками и спокойной обстановкой. Идеально подходит для деловых путешественников или тех, кто ищет дополнительное пространство и уединение в обстановке беспрецедентной изысканности»'
                ),
                'es' => array(
                    'name' => 'Salas Ejecutivas',
                    'description_short' => 'Disfrute de nuestras habitaciones ejecutivas, que cuentan con sala de estar y dormitorio independientes, un lujoso baño y acceso exclusivo al salón. Ideal para viajeros de negocios que buscan privacidad',
                    'description' => 'Mejore su estadía con la amplia comodidad de nuestra habitación ejecutiva. Diseñada para viajeros exigentes, esta habitación cuenta con áreas de estar y de dormitorio independientes adornadas con muebles sofisticados y toques de lujo. Mímese en el baño de lujo, completo con una bañera profunda y artículos de tocador de primera calidad. Disfrute de acceso exclusivo a nuestro salón ejecutivo para relajarse con refrigerios de cortesía y un entorno tranquilo. Ideal para viajeros de negocios o aquellos que buscan espacio adicional y privacidad en un entorno de sofisticación incomparable.'
                ),
            ),
            array(
                'price' => 2500,
                'id_bed_types' => array(6, 8),
                'en' => array(
                    'name' => 'Luxury Rooms',
                    'description_short' => 'Retreat to tranquility in our Luxury Rooms with expansive views. Featuring a queen-sized bed, workspace, and serene decor, perfect for business and leisure travelers alike.',
                    'description' => 'Indulge in ultimate tranquility in our Luxury Room, where expansive vistas and serene surroundings create a peaceful oasis. Wake up to panoramic views complemented by elegant decor and a comfortable queen-sized bed. Whether catching up on work at the well-appointed workspace or simply relaxing in serene ambiance, this room offers a luxurious retreat for both business and leisure travelers alike. Perfect for those seeking comfort, style, and breathtaking views in an unparalleled setting.'
                ),
                'nl' => array(
                    'name' => 'Luxe Kamers',
                    'description_short' => 'Retraite in de rust in onze luxe kamers met weids uitzicht. Met een queensize bed, werkruimte en een serene inrichting, perfect voor zowel zakenreizigers als vakantiegangers.',
                    'description' => 'Geniet van ultieme rust in onze Luxe Kamer, waar weidse vergezichten en een serene omgeving een oase van rust creëren. Word wakker met een panoramisch uitzicht, aangevuld met een elegante inrichting en een comfortabel queensize bed. Of u nu aan het werk bent in de goed uitgeruste werkruimte of gewoon wilt ontspannen in een serene sfeer, deze kamer biedt een luxe toevluchtsoord voor zowel zakenreizigers als vakantiegangers. Perfect voor wie op zoek is naar comfort, stijl en adembenemende uitzichten in een ongeëvenaarde omgeving.'
                ),
                'fr' => array(
                    'name' => 'Chambres de luxe',
                    'description_short' => 'Retraite dans la tranquillité dans nos chambres de luxe offrant une vue imprenable. Doté d\'un lit queen-size, d\'un espace de travail et d\'un décor serein, parfait pour les voyageurs d\'affaires et de loisirs.',
                    'description' => 'Offrez-vous une tranquillité ultime dans notre chambre de luxe, où de vastes vues et un environnement serein créent une oasis de paix. Réveillez-vous avec une vue panoramique complétée par un décor élégant et un lit queen-size confortable. Qu\'il s\'agisse de travailler dans un espace de travail bien aménagé ou simplement de se détendre dans une ambiance sereine, cette chambre offre un refuge luxueux aux voyageurs d\'affaires et de loisirs. Parfait pour ceux qui recherchent confort, style et vues à couper le souffle dans un cadre sans précédent.'
                ),
                'de' => array(
                    'name' => 'Luxuszimmer',
                    'description_short' => 'Ziehen Sie sich in unseren Luxuszimmern mit weitem Ausblick in die Ruhe zurück. Ausgestattet mit einem Queensize-Bett, einem Arbeitsbereich und einer ruhigen Einrichtung, perfekt für Geschäfts- und Urlaubsreisende gleichermaßen.',
                    'description' => 'Gönnen Sie sich ultimative Ruhe in unserem Luxuszimmer, wo weite Ausblicke und eine ruhige Umgebung eine friedliche Oase schaffen. Erwachen Sie mit Panoramablick, ergänzt durch elegantes Dekor und ein bequemes Queensize-Bett. Ganz gleich, ob Sie im gut ausgestatteten Arbeitsbereich Ihrer Arbeit nachgehen oder einfach in ruhiger Atmosphäre entspannen, dieser Raum bietet einen luxuriösen Rückzugsort für Geschäfts- und Urlaubsreisende gleichermaßen. „Perfekt für alle, die Komfort, Stil und atemberaubende Ausblicke in einer unvergleichlichen Umgebung suchen.“'
                ),
                'ru' => array(
                    'name' => 'Роскошные номера',
                    'description_short' => 'Погрузитесь в спокойствие в наших роскошных номерах с потрясающим видом. Кровать размера «queen-size», рабочее место и спокойный декор идеально подходят как для деловых путешественников, так и для туристов.',
                    'description' => 'Наслаждайтесь абсолютным спокойствием в нашем роскошном номере, где обширные виды и безмятежная обстановка создают умиротворяющий оазис. Просыпайтесь, наслаждаясь панорамным видом, дополненным элегантным декором и удобной кроватью размера «queen-size». Независимо от того, занимаетесь ли вы работой в хорошо оборудованном рабочем месте или просто отдыхаете в безмятежной обстановке, этот номер станет роскошным местом отдыха как для деловых путешественников, так и для туристов. Идеально подходит для тех, кто ищет комфорт, стиль и захватывающие дух виды в непревзойденной обстановке».'
                ),
                'es' => array(
                    'name' => 'Habitaciones de lujo',
                    'description_short' => 'Retírese a la tranquilidad en nuestras habitaciones de lujo con amplias vistas. Con una cama tamaño queen, espacio de trabajo y una decoración serena, perfecta tanto para viajeros de negocios como de placer.',
                    'description' => 'Disfrute de la máxima tranquilidad en nuestra habitación de lujo, donde las amplias vistas y el entorno sereno crean un oasis de paz. Despierte con vistas panorámicas complementadas con una decoración elegante y una cómoda cama tamaño queen. Ya sea para ponerse al día con su trabajo en el espacio de trabajo bien equipado o simplemente relajarse en un ambiente sereno, esta habitación ofrece un refugio de lujo tanto para viajeros de negocios como de placer. Perfecto para quienes buscan comodidad, estilo y vistas impresionantes en un entorno incomparable.'
                ),
            ),
        );

        $objHotelRoomTypeBedType = new HotelRoomTypeBedType();
        $languages = Language::getLanguages(true);
        foreach ($roomTypeDemoDataLang as $key => $roomTypeData) {
            // Add Product
            $product = new Product();
            $product->name = array();
            $product->description = array();
            $product->description_short = array();
            $product->link_rewrite = array();
            $roomTypeDataDefult = $roomTypeData['en'];
            foreach ($languages as $lang) {
                if (isset($roomTypeData[$lang['iso_code']])) {
                    $product->name[$lang['id_lang']] = $roomTypeData[$lang['iso_code']]['name'];
                    $product->description[$lang['id_lang']] = $roomTypeData[$lang['iso_code']]['description'];
                    $product->description_short[$lang['id_lang']] = $roomTypeData[$lang['iso_code']]['description_short'];
                    $product->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($roomTypeData[$lang['iso_code']]['name']);
                } else {
                    $product->name[$lang['id_lang']] = $roomTypeDataDefult['name'];
                    $product->description[$lang['id_lang']] = $roomTypeDataDefult['description'];
                    $product->description_short[$lang['id_lang']] = $roomTypeDataDefult['description_short'];
                    $product->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($roomTypeDataDefult['name']);
                }
            }

            $product->id_shop_default = Context::getContext()->shop->id;
            $product->id_category_default = 2;
            $product->price = $roomTypeData['price'];
            $product->active = 1;
            $product->quantity = 999999999;
            $product->booking_product = true;
            $product->show_at_front = 1;
            $product->is_virtual = 1;
            $product->indexed = 1;
            $product->save();
            $product_id = $product->id;

            Search::indexation(Tools::link_rewrite($roomTypeDataDefult['name']), $product_id);

            // assign all the categories of hotel and its parent to the product
            if (Validate::isLoadedObject($objHotel = new HotelBranchInformation($id_hotel))) {
                $hotelIdCategory = $objHotel->id_category;
                if (Validate::isLoadedObject($objCategory = new Category($hotelIdCategory))) {
                    if ($hotelCategories = $objCategory->getParentsCategories()) {
                        $categoryIds = array();
                        foreach ($hotelCategories as $rowCateg) {
                            $categoryIds[] = $rowCateg['id_category'];
                        }
                        $product->addToCategories($categoryIds);
                        // set the default category to the hotel category
                        $product->id_category_default = $hotelIdCategory;
                        $product->save();
                    }
                }
            }

            StockAvailable::updateQuantity($product_id, null, 999999999);

            //image upload for products
            $image_dir_path = _PS_MODULE_DIR_.'hotelreservationsystem/views/img/prod_imgs/'.($key+1).'/';
            $imagesTypes = ImageType::getImagesTypes('products');
            $count = 0;
            $have_cover = false;
            if (is_dir($image_dir_path)) {
                if ($opendir = opendir($image_dir_path)) {
                    while (($image = readdir($opendir)) !== false) {
                        $old_path = $image_dir_path.$image;

                        if (ImageManager::isRealImage($old_path)
                            && ImageManager::isCorrectImageFileExt($old_path)
                        ) {
                            $image_obj = new Image();
                            $image_obj->id_product = $product_id;
                            $image_obj->position = Image::getHighestPosition($product_id) + 1;

                            if ($count == 0) {
                                if (!$have_cover) {
                                    $image_obj->cover = 1;
                                    $have_cover = true;
                                }
                                $count++;
                            } else {
                                $image_obj->cover = 0;
                            }
                            $image_obj->add();
                            $new_path = $image_obj->getPathForCreation();
                            foreach ($imagesTypes as $image_type) {
                                ImageManager::resize(
                                    $old_path,
                                    $new_path.'-'.$image_type['name'].'.jpg',
                                    $image_type['width'],
                                    $image_type['height']
                                );
                            }
                            ImageManager::resize($old_path, $new_path.'.jpg');
                        }
                    }
                    closedir($opendir);
                }
            }

            for ($k = 1; $k <= 5; ++$k) {
                $htl_room_info_obj = new HotelRoomInformation();
                $htl_room_info_obj->id_product = $product_id;
                $htl_room_info_obj->id_hotel = $id_hotel;
                $htl_room_info_obj->room_num = $roomTypeDataDefult['name'][0].'R-10'.$k;
                $htl_room_info_obj->id_status = 1;
                $htl_room_info_obj->floor = 'First';
                $htl_room_info_obj->save();
            }

            $htl_rm_type = new HotelRoomType();
            $htl_rm_type->id_product = $product_id;
            $htl_rm_type->id_hotel = $id_hotel;
            $htl_rm_type->adults = 2;
            $htl_rm_type->children = 2;
            $htl_rm_type->max_adults = 2;
            $htl_rm_type->max_children = 2;
            $htl_rm_type->max_guests = 4;

            $htl_rm_type->save();

            // Add features to the product
            $ftr_arr = array(0 => 1, 1 => 2, 2 => 3, 3 => 4);
            $ftr_val_arr = array(0 => 1, 1 => 2, 2 => 3, 3 => 4);
            foreach ($ftr_arr as $key_htl_ftr => $val_htl_ftr) {
                $product->addFeaturesToDB($val_htl_ftr, $ftr_val_arr[$key_htl_ftr]);
            }

            // save advance payment information
            $this->saveAdvancedPaymentInfo($product_id);
            $objHotelRoomTypeBedType->updateRoomTypeBedTypes($roomTypeData['id_bed_types'], $product_id);
        }
    }

    public function saveDummyServiceProductsAndRelatedInfo()
    {
        $idCategoryServices = (int) Configuration::get('PS_SERVICE_CATEGORY');
        $idsGroup = array_column(Group::getGroups(Context::getContext()->language->id), 'id_group');

        // create service categories
        $categories = array(
            'restaurant' => array(
                'name' => array(
                    'en' => 'Restaurant',
                    'nl' => 'Restaurants',
                    'fr' => 'Restaurant',
                    'de' => 'Restaurant',
                    'ru' => 'Ресторан',
                    'es' => 'Restaurante'
                ),
                'id_category' => 'to_be_set_below',
            ),
            'transfers' => array(
                'name' => array(
                    'en' => 'Transfers',
                    'nl' => 'Overdrachten',
                    'fr' => 'Transferts',
                    'de' => 'Überweisungen',
                    'ru' => 'Трансферы',
                    'es' => 'Transferencias',
                ),
                'id_category' => 'to_be_set_below',
            ),
            'activities' => array(
                'name' => array(
                    'en' => 'Activities',
                    'nl' => 'Activiteiten',
                    'fr' => 'Activités',
                    'de' => 'Aktivitäten',
                    'ru' => 'Деятельность',
                    'es' => 'Actividades',
                ),
                'id_category' => 'to_be_set_below',
            ),
            'charges' => array(
                'name' => array(
                    'en' => 'Operational charges',
                    'nl' => 'Operationele kosten',
                    'fr' => 'Frais opérationnels',
                    'de' => 'Betriebskosten',
                    'es' => 'Cargos operativos',
                    'ru' => 'Операционные расходы',
                ),
                'id_category' => 'to_be_set_below',
            ),
        );

        foreach ($categories as &$category) {
            $idCategory = $this->addCategory(array('name' => $category['name'], 'group_ids' => $idsGroup, 'parent_category' => $idCategoryServices));
            $category['id_category'] = $idCategory;
        }

        // create service products
        $serviceProducts = array(
            array(
                'id_category_default' => $categories['charges']['id_category'],
                'price' => '250',
                'auto_add_to_cart' => 1,
                'show_at_front' => 0,
                'price_calculation_method' => Product::PRICE_CALCULATION_METHOD_PER_DAY,
                'price_addition_type' => Product::PRICE_ADDITION_TYPE_WITH_ROOM,
                'en' => array(
                    'name' => 'Room Maintenance Fees',
                    'description' => 'Ensure a comfortable stay with our room maintenance service, keeping your accommodation pristine and hassle-free throughout your visit.',
                ),
                'nl' => array(
                    'name' => 'Kameronderhoudskosten',
                    'description' => 'Zorg voor een comfortabel verblijf met onze kameronderhoudsservice, zodat uw accommodatie tijdens uw bezoek onberispelijk en probleemloos blijft.'
                ),
                'fr' => array(
                    'name' => 'Frais d\'entretien des chambres',
                    'description' => 'Assurez-vous d\'un séjour confortable grâce à notre service d\'entretien des chambres, en gardant votre hébergement impeccable et sans tracas tout au long de votre visite.'
                ),
                'de' => array(
                    'name' => 'Zimmerwartungsgebühren',
                    'description' => 'Sorgen Sie mit unserem Zimmerwartungsservice für einen komfortablen Aufenthalt und sorgen Sie dafür, dass Ihre Unterkunft während Ihres gesamten Aufenthalts makellos und problemlos bleibt.'
                ),
                'ru' => array(
                    'name' => 'Плата за обслуживание номера',
                    'description' => 'Обеспечите комфортное пребывание благодаря нашей службе обслуживания номеров, сохраняя ваше жилье в первозданном виде и без проблем на протяжении всего вашего визита.'
                ),
                'es' => array(
                    'name' => 'Tarifas de mantenimiento de la habitación',
                    'description' => 'Asegure una estancia cómoda con nuestro servicio de mantenimiento de habitaciones, manteniendo su alojamiento impecable y sin complicaciones durante su visita.'
                )
            ),
            array(
                'id_category_default' => $categories['charges']['id_category'],
                'price' => '250',
                'auto_add_to_cart' => 1,
                'show_at_front' => 0,
                'price_calculation_method' => Product::PRICE_CALCULATION_METHOD_PER_BOOKING,
                'price_addition_type' => Product::PRICE_ADDITION_TYPE_INDEPENDENT,
                'en' => array(
                    'name' => 'Internet Handling Charges',
                    'description' => 'Navigate our website effortlessly with seamless handling, ensuring reliable, high-speed access for an enjoyable browsing experience throughout your online journey.',
                ),
                'nl' => array(
                    'name' => 'Internetkosten',
                    'description' => 'Navigeer moeiteloos door onze website met een naadloze bediening, waardoor betrouwbare, snelle toegang wordt gegarandeerd voor een plezierige browse-ervaring tijdens uw online reis.',
                ),
                'fr' => array(
                    'name' => 'Frais de traitement Internet',
                    'description' => 'Naviguez sur notre site Web sans effort avec une manipulation transparente, garantissant un accès fiable et haut débit pour une expérience de navigation agréable tout au long de votre parcours en ligne.',
                ),
                'de' => array(
                    'name' => 'Internet-Bearbeitungsgebühren',
                    'description' => 'Navigieren Sie mühelos und reibungslos auf unserer Website und gewährleisten Sie einen zuverlässigen Hochgeschwindigkeitszugriff für ein angenehmes Surferlebnis während Ihrer gesamten Online-Reise.',
                ),
                'ru' => array(
                    'name' => 'Стоимость обслуживания Интернета',
                    'description' => 'Навигация по нашему веб-сайту проста и понятна, обеспечивая надежный и высокоскоростной доступ для приятного просмотра на протяжении всего вашего онлайн-путешествия.',
                ),
                'es' => array(
                    'name' => 'Cargos por manejo de Internet',
                    'description' => 'Navegue por nuestro sitio web sin esfuerzo con un manejo fluido, garantizando un acceso confiable y de alta velocidad para una experiencia de navegación agradable durante su viaje en línea.',
                ),

            ),
            array(
                'id_category_default' => $categories['transfers']['id_category'],
                'price' => '50',
                'auto_add_to_cart' => 0,
                'show_at_front' => 1,
                'price_calculation_method' => Product::PRICE_CALCULATION_METHOD_PER_BOOKING,
                'price_addition_type' => Product::PRICE_ADDITION_TYPE_WITH_ROOM,
                'en' => array(
                    'name' => 'Airport Shuttle',
                    'description' => 'Experience convenience from touchdown to check-in with our efficient airport shuttle service, whisking you to your accommodation with ease and comfort.',
                ),
                'nl' => array(
                    'name' => 'Luchthavenshuttle',
                    'description' => 'Ervaar het gemak van de landing tot het inchecken met onze efficiënte luchthavenshuttleservice, die u gemakkelijk en comfortabel naar uw accommodatie brengt.',
                ),
                'fr' => array(
                    'name' => 'Navette Aéroport',
                    'description' => 'Découvrez la commodité de l\'atterrissage à l\'enregistrement grâce à notre service de navette aéroport efficace, qui vous emmènera à votre hébergement en toute simplicité et confort.',
                ),
                'de' => array(
                    'name' => 'Flughafen-Shuttle',
                    'description' => 'Erleben Sie Komfort von der Landung bis zum Check-in mit unserem effizienten Flughafen-Shuttleservice, der Sie einfach und bequem zu Ihrer Unterkunft bringt.',
                ),
                'ru' => array(
                    'name' => 'Трансфер до аэропорта',
                    'description' => 'Ощутите удобство от приземления до регистрации на рейс благодаря нашему эффективному трансферу из аэропорта, который доставит вас к месту проживания с легкостью и комфортом.',
                ),
                'es' => array(
                    'name' => 'Transporte al aeropuerto',
                    'description' => 'Experimenta la comodidad desde el aterrizaje hasta el check-in con nuestro eficiente servicio de transporte al aeropuerto, que te llevará a tu alojamiento con facilidad y comodidad.',
                ),
            ),
            array(
                'id_category_default' => $categories['transfers']['id_category'],
                'price' => '200',
                'auto_add_to_cart' => 0,
                'show_at_front' => 1,
                'price_calculation_method' => Product::PRICE_CALCULATION_METHOD_PER_BOOKING,
                'price_addition_type' => Product::PRICE_ADDITION_TYPE_WITH_ROOM,
                'en' => array(
                    'name' => 'Cab on Demand',
                    'description' => 'Explore the city conveniently with our cab-on-demand service, giving you the freedom to travel and discover local attractions at your own pace.',
                ),
                'nl' => array(
                    'name' => 'Cabine op aanvraag',
                    'description' => 'Verken de stad op een handige manier met onze taxi-on-demand-service, waardoor u de vrijheid heeft om te reizen en lokale bezienswaardigheden in uw eigen tempo te ontdekken.',
                ),
                'fr' => array(
                    'name' => 'Cabine à la demande',
                    'description' => 'Explorez la ville facilement grâce à notre service de taxi à la demande, vous donnant la liberté de voyager et de découvrir les attractions locales à votre rythme.',
                ),
                'de' => array(
                    'name' => 'Cab on Demand',
                    'description' => 'Erkunden Sie die Stadt bequem mit unserem Cab-on-Demand-Service, der Ihnen die Freiheit gibt, in Ihrem eigenen Tempo zu reisen und lokale Sehenswürdigkeiten zu entdecken.',
                ),
                'ru' => array(
                    'name' => 'Такси по требованию',
                    'description' => 'Исследуйте город с комфортом, воспользовавшись нашей услугой заказа такси, которая дает вам свободу путешествовать и открывать для себя местные достопримечательности в удобном для вас темпе.',
                ),
                'es' => array(
                    'name' => 'Taxi a pedido',
                    'description' => 'Explore la ciudad cómodamente con nuestro servicio de taxi a pedido, que le brinda la libertad de viajar y descubrir atracciones locales a su propio ritmo.',
                ),
            ),
            array(
                'id_category_default' => $categories['restaurant']['id_category'],
                'price' => '350',
                'auto_add_to_cart' => 0,
                'show_at_front' => 1,
                'price_calculation_method' => Product::PRICE_CALCULATION_METHOD_PER_DAY,
                'price_addition_type' => Product::PRICE_ADDITION_TYPE_WITH_ROOM,
                'en' => array(
                    'name' => 'Breakfast',
                    'description' => 'Start your day right with a delicious and hearty breakfast, thoughtfully prepared to fuel your adventures and make your mornings exceptional.',
                ),
                'nl' => array(
                    'name' => 'Ontbijt',
                    'description' => 'Begin uw dag goed met een heerlijk en stevig ontbijt, zorgvuldig bereid om uw avonturen te voeden en uw ochtenden uitzonderlijk te maken.',
                ),
                'fr' => array(
                    'name' => 'Petit déjeuner',
                    'description' => 'Commencez bien votre journée avec un petit-déjeuner délicieux et copieux, soigneusement préparé pour alimenter vos aventures et rendre vos matinées exceptionnelles.',
                ),
                'de' => array(
                    'name' => 'Frühstück',
                    'description' => 'Beginnen Sie Ihren Tag mit einem köstlichen und herzhaften Frühstück, das sorgfältig zubereitet wird, um Ihre Abenteuer anzuregen und Ihren Morgen außergewöhnlich zu machen.',
                ),
                'ru' => array(
                    'name' => 'Завтрак',
                    'description' => 'Начните свой день с вкусного и сытного завтрака, тщательно приготовленного, который подстегнет ваши приключения и сделает ваше утро особенным.',
                ),
                'es' => array(
                    'name' => 'Desayuno',
                    'description' => 'Empiece bien el día con un delicioso y abundante desayuno, cuidadosamente preparado para alimentar sus aventuras y hacer que sus mañanas sean excepcionales.',
                ),
            ),
            array(
                'id_category_default' => $categories['restaurant']['id_category'],
                'price' => '450',
                'auto_add_to_cart' => 0,
                'show_at_front' => 1,
                'price_calculation_method' => Product::PRICE_CALCULATION_METHOD_PER_DAY,
                'price_addition_type' => Product::PRICE_ADDITION_TYPE_WITH_ROOM,
                'en' => array(
                    'name' => 'Dinner',
                    'description' => 'Wind down in the evening with a delectable dinner spread, offering a culinary journey that delights your taste buds and completes your day with satisfaction.',
                ),
                'nl' => array(
                    'name' => 'Diner',
                    'description' => 'Kom \'s avonds tot rust met een verrukkelijk diner, een culinaire reis die uw smaakpapillen verwent en uw dag met voldoening voltooit.',
                ),
                'fr' => array(
                    'name' => 'Dîner',
                    'description' => 'Détendez-vous en soirée avec un délicieux dîner, offrant un voyage culinaire qui ravit vos papilles et complète votre journée avec satisfaction.',
                ),
                'de' => array(
                    'name' => 'Abendessen',
                    'description' => 'Entspannen Sie den Abend mit einem köstlichen Abendessen und bieten Sie eine kulinarische Reise, die Ihren Gaumen verwöhnt und Ihren Tag mit Zufriedenheit abschließt.',
                ),
                'ru' => array(
                    'name' => 'Ужин',
                    'description' => 'Отдохните вечером за восхитительным ужином, предлагающим кулинарное путешествие, которое порадует ваши вкусовые рецепторы и с удовлетворением завершит ваш день.',
                ),
                'es' => array(
                    'name' => 'Cena',
                    'description' => 'Relájate por la noche con una deliciosa cena que ofrece un viaje culinario que deleita tu paladar y completa tu día con satisfacción.',
                ),
            ),
        );

        $languages = Language::getLanguages(true);
        foreach ($serviceProducts as $serviceProduct) {
            $objProduct = new Product();
            $objProduct->name = array();
            $objProduct->description = array();
            $objProduct->description_short = array();
            $objProduct->link_rewrite = array();

            // copy lang values
            $serviceProdDataDefult = $serviceProduct['en'];
            foreach ($languages as $language) {
                if (isset($serviceProduct[$language['iso_code']])) {
                    $objProduct->name[$language['id_lang']] = $serviceProduct[$language['iso_code']]['name'];
                    $objProduct->description[$language['id_lang']] = $serviceProduct[$language['iso_code']]['description'];
                    $objProduct->description_short[$language['id_lang']] = $serviceProduct[$language['iso_code']]['description'];
                    $objProduct->link_rewrite[$language['id_lang']] = Tools::link_rewrite($serviceProduct[$language['iso_code']]['name']);
                } else {
                    $objProduct->name[$language['id_lang']] = $serviceProdDataDefult['name'];
                    $objProduct->description[$language['id_lang']] = $serviceProdDataDefult['description'];
                    $objProduct->description_short[$language['id_lang']] = $serviceProdDataDefult['description'];
                    $objProduct->link_rewrite[$language['id_lang']] = Tools::link_rewrite($serviceProdDataDefult['name']);
                }
            }

            $objProduct->id_shop_default = Context::getContext()->shop->id;
            $objProduct->id_category_default = $serviceProduct['id_category_default'];
            $objProduct->price = $serviceProduct['price'];
            $objProduct->active = 1;
            $objProduct->quantity = 999999999;
            $objProduct->booking_product = 0;
            $objProduct->selling_preference_type = Product::SELLING_PREFERENCE_WITH_ROOM_TYPE;
            $objProduct->auto_add_to_cart = $serviceProduct['auto_add_to_cart'];
            $objProduct->show_at_front = $serviceProduct['show_at_front'];
            $objProduct->available_for_order = 1;
            $objProduct->price_addition_type = $serviceProduct['price_addition_type'];
            $objProduct->price_calculation_method = $serviceProduct['price_calculation_method'];
            $objProduct->is_virtual = 1;
            $objProduct->indexed = 1;
            if ($objProduct->auto_add_to_cart && $objProduct->price_addition_type == Product::PRICE_ADDITION_TYPE_WITH_ROOM) {
                $objProduct->id_tax_rules_group = 0;
            }

            $objProduct->save();
            $idProduct = $objProduct->id;

            // add to applicable categories
            $objProduct->addToCategories($serviceProduct['id_category_default']);
            Search::indexation(Tools::link_rewrite($serviceProdDataDefult['name']), $idProduct);

            StockAvailable::updateQuantity($idProduct, null, 999999999);

            // save service product images
            $imagesBasePath = _PS_MODULE_DIR_.'hotelreservationsystem/views/img/prod_imgs/'.$idProduct.'/';
            $imagesTypes = ImageType::getImagesTypes('products');
            if (is_dir($imagesBasePath) && $opendir = opendir($imagesBasePath)) {
                while (($image = readdir($opendir)) !== false) {
                    $sourceImagePath = $imagesBasePath.$image;

                    if (ImageManager::isRealImage($sourceImagePath)
                        && ImageManager::isCorrectImageFileExt($sourceImagePath)
                    ) {
                        $objImage = new Image();
                        $objImage->id_product = $idProduct;
                        $objImage->position = Image::getHighestPosition($idProduct) + 1;
                        $objImage->cover = 1;
                        $objImage->add();
                        $destinationPath = $objImage->getPathForCreation();
                        foreach ($imagesTypes as $imageType) {
                            ImageManager::resize(
                                $sourceImagePath,
                                $destinationPath.'-'.$imageType['name'].'.jpg',
                                $imageType['width'],
                                $imageType['height']
                            );
                        }
                        ImageManager::resize($sourceImagePath, $destinationPath.'.jpg');
                    }
                }
                closedir($opendir);
            }

            // link to all demo room types
            $objRoomTypeServiceProduct = new RoomTypeServiceProduct();
            $objRoomTypeServiceProduct->addRoomProductLink(
                $idProduct,
                array(1, 2, 3, 4),
                RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE
            );
        }
    }

    public function saveAdvancedPaymentInfo($id_product)
    {
        $obj_adv_pmt = new HotelAdvancedPayment();
        $obj_adv_pmt->active = 0;
        $obj_adv_pmt->id_product = $id_product;
        $obj_adv_pmt->payment_type = '';
        $obj_adv_pmt->value = '';
        $obj_adv_pmt->id_currency = '';
        $obj_adv_pmt->tax_include = '';
        $obj_adv_pmt->calculate_from = 0;
        return $obj_adv_pmt->save();
    }

    public function saveDummyHotelImages($idHotel)
    {
        if ($idHotel) {
            $objHotelImage = new HotelImage();
            if (is_dir(_PS_HOTEL_IMG_DIR_)) {
                foreach(scandir(_PS_HOTEL_IMG_DIR_) as $file) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }
                    if (preg_match('/[^\\s]+\.jpg$/', $file)) {
                        $imageDetail = $objHotelImage->uploadHotelImages(
                            array('tmp_name' => _PS_HOTEL_IMG_DIR_.$file),
                            $idHotel
                        );
                        unlink(_PS_HOTEL_IMG_DIR_.$file);
                    }
                }
            }
        }
    }

    public function createDummyDataForProject()
    {
        $htl_id = $this->saveDummyHotelBranchInfo();
        $this->saveDummyHotelImages($htl_id);
        $this->saveDummyHotelFeatures($htl_id);
        $this->saveDummyProductsAndRelatedInfo($htl_id);
        $this->saveDummyServiceProductsAndRelatedInfo();

        return true;
    }

    public function getCategoryParams($params)
    {
        if (!isset($params['parent_category'])) {
            $params['parent_category'] = false;
        }

        if (!isset($params['is_hotel'])) {
            $params['is_hotel'] = false;
        }

        if (!isset($params['id_hotel'])) {
            $params['id_hotel'] = 0;
        }

        if (!isset($params['link_rewrite'])) {
            $params['link_rewrite'] = false;
        }

        if (!isset($params['meta_title'])) {
            $params['meta_title'] = false;
        }

        if (!isset($params['meta_description'])) {
            $params['meta_description'] = false;
        }

        if (!isset($params['meta_keywords'])) {
            $params['meta_keywords'] = false;
        }

        return $params;
    }

    /**
     * Send parameters in array form
     * @param array $params
     *  $params['name']: [name of the category]
     *  $params['group_ids']: [group_ids of the category]
     *  $params['parent_category']: [parent_category of the category]
     *  $params['is_hotel']: [is_hotel = 1 if category is for hotel]
     *  $params['id_hotel']: [id_hotel of the category, if category is for hotel]
     *  $params['link_rewrite']: [link_rewrite of the category]
     *  $params['meta_title']: [meta_title of the category]
     *  $params['meta_description']: [meta_description of the category]
     *  $params['meta_keywords']: [meta_keywords of the category]
     * @return int  returns ID of the category added.
     */
    public function addCategory(array $params)
    {
        extract($this->getCategoryParams($params));
        if (!$parent_category) {
            $parent_category = Configuration::get('PS_LOCATIONS_CATEGORY');
        }

        if (!is_array($name)) {
            $name = array('en' => $name);
        }

        $defaultCatName = $name['en'];
        $languages = Language::getLanguages(true);
        if ($is_hotel && $id_hotel) {
            $cat_id_hotel = Db::getInstance()->getValue(
                'SELECT `id_category` FROM `'._DB_PREFIX_.'htl_branch_info` WHERE id='.(int) $id_hotel
            );
            if ($cat_id_hotel) {
                $obj_cat = new Category($cat_id_hotel);
                $obj_cat->name = array();
                $obj_cat->description = array();
                $obj_cat->link_rewrite = array();

                foreach ($languages as $lang) {
                    $obj_cat->description[$lang['id_lang']] = 'This category are for hotels only';
                    if (isset($name[$lang['iso_code']])) {
                        $obj_cat->name[$lang['id_lang']] = $name[$lang['iso_code']];
                        $obj_cat->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($name[$lang['iso_code']]);
                    } else {
                        $obj_cat->name[$lang['id_lang']] = $defaultCatName;
                        $obj_cat->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($defaultCatName);
                    }
                }
                $obj_cat->id_parent = $parent_category;
                $obj_cat->groupBox = $group_ids;
                $obj_cat->save();
                $cat_id = $obj_cat->id;

                return $cat_id;
            }
        }

        $context = Context::getContext();
        $check_category_exists = Category::searchByNameAndParentCategoryId($context->language->id, $defaultCatName, $parent_category);

        if ($check_category_exists) {
            return $check_category_exists['id_category'];
        } else {
            $obj = new Category();
            $obj->name = array();
            $obj->description = array();
            $obj->link_rewrite = array();

            foreach ($languages as $lang) {
                $obj->description[$lang['id_lang']] = 'This category are for hotels only';
                if (isset($name[$lang['iso_code']])) {
                    $obj->name[$lang['id_lang']] = $name[$lang['iso_code']];
                    $obj->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($name[$lang['iso_code']]);
                } else {
                    $obj->name[$lang['id_lang']] = $defaultCatName;
                    $obj->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($defaultCatName);
                }
            }
            $obj->id_parent = $parent_category;
            $obj->groupBox = $group_ids;
            $obj->add();
            $cat_id = $obj->id;

            return $cat_id;
        }
    }

    public static function generateRandomCode($length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $rand = '';
        for ($i = 0; $i < $length; ++$i) {
            $rand = $rand.$characters[mt_rand(0, Tools::strlen($characters) - 1)];
        }

        return $rand;
    }

    public static function getBaseDirUrl()
    {
        $forceSsl = Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
        $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
        $baseDirSsl = $protocol_link.Tools::getShopDomainSsl().__PS_BASE_URI__;
        $baseDir = _PS_BASE_URL_.__PS_BASE_URI__;

        $startUrl = $forceSsl ? $baseDirSsl : $baseDir;
        return $startUrl;
    }

    // update lang values of Configuration lang type keys when importing new language from localization
    public static function updateConfigurationLangKeys($idNewLang, $langKeys)
    {
        if ($langKeys && $idNewLang) {
            if (!is_array($langKeys)) {
                $langKeys = array($langKeys);
            }
            $defaultLangId = (int) Configuration::get('PS_LANG_DEFAULT');
            foreach ($langKeys as $configKey) {
                Configuration::updateValue(
                    $configKey,
                    array($idNewLang => Configuration::get($configKey, $defaultLangId))
                );
            }
        }
        return true;
    }

    // update lang values of lang tables when importing new language from localization
    public static function updateLangTables($idNewLang, $langTables)
    {
        if ($langTables && $idNewLang) {
            if (!is_array($langTables)) {
                $langTables = array($langTables);
            }
            $defaultLangId = (int) Configuration::get('PS_LANG_DEFAULT');
            foreach ($langTables as $table) {
                if ($tableLangsVals = Db::getInstance()->executeS(
                    'SELECT * FROM `'._DB_PREFIX_.$table.'_lang` WHERE `id_lang` = '.(int) $defaultLangId
                )) {
                    foreach ($tableLangsVals as $defaultLangRow) {
                        $defaultLangRow['id_lang'] = $idNewLang;
                        $tableValue = '';
                        $flag = 0;
                        foreach ($defaultLangRow as $value) {
                            $content = str_replace("'", "\'", $value);
                            $tableValue .= ($flag != 0 ? ', ' : '')."'".$content."'";
                            $flag = 1;
                        }
                        Db::getInstance()->execute(
                            'INSERT INTO `'._DB_PREFIX_.$table.'_lang` VALUES ('.$tableValue.')'
                        );
                    }
                }
            }
        }
        return true;
    }

    public static function getRandomZipcodeByForCountry($idCountry)
    {
        return Tools::generateRandomZipcode($idCountry);
    }

    /**
     * Get Super Admin Of Prestashop
     * @return int Super Admin Employee ID
     */
    public static function getSupperAdmin()
    {
        if ($data = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'employee` ORDER BY `id_employee`')) {
            foreach ($data as $emp) {
                $employee = new Employee($emp['id_employee']);
                if ($employee->isSuperAdmin()) {
                    return $emp['id_employee'];
                }
            }
        }

        return false;
    }

    public static function getNumberOfDays($dateFrom, $dateTo)
    {
        if (empty($dateFrom) || empty($dateTo)) {
            return 0;
        }

        $startDate = new DateTime($dateFrom);
        $endDate = new DateTime($dateTo);
        $daysDifference = $startDate->diff($endDate)->days;
        Hook::exec('actionDatesDifferenceModifier', array('date_from'=> $dateFrom, 'date_to'=> $dateTo, 'days_difference'=> &$daysDifference));

        return $daysDifference;
    }

    /**
     * Validate the date range for a hotel booking.
     *
     * @param string $dateFrom Start date (format: 'Y-m-d H:i:s')
     * @param string $dateTo End date (format: 'Y-m-d H:i:s')
     * @param int $idHotel Hotel ID
     * @return bool True if the date range is valid, otherwise false
     */
    public static function validateDateRangeForHotel($dateFrom, $dateTo, $idHotel)
    {
        $validStartDateTimeStamp = strtotime(date('Y-m-d'));
        if ($minBookingOffset = (int) HotelOrderRestrictDate::getMinimumBookingOffset($idHotel)) {
            $validStartDateTimeStamp = strtotime('+ '.$minBookingOffset.' day', $validStartDateTimeStamp);
        }

        $maxOrderDateTimestamp = strtotime(HotelOrderRestrictDate::getMaxOrderDate($idHotel));
        $dateFromTimestamp = strtotime($dateFrom);
        $dateToTimestamp = strtotime($dateTo);

        $isValid = true;
        if ($dateFrom != '' && ($dateFromTimestamp === false || ($dateFromTimestamp < $validStartDateTimeStamp))) {
            $isValid = false;
        } else if ($dateTo != '' && ($dateToTimestamp === false || ($dateToTimestamp < $validStartDateTimeStamp))) {
            $isValid = false;
        } else if ($dateTo != '' && $dateFrom != '' && $dateFromTimestamp >= $dateToTimestamp) {
            $isValid = false;
        } else if ($dateToTimestamp > $maxOrderDateTimestamp) {
            $isValid = false;
        }

        Hook::exec('actionValidateDateRangeForHotel', array(
            'is_valid' => &$isValid,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'id_hotel' => $idHotel
        ));

        return $isValid;
    }

    /**
     * This function is a utility function for creating an associative array for generating tree
     * provide tree structure in the following format
     *  country --> root
     *      state
     *          city
     *              hotel
     *                  room_type
     *                      room --> leaf
     *
     * You can edit the start and end point to generate tree for different levels
     * @param $rootNode = 'country'
     * @param $leafNode = 'room'
     * @param $rootNodeId int|array
     * @param $selectedElements int|array
     */

    const NODE_COUNTRY = 1;
    const NODE_STATE = 2;
    const NODE_CITY = 3;
    const NODE_HOTEL = 4;
    const NODE_ROOM_TYPE = 5;
    const NODE_ROOM = 6;

    public static function generateTreeData($params)
    {
        extract($params);

        if (!isset($rootNode)) {
            $rootNode = self::NODE_COUNTRY;
        }
        if (!isset($leafNode)) {
            $leafNode = self::NODE_ROOM;
        }
        if (!isset($selectedElements)) {
            $selectedElements = array();
        }
        if (!isset($rootNodeId)) {
            $rootNodeId = false;
        }
        if (!isset($prefix)) {
            $prefix = '';
        }
        $treeData = array();

        if ($rootNode == self::NODE_COUNTRY) {
            $treeData = self::generateCountryNodes($rootNodeId, $leafNode, $selectedElements, false, $prefix);
        } else if ($rootNode == self::NODE_STATE) {
            $treeData = self::generateStateNodes($rootNodeId, $leafNode, $selectedElements, false, $prefix);
        } else if ($rootNode == self::NODE_CITY) {
            $treeData = self::generateCityNodes($rootNodeId, $leafNode, $selectedElements, false, $prefix);
        } else if ($rootNode == self::NODE_HOTEL) {
            $treeData = self::generateHotelNodes($rootNodeId, $leafNode, $selectedElements, false, $prefix);
        } else if ($rootNode == self::NODE_ROOM_TYPE) {
            $treeData = self::generateRoomTypeNodes($rootNodeId, $leafNode, $selectedElements, false, $prefix);
        } else if ($rootNode == self::NODE_ROOM) {
            $treeData = self::generateRoomNodes($rootNodeId, $leafNode, $selectedElements, false, $prefix);
        }

        return $treeData;
    }

    /**
     * Generate the node data for country
     */
    protected static function generateCountryNodes($rootNodeId, $leafNode, $selectedElements, $previousElements = false, $prefix = '')
    {
        $return = array();
        $countries = self::getcategoryByParent($previousElements, 3, $prefix.'country', $rootNodeId);
        $countriesIds = array_column($countries, 'value');

        if ($leafNode > self::NODE_COUNTRY) {
            $states = self::generateStateNodes(false, $leafNode, $selectedElements, $countriesIds, $prefix);
        }

        foreach ($countries as $country) {
            if (isset($selectedElements['country']) && in_array($country['value'], $selectedElements['country'])) {
                $country['selected'] = true;
            }

            if (isset($states[$country['value']])) {
                $country['children'] = $states[$country['value']];
            }

            if ($previousElements) {
                $return[$country['id_parent']][] = $country;
            } else {
                $return[] = $country;
            }
        }

        return $return;
    }

    /**
     * Generate the node data for state
     */
    protected static function generateStateNodes($rootNodeId, $leafNode, $selectedElements, $previousElements = false, $prefix = '')
    {
        $return = array();
        $states = self::getcategoryByParent($previousElements, 4, $prefix.'state', $rootNodeId);
        $stateIds = array_column($states, 'value');

        if ($leafNode > self::NODE_STATE) {
            $cities = self::generateCityNodes(false, $leafNode, $selectedElements, $stateIds, $prefix);
        }

        foreach ($states as $state) {
            if (isset($selectedElements['state']) && in_array($state['value'], $selectedElements['state'])) {
                $state['selected'] = true;
            }

            if (isset($cities[$state['value']])) {
                $state['children'] = $cities[$state['value']];
            }

            if ($previousElements) {
                $return[$state['id_parent']][] = $state;
            } else {
                $return[] = $state;
            }
        }

        return $return;
    }

    /**
     * Generate the node data for city
     */
    protected static function generateCityNodes($rootNodeId, $leafNode, $selectedElements, $previousElements = false, $prefix = '')
    {
        $return = array();
        $cities = self::getcategoryByParent($previousElements, 5, $prefix.'city', $rootNodeId);
        $cityIds = array_column($cities, 'value');

        if ($leafNode > self::NODE_CITY) {
            $hotels = self::generateHotelNodes(false, $leafNode, $selectedElements, $cityIds, $prefix);
        }

        foreach ($cities as $city) {
            if (isset($selectedElements['city']) && in_array($city['value'], $selectedElements['city'])) {
                $city['selected'] = true;
            }

            if (isset($hotels[$city['value']])) {
                $city['children'] = $hotels[$city['value']];
            }

            if ($previousElements) {
                $return[$city['id_parent']][] = $city;
            } else {
                $return[] = $city;
            }
        }

        return $return;
    }

    /**
     * Generate the node data for hotels
     */
    protected static function generateHotelNodes($rootNodeId, $leafNode, $selectedElements, $previousElements = false, $prefix = '')
    {
        $return = array();
        $hotels = self::getHotelsByIdCity($previousElements, $rootNodeId, $prefix);
        $hotelIds = array_column($hotels, 'value');

        if (Validate::isLoadedObject(Context::getContext()->employee)){
            if (!Context::getContext()->employee->isSuperAdmin()) {
                $hotels = HotelBranchInformation::filterDataByHotelAccess($hotels, Context::getContext()->employee->id_profile, 'value', false, true);
            }
        }

        if ($leafNode > self::NODE_HOTEL) {
            $roomTypes = self::generateRoomTypeNodes(false, $leafNode, $selectedElements, $hotelIds, $prefix);
        }

        foreach ($hotels as $hotel) {
            if (isset($selectedElements['hotel']) && in_array($hotel['value'], $selectedElements['hotel'])) {
                $hotel['selected'] = true;
            }

            if (isset($hotel['htl_access']) && !$hotel['htl_access']) {
                $hotel['hidden'] = true;
            }

            if (isset($roomTypes[$hotel['value']])) {
                $hotel['children'] = $roomTypes[$hotel['value']];
            }

            if ($previousElements) {
                $return[$hotel['id_parent']][] = $hotel;
            } else {
                $return[] = $hotel;
            }
        }

        return $return;
    }

    /**
     * Generate the node data for room types
     */
    protected static function generateRoomTypeNodes($rootNodeId, $leafNode, $selectedElements, $previousElements = false, $prefix = '')
    {
        $return = array();
        $roomTypes = self::getRoomTypesByHotelsId($previousElements, $rootNodeId, $prefix);
        $roomTypeIds = array_column($roomTypes, 'value');

        if (Validate::isLoadedObject(Context::getContext()->employee)){
            if (!Context::getContext()->employee->isSuperAdmin()) {
                $roomTypes = HotelBranchInformation::filterDataByHotelAccess(
                    $roomTypes,
                    Context::getContext()->employee->id_profile,
                    false,
                    'value',
                    true
                );
            }
        }

        if ($leafNode > self::NODE_ROOM_TYPE) {
            $rooms = self::generateRoomNodes(false, $leafNode, $selectedElements, $roomTypeIds, $prefix);
        }

        foreach ($roomTypes as $roomType) {
            if (isset($selectedElements['room_type']) && in_array($roomType['value'], $selectedElements['room_type'])) {
                $roomType['selected'] = true;
            }

            if (isset($roomType['htl_access']) && !$roomType['htl_access']) {
                $roomType['hidden'] = true;
            }

            if (isset($rooms[$roomType['value']])) {
                $roomType['children'] = $rooms[$roomType['value']];
            }

            if ($previousElements) {
                $return[$roomType['id_hotel']][] = $roomType;
            } else {
                $return[] = $roomType;
            }
        }

        return $return;
    }

    /**
     * Generate the node data for rooms
     */
    protected static function generateRoomNodes($rootNodeId, $leafNode, $selectedElements, $previousElements = false, $prefix = '')
    {
        $return = array();
        $rooms = self::getRoomsByRoomTypeId($previousElements, $rootNodeId, $prefix);
        if (Validate::isLoadedObject(Context::getContext()->employee)){
            if (!Context::getContext()->employee->isSuperAdmin()) {
                $rooms = HotelBranchInformation::filterDataByHotelAccess(
                    $rooms,
                    Context::getContext()->employee->id_profile,
                    false,
                    false,
                    true
                );
            }
        }

        foreach ($rooms as $room) {
            if (isset($selectedElements['room']) && in_array($room['value'], $selectedElements['room'])) {
                $room['selected'] = true;
            }

            if (isset($room['htl_access']) && !$room['htl_access']) {
                $room['hidden'] = true;
            }

            if ($previousElements) {
                $return[$room['id_product']][] = $room;
            } else {
                $return[] = $room;
            }
        }

        return $return;
    }

    /**
     * Get all the children category of the provided id categories
     */
    protected static function getcategoryByParent($idParents, $levelDepth, $input_name, $rootNodeId)
    {
        $locationCategory = new Category(Configuration::get('PS_LOCATIONS_CATEGORY'));
        $sql = 'SELECT c.`id_category` as `value` , cl.`name`, "'.pSQL($input_name).'_box" as `input_name`, c.`id_parent`
        FROM `'._DB_PREFIX_.'category` AS c
        INNER JOIN `'._DB_PREFIX_.'category_lang` AS cl
        ON (c.`id_category` = cl.`id_category` AND cl.`id_lang`='.(int)Context::getContext()->language->id.')
        WHERE c.`nleft` > '.(int)$locationCategory->nleft.' AND c.`nright` < '.(int)$locationCategory->nright.'
        AND c.`level_depth` = '.(int)$levelDepth;

        if ($idParents) {
            $sql .= ' AND c.`id_parent` IN ('.implode(', ', $idParents).')';
        }
        if ($rootNodeId) {
            $sql .= ' AND c.`id_category` = '.(int)$rootNodeId;
        }

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Get all the hotels of the passed cities (city id_category)
     */
    protected static function getHotelsByIdCity($cities, $rootNodeId, $prefix = '')
    {

        $sql = 'SELECT hbi.`id` as `value` , hbil.`hotel_name` as `name`, "'.$prefix.'hotel_box" as `input_name`, c.`id_parent`
        FROM `'._DB_PREFIX_.'htl_branch_info` AS hbi
        INNER JOIN `'._DB_PREFIX_.'htl_branch_info_lang` AS hbil
        ON (hbi.`id` = hbil.`id` AND hbil.`id_lang`='.(int)Context::getContext()->language->id.')
        INNER JOIN `'._DB_PREFIX_.'category` AS c ON (hbi.`id_category` = c.`id_category`)
        WHERE 1';

        if ($cities) {
            $sql .= ' AND c.`id_parent` IN ('.implode(', ', $cities).')';
        }
        if ($rootNodeId) {
            $sql .= ' AND hbi.`id` = '.(int)$rootNodeId;
        }
        return Db::getInstance()->executeS($sql);
    }

    /**
     * Get all the room types of the passed hotels
     */
    protected static function getRoomTypesByHotelsId($hotels, $rootNodeId, $prefix = '')
    {
        $sql = 'SELECT p.`id_product` AS `value`, pl.`name`, "'.$prefix.'room_type_box" as `input_name`, rt.`id_hotel`
			FROM `'._DB_PREFIX_.'htl_room_type` AS rt';

        $sql .= ' INNER JOIN `'._DB_PREFIX_.'product` AS p ON (rt.`id_product` = p.`id_product`)
            INNER JOIN `'._DB_PREFIX_.'product_lang` AS pl
            ON (p.`id_product` = pl.`id_product` AND pl.`id_lang`='.(int)Context::getContext()->language->id.')
            WHERE 1';

        if ($hotels) {
            $sql .= ' AND rt.`id_hotel` IN ('.implode(', ', $hotels).')';
        }
        if ($rootNodeId) {
            $sql .= ' AND p.`id_product` = '.(int)$rootNodeId;
        }

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Get all the rooms of the passed room types (city id_category)
     */
    protected static function getRoomsByRoomTypeId($roomTypes, $rootNodeId, $prefix = '')
    {
        $sql = 'SELECT `id` as `value`, `room_num` as `name`, "'.$prefix.'room_box" as `input_name`, `id_product`
        FROM `'._DB_PREFIX_.'htl_room_information`
        WHERE 1';
        if ($roomTypes) {
            $sql .= ' AND `id_product` IN ('.implode(', ', $roomTypes).')';
        }
        if ($rootNodeId) {
            $sql .= ' AND `id` = '.(int)$rootNodeId;
        }

        return Db::getInstance()->executeS($sql);
    }
}
