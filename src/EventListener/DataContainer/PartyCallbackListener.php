<?php

declare(strict_types=1);

/*
 * This file is part of Contao Party Bundle.
 *
 * (c) Max Pawellek
 *
 * @license LGPL-3.0-or-later
 */

namespace Echtermax\PartyBundle\EventListener\DataContainer;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\DataContainer;
use Contao\Date;
use Contao\Image;
use Contao\StringUtil;

class PartyCallbackListener
{
    private $framework;

    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Gibt das Veröffentlichungs-Icon zurück
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        $this->framework->initialize();

        $stringUtil = $this->framework->getAdapter(StringUtil::class);
        $image = $this->framework->getAdapter(Image::class);

        if (strpos($attributes, 'data-state') === false) {
            $attributes = 'data-state="' . ($row['published'] ? 1 : 0) . '"';
        }

        if (!$row['published']) {
            $icon = 'invisible.svg';
        }

        return sprintf(
            '<a href="%s" title="%s" %s>%s</a> ',
            $stringUtil->ampersand('contao/main.php?do=party&amp;table=tl_party&amp;' . $href . '&amp;id=' . $row['id']),
            $title,
            $attributes,
            $image->getHtml($icon, $label, 'data-icon="' . $icon . '" data-icon-disabled="invisible.svg" data-icon-enabled="visible.svg"')
        );
    }

    /**
     * Speichert den Timestamp bei Änderungen
     *
     * @param DataContainer $dc
     */
    public function setTimestamp(DataContainer $dc): void
    {
        // Return if no active record is available
        if (!$dc->activeRecord) {
            return;
        }

        $this->framework->initialize();
        $dateAdapter = $this->framework->getAdapter(Date::class);

        $time = time();
        $db = \Contao\Database::getInstance();

        $db->prepare("UPDATE tl_party SET tstamp=? WHERE id=?")
           ->execute($time, $dc->id);
    }
}
