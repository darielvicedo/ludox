<?php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ticket>
 *
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function findValidToday()
    {
        // expiry date
        $expiry = new \DateTimeImmutable('today 23:59:59');

        return $this->createQueryBuilder('t')
            ->setParameter('expiry', $expiry->format('Y-m-d H:i:s'))
            ->where('t.expiryAt >= :expiry')
            ->getQuery()
            ->getResult();
    }
}
