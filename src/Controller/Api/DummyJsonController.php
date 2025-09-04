<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DummyJsonController extends AbstractController
{
    #[Route('/api/dummy/json', name: 'app_api_dummy_json')]
    public function index(): Response
    {
        return $this->render('api/dummy_json/index.html.twig', [
            'controller_name' => 'Api/DummyJsonController',
        ]);
    }
}
