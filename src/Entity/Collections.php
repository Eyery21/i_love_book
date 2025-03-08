<?php

namespace App\Entity;

use App\Repository\CollectionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CollectionsRepository::class)]
class Collections
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, Book>
     */
    #[ORM\OneToMany(targetEntity: Book::class, mappedBy: 'collection')]
    private Collection $books;

    /**
     * @var Collection<int, Series>
     */
    #[ORM\ManyToMany(targetEntity: Series::class, inversedBy: 'collectionsList')]
    #[ORM\JoinTable(name: 'collections_series')]
    private Collection $seriesList;



    public function __construct()
    {
        $this->books = new ArrayCollection();
        $this->seriesList = new ArrayCollection();
    }
    public function __toString(): string
    {
        return $this->getDisplayName();
    }
    public function getDisplayName(): string
    {
        return $this->name;
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
            $book->setCollection($this);
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getCollection() === $this) {
                $book->setCollection(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Series>
     */
    public function getSeriesList(): Collection
    {
        return $this->seriesList;
    }

    public function addSeriesList(Series $series): static
    {
        if (!$this->seriesList->contains($series)) {
            $this->seriesList->add($series);
            $series->addCollectionsList($this);
        }

        return $this;
    }

    public function removeSeriesList(Series $series): static
    {
        if ($this->seriesList->removeElement($series)) {
            $series->removeCollectionsList($this);
        }

        return $this;
    }
}
