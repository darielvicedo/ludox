<?php

namespace App\Service;

use App\Entity\Account;
use Doctrine\ORM\EntityManagerInterface;

class DataHelper
{
    private const INCOME_ACCOUNTS = [
        '50.2.900',
    ];

    private const OUTCOME_ACCOUNTS = [
        '50.1.822'
    ];

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Gets the current month net income.
     *
     * @return int
     */
    public function getMonthNetIncome(): int
    {
        $totalIncome = 0;
        $start = new \DateTimeImmutable('first day of this month 00:00:00');
        $end = new \DateTimeImmutable('last day of this month 23:59:59');

        // iterate income accounts
        foreach (self::INCOME_ACCOUNTS as $code) {
            $account = $this->em->getRepository(Account::class)->findOneBy(['code' => $code]);

            $totalIncome += $account->getBalanceInPeriod($start, $end);
        }

        return $totalIncome;
    }
}
