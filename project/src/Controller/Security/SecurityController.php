<?php

namespace App\Controller\Security;

use App\Entity\User;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
//use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
//    private $entityManager;
//    private $passwordHasher;
//
//    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
//    {
//        $this->entityManager = $entityManager;
//        $this->passwordHasher = $passwordHasher;
//    }

    #[Route('/security/signUp', name: 'app_security_signUp')]
    public function signUp(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Récupérer les données du corps de la requête
        $data = json_decode($request->getContent(), true);

        // Vérifier si les données nécessaires sont présentes
        if (isset($data['name'], $data['email'], $data['password'])) {
            // Récupérer les valeurs des données
            $name = $data['name'];
            $email = $data['email'];
            $plaintextPassword = $data['password'];

            try {
                // Créer une nouvelle instance de l'entité associée à votre table "music"
                $user = new User();
                $user->setName($name);
                $user->setEmail($email);

                // hash the password before saving it
                $hashedPassword = $passwordHasher->hashPassword($user, $plaintextPassword);
                $user->setPassword($hashedPassword);

                // Persister l'entité et effectuer l'opération d'insertion
                $entityManager->persist($user);
                $entityManager->flush();

                // Retourner une réponse réussie
                return new Response("true");
            } catch (\Exception $e) {
                return new Response(json_encode(["error" => $e->getMessage()]), 500);
            }
        } else {
            return new Response(json_encode(["error" => "Missing datas"]), 400);
        }
    }

    #[Route('/security/signIn', name: 'app_security_signIn')]
    public function signIn(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Récupérer les données du corps de la requête
        $data = json_decode($request->getContent(), true);

        // Vérifier si les données nécessaires sont présentes
        if (isset($data['email'], $data['password'])) {
            // Récupérer les valeurs des données
            $email = $data['email'];
            $plaintextPassword = $data['password'];

            try {
                // Utiliser Doctrine pour récupérer l'utilisateur par email
                $userRepository = $entityManager->getRepository(User::class);
                $user = $userRepository->findOneBy(['email' => $email]);

                // Vérifier si l'utilisateur existe et si le mot de passe est correct
                if ($user && $passwordHasher->isPasswordValid($user, $plaintextPassword)) {
                    return new Response("true");
                } else {
                    return new Response("false");
                }
            } catch (\Exception $e) {
                return new Response(json_encode(["error" => $e->getMessage()]), 500);
            }
        } else {
            return new Response(json_encode(["error" => "Missing datas"]), 400);
        }
    }
}
