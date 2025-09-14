<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]
final class CourseStatusController extends AbstractController
{
    #[Route('/admin', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/admin/course-status', name: 'course_status')]
    public function status(): Response
    {
        return $this->render('admin/course_status.html.twig');
    }
}
