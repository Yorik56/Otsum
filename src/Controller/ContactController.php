<?php

namespace App\Controller;

use App\Entity\{ContactRequest, InvitationToPlay, User};
use App\Form\ContactRequestType;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\{Form\FormError};
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Mercure\{HubInterface, Update};
use Symfony\Component\Routing\Annotation\Route;


class ContactController extends AbstractController
{
    private Utils $utils;

    public function __construct(Utils $utils)
    {
        $this->utils = $utils;
    }

    /**
     * - Manage the display of the "/amis" page
     * - Friend request form
     * - Sending a friend request topic (Mercure)
     * @param HubInterface $hub
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    #[Route('/amis', name: 'amis')]
    public function amis(HubInterface $hub, EntityManagerInterface $entityManager, Request $request): Response
    {
        //-- Contacts
        $usersContact = $this->utils->getContacts($this->getUser()->getId());
        //-- Contact request form
        $form = $this->createForm(ContactRequestType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pseudo = $form->getData();
            // Search for the targeted user
            $targetedUser = $entityManager
                ->getRepository(User::class)
                ->findOneBy(['pseudo'=> $pseudo['Pseudo']]);
            // If the user exists
            if($targetedUser){
                // In case the user sends a request to himself
                if ($targetedUser->getId() == $this->getUser()->getId()) {
                    $error = new FormError("Vous ne pouvez pas vous ajouter en tant que contact.");
                    $form->addError($error);
                } else {
                    $contactRequest = $entityManager->getRepository(ContactRequest::class)
                        ->findOneBy([
                                'source'=>$this->getUser()->getId(),
                                'target' =>$targetedUser,
                        ]);
                    // We check that this request has not already been sent
                    if(!$contactRequest){
                        // Registration of the contact request
                        $contactRequest = new ContactRequest($this->getUser(),$targetedUser);
                        $contactRequest->setFlagState(ContactRequest::REQUEST_CONTACT_PENDING);
                        $entityManager->persist($contactRequest);
                        $entityManager->flush();
                        // Sending of the notification
                        $update = new Update(
                            '/accueil/notifications/demande_ajout/'.$targetedUser->getId(),
                            json_encode([
                                'topic' => '/accueil/notifications/demande_ajout/'.$targetedUser->getId(),
                                'notification' => "Vous avez une demande d'amis de ".$this->getUser()->getPseudo(),
                                'id_source' => $this->getUser()->getId()
                            ])
                        );
                        $hub->publish($update);
                    } else {
                        // The applicant is notified that the request has already been sent
                        $error = new FormError("La demande à déjà été envoyée à ce contact.");
                        $form->addError($error);
                    }
                }
            } else {
                // The applicant is informed that the contact does not exist
                $error = new FormError("L'utilisateur avec ce pseudo n'existe pas.");
                $form->addError($error);
            }
        }
        //-- Friend requests received
        $demandes_de_contact = $entityManager->getRepository(entityName: ContactRequest::class)
            ->findBy([
                'target' => $this->getUser()->getId(),
                'flag_state' => ContactRequest::REQUEST_CONTACT_PENDING
            ]);

        return $this->render('amis/index.html.twig', [
            'controller_name' => 'AmisController',
            'contactRequestsForm' => $form->createView(),
            'contactRequests' => $demandes_de_contact,
            'userContacts' => $usersContact
        ]);
    }

    /**
     * Save the answer to a friend request
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    #[Route('/replyFriendRequest', name: 'replyFriendRequest')]
    public function replyFriendRequest(EntityManagerInterface $entityManager, Request $request): Response
    {
        $response     = $request->query->get('response');
        $user = $request->query->get('idUser');
        //Maj de la demande de contact
        $contactRequest = $entityManager->getRepository(ContactRequest::class)
        ->findOneBy([
            'source' => $user,
            'target' => $this->getUser()->getId(),
        ]);
        if($contactRequest && ($response=="accepte" || $response == "refuse") ){
            if($response == "accepte"){
                $flag_state = ContactRequest::REQUEST_CONTACT_ACCEPTED;
            }
            else{
                $flag_state = ContactRequest::REQUEST_CONTACT_REFUSED;
            }
            $contactRequest->setFlagState($flag_state);
            $entityManager->persist($contactRequest);
            $entityManager->flush();
        }
        return new Response($user);
    }

}
