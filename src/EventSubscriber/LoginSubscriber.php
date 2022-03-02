<?php

namespace App\EventSubscriber;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    private TokenStorageInterface $tokenStorage;
    private EntityManagerInterface $entityManager;
    private HubInterface $hub;

    const ACTION_LOGIN  = 1;
    const ACTION_LOGOUT = 2;


    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager, HubInterface $hub)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
        $this->hub = $hub;
    }

    /**
     * Select events from those available
     *
     * https://symfony.com/doc/current/reference/events.html
     * @return string[]
     */
    static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
            LogoutEvent::class => 'onLogout',
        ];
    }

    /**
     * Trigger an action on LoginSuccessEvent
     * @param LoginSuccessEvent $event
     * @return void
     */
    #[NoReturn] public function onLoginSuccess(LoginSuccessEvent $event)
    {
        // Publish a topic "imLogged/{id}" from the current User
        $this->publishTopicFromCurrentUser(self::ACTION_LOGIN);
    }

    /**
     * Trigger an action on LogoutEvent
     * @param LogoutEvent $event
     * @return void
     */
    #[NoReturn] public function onLogout(LogoutEvent $event)
    {
        // Publish a topic "iLeave/{id}" from the current User
        $this->publishTopicFromCurrentUser(self::ACTION_LOGOUT);
    }

    /**
     *  Publish a Topic (login/logout) from the current user
     * @param $event
     * @return void
     */
    public function publishTopicFromCurrentUser($event): void
    {
        $token = $this->getTokenStorageInterface();
        $user = $token?->getUser();
        $topic = null;
        if ($user) {
            if($event == self::ACTION_LOGIN){
                $topic = "imLogged/".$user->getId();
                $user->setConnected(Utilisateur::USER_CONNECTED_TRUE);
            }
            if($event == self::ACTION_LOGOUT){
                $topic = "iLeave/".$user->getId();
                $user->setConnected(Utilisateur::USER_CONNECTED_FALSE);
            }
            // Maj de la date de la dernière activité
            $user->setLastActivityAt(new \DateTime());
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            // Publication d'un topic
            if($topic){
                $update = new Update(
                    $topic,
                    json_encode([
                        'loggedUser' => $user->getId()
                    ])
                );
                $this->hub->publish($update);
            }
        }
    }

    /**
     * Get a token storage from the security session
     * @return TokenInterface|null
     */
    public function getTokenStorageInterface(): ?TokenInterface
    {
        return $this->tokenStorage->getToken();
    }
}


