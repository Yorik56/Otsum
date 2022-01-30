<?php

namespace App\Controller;

use App\Entity\Cellule;
use App\Entity\Ligne;
use App\Entity\Partie;

use App\Entity\Utilisateur;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Finder\SplFileInfo;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function index(): Response
    {

        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }

    #[Route('/generemot', name: 'genere_mot')]
    function genereUnMot(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        // Paramètres utiles à la construction du mot
        $parametre_longueur_mot = $request->request->get('id');
        $nomsFichiers = [
            5 => 'cinq_lettres.txt',
            6 => 'six_lettres.txt',
            7 => 'sept_lettres.txt',
            8 => 'huit_lettres.txt',
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
        $partie = new Partie($parametre_longueur_mot, $mot_a_trouver);
        $partie->setDureeSessionLigne(50);
        $partie->addIdJoueur($joueur);
        $partie->setNombreTours(5);
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
        // Paramètres utiles à la construction du mot
        $ligne_actuelle = $request->request->get('ligne_actuelle');
        $mot = $request->request->get('mot');
        $id_partie = $request->request->get('id_partie');

        // On envoi la première lettre
        return new JsonResponse($parametre_longueur_mot);
    }

    function genereListeValide(){
        $projectDir = $this->getParameter('kernel.project_dir');
        $file = $projectDir . '/public/liste_francais.txt';
        $file_arr = file($file);
        $num_lines = count($file_arr);
        $last_arr_index = $num_lines - 1;
        $rand_index = rand(0, $last_arr_index);
        $random_word = $file_arr[$rand_index];
        setlocale(LC_CTYPE, 'fr_FR','fra'); //Fournit les informations de localisation
        foreach ($file_arr as $index => $word){
            $word_length = mb_strlen(trim( $word, " \n\r\t\v\x00"));
            if (($word_length > 4) && ($word_length < 9)  ){
                if (preg_match('#^[a-z]*$#',trim( $word, " \n\r\t\v\x00"))){
                    $random_word = fopen($projectDir.'/public/'.$nomsFichiers[$word_length], "a") or die("Unable to open file!");
                    fwrite($random_word, $word);
                    fclose($random_word);
                }
            }
        }
    }
}
