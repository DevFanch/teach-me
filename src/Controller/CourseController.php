<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/course', name: 'app_course_')]
final class CourseController extends AbstractController
{
    #[Route(name: 'index', methods: ['GET'])]
    public function index(Request $request, CourseRepository $courseRepository): Response
    {
        /**
         * Les différentes façons de récupérer les cours avec le repository
         * NB : Les plus performantes sont findLastCourses(5) et findLastCoursesDQL(5)
         * 1. findLastCourses(5)
         * 2. findLastCoursesDQL(5)
         * 3. findPublishedCourses()
         * 4. findBy([], ['createdAt' => 'DESC'], 5)
         * 5. findAll()
         */
        // Vérifier si l'utilisateur est authorisé à créer un cours via le voter
        if (!$this->isGranted('ROLE_USER', null)) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à créer un cours');
        }

        // Récupérer la page et le nombre de cours par page
        $page = $request->query->getInt('page', 1);
        $limit = 6;

        // Récupérer les cours paginés
        $pagination = $courseRepository->findPaginatedCourses($page, $limit);

        return $this->render('course/index.html.twig', [
            'courses' => $pagination,
            'current_page' => $page,
            'total_pages' => ceil($pagination->count() / $limit)
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $course = new Course();
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($course);

            // ne pas oublier le flush() sinon l'objet ne sera pas persisté en base.
            $entityManager->flush();

            $this->addFlash('success', 'Le cours a été créé avec succès');
            return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('course/new.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Course $course): Response
    {
        // on vérifie si le cours existe
        if (!$course) {
            // on affiche une erreur 404
            throw $this->createNotFoundException('Cours inconnu');
        }

        return $this->render('course/show.html.twig', [
            'course' => $course,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_PLANNER")]
    // public function edit(Request $request, Course $course, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    public function edit(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ne pas oublier le flush() sinon l'objet ne sera pas persisté en base.
            $entityManager->flush();

            $this->addFlash('success', 'Le cours a été modifié avec succès');
            return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
        }

        // Exemple de validation avec le validator
        // $errors = $validator->validate($course);
        // if (count($errors) > 0) {
        //     $this->addFlash('error', 'Le cours n\'a pas été modifié');
        //     return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
        // } elseif ($form->isSubmitted() && $form->isValid()) {
        //     $entityManager->flush();
        //     $this->addFlash('success', 'Le cours a été modifié avec succès');
        //     return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
        // }

        return $this->render('course/edit.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    #[Route(
        '/{id}/delete/{_token}',
        name: 'delete',
        requirements: ['id' => '\d+', '_token' => '[a-zA-Z0-9_\-\.]+'],
        methods: ['GET']
    )]
    public function delete(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {

        // Vérifier si l'utilisateur est authorisé à créer un cours via le voter
        if (!$this->isGranted('ROLE_ADMIN', null)) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer un cours');
        }

        // on vérifie si le cours existe
        if (!$course) {
            // on affiche une erreur 404
            throw $this->createNotFoundException('Cours inconnu');
        }

        // En post, via le delete form (Ne pas oublier de repasser en methods: ['POST'])
        // if ($this->isCsrfTokenValid('delete'.$course->getId(), $request->getPayload()->getString('_token'))) {

        // En get, via le lien de suppression
        if ($this->isCsrfTokenValid('delete' . $course->getId(), $request->get('_token'))) {
            // dump($course); // before remove
            $entityManager->remove($course);
            // dump($course); // after remove
            $entityManager->flush();
            // dump($course); // after flush

            $this->addFlash('info', 'Le cours a été supprimé avec succès');
        } else {
            $this->addFlash('error', 'Le jeton CSRF est invalide, suppression impossible');
        }

        return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
    }
}
