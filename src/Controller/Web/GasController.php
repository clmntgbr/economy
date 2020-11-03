<?php

namespace App\Controller\Web;

use App\Entity\Department;
use App\Entity\Gas\Station;
use App\Entity\Gas\Type;
use App\Repository\Gas\StationRepository;
use App\Repository\Google\PlaceRepository;
use App\Util\DotEnv;
use App\Util\Gas;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GasController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function index(StationRepository $stationRepository, PlaceRepository $placeRepository)
    {
        $entity = $placeRepository->findOneBy(['id' => 540]);
        $entity = $stationRepository->findOneBy(['id' => 10110002]);
        dump($entity);
        die;
    }

    /**
     * @Route("/gas_stations", name="gas_stations")
     */
    public function gasStationsAction(Request $request, EntityManagerInterface $em, DotEnv $dotEnv)
    {
        return $this->render('gas/gas_stations.html.twig', [
            'KEY' => $dotEnv->load("KEY"),
        ]);
    }

    /**
     * @Route("/gas_station/{id}", name="gas_station_id")
     * @ParamConverter("station", class="App\Entity\Gas\Station", options={"mapping": {"id": "id"}})
     */
    public function gasStationIdAction(Station $station)
    {
        return $this->render('gas/gas_station_id.html.twig', [
            'station' => $station,
        ]);
    }

    /**
     * @Route("/gas_type/{slug}", name="gas_type_id")
     * @ParamConverter("type", class="App\Entity\Gas\Type", options={"mapping": {"slug": "slug"}})
     */
    public function gasTypeIdAction(Type $type)
    {
        return $this->render('gas/gas_type_id.html.twig', [
            'type' => $type
        ]);
    }
}
