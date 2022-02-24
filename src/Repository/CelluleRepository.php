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

    /*
    SELECT cellule.* FROM cellule
    JOIN ligne ON ligne.id = cellule.ligne_id
    JOIN partie ON partie.id = ligne.partie_id
    WHERE partie.id = '10'
    GROUP BY
    cellule.valeur,
    cellule.flag_presente,
    cellule.flag_placee
    ORDER BY ligne.id, cellule.position;
    */

    public function getMajKeyBoard($idPartie): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('p.id = :val')
            ->join('c.ligne', 'l')
            ->join('l.partie', 'p')
            ->addGroupBy('c.valeur')
            ->addGroupBy('c.flag_presente')
            ->addGroupBy('c.flag_placee')
            ->addOrderBy('l.id')
            ->addOrderBy('c.position')
            ->setParameter('val', $idPartie)
            ->getQuery()
            ->getResult()
            ;
    }
}
