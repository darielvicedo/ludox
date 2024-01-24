<?php

namespace App\Controller;

use App\Entity\Asset;
use App\Entity\Game;
use App\Form\GameType;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/game')]
class GameController extends AbstractController
{
    private EntityManagerInterface $em;

    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    #[Route('/', name: 'app_game_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($game);
            $this->em->flush();

            return $this->redirectToRoute('app_game_show', [
                'id' => $game->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('game/index.html.twig', [
            'games' => $this->em->getRepository(Game::class)->findBy([], ['name' => 'ASC']),
            'game' => $game,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_game_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('game/new.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_game_show', methods: ['GET'])]
    public function show(Game $game): Response
    {
        return $this->render('game/show.html.twig', [
            'game' => $game,
        ]);
    }

    #[Route('/{id}/add-asset', name: 'app_game_add_asset', methods: ['POST'])]
    public function addAsset(Request $request, Game $game): Response
    {
        $asset = $this->em->getRepository(Asset::class)->find($request->request->get('assetId'));

        $game->addAsset($asset);
        $this->em->flush();

        return new Response();
    }

    #[Route('/{id}/assets-table', name: 'app_game_assets_table', methods: ['GET'])]
    public function loadAssetsTable(Game $game): Response
    {
        return $this->render('game/_table_assets.html.twig', [
            'assets' => $game->getAssets(),
        ]);
    }

    #[Route('/{id}/image', name: 'app_game_image', methods: ['POST'])]
    public function setImage(Request $request, Game $game): Response
    {
        $imageFile = $request->files->get('imageFile');

        // validate image
        $violations = $this->validator->validate($imageFile, [
            new Image([
                'maxSize' => '1M',
                'mimeTypesMessage' => 'Por favor, seleccione una imagen válida.',
            ]),
        ]);
        if ($violations->count() > 0) {
            return $this->json($violations, Response::HTTP_BAD_REQUEST);
        }

        $game->setImageFile($imageFile);
        $this->em->flush();

        return $this->json([
            'fileName' => '/images/games/' . $game->getImageName(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_game_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Game $game, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('game/edit.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_game_delete', methods: ['POST'])]
    public function delete(Request $request, Game $game, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $game->getId(), $request->request->get('_token'))) {
            $entityManager->remove($game);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
    }
}
