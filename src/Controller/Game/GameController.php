<?php

namespace App\Controller\Game;

use App\Service\GameManagerService;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Annotation\Route;


class GameController extends AbstractController
{
    protected EntityManagerInterface $entityManager;
    protected HubInterface $hub;
    public GameManagerService $gameManagerService;
    public Utils $utils;


    public function __construct(
        EntityManagerInterface $entityManager,
        HubInterface $hub,
        GameManagerService $gameManagerService,
        Utils $utils
    )
    {
        $this->entityManager = $entityManager;
        $this->hub = $hub;
        $this->gameManagerService = $gameManagerService;
        $this->utils = $utils;
    }



    /**
     * Check for the existence of a word
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/checkWordExistence', name: 'checkWordExistence')]
    function checkWordExistence(Request $request): JsonResponse
    {


        return new JsonResponse(
            $this->utils->checkWordExistence(
                trim(
                    $request->request->get('mot')
                )
            )
        );
    }
}
