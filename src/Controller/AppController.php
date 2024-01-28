<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route('/', name: 'app', methods: ['GET'])]
    public function app(): Response
    {
        return $this->render('app.html.twig', []);
    }

    #[Route('/admin', name: 'admin', methods: ['GET'])]
    public function admin(): Response
    {
        return $this->render('admin.html.twig');
    }
}
