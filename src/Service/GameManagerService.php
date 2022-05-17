<?php

namespace App\Service;

use App\Entity\Cell;
use App\Entity\Game;
use App\Entity\Line;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use JetBrains\PhpStorm\ArrayShape;

class GameManagerService
{
    private EntityManagerInterface     $entityManager;
    private GameManagerKeyboardService $gameManagerKeyboardService;
    private GameManagerLineService     $gameManagerLineService;

    public function __construct(
        EntityManagerInterface $entityManager,
        GameManagerKeyboardService $gameManagerKeyboardService,
        GameManagerLineService $gameManagerLineService,
    )
    {
        $this->entityManager              = $entityManager;
        $this->gameManagerKeyboardService = $gameManagerKeyboardService;
        $this->gameManagerLineService     = $gameManagerLineService;
    }

    /**
     * Get the state of last played line ine game,
     * then we update it and return it with other information about the game
     *
     * @param int $idGame
     * @param int $numberOfTry
     * @param string $lastTry
     * @return array
     */
    #[ArrayShape([
        "wordToFind" => "array",
        "lastTry" => "array",
        "previous_line" => "mixed",
        "arrayMajKeyboard" => "array",
        "numberOfLines" => "int",
        "victory" => "bool"
    ])]
    public function updateLine(int $idGame, int $numberOfTry, string $lastTry): array
    {
        //Game in progress
        $game = $this->entityManager->getRepository(Game::class)->find($idGame);
        $wordToFind = trim($game->getWordToFind());
        // Counting in the word to be found,
        // the number of existing occurrences of each letter
        $wordSearchArray = str_split($wordToFind);
        $countsOccurrencesPlaced = $this->gameManagerLineService->countOccurrencesPlaced($wordSearchArray, $wordToFind);
        //-- Update of the current line
        $tableOfTheLastTry = str_split(trim($lastTry));
        $actualLine = $this->gameManagerLineService->updateNewLineState(
            $wordSearchArray,
            $countsOccurrencesPlaced,
            $tableOfTheLastTry,
            $wordToFind
        );
        $validLetters = $actualLine['valid_letters'];
        // Save the new line in database
        $this->gameManagerLineService->persistNewLineInDatabase($actualLine['actual_line'], $game);
        $this->gameManagerLineService->persistCurrentLineNumber($game, $numberOfTry);
        // Update keyboard keys
        $arrayMajKeyboard = $this->gameManagerKeyboardService->updateKeyboardKeys($idGame);
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
     * This function creates initial lines and cells of the grid
     * and fill them with initial values
     *
     * @param Game $game
     * @return Game
     */
    public function fillGridWithInitialValues(Game $game): Game
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
            $currentLine->setPosition($line);
            $this->entityManager->persist($currentLine);
            $game->addLine($currentLine);
        }
        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return $game;
    }
}
