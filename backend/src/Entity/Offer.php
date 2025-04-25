<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

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
    private ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $offerDate = null;

    #[ORM\ManyToOne(inversedBy: 'offers')]
    private ?User $userId = null;

    #[ORM\ManyToOne(inversedBy: 'offers')]
    private ?Room $roomId = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'offers')]
    private ?self $parentOffer = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parentOffer')]
    private Collection $offers;

    public function __construct()
    {
        $this->offers = new ArrayCollection();
    }

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
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

    public function getParentOffer(): ?self
    {
        return $this->parentOffer;
    }

    public function setParentOffer(?self $parentOffer): static
    {
        $this->parentOffer = $parentOffer;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getOffers(): Collection
    {
        return $this->offers;
    }

    public function addOffer(self $offer): static
    {
        if (!$this->offers->contains($offer)) {
            $this->offers->add($offer);
            $offer->setParentOffer($this);
        }

        return $this;
    }

    public function removeOffer(self $offer): static
    {
        if ($this->offers->removeElement($offer)) {
            // set the owning side to null (unless already changed)
            if ($offer->getParentOffer() === $this) {
                $offer->setParentOffer(null);
            }
        }

        return $this;
    }

    public function isCounterOffer(): bool
    {
        return $this->parentOffer !== null;
    }

}
