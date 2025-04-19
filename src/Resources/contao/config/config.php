<?php

/*
 * This file is part of Contao Party Bundle.
 *
 * (c) Max Pawellek
 *
 * @license LGPL-3.0-or-later
 */

// Frontend Module
$GLOBALS['FE_MOD']['echtermax'] = [
    'partylist' => 'Echtermax\PartyBundle\Module\PartyListModule',
];

// Backend-Module
$GLOBALS['BE_MOD']['content']['party'] = [
    'tables' => ['tl_party'],
    'icon'   => 'bundles/contaopartybundle/images/icon.svg',
];

// Models
$GLOBALS['TL_MODELS']['tl_party'] = \Echtermax\PartyBundle\Model\PartyModel::class;
