<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

//    /**
//     * @var Collection<int, Room>
//     */
//    #[ORM\ManyToMany(targetEntity: Room::class, mappedBy: 'service')]
//    private Collection $rooms;

    /**
     * @var Collection<int, UserPreference>
     */
    #[ORM\ManyToMany(targetEntity: UserPreference::class, mappedBy: 'services')]
    private Collection $userPreferences;

    public function __construct()
    {
        $this->rooms = new ArrayCollection();
        $this->userPreferences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

//    /**
//     * @return Collection<int, Room>
//     */
//    public function getRooms(): Collection
//    {
//        return $this->rooms;
//    }
//
//    public function addRoom(Room $room): static
//    {
//        if (!$this->rooms->contains($room)) {
//            $this->rooms->add($room);
//            $room->addService($this);
//        }
//
//        return $this;
//    }
//
//    public function removeRoom(Room $room): static
//    {
//        if ($this->rooms->removeElement($room)) {
//            $room->removeService($this);
//        }
//
//        return $this;
//    }

    /**
     * @return Collection<int, UserPreference>
     */
    public function getUserPreferences(): Collection
    {
        return $this->userPreferences;
    }

    public function addUserPreference(UserPreference $userPreference): static
    {
        if (!$this->userPreferences->contains($userPreference)) {
            $this->userPreferences->add($userPreference);
            $userPreference->addService($this);
        }

        return $this;
    }

    public function removeUserPreference(UserPreference $userPreference): static
    {
        if ($this->userPreferences->removeElement($userPreference)) {
            $userPreference->removeService($this);
        }

        return $this;
    }
}
