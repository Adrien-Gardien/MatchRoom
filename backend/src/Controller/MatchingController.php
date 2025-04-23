<?php

namespace App\Controller;

use App\Entity\Matching;
use App\Form\MatchingType;
use App\Repository\MatchingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/matching')]
final class MatchingController extends AbstractController
{
    #[Route(name: 'app_matching_index', methods: ['GET'])]
    public function index(MatchingRepository $matchingRepository): Response
    {
        return $this->render('matching/index.html.twig', [
            'matchings' => $matchingRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_matching_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $matching = new Matching();
        $form = $this->createForm(MatchingType::class, $matching);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($matching);
            $entityManager->flush();

            return $this->redirectToRoute('app_matching_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('matching/new.html.twig', [
            'matching' => $matching,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_matching_show', methods: ['GET'])]
    public function show(Matching $matching): Response
    {
        return $this->render('matching/show.html.twig', [
            'matching' => $matching,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_matching_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Matching $matching, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MatchingType::class, $matching);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_matching_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('matching/edit.html.twig', [
            'matching' => $matching,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_matching_delete', methods: ['POST'])]
    public function delete(Request $request, Matching $matching, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$matching->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($matching);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_matching_index', [], Response::HTTP_SEE_OTHER);
    }
}
