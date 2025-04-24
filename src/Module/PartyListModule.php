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
    protected function compile(): void
    {
        $memberId = 0;
        $user = \Contao\FrontendUser::getInstance();
        if ($user && $user->id) {
            $memberId = (int)$user->id;
        }

        $parties = PartyModel::findPublishedPartiesForMember($memberId);
        $arrParties = [];
        
        if (null !== $parties) {
            foreach ($parties as $party) {
                $arrParty = $party->row();
                
                if ($arrParty['date']) {
                    $arrParty['formattedDate'] = Date::parse('d.m.Y', $arrParty['date']);
                    $arrParty['formattedTime'] = Date::parse('H:i', $arrParty['date']);
                } else {
                    $arrParty['formattedDate'] = '?';
                    $arrParty['formattedTime'] = '?';
                }

                $arrParties[] = $arrParty;
            }
        }
        
        $twig = System::getContainer()->get('twig');
        $this->Template->parties = $twig->render('@ContaoParty/party_list.html.twig', [
            'parties' => $arrParties
        ]);
    }
}
