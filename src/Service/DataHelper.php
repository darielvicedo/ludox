<?php

namespace App\Service;

use App\Entity\Account;
use Doctrine\ORM\EntityManagerInterface;

class DataHelper
{
    private const INCOME_ACCOUNTS = [
        '50.2.900' => 'Ventas',
    ];

    private const OUTCOME_ACCOUNTS = [
        '50.1.822' => 'Electricidad',
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
        foreach (self::INCOME_ACCOUNTS as $code => $name) {
            $account = $this->em->getRepository(Account::class)->findOneBy(['code' => $code]);

            $totalIncome += $account->getBalanceInPeriod($start, $end);
        }

        return $totalIncome;
    }

    public function getFYPReportModel(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        $model = [
            'income' => [
                'accounts' => [],
                'total' => 0,
            ],
            'outcome' => [
                'accounts' => [],
                'total' => 0,
            ],
            'tax' => [],
        ];

        // iterate income accounts
        foreach (self::INCOME_ACCOUNTS as $code => $name) {
            $account = $this->em->getRepository(Account::class)->findOneBy(['code' => $code]);

            $balance = abs($account->getBalanceInPeriod($start, $end));

            $model['income']['accounts'][$name] = $balance;
            $model['income']['total'] += $balance;
        }

        // iterate outcome accounts
        foreach (self::OUTCOME_ACCOUNTS as $code => $name) {
            $account = $this->em->getRepository(Account::class)->findOneBy(['code' => $code]);

            $balance = abs($account->getBalanceInPeriod($start, $end));

            $model['outcome']['accounts'][$name] = $balance;
            $model['outcome']['total'] += $balance;
        }

        // benefits
        $model['benefits'] = $model['income']['total'] - $model['outcome']['total'];

        // taxes
        $cam = (10 * $model['benefits']) / 100;
        $model['tax']['CAM (10%)'] = $cam;

        return $model;
    }
}
