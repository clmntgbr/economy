<?php

namespace App\Controller\Web;

use App\Repository\Gas\StationRepository;
use App\Repository\Google\PlaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function index(StationRepository $stationRepository, PlaceRepository $placeRepository)
    {
        $entity = $placeRepository->findOneBy(['id' => 540]);
        dump($entity);
        die;
    }
}
