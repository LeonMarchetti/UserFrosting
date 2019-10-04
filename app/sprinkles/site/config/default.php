<?php

    /**
     * Sample site configuration file for UserFrosting.  You should definitely set these values!
     *
     */
    return [
        'address_book' => [
            'admin' => [
                'name'  => 'Squawkbot'
            ]
        ],
        'debug' => [
            'smtp' => true
        ],
        'site' => [
            'author'    =>      'David Attenborough',
            'title'     =>      'Owl Fancy',
            // URLs
            'uri' => [
                'author' => 'https://attenboroughsreef.com'
            ],
            'locales' => [
                'available' => [
                    'en_US' => 'English',
                    'zh_CN' => '中文',
                    'es_ES' => 'Español',
                    'ar'    => 'العربية',
                    'pt_PT' => 'Português',
                    'ru_RU' => 'русский',
                    'de_DE' => 'Deutsch',
                    'fr_FR' => 'Français',
                    'tr'    => 'Türk',
                    'it_IT' => 'Italiano',
                    'th_TH' => 'ภาษาไทย',
                    'fa'    => 'فارسی',
                    'el'    => 'Greek',

                ],
                'default' => 'es_ES',
            ],
        ],
        'php' => [
            'timezone' => 'America/Argentina/Buenos_Aires'
        ]
    ];
