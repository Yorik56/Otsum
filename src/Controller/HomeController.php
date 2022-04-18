<?php

namespace App\Controller;

use App\Entity\{
    ContactRequest,
};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Request, Response};
use App\Service\MailerService;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

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
     * Send a mail
     */
    #[Route('/mail', name: 'mail')]
    public function mail(MailerService $mailer): Response
    {
        try {
            $mailer->send(
                "Bienvenue chez Otsum",
                "yorikmoreau@gmail.com",
                "yorikmoreau@gmail.com",
                "email/contact.html.twig",
                [
                    "name" => "Otsum",
                    "Description" => "Ceci est un mail de test"
                ]
            );
        } catch (TransportExceptionInterface|SyntaxError|RuntimeError|LoaderError $e) {
            dump($e);
        }

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
