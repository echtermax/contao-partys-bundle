<?php

/*
 * This file is part of Contao Party Bundle.
 *
 * (c) Max Pawellek
 *
 * @license LGPL-3.0-or-later
 */

// Backend-Module
$GLOBALS['BE_MOD']['content']['party'] = [
    'tables' => ['tl_party'],
    'icon'   => 'bundles/contaopartybundle/images/icon.svg',
];

// Models
$GLOBALS['TL_MODELS']['tl_party'] = \Echtermax\PartyBundle\Model\PartyModel::class;
$GLOBALS['TL_MODELS']['tl_party_response'] = \Echtermax\PartyBundle\Model\PartyResponseModel::class;
$GLOBALS['TL_MODELS']['tl_push_subscription'] = \Echtermax\PartyBundle\Model\PushNotificationsModel::class;
