<?php

namespace App\Controller;



use App\Entity\GroupeOfCharacter;
use App\Entity\Character;


use App\Form\GroupeOfCharacterType;
use App\Repository\GroupeOfCharacterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/team')]
final class GroupeOfCharacterController extends AbstractController
{
    #[Route(name: 'app_groupe_of_character_index', methods: ['GET'])]
    public function index(GroupeOfCharacterRepository $groupeOfCharacterRepository): Response
    {
        return $this->render('groupe_of_character/index.html.twig', [
            'groupe_of_characters' => $groupeOfCharacterRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_groupe_of_character_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $groupeOfCharacter = new GroupeOfCharacter();
        $form = $this->createForm(GroupeOfCharacterType::class, $groupeOfCharacter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($groupeOfCharacter);
            $entityManager->flush();

            return $this->redirectToRoute('app_groupe_of_character_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('groupe_of_character/new.html.twig', [
            'groupe_of_character' => $groupeOfCharacter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_groupe_of_character_show', methods: ['GET'])]
    public function show(GroupeOfCharacter $groupeOfCharacter, EntityManagerInterface $entityManager): Response
    {
        $characterInTeam = $entityManager->createQueryBuilder()
            ->select('c') 
            ->from('App\Entity\Character', 'c') 
            ->join('c.groupeOfCharacters'
            , 't') 
            ->where('t.id = :groupeOfCharacterId') 
            ->setParameter('groupeOfCharacterId', $groupeOfCharacter->getId())
            ->getQuery()
            ->getResult();

        return $this->render('groupe_of_character/show.html.twig', [
            'groupe_of_character' => $groupeOfCharacter,
            'characters' => $characterInTeam 
        ]);
    }


    #[Route('/{id}/edit', name: 'app_groupe_of_character_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GroupeOfCharacter $groupeOfCharacter, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GroupeOfCharacterType::class, $groupeOfCharacter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_groupe_of_character_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('groupe_of_character/edit.html.twig', [
            'groupe_of_character' => $groupeOfCharacter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_groupe_of_character_delete', methods: ['POST'])]
    public function delete(Request $request, GroupeOfCharacter $groupeOfCharacter, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$groupeOfCharacter->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($groupeOfCharacter);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_groupe_of_character_index', [], Response::HTTP_SEE_OTHER);
    }
}
