<?php

namespace App\Controller;

use App\Entity\UserBadge;
use App\Form\UserBadgeType;
use App\Repository\UserBadgeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/badge')]
final class UserBadgeController extends AbstractController
{
    #[Route(name: 'app_user_badge_index', methods: ['GET'])]
    public function index(UserBadgeRepository $userBadgeRepository): Response
    {
        return $this->render('user_badge/index.html.twig', [
            'user_badges' => $userBadgeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_badge_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userBadge = new UserBadge();
        $form = $this->createForm(UserBadgeType::class, $userBadge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($userBadge);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_badge_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_badge/new.html.twig', [
            'user_badge' => $userBadge,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_badge_show', methods: ['GET'])]
    public function show(UserBadge $userBadge): Response
    {
        return $this->render('user_badge/show.html.twig', [
            'user_badge' => $userBadge,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_badge_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserBadge $userBadge, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserBadgeType::class, $userBadge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_badge_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_badge/edit.html.twig', [
            'user_badge' => $userBadge,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_badge_delete', methods: ['POST'])]
    public function delete(Request $request, UserBadge $userBadge, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userBadge->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($userBadge);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_badge_index', [], Response::HTTP_SEE_OTHER);
    }
}
