<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
