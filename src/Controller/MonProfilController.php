<?php

namespace App\Controller;

use App\Entity\Avatar;
use App\Form\MajAvatarFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MonProfilController extends AbstractController
{
    #[Route('/mon/profil', name: 'mon_profil')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $utilisateur = $this->getUser();
        $avatar = $entityManager->getRepository(Avatar::class)->findOneBy(['utilisateur'=>$utilisateur]);

        return $this->render('mon_profil/index.html.twig', [
            'utilisateur' => $utilisateur,
            'avatar' => $avatar
        ]);
    }

    #[Route('majAvatar', name: 'majAvatar')]
    public function majAvatar(Request $request, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = $this->getUser();
        $form = $this->createForm(MajAvatarFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $avatar = $entityManager->getRepository(Avatar::class)->findOneBy(['utilisateur'=>$utilisateur]);
            if(!$avatar){
                $avatar = new Avatar();
            }
            // encode the plain password
            $avatar->setAvatarFile( $form->get('avatarFile')->getData());
            $avatar->setUtilisateur($utilisateur);
            $utilisateur->setAvatar($avatar);
            $entityManager->persist($avatar);
            $entityManager->persist($utilisateur);
            $entityManager->flush();
            return $this->redirectToRoute('mon_profil');
        }

        return $this->render('mon_profil/majAvatar.html.twig', [
            'avatarForm' => $form->createView(),
        ]);
    }
}
