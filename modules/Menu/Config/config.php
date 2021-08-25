<?php

return [
	'name' => 'Menu',
    'views' => [
        'left_navbar' => [
            'top' => [
                'view' => 'menu::workshop_left_top',
                'callback' => 'composeWorkshopLeftTop',
                'sort' => 10
            ]
        ]
    ]
];