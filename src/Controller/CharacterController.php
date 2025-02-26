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
        $seriesWithBooks = $entityManager->createQueryBuilder() 
            ->select('s') 
            ->from('App\Entity\Series', 's') 
            ->join('s.books', 'b')
            ->join('b.characters', 'c')
            ->where('c.id = :characterId') 
            ->setParameter('characterId', $character->getId()) 
            ->getQuery() 
            ->getResult(); 

        $oneShotBooks = $entityManager->createQueryBuilder()
            ->select('b')
            ->from('App\Entity\Book', 'b')
            ->leftJoin('b.series', 's')
            ->join('b.characters', 'c')
            ->where('c.id = :characterId')
            ->andWhere('s.id IS NULL') 
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
