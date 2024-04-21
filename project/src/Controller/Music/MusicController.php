<?php

namespace App\Controller\Music;

use App\Entity\Music;
use App\Repository\MusicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use function App\Controller\json_response;

// ...

#[Route('/music')]
class MusicController extends AbstractController
{
    #[Route('/', name: 'app_music_index', methods: ['GET'])]
    public function index(MusicRepository $musicRepository): JsonResponse
    {
        // Fetch all music from the repository
        $allMusic = $musicRepository->findAll();

        // Convert entities to arrays manually
        $musicArray = [];
        foreach ($allMusic as $music) {
            $musicArray[] = [
                'id' => $music->getId(),
                'title' => $music->getTitle(),
                'author' => $music->getAuthor(),
                'path' => $music->getPath(),
                // Ajoutez d'autres propriétés au besoin
            ];
        }

        // Return a JsonResponse containing the fetched music as JSON
        return $this->json($musicArray);
    }

    #[Route('/new', name: 'app_music_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $contentType = $request->headers->get('Content-Type');


        $data = json_decode($request->getContent(), true);

        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Il y a une erreur lors du décodage JSON
            dump("Erreur JSON : " . json_last_error_msg());
        } else {
            // Le décodage JSON est réussi
            dump($data);
        }

        // Vérifie si les clés existent avant de les utiliser
        if (isset($data['title'], $data['author'], $data['fileContentBase64'])) {
            $title = $data['title'];
            $author = $data['author'];
            $fileContentBase64 = $data['fileContentBase64'];

            // Ajoutez des dumps pour déboguer
            dump($title);
            dump($author);
            dump($fileContentBase64);

            try {
                $fileContent = base64_decode($fileContentBase64);

                // Récupère le répertoire des fichiers de musique depuis les paramètres
                $musicDirectory = $this->getParameter('kernel.project_dir') . '/public/music/';

                // Génère un nom de fichier unique (vous pouvez ajuster cela selon vos besoins)
                $filename = uniqid() . '.mp3';

                // Chemin complet du fichier
                $filePath = $musicDirectory . $filename;

                // Écrit les données binaires dans le fichier
                file_put_contents($filePath, $fileContent);

                $music = new Music();

                $music
                    ->setTitle($title)
                    ->setAuthor($author)
                    ->setPath($filename);

                $entityManager->persist($music);
                $entityManager->flush();

                return new JsonResponse(["success" => true]);
            } catch (\Exception $e) {
                return new JsonResponse(["error" => $e->getMessage()], 500);
            }
        } else {
            return new JsonResponse(["error" => "Missing datas"], 400);
        }
    }




    #[Route('/{id}', name: 'app_music_show', methods: ['GET'])]
    public function show(Music $music): StreamedResponse
    {
        // Obtenez le chemin du fichier musical
        $musicDirectory = $this->getParameter('kernel.project_dir') . '/public/music/';
        $filePath = $musicDirectory . $music->getPath();

        // Vérifiez si le fichier existe
        if (!file_exists($filePath)) {
            $response = [
                'error' => 'Le fichier musical n\'existe pas.'
            ];

            // Utilisez votre fonction json_response pour renvoyer la réponse JSON
            return json_response($response);
        }


        // Configurez la réponse pour le streaming audio
        $response = new StreamedResponse(function () use ($filePath) {
            // Ouvrez le fichier en mode binaire
            $file = fopen($filePath, 'rb');

            // Écrivez le contenu du fichier dans la réponse en streaming
            while (!feof($file)) {
                echo fread($file, 1024 * 8); // ajustez la taille du tampon selon vos besoins
                flush();
            }

            fclose($file);
        });

        // Configurez les entêtes de la réponse pour indiquer qu'il s'agit d'un contenu audio
        $response->headers->set('Content-Type', 'audio/mpeg'); // ajustez le type de contenu en fonction du format de votre musique

        return $response;
    }

}
