<?php

namespace App\Repository;

use App\Entity\BigGame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BigGame|null find($id, $lockMode = null, $lockVersion = null)
 * @method BigGame|null findOneBy(array $criteria, array $orderBy = null)
 * @method BigGame[]    findAll()
 * @method BigGame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BigGameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BigGame::class);
    }

    // /**
    //  * @return BigGame[] Returns an array of BigGame objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BigGame
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
