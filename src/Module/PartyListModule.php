<?php

namespace Echtermax\PartyBundle\Module;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Date;
use Contao\FrontendUser;
use Contao\MemberModel;
use Contao\ModuleModel;
use Contao\System;
use Echtermax\PartyBundle\Model\PartyModel;
use Echtermax\PartyBundle\Model\PartyResponseModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class PartyListModule extends AbstractFrontendModuleController
{
    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        $user = FrontendUser::getInstance();
        $userId = $user ? $user->id : 0;

        $parties = PartyModel::findPublishedPartiesForMember($userId);
        $arrParties = [];

        if (null !== $parties) {
            foreach ($parties as $partyObj) {
                $party = $partyObj->row();

                $party['startDate'] = Date::parse($GLOBALS['TL_CONFIG']['dateFormat'], $party['startDate']);
                if ($party['startTime']) $party['startTime'] = $this->format_time($party['startTime']);
                if ($party['endDate']) $party['endDate'] = Date::parse($GLOBALS['TL_CONFIG']['dateFormat'], $party['endDate']);

                $userResponse = PartyResponseModel::findByPartyAndMember($party['id'], $userId);
                if ($userResponse) $party['userResponse'] = $userResponse->row()['response'];

                $arrParties[] = $party;
            }
        }

        $template->parties = $arrParties;
        $template->users = MemberModel::findAll();
        $template->feuser = $user;
        $template->public_key = $_ENV['VAPID_PUBLIC_KEY'];

        $csrfToken = $this->csrfTokenManager->getToken('create_party')->getValue();

        $template->csrfToken = $csrfToken;
        $template->moduleId = $model->id;
        $template->moduleUrl = $this->getPageModel()->getFrontendUrl();
        return $template->getResponse();
    }

    private function format_time($input): string
    {
        if ($input < 86400) {
            return gmdate("H:i", $input);
        } elseif ($input > 946684800) {
            return date("H:i", $input);
        } else {
            return "Unknown time format";
        }
    }
}