<?php

namespace App\Entity;

use App\Repository\FavoriteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavoriteRepository::class)]
class Favorite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $addedDate = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    private ?User $userId = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    private ?Hotel $hotelId = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    private ?Room $roomId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddedDate(): ?\DateTimeInterface
    {
        return $this->addedDate;
    }

    public function setAddedDate(\DateTimeInterface $addedDate): static
    {
        $this->addedDate = $addedDate;

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

    public function getHotelId(): ?Hotel
    {
        return $this->hotelId;
    }

    public function setHotelId(?Hotel $hotelId): static
    {
        $this->hotelId = $hotelId;

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
}
