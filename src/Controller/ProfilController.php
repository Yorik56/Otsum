<?php

namespace App\Controller;

use App\Entity\Avatar;
use App\Form\MajAvatarFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/mon/profil', name: 'profile')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $avatar = $entityManager->getRepository(Avatar::class)->findOneBy(['utilisateur'=>$user]);

        return $this->render('profile/index.html.twig', [
            'utilisateur' => $user,
            'avatar' => $avatar
        ]);
    }

    #[Route('majAvatar', name: 'majAvatar')]
    public function majAvatar(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(MajAvatarFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $avatar = $entityManager->getRepository(Avatar::class)->findOneBy(['utilisateur'=>$user]);
            if(!$avatar){
                $avatar = new Avatar();
            }
            // encode the plain password
            $avatar->setAvatarFile( $form->get('avatarFile')->getData());
            $avatar->setUser($user);
            $user->setAvatar($avatar);
            $entityManager->persist($avatar);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('profile');
        }

        return $this->render('profile/majAvatar.html.twig', [
            'avatarForm' => $form->createView(),
        ]);
    }
}
