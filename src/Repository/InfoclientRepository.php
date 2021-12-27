<?php

namespace App\Repository;

use App\Entity\Infoclient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Infoclient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Infoclient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Infoclient[]    findAll()
 * @method Infoclient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoclientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Infoclient::class);
    }

    // /**
    //  * @return Infoclient[] Returns an array of Infoclient objects
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
    public function findOneBySomeField($value): ?Infoclient
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
