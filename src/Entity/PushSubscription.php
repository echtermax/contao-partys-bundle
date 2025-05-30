<?php

namespace Echtermax\PartyBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tl_push_subscription')]
class PushSubscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "text", unique: true)]
    private string $endpoint;

    #[ORM\Column(type: "text")]
    private string $p256dh;

    #[ORM\Column(type: "text")]
    private string $auth;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $userId = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $deviceId = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $userAgent = null;

    public function getId(): int { return $this->id; }

    public function getEndpoint(): string { return $this->endpoint; }

    public function setEndpoint(string $endpoint): void { $this->endpoint = $endpoint; }

    public function getP256dh(): string { return $this->p256dh; }

    public function setP256dh(string $p256dh): void { $this->p256dh = $p256dh; }

    public function getAuth(): string { return $this->auth; }

    public function setAuth(string $auth): void { $this->auth = $auth; }

    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }

    public function setCreatedAt(\DateTimeInterface $createdAt): void { $this->createdAt = $createdAt; }

    public function getUserId(): ?int { return $this->userId; }

    public function setUserId(?int $userId): void { $this->userId = $userId; }
}