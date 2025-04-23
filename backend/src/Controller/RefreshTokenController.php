<?php

namespace App\Controller;

use App\Entity\RefreshToken;
use App\Form\RefreshTokenType;
use App\Repository\RefreshTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/refresh/token')]
final class RefreshTokenController extends AbstractController
{
    #[Route(name: 'app_refresh_token_index', methods: ['GET'])]
    public function index(RefreshTokenRepository $refreshTokenRepository): Response
    {
        return $this->render('refresh_token/index.html.twig', [
            'refresh_tokens' => $refreshTokenRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_refresh_token_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $refreshToken = new RefreshToken();
        $form = $this->createForm(RefreshTokenType::class, $refreshToken);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($refreshToken);
            $entityManager->flush();

            return $this->redirectToRoute('app_refresh_token_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('refresh_token/new.html.twig', [
            'refresh_token' => $refreshToken,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_refresh_token_show', methods: ['GET'])]
    public function show(RefreshToken $refreshToken): Response
    {
        return $this->render('refresh_token/show.html.twig', [
            'refresh_token' => $refreshToken,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_refresh_token_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RefreshToken $refreshToken, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RefreshTokenType::class, $refreshToken);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_refresh_token_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('refresh_token/edit.html.twig', [
            'refresh_token' => $refreshToken,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_refresh_token_delete', methods: ['POST'])]
    public function delete(Request $request, RefreshToken $refreshToken, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$refreshToken->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($refreshToken);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_refresh_token_index', [], Response::HTTP_SEE_OTHER);
    }
}
