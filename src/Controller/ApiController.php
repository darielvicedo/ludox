<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\AccountEntry;
use App\Entity\Client;
use App\Entity\Coupon;
use App\Entity\Ticket;
use App\Service\AccountHelper;
use App\Service\LudoxHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
    private EntityManagerInterface $em;

    private LudoxHelper $ludox;

    private AccountHelper $accountant;

    public function __construct(
        EntityManagerInterface $em,
        LudoxHelper            $ludox,
        AccountHelper          $accountant,
    )
    {
        $this->em = $em;
        $this->ludox = $ludox;
        $this->accountant = $accountant;
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
        $ticket = new Ticket();
        $ticket
            ->setExpiryAt($expire)
            ->setClient($client);

        // get basic price
        $price = $_ENV['DAILY_TICKET_PRICE'];

        // check if birthday
        if ($client->isBirthday()) {
            $price = 0;
        } else {
            // coupon & price
            $coupon = $this->em->getRepository(Coupon::class)->findOneBy(['code' => strtoupper($data['ticketCoupon'])]);
            if ($coupon) {
                $price = $this->ludox->calculateCouponTicketPrice($coupon, $price);
                $ticket->setCoupon($coupon);
                $coupon->setConsumedAt(new \DateTimeImmutable());
            }
        }

        // set the final price
        $ticket->setPrice($price);
        $this->em->persist($ticket);

        // persist to db
        $this->em->flush();

        // account operation
        if ($ticket->getPrice() > 0) {
            // register sale
            $this->accountant->registerSale($ticket->getPrice());

            // register cash
            $this->accountant->increaseCash($ticket->getPrice());
        }

        return new Response();
    }
}
