<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Character;
use App\Entity\Collections;


use App\Entity\Series;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

use App\Repository\UserRepository;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/book')]
final class BookController extends AbstractController
{

    private function getSlug(string $title, ?string $subtitle = null): string
    {
        // Combine le titre et le sous-titre (s'il existe)
        $baseString = $title;
        if ($subtitle) {
            $baseString .= ' ' . $subtitle;
        }

        // Remplace les caractères spéciaux par des espaces
        $slug = preg_replace(pattern: '/[^A-Za-z0-9-]+/', replacement: ' ', subject: $baseString);

        // Remplace les espaces par des tirets
        $slug = str_replace(search: ' ', replace: '-', subject: $slug);

        // Supprime les tirets en début et fin de chaîne
        $slug = trim(string: $slug, characters: '-');

        // Convertit en minuscules
        $slug = strtolower(string: $slug);

        return $slug;
    }
    #[Route( name: 'app_book_index', methods: ['GET'])]
    public function index(BookRepository $bookRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        
        $books = $bookRepository->findAll();
        $characters = $entityManager->getRepository(Character::class)->findAll();
        $series = $entityManager->getRepository(Series::class)->findAll();
        $collections = $entityManager->getRepository(Collections::class)->findAll(); // Ajout des collections

        $user = $this->getUser();
                return $this->render('book/index.html.twig', [
            'books' => $books,
            'user' => $user,
            'characters' => $characters,
            'series' => $series,
            'collections' => $collections, // Passez les collections à la vue

        ]);
    }

    #[Route('/new', name: 'app_book_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/{slug}', name: 'app_book_show', methods: ['GET'])]
    public function show(Book $book, string $slug, EntityManagerInterface $entityManager): Response
    


    {
        $correctSlug = $this->getSlug($book->getTitle(), $book->getSubtitle());
        if ($slug !== $correctSlug) {
            return $this->redirectToRoute('app_book_show', [
                'id' => $book->getId(),
                'slug' => $correctSlug
            ]);
        }
        $collection = $book->getCollection();
        $characters = $entityManager->getRepository(Character::class)->findAll();
        $series = $book->getSeries();

        $otherBooks = [];
        if ($series) {
            $otherBooks = $entityManager->getRepository(Book::class)->findBy(['series' => $series]);
        }
                return $this->render('book/show.html.twig', [
            'book' => $book,
            'characters' => $characters,
            'series' => $series,
            'otherBooks' => $otherBooks,
            'collection' => $collection,

        ]);
    }

    #[Route('/{id}/edit', name: 'app_book_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_book_delete', methods: ['POST'])]
    public function delete(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
    }



}
