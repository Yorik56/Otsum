<?php

namespace App\Controller\Game;

use App\Form\ChronoType;
use App\Form\LaunchGameType;
use App\Form\VersusType;
use App\Entity\{Cell, Game, InGamePlayerStatus, InvitationToPlay, Line, Team, User};
use App\Form\TeamType;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Form\FormError;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

class HubController extends AbstractController
{
    private Utils $utils;
    private EntityManagerInterface $entityManager;
    private HubInterface $hub;
    private CacheManager $cacheManager;

    public function __construct(CacheManager $cacheManager, HubInterface $hub, Utils $utils, EntityManagerInterface $entityManager)
    {
        $this->utils = $utils;
        $this->entityManager = $entityManager;
        $this->hub = $hub;
        $this->cacheManager = $cacheManager;
    }

    #[Route('/hubPrive', name: 'initHubPrive')]
    public function createHub(): Response
    {
        // Create a game
        $numberOfRounds = 6;
        $wordLength = rand(7,10);
        $game = new Game();
        $game->addPlayer($this->getUser());
        $game->setNumberOfRounds($numberOfRounds);
        $game->setNumberOfRoundsPlayed(0);
        $game->setWordToFind($this->utils->getRandomWord($wordLength));
        $game->setLineSessionTime(50);
        $game->setLineLength($wordLength);
        $game = $this->fillGridWithInitialValues($game);
        $game->setFlagTypeOfGame(Game::PRIVATE_MULTPLAYER_GAME);
        $game->setHost($this->getUser());
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $idGame = $game->getId();
        // Creation of the blue team
        $blueTeam = new Team();
        $blueTeam->setGame($game);
        $blueTeam->setColor('blue');
        $this->entityManager->persist($blueTeam);
        // Creation of the red team
        $redTeam = new Team();
        $redTeam->setGame($game);
        $redTeam->setColor('red');
        $this->entityManager->persist($redTeam);
        $this->entityManager->flush();
        // Redirection to the Hub
        return $this->redirectToRoute('hubPrive', ['idGame' => $idGame], 301);
    }

    /*
     * This function creates initial lines and cells of the grid
     * and fill them with initial values
     */
    public function fillGridWithInitialValues($game)
    {
        for($line = 0; $line < $game->getNumberOfRounds(); $line ++){
            $currentLine = new Line($game);
            for($column = 0; $column < $game->getLineLength(); $column++){
                $currentCell = new Cell();
                $currentCell->setFlagTestee(Cell::FLAG_TEST_FALSE);
                $currentCell->setFlagPresente(Cell::FLAG_PRESENCE_FALSE);
                $currentCell->setFlagPlacee(Cell::FLAG_PLACEMENT_FALSE);
                $currentCell->setPosition($column);
                $currentCell->setLigne($currentLine);
                if($line == 0 && $column == 0){
                    $currentCell->setValeur(
                        substr($game->getWordToFind(), 0, 1)
                    );
                }else{
                    $currentCell->setValeur(".");
                }
                // Add new cell to the current line
                $this->entityManager->persist($currentCell);
                $currentLine->addCell($currentCell);
            }
            // Add new line to the game
            $this->entityManager->persist($currentLine);
            $game->addLine($currentLine);
        }
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        return $game;
    }

    /**
     * @param Request $request
     * @param $idGame
     * @return Response
     */
    #[Route('/hubPrive/{idGame}', name: 'hubPrive')]
    public function renderHub(Request $request, $idGame): Response
    {
        //--- Retrieve the game
        $game = $this->entityManager->getRepository(Game::class)->find($idGame);
        // Form Team
        $teamForm = $this->createForm(TeamType::class);
        $teamForm->get('partie')->setData($idGame);
        $teamForm->handleRequest($request);
        // Form LaunchGame
        $launchGameForm = $this->createForm(LaunchGameType::class);
        $launchGameForm->get('idGame')->setData($idGame);
        $launchGameForm->handleRequest($request);
        // Form VersusType Choice
        $versusTypeForm = $this->createForm(VersusType::class, null, [
            'action' => $this->generateUrl('versusTypeChoice')
        ]);
        $versusTypeForm->get('idGame')->setData($idGame);
        // Form ChronoType Choice
        $chronoTypeForm = $this->createForm(ChronoType::class, null, [
            'action' => $this->generateUrl('chronoTypeChoice')
        ]);
        $chronoTypeForm->get('idGame')->setData($idGame);
        if ($game->getChronoType()){
            $chronoTypeForm->get('chronoType')->setData($game->getChronoType());
        }

        //--- Recovery of the teams
        //TODO Group By Team Color
        $tableTeam = $this->entityManager->getRepository(Team::class)->findBy(['game'=>$idGame]);
        // Launch Game if all is Ready
        if ($launchGameForm->isSubmitted() && $launchGameForm->isValid()) {
            $validTeams = true;
            // Checking the type of game
            if(!$game->getVersusType() || $game->getVersusType() == 0){
                $validTeams = false;
                // An error is displayed
                $error = new FormError(
                    "Choisissez un type de partie."
                );
                $launchGameForm->addError($error);
            } else {
                // Checking the number of players in each team
                foreach ($tableTeam as $team){
                    // If the number of players on the team is insufficient,
                    if($team->getNumberOfPlayer() < $game->getVersusType()){
                        $validTeams = false;
                        // An error is displayed
                        $error = new FormError(
                            "Le nombre de joueurs de l'équipe ".$team->getColor()." est insuffisant."
                        );
                        $launchGameForm->addError($error);
                    }
                    // If the number of players in the team is too high,
                    if($team->getNumberOfPlayer() > $game->getVersusType()){
                        $validTeams = false;
                        // An error is displayed
                        $error = new FormError(
                            "Le nombre de joueurs de l'équipe ".$team->getColor()." est trop élevé."
                        );
                        $launchGameForm->addError($error);
                    }
                }
            }
            if($validTeams){
                return $this->redirectToRoute(
                    'otsum.multiPrivateGame', [
                        'idGame' => $versusTypeForm->get('idGame')->getData()]
                );
            }
        }

        $newArrayTeam = [];
        foreach ($tableTeam as $team){
            $newArrayTeam[$team->getColor()] = $team;
        }
        $tableTeam = $newArrayTeam;
        if ($teamForm->isSubmitted() && $teamForm->isValid()) {
            // The current player is removed from the previously joined teams
            $newArrayTeam = [];
            foreach ($tableTeam as $team){
                if($team->playerExists($this->getUser())){
                    $team->removePlayer($this->getUser());
                    $team->setNumberOfPlayer($team->getNumberOfPlayer()-1);
                }
                $this->entityManager->persist($team);
                $newArrayTeam[$team->getColor()] = $team;
            }
            $tableTeam = $newArrayTeam;
            // Recovery of the teams related to the game
            $colorTeam = null;
            if($teamForm->get('team_blue')->isClicked()){
                // Adding the current player in the team
                $tableTeam['blue']->addPlayer($this->getUser());
                $tableTeam['blue']->setNumberOfPlayer($tableTeam['blue']->getNumberOfPlayer()+1);
                $this->entityManager->persist($tableTeam['blue']);
                $colorTeam = "blue";
            } else if ($teamForm->get('team_red')->isClicked()){
                // Adding the current player in the team
                $tableTeam['red']->addPlayer($this->getUser());
                $tableTeam['red']->setNumberOfPlayer($tableTeam['red']->getNumberOfPlayer()+1);
                $this->entityManager->persist($tableTeam['red']);
                $colorTeam = "red";
            }

            //--- Creat the player status
            // Check existence of the player status
            $teamOfTheCurrentUser = $this->entityManager
                ->getRepository(Team::class)
                ->findOneBy([
                    'game'  => $idGame,
                    'color' => $colorTeam
                ]);
            $playerStatus = $this->entityManager
                ->getRepository(InGamePlayerStatus::class)
                ->findOneBy([
                    'user'=>$this->getUser()->getId()
                ]);
            if(!$playerStatus){
                $playerStatus = new InGamePlayerStatus();
                $playerStatus->setUser($this->getUser());
            }
            // Set position in team and presence,
            // also warns that the player is not playing
            $playerStatus->setRelatedGame($game);
            $playerStatus->setFlagPresenceInGame(InGamePlayerStatus::FLAG_PRESENCE_FALSE);
            $playerStatus->setPositionInTeam($teamOfTheCurrentUser->getNumberOfPlayer()+1);
            $playerStatus->setFlagActualPlayer(InGamePlayerStatus::FLAG_ACTUAL_PLAYER_FALSE);
            $this->entityManager->persist($playerStatus);
            $this->entityManager->flush();
            // Mercure notification containing the current player id
            $update = new Update(
                '/hub/prive/'.$idGame,
                json_encode([
                    'topic' =>'/hub/prive/'.$idGame,
                    'url_partie' => $this->generateUrl('hubPrive', ['idGame' => $idGame])
                ])
            );
            $this->hub->publish($update);
        }
        // Groups all players in a table
        $playersArray = self::getPlayerArray($tableTeam);
        // Retrieves the user's contacts that are not present in the game
        $contactList = self::getContactList($playersArray, $idGame);

        // Recovery of all players related to the game
        return $this->render('hub/index.html.twig', [
            'controller_name'  => 'HubController',
            'teamForm'         => $teamForm->createView(),
            'launchGameForm'   => $launchGameForm->createView(),
            'versusTypeForm'   => $versusTypeForm->createView(),
            'chronoTypeForm'   => $chronoTypeForm->createView(),
            'idGame'           => $idGame,
            'host'             => $game->getHost(),
            'tablePlayer'      => $tableTeam,
            'contactList'      => $contactList,
            'game'             => $game,
            'additionalParams' => [
                'idGame' => $idGame
            ]
        ]);
    }

    #[Route('/versusTypeChoice', name: 'versusTypeChoice')]
    function versusTypeChoice(HubInterface $hub, Request $request)
    {
        //--- Form LaunchGame
        $versusTypeForm = $this->createForm(VersusType::class);
        $versusTypeForm->handleRequest($request);

        if ($versusTypeForm->isSubmitted() && $versusTypeForm->isValid()) {
            //--- Retrieve the game
            $game = $this->entityManager->getRepository(Game::class)
                ->find($versusTypeForm->get('idGame')->getData());
            $game->setVersusType($versusTypeForm->get('versusType')->getData());
            $this->entityManager->persist($game);
            $this->entityManager->flush();

            // Send Update Mercure
            $update = new Update(
                '/updateVersusType/'.$versusTypeForm->get('idGame')->getData(),
                json_encode([
                    'topic' => '/updateVersusType/'.$versusTypeForm->get('idGame')->getData(),
                    'versusType'  => Game::VERSUS_TYPE[$game->getVersusType()]
                ])
            );
            $hub->publish($update);
        }
        return $this->redirectToRoute(
            'hubPrive', [
            'idGame' => $versusTypeForm->get('idGame')->getData()]
        );
    }

    #[Route('/chronoTypeChoice', name: 'chronoTypeChoice')]
    function chronoTypeChoice(HubInterface $hub, Request $request)
    {
        //--- Form LaunchGame
        $chronoTypeForm = $this->createForm(ChronoType::class);
        $chronoTypeForm->handleRequest($request);

        if ($chronoTypeForm->isSubmitted() && $chronoTypeForm->isValid()) {
            //--- Retrieve the game
            $game = $this->entityManager->getRepository(Game::class)
                ->find($chronoTypeForm->get('idGame')->getData());
            $game->setChronoType($chronoTypeForm->get('chronoType')->getData());
            $this->entityManager->persist($game);
            $this->entityManager->flush();

            // Send Update Mercure
            $update = new Update(
                '/updateChronoType/'.$chronoTypeForm->get('idGame')->getData(),
                json_encode([
                    'topic' => '/updateChronoType/'.$chronoTypeForm->get('idGame')->getData(),
                    'chronoType'  => $game->getChronoType()
                ])
            );
            $hub->publish($update);
        }
        return $this->redirectToRoute(
            'hubPrive', [
                'idGame' => $chronoTypeForm->get('idGame')->getData()]
        );
    }

    /**
     * Update of the team players
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/reloadPlayerList', name: 'reloadPlayerList')]
    public function reloadPlayerList(Request $request): Response
    {
        $idGame = $request->request->get('idGame');
        //--- Recovery of the teams
        $tableTeam = $this->entityManager->getRepository(Team::class)->findBy(['game'=>$idGame]);
        $ArrayTeam = [];
        foreach ($tableTeam as $team){
            foreach ($team->getPlayers() as $joueur){
                $avatar = null;
                if($joueur->getAvatar()){
                    $avatar = $this->cacheManager
                        ->getBrowserPath(
                            'images/avatars/'.$joueur->getAvatar()->getAvatar(),
                            'my_thumb_filter'
                        );
                }
                $ArrayTeam[$team->getColor()][] = [
                    'id_player' => $joueur->getId(),
                    'pseudo'    => $joueur->getPseudo(),
                    'avatar'    => $avatar
                ];
            }
        }
        return new Response(json_encode($ArrayTeam));
    }

    /**
     * Groups all players in a table
     * @param $tableTeam
     * @return array
     */
    public function getPlayerArray($tableTeam): array
    {
        $playersArray = [];
        foreach ($tableTeam as $team){
            foreach ($team->getPlayers() as $joueur){
                $playersArray[] = $joueur;
            }
        }
        return $playersArray;
    }

    /**
     * Récupère la liste des contacts de l'utilisateur courant
     * Filtre les contacts déjà présents dans la partie
     *
     * @param $playersArray
     * @param $idGame
     * @return mixed
     */
    public function getContactList($playersArray, $idGame): mixed
    {
        $contactList = $this->utils->getContactsOnline($this->getUser()->getId());

        foreach ($playersArray as $player){
            foreach ($contactList as $index => $contact){
                // If the player has joined a team
                if($contact->getId() == $player->getId()){
                    unset($contactList[$index]);
                }
                // If the player has joined the game
                $checkPresence = $this->entityManager->getRepository(InvitationToPlay::class)->findBy([
                    'game' => $idGame,
                    'invitedUser'   => $contact->getId(),
                    'userWhoInvites' => $this->getUser()->getId()
                ]);
                if($checkPresence){
                    unset($contactList[$index]);
                }
            }
        }

        return $contactList;
    }

    #[Route('/inviteUser', name: 'inviteUser')]
    function inviteUser(HubInterface $hub, Request $request)
    {
        $idUser = $request->request->get('id_user');
        $idGame = $request->request->get('idGame');
        // Send topic
        $update = new Update(
            '/invitationPrivateGame/'.$idUser,
            json_encode([
                'topic' => '/invitationPrivateGame/'.$idUser,
                'idPartie' => $idGame
            ])
        );
        $hub->publish($update);
        // Save invitation
        $invitationToPlay = $this->entityManager->getRepository(InvitationToPlay::class)
            ->findOneBy([
                'game' => $idGame,
                'invitedUser'   => $idUser,
                'userWhoInvites' => $this->getUser()->getId()
            ]);
        if(!$invitationToPlay){
            $partie = $this->entityManager->getRepository(Game::class)->findOneBy(['id' => $idGame]);
            $invitedUser = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $idUser]);
            $invitation = new InvitationToPlay();
            $invitation->setGame($partie);
            $invitation->setInvitedUser($invitedUser);
            $invitation->setUserWhoInvites($this->getUser());
            $invitation->setFlagState(InvitationToPlay::REQUEST_GAME_PENDING);
            $this->entityManager->persist($invitation);
            $this->entityManager->flush();
        }


        return new JsonResponse($idUser, 200);
    }

    /**
     * Save the answer to a friend request
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    #[Route('/reponseInvitationToPlay', name: 'reponseInvitationToPlay')]
    public function reponseInvitationToPlay(EntityManagerInterface $entityManager, Request $request): Response
    {
        $choice = $request->query->get('reponse');
        $game    = $request->query->get('game');
        $userWhoInvites = $request->query->get('userWhoInvites');
        //Maj de la demande de contact
        $invitationToPlay = $entityManager->getRepository(InvitationToPlay::class)
            ->findOneBy([
                'game' => $game,
                'invitedUser'   => $this->getUser()->getId(),
                'userWhoInvites' => $userWhoInvites
            ]);
        $arrayResponse = [];
        if($invitationToPlay && ($choice=="accepte" || $choice == "refuse") ){
            if($choice == "accepte"){
                $flag_state = InvitationToPlay::REQUEST_GAME_ACCEPTED;
                $arrayResponse['code'] = "ok";
                $arrayResponse['url'] = $this->generateUrl('hubPrive', ['idGame' => $game]);

            }
            else{
                $flag_state = InvitationToPlay::REQUEST_GAME_REFUSED;
                $arrayResponse['code'] = "nok";
            }
            $invitationToPlay->setFlagState($flag_state);
            $entityManager->persist($invitationToPlay);
            $entityManager->flush();
        }
        $arrayResponse['idGame'] = $game;
        $arrayResponse['userWhoInvites'] = $userWhoInvites;

        return new Response(json_encode($arrayResponse));
    }
}
