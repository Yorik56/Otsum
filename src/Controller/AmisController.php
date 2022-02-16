<?php

namespace App\Controller;

use App\Entity\DemandeContact;
use App\Entity\Utilisateur;
use App\Form\ContactRequestType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class AmisController extends AbstractController
{
    #[Route('/amis', name: 'amis')]
    public function index(HubInterface $hub, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Formulaire demande de contact
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
                    //Enregistrement de la demande de contact
                    $demande_de_contact = new DemandeContact($this->getUser(),$utilisateur_cible);
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
                }
            } else {
                // On prévient le demandeur que le contact recherché n'existe pas
                $error = new FormError("L'utilisateur avec ce pseudo n'existe pas.");
                $form->addError($error);
            }
        }

        return $this->render('amis/index.html.twig', [
            'controller_name' => 'AmisController',
            'contact_request_form' => $form->createView()
        ]);
    }
}
