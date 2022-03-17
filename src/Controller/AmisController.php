<?php

namespace App\Controller;

use App\Entity\{DemandeContact,Utilisateur};
use App\Form\ContactRequestType;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\{Form\FormError};
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Mercure\{HubInterface, Update};
use Symfony\Component\Routing\Annotation\Route;


class AmisController extends AbstractController
{
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
        $usersContact = Utils::class->getContacts($this->getUser()->getId());
        //-- Demandes d'amis reçues
        $demandes_de_contact = $entityManager->getRepository(entityName: DemandeContact::class)
            ->findBy([
                'cible' => $this->getUser()->getId(),
                'flag_etat' => DemandeContact::DEMANDE_CONTACT_EN_ATTENTE
            ]);
        //-- Formulaire demande de contact
        $form = $this->createForm(ContactRequestType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pseudo = $form->getData();
            // Recherche de l'utilisateur ciblé
            $utilisateur_cible = $entityManager
                ->getRepository(Utilisateur::class)
                ->findOneBy(['pseudo'=> $pseudo['Pseudo']]);
            // Si l'utilisateur existe
            if($utilisateur_cible){
                // Au cas où l'utilisateur s'envoie une demande à lui-même
                if ($utilisateur_cible->getId() == $this->getUser()->getId()) {
                    $error = new FormError("Vous ne pouvez pas vous ajouter en tant que contact.");
                    $form->addError($error);
                } else {
                    $demande_de_contact = $entityManager->getRepository(DemandeContact::class)
                        ->findOneBy([
                                'source'=>$this->getUser()->getId(),
                                'cible' =>$utilisateur_cible,
                        ]);
                    // On vérifie que cette demande n'a pas déjà été envoyée
                    if(!$demande_de_contact){
                        //Enregistrement de la demande de contact
                        $demande_de_contact = new DemandeContact($this->getUser(),$utilisateur_cible);
                        $demande_de_contact->setFlagEtat(DemandeContact::DEMANDE_CONTACT_EN_ATTENTE);
                        $entityManager->persist($demande_de_contact);
                        $entityManager->flush();
                        //Envois de la notification
                        $update = new Update(
                            '/accueil/notifications/demande_ajout/'.$utilisateur_cible->getId(),
                            json_encode([
                                'topic' => '/accueil/notifications/demande_ajout/'.$utilisateur_cible->getId(),
                                'notification' => "Vous avez une demande d'amis de ".$this->getUser()->getPseudo(),
                                'id_source' => $this->getUser()->getId()
                            ])
                        );
                        $hub->publish($update);
                    } else {
                        // On prévient le demandeur que la demande a déjà été envoyée
                        $error = new FormError("La demande à déjà été envoyée à ce contact.");
                        $form->addError($error);
                    }
                }
            } else {
                // On prévient le demandeur que le contact recherché n'existe pas
                $error = new FormError("L'utilisateur avec ce pseudo n'existe pas.");
                $form->addError($error);
            }
        }
        return $this->render('amis/index.html.twig', [
            'controller_name' => 'AmisController',
            'contact_request_form' => $form->createView(),
            'demandes_contact' => $demandes_de_contact,
            'users_ontact' => $usersContact
        ]);
    }

    /**
     * Save the answer to a friend request
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    #[Route('/reponseDemandeAmis', name: 'reponseDemandeAmis')]
    public function reponseDemandeAmis(EntityManagerInterface $entityManager, Request $request): Response
    {
        $reponse     = $request->query->get('reponse');
        $utilisateur = $request->query->get('idUtilisateur');
        //Maj de la demande de contact
        $demande_de_contact = $entityManager->getRepository(DemandeContact::class)
        ->findOneBy([
            'source'=>$utilisateur,
            'cible' =>$this->getUser()->getId(),
        ]);
        if($demande_de_contact && ($reponse=="accepte" || $reponse == "refuse") ){
            if($reponse == "accepte"){
                $flag_etat = DemandeContact::DEMANDE_CONTACT_ACCEPTEE;
            }
            else{
                $flag_etat = DemandeContact::DEMANDE_CONTACT_REFUSEE;
            }
            $demande_de_contact->setFlagEtat($flag_etat);
            $entityManager->persist($demande_de_contact);
            $entityManager->flush();
        }
        return new Response($utilisateur);
    }

}
