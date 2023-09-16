<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\AccountEntry;
use App\Form\AccountType;
use App\Form\AccountEntryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/account')]
class AccountController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'app_account_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ACCOUNT')]
    public function index(Request $request): Response
    {
        $account = new Account();
        $form = $this->createForm(AccountType::class, $account);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($account);
            $this->em->flush();

            $this->addFlash('success', 'Cuenta creada!');

            return $this->redirectToRoute('app_account_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/index.html.twig', [
            'accounts' => $this->em->getRepository(Account::class)->findAll(),
            'account' => $account,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_account_show', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ACCOUNT')]
    public function show(Request $request, Account $account): Response
    {
        $form = $this->createForm(AccountType::class, $account);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'La cuenta ha sido modificada con éxito!');

            return $this->redirectToRoute('app_account_show', [
                'id' => $account->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        $entry = new AccountEntry();
        $entry->setAccount($account);
        $entryForm = $this->createForm(AccountEntryType::class, $entry);

        $entryForm->handleRequest($request);
        if ($entryForm->isSubmitted() && $entryForm->isValid()) {
            $this->em->persist($entry);
            $this->em->flush();

            $this->addFlash('success', 'Anotado!');

            return $this->redirectToRoute('app_account_show', [
                'id' => $account->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/show.html.twig', [
            'account' => $account,
            'entry' => $entry,
            'entryForm' => $entryForm->createView(),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_account_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ACCOUNT')]
    public function edit(Request $request, Account $account, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_account_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/edit.html.twig', [
            'account' => $account,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_account_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ACCOUNT')]
    public function delete(Request $request, Account $account, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $account->getId(), $request->request->get('_token'))) {
            $entityManager->remove($account);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_account_index', [], Response::HTTP_SEE_OTHER);
    }
}
