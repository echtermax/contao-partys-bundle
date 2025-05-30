<?php

namespace Echtermax\PartyBundle\Service;

use Contao\Config;
use ErrorException;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class WebPushService
{
    private WebPush $webPush;

    /**
     * @throws ErrorException
     */
    public function __construct()
    {
        $this->webPush = new WebPush([
            'VAPID' => [
                'subject' => $_ENV['VAPID_SUBJECT'] ?? 'mailto:' . Config::get('adminEmail'),
                'publicKey' => $_ENV['VAPID_PUBLIC_KEY'],
                'privateKey' => $_ENV['VAPID_PRIVATE_KEY'],
            ],
        ]);
    }

    /**
     * @throws ErrorException
     */
    public function sendNotification(string $endpoint, string $p256dh, string $auth, string $payload): void
    {
        $subscription = Subscription::create([
            'endpoint' => $endpoint,
            'keys' => [
                'p256dh' => $p256dh,
                'auth' => $auth,
            ],
        ]);

        $this->webPush->sendOneNotification($subscription, $payload);
    }
}