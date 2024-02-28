<?php

namespace App\Controller;

use App\Service\DataHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reporting')]
class ReportingController extends AbstractController
{
    private DataHelper $data;

    public function __construct(DataHelper $data)
    {
        $this->data = $data;
    }

    #[Route('/', name: 'reporting_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('reporting/reporting.html.twig', []);
    }

    #[Route('/fyp', name: 'reporting_fyp', methods: ['GET'])]
    public function viewFyp(): Response
    {
        // period
        $start = new \DateTimeImmutable('first day of this month 00:00:00');
        $end = new \DateTimeImmutable('last day of this month 23:59:59');

        $model = $this->data->getFYPReportModel($start, $end);

        return $this->render('reporting/_fyp.html.twig', [
            'data' => $model,
        ]);
    }

    #[Route('/api/fyp', name: 'reporting_api_fyp', methods: ['POST'])]
    public function reportFyp(Request $request): Response
    {
        // request data
        $data = $request->toArray();

        // determine period
        $thisPeriod = new \DateTimeImmutable();
        $year = array_key_exists('year', $data) ? $data['year'] : $thisPeriod->format('Y');
        $month = array_key_exists('month', $data) ? $data['month'] : $thisPeriod->format('n');

        $startDate = new \DateTimeImmutable(sprintf('1-%d-%d 00:00:00', $month, $year));
        $endDate = new \DateTimeImmutable(sprintf('last day of %s %d 23:59:59', $startDate->format('M'), $year));

        $lapse = new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate);

        $model = $this->data->getFYPReportModel($startDate, $endDate);

        return $this->render('reporting/reports/fyp.html.twig', [
            'data' => $model,
        ]);
    }
}
