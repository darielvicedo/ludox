<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\AccountEntry;
use Doctrine\ORM\EntityManagerInterface;

class AccountHelper
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Registers a sale entry.
     *
     * @param int $amount
     * @return void
     */
    public function registerSale(int $amount): void
    {
        $account = $this->em->getRepository(Account::class)->findOneBy(['code' => Account::ACCOUNT_SALES]);
        $concept = "Pase diario";

        $this->createCreditEntry($account, $concept, $amount);
    }

    /**
     * Increases cash account.
     *
     * @param int $amount
     * @return void
     */
    public function increaseCash(int $amount): void
    {
        $account = $this->em->getRepository(Account::class)->findOneBy(['code' => Account::ACCOUNT_CASH]);
        $concept = "Venta de ticket";

        $this->createDebitEntry($account, $concept, $amount);
    }

    /**
     * Registers a credit operation.
     *
     * @param Account $account
     * @param string $concept
     * @param int $amount
     * @return void
     */
    private function createCreditEntry(Account $account, string $concept, int $amount): void
    {
        $entry = new AccountEntry();
        $entry
            ->setConcept($concept)
            ->setCredit($amount)
            ->setAccount($account);

        $this->em->persist($entry);
        $this->em->flush();
    }

    /**
     * Registers a debit operation.
     *
     * @param Account $account
     * @param string $concept
     * @param int $amount
     * @return void
     */
    private function createDebitEntry(Account $account, string $concept, int $amount): void
    {
        $entry = new AccountEntry();
        $entry
            ->setConcept($concept)
            ->setDebit($amount)
            ->setAccount($account);

        $this->em->persist($entry);
        $this->em->flush();
    }
}
