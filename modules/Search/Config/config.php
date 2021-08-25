<?php

return [
	'name' => 'Search',
	'views' => [
		'header_navbar' => [
            'left' => [
                'view' => 'search::workshop_global_toogle',
                'callback' => 'composeWorkshopToogle',
                'sort' => 10
            ],
            'after' => [
                'view' => 'search::workshop_global_form',
                'callback' => 'composeWorkshopForm',
                'sort' => 10
            ],
        ]
	]
];