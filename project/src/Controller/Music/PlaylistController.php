<?php

namespace App\Controller\Music;

use App\Entity\Playlist;
use App\Entity\User;
use App\Repository\MusicRepository;
use App\Repository\PlaylistRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/playlist')]
class PlaylistController extends AbstractController
{
    #[Route('/user/{idUser}', name: 'app_user_favorite')]
    public function list(
        PlaylistRepository $playlistRepository,
        UserRepository $userRepository,
        $idUser
    ): JsonResponse
    {

        $user = $userRepository->findOneById($idUser);


        if (!$user) {
            // Handle the case when no user is found
            return new JsonResponse(['error' => 'User not found'], 404);
        }



        $playlistList = [];
        foreach ($user->getPlaylists() as $playlist) {
            $playlistList[] = [
                'id' => $playlist->getId(),
                'author' => $playlist->getOwner()->getId(),
                'title' => $playlist->getTitle(),
                'path_img' => $playlist->getPathImg(),
                'liked_title' => $playlist->isLikedTitle(),
            ];
        }

        return new JsonResponse($playlistList);
    }

    #[Route('/show/{id}', name: 'app_list_music_favorite')]
    public function show(
        PlaylistRepository $playlistRepository,
        $id,
    ): JsonResponse
    {
        $playlist = $playlistRepository->findOneById($id);

        $musicList = [];
        foreach ($playlist->getMusic() as $music) {
            $musicList[] = [
                'id' => $music->getId(),
                'title' => $music->getTitle(),
                'author' => $music->getAuthor(),
                'path' => $music->getPath(),
            ];
        }

        return new JsonResponse($musicList);
    }

    #[Route('/manage/{id}/music/{idMusic}', name: 'app_add_music_favorite')]
    public function manage(
        PlaylistRepository $playlistRepository,
        MusicRepository $musicRepository,
        EntityManagerInterface $entityManager,
        $id,
        $idMusic
    ): Response
    {
        try {
            $playlist = $playlistRepository->findOneById($id);

            if (!$playlist) {
                return new Response(json_encode(["error" => "Playist not found"]), 400);
            }
            $music = $musicRepository->findOneById($idMusic);
            if (!$music) {
                return new Response(json_encode(["error" => "Music not found"]), 400);
            }
            // Checks if the music is already in the favorites list
            if ($playlist->hasMusic($music)) {
                $playlist->removeMusic($music);
            } else {
                $playlist->addMusic($music);
            }
            $entityManager->persist($playlist);
            $entityManager->flush();

            return new Response("true");

        } catch (\Exception $e) {
            return new Response(json_encode(["error" => $e->getMessage()]), 500);
        }
    }
}

