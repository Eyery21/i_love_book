<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserBook;
use App\Entity\Book;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/profile')]
class ProfilController extends AbstractController
{
    #[Route('/', name:'app_profile_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        //recupere les books likes
        $favoriteBooks = $entityManager->createQueryBuilder()
            ->select('b')
            ->from(Book::class, 'b')
            ->join(UserBook::class, 'ub', 'WITH', 'b.id = ub.book')
            ->where('ub.user = :user')
            ->andWhere('ub.rating >= 4')
            ->orderBy('ub.rating', 'DESC')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

            // recupere les books souhaitez
        $wishlistBooks = $entityManager->createQueryBuilder()
            ->select('b')
            ->from(Book::class, 'b')
            ->join(UserBook::class, 'ub', 'WITH', 'b.id = ub.book')
            ->where('ub.user = :user')
            ->andWhere('ub.isWishlist = true')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

            // rexupère les livre possedez
        $ownedBooks = $entityManager->createQueryBuilder()
            ->select('b')
            ->from(Book::class, 'b')
            ->join(UserBook::class, 'ub', 'WITH', 'b.id = ub.book')
            ->where('ub.user = :user')
            ->andWhere('ub.isOwned = true')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'favoriteBooks' => $favoriteBooks,
            'wishlistBooks' => $wishlistBooks,
            'ownedBooks' => $ownedBooks
        ]);


    }
    #[Route('/books/add/{id}', name: 'app_profile_add_book')]
    public function addBook(Book $book, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si l'utilisateur a déjà ajouté ce livre
        $userBook = $entityManager->getRepository(UserBook::class)->findOneBy([
            'user' => $user,
            'book' => $book
        ]);

        if (!$userBook) {
            $userBook = new UserBook();
            $userBook->setUser($user);
            $userBook->setBook($book);
            $userBook->setAddedAt(new \DateTime());
            $userBook->setIsOwned(false);
            $userBook->setIsWishlist(false);
            $userBook->setIsRead(false);
        }

        // Traiter les actions
        $action = $request->query->get('action');
        if ($action === 'own') {
            $userBook->setIsOwned(true);
        } elseif ($action === 'wishlist') {
            $userBook->setIsWishlist(true);
        } elseif ($action === 'read') {
            $userBook->setIsRead(true);
        } elseif ($action === 'rate') {
            $rating = $request->query->getInt('rating', 0);
            if ($rating >= 1 && $rating <= 5) {
                $userBook->setRating($rating);
            }
        }

        $entityManager->persist($userBook);
        $entityManager->flush();

        return $this->redirectToRoute('app_book_show', ['id' => $book->getId(), 'slug' => $book->getTitle()]);
    }
}