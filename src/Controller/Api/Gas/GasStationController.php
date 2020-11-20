<?php

namespace App\Controller\Api\Gas;

use App\Entity\Gas\Station;
use App\Repository\Gas\StationRepository;
use App\Util\ResponseBody;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1", name="api_")
 */
class GasStationController extends AbstractFOSRestController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var StationRepository */
    private $stationRepository;

    /** @var ResponseBody */
    private $responseBody;

    public function __construct(EntityManagerInterface $entityManager, ResponseBody $responseBody, StationRepository $stationRepository)
    {
        $this->entityManager = $entityManager;
        $this->responseBody = $responseBody;
        $this->stationRepository = $stationRepository;
    }

    /**
     * @Rest\Get(path="/gas/station/{id}", name="gas_station_id")
     * @ParamConverter("station", class="App\Entity\Gas\Station", options={"mapping": {"id": "id"}})
     * @Rest\View(serializerGroups={"GasStation", "Address", "GasStation:Price", "GasStation:Type", "GasStation:Service", "GooglePlace", "Review:GasStation"})
     */
    public function getGasStationById(Request $request, Station $station)
    {
        return $this->responseBody->create(Response::HTTP_OK, [$station], []);
    }

    /**
     * @Rest\Get(path="/gas/station/{id}/prices", name="gas_station_id_prices")
     * @ParamConverter("station", class="App\Entity\Gas\Station", options={"mapping": {"id": "id"}})
     * @Rest\View(serializerGroups={"GasStation", "GasStation:Price", "GasStation:Type", "GasStation:Service", "GooglePlace", "Review:GasStation"})
     */
    public function getGasStationPricesById(Request $request, Station $station)
    {
        return $this->responseBody->create(Response::HTTP_OK, $station->getPrices(), []);
    }

    /**
     * @Rest\Get(path="/gas/stations/map/{longitude}/{latitude}/{radius}", name="gas_stations_map")
     * @Rest\RequestParam(name="longitude", requirements="\d+", strict=true, map=false, nullable=false)
     * @Rest\RequestParam(name="latitude", requirements="\d+", strict=true, map=false, nullable=false)
     * @Rest\RequestParam(name="radius", requirements="\d+", strict=true, map=false, nullable=false)
     * @Rest\View(serializerGroups={"GasStation", "GasStation:Price", "GasStation:Type", "GasStation:Service", "GooglePlace", "Review:GasStation", "Address"})
     */
    public function getGasStationsMap(Request $request, float $longitude, float $latitude, float $radius = 500)
    {
        return $this->responseBody->create(Response::HTTP_OK, $this->stationRepository->findGasStationMap($longitude, $latitude, $radius), []);
    }
}
