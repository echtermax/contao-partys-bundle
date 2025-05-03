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
use Contao\Model\Collection;

/**
 * Liest und schreibt Partys
 *
 * @property integer $id
 * @property integer $tstamp
 * @property string  $title
 * @property string  $description
 * @property string  $date
 * @property string  $location
 * @property boolean $published
 */
class PartyModel extends Model
{
    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_party';

    /**
     * Findet veröffentlichte Party-Einträge nach IDs
     *
     * @param array $arrIds
     * @param array $arrOptions
     *
     * @return \Model\Collection|PartyModel[]|PartyModel|null
     */
    public static function findPublishedByIds($arrIds, $arrOptions = [])
    {
        $t = static::$strTable;
        $arrColumns = [
            "$t.id IN(" . implode(',', array_map('\intval', $arrIds)) . ")"
        ];

        if (!isset($arrOptions['order'])) {
            $arrOptions['order'] = "$t.startDate DESC";
        }

        $arrColumns[] = "$t.published='1'";

        return static::findBy($arrColumns, null, $arrOptions);
    }

    /**
     * Findet alle veröffentlichten Partys für einen Nutzer, sortiert nach Datum
     *
     * @param int $memberId
     * @return Collection|null
     */
    public static function findPublishedPartiesForMember(int $memberId): ?Model\Collection
    {
        $t = static::$strTable;
        $arrColumns = ["$t.published='1'"];
        $parties = static::findBy($arrColumns, null, ['order' => "$t.startDate DESC"]);

        if (null === $parties) {
            return null;
        }

        $filtered = [];
        foreach ($parties as $party) {
            if (!$party->inviteOnly) {
                $filtered[] = $party;
                continue;
            }

            $partyInvitesBlob = $party->invitedUsers;
            if (!empty($partyInvitesBlob)) {
                $invitedUsers = @unserialize($partyInvitesBlob);
                if (is_array($invitedUsers) && in_array($memberId, $invitedUsers)) {
                    $filtered[] = $party;
                }
            }
        }

        if (empty($filtered)) {
            return null;
        }

        return new \Contao\Model\Collection($filtered, static::$strTable);
    }

    /**
     * Findet alle veröffentlichten Party-Einträge
     *
     * @param array $arrOptions
     *
     * @return Collection|Model|PartyModel|null
     */
    public static function findAllPublished(array $arrOptions = []): Collection|PartyModel|Model|null
    {
        $t = static::$strTable;
        $arrColumns = ["$t.published='1'"];

        if (!isset($arrOptions['order'])) {
            $arrOptions['order'] = "$t.startDate DESC";
        }

        return static::findBy($arrColumns, null, $arrOptions);
    }
}
