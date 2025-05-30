<?php

namespace Echtermax\PartyBundle\Model;

use Contao\Model;
use Echtermax\PartyBundle\Service\WebPushService;

class PushNotificationsModel extends Model
{
    protected static $strTable = 'tl_push_subscription';

    public function sendNotificationToUsersByMemberIds(array $userIds, string $title, string $message): void
    {
        $receiver = static::getAllReceiverByUserIds($userIds);
        if ($receiver->count() < 1) return;

        $webPushService = new WebPushService();
        foreach ($receiver as $subscription) {
            try {
                $webPushService->sendNotification(
                    $subscription->endpoint,
                    $subscription->p256dh,
                    $subscription->auth,
                    json_encode(['title' => $title, 'body' => $message])
                );
            } catch (\Exception $e) {
                error_log('Failed to send push notification: ' . $e->getMessage());
            }
        }
    }

    public function sendNotificationToAllUsers(string $title, string $message): void
    {
        $receiver = static::findAll();
        if ($receiver->count() < 1) return;

        $webPushService = new WebPushService();
        foreach ($receiver as $subscription) {
            try {
                $webPushService->sendNotification(
                    $subscription->endpoint,
                    $subscription->p256dh,
                    $subscription->auth,
                    json_encode(['title' => $title, 'body' => $message])
                );
            } catch (\Exception $e) {
                error_log('Failed to send push notification: ' . $e->getMessage());
            }
        }
    }

    private function getAllReceiverByUserIds(array $userIds): Model|Model\Collection|PushNotificationsModel
    {
        $t = static::$strTable;
        $arrColumns = [
            "$t.userId IN(" . implode(',', array_map('\intval', $userIds)) . ")"
        ];
        return static::findBy($arrColumns, null);
    }
}