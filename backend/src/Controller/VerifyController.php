<?php
namespace App\Controller;

use App\Service\EmailService;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/api')]
class VerifyController extends AbstractController
{
    private $emailService;
    private $userRepository;
    private $security;
    private $managerRegistry;

    public function __construct(EmailService $emailService, UserRepository $userRepository, Security $security, ManagerRegistry $managerRegistry)
    {
        $this->emailService = $emailService;
        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->managerRegistry = $managerRegistry;
    }

    #[Route('/verify/email', name: 'app_verify_email', methods: ['POST'])]
    public function verifyEmail(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $verificationToken = $data['token'] ?? null;

        if (!$verificationToken) {
            return new JsonResponse(['success' => false, 'message' => 'Token not provided.'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->findOneBy(['verificationCode' => $verificationToken]);

        if (!$user) {
            return new JsonResponse(['success' => false, 'message' => 'User not found or invalid token.'], Response::HTTP_NOT_FOUND);
        }

        $user->setIsVerified(true);

        $entityManager = $this->managerRegistry->getManager();
        $entityManager->flush();

        return new JsonResponse(['success' => true, 'message' => 'Your email address has been verified.'], Response::HTTP_OK);
    }
}
