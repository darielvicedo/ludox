<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Asset;
use App\Entity\Location;
use App\Enum\AssetCategoryTypeEnum;
use App\Form\AssetType;
use App\Repository\AssetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/asset')]
class AssetController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'app_asset_index', methods: ['GET'])]
    public function index(AssetRepository $assetRepository): Response
    {
        // get fixed actives accounts
        $accounts = $this->em->getRepository(Account::class)->findAllByCodes(Asset::FIXED_ASSETS_ACCOUNTS);

        return $this->render('asset/index.html.twig', [
            'assets' => $this->em->getRepository(Asset::class)->findAll(),
            'categories' => AssetCategoryTypeEnum::getTypesArray(),
            'locations' => $this->em->getRepository(Location::class)->findAll(),
            'accounts' => $accounts,
        ]);
    }

    #[Route('/new', name: 'app_asset_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // get last used code
        $last = $this->em->getRepository(Asset::class)->findOneBy([], ['createdAt' => 'DESC']);
        $code = $last->getCode() + 1;

        $asset = new Asset();
        $asset->setCode($code);
        $form = $this->createForm(AssetType::class, $asset);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($asset);
            $entityManager->flush();

            return $this->redirectToRoute('app_asset_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('asset/_form.html.twig', [
            'asset' => $asset,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dt', name: 'app_asset_dt', methods: ['POST'])]
    public function dt(Request $request): Response
    {
        $filter = $request->request->all();

        $result = $this->em->getRepository(Asset::class)->getByFilter($filter);

        return $this->render('asset/_dt.html.twig', [
            'assets' => $result[0],
            'total' => $result[1],
            'pages' => $result[2],
            'page' => $filter['filter_page'],
        ]);
    }

    #[Route('/{id}', name: 'app_asset_show', methods: ['GET'])]
    public function show(Asset $asset): Response
    {
        return $this->render('asset/show.html.twig', [
            'asset' => $asset,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_asset_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Asset $asset, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AssetType::class, $asset);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_asset_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('asset/edit.html.twig', [
            'asset' => $asset,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_asset_delete', methods: ['POST'])]
    public function delete(Request $request, Asset $asset, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $asset->getId(), $request->request->get('_token'))) {
            $entityManager->remove($asset);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_asset_index', [], Response::HTTP_SEE_OTHER);
    }
}
