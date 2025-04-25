<?php

namespace App\Entity;

use App\Repository\HotelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: HotelRepository::class)]
class Hotel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['hotel_list', 'hotel_details'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['hotel_list', 'hotel_details'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['hotel_list', 'hotel_details'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['hotel_list', 'hotel_details'])]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    #[Groups(['hotel_list', 'hotel_details'])]
    private ?string $city = null;

    #[ORM\Column]
    #[Groups(['hotel_list', 'hotel_details'])]
    private ?int $zipCode = null;

    #[ORM\Column(length: 255)]
    #[Groups(['hotel_list', 'hotel_details'])]
    private ?string $country = null;

    #[ORM\ManyToOne(inversedBy: 'hotels')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['hotel_details'])]
    private ?User $ownerId = null;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'hotel', orphanRemoval: true)]
    #[Groups(['hotel_list', 'hotel_details'])]
    private Collection $images;

    /**
     * @var Collection<int, Room>
     */
    #[ORM\OneToMany(targetEntity: Room::class, mappedBy: 'hotelId', orphanRemoval: true)]
    private Collection $rooms;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->rooms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getZipCode(): ?int
    {
        return $this->zipCode;
    }

    public function setZipCode(int $zipCode): static
    {
        $this->zipCode = $zipCode;

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

    public function getOwnerId(): ?User
    {
        return $this->ownerId;
    }

    public function setOwnerId(?User $ownerId): static
    {
        $this->ownerId = $ownerId;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setHotel($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getHotel() === $this) {
                $image->setHotel(null);
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
            $room->setHotelId($this);
        }

        return $this;
    }

    public function removeRoom(Room $room): static
    {
        if ($this->rooms->removeElement($room)) {
            // set the owning side to null (unless already changed)
            if ($room->getHotelId() === $this) {
                $room->setHotelId(null);
            }
        }

        return $this;
    }
}
