<?php

namespace App\Repository;

use App\Entity\DemandeContact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DemandeContact|null find($id, $lockMode = null, $lockVersion = null)
 * @method DemandeContact|null findOneBy(array $criteria, array $orderBy = null)
 * @method DemandeContact[]    findAll()
 * @method DemandeContact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandeContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeContact::class);
    }


    /**
     * @param $userId
     * @return int|mixed|string
     */
    public function mesContacts($userId)
    {
        return $this->createQueryBuilder('d')
            ->addSelect(
            "CASE
                        WHEN IDENTITY(d.source) = :userId THEN IDENTITY(d.cible) 
                        WHEN IDENTITY(d.cible)  = :userId THEN IDENTITY(d.source) 
                        ELSE 0 
                  END AS contact")
            ->where('IDENTITY(d.source) = :userId OR IDENTITY(d.cible) = :userId')
            ->andWhere("d.flag_etat = :acceptee")
            ->setParameter('userId', $userId)
            ->setParameter('acceptee', DemandeContact::DEMANDE_CONTACT_ACCEPTEE)
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?DemandeContact
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
