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
use Contao\FrontendUser;
use Contao\Environment;
use Contao\MemberModel;
use Echtermax\PartyBundle\Model\PartyModel;
use Echtermax\PartyBundle\Model\PartyResponseModel;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * Lädt den aktuellen Response-Status des Benutzers für eine Party
     */
    protected function getUserResponseForParty(int $partyId, int $userId): ?string
    {
        $responseModel = PartyResponseModel::findByPartyAndMember($partyId, $userId);
        return $responseModel?->response;
    }

    /**
     * @throws Exception
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

    #[Route('/accept-invite/{id}', name: 'accept_party_invite')]
    public function acceptInvite(int $id): Response
    {
        $this->framework->initialize();

        if (!Environment::get('isAjaxRequest')) return $this->redirectToRoute('party_list');

        $partyModel = PartyModel::findById($id);
        if (!$partyModel) {
            throw $this->createNotFoundException('Party not found.');
        }
    
        $user = FrontendUser::getInstance();
        if (!$user->id) {
            throw $this->createAccessDeniedException('User must be logged in.');
        }
        
        $responseModel = PartyResponseModel::findByPartyAndMember($id, (int)$user->id);
        
        if (!$responseModel) {
            $responseModel = new PartyResponseModel();
            $responseModel->pid = $id;
            $responseModel->member_id = (int)$user->id;
        }
        
        $responseModel->tstamp = time();
        $responseModel->response = 'accept';
        $responseModel->save();
        
        return $this->json([
            'success' => true,
            'message' => 'Vielen Dank! Sie haben die Einladung angenommen.',
            'partyId' => $id,
            'response' => 'accept'
        ]);
    }
    
    #[Route('/decline-invite/{id}', name: 'decline_party_invite')]
    public function declineInvite(int $id): Response
    {
        $this->framework->initialize();

        if (!Environment::get('isAjaxRequest')) return $this->redirectToRoute('party_list');

        $partyModel = PartyModel::findById($id);
        if (!$partyModel) {
            throw $this->createNotFoundException('Party not found.');
        }
    
        $user = FrontendUser::getInstance();
        if (!$user->id) {
            throw $this->createAccessDeniedException('User must be logged in.');
        }
        
        $responseModel = PartyResponseModel::findByPartyAndMember($id, (int)$user->id);
        
        if (!$responseModel) {
            $responseModel = new PartyResponseModel();
            $responseModel->pid = $id;
            $responseModel->member_id = (int)$user->id;
        }
        
        $responseModel->tstamp = time();
        $responseModel->response = 'decline';
        $responseModel->save();
        
        return $this->json([
            'success' => true,
            'message' => 'Sie haben die Einladung abgelehnt.',
            'partyId' => $id,
            'response' => 'decline'
        ]);
    }
    
    #[Route('/attendees/{id}', name: 'party_attendees')]
    public function getPartyAttendees(int $id): Response
    {
        $this->framework->initialize();

        if (!Environment::get('isAjaxRequest')) return $this->redirectToRoute('party_list');

        $user = FrontendUser::getInstance();
        if (!$user->id) {
            return new JsonResponse(['success' => false, 'message' => 'Nicht eingeloggt.'], Response::HTTP_UNAUTHORIZED);
        }
        
        $partyModel = PartyModel::findById($id);
        if (!$partyModel) {
            return new JsonResponse(['success' => false, 'message' => 'Veranstaltung nicht gefunden.'], Response::HTTP_NOT_FOUND);
        }
        
        $acceptedResponses = PartyResponseModel::findAcceptedByParty($id);
        $accepted = [];
        
        if ($acceptedResponses) {
            foreach ($acceptedResponses as $response) {
                $member = MemberModel::findById($response->member_id);
                if ($member) {
                    $accepted[] = [
                        'id' => $member->id,
                        'name' => $member->firstname . ' ' . $member->lastname
                    ];
                }
            }
        }
        
        $declinedResponses = PartyResponseModel::findDeclinedByParty($id);
        $declined = [];
        
        if ($declinedResponses) {
            foreach ($declinedResponses as $response) {
                $member = MemberModel::findById($response->member_id);
                if ($member) {
                    $declined[] = [
                        'id' => $member->id,
                        'name' => $member->firstname . ' ' . $member->lastname
                    ];
                }
            }
        }
        
        $pending = [];
        
        if ($partyModel->inviteOnly) {
            $invitedUsers = @unserialize($partyModel->row()['invitedUsers']);
            
            foreach ($invitedUsers as $memberId) {
                $responseModel = PartyResponseModel::findByPartyAndMember($id, (int)$memberId);
                
                if (!$responseModel) {
                    $member = MemberModel::findById($memberId);
                    if ($member) {
                        $pending[] = [
                            'id' => $member->id,
                            'name' => $member->firstname . ' ' . $member->lastname
                        ];
                    }
                }
            }
        }

        return new JsonResponse([
            'success' => true,
            'accepted' => $accepted,
            'declined' => $declined,
            'pending' => $pending
        ]);
    }

    /**
     * @throws Exception
     */
    private function generateIcs($party): string
    {
        $start = (new \DateTime())->setTimestamp((int) $party->date);
        $end = (clone $start)->modify('+4 hours');

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
