<?php

namespace App\Service;

use App\Entity\Cell;
use App\Entity\Game;
use App\Entity\Line;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;

class GameManagerService
{
    private EntityManagerInterface     $entityManager;
    public GameManagerKeyboardService $gameManagerKeyboardService;
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
     * @param $lastTriedWordsStatus
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
    public function updateLine(int $idGame, int $numberOfTry, string $lastTry, $lastTriedWordsStatus ): array
    {
        //-- Parameters
        $game = $this->entityManager->getRepository(Game::class)->find($idGame);
        $wordToFind = trim($game->getWordToFind());
        $wordSearchArray = str_split($wordToFind);
        $countsOccurrencesPlaced = $this->gameManagerLineService->countOccurrencesPlaced($wordSearchArray, $wordToFind);
        //-- Update of the current line
        if($lastTriedWordsStatus == GameManagerLineService::LAST_TRIED_WORDS_STATUS_INVALID){
            $currentLine = $this->entityManager->getRepository(Line::class)->findOneBy([
                "game"     => $game,
                "position" => $numberOfTry
            ]);
            $tableOfTheLastTry = $this->entityManager->getRepository(Cell::class)->findBy([
                "ligne" => $currentLine
            ]);
            $bonusLetterAlreadyAdded = false;
            $bonusLetters = [];
            foreach ($tableOfTheLastTry as $positionCell => $cell){
                if($positionCell === 0){
                    $tableOfTheLastTry[$positionCell] = $game->getWordToFind()[0];
                } elseif(
                    $cell->getValeur() === "." &&
                    $bonusLetterAlreadyAdded == false
                ){
                    $cell->setValeur($wordSearchArray[$positionCell]);
                    $cell->setFlagTestee(Cell::FLAG_TEST_TRUE);
                    $cell->setFlagPresente(Cell::FLAG_PRESENCE_FALSE);
                    $this->entityManager->persist($cell);
                    $tableOfTheLastTry[$positionCell] = $wordSearchArray[$positionCell];
                    $this->entityManager->flush();
                    $bonusLetters[] = $wordSearchArray[$positionCell];
                    $bonusLetterAlreadyAdded = true;
                } else {
                    $tableOfTheLastTry[$positionCell] = $cell->getValeur();
                }
                if(!$bonusLetterAlreadyAdded){
                    $bonusLetters[] = $wordSearchArray[$positionCell];
                }
            }
            if($bonusLetterAlreadyAdded){
                $this->updateBonusLetters($game, $bonusLetters);
            }
        } else {
            $tableOfTheLastTry = str_split(trim($lastTry));
        }
        $actualLine = $this->gameManagerLineService->updateNewLineState(
            $wordSearchArray,
            $countsOccurrencesPlaced,
            $tableOfTheLastTry,
            $wordToFind
        );
        $validLetters = $actualLine['valid_letters'];
        $this->gameManagerLineService->persistNewLineInDatabase($actualLine['actual_line'], $game);
        $this->gameManagerLineService->persistCurrentLineNumber($game, $numberOfTry);
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

    private function updateBonusLetters($game, $bonusLetters)
    {
        $gameLines = $this->entityManager->getRepository(Line::class)->findBy([
            "game" => $game
        ]);
        foreach($gameLines as $indexLine => $line ){
            $lineCells = $this->entityManager->getRepository(Cell::class)->findBy([
                "ligne" => $line
            ]);
            foreach ($bonusLetters as $indexBonusLetter => $bonusLetter){
                $lineCells[$indexBonusLetter]->setValeur($bonusLetter);
                $this->entityManager->persist($lineCells[$indexBonusLetter]);
            }
        }
        $this->entityManager->flush();
    }
}
