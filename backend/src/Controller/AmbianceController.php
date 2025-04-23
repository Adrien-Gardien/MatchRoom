<?php

namespace App\Controller;

use App\Entity\Ambiance;
use App\Form\AmbianceType;
use App\Repository\AmbianceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/ambiance')]
final class AmbianceController extends AbstractController
{
    #[Route(name: 'app_ambiance_index', methods: ['GET'])]
    public function index(AmbianceRepository $ambianceRepository): Response
    {
        return $this->render('ambiance/index.html.twig', [
            'ambiances' => $ambianceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_ambiance_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ambiance = new Ambiance();
        $form = $this->createForm(AmbianceType::class, $ambiance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ambiance);
            $entityManager->flush();

            return $this->redirectToRoute('app_ambiance_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ambiance/new.html.twig', [
            'ambiance' => $ambiance,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ambiance_show', methods: ['GET'])]
    public function show(Ambiance $ambiance): Response
    {
        return $this->render('ambiance/show.html.twig', [
            'ambiance' => $ambiance,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ambiance_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ambiance $ambiance, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AmbianceType::class, $ambiance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ambiance_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ambiance/edit.html.twig', [
            'ambiance' => $ambiance,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ambiance_delete', methods: ['POST'])]
    public function delete(Request $request, Ambiance $ambiance, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ambiance->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ambiance);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ambiance_index', [], Response::HTTP_SEE_OTHER);
    }
}
