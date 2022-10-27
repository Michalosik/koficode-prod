<?php

namespace App\Repository;

use App\Entity\ContactEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContactEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactEntry[]    findAll()
 * @method ContactEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactEntry::class);
    }

    // /**
    //  * @return ContactEntry[] Returns an array of ContactEntry objects
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
    public function findOneBySomeField($value): ?ContactEntry
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
