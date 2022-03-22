<?php

namespace App\Controller;

use App\Entity\{InvitationToPlay, Partie, Team, Utilisateur};
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
        $longueurMot = rand(7,10);
        $game = new Partie();
        $game->addIdJoueur($this->getUser());
        $game->setNombreTours(6);
        $game->setMotATrouver($this->utils->getRandomWord($longueurMot));
        $game->setDureeSessionLigne(50);
        $game->setLongueurLignes($longueurMot);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
        $id_partie = $game->getId();
        // création de l'équipe bleue
        $blueTeam = new Team();
        $blueTeam->setPartie($game);
        $blueTeam->setCouleur('blue');
        $this->entityManager->persist($blueTeam);
        // création de l'équipe rouge
        $redTeam = new Team();
        $redTeam->setPartie($game);
        $redTeam->setCouleur('red');
        $this->entityManager->persist($redTeam);
        $this->entityManager->flush();
        // Subscribe a topic
        return $this->redirectToRoute('hubPrive', ['id_partie' => $id_partie], 301);
    }

    /**
     * @param Request $request
     * @param $id_partie
     * @return Response
     */
    #[Route('/hubPrive/{id_partie}', name: 'hubPrive')]
    public function renderHub(Request $request, $id_partie): Response
    {

        //--- Récupération de la partie
        $game = $this->entityManager->getRepository(Partie::class)->find($id_partie);
        //--- Formulaire Team
        $teamForm = $this->createForm(TeamType::class);
        $teamForm->get('partie')->setData($id_partie);
        $teamForm->handleRequest($request);
        //--- Récupération des équipes
        $tableTeam = $this->entityManager->getRepository(Team::class)->findBy(['partie'=>$id_partie]);
        $newArrayTeam = [];
        foreach ($tableTeam as $team){
            $newArrayTeam[$team->getCouleur()] = $team;
        }
        $tableTeam = $newArrayTeam;
        if ($teamForm->isSubmitted() && $teamForm->isValid()) {
            // On retire le joueur courant des équipes rejointes précédemment
            $newArrayTeam = [];
            foreach ($tableTeam as $team){
                $team->removeJoueur($this->getUser());
                $this->entityManager->persist($team);
                $newArrayTeam[$team->getCouleur()] = $team;
            }
            $tableTeam = $newArrayTeam;
            // Récupération des équipes liées à la partie
            if($teamForm->get('team_blue')->isClicked()){
                // Ajout du joueur courant dans l'équipe
                $tableTeam['blue']->addJoueur($this->getUser());
                $this->entityManager->persist($tableTeam['blue']);
            } else if ($teamForm->get('team_red')->isClicked()){
                $tableTeam['red']->addJoueur($this->getUser());
                $this->entityManager->persist($tableTeam['red']);
            }
            $this->entityManager->flush();
            // Notification Mercure contenant l'id du joueur actuel
            $update = new Update(
                '/hub/prive/'.$id_partie,
                json_encode([
                    'topic' =>'/hub/prive/'.$id_partie,
                    'url_partie' => $this->generateUrl('hubPrive', ['id_partie' => $id_partie])
                ])
            );
            $this->hub->publish($update);
        }

        // Regroupe tous les joueurs dans un tableau
        $playersArray = self::getPlayerArray($tableTeam);
        // Récupère les contacts de l'utilisateur qui ne sont pas présents dans la partie
        $contactList = self::getContactList($playersArray, $id_partie);

        // Récupération de tous les joueurs liés à la partie
        return $this->render('hub/index.html.twig', [
            'controller_name' => 'HubController',
            'teamForm'        => $teamForm->createView(),
            'id_partie'       => $id_partie,
            'tablePlayer'     => $tableTeam,
            'contactList'     => $contactList
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/reloadPlayerList', name: 'reloadPlayerList')]
    public function reloadPlayerList(Request $request): Response
    {
        $id_partie = $request->request->get('id_partie');
        //--- Récupération des équipes
        $tableTeam = $this->entityManager->getRepository(Team::class)->findBy(['partie'=>$id_partie]);
        $ArrayTeam = [];
        foreach ($tableTeam as $team){
            foreach ($team->getJoueurs() as $joueur){
                $avatar = null;
                if($joueur->getAvatar()){
                    $avatar = $this->cacheManager
                        ->getBrowserPath(
                            'images/avatars/'.$joueur->getAvatar()->getAvatar(),
                            'my_thumb_filter'
                        );
                }
                $ArrayTeam[$team->getCouleur()][] = [
                    'id_player' => $joueur->getId(),
                    'pseudo'    => $joueur->getPseudo(),
                    'avatar'    => $avatar
                ];
            }
        }
        return new Response(json_encode($ArrayTeam));
    }

    /**
     * @param $tableTeam
     * @return array
     */
    public function getPlayerArray($tableTeam): array
    {
        $playersArray = [];
        foreach ($tableTeam as $team){
            foreach ($team->getJoueurs() as $joueur){
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
     * @param $id_partie
     * @return mixed
     */
    public function getContactList($playersArray, $id_partie): mixed
    {
        $contactList = $this->utils->getContactsOnline($this->getUser()->getId());

        foreach ($playersArray as $player){
            foreach ($contactList as $index => $contact){
                // Si le joueur a rejoint une équipe
                if($contact->getId() == $player->getId()){
                    unset($contactList[$index]);
                }
                // Si le joueur a rejoint la partie
                $checkPresence = $this->entityManager->getRepository(InvitationToPlay::class)->findBy([
                    'partie' => $id_partie,
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
        $id_user = $request->request->get('id_user');
        $id_partie = $request->request->get('id_partie');
        // Send topic
        $update = new Update(
            '/invitationPrivateGame/'.$id_user,
            json_encode([
                'topic' => '/invitationPrivateGame/'.$id_user,
                'idPartie' => $id_partie
            ])
        );
        $hub->publish($update);
        // Save invitation
        $invitationToPlay = $this->entityManager->getRepository(InvitationToPlay::class)
            ->findOneBy([
                'partie' => $id_partie,
                'invitedUser'   => $id_user,
                'userWhoInvites' => $this->getUser()->getId()
            ]);
        if(!$invitationToPlay){
            $partie = $this->entityManager->getRepository(Partie::class)->findOneBy(['id' => $id_partie]);
            $invitedUser = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['id' => $id_user]);
            $invitation = new InvitationToPlay();
            $invitation->setPartie($partie);
            $invitation->setInvitedUser($invitedUser);
            $invitation->setUserWhoInvites($this->getUser());
            $invitation->setFlagEtat(InvitationToPlay::DEMANDE_PARTIE_EN_ATTENTE);
            $this->entityManager->persist($invitation);
            $this->entityManager->flush();
        }


        return new JsonResponse($id_user, 200);
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
                'partie' => $game,
                'invitedUser'   => $this->getUser()->getId(),
                'userWhoInvites' => $userWhoInvites
            ]);
        $flag_etat = "";
        $arrayResponse = [];
        if($invitationToPlay && ($choice=="accepte" || $choice == "refuse") ){
            if($choice == "accepte"){
                $flag_etat = InvitationToPlay::DEMANDE_PARTIE_ACCEPTEE;
                $arrayResponse['code'] = "ok";
                $arrayResponse['url'] = $this->generateUrl('hubPrive', ['id_partie' => $game]);

            }
            else{
                $flag_etat = InvitationToPlay::DEMANDE_PARTIE_REFUSEE;
                $arrayResponse['code'] = "nok";
            }
            $invitationToPlay->setFlagEtat($flag_etat);
            $entityManager->persist($invitationToPlay);
            $entityManager->flush();
        }
        $arrayResponse['id_partie'] = $game;
        $arrayResponse['userWhoInvites'] = $userWhoInvites;

        return new Response(json_encode($arrayResponse));
    }
}
