<?php

namespace App\Service;

use App\Entity\Cell;
use App\Repository\CellRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class GameManagerKeyboardService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Updating the keyboard keys
     *
     * @param CellRepository $cellRepository
     * @param int $idGame
     * @param array $arrayMajKeyboard
     * @param Cell[] $majKeyboardArrayCell
     * @return array
     */
    public function arrayMajKeyboard(
        ObjectRepository $cellRepository, int $idGame, array $arrayMajKeyboard, array $majKeyboardArrayCell
    ): array
    {
        foreach ($majKeyboardArrayCell as $cell) {
            // Check if the letter has already been placed
            if (!$cellRepository->getPlacedOrFalse($idGame, $cell->getValeur())) {
                // Update the status of the letter
                $arrayMajKeyboard[$cell->getValeur()]['placement'] = $cell->getFlagPlacee();
                $arrayMajKeyboard[$cell->getValeur()]['presence'] = $cell->getFlagPresente();
                $arrayMajKeyboard[$cell->getValeur()]['test'] = $cell->getFlagTestee();
            }
        }
        return $arrayMajKeyboard;
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
        $cellRepository = $this->entityManager->getRepository(Cell::class);
        // Update letters placed
        $majKeyboardPlaced = null;
        if($cellRepository instanceof CellRepository){
            $majKeyboardPlaced = $cellRepository->getPlaced($idGame);
        }
        $arrayMajKeyboard = [];
        if(is_array($majKeyboardPlaced)){
            foreach ($majKeyboardPlaced as $cell){
                $arrayMajKeyboard[$cell->getValeur()]['placement'] = $cell->getFlagPlacee();
                $arrayMajKeyboard[$cell->getValeur()]['presence']  = $cell->getFlagPresente();
                $arrayMajKeyboard[$cell->getValeur()]['test']      = $cell->getFlagTestee();
            }
        }
        // Update of missing letters
        $majKeyboardNotPresent = $cellRepository->getNotPresent($idGame);
        $arrayMajKeyboard = $this->arrayMajKeyboard(
            $cellRepository,
            $idGame,
            $arrayMajKeyboard,
            $majKeyboardNotPresent
        );
        // Update letters present and not placed
        $majKeyboardPresentAndNotPlaced = $cellRepository->getPresentAndNotPlaced($idGame);
        return $this->arrayMajKeyboard(
            $cellRepository,
            $idGame,
            $arrayMajKeyboard,
            $majKeyboardPresentAndNotPlaced
        );
    }
}
