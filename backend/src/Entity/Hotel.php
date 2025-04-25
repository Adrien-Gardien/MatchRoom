<?php

namespace App\Entity;

use App\Repository\HotelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: HotelRepository::class)]
class Hotel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['hotel:read', 'room:read', 'favorite:read', 'user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['hotel:read', 'room:read', 'favorite:read', 'user:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['hotel:read', 'room:read', 'favorite:read'])]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    #[Groups(['hotel:read', 'room:read', 'favorite:read'])]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    #[Groups(['hotel:read', 'room:read', 'favorite:read'])]
    private ?string $country = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['hotel:read'])]
    private ?string $description = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'hotel')]
    #[Groups(['hotel:read'])]  // Ne pas inclure dans user:read pour éviter circularité
    private Collection $owners;

    /**
     * @var Collection<int, Room>
     */
    #[ORM\OneToMany(targetEntity: Room::class, mappedBy: 'hotel')]
    #[Groups(['hotel:read'])]  // Ne pas inclure dans room:read pour éviter circularité
    private Collection $rooms;

    /**
     * @var Collection<int, Favorite>
     */
    #[ORM\OneToMany(targetEntity: Favorite::class, mappedBy: 'hotelId')]
    #[Groups(['hotel:read'])]  // Ne pas inclure dans favorite:read pour éviter circularité
    private Collection $favorites;

    #[ORM\Column(length: 255)]
    #[Groups(['hotel:read', 'room:read', 'favorite:read'])]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true) ]
    private ?string $ambiance = null;

    public function __construct()
    {
        $this->owners = new ArrayCollection();
        $this->rooms = new ArrayCollection();
        $this->favorites = new ArrayCollection();
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

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

    /**
     * @return Collection<int, User>
     */
    public function getOwners(): Collection
    {
        return $this->owners;
    }

    public function addOwner(User $owner): static
    {
        if (!$this->owners->contains($owner)) {
            $this->owners->add($owner);
            $owner->setHotel($this);
        }

        return $this;
    }

    public function removeOwner(User $owner): static
    {
        if ($this->owners->removeElement($owner)) {
            // set the owning side to null (unless already changed)
            if ($owner->getHotel() === $this) {
                $owner->setHotel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getRooms(): Collection
    {
        return $this->rooms;
    }

    public function addRoom(Room $room): static
    {
        if (!$this->rooms->contains($room)) {
            $this->rooms->add($room);
            $room->setHotel($this);
        }

        return $this;
    }

    public function removeRoom(Room $room): static
    {
        if ($this->rooms->removeElement($room)) {
            // set the owning side to null (unless already changed)
            if ($room->getHotel() === $this) {
                $room->setHotel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Favorite>
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorite $favorite): static
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites->add($favorite);
            $favorite->setHotelId($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): static
    {
        if ($this->favorites->removeElement($favorite)) {
            // set the owning side to null (unless already changed)
            if ($favorite->getHotelId() === $this) {
                $favorite->setHotelId(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getAmbiance(): ?string
    {
        return $this->ambiance;
    }

    public function setAmbiance(string $ambiance): static
    {
        $this->ambiance = $ambiance;

        return $this;
    }
}
