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

        // Traiter les actions
        $action = $request->query->get('action');

        // Si l'action est remove et que le userBook existe, on le supprime
        if ($action === 'remove' && $userBook) {
            // Option 1: Suppression complète de l'entrée UserBook
            $entityManager->remove($userBook);
            $entityManager->flush();

            $this->addFlash('success', 'Le livre a été retiré de votre liste');

            // Rediriger vers la page de profil au lieu de la page du livre
            return $this->redirectToRoute('app_profile_index');
        }

        // Pour les autres actions, continuer comme avant
        if (!$userBook) {
            $userBook = new UserBook();
            $userBook->setUser($user);
            $userBook->setBook($book);
            $userBook->setAddedAt(new \DateTime());
            $userBook->setIsOwned(false);
            $userBook->setIsWishlist(false);
            $userBook->setIsRead(false);
        }

        if ($action === 'own') {
            $userBook->setIsOwned(true);
        } elseif ($action === 'wishlist') {
            $userBook->setIsWishlist(true);
        } elseif ($action === 'read') {
            $userBook->setIsRead(true);
        } elseif ($action === 'toggle_wishlist') {
            // Option alternative: basculer l'état isWishlist
            $userBook->setIsWishlist(!$userBook->isWishlist());
        }

        $entityManager->persist($userBook);
        $entityManager->flush();

        // Récupérer la page précédente (referer) pour y retourner après l'action
        $referer = $request->headers->get('referer');

        // Si le referer existe, y retourner, sinon aller à la page du livre
        if ($referer) {
            return $this->redirect($referer);
        } else {
            return $this->redirectToRoute('app_book_show', ['id' => $book->getId(), 'slug' => $book->getTitle()]);
        }
    }
    #[Route('/books/{id}', name: 'app_profile_manage_book')]
    public function manageBook(Book $book, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupérer l'action demandée
        $action = $request->query->get('action', 'view');

        // Vérifier si l'utilisateur a déjà une relation avec ce livre
        $userBook = $entityManager->getRepository(UserBook::class)->findOneBy([
            'user' => $user,
            'book' => $book
        ]);

        // Actions nécessitant la suppression de l'entrée
        if ($action === 'remove' && $userBook) {
            $entityManager->remove($userBook);
            $entityManager->flush();
            $this->addFlash('success', 'Le livre a été retiré de votre collection');

            // Rediriger vers la page d'où vient la requête ou la page de profil
            return $this->getRedirectResponse($request, 'app_profile_index');
        }

        // Créer une nouvelle entrée si elle n'existe pas
        if (!$userBook) {
            $userBook = new UserBook();
            $userBook->setUser($user);
            $userBook->setBook($book);
            $userBook->setAddedAt(new \DateTime());
            $userBook->setIsOwned(false);
            $userBook->setIsWishlist(false);
            $userBook->setIsRead(false);
        }

        // Tableau des messages flash pour chaque action
        $flashMessages = [
            'own' => 'Le livre a été ajouté à votre collection',
            'un_own' => 'Le livre a été retiré de votre collection',
            'wishlist' => 'Le livre a été ajouté à votre liste de souhaits',
            'un_wishlist' => 'Le livre a été retiré de votre liste de souhaits',
            'read' => 'Le livre a été marqué comme lu',
            'un_read' => 'Le livre a été marqué comme non lu',
            'rate' => 'Votre note a été enregistrée'
        ];

        // Traiter les différentes actions
        switch ($action) {
            case 'own':
                $userBook->setIsOwned(true);
                break;
            case 'un_own':
                $userBook->setIsOwned(false);
                break;
            case 'wishlist':
                $userBook->setIsWishlist(true);
                break;
            case 'un_wishlist':
                $userBook->setIsWishlist(false);
                break;
            case 'read':
                $userBook->setIsRead(true);
                break;
            case 'un_read':
                $userBook->setIsRead(false);
                break;
            case 'toggle_own':
                $userBook->setIsOwned(!$userBook->isOwned());
                $action = $userBook->isOwned() ? 'own' : 'un_own';
                break;
            case 'toggle_wishlist':
                $userBook->setIsWishlist(!$userBook->isWishlist());
                $action = $userBook->isWishlist() ? 'wishlist' : 'un_wishlist';
                break;
            case 'toggle_read':
                $userBook->setIsRead(!$userBook->isRead());
                $action = $userBook->isRead() ? 'read' : 'un_read';
                break;
            case 'rate':
                $rating = $request->query->getInt('rating', 0);
                if ($rating >= 1 && $rating <= 5) {
                    $userBook->setRating($rating);
                }
                break;
            case 'notes':
                $notes = $request->request->get('notes');
                if ($notes !== null) {
                    $userBook->setPersonnalNotes($notes);
                }
                break;
        }

        // Persister les changements
        $entityManager->persist($userBook);
        $entityManager->flush();

        // Ajouter un message flash si disponible pour cette action
        if (isset($flashMessages[$action])) {
            $this->addFlash('success', $flashMessages[$action]);
        }

        // Rediriger vers la page appropriée
        return $this->getRedirectResponse($request, 'app_book_show', ['id' => $book->getId(), 'slug' => $book->getTitle()]);
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
