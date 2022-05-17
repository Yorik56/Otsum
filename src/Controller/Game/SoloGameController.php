<?php

namespace App\Controller\Game;

use App\Service\GameManagerService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\{Game, User};
use App\Form\DropOutFormType;
use App\Service\Utils;
use Symfony\Component\HttpFoundation\{JsonResponse, RedirectResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;


class SoloGameController extends GameController
{

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

        $game = $this->gameManagerService->fillGridWithInitialValues($game);

        // Send the first letter
        return new JsonResponse([
            "firstLetter" => $wordToFind[0],
            "idGame"   => $game->getId(),
            "difficulty"  => $difficulty
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
     * Start a game
     * @return Response
     */
    #[Route('/otsum/soloGame', name: 'otsum.soloGame')]
    public function otsum(): Response
    {
        //--- Formulaire Abandon
        $dropOutForm = $this->createForm(DropOutFormType::class);
        return $this->render('otsum/soloGame.html.twig', [
            'controller_name' => 'HomeController',
            'dropOutForm' => $dropOutForm->createView(),
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
        $game = $this->entityManager->getRepository(Game::class)->find($idGame);
        $numberOfTry = $request->request->get('currentLineNumber') + 1;
        $lineUpdated = $this->gameManagerService->updateLine(
            $idGame,
            $numberOfTry,
            $request->request->get('mot')
        );
        // If you made it to the last round and the word was not found
        if(trim($request->request->get('mot')) === $game->getWordToFind() ){
            $lineUpdated['victory'] = true;
        } else if ($game->getNumberOfRounds() == $game->getNumberOfRoundsPlayed()) {
            $lineUpdated['victory'] = false;
            $lineUpdated['wordToFind'] = $game->getWordToFind();
        }else{
            $lineUpdated['victory'] = null;
        }
        return new JsonResponse($lineUpdated);
    }
}
