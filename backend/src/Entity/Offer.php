<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\OfferType;
use App\Enum\OfferStatus;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
class Offer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $proposedPrice = null;

    #[ORM\Column(length: 255)]
    private ?OfferStatus $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $offerDate = null;

    #[ORM\ManyToOne(inversedBy: 'offers')]
    private ?User $userId = null;

    #[ORM\ManyToOne(inversedBy: 'offers')]
    private ?Room $roomId = null;

    #[ORM\Column(length: 255)]
    private ?OfferType $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProposedPrice(): ?string
    {
        return $this->proposedPrice;
    }

    public function setProposedPrice(string $proposedPrice): static
    {
        $this->proposedPrice = $proposedPrice;

        return $this;
    }

    public function getStatus(): ?OfferStatus
    {
        return $this->status;
    }

    public function setStatus(OfferStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getOfferDate(): ?\DateTimeInterface
    {
        return $this->offerDate;
    }

    public function setOfferDate(\DateTimeInterface $offerDate): static
    {
        $this->offerDate = $offerDate;

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

    public function getRoomId(): ?Room
    {
        return $this->roomId;
    }

    public function setRoomId(?Room $roomId): static
    {
        $this->roomId = $roomId;

        return $this;
    }

    public function getType(): ?OfferType
    {
        return $this->type;
    }

    public function setType(OfferType $type): static
    {
        $this->type = $type;

        return $this;
    }
}
