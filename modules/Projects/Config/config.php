<?php

return [
	'name' => 'Projects',
    'menu' => [
        'workshop-left-top-desktop' => [
            [
                'title' => 'Праекты',
                'icon-class' => 'ion-closed-captioning',
                'type' => 'root',
                'category' => 'Пераклады',
                'sort' => 10,
                'children' => [
                    [
                        'title' => 'Дадаць',
                        'type' => 'child',
                        'routes' => ['workshop::projects::new'],
                    ],
                    [
                        'title' => 'Удзельнічаць',
                        'type' => 'child',
                        'routes' => [
                            'workshop::projects::list',
                            'workshop::subtitles::view',
                            'workshop::releases::subrip::view'
                        ],
                    ],
                    [
                        'title' => 'Мае праекты',
                        'type' => 'child',
                        'routes' => [
                            'workshop::projects::my',
                            'workshop::projects::edit'
                        ],
                    ],
                ]
            ]
        ]
    ],
    'languagesOrder' => [
        'rus' => 3,
        'ukr' => 5,
        'eng' => 1,
        'fre' => 2,
        'pol' => 4,
    ],
    'poster' => [
        'dimensions' => [
            'width' => 182,
            'height' => 268
        ]
    ],
    'orthographies' => [
        'n' => [
            'value' => 'акадэмічны',
            'alt-value' => 'наркамаўка'
        ],
        't' => [
            'value' => 'класічны',
            'alt-value' => 'тарашкевіца'
        ]
    ],
    'subtitlesApi' => [
        'username' => env('SUBTITLE_API_USERNAME', ''),
        'password' => env('SUBTITLE_API_PASSWORD', ''),
        'useragent' => env('SUBTITLE_API_USERAGENT', 'OSTestUserAgent'),
        'language' => 'bel'
    ],
    'subtitles' => [
        'permitted-formats' => ['srt']
    ],
    'charsets' => [
        'cy' => [
            'utf-8'
        ],
        'la' => [
            'utf-8'
        ]
    ],
    'downloadableCharsets' => [
        'cy' => [
            'utf-8',
            'windows-1251',
            'iso-8859-5'
        ],
        'la' => [
            'utf-8'
        ]
    ],
    'newLineFormats' => [
        'windows' => 'Windows (0D 0A)',
        'mac' => 'MacOS (0D)',
        'unix' => 'Unix (0A)'
    ],
    'languageDetector' => [
        'api-key' => env('LANG_DETECTOR_API_KEY', '')
    ],
    'modes' => [
        'pr' => 'private',
        'pu' => 'public'
    ],
    'states' => [
        'un' => 'underway',
        'fa' => 'failed',
        'co' => 'completed',
        'de' => 'destroyed'
    ],
    'subtitleStatuses' => [
        'un' => 'underway',
        'cl' => 'clean',
        'sa' => 'saved'
    ],
    'statesDetailed' => [
        'un' => [
            'color' => 'blue',
            'title' => 'Перакладаецца'
        ],
        'co' => [
            'color' => 'green',
            'title' => 'Завершана'
        ],
        'fa' => [
            'color' => 'orange',
            'title' => 'Выдалена'
        ]
    ],
    'releasesLimitPerPage' => 10,
    'subtitlesLimitPerPage' => 5,
    'statPeriodsWeek' => [
        2 => 'Панядзелак',
        3 => 'Аўторак',
        4 => 'Серада',
        5 => 'Чацвер',
        6 => 'Пятніца',
        7 => 'Субота',
        1 => 'Нядзеля'
    ]
];