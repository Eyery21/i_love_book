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
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;




#[Route('/profile')]
class ProfilController extends AbstractController
{
    #[Route('/', name: 'app_profile_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $userBooks = $entityManager->createQueryBuilder()
            ->select('ub')
            ->from(UserBook::class, 'ub')
            ->where('ub.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        //recupere les books lus
        $readBooks = $entityManager->createQueryBuilder()
            ->select('b')
            ->from(Book::class, 'b')
            ->join(UserBook::class, 'ub', 'WITH', 'b.id = ub.book')
            ->where('ub.user = :user')
            ->andWhere('ub.isRead = true')
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

        dump($readBooks);
        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'userBooks' => $userBooks,

            'readBooks' => $readBooks,
            'wishlistBooks' => $wishlistBooks,
            'ownedBooks' => $ownedBooks
        ]);
    }

    #[Route('/edit', name: 'app_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload d'avatar
            $avatarFile = $form->get('avatarFile')->getData();

            if ($avatarFile) {
                $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $avatarFile->guessExtension();

                try {
                    $avatarFile->move(
                        $this->getParameter('avatars_directory'),
                        $newFilename
                    );

                    // Met à jour le chemin de l'avatar
                    $user->setAvatar('uploads/avatars/' . $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'avatar');
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Votre profil a été mis à jour avec succès');
            return $this->redirectToRoute('app_profile_index');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

  
    #[Route('/books/{id}/own', name: 'app_profile_book_own')]
    public function ownBook(Book $book, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $userBook = $this->getUserBook($book, $entityManager);
        $userBook->setIsOwned(true);

        $entityManager->persist($userBook);
        $entityManager->flush();

        $this->addFlash('success', 'Le livre a été ajouté à votre collection');

        return $this->getRedirectResponse($request, 'app_book_show', ['id' => $book->getId()]);
    }
    #[Route('/books/{id}/unown', name: 'app_profile_book_unown')]
    public function unownBook(Book $book, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $userBook = $this->getUserBook($book, $entityManager, false);

        if ($userBook) {
            $userBook->setIsOwned(false);

            // Si aucune autre propriété n'est activée, supprimer l'entrée
            if (!$userBook->isRead() && !$userBook->isWishlist() && !$userBook->getRating() && !$userBook->getPersonnalNotes()) {
                $entityManager->remove($userBook);
            } else {
                $entityManager->persist($userBook);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Le livre a été retiré de votre collection');
        }

        return $this->getRedirectResponse($request, 'app_book_show', ['id' => $book->getId()]);
    }
    #[Route('/books/{id}/wishlist', name: 'app_profile_book_wishlist')]
    public function wishlistBook(Book $book, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $userBook = $this->getUserBook($book, $entityManager);
        $userBook->setIsWishlist(true);

        $entityManager->persist($userBook);
        $entityManager->flush();

        $this->addFlash('success', 'Le livre a été ajouté à votre liste de souhaits');

        return $this->getRedirectResponse($request, 'app_book_show', ['id' => $book->getId()]);
    }

    #[Route('/books/{id}/unwishlist', name: 'app_profile_book_unwishlist')]
    public function unwishlistBook(Book $book, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $userBook = $this->getUserBook($book, $entityManager, false);

        if ($userBook) {
            $userBook->setIsWishlist(false);

            // Si aucune autre propriété n'est activée, supprimer l'entrée
            if (!$userBook->isRead() && !$userBook->isOwned() && !$userBook->getRating() && !$userBook->getPersonnalNotes()) {
                $entityManager->remove($userBook);
            } else {
                $entityManager->persist($userBook);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Le livre a été retiré de votre liste de souhaits');
        }

        return $this->getRedirectResponse($request, 'app_book_show', ['id' => $book->getId()]);
    }

    #[Route('/books/{id}/read', name: 'app_profile_book_read')]
    public function readBook(Book $book, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $userBook = $this->getUserBook($book, $entityManager);
        $userBook->setIsRead(true);

        $entityManager->persist($userBook);
        $entityManager->flush();

        $this->addFlash('success', 'Le livre a été marqué comme lu');

        return $this->getRedirectResponse($request, 'app_book_show', ['id' => $book->getId()]);
    }

    #[Route('/books/{id}/unread', name: 'app_profile_book_unread')]
    public function unreadBook(Book $book, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $userBook = $this->getUserBook($book, $entityManager, false);

        if ($userBook) {
            $userBook->setIsRead(false);

            // Si aucune autre propriété n'est activée, supprimer l'entrée
            if (!$userBook->isOwned() && !$userBook->isWishlist() && !$userBook->getRating() && !$userBook->getPersonnalNotes()) {
                $entityManager->remove($userBook);
            } else {
                $entityManager->persist($userBook);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Le livre a été marqué comme non lu');
        }

        return $this->getRedirectResponse($request, 'app_book_show', ['id' => $book->getId()]);
    }
    #[Route('/books/{id}/rate/{rating}', name: 'app_profile_book_rate', requirements: ['rating' => '[1-5]'])]
    public function rateBook(Book $book, int $rating, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $userBook = $this->getUserBook($book, $entityManager);

        // Convertir la notation en système de préférence
        switch ($rating) {
            case 5: // J'aime (5 étoiles)
                $userBook->setRating(5);
                $message = 'Vous avez ajouté ce livre à vos favoris';
                break;
            case 1: // Je n'aime pas (1 étoile)
                $userBook->setRating(1);
                $message = 'Vous avez indiqué que vous n\'aimez pas ce livre';
                break;
            case 3: // Sans avis (3 étoiles, neutre)
                $userBook->setRating(null); // ou 0, selon votre modèle de données
                $message = 'Vous avez retiré votre avis sur ce livre';
                break;
            default:
                $userBook->setRating($rating);
                $message = 'Votre avis a été enregistré';
        }

        $entityManager->persist($userBook);
        $entityManager->flush();

        $this->addFlash('success', $message);

        return $this->getRedirectResponse($request, 'app_book_show', ['id' => $book->getId()]);
    }

    #[Route('/books/{id}/remove', name: 'app_profile_book_remove')]
    public function removeBook(Book $book, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $userBook = $entityManager->getRepository(UserBook::class)->findOneBy([
            'user' => $user,
            'book' => $book
        ]);

        if ($userBook) {
            $entityManager->remove($userBook);
            $entityManager->flush();
            $this->addFlash('success', 'Le livre a été complètement retiré de votre profil');
        }

        return $this->getRedirectResponse($request, 'app_profile_index');
    }
    #[Route('/books/{id}/notes', name: 'app_profile_book_notes', methods: ['POST'])]
    public function editNotes(Book $book, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $userBook = $this->getUserBook($book, $entityManager);
        $notes = $request->request->get('notes');

        if ($notes !== null) {
            $userBook->setPersonnalNotes($notes);
            $entityManager->persist($userBook);
            $entityManager->flush();

            $this->addFlash('success', 'Vos notes personnelles ont été enregistrées');
        }

        return $this->getRedirectResponse($request, 'app_book_show', ['id' => $book->getId()]);
    }

    /**
     * Récupère ou crée un objet UserBook pour l'utilisateur courant et le livre spécifié
     */
    private function getUserBook(Book $book, EntityManagerInterface $entityManager, bool $createIfNotExist = true): ?UserBook
    {
        $user = $this->getUser();
        
        $userBook = $entityManager->getRepository(UserBook::class)->findOneBy([
            'user' => $user,
            'book' => $book
        ]);
        
        if (!$userBook && $createIfNotExist) {
            $userBook = new UserBook();
            $userBook->setUser($user);
            $userBook->setBook($book);
            $userBook->setAddedAt(new \DateTime());
            $userBook->setIsOwned(false);
            $userBook->setIsWishlist(false);
            $userBook->setIsRead(false);
        }
        
        return $userBook;
    }
    /**
     * Génère une réponse de redirection basée sur le referer ou une route par défaut
     */
    private function getRedirectResponse(Request $request, string $defaultRoute, array $defaultRouteParams = []): Response
    {
        $referer = $request->headers->get('referer');

        // Si un referer existe et qu'il appartient au même domaine, y retourner
        if ($referer) {
            $refererHost = parse_url($referer, PHP_URL_HOST);
            $requestHost = $request->getHost();

            if ($refererHost === $requestHost) {
                return $this->redirect($referer);
            }
        }

        // Sinon, rediriger vers la route par défaut
        return $this->redirectToRoute($defaultRoute, $defaultRouteParams);
    }
}
