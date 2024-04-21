<?php

namespace App\Controller\User;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/user')]
class ManageController extends AbstractController
{
    #[Route('/show/{email}', name: 'app_show_user')]
    public function show(
        UserRepository $userRepository,
        $email
    ): JsonResponse
    {
        try {
        $user = $userRepository->findOneByEmail($email);

        if (!$user) {
            return new Response(json_encode(["error" => "user not found"]), 400);
        }

        $userData = [
            'email' => $user->getEmail(),
            'name' => $user->getName()
        ];

        return new JsonResponse($userData);

        } catch (\Exception $e) {
            return new Response(json_encode(["error" => $e->getMessage()]), 500);
        }
    }
}
