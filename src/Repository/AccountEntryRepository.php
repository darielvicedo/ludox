<?php

namespace App\Repository;

use App\Entity\AccountEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccountEntry>
 *
 * @method AccountEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccountEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccountEntry[]    findAll()
 * @method AccountEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccountEntry::class);
    }

//    /**
//     * @return AccountEntry[] Returns an array of AccountEntry objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AccountEntry
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
