<?php

namespace App\Controller;

use App\Entity\{
    Cellule,
    DemandeContact,
    Ligne,
    Partie,
    Utilisateur
};
use App\Form\DropOutFormType;
use App\Repository\CelluleRepository;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController {


    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Display the home page
     */
    #[Route('/', name: 'accueil')]
    public function index(HubInterface $hub, EntityManagerInterface $entityManager, Request $request): Response
    {
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController'
        ]);
    }

    /**
     * Get friend requests for the current user
     * @param Request $request
     * @return Response
     */
    #[Route('/mes-notifications', name: 'mes_notifications')]
    public function mes_notifications(Request $request): Response
    {
        $demande_de_contact = $this->entityManager->getRepository(DemandeContact::class)
            ->findOneBy([
                'cible' =>$this->getUser()->getId(),
                'flag_etat' => DemandeContact::DEMANDE_CONTACT_EN_ATTENTE
            ]);
        ($demande_de_contact)?$demandes=true:$demandes=false;
        return new Response($demandes);
    }

    /**
     * Display a hub
     * @return Response
     */
    #[Route('/choixDifficulte', name: 'choixDifficulte')]
    public function hub(): Response
    {
        return $this->render('hub/choix_difficulte.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }

    /**
     * Start a game
     * @param $id
     * @return Response
     */
    #[Route('/otsum/{id}', name: 'otsum')]
    public function otsum($id): Response
    {
        //--- Formulaire Abandon
        $dropOutForm = $this->createForm(DropOutFormType::class);
        return $this->render('otsum/index.html.twig', [
            'controller_name' => 'AccueilController',
            'id' => $id,
            'dropOutForm' => $dropOutForm->createView(),
        ]);
    }

    #[Route('dropOut', name: 'dropOut')]
    public function dropOut(Request $request){
        $dropOutForm = $this->createForm(DropOutFormType::class);
        $dropOutForm->handleRequest($request);
        if ($dropOutForm->isSubmitted() && $dropOutForm->isValid()) {
            $game = $this->entityManager
                ->getRepository(Partie::class)
                ->find($dropOutForm->getData()['gameId']);
            $this->entityManager->remove($game);
            $this->entityManager->flush();
            return $this->redirectToRoute('accueil');
        }
    }

    /**
     * - Generate a word
     * - Create a player
     * - Creation of the game
     *
     * @param Utils $utils
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/createGame', name: 'createGame')]
    function createGame(Utils $utils, Request $request): JsonResponse
    {
        // Parameters to get a word
        $parametre_longueur_mot = $request->request->get('id');
        $word_to_find = $utils->getRandomWord($parametre_longueur_mot);
        //--- Create a player
        $joueur = $this->entityManager->getRepository(Utilisateur::class)->find($this->getUser()->getId());
        //--- Create a game
        $game = new Partie();
        $game->setLongueurLignes($parametre_longueur_mot);
        $game->setMotATrouver(trim($word_to_find));
        $game->setDureeSessionLigne(50);
        $game->addIdJoueur($joueur);
        $game->setNombreTours(6);
        $game->setNombreToursJoues(1);
        $this->entityManager->persist($game);
        $this->entityManager->flush();

        // Send the first letter
        return new JsonResponse([$word_to_find[0], $game->getId()]);
    }

    /**
     * Get the state of last played line ine game,
     * then we update it and return it with other information about the game
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/updateLine', name: 'updateLine')]
    function updateLine(Request $request): JsonResponse
    {
        $id_game = $request->request->get('id_partie');
        //Game in progress
        $game = $this->entityManager->getRepository(Partie::class)->find($id_game);
        $number_of_try = $request->request->get('current_line_number') + 1;
        $this->persistCurrentLineNumber($game, $number_of_try);
        $word_to_find = $game->getMotATrouver();
        // Counting in the word to be found,
        // the number of existing occurrences of each letter
        $word_search_array = str_split($word_to_find);
        $counts_occurrences_placed = $this->countOccurrencesPlaced($word_search_array, $word_to_find);
        //-- Update of the current line
        // Last word tried
        $last_try = $request->request->get('mot');
        $table_of_the_last_try = str_split($last_try);
        $actual_line = $this->updateNewLineState(
            $word_search_array, $counts_occurrences_placed,
            $table_of_the_last_try, $word_to_find
        );
        $valid_letters = $actual_line['valid_letters'];
        // Save the new line in database
        $this->persistNewLineInDatabase($actual_line['actual_line'], $game);
        // Update keyboard keys
        $arrayMajKeyboard = $this->updateKeyboardKeys($id_game);
        // Set victory variable
        ($valid_letters == count($word_search_array))?$victory = true:$victory = false;
        $response = [
            "mot_a_trouver"    => $word_search_array,
            "dernier_essai"    => $table_of_the_last_try,
            "ligne_precedente" => $actual_line['actual_line'],
            "arrayMajKeyboard" => $arrayMajKeyboard,
            "essais"           => $number_of_try,
            "victoire"         => $victory
        ];
        // If you made it to the last round and the word was not found
        if($game->getNombreTours() == $game->getNombreToursJoues() && $victory == false){
            $response['mot_a_trouver'] = $word_to_find;
        }
        return new JsonResponse($response);
    }

    /**
     * Update states of the last played line
     *
     * @param $word_search_array
     * @param $counts_occurrences_placed
     * @param $table_of_the_last_try
     * @param $word_to_find
     * @return array
     */
    #[ArrayShape(['actual_line' => "array", 'valid_letters' => "int"])]
    public function updateNewLineState(
        $word_search_array, $counts_occurrences_placed, $table_of_the_last_try, $word_to_find
    ): array
    {
        $valid_letters = 0;
        $actual_line = [];
        //-- It is also necessary to count the occurrences of all the letters tested
        $test_occurrence_counter = $counts_occurrences_placed;
        // For each letter of the word to find
        foreach ($word_search_array as $index_word_to_find => $letter){
            $actual_line[$index_word_to_find]['valeur'] = $table_of_the_last_try[$index_word_to_find];
            $actual_line[$index_word_to_find]['test']   = true;
            $actual_line[$index_word_to_find]['comparaison']   = $table_of_the_last_try[$index_word_to_find] . " -> " . $letter;
            // If the letter is in the word
            if(preg_match('/'.$table_of_the_last_try[$index_word_to_find].'/', $word_to_find)){
                $actual_line[$index_word_to_find]['presence'] = true;
                // If the letter is well-placed
                if($table_of_the_last_try[$index_word_to_find] === $letter) {
                    $counts_occurrences_placed[$table_of_the_last_try[$index_word_to_find]]--;
                    $actual_line[$index_word_to_find]['placement'] = true;
                    $valid_letters++;
                } else {
                    $actual_line[$index_word_to_find]['placement'] = false;
                    // If the total number of this occurrence have been played in the last attempt
                    // then this occurrence is not considered to be present in the line
                    if($test_occurrence_counter[$table_of_the_last_try[$index_word_to_find]] < 1){
                        $actual_line[$index_word_to_find]['presence']  = false;
                    }
                    $test_occurrence_counter[$table_of_the_last_try[$index_word_to_find]]--;
                }
            } else {
                $actual_line[$index_word_to_find]['presence']  = false;
                $actual_line[$index_word_to_find]['placement'] = false;
            }
        }
        // We remove the presence flag from the misplaced letters whose placement counter is at zero
        foreach($actual_line as $index => $letter){
            if(
                ($letter['placement'] == false && $letter['presence'] == true) &&
                $counts_occurrences_placed[$letter['valeur']] < 1
            )
            {
                $actual_line[$index]['presence']  = false;
            }
        }
        return [
            'actual_line' => $actual_line,
            'valid_letters' => $valid_letters
        ];
    }

    /**
     * Persist the new number of lines played in this game
     *
     * @param $game
     * @param $current_number_line
     * @return void
     */
    public function persistCurrentLineNumber($game, $current_number_line){
        $game->setNombreToursJoues($current_number_line);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
    }

    /**
     * Persist the last generated line in database
     *
     * @param $actual_line
     * @param $game
     * @return void
     */
    public function persistNewLineInDatabase($actual_line, $game){
        // Registration of the new line
        $ligne = new Ligne($this->getUser(), $game);
        $this->entityManager->persist($ligne);
        // Update of letters
        foreach ($actual_line as $index => $letter){
            $cellule = new Cellule();
            $cellule->setLigne($ligne);
            $cellule->setValeur($letter['valeur']);
            $cellule->setPosition($index);
            if($letter['placement']){
                $cellule->setFlagPlacee(Cellule::FLAG_PLACEMENT_TRUE);
            } else {
                $cellule->setFlagPlacee(Cellule::FLAG_PLACEMENT_FALSE);
            }
            if($letter['presence']){
                $cellule->setFlagPresente(Cellule::FLAG_PRESENCE_TRUE);
            } else {
                $cellule->setFlagPresente(Cellule::FLAG_PRESENCE_FALSE);
            }
            if($letter['test']){
                $cellule->setFlagTestee(Cellule::FLAG_TEST_TRUE);
            } else {
                $cellule->setFlagTestee(Cellule::FLAG_TEST_FALSE);
            }
            $this->entityManager->persist($cellule);
        }
        $this->entityManager->flush();
    }

    /**
     * Generates a list of words
     *
     * @return void
     */
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

    /**
     * Count occurrences for each letters
     *
     * @param $word_search_array
     * @param $word_to_find
     * @return array
     */
    public function countOccurrencesPlaced($word_search_array, $word_to_find): array
    {
        $counts_occurrences_placed = [];
        foreach ($word_search_array as $value){
            // If this letter has not been counted
            if(!isset($compteur_occurrence[$value])){
                $counts_occurrences_placed[$value] = substr_count($word_to_find, $value);
            }
        }
        return $counts_occurrences_placed;
    }

    /**
     * Update the keyboard displayed in game
     *
     * @param $id_game
     * @return array
     */
    public function updateKeyboardKeys($id_game): array
    {
        //-- Update of the keyboard keys
        $celluleRepository = $this->entityManager->getRepository(Cellule::class);
        // Update letters placed
        $majKeyboardPlaced = $celluleRepository->getPlaced($id_game);
        $arrayMajKeyboard = [];
        foreach ($majKeyboardPlaced as $cellule){
            $arrayMajKeyboard[$cellule->getValeur()]['placement'] = $cellule->getFlagPlacee();
            $arrayMajKeyboard[$cellule->getValeur()]['presence']  = $cellule->getFlagPresente();
            $arrayMajKeyboard[$cellule->getValeur()]['test']      = $cellule->getFlagTestee();
        }
        // Update of missing letters
        $majKeyboardNotPresent = $celluleRepository->getNotPresent($id_game);
        $arrayMajKeyboard = $this->arrayMajKeyboard(
            $celluleRepository,
            $id_game,
            $arrayMajKeyboard,
            $majKeyboardNotPresent
        );
        // Update letters present and not placed
        $majKeyboardPresentAndNotPlaced = $celluleRepository->getPresentAndNotPlaced($id_game);
        return $this->arrayMajKeyboard(
            $celluleRepository,
            $id_game,
            $arrayMajKeyboard,
            $majKeyboardPresentAndNotPlaced
        );
    }

    /**
     * Updating the keyboard keys
     *
     * @param CelluleRepository $celluleRepository
     * @param int $id_game
     * @param array $arrayMajKeyboard
     * @param Cellule[] $majKeyboardArrayCell
     * @return array
     */
    public function arrayMajKeyboard(ObjectRepository $celluleRepository, int $id_game, array $arrayMajKeyboard, array $majKeyboardArrayCell): array
    {
        foreach ($majKeyboardArrayCell as $cellule) {
            // Check if the letter has already been placed
            if (!$celluleRepository->getPlacedOrFalse($id_game, $cellule->getValeur())) {
                // Update the status of the letter
                $arrayMajKeyboard[$cellule->getValeur()]['placement'] = $cellule->getFlagPlacee();
                $arrayMajKeyboard[$cellule->getValeur()]['presence'] = $cellule->getFlagPresente();
                $arrayMajKeyboard[$cellule->getValeur()]['test'] = $cellule->getFlagTestee();
            }
        }

        return $arrayMajKeyboard;
    }
}
