<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['hotel_details', 'user_list', 'user_details'])]
    /**
     * @phpstan-ignore-next-line
     */
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['hotel_details', 'user_list', 'user_details'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['user_details'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'userId', orphanRemoval: true)]
    #[Groups(['user_details'])]
    private Collection $bookings;

    /**
     * @var Collection<int, Hotel>
     */
    #[ORM\OneToMany(targetEntity: Hotel::class, mappedBy: 'ownerId')]
    #[Groups(['user_details'])]
    private Collection $hotels;

    #[ORM\Column(length: 255)]
    #[Groups(['hotel_details', 'user_list', 'user_details'])]
    private ?string $name = null;

    /**
     * @var Collection<int, Badge>
     */
    #[ORM\ManyToMany(targetEntity: Badge::class, mappedBy: 'userId')]
    #[Groups(['user_details'])]
    private Collection $badges;

    /**
     * @var Collection<int, Trophy>
     */
    #[ORM\ManyToMany(targetEntity: Trophy::class, mappedBy: 'userId')]
    #[Groups(['user_details'])]
    private Collection $trophies;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->hotels = new ArrayCollection();
        $this->badges = new ArrayCollection();
        $this->trophies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
            $booking->setUserId($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getUserId() === $this) {
                $booking->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Hotel>
     */
    public function getHotels(): Collection
    {
        return $this->hotels;
    }

    public function addHotel(Hotel $hotel): static
    {
        if (!$this->hotels->contains($hotel)) {
            $this->hotels->add($hotel);
            $hotel->setOwnerId($this);
        }

        return $this;
    }

    public function removeHotel(Hotel $hotel): static
    {
        if ($this->hotels->removeElement($hotel)) {
            // set the owning side to null (unless already changed)
            if ($hotel->getOwnerId() === $this) {
                $hotel->setOwnerId(null);
            }
        }

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

    /**
     * @return Collection<int, Badge>
     */
    public function getBadges(): Collection
    {
        return $this->badges;
    }

    public function addBadge(Badge $badge): static
    {
        if (!$this->badges->contains($badge)) {
            $this->badges->add($badge);
            $badge->addUserId($this);
        }

        return $this;
    }

    public function removeBadge(Badge $badge): static
    {
        if ($this->badges->removeElement($badge)) {
            $badge->removeUserId($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Trophy>
     */
    public function getTrophies(): Collection
    {
        return $this->trophies;
    }

    public function addTrophy(Trophy $trophy): static
    {
        if (!$this->trophies->contains($trophy)) {
            $this->trophies->add($trophy);
            $trophy->addUserId($this);
        }

        return $this;
    }

    public function removeTrophy(Trophy $trophy): static
    {
        if ($this->trophies->removeElement($trophy)) {
            $trophy->removeUserId($this);
        }

        return $this;
    }
}
