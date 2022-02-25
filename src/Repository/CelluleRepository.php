<?php

namespace App\Repository;

use App\Entity\Cellule;
use App\Entity\Partie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cellule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cellule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cellule[]    findAll()
 * @method Cellule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CelluleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cellule::class);
    }

    // /**
    //  * @return Cellule[] Returns an array of Cellule objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Cellule
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getPlaced($idPartie): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.ligne', 'l')
            ->join('l.partie', 'p')
            ->Where('p.id = :val')
            ->andWhere('c.flag_placee = :flag_placee')
            ->addGroupBy('c.valeur')
            ->addOrderBy('l.id')
            ->addOrderBy('c.position')
            ->setParameter('val', $idPartie)
            ->setParameter('flag_placee'  , Cellule::FLAG_PLACEMENT_TRUE)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getNotPresent($idPartie): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.ligne', 'l')
            ->join('l.partie', 'p')
            ->Where('p.id = :val')
            ->andWhere('c.flag_presente = :flag_presente')
            ->addGroupBy('c.valeur')
            ->addOrderBy('l.id')
            ->addOrderBy('c.position')
            ->setParameter('val', $idPartie)
            ->setParameter('flag_presente', Cellule::FLAG_PRESENCE_FALSE)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getPresentAndNotPlaced($idPartie): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.ligne', 'l')
            ->join('l.partie', 'p')
            ->Where('p.id = :val')
            ->andWhere('c.flag_presente = :flag_presente')
            ->andWhere('c.flag_placee = :flag_placee')
            ->addGroupBy('c.valeur')
            ->addOrderBy('l.id')
            ->addOrderBy('c.position')
            ->setParameter('val', $idPartie)
            ->setParameter('flag_presente', Cellule::FLAG_PRESENCE_TRUE)
            ->setParameter('flag_placee'  , Cellule::FLAG_PLACEMENT_FALSE)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getPlacedOrFalse($idPartie, $valeur): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.ligne', 'l')
            ->join('l.partie', 'p')
            ->Where('p.id = :val')
            ->andWhere('c.valeur = :valeur')
            ->andWhere('c.flag_placee = :flag_placee')
            ->addGroupBy('c.valeur')
            ->setParameter('val', $idPartie)
            ->setParameter('valeur', $valeur)
            ->setParameter('flag_placee'  , Cellule::FLAG_PLACEMENT_TRUE)
            ->getQuery()
            ->getResult()
            ;
    }
}
