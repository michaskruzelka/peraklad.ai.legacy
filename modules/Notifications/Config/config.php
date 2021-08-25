<?php

return [
	'name' => 'Notifications',
    'views' => [
        'header_navbar' => [
            'right' => [
                'view' => 'notifications::workshop_top_menu',
                'callback' => 'composeWorkshopTopMenu',
                'sort' => 10
            ],
        ],
        'left_navbar' => [
            'bottom' => [
                'view' => 'notifications::workshop_left_buttons',
                'callback' => 'composeWorkshopLeftButtons',
                'sort' => 20
            ]
        ]
    ]
];