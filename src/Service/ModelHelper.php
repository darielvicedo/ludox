<?php

namespace App\Service;

use App\Entity\Session;
use App\Entity\Ticket;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * Get available clients for a game session.
     *
     * @return Collection
     */
    public function getAvailableClientsToday(): Collection
    {
        $tickets = new ArrayCollection($this->fetchAvailableTickets());
        return $tickets->filter(function (Ticket $ticket) {
            return !$ticket->getClient()->hasActiveSession();
        });
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
