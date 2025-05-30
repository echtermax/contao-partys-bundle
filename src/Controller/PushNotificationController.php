<?php

namespace Echtermax\PartyBundle\Controller;

use Doctrine\DBAL\Exception;
use Echtermax\PartyBundle\Entity\PushSubscription;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


class PushNotificationController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/push-subscription/save', name: 'save_push_subscription', defaults: ['_scope' => 'frontend', '_token_check' => false], methods: ['POST'])]
    public function save(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();

        $subscription = new PushSubscription();
        $subscription->setEndpoint($data['endpoint']);
        $subscription->setP256dh($data['keys']['p256dh']);
        $subscription->setAuth($data['keys']['auth']);
        $subscription->setCreatedAt(new DateTimeImmutable());
        $subscription->setUserId($user->id);

        $em->persist($subscription);
        $em->flush();

        $conn = $em->getConnection();
        $conn->executeStatement(
            'UPDATE tl_push_subscription SET deviceId = ?, userAgent = ? WHERE id = ?',
            [$data['deviceId'], $data['userAgent'], $subscription->getId()]
        );


        return $this->json(['status' => 'ok']);
    }

    /**
     * @throws Exception
     */
    #[Route('/push-subscription/delete', name: 'delete_push_subscription', defaults: ['_scope' => 'frontend', '_token_check' => false], methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();

        $conn = $em->getConnection();
        $conn->executeStatement(
            'DELETE FROM tl_push_subscription WHERE deviceId = ? AND userId = ?',
            [$data['deviceId'], $user->id]
        );

        return $this->json(['status' => 'ok']);
    }
}