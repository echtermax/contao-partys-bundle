<?php

declare(strict_types=1);

/*
 * This file is part of Contao Party Bundle.
 *
 * (c) Max Pawellek
 *
 * @license LGPL-3.0-or-later
 */

namespace Echtermax\PartyBundle\Module;

use Contao\BackendTemplate;
use Contao\Date;
use Contao\Module;
use Contao\System;
use Echtermax\PartyBundle\Model\PartyModel;
use Symfony\Component\HttpFoundation\Request;

class PartyListModule extends Module
{
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_partylist';

    /**
     * Display a wildcard in the back end
     *
     * @return string
     */
    public function generate()
    {
        if (System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest(System::getContainer()->get('request_stack')->getCurrentRequest() ?? Request::create(''))) {
            $template = new BackendTemplate('be_wildcard');
            $template->wildcard = '### ' . $GLOBALS['TL_LANG']['FMD']['partylist'][0] . ' ###';
            $template->title = $this->headline;
            $template->id = $this->id;
            $template->link = $this->name;
            $template->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $template->parse();
        }

        return parent::generate();
    }

    /**
     * Generate the module
     */
    protected function compile()
    {
        $parties = PartyModel::findPublishedParties();
        $arrParties = [];
        
        if (null !== $parties) {
            // Vorbereiten der Party-Daten für die Twig-Vorlage
            foreach ($parties as $party) {
                $arrParty = $party->row();
                
                // Datumsformatierung
                if ($arrParty['date']) {
                    $arrParty['formattedDate'] = Date::parse('d.m.Y', $arrParty['date']);
                    $arrParty['formattedTime'] = Date::parse('H:i', $arrParty['date']);
                }
                
                // Beschreibung mit HTML-Elementen verarbeiten
                if ($arrParty['description']) {
                    // In Contao wird RichText-Inhalt automatisch als HTML gespeichert
                    // Wir müssen daher keine spezielle Konvertierung durchführen
                    $arrParty['description'] = $arrParty['description'];
                }
                
                $arrParties[] = $arrParty;
            }
        }
        
        // Übergeben der Daten an das Twig-Template, auch wenn die Liste leer ist
        $twig = System::getContainer()->get('twig');
        $this->Template->parties = $twig->render('@ContaoParty/party_list.html.twig', [
            'parties' => $arrParties
        ]);
    }
}
