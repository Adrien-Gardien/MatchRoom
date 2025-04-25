<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['room_details'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['room_details'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['room_details'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['room_details'])]
    private ?int $capacity = null;

    #[ORM\Column]
    #[Groups(['room_details'])]
    private ?int $price = null;

    #[ORM\Column]
    #[Groups(['room_details'])]
    private ?bool $available = null;

    #[ORM\Column]
    #[Groups(['room_details'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['room_details'])]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'roomId', orphanRemoval: true)]
    private Collection $bookings;

    #[ORM\ManyToOne(inversedBy: 'rooms')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Hotel $hotelId = null;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'room')]
    private Collection $images;

    /**
     * @var Collection<int, Negotiation>
     */
    #[ORM\OneToMany(targetEntity: Negotiation::class, mappedBy: 'roomId', orphanRemoval: true)]
    private Collection $negotiations;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->negotiations = new ArrayCollection();
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

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): static
    {
        $this->available = $available;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setRoomId($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getRoomId() === $this) {
                $booking->setRoomId(null);
            }
        }

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
            $image->setRoom($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getRoom() === $this) {
                $image->setRoom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Negotiation>
     */
    public function getNegotiations(): Collection
    {
        return $this->negotiations;
    }

    public function addNegotiation(Negotiation $negotiation): static
    {
        if (!$this->negotiations->contains($negotiation)) {
            $this->negotiations->add($negotiation);
            $negotiation->setRoomId($this);
        }

        return $this;
    }

    public function removeNegotiation(Negotiation $negotiation): static
    {
        if ($this->negotiations->removeElement($negotiation)) {
            // set the owning side to null (unless already changed)
            if ($negotiation->getRoomId() === $this) {
                $negotiation->setRoomId(null);
            }
        }

        return $this;
    }
}
