<?php

namespace App\Controller;

use App\Entity\{InvitationToPlay, Game, Team, User};
use App\Form\TeamType;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, RedirectResponse, Request, Response};
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use \Liip\ImagineBundle\Imagine\Cache\CacheManager;

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
        $wordLength = rand(7,10);
        $game = new Game();
        $game->addPlayer($this->getUser());
        $game->setNumberOfRounds(6);
        $game->setWordToFind($this->utils->getRandomWord($wordLength));
        $game->setLineSessionTime(50);
        $game->setLineLength($wordLength);
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
        // Subscribe a topic
        return $this->redirectToRoute('hubPrive', ['idGame' => $idGame], 301);
    }

    /**
     * @param Request $request
     * @param $idGame
     * @return Response
     */
    #[Route('/hubPrive/{idGame}', name: 'hubPrive')]
    public function renderHub(Request $request, $idGame): Response
    {

        //--- Recovery of the game
        $game = $this->entityManager->getRepository(Game::class)->find($idGame);
        //--- Form Team
        $teamForm = $this->createForm(TeamType::class);
        $teamForm->get('partie')->setData($idGame);
        $teamForm->handleRequest($request);
        //--- Recovery of the teams
        $tableTeam = $this->entityManager->getRepository(Team::class)->findBy(['game'=>$idGame]);
        $newArrayTeam = [];
        foreach ($tableTeam as $team){
            $newArrayTeam[$team->getColor()] = $team;
        }
        $tableTeam = $newArrayTeam;
        if ($teamForm->isSubmitted() && $teamForm->isValid()) {
            // The current player is removed from the previously joined teams
            $newArrayTeam = [];
            foreach ($tableTeam as $team){
                $team->removePlayer($this->getUser());
                $this->entityManager->persist($team);
                $newArrayTeam[$team->getColor()] = $team;
            }
            $tableTeam = $newArrayTeam;
            // Recovery of the teams related to the game
            if($teamForm->get('team_blue')->isClicked()){
                // Ajout du joueur courant dans l'équipe
                $tableTeam['blue']->addPlayer($this->getUser());
                $this->entityManager->persist($tableTeam['blue']);
            } else if ($teamForm->get('team_red')->isClicked()){
                $tableTeam['red']->addPlayer($this->getUser());
                $this->entityManager->persist($tableTeam['red']);
            }
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
            'controller_name' => 'HubController',
            'teamForm'        => $teamForm->createView(),
            'idGame'       => $idGame,
            'tablePlayer'     => $tableTeam,
            'contactList'     => $contactList
        ]);
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
