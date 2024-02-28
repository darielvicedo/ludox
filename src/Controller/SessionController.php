<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Game;
use App\Entity\Session;
use App\Form\SessionType;
use App\Repository\SessionRepository;
use App\Service\ModelHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/session')]
class SessionController extends AbstractController
{
    private EntityManagerInterface $em;

    private ModelHelper $model;

    public function __construct(EntityManagerInterface $em, ModelHelper $model)
    {
        $this->em = $em;
        $this->model = $model;
    }

    #[Route('/', name: 'session_index', methods: ['GET'])]
    public function index(SessionRepository $sessionRepository): Response
    {
        return $this->render('session/index.html.twig', [
            'sessions' => $sessionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'session_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = new Session();
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute('session_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('session/new.html.twig', [
            'session' => $session,
            'form' => $form,
        ]);
    }

    #[Route('/api/get-active-sessions', name: 'session_api_get_active', methods: ['GET'])]
    public function loadActiveSessions(): Response
    {
        $sessions = $this->model->fetchTodayActiveSessions();

        return $this->render('session/_active_sessions.html.twig', [
            'sessions' => $sessions,
        ]);
    }

    #[Route('/api/get-finished-sessions', name: 'session_api_get_finished', methods: ['GET'])]
    public function loadFinishedSessions(): Response
    {
        $sessions = $this->model->fetchTodayFinishedSessions();

        return $this->render('session/_active_sessions.html.twig', [
            'sessions' => $sessions,
        ]);
    }

    #[Route('/api/new-session', name: 'session_api_new', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $data = $request->request->all();

        // check for clients
        if (!array_key_exists('sessionClient', $data)) {
            return new Response("Seleccione al menos un cliente.", Response::HTTP_BAD_REQUEST);
        }

        // fetch game
        $game = $this->em->getRepository(Game::class)->find($data['sessionGame']);

        // create session
        $session = new Session();
        $session
            ->setGame($game);

        // add players
        foreach ($data['sessionClient'] as $clientId) {
            $client = $this->em->getRepository(Client::class)->find($clientId);
            $session->addClient($client);
        }

        $this->em->persist($session);
        $this->em->flush();

        return new Response();
    }

    #[Route('/api/finish', name: 'session_api_finish', methods: ['POST'])]
    public function finish(Request $request): Response
    {
        $data = $request->toArray();

        $session = $this->em->getRepository(Session::class)->find($data['sessionId']);
        $session->setFinishedAt(new \DateTimeImmutable());

        $this->em->flush();

        return new Response();
    }

    #[Route('/api/get-available-players', name: 'session_api_get_availableplayers', methods: ['GET'])]
    public function getAvailablePlayers(): Response
    {
        $tickets = $this->model->getAvailableClientsToday();

        return $this->render('session/_checks_available_players.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    #[Route('/{id}', name: 'session_show', methods: ['GET'])]
    public function show(Session $session): Response
    {
        return $this->render('session/show.html.twig', [
            'session' => $session,
        ]);
    }

    #[Route('/{id}/edit', name: 'session_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Session $session, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('session_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('session/edit.html.twig', [
            'session' => $session,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'session_delete', methods: ['POST'])]
    public function delete(Request $request, Session $session, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $session->getId(), $request->request->get('_token'))) {
            $entityManager->remove($session);
            $entityManager->flush();
        }

        return $this->redirectToRoute('session_index', [], Response::HTTP_SEE_OTHER);
    }
}
