<?php

namespace App\Entity;

use App\Repository\SeriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: SeriesRepository::class)]
#[Vich\Uploadable]

class Series
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $isOneShot = null; // true pour One-Shot, false pour Série

  
    #[ORM\Column(length: 600, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $length = null; // Longueur de la série (nombre de tomes)

    /**
     * @var Collection<int, Book>
     */
    #[ORM\OneToMany(mappedBy: 'series', targetEntity: Book::class)]
    private Collection $books;

    #[Vich\UploadableField(mapping: 'series_images', fileNameProperty: 'image')]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Collections>
     */
    #[ORM\ManyToMany(targetEntity: Collections::class, mappedBy: 'seriesList')]

    private Collection $collectionsList;

    public function __construct()
    {
        $this->books = new ArrayCollection();
        $this->collectionsList = new ArrayCollection();
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

    public function isOneShot(): ?bool
    {
        return $this->isOneShot;
    }

    public function setIsOneShot(bool $isOneShot): static
    {
        $this->isOneShot = $isOneShot;

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

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(?int $length): static
    {
        $this->length = $length;

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): static
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->setSeries($this);
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            // Unset the owning side
            if ($book->getSeries() === $this) {
                $book->setSeries(null);
            }
        }

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

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

    public function __toString(): string
    {
        // Retourne une représentation textuelle de l'objet
        return $this->name ?? 'Unnamed Character';
    }

    /**
     * @return Collection<int, Collections>
     */ public function getCollectionsList(): Collection
    {
        return $this->collectionsList;
    }

    public function addToCollectionsList(Collections $collection): static
    {
        if (!$this->collectionsList->contains($collection)) {
            $this->collectionsList->add($collection);
        }

        return $this;
    }

    public function removeFromCollectionsList(Collections $collection): static
    {
        $this->collectionsList->removeElement($collection);

        return $this;
    }

}
