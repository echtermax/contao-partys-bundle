<?php

/*
 * This file is part of Contao Party Bundle.
 *
 * (c) Max Pawellek
 *
 * @license LGPL-3.0-or-later
 */

$GLOBALS['TL_DCA']['tl_party'] = [
    // Config
    'config' => [
        'dataContainer'               => \Contao\DC_Table::class,
        'enableVersioning'            => true,
        'sql' => [
            'keys' => [
                'id' => 'primary'
            ]
        ],
    ],

    // List
    'list' => [
        'sorting' => [
            'mode'                    => 2,
            'fields'                  => ['startDate'],
            'flag'                    => 6,
            'panelLayout'             => 'filter;search,limit',
            'sorting' => [
                'mode'            => 1,
                'fields'          => ['startDate'],
                'flag'            => 1,
                'panelLayout'     => 'filter;search,limit',
            ],
        ],
        'label' => [
            'fields'                  => ['startDate', 'title'],
            'label_callback'          => function(array $row) {
                $date = $row['startDate'] ? \Contao\Date::parse('d.m.Y', $row['startDate']) : '-';
                $time = $row['startTime'] ? \Contao\Date::parse('H:i', $row['startTime']) : '';
                $title = $row['title'];

                return sprintf('%s %s | %s', $date, $time, $title);
            }
        ],
        'global_operations' => [
            'all' => [
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            ]
        ],
        'operations' => [
            'edit' => [
                'href'                => 'act=edit',
                'icon'                => 'edit.svg'
            ],
            'copy' => [
                'href'                => 'act=copy',
                'icon'                => 'copy.svg'
            ],
            'delete' => [
                'href'                => 'act=delete',
                'icon'                => 'delete.svg',
                'attributes'          => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"'
            ],
            'toggle' => [
                'href'                => 'act=toggle&amp;field=published',
                'icon'                => 'visible.svg',
            ],
            'show' => [
                'href'                => 'act=show',
                'icon'                => 'show.svg'
            ]
        ]
    ],

    // Palettes
    'palettes' => [
        'default'                     => '  {title_legend},title,addedBy,description,cost,currency;
                                            {date_legend},startDate,startTime,endDate,location;
                                            {invitation_legend},inviteOnly;
                                            {publish_legend},published',
        '__selector__'               => ['inviteOnly'],
    ],

    // Subpalettes
    'subpalettes' => [
        'inviteOnly'                 => 'invitedUsers',
    ],

    // Fields
    'fields' => [
        'id' => [
            'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ],
        'tstamp' => [
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ],
        'title' => [
            'inputType'               => 'text',
            'exclude'                 => true,
            'search'                  => true,
            'filter'                  => true,
            'sorting'                 => true,
            'eval'                    => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'addedBy' => [
            'exclude'                 => true,
            'inputType'               => 'select',
            'eval'                    => ['mandatory' => true, 'includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
            'sql'                     => "int(10) unsigned NOT NULL default '0'",
            'options_callback'        => function () {
                $options = [];
                $members = \Contao\Database::getInstance()
                    ->prepare("SELECT id, firstname, lastname FROM tl_member ORDER BY lastname, firstname")
                    ->execute();

                while ($members->next()) {
                    $options[$members->id] = $members->firstname . ' ' . $members->lastname;
                }

                return $options;
            },
        ],
        'description' => [
            'inputType'               => 'textarea',
            'exclude'                 => true,
            'search'                  => true,
            'eval'                    => ['rte'=>'tinyMCE', 'tl_class'=>'clr'],
            'sql'                     => "text NULL"
        ],
        'cost' => [
            'inputType'  => 'text',
            'exclude'    => true,
            'eval'       => [
                'rgxp'        => 'price',
                'minval'      => 0,
                'tl_class'    => 'w50',
                'maxlength'   => 10,
                'decodeEntities' => true,
            ],
            'sql'        => "decimal(10,2) NOT NULL default '0.00'"
        ],
        'currency' => [
            'inputType'  => 'select',
            'exclude'    => true,
            'options'    => ['EUR', 'USD', 'GBP'],
            'eval'       => ['tl_class' => 'w50'],
            'sql'        => "varchar(3) NOT NULL default 'EUR'"
        ],
        'startDate' => [
            'inputType'  => 'text',
            'exclude'    => true,
            'sorting'    => true,
            'filter'     => true,
            'eval'       => ['mandatory' => true, 'rgxp' => 'date', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql'        => "varchar(10) NOT NULL default ''"
        ],
        'startTime' => [
            'inputType'  => 'text',
            'exclude'    => true,
            'eval'       => ['rgxp' => 'time', 'tl_class' => 'w50'],
            'sql'        => "varchar(10) NOT NULL default ''"
        ],
        'endDate' => [
            'inputType'  => 'text',
            'exclude'    => true,
            'filter'     => true,
            'eval'       => ['rgxp' => 'date', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql'        => "varchar(10) NOT NULL default ''"
        ],
        'location' => [
            'inputType'               => 'text',
            'exclude'                 => true,
            'search'                  => true,
            'eval'                    => ['maxlength'=>255, 'tl_class'=>'w50'],
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'published' => [
            'toggle'                  => true,
            'exclude'                 => true,
            'filter'                  => true,
            'flag'                    => 1,
            'inputType'               => 'checkbox',
            'eval'                    => ['doNotCopy'=>true],
            'sql'                     => "char(1) NOT NULL default ''"
        ],
        'inviteOnly' => [
            'exclude'                 => true,
            'filter'                  => true,
            'inputType'               => 'checkbox',
            'eval'                    => ['submitOnChange'=>true, 'tl_class'=>'w50 clr'],
            'sql'                     => "char(1) NOT NULL default ''"
        ],
        'invitedUsers' => [
            'exclude'                 => true,
            'inputType'               => 'checkbox',
            'eval'                    => ['multiple' => true, 'tl_class' => 'clr'],
            'sql'                     => "blob NULL",
            'relation'                => [
                'type'                => 'hasMany',
                'load'                => 'lazy',
                'table'               => 'tl_member',
                'field'               => 'id'
            ],
            'options_callback'        => function () {
                $options = [];
                $members = \Contao\Database::getInstance()
                    ->prepare("SELECT id, firstname, lastname FROM tl_member ORDER BY lastname, firstname")
                    ->execute();

                while ($members->next()) {
                    $options[$members->id] = $members->firstname . ' ' . $members->lastname;
                }

                return $options;
            },
        ],
    ]
];
