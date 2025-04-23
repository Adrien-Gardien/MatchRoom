<?php

namespace App\Controller;

use App\Entity\UserPreference;
use App\Form\UserPreferenceType;
use App\Repository\UserPreferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/preference')]
final class UserPreferenceController extends AbstractController
{
    #[Route(name: 'app_user_preference_index', methods: ['GET'])]
    public function index(UserPreferenceRepository $userPreferenceRepository): Response
    {
        return $this->render('user_preference/index.html.twig', [
            'user_preferences' => $userPreferenceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_preference_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userPreference = new UserPreference();
        $form = $this->createForm(UserPreferenceType::class, $userPreference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($userPreference);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_preference_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_preference/new.html.twig', [
            'user_preference' => $userPreference,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_preference_show', methods: ['GET'])]
    public function show(UserPreference $userPreference): Response
    {
        return $this->render('user_preference/show.html.twig', [
            'user_preference' => $userPreference,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_preference_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserPreference $userPreference, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserPreferenceType::class, $userPreference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_preference_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_preference/edit.html.twig', [
            'user_preference' => $userPreference,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_preference_delete', methods: ['POST'])]
    public function delete(Request $request, UserPreference $userPreference, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userPreference->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($userPreference);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_preference_index', [], Response::HTTP_SEE_OTHER);
    }
}
