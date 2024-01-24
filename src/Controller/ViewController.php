<?php

namespace App\Controller;

use App\Entity\Asset;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/view')]
class ViewController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route(path: '/form/assets-select', name: 'view_form_assets_select', methods: ['GET'])]
    public function loadAssetsSelect(): Response
    {
        $availableGames = $this->em->getRepository(Asset::class)->findAvailableGames();

        return $this->render('game/_assets_select.html.twig', [
            'assets' => $availableGames,
        ]);
    }
}
