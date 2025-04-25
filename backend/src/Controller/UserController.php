<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
final class UserController extends AbstractController
{
    #[Route('/me', name: 'app_user')]
    public function me(UserRepository $userRepository, SerializerInterface $serializerInterface): JsonResponse
    {
        $current_user = $this->getUser();
        $user = $userRepository->findOneBy(['email' => $current_user->getUserIdentifier()]);

        // Sérialisation avec gestion des références circulaires
        $userSerialized = $serializerInterface->serialize(
            $user, 
            'json', 
            [
                'ignored_attributes' => ['password', 'userIdentifier'],
                'circular_reference_handler' => function ($object) {
                    return $object->getId(); // Retourne seulement l'ID pour éviter la boucle
                }
            ]
        );

        return JsonResponse::fromJsonString($userSerialized);
    }
}
