<?php

namespace App\Controller;


use App\Entity\Book;
use App\Entity\Character;
use App\Entity\Series;

use App\Form\CharacterType;
use App\Repository\CharacterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/character')]
final class CharacterController extends AbstractController
{
    #[Route(name: 'app_character_index', methods: ['GET'])]
    public function index(CharacterRepository $characterRepository): Response
    {
        return $this->render('character/index.html.twig', [
            'characters' => $characterRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_character_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $character = new Character();
        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($character);
            $entityManager->flush();

            return $this->redirectToRoute('app_character_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('character/new.html.twig', [
            'character' => $character,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: 'app_character_show', methods: ['GET'])]
    public function show(Character $character, EntityManagerInterface $entityManager): Response
    {
        // Récupérer uniquement les séries où ce personnage apparaît
        $seriesWithBooks = $entityManager->createQueryBuilder() // Crée une nouvelle instance du QueryBuilder à partir de l'EntityManager
            ->select('s') // Sélectionne l'entité 's' (Series) comme résultat principal de la requête
            ->from('App\Entity\Series', 's') // Définit la table (entité) principale 'Series' avec l'alias 's'
            ->join('s.books', 'b') // Effectue une jointure entre 'Series' et son association 'books' (relation définie dans l'entité Series)
            ->join('b.characters', 'c') // Effectue une jointure entre 'books' et son association 'characters' (relation définie dans l'entité Book)
            ->where('c.id = :characterId') // Ajoute une condition pour filtrer uniquement les résultats où l'ID du personnage correspond à :characterId
            ->setParameter('characterId', $character->getId()) // Associe la valeur du paramètre :characterId avec l'ID du personnage donné
            ->getQuery() // Génère la requête à partir du QueryBuilder
            ->getResult(); // Exécute la requête et retourne le résultat sous forme d'un tableau d'objets Series

        // Récupérer uniquement les one-shots où ce personnage apparaît
        $oneShotBooks = $entityManager->createQueryBuilder()
            ->select('b')
            ->from('App\Entity\Book', 'b')
            ->leftJoin('b.series', 's')
            ->join('b.characters', 'c')
            ->where('c.id = :characterId')
            ->andWhere('s.id IS NULL') // Assurer que ce sont des one-shots
            ->setParameter('characterId', $character->getId())
            ->getQuery()
            ->getResult();
    

        dump($seriesWithBooks, $oneShotBooks);
        return $this->render('character/show.html.twig', [
            'character' => $character,
            'series' => $seriesWithBooks,
            'oneShot' => $oneShotBooks,
        ]);
    }
    
    
    

    #[Route('/{id}/edit', name: 'app_character_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Character $character, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_character_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('character/edit.html.twig', [
            'character' => $character,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_character_delete', methods: ['POST'])]
    public function delete(Request $request, Character $character, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$character->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($character);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_character_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/character/partial', name: 'app_character_partial')]
    public function partial(EntityManagerInterface $entityManager): Response
    {
        $character = $entityManager->getRepository(Character::class)->findAll();
    
        return $this->render('character/partial.html.twig', [
            'character' => $character,
        ]);
    }
}
