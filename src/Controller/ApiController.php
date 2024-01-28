<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\AccountEntry;
use App\Entity\Client;
use App\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/ticket/new', name: 'api_ticket_new', methods: ['POST'])]
    public function createTicket(Request $request): Response
    {
        $data = $request->request->all();

        // check if client exists or create
        $client = $this->em->getRepository(Client::class)->findOneBy(['ci' => $data['ticketCi']]);
        if (!$client) {
            $client = new Client();
            $client
                ->setCi($data['ticketCi'])
                ->setName($data['ticketName']);
            $this->em->persist($client);
        }

        // create ticket
        $expire = new \DateTimeImmutable('today 23:59:59');
        $price = $_ENV['DAILY_TICKET_PRICE'];

        $ticket = new Ticket();
        $ticket
            ->setExpiryAt($expire)
            ->setPrice($price)
            ->setClient($client);
        $this->em->persist($ticket);

        // account operation
        $salesAccountCode = $_ENV['SALES_ACCOUNT'];
        $account = $this->em->getRepository(Account::class)->findOneBy(['code' => $salesAccountCode]);

        $accountEntry = new AccountEntry();
        $accountEntry
            ->setConcept('Venta de pase diario')
            ->setCredit($ticket->getPrice())
            ->setAccount($account);
        $this->em->persist($accountEntry);

        // persist to db
        $this->em->flush();

        return new Response();
    }
}