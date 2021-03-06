<?php

namespace App\Service;

use App\Entity\Cell;
use App\Entity\Game;
use App\Entity\Line;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;

class GameManagerLineService
{
    const LAST_TRIED_WORDS_STATUS_VALID   = 1;
    const LAST_TRIED_WORDS_STATUS_INVALID = 2;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
     * We remove the presence flag from the misplaced letters whose placement counter is at zero
     *
     * @param array $actualLine
     * @param array $countsOccurrencesPlaced
     * @return array
     */
    public function cleanPresenceFlag(array $actualLine, array $countsOccurrencesPlaced):array
    {
        foreach($actualLine as $index => $letter){
            if(isset($countsOccurrencesPlaced[$letter['valeur']])){
                if(
                    ($letter['placement'] == false && $letter['presence'] == true) &&
                    $countsOccurrencesPlaced[$letter['valeur']] < 1
                )
                {
                    $actualLine[$index]['presence']  = false;
                }
            }
        }
        return $actualLine;
    }

    /**
     * Update of the state of the cells line
     *
     * @param string $wordToFind
     * @param array $wordSearchArray
     * @param array $tableOfTheLastTry
     * @param array $testOccurrenceCounter
     * @param array $countsOccurrencesPlaced
     * @return array
     */
    #[ArrayShape([
        "valid_letters" => "int",
        "actual_line" => "array"
    ])]
    public function updateCellsState(
        array  $wordSearchArray, array $tableOfTheLastTry,
        string $wordToFind, array $testOccurrenceCounter,
        array  $countsOccurrencesPlaced
    ): array
    {
        $validLetters = 0;
        $actualLine = [];

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
                    if(isset($testOccurrenceCounter[$tableOfTheLastTry[$indexWordToFind]])){
                        if($testOccurrenceCounter[$tableOfTheLastTry[$indexWordToFind]] < 1){
                            $actualLine[$indexWordToFind]['presence']  = false;
                        }
                        $testOccurrenceCounter[$tableOfTheLastTry[$indexWordToFind]]--;
                    }
                }
            } else {
                $actualLine[$indexWordToFind]['presence']  = false;
                $actualLine[$indexWordToFind]['placement'] = false;
            }
        }

        return [
            "valid_letters" => $validLetters,
            "actual_line"   => $actualLine
        ];
    }

    /**
     * Update states of the last played line
     *
     * @param array $wordSearchArray
     * @param array $countsOccurrencesPlaced
     * @param array $tableOfTheLastTry
     * @param string $wordToFind
     * @return array
     */
    #[ArrayShape(['actual_line' => "array", 'valid_letters' => "int"])]
    public function updateNewLineState(
        array  $wordSearchArray,
        array  $countsOccurrencesPlaced,
        array  $tableOfTheLastTry,
        string $wordToFind
    ): array
    {
        //-- It is also necessary to count the occurrences of all the letters tested
        $testOccurrenceCounter = $countsOccurrencesPlaced;
        $actualLine = $this->updateCellsState(
            $wordSearchArray, $tableOfTheLastTry,
            $wordToFind, $testOccurrenceCounter,
            $countsOccurrencesPlaced
        );
        $valid_letters = $actualLine['valid_letters'];
        $actualLine = $this->cleanPresenceFlag($actualLine["actual_line"], $countsOccurrencesPlaced);
        return [
            'actual_line' => $actualLine,
            'valid_letters' => $valid_letters
        ];
    }

    /**
     * Persist the last generated line in database
     *
     * @param $actualLine
     * @param $game
     * @return void
     */
    public function persistNewLineInDatabase($actualLine, $game){
        // Retrieve the current line
        $line = $this->entityManager->getRepository(Line::class)->findOneBy([
            'game'     => $game->getId(),
            'position' => $game->getNumberOfRoundsPlayed()
        ]);
        $this->entityManager->persist($line);
        // Update of letters
        foreach ($actualLine as $index => $letter){
            $cell = $this->entityManager->getRepository(Cell::class)->findOneBy([
                'ligne' => $line->getId(),
                'position' => $index
            ]);
            $cell->setValeur($letter['valeur']);
            $cell->setPosition($index);
            if($letter['placement']){
                $cell->setFlagPlacee(Cell::FLAG_PLACEMENT_TRUE);
            } else {
                $cell->setFlagPlacee(Cell::FLAG_PLACEMENT_FALSE);
            }
            if($letter['presence']){
                $cell->setFlagPresente(Cell::FLAG_PRESENCE_TRUE);
            } else {
                $cell->setFlagPresente(Cell::FLAG_PRESENCE_FALSE);
            }
            if($letter['test']){
                $cell->setFlagTestee(Cell::FLAG_TEST_TRUE);
            } else {
                $cell->setFlagTestee(Cell::FLAG_TEST_FALSE);
            }
            $this->entityManager->persist($cell);
        }
        $this->entityManager->flush();
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
}
