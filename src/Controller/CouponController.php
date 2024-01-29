<?php

namespace App\Controller;

use App\Entity\Coupon;
use App\Form\CouponType;
use App\Repository\CouponRepository;
use App\Service\LudoxHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/coupon')]
class CouponController extends AbstractController
{
    private EntityManagerInterface $em;

    private LudoxHelper $ludox;

    public function __construct(EntityManagerInterface $em, LudoxHelper $ludox)
    {
        $this->em = $em;
        $this->ludox = $ludox;
    }

    #[Route('/', name: 'coupon_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $coupon = new Coupon();
        $form = $this->createForm(CouponType::class, $coupon);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // create code
            do {
                $code = $this->ludox->generateUniqueKey();
                $exists = $this->em->getRepository(Coupon::class)->findOneBy(['code' => $code]);
            } while ($exists);
            $coupon->setCode($code);

            $this->em->persist($coupon);
            $this->em->flush();

            return $this->redirectToRoute('coupon_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('coupon/index.html.twig', [
            'coupons' => $this->em->getRepository(Coupon::class)->findBy([], ['consumedAt' => 'ASC']),
            'coupon' => $coupon,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/consume', name: 'coupon_consume', methods: ['POST'])]
    public function consume(Coupon $coupon): Response
    {
        $coupon->setConsumedAt(new \DateTimeImmutable());

        $this->em->flush();

        return $this->redirectToRoute('coupon_index');
    }

    #[Route('/{id}/edit', name: 'coupon_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Coupon $coupon, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CouponType::class, $coupon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('coupon_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('coupon/edit.html.twig', [
            'coupon' => $coupon,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'coupon_delete', methods: ['POST'])]
    public function delete(Request $request, Coupon $coupon, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $coupon->getId(), $request->request->get('_token'))) {
            $entityManager->remove($coupon);
            $entityManager->flush();
        }

        return $this->redirectToRoute('coupon_index', [], Response::HTTP_SEE_OTHER);
    }
}
