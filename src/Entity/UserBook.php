<?php

namespace App\Entity;

use App\Repository\UserBookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserBookRepository::class)]
class UserBook
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userBooks')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userBooks')]
    private ?Book $book = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $addedAt = null;

    #[ORM\Column]
    private ?bool $isOwned = null;

    #[ORM\Column]
    private ?bool $isWishlist = null;

    #[ORM\Column]
    private ?bool $isRead = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $rating = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $personnalNotes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;

        return $this;
    }

    public function getAddedAt(): ?\DateTimeInterface
    {
        return $this->addedAt;
    }

    public function setAddedAt(\DateTimeInterface $addedAt): static
    {
        $this->addedAt = $addedAt;

        return $this;
    }

    public function isOwned(): ?bool
    {
        return $this->isOwned;
    }

    public function setIsOwned(bool $isOwned): static
    {
        $this->isOwned = $isOwned;

        return $this;
    }

    public function isWishlist(): ?bool
    {
        return $this->isWishlist;
    }

    public function setIsWishlist(bool $isWishlist): static
    {
        $this->isWishlist = $isWishlist;

        return $this;
    }

    public function isRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): static
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getPersonnalNotes(): ?string
    {
        return $this->personnalNotes;
    }

    public function setPersonnalNotes(?string $personnalNotes): static
    {
        $this->personnalNotes = $personnalNotes;

        return $this;
    }
}
