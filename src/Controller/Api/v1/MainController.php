<?php

namespace App\Controller\Api\v1;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1', name: 'api_v1_')]
class MainController extends AbstractController
{
    #[Route('/', name: 'index', methods:['GET'])]
    public function index(PostRepository $postRepository): JsonResponse
    {
        $listOfPosts = $postRepository->findAll();

        return $this->json($listOfPosts, 200, [], [
            'groups' => ['postsList', 'usersList'],
        ]);
    }
}
