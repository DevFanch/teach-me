<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class BanController extends AbstractController
{
    #[Route('/api/regions', name: 'app_api_gouv')]
    public function index(SerializerInterface $serializer): Response
    {
        $content = file_get_contents('https://geo.api.gouv.fr/regions');
        $regions = $serializer->decode($content, 'json');
        
        // Return Json Response
        return $this->json($regions);
    }
}
