<?php

namespace App\Entity;

use App\Repository\UserBadgeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserBadgeRepository::class)]
class UserBadge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $awardedDate = null;

    #[ORM\ManyToOne(inversedBy: 'badgeId')]
    private ?User $userId = null;

    #[ORM\ManyToOne(inversedBy: 'userBadges')]
    private ?Badge $badgeId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAwardedDate(): ?\DateTimeInterface
    {
        return $this->awardedDate;
    }

    public function setAwardedDate(\DateTimeInterface $awardedDate): static
    {
        $this->awardedDate = $awardedDate;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getBadgeId(): ?Badge
    {
        return $this->badgeId;
    }

    public function setBadgeId(?Badge $badgeId): static
    {
        $this->badgeId = $badgeId;

        return $this;
    }
}
