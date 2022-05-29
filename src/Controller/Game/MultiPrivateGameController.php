<?php

namespace App\Controller\Game;

use App\Entity\Game;
use App\Entity\InGamePlayerStatus;
use App\Entity\Team;
use App\Entity\User;
use App\Service\GameManagerLineService;
use App\Service\Utils;
use JetBrains\PhpStorm\ArrayShape;
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
            // LOGS
            $this->utils->addLogHandlers("MultiPrivateGame_" . $idGame);
            $this->utils->addLog(
                Utils::LOG_LEVEL_INFO,
                "[REQUEST] otsum(" . $idGame . ") | game created ",
                [
                    "userId" => $this->getUser()->getId()
                ]
            );
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
        $playerStatus->setFlagActualPlayer(InGamePlayerStatus::FLAG_ACTUAL_PLAYER_FALSE);
        $this->entityManager->persist($playerStatus);
        $this->entityManager->flush();

        return $this->render('otsum/multiPrivateGame.html.twig',[
            "firstLetter" => $game->getWordToFind()[0],
            "idGame"      => $game->getId(),
            "difficulty"  => $game->getLineLength(),
            "chronoType"  => $game->getChronoType(),
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

        $this->utils->addLogHandlers("MultiPrivateGame_" . $idGame);
        $this->utils->addLog(
            Utils::LOG_LEVEL_INFO,
            "[REQUEST] joiningPrivateGame()",
            [
                "userId"=> $idPlayer
            ]
        );

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

        $this->utils->addLog(
            Utils::LOG_LEVEL_INFO,
            "[EVENT  ] joiningPrivateGame",
            [
                "userId"=> $idPlayer
            ]
        );

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
        $response = 404;
        if($gameReady){
            if(!$game->getStartDate()){
                $this->utils->addLogHandlers("MultiPrivateGame_" . $idGame);
                $this->utils->addLog(
                    Utils::LOG_LEVEL_INFO,
                    "[REQUEST] checkPresenceOfAllPlayers()",
                    [
                        "userId" => $this->getUser()->getId()
                    ]
                );
                // Setting actual player
                $this->setFirstPlayer($inGamePlayerStatuses);
                $currentPlayer = $this->getActualPlayer($inGamePlayerStatuses);
                if($currentPlayer){
                    $game->setCurrentPlayer($currentPlayer);
                }
                $game->setStartDate(new \DateTimeImmutable());
                $this->entityManager->persist($game);
                $this->entityManager->flush();
                $this->getActualPlayer($inGamePlayerStatuses);

                // Mercure notification joiningPrivateGame
                $update = new Update(
                    '/checkPresenceOfAllPlayers/'.$idGame,
                    json_encode([
                        'topic' =>'/checkPresenceOfAllPlayers/'.$idGame,
                        'actualPlayer' => $game->getCurrentPlayer()?->getId(),
                        'idGame' => $idGame
                    ])
                );
                $this->hub->publish($update);

                $this->utils->addLogHandlers("MultiPrivateGame_" . $idGame);
                $this->utils->addLog(
                    Utils::LOG_LEVEL_INFO,
                    "[EVENT  ] checkPresenceOfAllPlayers | game just started",
                    [
                        "userId" => $this->getUser()->getId()
                    ]
                );
                $response = "gameJustStarted";
            } else {
                $this->utils->addLogHandlers("MultiPrivateGame_" . $idGame);
                $this->utils->addLog(
                    Utils::LOG_LEVEL_INFO,
                    "[EVENT  ] checkPresenceOfAllPlayers | game already started",
                    [
                        "userId" => $this->getUser()->getId()
                    ]
                );
                $response = "gameAlreadyStarted";
            }
        }
        return new JsonResponse([
            'actualTeam'   => $this->getPlayerTeam($game)->getColor(),
            'actualPlayer' => $game->getCurrentPlayer()?->getId(),
            'status'       => $response,
            'gameReady'       => $gameReady,
        ]);
    }

    /**
     * Display the actual game grid
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/displayActualGrid', name: 'displayActualGrid')]
    public function displayActualGrid(Request $request): JsonResponse
    {
        $idGame   = $request->request->get('idGame');
        $game = $this->entityManager->getRepository(Game::class)->find($idGame);
        $arrayGrid = [];

        foreach ($game->getLines() as $indexLine => $line){
            foreach ($line->getCells() as $indexCell => $cell){
                $arrayGrid[$indexLine][$indexCell] = [
                    "value" => $cell->getValeur(),
                    "test" => $cell->getFlagTestee(),
                    "presence" => $cell->getFlagPresente(),
                    "placement" => $cell->getFlagPlacee(),
                ];
            }
        }

        //LOG
        $this->utils->addLogHandlers("MultiPrivateGame_" . $game->getId());
        $this->utils->addLog(
            Utils::LOG_LEVEL_INFO,
            "[REQUEST] displayActualGrid()",
            [
                "userId" => $this->getUser()->getId(),
                'numberOfRoundPlayed' => $game->getNumberOfRoundsPlayed(),
                'difficulty'          => $game->getLineLength(),
                'wordToFind'          => trim($game->getWordToFind())
            ]
        );

        return new JsonResponse([
            'arrayGrid' => $arrayGrid,
            'numberOfRoundPlayed' => $game->getNumberOfRoundsPlayed(),
            'arrayMajKeyboard'    => $this->gameManagerService->gameManagerKeyboardService->updateKeyboardKeys($idGame),
            'difficulty'          => $game->getLineLength(),
            'wordToFind'          => trim($game->getWordToFind())
        ]);
    }

    /**
     * Set randomly the first gamer in the game
     *
     * @param $inGamePlayerStatuses
     * @return void
     */
    public function setFirstPlayer($inGamePlayerStatuses): void
    {
        $actualPlayer = rand(1, count($inGamePlayerStatuses));
        foreach ($inGamePlayerStatuses as $index => $inGamePlayerStatus){
            if ($actualPlayer == $index + 1 ){
                $inGamePlayerStatus->setFlagActualPlayer(InGamePlayerStatus::FLAG_ACTUAL_PLAYER_TRUE);
                $this->entityManager->persist($inGamePlayerStatus);
                $this->entityManager->flush();
                //LOG
                $this->utils->addLogHandlers("MultiPrivateGame_" . $inGamePlayerStatus->getRelatedGame()->getId());
                $this->utils->addLog(
                    Utils::LOG_LEVEL_INFO,
                    "[REQUEST] setFirstPlayer()",
                    [
                        "userId" => $this->getUser()->getId(),
                        'actualPlayerId' => $inGamePlayerStatus->getUser()->getId()
                    ]
                );
            }
        }
    }

    /**
     * Set randomly the first gamer in the game
     *
     * @param InGamePlayerStatus $inGamePlayerStatus
     * @param $game
     * @return array
     */
    #[ArrayShape([
        'color' => "int|null",
        'flagActualPlayer' => "int|null",
        'response' => "bool"
    ])]
    public function majCurrentPlayer(InGamePlayerStatus $inGamePlayerStatus, $game): array
    {
        $response = true;
        if($inGamePlayerStatus->getFlagActualPlayer() == InGamePlayerStatus::FLAG_ACTUAL_PLAYER_FALSE){
            $inGamePlayerStatus->setFlagActualPlayer(InGamePlayerStatus::FLAG_ACTUAL_PLAYER_TRUE);
            $this->entityManager->persist($inGamePlayerStatus);
            $game->setCurrentPlayer($inGamePlayerStatus->getUser());
            $this->entityManager->persist($game);
        } elseif ($inGamePlayerStatus->getFlagActualPlayer() == InGamePlayerStatus::FLAG_ACTUAL_PLAYER_TRUE){
            $inGamePlayerStatus->setFlagActualPlayer(InGamePlayerStatus::FLAG_ACTUAL_PLAYER_FALSE);
            $this->entityManager->persist($inGamePlayerStatus);
        } else {
            $response = false;
        }
        $this->entityManager->flush();

        //LOG
        $this->utils->addLogHandlers("MultiPrivateGame_" . $game->getId());
        $this->utils->addLog(
            Utils::LOG_LEVEL_INFO,
            "[REQUEST] majCurrentPlayer()",
            [
                "userId" => $this->getUser()->getId(),
                'flagActualPlayer' => $inGamePlayerStatus->getFlagActualPlayer()
            ]
        );

        return [
            'color' => $inGamePlayerStatus->getUser()->getId(),
            'flagActualPlayer' => $inGamePlayerStatus->getFlagActualPlayer(),
            'response' => $response
        ];
    }

    /**
     * Get actual player
     *
     * @param $inGamePlayerStatuses
     * @return User|null
     */
    public function getActualPlayer($inGamePlayerStatuses): ?User
    {
        $currentPlayer = null;
        foreach ($inGamePlayerStatuses as $inGamePlayerStatus){
            if ($inGamePlayerStatus->getFlagActualPlayer() == InGamePlayerStatus::FLAG_ACTUAL_PLAYER_TRUE)
            {
                $currentPlayer = $inGamePlayerStatus->getUser();
            }
        }
        return $currentPlayer;
    }

    /**
     * Get player team
     *
     * @param $game
     * @return Team|null
     */
    public function getPlayerTeam($game): ?Team
    {
        $response = null;
        $teams =  $this->entityManager->getRepository(Team::class)->findBy([
            "game" => $game
        ]);
        if($teams){
            foreach ($teams as $team){
                if ($team->playerExists($game->getCurrentPlayer())){
                    $response = $team;
                }
            }
        }
        return $response;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/startChrono', name: 'startChrono')]
    public function startChrono(Request $request): JsonResponse
    {
        $idGame   = $request->request->get('idGame');

        $this->utils->addLogHandlers("MultiPrivateGame_" . $idGame);
        $this->utils->addLog(
            Utils::LOG_LEVEL_INFO,
            "[REQUEST] startChrono()",
            []
        );

        $game = $this->entityManager->getRepository(Game::class)->find($idGame);
        $lastLine = $game->getLines()->get($game->getNumberOfRoundsPlayed());
        if(!$lastLine->getEndDate() || $request->request->get('restart')){
            $dateNow = new \DateTime();
            $endDate = $dateNow->modify("+31 seconds");
            $lastLine->setEndDate($endDate);
            $this->entityManager->persist($lastLine);
            $this->entityManager->flush();
        } else {
            $endDate = $lastLine->getEndDate();
        }

        // Mercure notification startChrono
        $update = new Update(
            '/startChrono/'.$idGame,
            json_encode([
                'topic'         =>'/startChrono/'.$idGame,
                'currentPlayer' =>$game->getCurrentPlayer()->getId(),
                'arrayChrono'   => [
                    'year'     => $endDate->format("Y"),
                    'month'    => $endDate->format("n"),
                    'day'      => $endDate->format("j"),
                    'hours'    => $endDate->format("G"),
                    'minutes'  => $endDate->format("i"),
                    'seconds'  => $endDate->format("s"),
                ]
            ])
        );
        $this->hub->publish($update);

        $this->utils->addLogHandlers("MultiPrivateGame_" . $idGame);
        $this->utils->addLog(
            Utils::LOG_LEVEL_INFO,
            "[EVENT  ] startChrono",
            [
                "userId" => $this->getUser()->getId(),
                "endDate" => $lastLine->getEndDate()
            ]
        );

        return new JsonResponse(200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/retrieveChrono', name: 'retrieveChrono')]
    public function retrieveChrono(Request $request): JsonResponse
    {
        $idGame   = $request->request->get('idGame');
        $idUser   = $request->request->get('idUser');
        $this->utils->addLogHandlers("MultiPrivateGame_" . $idGame);
        $this->utils->addLog(
            Utils::LOG_LEVEL_INFO,
            "[REQUEST] retrieveChrono()",
            []
        );
        $game = $this->entityManager->getRepository(Game::class)->find($idGame);
        $lastLine = $game->getLines()->get($game->getNumberOfRoundsPlayed());
        $arrayChrono = null;
        if($lastLine){
            $endDate = $lastLine->getEndDate();
            $arrayChrono   = [
                'year'     => $endDate->format("Y"),
                'month'    => $endDate->format("n"),
                'day'      => $endDate->format("j"),
                'hours'    => $endDate->format("G"),
                'minutes'  => $endDate->format("i"),
                'seconds'  => $endDate->format("s"),
            ];
        }


        // Mercure notification retrieveChrono
        $update = new Update(
            '/retrieveChrono/'.$idGame.'/'.$idUser,
            json_encode([
                'topic'         =>'/retrieveChrono/'.$idGame.'/'.$idUser,
                'currentPlayer' =>$game->getCurrentPlayer()->getId(),
                'arrayChrono'   => $arrayChrono

            ])
        );
        $this->hub->publish($update);

        $this->utils->addLogHandlers("MultiPrivateGame_" . $idGame);
        $this->utils->addLog(
            Utils::LOG_LEVEL_INFO,
            "[EVENT  ] retrieveChrono",
            [
                "userId" => $this->getUser()->getId(),
                "endDate" => $lastLine?->getEndDate()
            ]
        );

        return new JsonResponse(200);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/endOfCountDown', name: 'endOfCountDown')]
    public function endOfCountDown(Request $request): JsonResponse
    {
        $idGame   = $request->request->get('idGame');
        $game = $this->entityManager->getRepository(Game::class)->find($idGame);
        $inGamePlayerStatuses = $game->getinGamePlayerStatuses();
        $numberOfTry = $request->request->get('currentLineNumber');
        //LOG
        $this->utils->addLogHandlers("MultiPrivateGame_" . $idGame);
        $this->utils->addLog(
            Utils::LOG_LEVEL_INFO,
            "[REQUEST] endOfCountDown()",
            [
                "userId" => $this->getUser()->getId(),
                "numberOfTry" => $numberOfTry
            ]
        );

        foreach ($inGamePlayerStatuses as $inGamePlayerStatus){
            $this->majCurrentPlayer($inGamePlayerStatus, $game);
        }

        $lineUpdated = $this->gameManagerService->updateLine(
            $idGame,
            $numberOfTry,
            $request->request->get('mot'),
            GameManagerLineService::LAST_TRIED_WORDS_STATUS_INVALID
        );

        // Word Founded
        if(trim($request->request->get('mot')) === $game->getWordToFind()){
            $lineUpdated['victory'] = true;
        } else if(
            // If you made it to the last round and the word was not found
            $game->getNumberOfRounds() == $game->getNumberOfRoundsPlayed() &&
            $lineUpdated['victory'] == false
        )
        {
            $lineUpdated['wordToFind'] = $game->getWordToFind();
        }

        // Mercure notification displayNewLine
        $update = new Update(
            '/displayNewLine/'.$idGame,
            json_encode([
                'topic'        => '/displayNewLine/'.$idGame,
                'lineUpdated'  => $lineUpdated,
                'actualPlayer' => $game->getCurrentPlayer()->getId(),
                'chronoType'   => $game->getChronoType(),
                'actualTeam'   => $this->getPlayerTeam($game)->getColor(),
                'difficulty'   => $game->getLineLength()
            ])
        );
        $this->hub->publish($update);

        //LOG
        $this->utils->addLogHandlers("MultiPrivateGame_" . $idGame);
        $this->utils->addLog(
            Utils::LOG_LEVEL_INFO,
            "[EVENT  ] displayNewLine ",
            [
                "userId" => $this->getUser()->getId(),
                "numberOfTry" => $numberOfTry
            ]
        );

        return new JsonResponse([
            200
        ]);
    }

    /**
     * Get the state of last played line ine game,
     * then we update it and return it with other information about the game
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/updateLineMultiplayer', name: 'updateLineMultiplayer')]
    function updateLineMultiplayer(Request $request): JsonResponse
    {


        $idGame = $request->request->get('idGame');
        $game = $this->entityManager->getRepository(Game::class)->find($idGame);

        //LOG
        $this->utils->addLogHandlers("MultiPrivateGame_" . $idGame);
        $this->utils->addLog(
            Utils::LOG_LEVEL_INFO,
            "[REQUEST] updateLineMultiplayer()",
            []
        );

        if($request->request->get('lastTriedWordsStatus') == GameManagerLineService::LAST_TRIED_WORDS_STATUS_VALID)
        {
            $numberOfTry = $request->request->get('currentLineNumber') + 1;
        } else {
            $numberOfTry = $request->request->get('currentLineNumber');
            $inGamePlayerStatuses = $game->getinGamePlayerStatuses();
            foreach ($inGamePlayerStatuses as $inGamePlayerStatus){
                $this->majCurrentPlayer($inGamePlayerStatus, $game);
            }
        }
        $lineUpdated = $this->gameManagerService->updateLine(
            $idGame,
            $numberOfTry,
            $request->request->get('mot'),
            $request->request->get('lastTriedWordsStatus')
        );
        // If you made it to the last round and the word was not found
        if(trim($request->request->get('mot')) === $game->getWordToFind() ){
            $lineUpdated['victory'] = true;
        } else if(
            $game->getNumberOfRounds() == $game->getNumberOfRoundsPlayed() &&
            $lineUpdated['victory'] == false
        )
        {
            $lineUpdated['wordToFind'] = $game->getWordToFind();
        }

        // Mercure notification displayNewLine
        $update = new Update(
            '/displayNewLine/'.$idGame,
            json_encode([
                'topic'        => '/displayNewLine/'.$idGame,
                'lineUpdated'  => $lineUpdated,
                'actualPlayer' => $game->getCurrentPlayer()->getId(),
                'chronoType'   => $game->getChronoType(),
                'actualTeam'   => $this->getPlayerTeam($game)->getColor(),
                'difficulty'   => $game->getLineLength()
            ])
        );
        $this->hub->publish($update);

        //LOG
        $this->utils->addLogHandlers("MultiPrivateGame_" . $idGame);
        $this->utils->addLog(
            Utils::LOG_LEVEL_INFO,
            "[EVENT  ] displayNewLine",
            [
                "userId" => $this->getUser()->getId(),
                "numberOfTry" => $numberOfTry,
                'actualPlayer' => $game->getCurrentPlayer()->getId(),
                'chronoType'   => $game->getChronoType(),
                'actualTeam'   => $this->getPlayerTeam($game)->getColor(),
                'difficulty'   => $game->getLineLength()
            ]
        );

        return new JsonResponse([
            200
        ]);
    }

    #[Route('/sendCurrentKey', name: 'sendCurrentKey')]
    public function sendCurrentKey(Request $request): JsonResponse
    {
        $idGame     = $request->request->get('idGame');
        $currentKey = $request->request->get('currentKey');

        // Mercure notification displayNewLine
        $update = new Update(
            '/currentKey/'.$idGame,
            json_encode([
                'topic'        => '/currentKey/'.$idGame,
                'actualPlayer' => $this->getUser()->getId(),
                'currentKey'   => $currentKey
            ])
        );
        $this->hub->publish($update);

        //LOG
        $this->utils->addLogHandlers("MultiPrivateGame_" . $idGame);
        $this->utils->addLog(
            Utils::LOG_LEVEL_INFO,
            "[EVENT  ] sendCurrentKey",
            [
                "userId" => $this->getUser()->getId(),
                "currentKey" => $currentKey
            ]
        );

        return new JsonResponse([
            200
        ]);
    }
}
