<?php

return [
	'name' => 'Breadcrumbs',
    'views' => [
        'page_main' => [
            'mainActions' => [
                'view' => 'breadcrumbs::workshop_main_actions',
                'callback' => 'composeWorkshopMainActions',
                'sort' => 10
            ]
        ]
    ]
];