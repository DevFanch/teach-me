<?php

namespace App\Controller;

use App\Entity\Region;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class BanController extends AbstractController
{
    #[Route('/ban/regions', name: 'app_ban')]
    public function index(SerializerInterface $serializer): Response
    {
        $content = file_get_contents('https://geo.api.gouv.fr/regions');
        $regions = $serializer->decode($content, 'json');
        // Normalize data
        $regionsObject = $serializer->denormalize($regions, Region::class.'[]', 'json');        

        return $this->render('ban/index.html.twig', [
            'controller_name' => 'BanController',
            'regions' => $regionsObject
        ]);
    }
}
