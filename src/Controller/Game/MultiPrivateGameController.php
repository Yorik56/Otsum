<?php

namespace App\Controller\Game;

use App\Entity\Game;
use App\Entity\InGamePlayerStatus;
use App\Entity\Team;
use App\Form\DropOutFormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class MultiPrivateGameController extends GameController
{
    /**
     * Start a game
     * @param $idGame
     * @return Response
     */
    #[Route('/otsum/multiPrivateGame/{idGame}', name: 'otsum.multiPrivateGame')]
    public function otsum($idGame): Response
    {
        //Retrieve Game
        $game = $this->entityManager->getRepository(Game::class)->find($idGame);

        // Send mercure Update
        if($game->getHost()->getId() == $this->getUser()->getId()){
            // Mercure notification containing the current player id
            $update = new Update(
                '/hub/prive/start/'.$idGame,
                json_encode([
                    'topic' =>'/hub/prive/start/'.$idGame,
                    'url_partie' => $this->generateUrl('otsum.multiPrivateGame', ['idGame' => $idGame])
                ])
            );
            $this->hub->publish($update);
        }

        //--- Recovery of the teams
        $tableTeam = $this->entityManager->getRepository(Team::class)->findBy(['game'=>$idGame]);
        $newArrayTeam = [];
        foreach ($tableTeam as $team){
            $newArrayTeam[$team->getColor()] = $team;
        }
        $tableTeam = $newArrayTeam;

        // Set the presence of the current user
        $playerStatus = $this->entityManager
            ->getRepository(InGamePlayerStatus::class)
            ->findOneBy([
                'user'=>$this->getUser()->getId()
            ]);

        $playerStatus->setFlagPresenceInGame(InGamePlayerStatus::FLAG_PRESENCE_TRUE);
        $this->entityManager->persist($playerStatus);
        $this->entityManager->flush();

        return $this->render('otsum/multiPrivateGame.html.twig',[
            "firstLetter" => $game->getWordToFind()[0],
            "idGame"      => $game->getId(),
            "difficulty"  => $game->getLineLength(),
            "tableTeam"   => $tableTeam,
            'additionalParams' => [
                'idGame' => $idGame
            ]
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/joiningPrivateGame', name: 'joiningPrivateGame')]
    public function joiningPrivateGame(Request $request): JsonResponse
    {
        $idGame   = $request->request->get('idGame');
        $idPlayer = $request->request->get('idPlayer');

        // Mercure notification joiningPrivateGame
        $update = new Update(
            '/joiningPrivateGame/'.$idGame,
            json_encode([
                'topic' =>'/joiningPrivateGame/'.$idGame,
                'idGame' => $idGame,
                'idPlayer' => $idPlayer
            ])
        );
        $this->hub->publish($update);
        return new JsonResponse([
            'idGame' => $idGame,
            'idPlayer' => $idPlayer
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/checkPresenceOfAllPlayers', name: 'checkPresenceOfAllPlayers')]
    public function checkPresenceOfAllPlayers(Request $request): JsonResponse
    {
        $idGame   = $request->request->get('idGame');
        $game = $this->entityManager->getRepository(Game::class)->find($idGame);
        $inGamePlayerStatuses = $game->getInGamePlayerStatuses();
        $gameReady = true;
        foreach ($inGamePlayerStatuses as $inGamePlayerStatus){
            if($inGamePlayerStatus->getFlagPresenceInGame() != InGamePlayerStatus::FLAG_PRESENCE_TRUE){
                $gameReady = false;
            }
        }
        if($gameReady){
            // Mercure notification joiningPrivateGame
            $update = new Update(
                '/checkPresenceOfAllPlayers/'.$idGame,
                json_encode([
                    'topic' =>'/checkPresenceOfAllPlayers/'.$idGame,
                    'idGame' => $idGame
                ])
            );
            $this->hub->publish($update);
        }
        return new JsonResponse([
            'gameReady' => $gameReady
        ]);
    }
}
