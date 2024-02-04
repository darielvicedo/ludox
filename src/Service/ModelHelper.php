<?php

namespace App\Service;

use App\Entity\Session;
use App\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;

class ModelHelper
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Return available tickets for the day.
     *
     * @return float|int|mixed|string
     */
    public function fetchAvailableTickets()
    {
        return $this->em->getRepository(Ticket::class)->findValidToday();
    }

    public function fetchTodayActiveSessions()
    {
        $today = new \DateTimeImmutable();

        return $this->em->getRepository(Session::class)->findDayActiveSessions($today);
    }

    public function fetchTodayFinishedSessions()
    {
        $today = new \DateTimeImmutable();

        return $this->em->getRepository(Session::class)->findDayFinishedSessions($today);
    }
}
