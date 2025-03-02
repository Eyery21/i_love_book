<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[Vich\Uploadable]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 255)]
    private ?string $title = null;
    
    #[ORM\Column(length: 255, nullable:true)]
    private ?string $subtitle = null;

    #[ORM\Column(length: 600, nullable:true)]
    private ?string $description = null;

    #[Vich\UploadableField(mapping: 'book_images', fileNameProperty: 'image')]
    private ?File $imageFile = null;

    #[ORM\Column(length:255, nullable: true)]
    private ?string $parution = null;

    #[ORM\Column(nullable: true)]
    private ?bool $posseded = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $author = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $illustrator = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    #[ORM\Column(nullable: true)]
    private ?int $volumeNumber = null;

    #[ORM\Column(nullable: true)]
    private ?int $pageCount = null;

    /**
     * @var Collection<int, Character>
     */
    #[ORM\ManyToMany(targetEntity: Character::class, inversedBy: 'books')]
    private Collection $characters;

    #[ORM\ManyToOne(targetEntity: Series::class, inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Series $series = null;

   

    #[ORM\ManyToOne(targetEntity: Collections::class, inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: true, onDelete:"SET NULL")]
    private ?Collections $collection = null;

    public function __construct()
    {
        $this->characters = new ArrayCollection();
    }

    // Getters et setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): static
    {
        $this->imageFile = $imageFile;

        if ($imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getParution(): ?string
    {
        return $this->parution;
    }

    public function setParution(string $parution): static
    {
        $this->parution = $parution;

        return $this;
    }

    public function isPosseded(): ?bool
    {
        return $this->posseded;
    }

    public function setPosseded(?bool $posseded): static
    {
        $this->posseded = $posseded;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

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

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getIllustrator(): ?string
    {
        return $this->illustrator;
    }

    public function setIllustrator(?string $illustrator): static
    {
        $this->illustrator = $illustrator;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getVolumeNumber(): ?int
    {
        return $this->volumeNumber;
    }

    public function setVolumeNumber(?int $volumeNumber): static
    {
        $this->volumeNumber = $volumeNumber;

        return $this;
    }

    public function getPageCount(): ?int
    {
        return $this->pageCount;
    }

    public function setPageCount(?int $pageCount): static
    {
        $this->pageCount = $pageCount;

        return $this;
    }


    /**
     * @return Collection<int, Character>
     */
    public function getCharacters(): Collection
    {
        return $this->characters;
    }

    public function addCharacter(Character $character): static
    {
        if (!$this->characters->contains($character)) {
            $this->characters->add($character);
            $character->addApparition($this);
        }

        return $this;
    }

    public function removeCharacter(Character $character): static
    {
        if ($this->characters->removeElement($character)) {
            $character->removeApparition($this);
        }

        return $this;
    }

    public function getSeries(): ?Series
    {
        return $this->series;
    }

    public function setSeries(?Series $series): static
    {
        $this->series = $series;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(string $subtitle): static
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getCollection(): ?Collections
    {
        return $this->collection;
    }

    public function setCollection(?Collections $Collection): static
    {
        $this->collection = $Collection;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getDisplayName();
    }
    public function getDisplayName(): string
    {
        return $this->title . ($this->subtitle ? ' - ' . $this->subtitle : '');
    }

}
