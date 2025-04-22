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
            'mode'                    => 1,
            'fields'                  => ['title'],
            'flag'                    => 1,
            'panelLayout'             => 'filter;search,limit'
        ],
        'label' => [
            'fields'                  => ['title', 'date', 'location'],
            'format'                  => '%s | %s | %s'
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
        'default'                     => '{title_legend},title,description;{date_legend},date,location;{invitation_legend},inviteOnly,invitedUsers;{publish_legend},published'
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
            'eval'                    => ['mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'],
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'description' => [
            'inputType'               => 'textarea',
            'exclude'                 => true,
            'search'                  => true,
            'eval'                    => ['rte'=>'tinyMCE', 'tl_class'=>'clr'],
            'sql'                     => "text NULL"
        ],
        'date' => [
            'inputType'               => 'text',
            'exclude'                 => true,
            'sorting'                 => true,
            'filter'                  => true,
            'flag'                    => 8,
            'eval'                    => ['rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'],
            'sql'                     => "varchar(10) NOT NULL default ''"
        ],
        'location' => [
            'inputType'               => 'text',
            'exclude'                 => true,
            'search'                  => true,
            'eval'                    => ['maxlength'=>255, 'tl_class'=>'w50'],
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'published' => [
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
            'foreignKey'              => 'tl_member.firstname',
            'eval'                    => ['multiple'=>true, 'tl_class'=>'clr'],
            'sql'                     => "blob NULL",
            'relation'                => ['type'=>'hasMany', 'load'=>'lazy']
        ]
    ]
];
