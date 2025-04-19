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
use Twig\Environment as TwigEnvironment;

/**
 * Controller für die Frontend-Darstellung von Party-Einträgen
 *
 * @Route("/party", defaults={"_scope" = "frontend"})
 */
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
     * Zeigt eine Liste aller veröffentlichten Partys an
     *
     * @Route("/list", name="party_list")
     */
    public function listAction(): Response
    {
        $this->framework->initialize();

        $parties = PartyModel::findPublishedParties();
        $arrParties = [];

        if ($parties !== null) {
            // Formatieren der Party-Daten
            foreach ($parties as $party) {
                $partyData = $party->row();

                // Datumsformatierung hinzufügen
                if ($partyData['date']) {
                    $partyData['formattedDate'] = Date::parse('d.m.Y', $partyData['date']);
                    $partyData['formattedTime'] = Date::parse('H:i', $partyData['date']);
                }

                $arrParties[] = $partyData;
            }
        }
        
        return $this->render('@ContaoParty/party_list.html.twig', [
            'parties' => $arrParties
        ]);
    }
}
