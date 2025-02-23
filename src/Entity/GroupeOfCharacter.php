<?php

namespace App\Entity;

use App\Repository\GroupeOfCharacterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupeOfCharacterRepository::class)]
class GroupeOfCharacter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $fonded_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $alignment = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $headquarters = null;

    /**
     * @var Collection<int, Character>
     */
    #[ORM\ManyToMany(targetEntity: Character::class, inversedBy: 'groupeOfCharacters')]
    private Collection $members;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'groupeOfCharacters')]
    private Collection $rival_groups;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'rival_groups')]
    private Collection $groupeOfCharacters;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated_at = null;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable(); 
        $this->updated_at = new \DateTime();
        $this->members = new ArrayCollection();
        $this->rival_groups = new ArrayCollection();
        $this->groupeOfCharacters = new ArrayCollection();
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

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getFondedAt(): ?\DateTimeImmutable
    {
        return $this->fonded_at;
    }

    public function setFondedAt(?\DateTimeImmutable $fonded_at): static
    {
        $this->fonded_at = $fonded_at;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getAlignment(): ?string
    {
        return $this->alignment;
    }

    public function setAlignment(?string $alignment): static
    {
        $this->alignment = $alignment;

        return $this;
    }

    public function getHeadquarters(): ?string
    {
        return $this->headquarters;
    }

    public function setHeadquarters(?string $headquarters): static
    {
        $this->headquarters = $headquarters;

        return $this;
    }

    /**
     * @return Collection<int, Character>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(Character $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
        }

        return $this;
    }

    public function removeMember(Character $member): static
    {
        $this->members->removeElement($member);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getRivalGroups(): Collection
    {
        return $this->rival_groups;
    }

    public function addRivalGroup(self $rivalGroup): static
    {
        if (!$this->rival_groups->contains($rivalGroup)) {
            $this->rival_groups->add($rivalGroup);
        }

        return $this;
    }

    public function removeRivalGroup(self $rivalGroup): static
    {
        $this->rival_groups->removeElement($rivalGroup);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getGroupeOfCharacters(): Collection
    {
        return $this->groupeOfCharacters;
    }

    public function addGroupeOfCharacter(self $groupeOfCharacter): static
    {
        if (!$this->groupeOfCharacters->contains($groupeOfCharacter)) {
            $this->groupeOfCharacters->add($groupeOfCharacter);
            $groupeOfCharacter->addRivalGroup($this);
        }

        return $this;
    }

    public function removeGroupeOfCharacter(self $groupeOfCharacter): static
    {
        if ($this->groupeOfCharacters->removeElement($groupeOfCharacter)) {
            $groupeOfCharacter->removeRivalGroup($this);
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

        public function __toString(): string
    {
        return $this->name ?? 'Unnamed Group';
    }
}
