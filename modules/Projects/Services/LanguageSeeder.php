<?php

namespace Modules\Projects\Services;

use Rlima\Laravel5DoctrineODM\LaravelDocumentManager;
use Modules\Projects\Entities\Language;

class LanguageSeeder
{
    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    /**
     * @var array
     */
    protected $languagesData = [
        ['iso639-3b' => 'abk', 'na' => 'аҧсуа бызшәа', 'en' => 'Abkhazian', 'be' => 'абхазская', 'sub' => false],
        ['iso639-3b' => 'aar', 'na' => 'Qafár af', 'en' => 'Afar', 'be' => 'афарская', 'sub' => false],
        ['iso639-3b' => 'afr', 'na' => 'Afrikaans', 'en' => 'Afrikaans', 'be' => 'афрыкаанс', 'sub' => false],
        ['iso639-3b' => 'aka', 'na' => 'Akan', 'en' => 'Akan', 'be' => 'аканская', 'sub' => false],
        ['iso639-3b' => 'alb', 'na' => 'Shqip', 'en' => 'Albanian', 'be' => 'албанская', 'sub' => false],
        ['iso639-3b' => 'amh', 'na' => 'አማርኛ', 'en' => 'Amharic', 'be' => 'амхарская', 'sub' => false],
        ['iso639-3b' => 'ara', 'na' => 'العربية', 'en' => 'Arabic', 'be' => 'арабская', 'sub' => true],
        ['iso639-3b' => 'arg', 'na' => 'aragonés', 'en' => 'Aragonese', 'be' => 'арагонская', 'sub' => false],
        ['iso639-3b' => 'arm', 'na' => 'Հայերեն', 'en' => 'Armenian', 'be' => 'армянская', 'sub' => true],
        ['iso639-3b' => 'asm', 'na' => 'অসমীয়া', 'en' => 'Assamese', 'be' => 'асамская', 'sub' => false],
        ['iso639-3b' => 'ava', 'na' => 'авар мацӀ', 'en' => 'Avaric', 'be' => 'аварская', 'sub' => false],
        ['iso639-3b' => 'ave', 'na' => 'avesta', 'en' => 'Avestan', 'be' => 'авестыйская', 'sub' => false],
        ['iso639-3b' => 'aym', 'na' => 'aymar aru', 'en' => 'Aymara', 'be' => 'аймара', 'sub' => false],
        ['iso639-3b' => 'aze', 'na' => 'azərbaycan dili', 'en' => 'Azerbaijani', 'be' => 'азербайджанская', 'sub' => true],
        ['iso639-3b' => 'bam', 'na' => 'bamanankan', 'en' => 'Bambara', 'be' => 'бамбара', 'sub' => false],
        ['iso639-3b' => 'bak', 'na' => 'башҡорт теле', 'en' => 'Bashkir', 'be' => 'башкірская', 'sub' => false],
        ['iso639-3b' => 'baq', 'na' => 'euskara', 'en' => 'Basque', 'be' => 'баскская', 'sub' => false],
        ['iso639-3b' => 'bel', 'na' => 'беларуская', 'en' => 'Belarusian', 'be' => 'беларуская', 'sub' => false],
        ['iso639-3b' => 'ben', 'na' => 'বাংলা', 'en' => 'Bengali', 'be' => 'бенгальская', 'sub' => false],
        ['iso639-3b' => 'bih', 'na' => 'भोजपुरी', 'en' => 'Bihari', 'be' => 'біхары', 'sub' => false],
        ['iso639-3b' => 'bis', 'na' => 'Bislama', 'en' => 'Bislama', 'be' => 'біслама', 'sub' => false],
        ['iso639-3b' => 'bos', 'na' => 'bosanski jezik', 'en' => 'Bosnian', 'be' => 'баснійская', 'sub' => true],
        ['iso639-3b' => 'bre', 'na' => 'brezhoneg', 'en' => 'Breton', 'be' => 'брэтонская', 'sub' => false],
        ['iso639-3b' => 'bul', 'na' => 'български език', 'en' => 'Bulgarian', 'be' => 'балгарская', 'sub' => true],
        ['iso639-3b' => 'bur', 'na' => 'ဗမာစာ', 'en' => 'Burmese', 'be' => 'бірманская', 'sub' => false],
        ['iso639-3b' => 'cat', 'na' => 'català', 'en' => 'Catalan', 'be' => 'каталонская', 'sub' => true],
        ['iso639-3b' => 'cha', 'na' => 'Chamoru', 'en' => 'Chamorro', 'be' => 'чамора', 'sub' => false],
        ['iso639-3b' => 'che', 'na' => 'нохчийн мотт', 'en' => 'Chechen', 'be' => 'чачэнская', 'sub' => false],
        ['iso639-3b' => 'nya', 'na' => 'chiheŵa', 'en' => 'Chichewa', 'be' => 'ньянджа', 'sub' => false],
        ['iso639-3b' => 'chi', 'na' => '中文, 汉语, 漢語', 'en' => 'Chinese', 'be' => 'кітайская', 'sub' => true],
        ['iso639-3b' => 'chv', 'na' => 'чӑваш чӗлхи', 'en' => 'Chuvash', 'be' => 'чувашская', 'sub' => false],
        ['iso639-3b' => 'cor', 'na' => 'Kernewek', 'en' => 'Cornish', 'be' => 'корнская', 'sub' => false],
        ['iso639-3b' => 'cos', 'na' => 'corsu', 'en' => 'Corsican', 'be' => 'карсіканская', 'sub' => false],
        ['iso639-3b' => 'cre', 'na' => 'ᓀᐦᐃᔭᐍᐏᐣ', 'en' => 'Cree', 'be' => 'кры', 'sub' => false],
        ['iso639-3b' => 'hrv', 'na' => 'hrvatski jezik', 'en' => 'Croatian', 'be' => 'харватская', 'sub' => true],
        ['iso639-3b' => 'cze', 'na' => 'čeština', 'en' => 'Czech', 'be' => 'чэшская', 'sub' => true],
        ['iso639-3b' => 'dan', 'na' => 'dansk', 'en' => 'Danish', 'be' => 'дацкая', 'sub' => true],
        ['iso639-3b' => 'div', 'na' => 'ދިވެހި', 'en' => 'Divehi', 'be' => 'дывехі', 'sub' => false],
        ['iso639-3b' => 'dut', 'na' => 'Nederlands', 'en' => 'Dutch', 'be' => 'нідэрландская', 'sub' => true],
        ['iso639-3b' => 'dzo', 'na' => 'རྫོང་ཁ', 'en' => 'Dzongkha', 'be' => 'дзонг-кэ', 'sub' => false],
        ['iso639-3b' => 'eng', 'na' => 'English', 'en' => 'English', 'be' => 'англійская', 'sub' => true],
        ['iso639-3b' => 'epo', 'na' => 'Esperanto', 'en' => 'Esperanto', 'be' => 'эсперанта', 'sub' => false],
        ['iso639-3b' => 'est', 'na' => 'eesti', 'en' => 'Estonian', 'be' => 'эстонская', 'sub' => true],
        ['iso639-3b' => 'ewe', 'na' => 'Eʋegbe', 'en' => 'Ewe', 'be' => 'эве', 'sub' => false],
        ['iso639-3b' => 'fao', 'na' => 'føroyskt', 'en' => 'Faroese', 'be' => 'фарэрская', 'sub' => false],
        ['iso639-3b' => 'fij', 'na' => 'vosa Vakaviti', 'en' => 'Fijian', 'be' => 'фіджыйская', 'sub' => false],
        ['iso639-3b' => 'fin', 'na' => 'suomi', 'en' => 'Finnish', 'be' => 'фінская', 'sub' => true],
        ['iso639-3b' => 'fre', 'na' => 'français', 'en' => 'French', 'be' => 'французская', 'sub' => true],
        ['iso639-3b' => 'ful', 'na' => 'Fulfulde', 'en' => 'Fula', 'be' => 'фула', 'sub' => false],
        ['iso639-3b' => 'glg', 'na' => 'galego', 'en' => 'Galician', 'be' => 'галісійская', 'sub' => false],
        ['iso639-3b' => 'geo', 'na' => 'ქართული', 'en' => 'Georgian', 'be' => 'грузінская', 'sub' => true],
        ['iso639-3b' => 'ger', 'na' => 'Deutsch', 'en' => 'German', 'be' => 'нямецкая', 'sub' => true],
        ['iso639-3b' => 'gre', 'na' => 'ελληνικά', 'en' => 'Greek', 'be' => 'грэчаская', 'sub' => true],
        ['iso639-3b' => 'grn', 'na' => 'Avañe\'ẽ', 'en' => 'Guarani', 'be' => 'гуарані', 'sub' => false],
        ['iso639-3b' => 'guj', 'na' => 'ગુજરાતી', 'en' => 'Gujarati', 'be' => 'гуджараці', 'sub' => false],
        ['iso639-3b' => 'hat', 'na' => 'Kreyòl ayisyen', 'en' => 'Haitian Creole', 'be' => 'гаіцянская крэольская', 'sub' => false],
        ['iso639-3b' => 'hau', 'na' => 'هَوُسَ', 'en' => 'Hausa', 'be' => 'хаўса', 'sub' => false],
        ['iso639-3b' => 'heb', 'na' => 'עברית', 'en' => 'Hebrew', 'be' => 'габрэйская', 'sub' => true],
        ['iso639-3b' => 'her', 'na' => 'Otjiherero', 'en' => 'Herero', 'be' => 'герэра', 'sub' => false],
        ['iso639-3b' => 'hin', 'na' => 'हिन्दी, हिंदी', 'en' => 'Hindi', 'be' => 'хіндзі', 'sub' => true],
        ['iso639-3b' => 'hmo', 'na' => 'Hiri Motu', 'en' => 'Hiri Motu', 'be' => 'гіры-моту', 'sub' => false],
        ['iso639-3b' => 'hun', 'na' => 'magyar', 'en' => 'Hungarian', 'be' => 'венгерская', 'sub' => true],
        ['iso639-3b' => 'ina', 'na' => 'Interlingua', 'en' => 'Interlingua', 'be' => 'інтэрлінгва', 'sub' => false],
        ['iso639-3b' => 'ind', 'na' => 'Bahasa Indonesia', 'en' => 'Indonesian', 'be' => 'інданезійская', 'sub' => false],
        ['iso639-3b' => 'ile', 'na' => 'Interlingue', 'en' => 'Interlingue', 'be' => 'акцыдэнталь', 'sub' => false],
        ['iso639-3b' => 'gle', 'na' => 'Gaeilge', 'en' => 'Irish', 'be' => 'ірландская', 'sub' => false],
        ['iso639-3b' => 'ibo', 'na' => 'Asụsụ Igbo', 'en' => 'Igbo', 'be' => 'ігба', 'sub' => false],
        ['iso639-3b' => 'ipk', 'na' => 'Iñupiaq', 'en' => 'Inupiaq', 'be' => 'аляскінска-інуіцкія мовы', 'sub' => false],
        ['iso639-3b' => 'ido', 'na' => 'Ido', 'en' => 'Ido', 'be' => 'іда', 'sub' => false],
        ['iso639-3b' => 'ice', 'na' => 'Íslenska', 'en' => 'Icelandic', 'be' => 'ісландская', 'sub' => true],
        ['iso639-3b' => 'ita', 'na' => 'italiano', 'en' => 'Italian', 'be' => 'італьянская', 'sub' => true],
        ['iso639-3b' => 'iku', 'na' => 'ᐃᓄᒃᑎᑐᑦ', 'en' => 'Inuktitut', 'be' => 'інукцітут', 'sub' => false],
        ['iso639-3b' => 'jpn', 'na' => '日本語 (にほんご)', 'en' => 'Japanese', 'be' => 'японская', 'sub' => true],
        ['iso639-3b' => 'jav', 'na' => 'basa Jawa', 'en' => 'Javanese', 'be' => 'яванская', 'sub' => false],
        ['iso639-3b' => 'kal', 'na' => 'kalaallisut', 'en' => 'Greenlandic', 'be' => 'грэнландская', 'sub' => false],
        ['iso639-3b' => 'kan', 'na' => 'ಕನ್ನಡ', 'en' => 'Kannada', 'be' => 'канада', 'sub' => false],
        ['iso639-3b' => 'kau', 'na' => 'Kanuri', 'en' => 'Kanuri', 'be' => 'кануры', 'sub' => false],
        ['iso639-3b' => 'kas', 'na' => 'कश्मीरी', 'en' => 'Kashmiri', 'be' => 'кашмірская', 'sub' => false],
        ['iso639-3b' => 'kaz', 'na' => 'қазақ тілі', 'en' => 'Kazakh', 'be' => 'казахская', 'sub' => false],
        ['iso639-3b' => 'khm', 'na' => 'ខ្មែរ', 'en' => 'Khmer', 'be' => 'кхмерская', 'sub' => false],
        ['iso639-3b' => 'kik', 'na' => 'Gĩkũyũ', 'en' => 'Kikuyu', 'be' => 'кікуйю', 'sub' => false],
        ['iso639-3b' => 'kin', 'na' => 'Ikinyarwanda', 'en' => 'Kinyarwanda', 'be' => 'руандзійская', 'sub' => false],
        ['iso639-3b' => 'kir', 'na' => 'Кыргызча', 'en' => 'Kirghiz', 'be' => 'кіргізская', 'sub' => false],
        ['iso639-3b' => 'kom', 'na' => 'коми кыв', 'en' => 'Komi', 'be' => 'комі', 'sub' => false],
        ['iso639-3b' => 'kon', 'na' => 'Kikongo', 'en' => 'Kongo', 'be' => 'конга', 'sub' => false],
        ['iso639-3b' => 'kor', 'na' => '한국어', 'en' => 'Korean', 'be' => 'карэйская', 'sub' => true],
        ['iso639-3b' => 'kur', 'na' => 'Kurdî', 'en' => 'Kurdish', 'be' => 'курдская', 'sub' => false],
        ['iso639-3b' => 'kua', 'na' => 'Kuanyama', 'en' => 'Kwanyama', 'be' => 'кваньяма', 'sub' => false],
        ['iso639-3b' => 'lat', 'na' => 'lingua latina', 'en' => 'Latin', 'be' => 'лацінская', 'sub' => false],
        ['iso639-3b' => 'ltz', 'na' => 'Lëtzebuergesch', 'en' => 'Luxembourgish', 'be' => 'люксембургская', 'sub' => false],
        ['iso639-3b' => 'lug', 'na' => 'Luganda', 'en' => 'Ganda', 'be' => 'луганда', 'sub' => false],
        ['iso639-3b' => 'lim', 'na' => 'Limburgs', 'en' => 'Limburgish', 'be' => 'лімбургская', 'sub' => false],
        ['iso639-3b' => 'lin', 'na' => 'Lingála', 'en' => 'Lingala', 'be' => 'лінгала', 'sub' => false],
        ['iso639-3b' => 'lao', 'na' => 'ພາສາລາວ', 'en' => 'Lao', 'be' => 'лаоская', 'sub' => false],
        ['iso639-3b' => 'lit', 'na' => 'lietuvių kalba', 'en' => 'Lithuanian', 'be' => 'літоўская', 'sub' => true],
        ['iso639-3b' => 'lub', 'na' => 'Tshiluba', 'en' => 'Luba-Katanga', 'be' => 'луба-катанга', 'sub' => false],
        ['iso639-3b' => 'lav', 'na' => 'latviešu valoda', 'en' => 'Latvian', 'be' => 'латвійская', 'sub' => true],
        ['iso639-3b' => 'glv', 'na' => 'Gaelg', 'en' => 'Manx', 'be' => 'мэнская', 'sub' => false],
        ['iso639-3b' => 'mac', 'na' => 'македонски јазик', 'en' => 'Macedonian', 'be' => 'македонская', 'sub' => true],
        ['iso639-3b' => 'mlg', 'na' => 'fiteny malagasy', 'en' => 'Malagasy', 'be' => 'малагасійская', 'sub' => false],
        ['iso639-3b' => 'may', 'na' => 'bahasa Melayu', 'en' => 'Malay', 'be' => 'малайская', 'sub' => false],
        ['iso639-3b' => 'mal', 'na' => 'മലയാളം', 'en' => 'Malayalam', 'be' => 'малаялам', 'sub' => false],
        ['iso639-3b' => 'mlt', 'na' => 'Malti', 'en' => 'Maltese', 'be' => 'мальційская', 'sub' => false],
        ['iso639-3b' => 'mao', 'na' => 'te reo Māori', 'en' => 'Maori', 'be' => 'маары', 'sub' => false],
        ['iso639-3b' => 'mar', 'na' => 'मराठी', 'en' => 'Marathi', 'be' => 'маратхі', 'sub' => false],
        ['iso639-3b' => 'mah', 'na' => 'Kajin M̧ajeļ', 'en' => 'Marshallese', 'be' => 'маршальская', 'sub' => false],
        ['iso639-3b' => 'mon', 'na' => 'Монгол хэл', 'en' => 'Mongolian', 'be' => 'мангольская', 'sub' => false],
        ['iso639-3b' => 'nau', 'na' => 'Dorerin Naoero', 'en' => 'Nauru', 'be' => 'наўруанская', 'sub' => false],
        ['iso639-3b' => 'nav', 'na' => 'Diné bizaad', 'en' => 'Navajo', 'be' => 'навахо', 'sub' => false],
        ['iso639-3b' => 'nde', 'na' => 'isiNdebele', 'en' => 'North Ndebele', 'be' => 'паўночная ндэбеле', 'sub' => false],
        ['iso639-3b' => 'nep', 'na' => 'नेपाली', 'en' => 'Nepali', 'be' => 'непальская', 'sub' => false],
        ['iso639-3b' => 'ndo', 'na' => 'Owambo', 'en' => 'Ndonga', 'be' => 'ндонга', 'sub' => false],
        ['iso639-3b' => 'nob', 'na' => 'Norsk bokmål', 'en' => 'Bokmål', 'be' => 'букмал', 'sub' => false],
        ['iso639-3b' => 'nno', 'na' => 'Norsk nynorsk', 'en' => 'Nynorsk', 'be' => 'нованарвежская', 'sub' => false],
        ['iso639-3b' => 'nor', 'na' => 'Norsk', 'en' => 'Norwegian', 'be' => 'нарвежская', 'sub' => true],
        ['iso639-3b' => 'iii', 'na' => 'Nuosuhxop', 'en' => 'Nuosu', 'be' => 'носу', 'sub' => false],
        ['iso639-3b' => 'nbl', 'na' => 'isiNdebele', 'en' => 'South Ndebele', 'be' => 'паўднёвая ндэбеле', 'sub' => false],
        ['iso639-3b' => 'oci', 'na' => 'occitan, lenga d\'òc', 'en' => 'Occitan', 'be' => 'аксітанская', 'sub' => false],
        ['iso639-3b' => 'oji', 'na' => 'ᐊᓂᔑᓈᐯᒧᐎᓐ', 'en' => 'Ojibwa', 'be' => 'аджыбва', 'sub' => false],
        ['iso639-3b' => 'chu', 'na' => 'ѩзыкъ словѣньскъ', 'en' => 'Church Slavonic', 'be' => 'царкоўнаславянская', 'sub' => false],
        ['iso639-3b' => 'orm', 'na' => 'Afaan Oromoo', 'en' => 'Oromo', 'be' => 'арома', 'sub' => false],
        ['iso639-3b' => 'ori', 'na' => 'ଓଡ଼ିଆ', 'en' => 'Oriya', 'be' => 'орыя', 'sub' => false],
        ['iso639-3b' => 'oss', 'na' => 'ирон æвзаг', 'en' => 'Ossetian', 'be' => 'асецінская', 'sub' => false],
        ['iso639-3b' => 'pan', 'na' => 'ਪੰਜਾਬੀ', 'en' => 'Punjabi', 'be' => 'панджабі', 'sub' => true],
        ['iso639-3b' => 'pli', 'na' => 'पाऴि', 'en' => 'Pali', 'be' => 'палі', 'sub' => false],
        ['iso639-3b' => 'per', 'na' => 'فارسی', 'en' => 'Persian', 'be' => 'персідская', 'sub' => true],
        ['iso639-3b' => 'pol', 'na' => 'polszczyzna', 'en' => 'Polish', 'be' => 'польская', 'sub' => true],
        ['iso639-3b' => 'pus', 'na' => 'پښتو', 'en' => 'Pashto', 'be' => 'пушту', 'sub' => false],
        ['iso639-3b' => 'por', 'na' => 'português', 'en' => 'Portuguese', 'be' => 'партугальская', 'sub' => true],
        ['iso639-3b' => 'que', 'na' => 'Kichwa', 'en' => 'Quechua', 'be' => 'кечуа', 'sub' => false],
        ['iso639-3b' => 'roh', 'na' => 'rumantsch grischun', 'en' => 'Romansh', 'be' => 'рэтараманская', 'sub' => false],
        ['iso639-3b' => 'run', 'na' => 'Ikirundi', 'en' => 'Rundi', 'be' => 'рундзі', 'sub' => false],
        ['iso639-3b' => 'rum', 'na' => 'limba română', 'en' => 'Romanian', 'be' => 'румынская', 'sub' => true],
        ['iso639-3b' => 'rus', 'na' => 'Русский', 'en' => 'Russian', 'be' => 'руская', 'sub' => true],
        ['iso639-3b' => 'san', 'na' => 'संस्कृतम्', 'en' => 'Sanskrit', 'be' => 'санскрыт', 'sub' => false],
        ['iso639-3b' => 'srd', 'na' => 'sardu', 'en' => 'Sardinian', 'be' => 'сардзінская', 'sub' => false],
        ['iso639-3b' => 'snd', 'na' => 'सिन्धी', 'en' => 'Sindhi', 'be' => 'сіндхі', 'sub' => false],
        ['iso639-3b' => 'sme', 'na' => 'Davvisámegiella', 'en' => 'Northern Sami', 'be' => 'паўночнасаамская', 'sub' => false],
        ['iso639-3b' => 'smo', 'na' => 'gagana fa\'a Samoa', 'en' => 'Samoan', 'be' => 'самаанская', 'sub' => false],
        ['iso639-3b' => 'sag', 'na' => 'yângâ tî sängö', 'en' => 'Sango', 'be' => 'санга', 'sub' => false],
        ['iso639-3b' => 'srp', 'na' => 'српски језик', 'en' => 'Serbian', 'be' => 'сербская', 'sub' => true],
        ['iso639-3b' => 'gla', 'na' => 'Gàidhlig', 'en' => 'Scottish Gaelic', 'be' => 'шатландская гэльская', 'sub' => false],
        ['iso639-3b' => 'sna', 'na' => 'chiShona', 'en' => 'Shona', 'be' => 'шона', 'sub' => false],
        ['iso639-3b' => 'sin', 'na' => 'සිංහල', 'en' => 'Sinhalese', 'be' => 'сінгальская', 'sub' => false],
        ['iso639-3b' => 'slo', 'na' => 'slovenčina', 'en' => 'Slovak', 'be' => 'славацкая', 'sub' => true],
        ['iso639-3b' => 'slv', 'na' => 'slovenščina', 'en' => 'Slovenian', 'be' => 'славенская', 'sub' => true],
        ['iso639-3b' => 'som', 'na' => 'Soomaaliga', 'en' => 'Somali', 'be' => 'самалійская', 'sub' => false],
        ['iso639-3b' => 'sot', 'na' => 'Sesotho', 'en' => 'Sotho', 'be' => 'сесота', 'sub' => false],
        ['iso639-3b' => 'spa', 'na' => 'español', 'en' => 'Spanish', 'be' => 'іспанская', 'sub' => true],
        ['iso639-3b' => 'sun', 'na' => 'Basa Sunda', 'en' => 'Sundanese', 'be' => 'сунданская', 'sub' => false],
        ['iso639-3b' => 'swa', 'na' => 'Kiswahili', 'en' => 'Swahili', 'be' => 'суахілі', 'sub' => false],
        ['iso639-3b' => 'ssw', 'na' => 'SiSwati', 'en' => 'Swati', 'be' => 'сваці', 'sub' => false],
        ['iso639-3b' => 'swe', 'na' => 'svenska', 'en' => 'Swedish', 'be' => 'шведская', 'sub' => true],
        ['iso639-3b' => 'tam', 'na' => 'தமிழ்', 'en' => 'Tamil', 'be' => 'тамільская', 'sub' => false],
        ['iso639-3b' => 'tel', 'na' => 'తెలుగు', 'en' => 'Telugu', 'be' => 'тэлугу', 'sub' => false],
        ['iso639-3b' => 'tgk', 'na' => 'тоҷикӣ', 'en' => 'Tajik', 'be' => 'таджыкская', 'sub' => false],
        ['iso639-3b' => 'tah', 'na' => 'ไทย', 'en' => 'Tahitian', 'be' => 'таіцянская', 'sub' => false],
        ['iso639-3b' => 'tir', 'na' => 'ትግርኛ', 'en' => 'Tigrinya', 'be' => 'тыгрынья', 'sub' => false],
        ['iso639-3b' => 'tib', 'na' => 'བོད་ཡིག', 'en' => 'Tibetan', 'be' => 'тыбецкая', 'sub' => false],
        ['iso639-3b' => 'tuk', 'na' => 'Türkmen', 'en' => 'Turkmen', 'be' => 'туркменская', 'sub' => false],
        ['iso639-3b' => 'tgl', 'na' => 'Wikang Tagalog', 'en' => 'Tagalog', 'be' => 'тагальская', 'sub' => false],
        ['iso639-3b' => 'tsn', 'na' => 'Setswana', 'en' => 'Tswana', 'be' => 'тсвана', 'sub' => false],
        ['iso639-3b' => 'ton', 'na' => 'faka Tonga', 'en' => 'Tonga', 'be' => 'танганская', 'sub' => false],
        ['iso639-3b' => 'tur', 'na' => 'Türkçe', 'en' => 'Turkish', 'be' => 'турэцкая', 'sub' => true],
        ['iso639-3b' => 'tso', 'na' => 'Xitsonga', 'en' => 'Tsonga', 'be' => 'тсонга', 'sub' => false],
        ['iso639-3b' => 'tat', 'na' => 'татар теле', 'en' => 'Tatar', 'be' => 'татарская', 'sub' => false],
        ['iso639-3b' => 'twi', 'na' => 'Twi', 'en' => 'Twi', 'be' => 'чві', 'sub' => false],
        ['iso639-3b' => 'tha', 'na' => 'ภาษาไทย', 'en' => 'Thai', 'be' => 'тайская', 'sub' => false],
        ['iso639-3b' => 'uig', 'na' => 'ئۇيغۇرچە‎', 'en' => 'Uighur', 'be' => 'уйгурская', 'sub' => false],
        ['iso639-3b' => 'ukr', 'na' => 'Українська', 'en' => 'Ukrainian', 'be' => 'украінская', 'sub' => true],
        ['iso639-3b' => 'urd', 'na' => 'اردو', 'en' => 'Urdu', 'be' => 'урду', 'sub' => false],
        ['iso639-3b' => 'uzb', 'na' => 'Oʻzbek', 'en' => 'Uzbek', 'be' => 'узбекская', 'sub' => false],
        ['iso639-3b' => 'ven', 'na' => 'Tshivenḓa', 'en' => 'Venda', 'be' => 'венда', 'sub' => false],
        ['iso639-3b' => 'vie', 'na' => 'Tiếng Việt', 'en' => 'Vietnamese', 'be' => 'в\'етнамская', 'sub' => false],
        ['iso639-3b' => 'vol', 'na' => 'Volapük', 'en' => 'Volapük', 'be' => 'валапюк', 'sub' => false],
        ['iso639-3b' => 'win', 'na' => 'walon', 'en' => 'Walloon', 'be' => 'валонская', 'sub' => false],
        ['iso639-3b' => 'wel', 'na' => 'Cymraeg', 'en' => 'Welsh', 'be' => 'валійская', 'sub' => false],
        ['iso639-3b' => 'wol', 'na' => 'Wollof', 'en' => 'Wolof', 'be' => 'волаф', 'sub' => false],
        ['iso639-3b' => 'fry', 'na' => 'Frysk', 'en' => 'West Frisian', 'be' => 'заходнефрызская', 'sub' => false],
        ['iso639-3b' => 'xho', 'na' => 'isiXhosa', 'en' => 'Xhosa', 'be' => 'коса', 'sub' => false],
        ['iso639-3b' => 'yid', 'na' => 'ייִדיש', 'en' => 'Yiddish', 'be' => 'ідыш', 'sub' => false],
        ['iso639-3b' => 'yor', 'na' => 'Yorùbá', 'en' => 'Yoruba', 'be' => 'ёруба', 'sub' => false],
        ['iso639-3b' => 'zha', 'na' => 'Saɯ cueŋƅ', 'en' => 'Zhuang', 'be' => 'чжуанская', 'sub' => false],
        ['iso639-3b' => 'zul', 'na' => 'isiZulu', 'en' => 'Zulu', 'be' => 'зулу', 'sub' => false],
    ];

    /**
     * LanguageSeeder constructor.
     * @param LaravelDocumentManager $ldm
     * @param Language $language
     */
    public function __construct(LaravelDocumentManager $ldm)
    {
        $this->dm = $ldm->getDocumentManager();
    }

    public function seed()
    {
        // Truncate collection
        $collection = $this->dm->getDocumentCollection(Language::class);
        $collection->remove([]);
        // Import data
        array_walk($this->languagesData, [$this, 'persist']);
        $this->dm->flush();
    }

    /**
     * @param $languageData array
     */
    protected function persist(array $languageData)
    {
        $language = new Language();
        $language->setId($languageData['iso639-3b']);
        $language->setNativeName($languageData['na']);
        $language->setEnglishName($languageData['en']);
        $language->setBelName($languageData['be']);
        $language->setIsSubable($languageData['sub']);
        $this->dm->persist($language);
    }
}