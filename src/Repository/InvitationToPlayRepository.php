<?php

namespace App\Repository;

use App\Entity\InvitationToPlay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InvitationToPlay|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvitationToPlay|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvitationToPlay[]    findAll()
 * @method InvitationToPlay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvitationToPlayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvitationToPlay::class);
    }

    // /**
    //  * @return InvitationToPlay[] Returns an array of InvitationToPlay objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InvitationToPlay
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
