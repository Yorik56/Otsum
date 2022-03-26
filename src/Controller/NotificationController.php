<?php

namespace App\Controller;

use App\Entity\InvitationToPlay;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/notification', name: 'notification')]
    public function index(): Response
    {
        //-- Invitations to play received
        $invitationToPlay = $this->entityManager->getRepository(entityName: InvitationToPlay::class)
            ->findBy([
                'invitedUser' => $this->getUser()->getId(),
                'flag_state' => InvitationToPlay::REQUEST_GAME_PENDING
            ]);

        return $this->render('notification/index.html.twig', [
            'controller_name' => 'NotificationController',
            'invitationToPlay' => $invitationToPlay,
        ]);
    }
}
