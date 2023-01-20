// jquery.daterangepicker.js
// author : Chunlong Liu
// license : MIT
// www.jszen.com

(function(factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery', 'moment'], factory);
    } else if (typeof exports === 'object' && typeof module !== 'undefined') {
        // CommonJS. Register as a module
        module.exports = factory(require('jquery'), require('moment'));
    } else {
        // Browser globals
        factory(jQuery, moment);
    }
}(function($, moment) {
    'use strict';
    $.dateRangePickerLanguages = {
        "default": //default language: English
        {
            "selected": "Selected:",
            "day": "Day",
            "days": "Days",
            "apply": "Close",
            "week-1": "mo",
            "week-2": "tu",
            "week-3": "we",
            "week-4": "th",
            "week-5": "fr",
            "week-6": "sa",
            "week-7": "su",
            "week-number": "W",
            "month-name": ["january", "february", "march", "april", "may", "june", "july", "august", "september", "october", "november", "december"],
            "shortcuts": "Shortcuts",
            "custom-values": "Custom Values",
            "past": "Past",
            "following": "Following",
            "previous": "Previous",
            "prev-week": "Week",
            "prev-month": "Month",
            "prev-year": "Year",
            "next": "Next",
            "next-week": "Week",
            "next-month": "Month",
            "next-year": "Year",
            "less-than": "Date range should not be more than %d days",
            "more-than": "Date range should not be less than %d days",
            "default-more": "Please select a date range longer than %d days",
            "default-single": "Please select a date",
            "default-less": "Please select a date range less than %d days",
            "default-range": "Please select a date range between %d and %d days",
            "default-default": "Please select a date range",
            "time": "Time",
            "hour": "Hour",
            "minute": "Minute"
        },
        "id": {
            "selected": "Terpilih:",
            "day": "Hari",
            "days": "Hari",
            "apply": "Tutup",
            "week-1": "sen",
            "week-2": "sel",
            "week-3": "rab",
            "week-4": "kam",
            "week-5": "jum",
            "week-6": "sab",
            "week-7": "min",
            "week-number": "W",
            "month-name": ["januari", "februari", "maret", "april", "mei", "juni", "juli", "agustus", "september", "oktober", "november", "desember"],
            "shortcuts": "Pintas",
            "custom-values": "Nilai yang ditentukan",
            "past": "Yang Lalu",
            "following": "Mengikuti",
            "previous": "Sebelumnya",
            "prev-week": "Minggu",
            "prev-month": "Bulan",
            "prev-year": "Tahun",
            "next": "Selanjutnya",
            "next-week": "Minggu",
            "next-month": "Bulan",
            "next-year": "Tahun",
            "less-than": "Tanggal harus lebih dari %d hari",
            "more-than": "Tanggal harus kurang dari %d hari",
            "default-more": "Jarak tanggal harus lebih lama dari %d hari",
            "default-single": "Silakan pilih tanggal",
            "default-less": "Jarak rentang tanggal tidak boleh lebih lama dari %d hari",
            "default-range": "Rentang tanggal harus antara %d dan %d hari",
            "default-default": "Silakan pilih rentang tanggal",
            "time": "Waktu",
            "hour": "Jam",
            "minute": "Menit"
        },
        "az": {
            "selected": "Seçildi:",
            "day": " gün",
            "days": " gün",
            "apply": "tətbiq",
            "week-1": "1",
            "week-2": "2",
            "week-3": "3",
            "week-4": "4",
            "week-5": "5",
            "week-6": "6",
            "week-7": "7",
            "month-name": ["yanvar", "fevral", "mart", "aprel", "may", "iyun", "iyul", "avqust", "sentyabr", "oktyabr", "noyabr", "dekabr"],
            "shortcuts": "Qısayollar",
            "past": "Keçmiş",
            "following": "Növbəti",
            "previous": "&nbsp;&nbsp;&nbsp;",
            "prev-week": "Öncəki həftə",
            "prev-month": "Öncəki ay",
            "prev-year": "Öncəki il",
            "next": "&nbsp;&nbsp;&nbsp;",
            "next-week": "Növbəti həftə",
            "next-month": "Növbəti ay",
            "next-year": "Növbəti il",
            "less-than": "Tarix aralığı %d gündən çox olmamalıdır",
            "more-than": "Tarix aralığı %d gündən az olmamalıdır",
            "default-more": "%d gündən çox bir tarix seçin",
            "default-single": "Tarix seçin",
            "default-less": "%d gündən az bir tarix seçin",
            "default-range": "%d və %d gün aralığında tarixlər seçin",
            "default-default": "Tarix aralığı seçin"
        },
        "bg": {
            "selected": "Избрано:",
            "day": "Ден",
            "days": "Дни",
            "apply": "Затвори",
            "week-1": "пн",
            "week-2": "вт",
            "week-3": "ср",
            "week-4": "чт",
            "week-5": "пт",
            "week-6": "сб",
            "week-7": "нд",
            "week-number": "С",
            "month-name": ["януари", "февруари", "март", "април", "май", "юни", "юли", "август", "септември", "октомври", "ноември", "декември"],
            "shortcuts": "Преки пътища",
            "custom-values": "Персонализирани стойности",
            "past": "Минал",
            "following": "Следващ",
            "previous": "Предишен",
            "prev-week": "Седмица",
            "prev-month": "Месец",
            "prev-year": "Година",
            "next": "Следващ",
            "next-week": "Седмица",
            "next-month": "Месец",
            "next-year": "Година",
            "less-than": "Периодът от време не трябва да е повече от %d дни",
            "more-than": "Периодът от време не трябва да е по-малко от %d дни",
            "default-more": "Моля изберете период по-дълъг от %d дни",
            "default-single": "Моля изберете дата",
            "default-less": "Моля изберете период по-къс от %d дни",
            "default-range": "Моля изберете период между %d и %d дни",
            "default-default": "Моля изберете период",
            "time": "Време",
            "hour": "Час",
            "minute": "Минута"
        },
        "cn": //simplified chinese
        {
            "selected": "已选择:",
            "day": "天",
            "days": "天",
            "apply": "确定",
            "week-1": "一",
            "week-2": "二",
            "week-3": "三",
            "week-4": "四",
            "week-5": "五",
            "week-6": "六",
            "week-7": "日",
            "week-number": "周",
            "month-name": ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
            "shortcuts": "快捷选择",
            "past": "过去",
            "following": "将来",
            "previous": "&nbsp;&nbsp;&nbsp;",
            "prev-week": "上周",
            "prev-month": "上个月",
            "prev-year": "去年",
            "next": "&nbsp;&nbsp;&nbsp;",
            "next-week": "下周",
            "next-month": "下个月",
            "next-year": "明年",
            "less-than": "所选日期范围不能大于%d天",
            "more-than": "所选日期范围不能小于%d天",
            "default-more": "请选择大于%d天的日期范围",
            "default-less": "请选择小于%d天的日期范围",
            "default-range": "请选择%d天到%d天的日期范围",
            "default-single": "请选择一个日期",
            "default-default": "请选择一个日期范围",
            "time": "时间",
            "hour": "小时",
            "minute": "分钟"
        },
        "cz": {
            "selected": "Vybráno:",
            "day": "Den",
            "days": "Dny",
            "apply": "Zavřít",
            "week-1": "po",
            "week-2": "út",
            "week-3": "st",
            "week-4": "čt",
            "week-5": "pá",
            "week-6": "so",
            "week-7": "ne",
            "month-name": ["leden", "únor", "březen", "duben", "květen", "červen", "červenec", "srpen", "září", "říjen", "listopad", "prosinec"],
            "shortcuts": "Zkratky",
            "past": "po",
            "following": "následující",
            "previous": "předchozí",
            "prev-week": "týden",
            "prev-month": "měsíc",
            "prev-year": "rok",
            "next": "další",
            "next-week": "týden",
            "next-month": "měsíc",
            "next-year": "rok",
            "less-than": "Rozsah data by neměl být větší než %d dnů",
            "more-than": "Rozsah data by neměl být menší než %d dnů",
            "default-more": "Prosím zvolte rozsah data větší než %d dnů",
            "default-single": "Prosím zvolte datum",
            "default-less": "Prosím zvolte rozsah data menší než %d dnů",
            "default-range": "Prosím zvolte rozsah data mezi %d a %d dny",
            "default-default": "Prosím zvolte rozsah data"
        },
        "de": {
            "selected": "Auswahl:",
            "day": "Tag",
            "days": "Tage",
            "apply": "Schließen",
            "week-1": "mo",
            "week-2": "di",
            "week-3": "mi",
            "week-4": "do",
            "week-5": "fr",
            "week-6": "sa",
            "week-7": "so",
            "month-name": ["januar", "februar", "märz", "april", "mai", "juni", "juli", "august", "september", "oktober", "november", "dezember"],
            "shortcuts": "Schnellwahl",
            "past": "Vorherige",
            "following": "Folgende",
            "previous": "Vorherige",
            "prev-week": "Woche",
            "prev-month": "Monat",
            "prev-year": "Jahr",
            "next": "Nächste",
            "next-week": "Woche",
            "next-month": "Monat",
            "next-year": "Jahr",
            "less-than": "Datumsbereich darf nicht größer sein als %d Tage",
            "more-than": "Datumsbereich darf nicht kleiner sein als %d Tage",
            "default-more": "Bitte mindestens %d Tage auswählen",
            "default-single": "Bitte ein Datum auswählen",
            "default-less": "Bitte weniger als %d Tage auswählen",
            "default-range": "Bitte einen Datumsbereich zwischen %d und %d Tagen auswählen",
            "default-default": "Bitte ein Start- und Enddatum auswählen",
            "Time": "Zeit",
            "hour": "Stunde",
            "minute": "Minute"
        },
        "es": {
            "selected": "Seleccionado:",
            "day": "Día",
            "days": "Días",
            "apply": "Cerrar",
            "week-1": "lu",
            "week-2": "ma",
            "week-3": "mi",
            "week-4": "ju",
            "week-5": "vi",
            "week-6": "sa",
            "week-7": "do",
            "month-name": ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"],
            "shortcuts": "Accesos directos",
            "past": "Pasado",
            "following": "Siguiente",
            "previous": "Anterior",
            "prev-week": "Semana",
            "prev-month": "Mes",
            "prev-year": "Año",
            "next": "Siguiente",
            "next-week": "Semana",
            "next-month": "Mes",
            "next-year": "Año",
            "less-than": "El rango no debería ser mayor de %d días",
            "more-than": "El rango no debería ser menor de %d días",
            "default-more": "Por favor selecciona un rango mayor a %d días",
            "default-single": "Por favor selecciona un día",
            "default-less": "Por favor selecciona un rango menor a %d días",
            "default-range": "Por favor selecciona un rango entre %d y %d días",
            "default-default": "Por favor selecciona un rango de fechas."
        },
        "fr": {
            "selected": "Sélection:",
            "day": "Jour",
            "days": "Jours",
            "apply": "Fermer",
            "week-1": "lu",
            "week-2": "ma",
            "week-3": "me",
            "week-4": "je",
            "week-5": "ve",
            "week-6": "sa",
            "week-7": "di",
            "month-name": ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"],
            "shortcuts": "Raccourcis",
            "past": "Passé",
            "following": "Suivant",
            "previous": "Précédent",
            "prev-week": "Semaine",
            "prev-month": "Mois",
            "prev-year": "Année",
            "next": "Suivant",
            "next-week": "Semaine",
            "next-month": "Mois",
            "next-year": "Année",
            "less-than": "L'intervalle ne doit pas être supérieure à %d jours",
            "more-than": "L'intervalle ne doit pas être inférieure à %d jours",
            "default-more": "Merci de choisir une intervalle supérieure à %d jours",
            "default-single": "Merci de choisir une date",
            "default-less": "Merci de choisir une intervalle inférieure %d jours",
            "default-range": "Merci de choisir une intervalle comprise entre %d et %d jours",
            "default-default": "Merci de choisir une date"
        },
        "hu": {
            "selected": "Kiválasztva:",
            "day": "Nap",
            "days": "Nap",
            "apply": "Ok",
            "week-1": "h",
            "week-2": "k",
            "week-3": "sz",
            "week-4": "cs",
            "week-5": "p",
            "week-6": "sz",
            "week-7": "v",
            "month-name": ["január", "február", "március", "április", "május", "június", "július", "augusztus", "szeptember", "október", "november", "december"],
            "shortcuts": "Gyorsválasztó",
            "past": "Múlt",
            "following": "Következő",
            "previous": "Előző",
            "prev-week": "Hét",
            "prev-month": "Hónap",
            "prev-year": "Év",
            "next": "Következő",
            "next-week": "Hét",
            "next-month": "Hónap",
            "next-year": "Év",
            "less-than": "A kiválasztás nem lehet több %d napnál",
            "more-than": "A kiválasztás nem lehet több %d napnál",
            "default-more": "Válassz ki egy időszakot ami hosszabb mint %d nap",
            "default-single": "Válassz egy napot",
            "default-less": "Válassz ki egy időszakot ami rövidebb mint %d nap",
            "default-range": "Válassz ki egy %d - %d nap hosszú időszakot",
            "default-default": "Válassz ki egy időszakot"
        },
        "it": {
            "selected": "Selezionati:",
            "day": "Giorno",
            "days": "Giorni",
            "apply": "Chiudi",
            "week-1": "lu",
            "week-2": "ma",
            "week-3": "me",
            "week-4": "gi",
            "week-5": "ve",
            "week-6": "sa",
            "week-7": "do",
            "month-name": ["gennaio", "febbraio", "marzo", "aprile", "maggio", "giugno", "luglio", "agosto", "settembre", "ottobre", "novembre", "dicembre"],
            "shortcuts": "Scorciatoie",
            "past": "Scorso",
            "following": "Successivo",
            "previous": "Precedente",
            "prev-week": "Settimana",
            "prev-month": "Mese",
            "prev-year": "Anno",
            "next": "Prossimo",
            "next-week": "Settimana",
            "next-month": "Mese",
            "next-year": "Anno",
            "less-than": "L'intervallo non dev'essere maggiore di %d giorni",
            "more-than": "L'intervallo non dev'essere minore di %d giorni",
            "default-more": "Seleziona un intervallo maggiore di %d giorni",
            "default-single": "Seleziona una data",
            "default-less": "Seleziona un intervallo minore di %d giorni",
            "default-range": "Seleziona un intervallo compreso tra i %d e i %d giorni",
            "default-default": "Seleziona un intervallo di date"
        },
        "ko": {
            "selected": "기간:",
            "day": "일",
            "days": "일간",
            "apply": "닫기",
            "week-1": "월",
            "week-2": "화",
            "week-3": "수",
            "week-4": "목",
            "week-5": "금",
            "week-6": "토",
            "week-7": "일",
            "week-number": "주",
            "month-name": ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
            "shortcuts": "단축키들",
            "past": "지난(오늘기준)",
            "following": "이후(오늘기준)",
            "previous": "이전",
            "prev-week": "1주",
            "prev-month": "1달",
            "prev-year": "1년",
            "next": "다음",
            "next-week": "1주",
            "next-month": "1달",
            "next-year": "1년",
            "less-than": "날짜 범위는 %d 일보다 많을 수 없습니다",
            "more-than": "날짜 범위는 %d 일보다 작을 수 없습니다",
            "default-more": "날짜 범위를 %d 일보다 길게 선택해 주세요",
            "default-single": "날짜를 선택해 주세요",
            "default-less": "%d 일보다 작은 날짜를 선택해 주세요",
            "default-range": "%d와 %d 일 사이의 날짜 범위를 선택해 주세요",
            "default-default": "날짜 범위를 선택해 주세요",
            "time": "시각",
            "hour": "시",
            "minute": "분"
        },
        "no": {
            "selected": "Valgt:",
            "day": "Dag",
            "days": "Dager",
            "apply": "Lukk",
            "week-1": "ma",
            "week-2": "ti",
            "week-3": "on",
            "week-4": "to",
            "week-5": "fr",
            "week-6": "lø",
            "week-7": "sø",
            "month-name": ["januar", "februar", "mars", "april", "mai", "juni", "juli", "august", "september", "oktober", "november", "desember"],
            "shortcuts": "Snarveier",
            "custom-values": "Egendefinerte Verdier",
            "past": "Over", // Not quite sure about the context of this one
            "following": "Følger",
            "previous": "Forrige",
            "prev-week": "Uke",
            "prev-month": "Måned",
            "prev-year": "År",
            "next": "Neste",
            "next-week": "Uke",
            "next-month": "Måned",
            "next-year": "År",
            "less-than": "Datoperioden skal ikkje være lengre enn %d dager",
            "more-than": "Datoperioden skal ikkje være kortere enn %d dager",
            "default-more": "Vennligst velg ein datoperiode lengre enn %d dager",
            "default-single": "Vennligst velg ein dato",
            "default-less": "Vennligst velg ein datoperiode mindre enn %d dager",
            "default-range": "Vennligst velg ein datoperiode mellom %d og %d dager",
            "default-default": "Vennligst velg ein datoperiode",
            "time": "Tid",
            "hour": "Time",
            "minute": "Minutter"
        },
        "nl": {
            "selected": "Geselecteerd:",
            "day": "Dag",
            "days": "Dagen",
            "apply": "Ok",
            "week-1": "ma",
            "week-2": "di",
            "week-3": "wo",
            "week-4": "do",
            "week-5": "vr",
            "week-6": "za",
            "week-7": "zo",
            "month-name": ["januari", "februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december"],
            "shortcuts": "Snelkoppelingen",
            "custom-values": "Aangepaste waarden",
            "past": "Verleden",
            "following": "Komend",
            "previous": "Vorige",
            "prev-week": "Week",
            "prev-month": "Maand",
            "prev-year": "Jaar",
            "next": "Volgende",
            "next-week": "Week",
            "next-month": "Maand",
            "next-year": "Jaar",
            "less-than": "Interval moet langer dan %d dagen zijn",
            "more-than": "Interval mag niet minder dan %d dagen zijn",
            "default-more": "Selecteer een interval langer dan %dagen",
            "default-single": "Selecteer een datum",
            "default-less": "Selecteer een interval minder dan %d dagen",
            "default-range": "Selecteer een interval tussen %d en %d dagen",
            "default-default": "Selecteer een interval",
            "time": "Tijd",
            "hour": "Uur",
            "minute": "Minuut"
        },
        "ru": {
            "selected": "Выбрано:",
            "day": "День",
            "days": "Дней",
            "apply": "Применить",
            "week-1": "пн",
            "week-2": "вт",
            "week-3": "ср",
            "week-4": "чт",
            "week-5": "пт",
            "week-6": "сб",
            "week-7": "вс",
            "month-name": ["январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь"],
            "shortcuts": "Быстрый выбор",
            "custom-values": "Пользовательские значения",
            "past": "Прошедшие",
            "following": "Следующие",
            "previous": "&nbsp;&nbsp;&nbsp;",
            "prev-week": "Неделя",
            "prev-month": "Месяц",
            "prev-year": "Год",
            "next": "&nbsp;&nbsp;&nbsp;",
            "next-week": "Неделя",
            "next-month": "Месяц",
            "next-year": "Год",
            "less-than": "Диапазон не может быть больше %d дней",
            "more-than": "Диапазон не может быть меньше %d дней",
            "default-more": "Пожалуйста выберите диапазон больше %d дней",
            "default-single": "Пожалуйста выберите дату",
            "default-less": "Пожалуйста выберите диапазон меньше %d дней",
            "default-range": "Пожалуйста выберите диапазон между %d и %d днями",
            "default-default": "Пожалуйста выберите диапазон",
            "time": "Время",
            "hour": "Часы",
            "minute": "Минуты"
        },
        "uk": {
            "selected": "Вибрано:",
            "day": "День",
            "days": "Днів",
            "apply": "Застосувати",
            "week-1": "пн",
            "week-2": "вт",
            "week-3": "ср",
            "week-4": "чт",
            "week-5": "пт",
            "week-6": "сб",
            "week-7": "нд",
            "month-name": ["січень", "лютий", "березень", "квітень", "травень", "червень", "липень", "серпень", "вересень", "жовтень", "листопад", "грудень"],
            "shortcuts": "Швидкий вибір",
            "custom-values": "Значення користувача",
            "past": "Минулі",
            "following": "Наступні",
            "previous": "&nbsp;&nbsp;&nbsp;",
            "prev-week": "Тиждень",
            "prev-month": "Місяць",
            "prev-year": "Рік",
            "next": "&nbsp;&nbsp;&nbsp;",
            "next-week": "Тиждень",
            "next-month": "Місяць",
            "next-year": "Рік",
            "less-than": "Діапазон не може бути більш ніж %d днів",
            "more-than": "Діапазон не може бути меньш ніж %d днів",
            "default-more": "Будь ласка виберіть діапазон більше %d днів",
            "default-single": "Будь ласка виберіть дату",
            "default-less": "Будь ласка виберіть діапазон менше %d днів",
            "default-range": "Будь ласка виберіть діапазон між %d та %d днями",
            "default-default": "Будь ласка виберіть діапазон",
            "time": "Час",
            "hour": "Години",
            "minute": "Хвилини"
        },
        "pl": {
            "selected": "Wybrany:",
            "day": "Dzień",
            "days": "Dni",
            "apply": "Zamknij",
            "week-1": "pon",
            "week-2": "wt",
            "week-3": "śr",
            "week-4": "czw",
            "week-5": "pt",
            "week-6": "so",
            "week-7": "nd",
            "month-name": ["styczeń", "luty", "marzec", "kwiecień", "maj", "czerwiec", "lipiec", "sierpień", "wrzesień", "październik", "listopad", "grudzień"],
            "shortcuts": "Skróty",
            "custom-values": "Niestandardowe wartości",
            "past": "Przeszłe",
            "following": "Następne",
            "previous": "Poprzednie",
            "prev-week": "tydzień",
            "prev-month": "miesiąc",
            "prev-year": "rok",
            "next": "Następny",
            "next-week": "tydzień",
            "next-month": "miesiąc",
            "next-year": "rok",
            "less-than": "Okres nie powinien być dłuższy niż %d dni",
            "more-than": "Okres nie powinien być krótszy niż  %d ni",
            "default-more": "Wybierz okres dłuższy niż %d dni",
            "default-single": "Wybierz datę",
            "default-less": "Wybierz okres krótszy niż %d dni",
            "default-range": "Wybierz okres trwający od %d do %d dni",
            "default-default": "Wybierz okres",
            "time": "Czas",
            "hour": "Godzina",
            "minute": "Minuta"
        },
        "se": {
            "selected": "Vald:",
            "day": "dag",
            "days": "dagar",
            "apply": "godkänn",
            "week-1": "ma",
            "week-2": "ti",
            "week-3": "on",
            "week-4": "to",
            "week-5": "fr",
            "week-6": "lö",
            "week-7": "sö",
            "month-name": ["januari", "februari", "mars", "april", "maj", "juni", "juli", "augusti", "september", "oktober", "november", "december"],
            "shortcuts": "genvägar",
            "custom-values": "Anpassade värden",
            "past": "över",
            "following": "följande",
            "previous": "förra",
            "prev-week": "vecka",
            "prev-month": "månad",
            "prev-year": "år",
            "next": "nästa",
            "next-week": "vecka",
            "next-month": "måned",
            "next-year": "år",
            "less-than": "Datumintervall bör inte vara mindre än %d dagar",
            "more-than": "Datumintervall bör inte vara mer än %d dagar",
            "default-more": "Välj ett datumintervall längre än %d dagar",
            "default-single": "Välj ett datum",
            "default-less": "Välj ett datumintervall mindre än %d dagar",
            "default-range": "Välj ett datumintervall mellan %d och %d dagar",
            "default-default": "Välj ett datumintervall",
            "time": "tid",
            "hour": "timme",
            "minute": "minut"
        },
        "pt": //Portuguese (European)
        {
            "selected": "Selecionado:",
            "day": "Dia",
            "days": "Dias",
            "apply": "Fechar",
            "week-1": "seg",
            "week-2": "ter",
            "week-3": "qua",
            "week-4": "qui",
            "week-5": "sex",
            "week-6": "sab",
            "week-7": "dom",
            "week-number": "N",
            "month-name": ["janeiro", "fevereiro", "março", "abril", "maio", "junho", "julho", "agosto", "setembro", "outubro", "novembro", "dezembro"],
            "shortcuts": "Atalhos",
            "custom-values": "Valores Personalizados",
            "past": "Passado",
            "following": "Seguinte",
            "previous": "Anterior",
            "prev-week": "Semana",
            "prev-month": "Mês",
            "prev-year": "Ano",
            "next": "Próximo",
            "next-week": "Próxima Semana",
            "next-month": "Próximo Mês",
            "next-year": "Próximo Ano",
            "less-than": "O período selecionado não deve ser maior que %d dias",
            "more-than": "O período selecionado não deve ser menor que %d dias",
            "default-more": "Selecione um período superior a %d dias",
            "default-single": "Selecione uma data",
            "default-less": "Selecione um período inferior a %d dias",
            "default-range": "Selecione um período de %d a %d dias",
            "default-default": "Selecione um período",
            "time": "Tempo",
            "hour": "Hora",
            "minute": "Minuto"
        },
        "tc": // traditional chinese
        {
            "selected": "已選擇:",
            "day": "天",
            "days": "天",
            "apply": "確定",
            "week-1": "一",
            "week-2": "二",
            "week-3": "三",
            "week-4": "四",
            "week-5": "五",
            "week-6": "六",
            "week-7": "日",
            "week-number": "週",
            "month-name": ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
            "shortcuts": "快速選擇",
            "past": "過去",
            "following": "將來",
            "previous": "&nbsp;&nbsp;&nbsp;",
            "prev-week": "上週",
            "prev-month": "上個月",
            "prev-year": "去年",
            "next": "&nbsp;&nbsp;&nbsp;",
            "next-week": "下週",
            "next-month": "下個月",
            "next-year": "明年",
            "less-than": "所選日期範圍不能大於%d天",
            "more-than": "所選日期範圍不能小於%d天",
            "default-more": "請選擇大於%d天的日期範圍",
            "default-less": "請選擇少於%d天的日期範圍",
            "default-range": "請選擇%d天到%d天的日期範圍",
            "default-single": "請選擇一個日期",
            "default-default": "請選擇一個日期範圍",
            "time": "日期",
            "hour": "小時",
            "minute": "分鐘"
        },
        "ja": {
            "selected": "選択しました:",
            "day": "日",
            "days": "日",
            "apply": "確定",
            "week-1": "月",
            "week-2": "火",
            "week-3": "水",
            "week-4": "木",
            "week-5": "金",
            "week-6": "土",
            "week-7": "日",
            "month-name": ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
            "shortcuts": "クイック選択",
            "past": "過去",
            "following": "将来",
            "previous": "&nbsp;&nbsp;&nbsp;",
            "prev-week": "先週",
            "prev-month": "先月",
            "prev-year": "昨年",
            "next": "&nbsp;&nbsp;&nbsp;",
            "next-week": "来週",
            "next-month": "来月",
            "next-year": "来年",
            "less-than": "日付の範囲は％d日以上にすべきではありません",
            "more-than": "日付の範囲は％d日を下回ってはいけません",
            "default-more": "％d日よりも長い期間を選択してください",
            "default-less": "％d日未満の期間を選択してください",
            "default-range": "％d日と％d日の間の日付範囲を選択してください",
            "default-single": "日付を選択してください",
            "default-default": "日付範囲を選択してください",
            "time": "時間",
            "hour": "時間",
            "minute": "分"
        },
        "da": {
            "selected": "Valgt:",
            "day": "Dag",
            "days": "Dage",
            "apply": "Luk",
            "week-1": "ma",
            "week-2": "ti",
            "week-3": "on",
            "week-4": "to",
            "week-5": "fr",
            "week-6": "lø",
            "week-7": "sø",
            "month-name": ["januar", "februar", "marts", "april", "maj", "juni", "juli", "august", "september", "oktober", "november", "december"],
            "shortcuts": "genveje",
            "custom-values": "Brugerdefinerede værdier",
            "past": "Forbi",
            "following": "Følgende",
            "previous": "Forrige",
            "prev-week": "uge",
            "prev-month": "måned",
            "prev-year": "år",
            "next": "Næste",
            "next-week": "uge",
            "next-month": "måned",
            "next-year": "år",
            "less-than": "Dato interval bør ikke være med end %d dage",
            "more-than": "Dato interval bør ikke være mindre end %d dage",
            "default-more": "Vælg datointerval længere end %d dage",
            "default-single": "Vælg dato",
            "default-less": "Vælg datointerval mindre end %d dage",
            "default-range": "Vælg datointerval mellem %d og %d dage",
            "default-default": "Vælg datointerval",
            "time": "tid",
            "hour": "time",
            "minute": "minut"
        },
        "fi": // Finnish
        {
            "selected": "Valittu:",
            "day": "Päivä",
            "days": "Päivää",
            "apply": "Sulje",
            "week-1": "ma",
            "week-2": "ti",
            "week-3": "ke",
            "week-4": "to",
            "week-5": "pe",
            "week-6": "la",
            "week-7": "su",
            "week-number": "V",
            "month-name": ["tammikuu", "helmikuu", "maaliskuu", "huhtikuu", "toukokuu", "kesäkuu", "heinäkuu", "elokuu", "syyskuu", "lokakuu", "marraskuu", "joulukuu"],
            "shortcuts": "Pikavalinnat",
            "custom-values": "Mukautetut Arvot",
            "past": "Menneet",
            "following": "Tulevat",
            "previous": "Edellinen",
            "prev-week": "Viikko",
            "prev-month": "Kuukausi",
            "prev-year": "Vuosi",
            "next": "Seuraava",
            "next-week": "Viikko",
            "next-month": "Kuukausi",
            "next-year": "Vuosi",
            "less-than": "Aikajakson tulisi olla vähemmän kuin %d päivää",
            "more-than": "Aikajakson ei tulisi olla vähempää kuin %d päivää",
            "default-more": "Valitse pidempi aikajakso kuin %d päivää",
            "default-single": "Valitse päivä",
            "default-less": "Valitse lyhyempi aikajakso kuin %d päivää",
            "default-range": "Valitse aikajakso %d ja %d päivän väliltä",
            "default-default": "Valitse aikajakso",
            "time": "Aika",
            "hour": "Tunti",
            "minute": "Minuutti"
        },
        "cat": // Catala
        {
            "selected": "Seleccionats:",
            "day": "Dia",
            "days": "Dies",
            "apply": "Tanca",
            "week-1": "Dl",
            "week-2": "Dm",
            "week-3": "Dc",
            "week-4": "Dj",
            "week-5": "Dv",
            "week-6": "Ds",
            "week-7": "Dg",
            "week-number": "S",
            "month-name": ["gener", "febrer", "març", "abril", "maig", "juny", "juliol", "agost", "setembre", "octubre", "novembre", "desembre"],
            "shortcuts": "Dreçeres",
            "custom-values": "Valors personalitzats",
            "past": "Passat",
            "following": "Futur",
            "previous": "Anterior",
            "prev-week": "Setmana",
            "prev-month": "Mes",
            "prev-year": "Any",
            "next": "Següent",
            "next-week": "Setmana",
            "next-month": "Mes",
            "next-year": "Any",
            "less-than": "El període no hauria de ser de més de %d dies",
            "more-than": "El període no hauria de ser de menys de %d dies",
            "default-more": "Perfavor selecciona un període més gran de %d dies",
            "default-single": "Perfavor selecciona una data",
            "default-less": "Perfavor selecciona un període de menys de %d dies",
            "default-range": "Perfavor selecciona un període d'entre %d i %d dies",
            "default-default": "Perfavor selecciona un període",
            "time": "Temps",
            "hour": "Hora",
            "minute": "Minut"
        },
        "sk": {
            "selected": "Vybrané:",
            "day": "Deň",
            "days": "Dni",
            "apply": "Zavrieť",
            "week-1": "po",
            "week-2": "út",
            "week-3": "st",
            "week-4": "št",
            "week-5": "pi",
            "week-6": "so",
            "week-7": "ne",
            "week-number": "T",
            "month-name": ["január", "február", "marec", "apríl", "máj", "jún", "júl", "august", "september", "október", "november", "december"],
            "shortcuts": "Skratky",
            "custom-values": "Vlastné hodnoty",
            "past": "po",
            "following": "nasledujúci",
            "previous": "predchádzajúci",
            "prev-week": "týždeň",
            "prev-month": "mesiac",
            "prev-year": "rok",
            "next": "ďalší",
            "next-week": "týždeň",
            "next-month": "mesiac",
            "next-year": "rok",
            "less-than": "Rozsah dátumu by nemal byť väčší ako %d dní",
            "more-than": "Rozsah dátumu by nemal byť menší ako %d dní",
            "default-more": "Prosím zvoľte rozsah dlhší ako %d dní",
            "default-single": "Prosím zvoľte dátum",
            "default-less": "Prosím zvoľte rozsah menší ako %d dní",
            "default-range": "Prosím zvoľte rozsah medzi %d a %d dňami",
            "default-default": "Prosím zvoľte rozsah",
            "time": "Čas",
            "hour": "Hodina",
            "minute": "Minúta"
        }
    };

    $.fn.dateRangePicker = function(opt) {
        if (!opt) opt = {};
        opt = $.extend(true, {
            autoClose: false,
            format: 'YYYY-MM-DD',
            separator: ' to ',
            language: 'auto',
            startOfWeek: 'sunday', // or monday
            getValue: function() {
                return $(this).val();
            },
            setValue: function(s) {
                if (!$(this).attr('readonly') && !$(this).is(':disabled') && s != $(this).val()) {
                    $(this).val(s);
                }
            },
            startDate: false,
            endDate: false,
            time: {
                enabled: false
            },
            minDays: 0,
            maxDays: 0,
            showShortcuts: false,
            shortcuts: {
                //'prev-days': [1,3,5,7],
                // 'next-days': [3,5,7],
                //'prev' : ['week','month','year'],
                // 'next' : ['week','month','year']
            },
            customShortcuts: [],
            inline: false,
            container: 'body',
            alwaysOpen: false,
            singleDate: false,
            lookBehind: false,
            batchMode: false,
            duration: 200,
            stickyMonths: false,
            dayDivAttrs: [],
            dayTdAttrs: [],
            selectForward: false,
            selectBackward: false,
            applyBtnClass: '',
            singleMonth: 'auto',
            hoveringTooltip: function(days, startTime, hoveringTime) {
                return days > 1 ? days + ' ' + translate('days') : '';
            },
            showTopbar: true,
            swapTime: false,
            showWeekNumbers: false,
            getWeekNumber: function(date) //date will be the first day of a week
            {
                return moment(date).format('w');
            },
            customOpenAnimation: null,
            customCloseAnimation: null,
            customArrowPrevSymbol: null,
            customArrowNextSymbol: null,
            monthSelect: false,
            yearSelect: false
        }, opt);

        opt.start = false;
        opt.end = false;

        opt.startWeek = false;

        //detect a touch device
        opt.isTouchDevice = 'ontouchstart' in window || navigator.msMaxTouchPoints;

        //if it is a touch device, hide hovering tooltip
        if (opt.isTouchDevice) opt.hoveringTooltip = false;

        //show one month on mobile devices
        if (opt.singleMonth == 'auto') opt.singleMonth = $(window).width() < 480;
        if (opt.singleMonth) opt.stickyMonths = false;

        if (!opt.showTopbar) opt.autoClose = true;

        if (opt.startDate && typeof opt.startDate == 'string') opt.startDate = moment(opt.startDate, opt.format).toDate();
        if (opt.endDate && typeof opt.endDate == 'string') opt.endDate = moment(opt.endDate, opt.format).toDate();

        if (opt.yearSelect && typeof opt.yearSelect === 'boolean') {
            opt.yearSelect = function(current) { return [current - 5, current + 5]; }
        }

        var languages = getLanguages();
        var box;
        var initiated = false;
        var self = this;
        var selfDom = $(self).get(0);
        var domChangeTimer;

        $(this).off('.datepicker').on('click.datepicker', function(evt) {
            var isOpen = box.is(':visible');
            if (!isOpen) open(opt.duration);
        }).on('change.datepicker', function(evt) {
            checkAndSetDefaultValue();
        }).on('keyup.datepicker', function() {
            try {
                clearTimeout(domChangeTimer);
            } catch (e) {}
            domChangeTimer = setTimeout(function() {
                checkAndSetDefaultValue();
            }, 2000);
        });

        init_datepicker.call(this);

        if (opt.alwaysOpen) {
            open(0);
        }

        // expose some api
        $(this).data('dateRangePicker', {
            setStart: function(d1) {
                if (typeof d1 == 'string') {
                    d1 = moment(d1, opt.format).toDate();
                }

                opt.end = false;
                setSingleDate(d1);

                return this;
            },
            setEnd: function(d2, silent) {
                var start = new Date();
                start.setTime(opt.start);
                if (typeof d2 == 'string') {
                    d2 = moment(d2, opt.format).toDate();
                }
                setDateRange(start, d2, silent);
                return this;
            },
            setDateRange: function(d1, d2, silent) {
                if (typeof d1 == 'string' && typeof d2 == 'string') {
                    d1 = moment(d1, opt.format).toDate();
                    d2 = moment(d2, opt.format).toDate();
                }
                setDateRange(d1, d2, silent);
            },
            clear: clearSelection,
            close: closeDatePicker,
            open: open,
            redraw: redrawDatePicker,
            getDatePicker: getDatePicker,
            resetMonthsView: resetMonthsView,
            destroy: function() {
                $(self).off('.datepicker');
                $(self).data('dateRangePicker', '');
                $(self).data('date-picker-opened', null);
                box.remove();
                $(window).off('resize.datepicker', calcPosition);
                $(document).off('click.datepicker', outsideClickClose);
            }
        });

        $(window).on('resize.datepicker', calcPosition);

        return this;

        function IsOwnDatePickerClicked(evt, selfObj) {
            return (selfObj.contains(evt.target) || evt.target == selfObj || (selfObj.childNodes != undefined && $.inArray(evt.target, selfObj.childNodes) >= 0));
        }

        function init_datepicker() {
            var self = this;

            if ($(this).data('date-picker-opened')) {
                closeDatePicker();
                return;
            }
            $(this).data('date-picker-opened', true);


            box = createDom().hide();
            box.append('<div class="date-range-length-tip"></div>');

            $(opt.container).append(box);

            if (!opt.inline) {
                calcPosition();
            } else {
                box.addClass('inline-wrapper');
            }

            if (opt.alwaysOpen) {
                box.find('.apply-btn').hide();
            }

            var defaultTime = getDefaultTime();
            resetMonthsView(defaultTime);

            if (opt.time.enabled) {
                if ((opt.startDate && opt.endDate) || (opt.start && opt.end)) {
                    showTime(moment(opt.start || opt.startDate).toDate(), 'time1');
                    showTime(moment(opt.end || opt.endDate).toDate(), 'time2');
                } else {
                    var defaultEndTime = opt.defaultEndTime ? opt.defaultEndTime : defaultTime;
                    showTime(defaultTime, 'time1');
                    showTime(defaultEndTime, 'time2');
                }
            }

            //showSelectedInfo();


            var defaultTopText = '';
            if (opt.singleDate)
                defaultTopText = translate('default-single');
            else if (opt.minDays && opt.maxDays)
                defaultTopText = translate('default-range');
            else if (opt.minDays)
                defaultTopText = translate('default-more');
            else if (opt.maxDays)
                defaultTopText = translate('default-less');
            else
                defaultTopText = translate('default-default');

            box.find('.default-top').html(defaultTopText.replace(/\%d/, opt.minDays).replace(/\%d/, opt.maxDays));
            if (opt.singleMonth) {
                box.addClass('single-month');
            } else {
                box.addClass('two-months');
            }


            setTimeout(function() {
                updateCalendarWidth();
                initiated = true;
            }, 0);

            box.on('click', function(evt) {
                evt.stopPropagation();
            });

            //if user click other place of the webpage, close date range picker window
            $(document).on('click.datepicker', outsideClickClose);

            box.find('.next').on('click', function() {
                if (!opt.stickyMonths)
                    gotoNextMonth(this);
                else
                    gotoNextMonth_stickily(this);
            });

            function gotoNextMonth(self) {
                var isMonth2 = $(self).parents('table').hasClass('month2');
                var month = isMonth2 ? opt.month2 : opt.month1;
                month = nextMonth(month);
                if (!opt.singleMonth && !opt.singleDate && !isMonth2 && compare_month(month, opt.month2) >= 0 || isMonthOutOfBounds(month)) return;
                showMonth(month, isMonth2 ? 'month2' : 'month1');
                showGap();
            }

            function gotoNextMonth_stickily(self) {
                var nextMonth1 = nextMonth(opt.month1);
                var nextMonth2 = nextMonth(opt.month2);
                if (isMonthOutOfBounds(nextMonth2)) return;
                if (!opt.singleDate && compare_month(nextMonth1, nextMonth2) >= 0) return;
                showMonth(nextMonth1, 'month1');
                showMonth(nextMonth2, 'month2');
                showSelectedDays();
            }


            box.find('.prev').on('click', function() {
                if (!opt.stickyMonths)
                    gotoPrevMonth(this);
                else
                    gotoPrevMonth_stickily(this);
            });

            function gotoPrevMonth(self) {
                var isMonth2 = $(self).parents('table').hasClass('month2');
                var month = isMonth2 ? opt.month2 : opt.month1;
                month = prevMonth(month);
                if (isMonth2 && compare_month(month, opt.month1) <= 0 || isMonthOutOfBounds(month)) return;
                showMonth(month, isMonth2 ? 'month2' : 'month1');
                showGap();
            }

            function gotoPrevMonth_stickily(self) {
                var prevMonth1 = prevMonth(opt.month1);
                var prevMonth2 = prevMonth(opt.month2);
                if (isMonthOutOfBounds(prevMonth1)) return;
                if (!opt.singleDate && compare_month(prevMonth2, prevMonth1) <= 0) return;
                showMonth(prevMonth2, 'month2');
                showMonth(prevMonth1, 'month1');
                showSelectedDays();
            }

            box.attr('unselectable', 'on')
                .css('user-select', 'none')
                .on('selectstart', function(e) {
                    e.preventDefault();
                    return false;
                });

            box.find('.apply-btn').on('click', function() {
                closeDatePicker();
                var dateRange = getDateString(new Date(opt.start)) + opt.separator + getDateString(new Date(opt.end));
                $(self).trigger('datepicker-apply', {
                    'value': dateRange,
                    'date1': new Date(opt.start),
                    'date2': new Date(opt.end)
                });
            });

            box.find('[custom]').on('click', function() {
                var valueName = $(this).attr('custom');
                opt.start = false;
                opt.end = false;
                box.find('.day.checked').removeClass('checked');
                opt.setValue.call(selfDom, valueName);
                checkSelectionValid();
                showSelectedInfo(true);
                showSelectedDays();
                if (opt.autoClose) closeDatePicker();
            });

            box.find('[shortcut]').on('click', function() {
                var shortcut = $(this).attr('shortcut');
                var end = new Date(),
                    start = false;
                var dir;
                if (shortcut.indexOf('day') != -1) {
                    var day = parseInt(shortcut.split(',', 2)[1], 10);
                    start = new Date(new Date().getTime() + 86400000 * day);
                    end = new Date(end.getTime() + 86400000 * (day > 0 ? 1 : -1));
                } else if (shortcut.indexOf('week') != -1) {
                    dir = shortcut.indexOf('prev,') != -1 ? -1 : 1;
                    var stopDay;
                    if (dir == 1)
                        stopDay = opt.startOfWeek == 'monday' ? 1 : 0;
                    else
                        stopDay = opt.startOfWeek == 'monday' ? 0 : 6;

                    end = new Date(end.getTime() - 86400000);
                    while (end.getDay() != stopDay) end = new Date(end.getTime() + dir * 86400000);
                    start = new Date(end.getTime() + dir * 86400000 * 6);
                } else if (shortcut.indexOf('month') != -1) {
                    dir = shortcut.indexOf('prev,') != -1 ? -1 : 1;
                    if (dir == 1)
                        start = nextMonth(end);
                    else
                        start = prevMonth(end);
                    start.setDate(1);
                    end = nextMonth(start);
                    end.setDate(1);
                    end = new Date(end.getTime() - 86400000);
                } else if (shortcut.indexOf('year') != -1) {
                    dir = shortcut.indexOf('prev,') != -1 ? -1 : 1;
                    start = new Date();
                    start.setFullYear(end.getFullYear() + dir);
                    start.setMonth(0);
                    start.setDate(1);
                    end.setFullYear(end.getFullYear() + dir);
                    end.setMonth(11);
                    end.setDate(31);
                } else if (shortcut == 'custom') {
                    var name = $(this).html();
                    if (opt.customShortcuts && opt.customShortcuts.length > 0) {
                        for (var i = 0; i < opt.customShortcuts.length; i++) {
                            var sh = opt.customShortcuts[i];
                            if (sh.name == name) {
                                var data = [];
                                // try
                                // {
                                data = sh['dates'].call();
                                //}catch(e){}
                                if (data && data.length == 2) {
                                    start = data[0];
                                    end = data[1];
                                }

                                // if only one date is specified then just move calendars there
                                // move calendars to show this date's month and next months
                                if (data && data.length == 1) {
                                    var movetodate = data[0];
                                    showMonth(movetodate, 'month1');
                                    showMonth(nextMonth(movetodate), 'month2');
                                    showGap();
                                }

                                break;
                            }
                        }
                    }
                }
                if (start && end) {
                    setDateRange(start, end);
                    checkSelectionValid();
                }
            });

            box.find('.time1 input[type=range]').on('change touchmove', function(e) {
                var target = e.target,
                    hour = target.name == 'hour' ? $(target).val().replace(/^(\d{1})$/, '0$1') : undefined,
                    min = target.name == 'minute' ? $(target).val().replace(/^(\d{1})$/, '0$1') : undefined;
                setTime('time1', hour, min);
            });

            box.find('.time2 input[type=range]').on('change touchmove', function(e) {
                var target = e.target,
                    hour = target.name == 'hour' ? $(target).val().replace(/^(\d{1})$/, '0$1') : undefined,
                    min = target.name == 'minute' ? $(target).val().replace(/^(\d{1})$/, '0$1') : undefined;
                setTime('time2', hour, min);
            });

        }


        function calcPosition() {
            if (!opt.inline) {
                var offset = $(self).offset();
                if ($(opt.container).css('position') == 'relative') {
                    var containerOffset = $(opt.container).offset();
                    var leftIndent = Math.max(0, offset.left + box.outerWidth() - $('body').width() + 16);
                    box.css({
                        top: offset.top - containerOffset.top + $(self).outerHeight() + 4,
                        left: offset.left - containerOffset.left - leftIndent
                    });
                } else {
                    if (offset.left < 460) //left to right
                    {
                        box.css({
                            top: offset.top + $(self).outerHeight() + parseInt($('body').css('border-top') || 0, 10),
                            left: offset.left
                        });
                    } else {
                        box.css({
                            top: offset.top + $(self).outerHeight() + parseInt($('body').css('border-top') || 0, 10),
                            left: offset.left + $(self).width() - box.width() - 16
                        });
                    }
                }
            }
        }

        // Return the date picker wrapper element
        function getDatePicker() {
            return box;
        }

        function open(animationTime) {
            redrawDatePicker();
            checkAndSetDefaultValue();
            if (opt.customOpenAnimation) {
                opt.customOpenAnimation.call(box.get(0), function() {
                    $(self).trigger('datepicker-opened', {
                        relatedTarget: box
                    });
                });
            } else {
                box.slideDown(animationTime, function() {
                    $(self).trigger('datepicker-opened', {
                        relatedTarget: box
                    });
                });
            }
            $(self).trigger('datepicker-open', {
                relatedTarget: box
            });
            showGap();
            updateCalendarWidth();
            calcPosition();
        }

        function checkAndSetDefaultValue() {
            var __default_string = opt.getValue.call(selfDom);
            var defaults = __default_string ? __default_string.split(opt.separator) : '';

            if (defaults && ((defaults.length == 1 && opt.singleDate) || defaults.length >= 2)) {
                var ___format = opt.format;
                if (___format.match(/Do/)) {

                    ___format = ___format.replace(/Do/, 'D');
                    defaults[0] = defaults[0].replace(/(\d+)(th|nd|st)/, '$1');
                    if (defaults.length >= 2) {
                        defaults[1] = defaults[1].replace(/(\d+)(th|nd|st)/, '$1');
                    }
                }
                // set initiated  to avoid triggerring datepicker-change event
                initiated = false;
                if (defaults.length >= 2) {
                    setDateRange(getValidValue(defaults[0], ___format, moment.locale(opt.language)), getValidValue(defaults[1], ___format, moment.locale(opt.language)));
                } else if (defaults.length == 1 && opt.singleDate) {
                    setSingleDate(getValidValue(defaults[0], ___format, moment.locale(opt.language)));
                }

                initiated = true;
            }
        }

        function getValidValue(date, format, locale) {
            if (moment(date, format, locale).isValid()) {
                return moment(date, format, locale).toDate();
            } else {
                return moment().toDate();
            }
        }

        function updateCalendarWidth() {
            var gapMargin = box.find('.gap').css('margin-left');
            if (gapMargin) gapMargin = parseInt(gapMargin);
            var w1 = box.find('.month1').width();
            var w2 = box.find('.gap').width() + (gapMargin ? gapMargin * 2 : 0);
            var w3 = box.find('.month2').width();
            box.find('.month-wrapper').width(w1 + w2 + w3);
        }

        function renderTime(name, date) {
            box.find('.' + name + ' input[type=range].hour-range').val(moment(date).hours());
            box.find('.' + name + ' input[type=range].minute-range').val(moment(date).minutes());
            setTime(name, moment(date).format('HH'), moment(date).format('mm'));
        }

        function changeTime(name, date) {
            opt[name] = parseInt(
                moment(parseInt(date))
                .hour(moment(opt[name + 'Time']).format('HH'), 'h')
                .minute(moment(opt[name + 'Time']).format('mm'), 'm').valueOf()
            );
        }

        function swapTime() {
            renderTime('time1', opt.start);
            renderTime('time2', opt.end);
        }

        function setTime(name, hour, minute) {
            hour && (box.find('.' + name + ' .hour-val').text(hour));
            minute && (box.find('.' + name + ' .minute-val').text(minute));
            switch (name) {
                case 'time1':
                    if (opt.start) {
                        setRange('start', moment(opt.start));
                    }
                    setRange('startTime', moment(opt.startTime || moment().valueOf()));
                    break;
                case 'time2':
                    if (opt.end) {
                        setRange('end', moment(opt.end));
                    }
                    setRange('endTime', moment(opt.endTime || moment().valueOf()));
                    break;
            }

            function setRange(name, timePoint) {
                var h = timePoint.format('HH'),
                    m = timePoint.format('mm');
                opt[name] = timePoint
                    .hour(hour || h, 'h')
                    .minute(minute || m, 'm')
                    .valueOf();
            }
            checkSelectionValid();
            showSelectedInfo();
            showSelectedDays();
        }

        function clearSelection() {
            opt.start = false;
            opt.end = false;
            box.find('.day.checked').removeClass('checked');
            box.find('.day.last-date-selected').removeClass('last-date-selected');
            box.find('.day.first-date-selected').removeClass('first-date-selected');
            opt.setValue.call(selfDom, '');
            checkSelectionValid();
            showSelectedInfo();
            showSelectedDays();
        }

        function handleStart(time) {
            var r = time;
            if (opt.batchMode === 'week-range') {
                if (opt.startOfWeek === 'monday') {
                    r = moment(parseInt(time)).startOf('isoweek').valueOf();
                } else {
                    r = moment(parseInt(time)).startOf('week').valueOf();
                }
            } else if (opt.batchMode === 'month-range') {
                r = moment(parseInt(time)).startOf('month').valueOf();
            }
            return r;
        }

        function handleEnd(time) {
            var r = time;
            if (opt.batchMode === 'week-range') {
                if (opt.startOfWeek === 'monday') {
                    r = moment(parseInt(time)).endOf('isoweek').valueOf();
                } else {
                    r = moment(parseInt(time)).endOf('week').valueOf();
                }
            } else if (opt.batchMode === 'month-range') {
                r = moment(parseInt(time)).endOf('month').valueOf();
            }
            return r;
        }


        function dayClicked(day) {
            if (day.hasClass('invalid')) return;
            var time = day.attr('time');
            day.addClass('checked');
            if (opt.singleDate) {
                opt.start = time;
                opt.end = false;
            } else if (opt.batchMode === 'week') {
                if (opt.startOfWeek === 'monday') {
                    opt.start = moment(parseInt(time)).startOf('isoweek').valueOf();
                    opt.end = moment(parseInt(time)).endOf('isoweek').valueOf();
                } else {
                    opt.end = moment(parseInt(time)).endOf('week').valueOf();
                    opt.start = moment(parseInt(time)).startOf('week').valueOf();
                }
            } else if (opt.batchMode === 'workweek') {
                opt.start = moment(parseInt(time)).day(1).valueOf();
                opt.end = moment(parseInt(time)).day(5).valueOf();
            } else if (opt.batchMode === 'weekend') {
                opt.start = moment(parseInt(time)).day(6).valueOf();
                opt.end = moment(parseInt(time)).day(7).valueOf();
            } else if (opt.batchMode === 'month') {
                opt.start = moment(parseInt(time)).startOf('month').valueOf();
                opt.end = moment(parseInt(time)).endOf('month').valueOf();
            } else if ((opt.start && opt.end) || (!opt.start && !opt.end)) {
                opt.start = handleStart(time);
                opt.end = false;
            } else if (opt.start) {
                opt.end = handleEnd(time);
                if (opt.time.enabled) {
                    changeTime('end', opt.end);
                }
            }

            //Update time in case it is enabled and timestamps are available
            if (opt.time.enabled) {
                if (opt.start) {
                    changeTime('start', opt.start);
                }
                if (opt.end) {
                    changeTime('end', opt.end);
                }
            }

            //In case the start is after the end, swap the timestamps
            if (!opt.singleDate && opt.start && opt.end && opt.start > opt.end) {
                var tmp = opt.end;
                opt.end = handleEnd(opt.start);
                opt.start = handleStart(tmp);
                if (opt.time.enabled && opt.swapTime) {
                    swapTime();
                }
            }

            opt.start = parseInt(opt.start);
            opt.end = parseInt(opt.end);

            clearHovering();
            if (opt.start && !opt.end) {
                $(self).trigger('datepicker-first-date-selected', {
                    'date1': new Date(opt.start)
                });
                dayHovering(day);
            }
            updateSelectableRange(time);

            checkSelectionValid();
            showSelectedInfo();
            showSelectedDays();
            autoclose();
        }


        function weekNumberClicked(weekNumberDom) {
            var thisTime = parseInt(weekNumberDom.attr('data-start-time'), 10);
            var date1, date2;
            if (!opt.startWeek) {
                opt.startWeek = thisTime;
                weekNumberDom.addClass('week-number-selected');
                date1 = new Date(thisTime);
                opt.start = moment(date1).day(opt.startOfWeek == 'monday' ? 1 : 0).valueOf();
                opt.end = moment(date1).day(opt.startOfWeek == 'monday' ? 7 : 6).valueOf();
            } else {
                box.find('.week-number-selected').removeClass('week-number-selected');
                date1 = new Date(thisTime < opt.startWeek ? thisTime : opt.startWeek);
                date2 = new Date(thisTime < opt.startWeek ? opt.startWeek : thisTime);
                opt.startWeek = false;
                opt.start = moment(date1).day(opt.startOfWeek == 'monday' ? 1 : 0).valueOf();
                opt.end = moment(date2).day(opt.startOfWeek == 'monday' ? 7 : 6).valueOf();
            }
            updateSelectableRange();
            checkSelectionValid();
            showSelectedInfo();
            showSelectedDays();
            autoclose();
        }

        function isValidTime(time) {
            time = parseInt(time, 10);
            if (opt.startDate && compare_day(time, opt.startDate) < 0) return false;
            if (opt.endDate && compare_day(time, opt.endDate) > 0) return false;

            if (opt.start && !opt.end && !opt.singleDate) {
                //check maxDays and minDays setting
                if (opt.maxDays > 0 && countDays(time, opt.start) > opt.maxDays) return false;
                if (opt.minDays > 0 && countDays(time, opt.start) < opt.minDays) return false;

                //check selectForward and selectBackward
                if (opt.selectForward && time < opt.start) return false;
                if (opt.selectBackward && time > opt.start) return false;

                //check disabled days
                if (opt.beforeShowDay && typeof opt.beforeShowDay == 'function') {
                    var valid = true;
                    var timeTmp = time;
                    while (countDays(timeTmp, opt.start) > 1) {
                        var arr = opt.beforeShowDay(new Date(timeTmp));
                        if (!arr[0]) {
                            valid = false;
                            break;
                        }
                        if (Math.abs(timeTmp - opt.start) < 86400000) break;
                        if (timeTmp > opt.start) timeTmp -= 86400000;
                        if (timeTmp < opt.start) timeTmp += 86400000;
                    }
                    if (!valid) return false;
                }
            }
            return true;
        }


        function updateSelectableRange() {
            box.find('.day.invalid.tmp').removeClass('tmp invalid').addClass('valid');
            if (opt.start && !opt.end) {
                box.find('.day.toMonth.valid').each(function() {
                    var time = parseInt($(this).attr('time'), 10);
                    if (!isValidTime(time))
                        $(this).addClass('invalid tmp').removeClass('valid');
                    else
                        $(this).addClass('valid tmp').removeClass('invalid');
                });
            }

            return true;
        }


        function dayHovering(day) {
            var hoverTime = parseInt(day.attr('time'));
            var tooltip = '';

            if (day.hasClass('has-tooltip') && day.attr('data-tooltip')) {
                tooltip = '<span class="tooltip-content">' + day.attr('data-tooltip') + '</span>';
            } else if (!day.hasClass('invalid')) {
                if (opt.singleDate) {
                    box.find('.day.hovering').removeClass('hovering');
                    day.addClass('hovering');
                } else {
                    box.find('.day').each(function() {
                        var time = parseInt($(this).attr('time')),
                            start = opt.start,
                            end = opt.end;

                        if (time == hoverTime) {
                            $(this).addClass('hovering');
                        } else {
                            $(this).removeClass('hovering');
                        }

                        if (
                            (opt.start && !opt.end) &&
                            (
                                (opt.start < time && hoverTime >= time) ||
                                (opt.start > time && hoverTime <= time)
                            )
                        ) {
                            $(this).addClass('hovering');
                        } else {
                            $(this).removeClass('hovering');
                        }
                    });

                    if (opt.start && !opt.end) {
                        var days = countDays(hoverTime, opt.start);
                        if (opt.hoveringTooltip) {
                            if (typeof opt.hoveringTooltip == 'function') {
                                tooltip = opt.hoveringTooltip(days, opt.start, hoverTime);
                            } else if (opt.hoveringTooltip === true && days > 1) {
                                tooltip = days + ' ' + translate('days');
                            }
                        }
                    }
                }
            }

            if (tooltip) {
                var posDay = day.offset();
                var posBox = box.offset();

                var _left = posDay.left - posBox.left;
                var _top = posDay.top - posBox.top;
                _left += day.width() / 2;


                var $tip = box.find('.date-range-length-tip');
                var w = $tip.css({
                    'visibility': 'hidden',
                    'display': 'none'
                }).html(tooltip).width();
                var h = $tip.height();
                _left -= w / 2;
                _top -= h;
                setTimeout(function() {
                    $tip.css({
                        left: _left,
                        top: _top,
                        display: 'block',
                        'visibility': 'visible'
                    });
                }, 10);
            } else {
                box.find('.date-range-length-tip').hide();
            }
        }

        function clearHovering() {
            box.find('.day.hovering').removeClass('hovering');
            box.find('.date-range-length-tip').hide();
        }

        function dateChanged(date) {
            var value = date.val();
            var name = date.attr('name');
            var type = date.parents('table').hasClass('month1') ? 'month1' : 'month2';
            var oppositeType = type === 'month1' ? 'month2' : 'month1';
            var startDate = opt.startDate ? moment(opt.startDate) : false;
            var endDate = opt.endDate ? moment(opt.endDate) : false;
            var newDate = moment(opt[type])[name](value);


            if (startDate && newDate.isSameOrBefore(startDate)) {
                newDate = startDate.add(type === 'month2' ? 1 : 0, 'month');
            }

            if (endDate && newDate.isSameOrAfter(endDate)) {
                newDate = endDate.add(!opt.singleMonth && type === 'month1' ? -1 : 0, 'month');
            }

            showMonth(newDate, type);

            if (type === 'month1') {
                if (opt.stickyMonths || moment(newDate).isSameOrAfter(opt[oppositeType], 'month')) {
                    showMonth(moment(newDate).add(1, 'month'), oppositeType);
                }
            } else {
                if (opt.stickyMonths || moment(newDate).isSameOrBefore(opt[oppositeType], 'month')) {
                    showMonth(moment(newDate).add(-1, 'month'), oppositeType);
                }
            }

            showGap();
        }

        function autoclose() {
            if (opt.singleDate === true) {
                if (initiated && opt.start) {
                    if (opt.autoClose) closeDatePicker();
                }
            } else {
                if (initiated && opt.start && opt.end) {
                    if (opt.autoClose) closeDatePicker();
                }
            }
        }

        function checkSelectionValid() {
            var days = Math.ceil((opt.end - opt.start) / 86400000) + 1;
            if (opt.singleDate) { // Validate if only start is there
                if (opt.start && !opt.end)
                    box.find('.drp_top-bar').removeClass('error').addClass('normal');
                else
                    box.find('.drp_top-bar').removeClass('error').removeClass('normal');
            } else if (opt.maxDays && days > opt.maxDays) {
                opt.start = false;
                opt.end = false;
                box.find('.day').removeClass('checked');
                box.find('.drp_top-bar').removeClass('normal').addClass('error').find('.error-top').html(translate('less-than').replace('%d', opt.maxDays));
            } else if (opt.minDays && days < opt.minDays) {
                opt.start = false;
                opt.end = false;
                box.find('.day').removeClass('checked');
                box.find('.drp_top-bar').removeClass('normal').addClass('error').find('.error-top').html(translate('more-than').replace('%d', opt.minDays));
            } else {
                if (opt.start || opt.end)
                    box.find('.drp_top-bar').removeClass('error').addClass('normal');
                else
                    box.find('.drp_top-bar').removeClass('error').removeClass('normal');
            }

            if ((opt.singleDate && opt.start && !opt.end) || (!opt.singleDate && opt.start && opt.end)) {
                box.find('.apply-btn').removeClass('disabled');
            } else {
                box.find('.apply-btn').addClass('disabled');
            }

            if (opt.batchMode) {
                if (
                    (opt.start && opt.startDate && compare_day(opt.start, opt.startDate) < 0) ||
                    (opt.end && opt.endDate && compare_day(opt.end, opt.endDate) > 0)
                ) {
                    opt.start = false;
                    opt.end = false;
                    box.find('.day').removeClass('checked');
                }
            }
        }

        function showSelectedInfo(forceValid, silent) {
            box.find('.start-day').html('...');
            box.find('.end-day').html('...');
            box.find('.selected-days').hide();
            if (opt.start) {
                box.find('.start-day').html(getDateString(new Date(parseInt(opt.start))));
            }
            if (opt.end) {
                box.find('.end-day').html(getDateString(new Date(parseInt(opt.end))));
            }
            var dateRange;
            if (opt.start && opt.singleDate) {
                box.find('.apply-btn').removeClass('disabled');
                dateRange = getDateString(new Date(opt.start));
                opt.setValue.call(selfDom, dateRange, getDateString(new Date(opt.start)), getDateString(new Date(opt.end)));

                if (initiated && !silent) {
                    $(self).trigger('datepicker-change', {
                        'value': dateRange,
                        'date1': new Date(opt.start)
                    });
                }
            } else if (opt.start && opt.end) {
                box.find('.selected-days').show().find('.selected-days-num').html(countDays(opt.end, opt.start));
                box.find('.apply-btn').removeClass('disabled');
                dateRange = getDateString(new Date(opt.start)) + opt.separator + getDateString(new Date(opt.end));
                opt.setValue.call(selfDom, dateRange, getDateString(new Date(opt.start)), getDateString(new Date(opt.end)));
                if (initiated && !silent) {
                    $(self).trigger('datepicker-change', {
                        'value': dateRange,
                        'date1': new Date(opt.start),
                        'date2': new Date(opt.end)
                    });
                }
            } else if (forceValid) {
                box.find('.apply-btn').removeClass('disabled');
            } else {
                box.find('.apply-btn').addClass('disabled');
            }
        }

        function countDays(start, end) {
            return Math.abs(moment(start).diff(moment(end), 'd')) + 1;
        }

        function setDateRange(date1, date2, silent) {
            if (date1.getTime() > date2.getTime()) {
                var tmp = date2;
                date2 = date1;
                date1 = tmp;
                tmp = null;
            }
            var valid = true;
            if (opt.startDate && compare_day(date1, opt.startDate) < 0) valid = false;
            if (opt.endDate && compare_day(date2, opt.endDate) > 0) valid = false;
            if (!valid) {
                showMonth(opt.startDate, 'month1');
                showMonth(nextMonth(opt.startDate), 'month2');
                showGap();
                return;
            }

            opt.start = date1.getTime();
            opt.end = date2.getTime();

            if (opt.time.enabled) {
                renderTime('time1', date1);
                renderTime('time2', date2);
            }

            if (opt.stickyMonths || (compare_day(date1, date2) > 0 && compare_month(date1, date2) === 0)) {
                if (opt.lookBehind) {
                    date1 = prevMonth(date2);
                } else {
                    date2 = nextMonth(date1);
                }
            }

            if (opt.stickyMonths && opt.endDate !== false && compare_month(date2, opt.endDate) > 0) {
                date1 = prevMonth(date1);
                date2 = prevMonth(date2);
            }

            if (!opt.stickyMonths) {
                if (compare_month(date1, date2) === 0) {
                    if (opt.lookBehind) {
                        date1 = prevMonth(date2);
                    } else {
                        date2 = nextMonth(date1);
                    }
                }
            }

            showMonth(date1, 'month1');
            showMonth(date2, 'month2');
            showGap();
            checkSelectionValid();
            showSelectedInfo(false, silent);
            autoclose();
        }

        function setSingleDate(date1) {

            var valid = true;
            if (opt.startDate && compare_day(date1, opt.startDate) < 0) valid = false;
            if (opt.endDate && compare_day(date1, opt.endDate) > 0) valid = false;
            if (!valid) {
                showMonth(opt.startDate, 'month1');
                return;
            }

            opt.start = date1.getTime();


            if (opt.time.enabled) {
                renderTime('time1', date1);

            }
            showMonth(date1, 'month1');
            if (opt.singleMonth !== true) {
                var date2 = nextMonth(date1);
                showMonth(date2, 'month2');
            }
            showGap();
            showSelectedInfo();
            autoclose();
        }

        function showSelectedDays() {
            if (!opt.start && !opt.end) return;
            box.find('.day').each(function() {
                var time = parseInt($(this).attr('time')),
                    start = opt.start,
                    end = opt.end;
                if (opt.time.enabled) {
                    time = moment(time).startOf('day').valueOf();
                    start = moment(start || moment().valueOf()).startOf('day').valueOf();
                    end = moment(end || moment().valueOf()).startOf('day').valueOf();
                }
                if (
                    (opt.start && opt.end && end >= time && start <= time) ||
                    (opt.start && !opt.end && moment(start).format('YYYY-MM-DD') == moment(time).format('YYYY-MM-DD'))
                ) {
                    $(this).addClass('checked');
                } else {
                    $(this).removeClass('checked');
                }

                //add first-date-selected class name to the first date selected
                if (opt.start && moment(start).format('YYYY-MM-DD') == moment(time).format('YYYY-MM-DD')) {
                    $(this).addClass('first-date-selected');
                } else {
                    $(this).removeClass('first-date-selected');
                }
                //add last-date-selected
                if (opt.end && moment(end).format('YYYY-MM-DD') == moment(time).format('YYYY-MM-DD')) {
                    $(this).addClass('last-date-selected');
                } else {
                    $(this).removeClass('last-date-selected');
                }
            });

            box.find('.week-number').each(function() {
                if ($(this).attr('data-start-time') == opt.startWeek) {
                    $(this).addClass('week-number-selected');
                }
            });
        }

        function showMonth(date, month) {
            date = moment(date).toDate();
            var monthElement = generateMonthElement(date, month);
            var yearElement = generateYearElement(date, month);

            box.find('.' + month + ' .month-name').html(monthElement + ' ' + yearElement);
            box.find('.' + month + ' tbody').html(createMonthHTML(date));
            opt[month] = date;
            updateSelectableRange();
            bindEvents();
        }

        function generateMonthElement(date, month) {
            date = moment(date);
            var currentMonth = date.get('month');
            var currentMonthName = nameMonth(currentMonth);
            var nonSelectableMonth = '<div class="month-element">' + currentMonthName + '</div>';

            if (!opt.monthSelect) { return nonSelectableMonth; }

            var startDate = opt.startDate ? moment(opt.startDate).add(!opt.singleMonth && month === 'month2' ? 1 : 0, 'month') : false;
            var endDate = opt.endDate ? moment(opt.endDate).add(!opt.singleMonth && month === 'month1' ? -1 : 0, 'month') : false;

            var minSelectableMonth = startDate && date.isSame(startDate, 'year') ? startDate.get('month') : 0;
            var maxSelectableMonth = endDate && date.isSame(endDate, 'year') ? endDate.get('month') : 11;
            var minVisibleMonth = Math.min(minSelectableMonth, currentMonth);
            var maxVisibleMonth = Math.max(maxSelectableMonth, currentMonth);

            if (minVisibleMonth === maxVisibleMonth) { return nonSelectableMonth; }

            var selectData = generateSelectData(
                {
                    minSelectable: minSelectableMonth,
                    maxSelectable: maxSelectableMonth,
                    minVisible: minVisibleMonth,
                    maxVisible: maxVisibleMonth,
                },
                currentMonth,
                function (value) { return nameMonth(value); }
            )
            return generateSelect('month', selectData);
        }

        function generateYearElement(date, month) {
            date = moment(date);
            var currentYear = date.get('year');
            var nonSelectableMonth = '<div class="month-element">' + currentYear + '</div>';

            if (!opt.yearSelect) { return nonSelectableMonth; }

            var isYearFunction = opt.yearSelect && typeof opt.yearSelect === 'function';
            var startDate = opt.startDate ? moment(opt.startDate).add(!opt.singleMonth && month === 'month2' ? 1 : 0, 'month') : false;
            var endDate = opt.endDate ? moment(opt.endDate).add(!opt.singleMonth && month === 'month1' ? -1 : 0, 'month') : false;
            var range = isYearFunction ? opt.yearSelect(currentYear) : opt.yearSelect.slice();

            var minSelectableYear = startDate ? Math.max(range[0], startDate.get('year')) : Math.min(range[0], currentYear);
            var maxSelectableYear = endDate ? Math.min(range[1], endDate.get('year')) : Math.max(range[1], currentYear);
            var minVisibleYear = Math.min(minSelectableYear, currentYear);
            var maxVisibleYear = Math.max(maxSelectableYear, currentYear);

            if (minVisibleYear === maxVisibleYear) { return nonSelectableMonth; }

            var selectData = generateSelectData(
                {
                    minSelectable: minSelectableYear,
                    maxSelectable: maxSelectableYear,
                    minVisible: minVisibleYear,
                    maxVisible: maxVisibleYear,
                },
                currentYear
            )
            return generateSelect('year', selectData);
        }


        function generateSelectData(range, current, valueBeautifier) {
            var data = [];
            valueBeautifier = valueBeautifier || function(value) { return value; };

            for (var i = range.minVisible; i <= range.maxVisible; i++) {
                data.push({
                    value: i,
                    text: valueBeautifier(i),
                    selected: i === current,
                    disabled: ((i < range.minSelectable) || (i > range.maxSelectable)),
                });
            }

            return data;
        }

        function generateSelect(name, data) {
            var select = '<div class="select-wrapper"><select class="' + name + '" name="' + name + '">';
            var current;

            for (var i = 0, l = data.length; i < l; i++) {
                var item = data[i];
                select += '<option value="' + item.value + '"' +
                    (item.selected ? ' selected' : '') +
                    (item.disabled ? ' disabled' : '') + '>' +
                    item.text + '</option>';

                if (item.selected) {
                    current = item.text;
                }
            }

            select += '</select>' + current + '</div>';

            return select;
        }

        function bindEvents() {
            box.find('.day').off("click").on('click', function(evt) {
                dayClicked($(this));
            });

            box.find('.day').off("mouseenter").on('mouseenter', function(evt) {
                dayHovering($(this));
            });

            box.find('.day').off("mouseleave").on('mouseleave', function(evt) {
                box.find('.date-range-length-tip').hide();
                if (opt.singleDate) {
                    clearHovering();
                }
            });

            box.find('.week-number').off("click").on('click', function(evt) {
                weekNumberClicked($(this));
            });

            box.find('.month').off("change").on('change', function(evt) {
                dateChanged($(this));
            });

            box.find('.year').off("change").on('change', function(evt) {
                dateChanged($(this));
            });
        }

        function showTime(date, name) {
            box.find('.' + name).append(getTimeHTML());
            renderTime(name, date);
        }

        function nameMonth(m) {
            return translate('month-name')[m];
        }

        function getDateString(d) {
            return moment(d).format(opt.format);
        }

        function showGap() {
            showSelectedDays();
            var m1 = parseInt(moment(opt.month1).format('YYYYMM'));
            var m2 = parseInt(moment(opt.month2).format('YYYYMM'));
            var p = Math.abs(m1 - m2);
            var shouldShow = (p > 1 && p != 89);
            if (shouldShow) {
                box.addClass('has-gap').removeClass('no-gap').find('.gap').css('visibility', 'visible');
            } else {
                box.removeClass('has-gap').addClass('no-gap').find('.gap').css('visibility', 'hidden');
            }
            var h1 = box.find('table.month1').height();
            var h2 = box.find('table.month2').height();
            box.find('.gap').height(Math.max(h1, h2) + 10);
        }

        function closeDatePicker() {
            if (opt.alwaysOpen) return;

            var afterAnim = function() {
                $(self).data('date-picker-opened', false);
                $(self).trigger('datepicker-closed', {
                    relatedTarget: box
                });
            };
            if (opt.customCloseAnimation) {
                opt.customCloseAnimation.call(box.get(0), afterAnim);
            } else {
                $(box).slideUp(opt.duration, afterAnim);
            }
            $(self).trigger('datepicker-close', {
                relatedTarget: box
            });
        }

        function redrawDatePicker() {
            showMonth(opt.month1, 'month1');
            showMonth(opt.month2, 'month2');
        }

        function compare_month(m1, m2) {
            var p = parseInt(moment(m1).format('YYYYMM')) - parseInt(moment(m2).format('YYYYMM'));
            if (p > 0) return 1;
            if (p === 0) return 0;
            return -1;
        }

        function compare_day(m1, m2) {
            var p = parseInt(moment(m1).format('YYYYMMDD')) - parseInt(moment(m2).format('YYYYMMDD'));
            if (p > 0) return 1;
            if (p === 0) return 0;
            return -1;
        }

        function nextMonth(month) {
            return moment(month).add(1, 'months').toDate();
        }

        function prevMonth(month) {
            return moment(month).add(-1, 'months').toDate();
        }

        function getTimeHTML() {
            return '<div>' +
                '<span>' + translate('Time') + ': <span class="hour-val">00</span>:<span class="minute-val">00</span></span>' +
                '</div>' +
                '<div class="hour">' +
                '<label>' + translate('Hour') + ': <input type="range" class="hour-range" name="hour" min="0" max="23"></label>' +
                '</div>' +
                '<div class="minute">' +
                '<label>' + translate('Minute') + ': <input type="range" class="minute-range" name="minute" min="0" max="59"></label>' +
                '</div>';
        }

        function createDom() {
            var html = '<div class="date-picker-wrapper';
            if (opt.extraClass) html += ' ' + opt.extraClass + ' ';
            if (opt.singleDate) html += ' single-date ';
            if (!opt.showShortcuts) html += ' no-shortcuts ';
            if (!opt.showTopbar) html += ' no-topbar ';
            if (opt.customTopBar) html += ' custom-topbar ';
            html += '">';

            if (opt.showTopbar) {
                html += '<div class="drp_top-bar">';

                if (opt.customTopBar) {
                    if (typeof opt.customTopBar == 'function') opt.customTopBar = opt.customTopBar();
                    html += '<div class="custom-top">' + opt.customTopBar + '</div>';
                } else {
                    html += '<div class="normal-top">' +
                        '<span class="selection-top">' + translate('selected') + ' </span> <b class="start-day">...</b>';
                    if (!opt.singleDate) {
                        html += ' <span class="separator-day">' + opt.separator + '</span> <b class="end-day">...</b> <i class="selected-days">(<span class="selected-days-num">3</span> ' + translate('days') + ')</i>';
                    }
                    html += '</div>';
                    html += '<div class="error-top">error</div>' +
                        '<div class="default-top">default</div>';
                }

                html += '<input type="button" class="apply-btn disabled' + getApplyBtnClass() + '" value="' + translate('apply') + '" />';
                html += '</div>';
            }

            var _colspan = opt.showWeekNumbers ? 6 : 5;

            var arrowPrev = '&lt;';
            if (opt.customArrowPrevSymbol) arrowPrev = opt.customArrowPrevSymbol;

            var arrowNext = '&gt;';
            if (opt.customArrowNextSymbol) arrowNext = opt.customArrowNextSymbol;

            html += '<div class="month-wrapper">' +
                '   <table class="month1" cellspacing="0" border="0" cellpadding="0">' +
                '       <thead>' +
                '           <tr class="caption">' +
                '               <th>' +
                '                   <span class="prev">' +
                arrowPrev +
                '                   </span>' +
                '               </th>' +
                '               <th colspan="' + _colspan + '" class="month-name">' +
                '               </th>' +
                '               <th>' +
                (opt.singleDate || !opt.stickyMonths ? '<span class="next">' + arrowNext + '</span>' : '') +
                '               </th>' +
                '           </tr>' +
                '           <tr class="week-name">' + getWeekHead() +
                '       </thead>' +
                '       <tbody></tbody>' +
                '   </table>';

            if (hasMonth2()) {
                html += '<div class="gap">' + getGapHTML() + '</div>' +
                    '<table class="month2" cellspacing="0" border="0" cellpadding="0">' +
                    '   <thead>' +
                    '   <tr class="caption">' +
                    '       <th>' +
                    (!opt.stickyMonths ? '<span class="prev">' + arrowPrev + '</span>' : '') +
                    '       </th>' +
                    '       <th colspan="' + _colspan + '" class="month-name">' +
                    '       </th>' +
                    '       <th>' +
                    '           <span class="next">' + arrowNext + '</span>' +
                    '       </th>' +
                    '   </tr>' +
                    '   <tr class="week-name">' + getWeekHead() +
                    '   </thead>' +
                    '   <tbody></tbody>' +
                    '</table>';

            }
            //+'</div>'
            html += '<div class="dp-clearfix"></div>' +
                '<div class="time">' +
                '<div class="time1"></div>';
            if (!opt.singleDate) {
                html += '<div class="time2"></div>';
            }
            html += '</div>' +
                '<div class="dp-clearfix"></div>' +
                '</div>';

            html += '<div class="footer">';
            if (opt.showShortcuts) {
                html += '<div class="shortcuts"><b>' + translate('shortcuts') + '</b>';

                var data = opt.shortcuts;
                if (data) {
                    var name;
                    if (data['prev-days'] && data['prev-days'].length > 0) {
                        html += '&nbsp;<span class="prev-days">' + translate('past');
                        for (var i = 0; i < data['prev-days'].length; i++) {
                            name = data['prev-days'][i];
                            name += (data['prev-days'][i] > 1) ? translate('days') : translate('day');
                            html += ' <a href="javascript:;" shortcut="day,-' + data['prev-days'][i] + '">' + name + '</a>';
                        }
                        html += '</span>';
                    }

                    if (data['next-days'] && data['next-days'].length > 0) {
                        html += '&nbsp;<span class="next-days">' + translate('following');
                        for (var i = 0; i < data['next-days'].length; i++) {
                            name = data['next-days'][i];
                            name += (data['next-days'][i] > 1) ? translate('days') : translate('day');
                            html += ' <a href="javascript:;" shortcut="day,' + data['next-days'][i] + '">' + name + '</a>';
                        }
                        html += '</span>';
                    }

                    if (data.prev && data.prev.length > 0) {
                        html += '&nbsp;<span class="prev-buttons">' + translate('previous');
                        for (var i = 0; i < data.prev.length; i++) {
                            name = translate('prev-' + data.prev[i]);
                            html += ' <a href="javascript:;" shortcut="prev,' + data.prev[i] + '">' + name + '</a>';
                        }
                        html += '</span>';
                    }

                    if (data.next && data.next.length > 0) {
                        html += '&nbsp;<span class="next-buttons">' + translate('next');
                        for (var i = 0; i < data.next.length; i++) {
                            name = translate('next-' + data.next[i]);
                            html += ' <a href="javascript:;" shortcut="next,' + data.next[i] + '">' + name + '</a>';
                        }
                        html += '</span>';
                    }
                }

                if (opt.customShortcuts) {
                    for (var i = 0; i < opt.customShortcuts.length; i++) {
                        var sh = opt.customShortcuts[i];
                        html += '&nbsp;<span class="custom-shortcut"><a href="javascript:;" shortcut="custom">' + sh.name + '</a></span>';
                    }
                }
                html += '</div>';
            }

            // Add Custom Values Dom
            if (opt.showCustomValues) {
                html += '<div class="customValues"><b>' + (opt.customValueLabel || translate('custom-values')) + '</b>';

                if (opt.customValues) {
                    for (var i = 0; i < opt.customValues.length; i++) {
                        var val = opt.customValues[i];
                        html += '&nbsp;<span class="custom-value"><a href="javascript:;" custom="' + val.value + '">' + val.name + '</a></span>';
                    }
                }
            }

            html += '</div></div>';


            return $(html);
        }

        function getApplyBtnClass() {
            var klass = '';
            if (opt.autoClose === true) {
                klass += ' hide';
            }
            if (opt.applyBtnClass !== '') {
                klass += ' ' + opt.applyBtnClass;
            }
            return klass;
        }

        function getWeekHead() {
            var prepend = opt.showWeekNumbers ? '<th>' + translate('week-number') + '</th>' : '';
            if (opt.startOfWeek == 'monday') {
                return prepend + '<th>' + translate('week-1') + '</th>' +
                    '<th>' + translate('week-2') + '</th>' +
                    '<th>' + translate('week-3') + '</th>' +
                    '<th>' + translate('week-4') + '</th>' +
                    '<th>' + translate('week-5') + '</th>' +
                    '<th>' + translate('week-6') + '</th>' +
                    '<th>' + translate('week-7') + '</th>';
            } else {
                return prepend + '<th>' + translate('week-7') + '</th>' +
                    '<th>' + translate('week-1') + '</th>' +
                    '<th>' + translate('week-2') + '</th>' +
                    '<th>' + translate('week-3') + '</th>' +
                    '<th>' + translate('week-4') + '</th>' +
                    '<th>' + translate('week-5') + '</th>' +
                    '<th>' + translate('week-6') + '</th>';
            }
        }

        function isMonthOutOfBounds(month) {
            month = moment(month);
            if (opt.startDate && month.endOf('month').isBefore(opt.startDate)) {
                return true;
            }
            if (opt.endDate && month.startOf('month').isAfter(opt.endDate)) {
                return true;
            }
            return false;
        }

        function getGapHTML() {
            var html = ['<div class="gap-top-mask"></div><div class="gap-bottom-mask"></div><div class="gap-lines">'];
            for (var i = 0; i < 20; i++) {
                html.push('<div class="gap-line">' +
                    '<div class="gap-1"></div>' +
                    '<div class="gap-2"></div>' +
                    '<div class="gap-3"></div>' +
                    '</div>');
            }
            html.push('</div>');
            return html.join('');
        }

        function hasMonth2() {
            return (!opt.singleMonth);
        }

        function attributesCallbacks(initialObject, callbacksArray, today) {
            var resultObject = $.extend(true, {}, initialObject);

            $.each(callbacksArray, function(cbAttrIndex, cbAttr) {
                var addAttributes = cbAttr(today);
                for (var attr in addAttributes) {
                    if (resultObject.hasOwnProperty(attr)) {
                        resultObject[attr] += addAttributes[attr];
                    } else {
                        resultObject[attr] = addAttributes[attr];
                    }
                }
            });

            var attrString = '';

            for (var attr in resultObject) {
                if (resultObject.hasOwnProperty(attr)) {
                    attrString += attr + '="' + resultObject[attr] + '" ';
                }
            }

            return attrString;
        }

        function createMonthHTML(d) {
            var days = [];
            d.setDate(1);
            var lastMonth = new Date(d.getTime() - 86400000);
            var now = new Date();

            var dayOfWeek = d.getDay();
            if ((dayOfWeek === 0) && (opt.startOfWeek === 'monday')) {
                // add one week
                dayOfWeek = 7;
            }
            var today, valid;

            if (dayOfWeek > 0) {
                for (var i = dayOfWeek; i > 0; i--) {
                    var day = new Date(d.getTime() - 86400000 * i);
                    valid = isValidTime(day.getTime());
                    if (opt.startDate && compare_day(day, opt.startDate) < 0) valid = false;
                    if (opt.endDate && compare_day(day, opt.endDate) > 0) valid = false;
                    days.push({
                        date: day,
                        type: 'lastMonth',
                        day: day.getDate(),
                        time: day.getTime(),
                        valid: valid
                    });
                }
            }
            var toMonth = d.getMonth();
            for (var i = 0; i < 40; i++) {
                today = moment(d).add(i, 'days').toDate();
                valid = isValidTime(today.getTime());
                if (opt.startDate && compare_day(today, opt.startDate) < 0) valid = false;
                if (opt.endDate && compare_day(today, opt.endDate) > 0) valid = false;
                days.push({
                    date: today,
                    type: today.getMonth() == toMonth ? 'toMonth' : 'nextMonth',
                    day: today.getDate(),
                    time: today.getTime(),
                    valid: valid
                });
            }
            var html = [];
            for (var week = 0; week < 6; week++) {
                if (days[week * 7].type == 'nextMonth') break;
                html.push('<tr>');

                for (var day = 0; day < 7; day++) {
                    var _day = (opt.startOfWeek == 'monday') ? day + 1 : day;
                    today = days[week * 7 + _day];
                    var highlightToday = moment(today.time).format('L') == moment(now).format('L');
                    today.extraClass = '';
                    today.tooltip = '';
                    if (today.valid && opt.beforeShowDay && typeof opt.beforeShowDay == 'function') {
                        var _r = opt.beforeShowDay(moment(today.time).toDate());
                        today.valid = _r[0];
                        today.extraClass = _r[1] || '';
                        today.tooltip = _r[2] || '';
                        if (today.tooltip !== '') today.extraClass += ' has-tooltip ';
                    }

                    var todayDivAttr = {
                        time: today.time,
                        'data-tooltip': today.tooltip,
                        'class': 'day ' + today.type + ' ' + today.extraClass + ' ' + (today.valid ? 'valid' : 'invalid') + ' ' + (highlightToday ? 'real-today' : '')
                    };

                    if (day === 0 && opt.showWeekNumbers) {
                        html.push('<td><div class="week-number" data-start-time="' + today.time + '">' + opt.getWeekNumber(today.date) + '</div></td>');
                    }

                    html.push('<td ' + attributesCallbacks({}, opt.dayTdAttrs, today) + '><div ' + attributesCallbacks(todayDivAttr, opt.dayDivAttrs, today) + '>' + showDayHTML(today.time, today.day) + '</div></td>');
                }
                html.push('</tr>');
            }
            return html.join('');
        }

        function showDayHTML(time, date) {
            if (opt.showDateFilter && typeof opt.showDateFilter == 'function') return opt.showDateFilter(time, date);
            return date;
        }

        function getLanguages() {
            if (opt.language == 'auto') {
                var language = navigator.language ? navigator.language : navigator.browserLanguage;
                if (!language) {
                    return $.dateRangePickerLanguages['default'];
                }
                language = language.toLowerCase();
                if(language in $.dateRangePickerLanguages){
                    return $.dateRangePickerLanguages[language];
                }

                return $.dateRangePickerLanguages['default'];
            } else if (opt.language && opt.language in $.dateRangePickerLanguages) {
                return $.dateRangePickerLanguages[opt.language];
            } else {
                return $.dateRangePickerLanguages['default'];
            }
        }

        /**
         * Translate language string, try both the provided translation key, as the lower case version
         */
        function translate(translationKey) {
            var translationKeyLowerCase = translationKey.toLowerCase();
            console.log(languages);
            var result = (translationKey in languages) ? languages[translationKey] : (translationKeyLowerCase in languages) ? languages[translationKeyLowerCase] : null;
            var defaultLanguage = $.dateRangePickerLanguages['default'];
            if (result == null) result = (translationKey in defaultLanguage) ? defaultLanguage[translationKey] : (translationKeyLowerCase in defaultLanguage) ? defaultLanguage[translationKeyLowerCase] : '';
            console.log(result);

            return result;
        }

        function getDefaultTime() {
            var defaultTime = opt.defaultTime ? opt.defaultTime : new Date();

            if (opt.lookBehind) {
                if (opt.startDate && compare_month(defaultTime, opt.startDate) < 0) defaultTime = nextMonth(moment(opt.startDate).toDate());
                if (opt.endDate && compare_month(defaultTime, opt.endDate) > 0) defaultTime = moment(opt.endDate).toDate();
            } else {
                if (opt.startDate && compare_month(defaultTime, opt.startDate) < 0) defaultTime = moment(opt.startDate).toDate();
                if (opt.endDate && compare_month(nextMonth(defaultTime), opt.endDate) > 0) defaultTime = prevMonth(moment(opt.endDate).toDate());
            }

            if (opt.singleDate) {
                if (opt.startDate && compare_month(defaultTime, opt.startDate) < 0) defaultTime = moment(opt.startDate).toDate();
                if (opt.endDate && compare_month(defaultTime, opt.endDate) > 0) defaultTime = moment(opt.endDate).toDate();
            }

            return defaultTime;
        }

        function resetMonthsView(time) {
            if (!time) {
                time = getDefaultTime();
            }

            if (opt.lookBehind) {
                showMonth(prevMonth(time), 'month1');
                showMonth(time, 'month2');
            } else {
                showMonth(time, 'month1');
                showMonth(nextMonth(time), 'month2');
            }

            if (opt.singleDate) {
                showMonth(time, 'month1');
            }

            showSelectedDays();
            showGap();
        }

        function outsideClickClose(evt) {
            if (!IsOwnDatePickerClicked(evt, self[0])) {
                if (box.is(':visible')) closeDatePicker();
            }
        }

    };
}));
