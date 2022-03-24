<?php

namespace App\EventSubscriber;

use App\Controller\LoginController;
use App\Controller\RegistrationController;
use App\Entity\ContactRequest;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Twig\Environment;

class LoginSubscriber implements EventSubscriberInterface
{
    private TokenStorageInterface $tokenStorage;
    private EntityManagerInterface $entityManager;
    private HubInterface $hub;
    private Environment $twig;

    const ACTION_LOGIN  = 1;
    const ACTION_LOGOUT = 2;


    public function __construct(Environment $twig, TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager, HubInterface $hub)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
        $this->hub = $hub;
        $this->twig = $twig;
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
            ControllerEvent ::class => 'onKernelController',
        ];
    }

    /**
     * Display the contact list of current User
     * @param ControllerEvent $event
     * @return void
     */
    public function onKernelController(ControllerEvent $event)
    {
        // Get controller from ControllerEvent
        $controller = $event->getController();
        // We want to filter the registration/connexion controllers
        if (!$controller instanceof LoginController and !$controller instanceof RegistrationController) {
            // Get the current User
            $token = $this->getTokenStorageInterface();
            $user = $token?->getUser();
             if ($user) {
                 // Get contact requests id's then User Entities
                 $contacts = $this->getContacts($user->getId());
                 if($contacts){
                     $listeAmis = [];
                     foreach ($contacts as $contact) {
                         $amis = $this->entityManager->getRepository(entityName: User::class)
                             ->findOneBy([
                                 'id' => $contact['contact']
                             ]);
                         $listeAmis[] = $amis;
                         dump($listeAmis);
                     }
                     $this->twig->addGlobal('listeAmis', $listeAmis);
                 }
            }
        }
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
            $contacts = $this->getContacts($user->getId());
            if($contacts){
                foreach ($contacts as $contact) {
                    if($event == self::ACTION_LOGIN){
                        $topic = "/imLogged/".$contact['contact'];
                        dump($topic);
                        $user->setConnected(User::USER_CONNECTED_TRUE);
                    }
                    if($event == self::ACTION_LOGOUT){
                        $topic = "/iLeave/".$contact['contact'];
                        $user->setConnected(User::USER_CONNECTED_FALSE);
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
                                'topic' => $topic,
                                'idUser' => $user->getId()
                            ])
                        );
                        $this->hub->publish($update);
                    }
                }
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

    /**
     * Get array of contact id's
     * @param $userId
     * @return array
     */
    public function getContacts($userId): array
    {
        return $this->entityManager->getRepository(entityName: ContactRequest::class)
            ->mesContacts(userId: $userId);
    }
}


