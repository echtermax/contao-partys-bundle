<?php

namespace Echtermax\PartyBundle\Module;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Date;
use Contao\FrontendUser;
use Contao\ModuleModel;
use Echtermax\PartyBundle\Model\PartyModel;
use Echtermax\PartyBundle\Model\PartyResponseModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PartyListModule extends AbstractFrontendModuleController
{

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
                if ($party['startTime']) $party['startTime'] = Date::parse($GLOBALS['TL_CONFIG']['timeFormat'], $party['startDate']);
                if ($party['endDate']) $party['endDate'] = Date::parse($GLOBALS['TL_CONFIG']['dateFormat'], $party['endDate']);

                $userResponse = PartyResponseModel::findByPartyAndMember($party['id'], $userId);
                if ($userResponse) $party['userResponse'] = $userResponse->row()['response'];

                $arrParties[] = $party;
            }
        }

        $template->parties = $arrParties;

        $template->moduleId = $model->id;
        $template->moduleUrl = $this->getPageModel()->getFrontendUrl();
        return $template->getResponse();
    }
}