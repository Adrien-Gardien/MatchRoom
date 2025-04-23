<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * @phpstan-ignore-next-line
     */
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\ManyToOne(inversedBy: 'owners')]
    private ?Hotel $hotel = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'userId')]
    private Collection $bookings;

    /**
     * @var Collection<int, Offer>
     */
    #[ORM\OneToMany(targetEntity: Offer::class, mappedBy: 'userId')]
    private Collection $offers;

    /**
     * @var Collection<int, Rating>
     */
    #[ORM\OneToMany(targetEntity: Rating::class, mappedBy: 'authorId')]
    private Collection $ratings;

    /**
     * @var Collection<int, Matching>
     */
    #[ORM\OneToMany(targetEntity: Matching::class, mappedBy: 'userId')]
    private Collection $matchings;

    /**
     * @var Collection<int, UserPreference>
     */
    #[ORM\OneToMany(targetEntity: UserPreference::class, mappedBy: 'userId')]
    private Collection $userPreferences;

    /**
     * @var Collection<int, Favorite>
     */
    #[ORM\OneToMany(targetEntity: Favorite::class, mappedBy: 'userId')]
    private Collection $favorites;

    /**
     * @var Collection<int, Search>
     */
    #[ORM\OneToMany(targetEntity: Search::class, mappedBy: 'userId')]
    private Collection $searches;

    /**
     * @var Collection<int, UserBadge>
     */
    #[ORM\OneToMany(targetEntity: UserBadge::class, mappedBy: 'userId')]
    private Collection $userBadges;

    #[ORM\Column]
    private bool $isVerified = false;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->offers = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->matchings = new ArrayCollection();
        $this->userPreferences = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->searches = new ArrayCollection();
        $this->badgeId = new ArrayCollection();
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getHotel(): ?Hotel
    {
        return $this->hotel;
    }

    public function setHotel(?Hotel $hotel): static
    {
        $this->hotel = $hotel;

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
     * @return Collection<int, Offer>
     */
    public function getOffers(): Collection
    {
        return $this->offers;
    }

    public function addOffer(Offer $offer): static
    {
        if (!$this->offers->contains($offer)) {
            $this->offers->add($offer);
            $offer->setUserId($this);
        }

        return $this;
    }

    public function removeOffer(Offer $offer): static
    {
        if ($this->offers->removeElement($offer)) {
            // set the owning side to null (unless already changed)
            if ($offer->getUserId() === $this) {
                $offer->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Rating>
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): static
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
            $rating->setAuthorId($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): static
    {
        if ($this->ratings->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getAuthorId() === $this) {
                $rating->setAuthorId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Matching>
     */
    public function getMatchings(): Collection
    {
        return $this->matchings;
    }

    public function addMatching(Matching $matching): static
    {
        if (!$this->matchings->contains($matching)) {
            $this->matchings->add($matching);
            $matching->setUserId($this);
        }

        return $this;
    }

    public function removeMatching(Matching $matching): static
    {
        if ($this->matchings->removeElement($matching)) {
            // set the owning side to null (unless already changed)
            if ($matching->getUserId() === $this) {
                $matching->setUserId(null);
            }
        }

        return $this;
    }

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
            $userPreference->setUserId($this);
        }

        return $this;
    }

    public function removeUserPreference(UserPreference $userPreference): static
    {
        if ($this->userPreferences->removeElement($userPreference)) {
            // set the owning side to null (unless already changed)
            if ($userPreference->getUserId() === $this) {
                $userPreference->setUserId(null);
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
            $favorite->setUserId($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): static
    {
        if ($this->favorites->removeElement($favorite)) {
            // set the owning side to null (unless already changed)
            if ($favorite->getUserId() === $this) {
                $favorite->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Search>
     */
    public function getSearches(): Collection
    {
        return $this->searches;
    }

    public function addSearch(Search $search): static
    {
        if (!$this->searches->contains($search)) {
            $this->searches->add($search);
            $search->setUserId($this);
        }

        return $this;
    }

    public function removeSearch(Search $search): static
    {
        if ($this->searches->removeElement($search)) {
            // set the owning side to null (unless already changed)
            if ($search->getUserId() === $this) {
                $search->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserBadge>
     */
    public function getUserBadges(): Collection
    {
        return $this->userBadges;
    }

    public function addUserBadges(UserBadge $userBadge): static
    {
        if (!$this->userBadges->contains($userBadge)) {
            $this->userBadges->add($userBadge);
            $userBadge->setUserId($this);
        }

        return $this;
    }

    public function removeUserBadges(UserBadge $userBadge): static
    {
        if ($this->userBadges->removeElement($userBadge)) {
            // set the owning side to null (unless already changed)
            if ($userBadge->getUserId() === $this) {
                $userBadge->setUserId(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
