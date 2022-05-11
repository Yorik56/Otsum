<?php

namespace App\Service;

use App\Entity\Cell;
use App\Entity\Game;
use App\Entity\Line;
use App\Repository\CellRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpKernel\KernelInterface;

class GameManagerService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, KernelInterface $appKernel)
    {
        $this->entityManager = $entityManager;
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
    public function arrayMajKeyboard(
        ObjectRepository $celluleRepository,
        int $idGame,
        array $arrayMajKeyboard,
        array $majKeyboardArrayCell
    ): array
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

    /**
     * Count occurrences for each letters
     *
     * @param array $wordSearchArray
     * @param string $wordToFind
     * @return array
     */
    public function countOccurrencesPlaced(
        array $wordSearchArray,
        string $wordToFind
    ): array
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
     * Persist the new number of lines played in this game
     *
     * @param Game $game
     * @param int $current_number_line
     * @return void
     */
    public function persistCurrentLineNumber(Game $game,int $current_number_line){
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
        $ligne = new Line($game);
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
     * Get the state of last played line ine game,
     * then we update it and return it with other information about the game
     * @param int $idGame
     * @param int $numberOfTry
     * @param string $lastTry
     *
     * @return array
     */
    #[ArrayShape([
        "wordToFind" => "array",
        "lastTry" => "array",
        "previous_line" => "array|mixed",
        "arrayMajKeyboard" => "array",
        "numberOfLines" => "int",
        "victory" => "bool"
    ])]
    public function updateLine(int $idGame, int $numberOfTry, string $lastTry): array
    {
        //Game in progress
        $game = $this->entityManager->getRepository(Game::class)->find($idGame);
        $this->persistCurrentLineNumber($game, $numberOfTry);
        $wordToFind = $game->getWordToFind();
        // Counting in the word to be found,
        // the number of existing occurrences of each letter
        $wordSearchArray = str_split($wordToFind);
        $countsOccurrencesPlaced = $this->countOccurrencesPlaced($wordSearchArray, $wordToFind);
        //-- Update of the current line
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

        return [
            "wordToFind"       => $wordSearchArray,
            "lastTry"          => $tableOfTheLastTry,
            "previous_line"    => $actualLine['actual_line'],
            "arrayMajKeyboard" => $arrayMajKeyboard,
            "numberOfLines"    => $numberOfTry,
            "victory"          => $victory
        ];
    }

    /**
     * Update states of the last played line
     *
     * @param array $wordSearchArray
     * @param $countsOccurrencesPlaced
     * @param $tableOfTheLastTry
     * @param $wordToFind
     * @return array
     */
    #[ArrayShape(['actual_line' => "array", 'valid_letters' => "int"])]
    public function updateNewLineState(
        array $wordSearchArray,
        $countsOccurrencesPlaced,
        $tableOfTheLastTry,
        $wordToFind
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
        $majKeyboardPlaced = null;
        if($celluleRepository instanceof CellRepository){
            $majKeyboardPlaced = $celluleRepository->getPlaced($idGame);
        }
        $arrayMajKeyboard = [];
        if(is_array($majKeyboardPlaced)){
            foreach ($majKeyboardPlaced as $cellule){
                $arrayMajKeyboard[$cellule->getValeur()]['placement'] = $cellule->getFlagPlacee();
                $arrayMajKeyboard[$cellule->getValeur()]['presence']  = $cellule->getFlagPresente();
                $arrayMajKeyboard[$cellule->getValeur()]['test']      = $cellule->getFlagTestee();
            }
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

}
