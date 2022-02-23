<?php
namespace App\Listener;
use App\Controller\LoginController;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\HttpKernel;

/**
 * Listener that updates the last activity of the authenticated user
 */
class ActivityListener
{
    protected LoginController $LoginController;
    protected EntityManager $entityManager;

    public function __construct(LoginController $LoginController, EntityManager $entityManager)
    {
        $this->LoginController = $LoginController;
        $this->entityManager = $entityManager;
    }

    /**
     * Update the user "lastActivity" on each request
     * @param FilterControllerEvent $event
     */
    public function onCoreController(FilterControllerEvent $event)
    {
        // Check that the current request is a "MASTER_REQUEST"
        // Ignore any sub-request
        if ($event->getRequestType() !== HttpKernel::MASTER_REQUEST) {
        return;
        }

        // Check token authentication availability
        if ($this->LoginController->getToken()) {
            $user = $this->LoginController->getToken()->getUser();

            if ( ($user instanceof Utilisateur) && !($user->isActiveNow()) ) {
                $user->setLastActivityAt(new \DateTime());
                $this->entityManager->flush($user);
            }
        }
    }
}
