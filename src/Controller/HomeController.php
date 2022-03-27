<?php

namespace App\Controller;

use App\Entity\{
    ContactRequest,
};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Request, Response};
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Display the home page
     */
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'AccueilController'
        ]);
    }

    /**
     * Get friend requests for the current user
     * @param Request $request
     * @return Response
     */
    #[Route('/myNotifications', name: 'myNotifications')]
    public function myNotifications(Request $request): Response
    {
        $contactRequest = $this->entityManager->getRepository(ContactRequest::class)
            ->findOneBy([
                'target' =>$this->getUser()->getId(),
                'flag_state' => ContactRequest::REQUEST_CONTACT_PENDING
            ]);
        ($contactRequest)?$response=true:$response=false;
        return new Response($response);
    }

}
