<?php

namespace App\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ActivityListener
{
    private TokenStorageInterface $tokenStorage;
    private $entityManager;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    /**
     * Trigger an action on KernelRequest
     * @param RequestEvent $event
     * @return void
     */
    public function onKernelRequest(RequestEvent $event)
    {
        // Get the user object from the tokenStorageInterface
        $token = $this->getTokenStorageInterface();
        $user = $token?->getUser();
        if($user){
            $user->setLastActivityAt(new \DateTime());
            $this->entityManager->persist($user);
            $this->entityManager->flush();
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
