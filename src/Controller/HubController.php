<?php

namespace App\Controller;

use App\Entity\{Partie,Team};
use App\Form\TeamType;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class HubController extends AbstractController
{
    private $utils;

    public function __construct(Utils $utils)
    {
        $this->utils = $utils;
    }

    #[Route('/partiePrivee', name: 'initPartiePrivee')]
    public function createHub(
        EntityManagerInterface $entityManager
    ): Response
    {
        // Create a game
        $longueurMot = rand(7,10);
        $game = new Partie();
        $game->addIdJoueur($this->getUser());
        $game->setNombreTours(6);
        $game->setMotATrouver($this->utils->getRandomWord($longueurMot));
        $game->setDureeSessionLigne(50);
        $game->setLongueurLignes($longueurMot);
        $entityManager->persist($game);
        $entityManager->flush();
        $id_partie = $game->getId();
        // création de l'équipe bleue
        $blueTeam = new Team();
        $blueTeam->setPartie($game);
        $blueTeam->setCouleur('blue');
        $entityManager->persist($blueTeam);
        // création de l'équipe rouge
        $redTeam = new Team();
        $redTeam->setPartie($game);
        $redTeam->setCouleur('red');
        $entityManager->persist($redTeam);
        $entityManager->flush();
        // Subscribe a topic
        return $this->redirectToRoute('partiePrivee', ['id_partie' => $id_partie], 301);
    }

    #[Route('/partiePrivee/{id_partie}', name: 'partiePrivee')]
    public function renderHub(EntityManagerInterface $entityManager, Request $request, $id_partie): Response
    {

        //--- Récupération de la partie
        $game = $entityManager->getRepository(Partie::class)->find($id_partie);
        //--- Formulaire Team
        $teamForm = $this->createForm(TeamType::class);
        $teamForm->get('partie')->setData($id_partie);
        $teamForm->handleRequest($request);
        //--- Récupération des équipes
        $tableTeam = $entityManager->getRepository(Team::class)->findBy(['partie'=>$id_partie]);
        if ($teamForm->isSubmitted() && $teamForm->isValid()) {
            // On retire le joueur courant des équipes rejointes précédemment
            $newArrayTeam = [];
            foreach ($tableTeam as $team){
                $team->removeJoueur($this->getUser());
                $entityManager->persist($team);
                $newArrayTeam[$team->getCouleur()] = $team;
            }
            $tableTeam = $newArrayTeam;
            // Récupération des équipes liées à la partie
            if($teamForm->get('team_blue')->isClicked()){
                // Ajout du joueur courant dans l'équipe
                $tableTeam['blue']->addJoueur($this->getUser());
                $entityManager->persist($tableTeam['blue']);
            } else if ($teamForm->get('team_red')->isClicked()){
                $tableTeam['red']->addJoueur($this->getUser());
                $entityManager->persist($tableTeam['red']);
            }
            $entityManager->flush();
            // Notification Mercure contenant l'id du joueur actuel
            $update = new Update(
                '/hub/prive'.$game->getId(),
                json_encode([
                    'topic'      => '/hub/prive'.$game->getId(),
                    'new_player' => $this->getUser()->getId()
                ])
            );
        }

        // Regroupe tous les joueurs dans un tableau
        $playersArray = self::getPlayerArray($tableTeam);
        // Récupère les contacts de l'utilisateur qui ne sont pas présents dans la partie
        $contactList = self::getContactList($playersArray);

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
     * @return mixed
     */
    public function getContactList($playersArray): mixed
    {
        $contactList = $this->utils->getContacts($this->getUser()->getId());

        foreach ($playersArray as $player){
            foreach ($contactList as $index => $contact){
                if($contact->getId() == $player->getId()){
                    unset($contactList[$index]);
                }
            }
        }
        return $contactList;
    }
}
