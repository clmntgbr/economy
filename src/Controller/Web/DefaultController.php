<?php

namespace App\Controller\Web;

use App\Repository\Gas\StationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function index(StationRepository $stationRepository)
    {
        $station = $stationRepository->findOneBy(['id' => 2000002]);

        $station->getPrices();
        die;
    }
}
