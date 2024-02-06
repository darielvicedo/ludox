<?php

namespace App\Controller;

use App\Entity\Game;
use App\Service\ModelHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{
    private EntityManagerInterface $em;

    private ModelHelper $model;

    public function __construct(EntityManagerInterface $em, ModelHelper $model)
    {
        $this->em = $em;
        $this->model = $model;
    }

    /**
     * @throws \Exception
     */
    #[Route('/', name: 'app', methods: ['GET'])]
    public function app(): Response
    {
        $games = $this->em->getRepository(Game::class)->findBy([], ['name' => 'ASC']);
        $tickets = $this->model->getAvailableClientsToday();

        return $this->render('app.html.twig', [
            'games' => $games,
            'tickets' => $tickets,
        ]);
    }

    #[Route('/admin', name: 'admin', methods: ['GET'])]
    public function admin(): Response
    {
        return $this->render('admin.html.twig');
    }
}
