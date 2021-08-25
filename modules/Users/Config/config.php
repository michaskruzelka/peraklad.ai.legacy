<?php

return [
	'name' => 'Users',
    'menu' => [
        'workshop-left-top-desktop' => [
            [
                'title' => 'Удзельнікі',
                'icon-class' => 'wb-users',
                'type' => 'root',
                'category' => 'Перакладчыкі',
                'sort' => 20,
                'routes' => [
                    'workshop::users::view'
                ]
            ],
            [
                'title' => 'Геаграфія',
                'category' => 'Перакладчыкі',
                'type' => 'root',
                'sort' => 30,
                'icon-class' => 'wb-map',
                'routes' => [
                    'workshop::users::geo'
                ],
            ]
        ]
    ],
    'views' => [
        'header_navbar' => [
            'right' => [
                'view' => 'users::workshop_top_menu',
                'callback' => 'composeWorkshopTopMenu',
                'sort' => 20
            ],
        ],
        'left_navbar' => [
            'bottom' => [
                'view' => 'users::workshop_left_buttons',
                'callback' => 'composeWorkshopLeftButtons',
                'sort' => 10
            ]
        ]
    ],
    'redirects' => [
        'login' => 'workshop::projects::my',
        'logout' => 'home',
        'register' => 'workshop::projects::list'
    ],
    'emails' => [
        'default' => 'mihaska87@gmail.com'
    ],
    'genderDetectorApiKey' => env('GENDER_DETECTOR_API_KEY', ''),
    'avatar' => [
        'dimensions' => [
            'width' => 128,
            'height' => 128
        ]
    ],
    'socialNetworks' => [
        'vk' => [
            'name' => 'Вконтакте',
            'class' => 'vk'
        ],
        'fb' => [
            'name' => 'Facebook',
            'class' => 'facebook'
        ],
        'tw' => [
            'name' => 'Twitter',
            'class' => 'twitter'
        ],
        'ln' => [
            'name' => 'Linkedin',
            'class' => 'linkedin'
        ],
        's' => [
            'name' => 'Skype',
            'class' => 'skype'
        ]
    ]
];