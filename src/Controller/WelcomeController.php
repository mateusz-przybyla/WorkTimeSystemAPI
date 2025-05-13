<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController extends AbstractController
{
  #[Route('/', name: 'api_index', methods: ['GET'])]
  public function index(): JsonResponse
  {
    return $this->json(['response' => 'Witaj w WorkTimeSystem API.'], Response::HTTP_OK);
  }
}
