<?php

namespace App\Service;

use App\Entity\DemandeContact;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class Utils
{
    /** KernelInterface $appKernel */
    private $appKernel;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, KernelInterface $appKernel)
    {
        $this->appKernel     = $appKernel;
        $this->entityManager = $entityManager;
    }

    /**
     * Return the contact liste of the current user
     *
     * @param $idUser
     * @return array
     */
    public function getContacts($idUser): array
    {
        $contacts = $this->entityManager->getRepository(entityName: DemandeContact::class)
            ->mesContacts(userId: $idUser);
        $usersContact = [];
        foreach ($contacts as $index => $contact) {
            $usersContact[] = $this->entityManager->getRepository(entityName: Utilisateur::class)
                ->findOneBy([
                    'id' => $contact['contact']
                ]);
        }
        return $usersContact;
    }

    public function getRandomWord($parametre_longueur_mot){
        // Parameters to get a word
        $nomsFichiers = [
            7  => 'sept_lettres.txt',
            8  => 'huit_lettres.txt',
            9  => 'neuf_lettres.txt',
            10 => 'dix_lettres.txt',
        ];
        //--- Get a random word
        $projectDir = $this->appKernel->getProjectDir();
        $file = $projectDir . '/public/'.$nomsFichiers[$parametre_longueur_mot];
        $file_arr = file($file);
        $num_lines = count($file_arr);
        $last_arr_index = $num_lines - 1;
        $rand_index = rand(0, $last_arr_index);

        return $file_arr[$rand_index];
    }
}
