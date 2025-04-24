<?php

namespace App\Entity;

use App\Repository\FavoriteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FavoriteRepository::class)]
class Favorite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['favorite:read', 'user:read', 'hotel:read', 'room:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['favorite:read', 'user:read', 'hotel:read', 'room:read'])]
    private ?\DateTimeInterface $addedDate = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    #[Groups(['favorite:read'])]  // Ne pas inclure dans user:read pour éviter circularité
    private ?User $userId = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    #[Groups(['favorite:read', 'user:read'])]  // Ne pas inclure dans hotel:read pour éviter circularité
    private ?Hotel $hotelId = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    #[Groups(['favorite:read', 'user:read'])]  // Ne pas inclure dans room:read pour éviter circularité
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
