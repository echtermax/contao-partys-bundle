<?php

declare(strict_types=1);

/*
 * This file is part of Contao Party Bundle.
 *
 * (c) Max Pawellek
 *
 * @license LGPL-3.0-or-later
 */

namespace Echtermax\PartyBundle\Model;

use Contao\Model;

/**
 * Handles the tl_party_response table
 */
class PartyResponseModel extends Model
{
    protected static $strTable = 'tl_party_response';

    /**
     * Findet die Antwort eines Mitglieds zu einer Party
     */
    public static function findByPartyAndMember(int $partyId, int $memberId)
    {
        $t = static::$strTable;
        return static::findOneBy(["$t.pid=? AND $t.member_id=?"], [$partyId, $memberId]);
    }

    /**
     * Findet alle Antworten zu einer Party
     */
    public static function findByParty(int $partyId)
    {
        return static::findBy(['pid=?'], [$partyId]);
    }

    /**
     * Findet alle akzeptierten Einladungen zu einer Party
     */
    public static function findAcceptedByParty(int $partyId)
    {
        return static::findBy(['pid=? AND response=?'], [$partyId, 'accept']);
    }

    /**
     * Findet alle abgelehnten Einladungen zu einer Party
     */
    public static function findDeclinedByParty(int $partyId)
    {
        return static::findBy(['pid=? AND response=?'], [$partyId, 'decline']);
    }
}
