<?php

namespace App\Controller\Game;

use App\Entity\{Cell, Game, Line, User};
use App\Form\DropOutFormType;
use App\Repository\CellRepository;
use App\Service\Utils;
use Doctrine\Persistence\ObjectRepository;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\{JsonResponse, RedirectResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;


class GameSoloController extends GameController
{
    /**
     * Start a game
     * @return Response
     */
    #[Route('/otsum', name: 'otsum')]
    public function otsum(): Response
    {
        //--- Formulaire Abandon
        $dropOutForm = $this->createForm(DropOutFormType::class);
        return $this->render('otsum/index.html.twig', [
            'controller_name' => 'HomeController',
            'dropOutForm' => $dropOutForm->createView(),
        ]);
    }

    #[Route('dropOut', name: 'dropOut')]
    public function dropOut(Request $request): RedirectResponse|bool
    {
        $dropOutForm = $this->createForm(DropOutFormType::class);
        $dropOutForm->handleRequest($request);
        if ($dropOutForm->isSubmitted() && $dropOutForm->isValid()) {
            $game = $this->entityManager
                ->getRepository(Game::class)
                ->find($dropOutForm->getData()['gameId']);
            $this->entityManager->remove($game);
            $this->entityManager->flush();
            return $this->redirectToRoute('home');
        }

        return false;
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
        $difficulty = rand(7, 10);
        $wordToFind = $utils->getRandomWord($difficulty);
        //--- Create a player
        $joueur = $this->entityManager->getRepository(User::class)->find($this->getUser()->getId());
        //--- Create a game
        $game = new Game();
        $game->setLineLength($difficulty);
        $game->setWordToFind(trim($wordToFind));
        $game->setLineSessionTime(50);
        $game->addPlayer($joueur);
        $game->setNumberOfRounds(6);
        $game->setNumberOfRoundsPlayed(1);
        $this->entityManager->persist($game);
        $this->entityManager->flush();

        // Send the first letter
        return new JsonResponse([
            "firstLetter" => $wordToFind[0],
            "idGame"   => $game->getId(),
            "difficulty"  => $difficulty
        ]);
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
        $idGame = $request->request->get('idGame');
        //Game in progress
        $game = $this->entityManager->getRepository(Game::class)->find($idGame);
        $numberOfTry = $request->request->get('currentLineNumber') + 1;
        $this->persistCurrentLineNumber($game, $numberOfTry);
        $wordToFind = $game->getWordToFind();
        // Counting in the word to be found,
        // the number of existing occurrences of each letter
        $wordSearchArray = str_split($wordToFind);
        $countsOccurrencesPlaced = $this->countOccurrencesPlaced($wordSearchArray, $wordToFind);
        //-- Update of the current line
        // Last word tried
        $lastTry = $request->request->get('mot');
        $tableOfTheLastTry = str_split($lastTry);
        $actualLine = $this->updateNewLineState(
            $wordSearchArray, $countsOccurrencesPlaced,
            $tableOfTheLastTry, $wordToFind
        );
        $validLetters = $actualLine['valid_letters'];
        // Save the new line in database
        $this->persistNewLineInDatabase($actualLine['actual_line'], $game);
        // Update keyboard keys
        $arrayMajKeyboard = $this->updateKeyboardKeys($idGame);
        // Set victory variable
        ($validLetters == count($wordSearchArray))?$victory = true:$victory = false;
        $response = [
            "wordToFind"     => $wordSearchArray,
            "lastTry"    => $tableOfTheLastTry,
            "ligne_precedente" => $actualLine['actual_line'],
            "arrayMajKeyboard" => $arrayMajKeyboard,
            "numberOfLines"    => $numberOfTry,
            "victoire"         => $victory
        ];
        // If you made it to the last round and the word was not found
        if($game->getNumberOfRounds() == $game->getNumberOfRoundsPlayed() && $victory == false){
            $response['wordToFind'] = $wordToFind;
        }
        return new JsonResponse($response);
    }

    /**
     * Update states of the last played line
     *
     * @param $wordSearchArray
     * @param $countsOccurrencesPlaced
     * @param $tableOfTheLastTry
     * @param $wordToFind
     * @return array
     */
    #[ArrayShape(['actual_line' => "array", 'valid_letters' => "int"])]
    public function updateNewLineState(
        $wordSearchArray, $countsOccurrencesPlaced, $tableOfTheLastTry, $wordToFind
    ): array
    {
        $validLetters = 0;
        $actualLine = [];
        //-- It is also necessary to count the occurrences of all the letters tested
        $testOccurrenceCounter = $countsOccurrencesPlaced;
        // For each letter of the word to find
        foreach ($wordSearchArray as $indexWordToFind => $letter){
            $actualLine[$indexWordToFind]['valeur'] = $tableOfTheLastTry[$indexWordToFind];
            $actualLine[$indexWordToFind]['test']   = true;
            $actualLine[$indexWordToFind]['comparaison']   = $tableOfTheLastTry[$indexWordToFind] . " -> " . $letter;
            // If the letter is in the word
            if(preg_match('/'.$tableOfTheLastTry[$indexWordToFind].'/', $wordToFind)){
                $actualLine[$indexWordToFind]['presence'] = true;
                // If the letter is well-placed
                if($tableOfTheLastTry[$indexWordToFind] === $letter) {
                    $countsOccurrencesPlaced[$tableOfTheLastTry[$indexWordToFind]]--;
                    $actualLine[$indexWordToFind]['placement'] = true;
                    $validLetters++;
                } else {
                    $actualLine[$indexWordToFind]['placement'] = false;
                    // If the total number of this occurrence have been played in the last attempt
                    // then this occurrence is not considered to be present in the line
                    if($testOccurrenceCounter[$tableOfTheLastTry[$indexWordToFind]] < 1){
                        $actualLine[$indexWordToFind]['presence']  = false;
                    }
                    $testOccurrenceCounter[$tableOfTheLastTry[$indexWordToFind]]--;
                }
            } else {
                $actualLine[$indexWordToFind]['presence']  = false;
                $actualLine[$indexWordToFind]['placement'] = false;
            }
        }
        // We remove the presence flag from the misplaced letters whose placement counter is at zero
        foreach($actualLine as $index => $letter){
            if(
                ($letter['placement'] == false && $letter['presence'] == true) &&
                $countsOccurrencesPlaced[$letter['valeur']] < 1
            )
            {
                $actualLine[$index]['presence']  = false;
            }
        }
        return [
            'actual_line' => $actualLine,
            'valid_letters' => $validLetters
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
        $game->setNumberOfRoundsPlayed($current_number_line);
        $this->entityManager->persist($game);
        $this->entityManager->flush();
    }

    /**
     * Persist the last generated line in database
     *
     * @param $actualLine
     * @param $game
     * @return void
     */
    public function persistNewLineInDatabase($actualLine, $game){
        // Registration of the new line
        $ligne = new Line($this->getUser(), $game);
        $this->entityManager->persist($ligne);
        // Update of letters
        foreach ($actualLine as $index => $letter){
            $cellule = new Cell();
            $cellule->setLigne($ligne);
            $cellule->setValeur($letter['valeur']);
            $cellule->setPosition($index);
            if($letter['placement']){
                $cellule->setFlagPlacee(Cell::FLAG_PLACEMENT_TRUE);
            } else {
                $cellule->setFlagPlacee(Cell::FLAG_PLACEMENT_FALSE);
            }
            if($letter['presence']){
                $cellule->setFlagPresente(Cell::FLAG_PRESENCE_TRUE);
            } else {
                $cellule->setFlagPresente(Cell::FLAG_PRESENCE_FALSE);
            }
            if($letter['test']){
                $cellule->setFlagTestee(Cell::FLAG_TEST_TRUE);
            } else {
                $cellule->setFlagTestee(Cell::FLAG_TEST_FALSE);
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
     * @param $wordSearchArray
     * @param $wordToFind
     * @return array
     */
    public function countOccurrencesPlaced($wordSearchArray, $wordToFind): array
    {
        $countsOccurrencesPlaced = [];
        foreach ($wordSearchArray as $value){
            // If this letter has not been counted
            if(!isset($compteur_occurrence[$value])){
                $countsOccurrencesPlaced[$value] = substr_count($wordToFind, $value);
            }
        }
        return $countsOccurrencesPlaced;
    }

    /**
     * Update the keyboard displayed in game
     *
     * @param $idGame
     * @return array
     */
    public function updateKeyboardKeys($idGame): array
    {
        //-- Update of the keyboard keys
        $celluleRepository = $this->entityManager->getRepository(Cell::class);
        // Update letters placed
        $majKeyboardPlaced = $celluleRepository->getPlaced($idGame);
        $arrayMajKeyboard = [];
        foreach ($majKeyboardPlaced as $cellule){
            $arrayMajKeyboard[$cellule->getValeur()]['placement'] = $cellule->getFlagPlacee();
            $arrayMajKeyboard[$cellule->getValeur()]['presence']  = $cellule->getFlagPresente();
            $arrayMajKeyboard[$cellule->getValeur()]['test']      = $cellule->getFlagTestee();
        }
        // Update of missing letters
        $majKeyboardNotPresent = $celluleRepository->getNotPresent($idGame);
        $arrayMajKeyboard = $this->arrayMajKeyboard(
            $celluleRepository,
            $idGame,
            $arrayMajKeyboard,
            $majKeyboardNotPresent
        );
        // Update letters present and not placed
        $majKeyboardPresentAndNotPlaced = $celluleRepository->getPresentAndNotPlaced($idGame);
        return $this->arrayMajKeyboard(
            $celluleRepository,
            $idGame,
            $arrayMajKeyboard,
            $majKeyboardPresentAndNotPlaced
        );
    }

    /**
     * Updating the keyboard keys
     *
     * @param CellRepository $celluleRepository
     * @param int $idGame
     * @param array $arrayMajKeyboard
     * @param Cell[] $majKeyboardArrayCell
     * @return array
     */
    public function arrayMajKeyboard(ObjectRepository $celluleRepository, int $idGame, array $arrayMajKeyboard, array $majKeyboardArrayCell): array
    {
        foreach ($majKeyboardArrayCell as $cellule) {
            // Check if the letter has already been placed
            if (!$celluleRepository->getPlacedOrFalse($idGame, $cellule->getValeur())) {
                // Update the status of the letter
                $arrayMajKeyboard[$cellule->getValeur()]['placement'] = $cellule->getFlagPlacee();
                $arrayMajKeyboard[$cellule->getValeur()]['presence'] = $cellule->getFlagPresente();
                $arrayMajKeyboard[$cellule->getValeur()]['test'] = $cellule->getFlagTestee();
            }
        }

        return $arrayMajKeyboard;
    }
}
