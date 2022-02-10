<?php

namespace App\Controller;

use App\Entity\Cellule;
use App\Entity\DemandeContact;
use App\Entity\Ligne;
use App\Entity\Partie;

use App\Entity\Utilisateur;
use App\Form\ContactRequestType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Finder\SplFileInfo;

class AccueilController extends AbstractController {

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/', name: 'accueil')]
    public function index(HubInterface $hub, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Formulaire demande de contact
        $form = $this->createForm(ContactRequestType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pseudo = $form->getData();
            // Recherche de l'utilisateur ciblé
            $utilisateur_cible = $entityManager
                ->getRepository(Utilisateur::class)
                ->findOneBy(['pseudo'=> $pseudo['Pseudo']]);
            // Si l'utilisateur existe
            if($utilisateur_cible){
                // Au cas où l'utilisateur s'envoie une demande à lui-même
                if ($utilisateur_cible->getId() == $this->getUser()->getId()) {
                    $error = new FormError("Vous ne pouvez pas vous ajouter en tant que contact.");
                    $form->addError($error);
                } else {
                    //Enregistrement de la demande de contact
                    $demande_de_contact = new DemandeContact($this->getUser(),$utilisateur_cible);
                    $entityManager->persist($demande_de_contact);
                    $entityManager->flush();
                    //Envois de la notification
                    $update = new Update(
                        '/accueil/notifications/demande_ajout/'.$utilisateur_cible->getId(),
                        json_encode([
                            'topic' => '/accueil/notifications/demande_ajout/'.$utilisateur_cible->getId(),
                            'notification' => "Vous avez une demande d'amis de ".$this->getUser()->getPseudo(),
                            'id_source' => $this->getUser()->getId()
                        ])
                    );
                    $hub->publish($update);
                }
            } else {
                // On prévient le demandeur que le contact recherché n'existe pas
                $error = new FormError("L'utilisateur avec ce pseudo n'existe pas.");
                $form->addError($error);
            }
        }

        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
            'contact_request_form' => $form->createView(),
        ]);
    }

    #[Route('/contact_request', name: 'contact_request')]
    public function contact_request(HubInterface $hub): Response
    {
        $update = new Update(
            '/accueil',
            json_encode(['data' => "je veux savoir qui est co "])
        );
        $hub->publish($update);
        return new Response($this->generateUrl("accueil"));
    }

    #[Route('/respond_contact_request', name: 'respond_contact_request')]
    public function respond_contact_request(HubInterface $hub): Response
    {
        $update = new Update(
            '/accueil',
            json_encode(['data' => "je veux savoir qui est co "])
        );
        $hub->publish($update);
        return new Response($this->generateUrl("accueil"));
    }

    #[Route('/push', name: 'push')]
    public function push(HubInterface $hub): Response
    {
        $update = new Update(
            '/accueil',
            json_encode(['data' => "je veux savoir qui est co "])
        );
        $hub->publish($update);
        return new Response($this->generateUrl("accueil"));
    }

    #[Route('/hub', name: 'hub')]
    public function hub(): Response
    {
        return $this->render('hub/index.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }
    
    #[Route('/otsum/{id}', name: 'otsum')]
    public function otsum($id): Response
    {
        return $this->render('otsum/index.html.twig', [
            'controller_name' => 'AccueilController',
            'id' => $id,
        ]);
    }

    #[Route('/generemot', name: 'genere_mot')]
    function genereUnMot(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        // Paramètres utiles à la construction du mot
        $parametre_longueur_mot = $request->request->get('id');
        $nomsFichiers = [
            7  => 'sept_lettres.txt',
            8  => 'huit_lettres.txt',
            9  => 'neuf_lettres.txt',
            10 => 'dix_lettres.txt',
        ];
        //--- Recherche d'un mot aléatoire
        $projectDir = $this->getParameter('kernel.project_dir');
        $file = $projectDir . '/public/'.$nomsFichiers[$parametre_longueur_mot];
        $file_arr = file($file);
        $num_lines = count($file_arr);
        $last_arr_index = $num_lines - 1;
        $rand_index = rand(0, $last_arr_index);
        $mot_a_trouver = $file_arr[$rand_index];
        //--- Création d'un joueur
        $joueur = $doctrine->getRepository(Utilisateur::class)->find($this->getUser()->getId());
        //--- Création de la partie
        $partie = new Partie($parametre_longueur_mot, trim($mot_a_trouver));
        $partie->setDureeSessionLigne(50);
        $partie->addIdJoueur($joueur);
        $partie->setNombreTours(6);
        $partie->setNombreToursJoues(1);
        $entityManager->persist($partie);
        $entityManager->flush();
        //--- Création d'une ligne
        $ligne = new Ligne($joueur, $partie);
        $entityManager->persist($ligne);
        $entityManager->flush();
        //-- Mise à jour des lettres
        $lettre = new Cellule();
        $lettre->setLigne($ligne);
        $lettre->setValeur($mot_a_trouver[0]);
        $lettre->setPosition(0);
        $lettre->setFlagPlacee(1);
        $lettre->setFlagPresente(1);
        $entityManager->persist($lettre);
        $entityManager->flush();

        // On envoi la première lettre
        return new JsonResponse([$mot_a_trouver[0], $partie->getId()]);
    }

    #[Route('/maj_ligne', name: 'maj_ligne')]
    function maj_ligne(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        //Nombre d'essais
        $essai = $request->request->get('ligne_actuelle');
        $essai ++;
        //ID de la partie
        $id_partie = $request->request->get('id_partie');
        //Dernier essai de mot
        $dernier_essai =  $request->request->get('mot');
        //Partie en cours
        $partie = $doctrine->getRepository(Partie::class)->find($id_partie);
        $mot_a_trouver = $partie->getMotATrouver();
        $partie->setNombreToursJoues($essai);
        $doctrine->getManager()->persist($partie);
        $doctrine->getManager()->flush();

        if($partie->getNombreTours() ==  $partie->getNombreToursJoues()){
            $response = $mot_a_trouver;
        } else {
            // Comparaison de la tentative et du mot à trouver
            $tableau_mot_a_trouver = str_split($mot_a_trouver);
            $tableau_dernier_essai = str_split($dernier_essai);
            //-- Mise à jour de la ligne actuelle
            $ligne_actuelle = [];
            $lettres_valides = 0;
            // Pour chaque lettre du mot à trouver
            foreach ($tableau_mot_a_trouver as $index_mot_a_trouver => $lettre){
                $ligne_actuelle[$index_mot_a_trouver]['valeur'] = $tableau_dernier_essai[$index_mot_a_trouver];
                $ligne_actuelle[$index_mot_a_trouver]['test']   = true;
                $ligne_actuelle[$index_mot_a_trouver]['comparaison']   = $tableau_dernier_essai[$index_mot_a_trouver] . " -> " . $lettre;
                // Si la lettre est dans le mot
                if(preg_match('/'.$tableau_dernier_essai[$index_mot_a_trouver].'/', $mot_a_trouver)){
                    $ligne_actuelle[$index_mot_a_trouver]['presence'] = true;
                    // Si la lettre est bien placée
                    if($tableau_dernier_essai[$index_mot_a_trouver] === $lettre) {
                        $ligne_actuelle[$index_mot_a_trouver]['placement'] = true;
                        $lettres_valides++;
                    } else {
                        $ligne_actuelle[$index_mot_a_trouver]['placement'] = false;
                    }
                } else {
                    $ligne_actuelle[$index_mot_a_trouver]['presence']  = false;
                    $ligne_actuelle[$index_mot_a_trouver]['placement'] = false;
                }
            }
            // Si le mot est trouvé
            if($lettres_valides == count($tableau_mot_a_trouver)){
                $victoire = true;
            } else {
                $victoire = false;
            }
            $response = [
                "mot_a_trouver"    => $tableau_mot_a_trouver,
                "dernier_essai"    => $tableau_dernier_essai,
                "ligne_precedente" => $ligne_actuelle,
                "essais"           => $essai,
                "victoire"         => $victoire
            ];
        }
        // $parametre_longueur_mot;
        // On envoi la première lettre
        return new JsonResponse($response);
    }

    function genereListeValide(){
        $nomsFichiers = [
            7  => 'sept_lettres.txt',
            8  => 'huit_lettres.txt',
            9  => 'neuf_lettres.txt',
            10 => 'dix_lettres.txt',
        ];
        $projectDir = $this->getParameter('kernel.project_dir');
        $file = $projectDir . '/public/liste_francais2.txt';
        $file_arr = file($file);
        $num_lines = count($file_arr);
        $last_arr_index = $num_lines - 1;
        $rand_index = rand(0, $last_arr_index);
        $random_word = $file_arr[$rand_index];
        $nombre_de_mots = 0;
        setlocale(LC_CTYPE, 'fr_FR','fra'); //Fournit les informations de localisation
        foreach ($file_arr as $index => $word){
            $word_length = mb_strlen(trim( $word, " \n\r\t\v\x00"));
            if (($word_length > 6) && ($word_length < 11)  ){
                if (preg_match('#^[a-z]*$#',trim( $word, " \n\r\t\v\x00"))){
                    $nombre_de_mots++;
                    /*écriture du mot dans le fichier approprié*/
                    $random_word = fopen($projectDir.'/public/'.$nomsFichiers[$word_length], "a") or die("Unable to open file!");
                    fwrite($random_word, $word);
                    fclose($random_word);
                }
            }
        }
        /*écriture du nombre de mots dans un fichier séparé*/

        $compteur = fopen($projectDir.'/public/compteur.txt', "a") or die("Unable to open file!");
        fwrite($compteur, $nombre_de_mots);
        fclose($compteur);

    }
}
