<?php

namespace App\Controller\Web;

use App\Entity\Gas\Station;
use App\Entity\Gas\Type;
use App\Repository\Gas\StationRepository;
use App\Repository\Gas\TypeRepository;
use App\Repository\Google\PlaceRepository;
use App\Util\DotEnv;
use App\Util\Gas\StationUtil;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GasController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var TypeRepository */
    private $typeRepository;

    /** @var StationUtil */
    private $stationUtil;

    /** @var DotEnv */
    private $dotEnv;

    public function __construct(EntityManagerInterface $entityManager, TypeRepository $typeRepository, StationUtil $stationUtil, DotEnv $dotEnv)
    {
        $this->entityManager = $entityManager;
        $this->typeRepository = $typeRepository;
        $this->stationUtil = $stationUtil;
        $this->dotEnv = $dotEnv;
    }
    /**
     * @Route("/", name="default")
     */
    public function index(StationRepository $stationRepository, PlaceRepository $placeRepository)
    {
    }

    /**
     * @Route("/gas_stations", name="gas_stations")
     */
    public function gasStationsAction(Request $request, EntityManagerInterface $em, DotEnv $dotEnv)
    {
        return $this->render('gas/gas_stations.html.twig', [
            'KEY' => $dotEnv->load("KEY"),
            'gas_types' => $this->typeRepository->findAll(),
            'sid' => $request->query->get('sid') ?? 0,
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
            'last_prices' => $this->stationUtil->getLastPrices($station),
            'gas_types' => $this->typeRepository->findAll(),
            'google_rating' => $this->stationUtil->getGoogleRating($station->getGooglePlace()->getGoogleRating()),
            'last_six_month_prices' => $this->stationUtil->getYearPrices($station, "LAST_SIX_MONTH"),
            'gas_years' => explode(",", $this->dotEnv->load('GAS_YEARS')),
            'KEY' => $this->dotEnv->load('KEY'),
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
