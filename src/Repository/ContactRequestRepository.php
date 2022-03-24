<?php

namespace App\Repository;

use App\Entity\ContactRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContactRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactRequest[]    findAll()
 * @method ContactRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactRequest::class);
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
                        WHEN IDENTITY(d.source) = :userId THEN IDENTITY(d.target) 
                        WHEN IDENTITY(d.target)  = :userId THEN IDENTITY(d.source) 
                        ELSE 0 
                  END AS contact")
            ->where('IDENTITY(d.source) = :userId OR IDENTITY(d.target) = :userId')
            ->andWhere("d.flag_state = :acceptee")
            ->setParameter('userId', $userId)
            ->setParameter('acceptee', ContactRequest::REQUEST_CONTACT_ACCEPTED)
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
