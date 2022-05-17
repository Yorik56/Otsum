<?php

namespace App\Controller\Game;

use App\Service\GameManagerKeyboardService;
use App\Service\GameManagerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mercure\HubInterface;

class GameController extends AbstractController
{
    protected EntityManagerInterface $entityManager;
    protected HubInterface $hub;
    public GameManagerService $gameManagerService;

    public function __construct(
        EntityManagerInterface $entityManager,
        HubInterface $hub,
        GameManagerService $gameManagerService
    )
    {
        $this->entityManager = $entityManager;
        $this->hub = $hub;
        $this->gameManagerService = $gameManagerService;
    }
}
