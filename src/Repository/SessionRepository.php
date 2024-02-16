<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function findDayActiveSessions(\DateTimeImmutable $date)
    {
        $start = $date->format('Y-m-d 00:00:00');
        $end = $date->format('Y-m-d 23:59:59');

        return $this->createQueryBuilder('s')
            ->setParameters([
                'start' => $start,
                'end' => $end,
            ])
            ->where('s.createdAt BETWEEN :start AND :end')
            ->andWhere('s.finishedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function findDayFinishedSessions(\DateTimeImmutable $date)
    {
        $start = $date->format('Y-m-d 00:00:00');
        $end = $date->format('Y-m-d 23:59:59');

        return $this->createQueryBuilder('s')
            ->setParameters([
                'start' => $start,
                'end' => $end,
            ])
            ->where('s.createdAt BETWEEN :start AND :end')
            ->andWhere('s.finishedAt IS NOT NULL')
            ->orderBy('s.finishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
