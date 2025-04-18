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
            $arrOptions['order'] = "$t.date DESC";
        }

        $arrColumns[] = "$t.published='1'";

        return static::findBy($arrColumns, null, $arrOptions);
    }

    /**
     * Findet alle veröffentlichten Party-Einträge
     *
     * @param array $arrOptions
     *
     * @return \Model\Collection|PartyModel[]|PartyModel|null
     */
    public static function findAllPublished($arrOptions = [])
    {
        $t = static::$strTable;
        $arrColumns = ["$t.published='1'"];

        if (!isset($arrOptions['order'])) {
            $arrOptions['order'] = "$t.date DESC";
        }

        return static::findBy($arrColumns, null, $arrOptions);
    }
}
