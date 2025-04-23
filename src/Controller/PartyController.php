<?php

declare(strict_types=1);

/*
 * This file is part of Contao Party Bundle.
 *
 * (c) Max Pawellek
 *
 * @license LGPL-3.0-or-later
 */

namespace Echtermax\PartyBundle\Controller;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Date;
use Echtermax\PartyBundle\Model\PartyModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment as TwigEnvironment;

/**
 * Controller für die Frontend-Darstellung von Party-Einträgen
 */
#[Route('/party', defaults: ['_scope' => 'frontend'])]
class PartyController extends AbstractController
{
    private ContaoFramework $framework;
    private TwigEnvironment $twig;

    public function __construct(ContaoFramework $framework, TwigEnvironment $twig)
    {
        $this->framework = $framework;
        $this->twig = $twig;
    }

    /**
     * @throws \Exception
     */
    #[Route('/generate-ics/{id}', name: 'download_party_ics')]
    public function downloadIcs(int $id): Response
    {
        $this->framework->initialize();

        $partyModel = PartyModel::findById($id);

        if (!$partyModel) {
            throw $this->createNotFoundException('Party not found.');
        }

        $icsContent = $this->generateIcs($partyModel);

        return new Response($icsContent, 200, [
            'Content-Type' => 'text/calendar',
            'Content-Disposition' => 'attachment; filename="party.ics"',
        ]);
    }

    /**
     * @throws \Exception
     */
    private function generateIcs($party): string
    {
        dump($party);
        if (!$party->date) return '';

        $start = (new \DateTime())->setTimestamp((int) $party->date);
        $end = (clone $start)->modify('+2 hours');

        return <<<ICS
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Echtermax//PartyCalendar//DE
BEGIN:VEVENT
UID:party-{$party->id}-echtermax
DTSTAMP:{$start->format('Ymd\THis\Z')}
DTSTART:{$start->format('Ymd\THis')}
DTEND:{$end->format('Ymd\THis')}
SUMMARY:{$this->escapeIcsText($party->title)}
LOCATION:{$this->escapeIcsText($party->location)}
DESCRIPTION:{$this->escapeIcsText($party->description)}
END:VEVENT
END:VCALENDAR
ICS;
    }

    private function escapeIcsText(string $text): string
    {
        $text = strip_tags($text);
        $text = str_replace(["\\", ";", ",", "\n"], ["\\\\", "\;", "\,", "\\n"], $text);
        return $text;
    }
}
