<?php

namespace App\Controller\Api\Gas;

use App\Entity\Gas\Station;
use App\Util\ResponseBody;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Route("/api", name="api_")
 */
class GasStationController extends AbstractFOSRestController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ResponseBody */
    private $responseBody;

    public function __construct(EntityManagerInterface $entityManager, ResponseBody $responseBody)
    {
        $this->entityManager = $entityManager;
        $this->responseBody = $responseBody;
    }

    /**
     * @Rest\Get(path="/gas/station/{id}", name="gas_station_id")
     * @ParamConverter("station", class="App\Entity\Gas\Station", options={"mapping": {"id": "id"}})
     * @Rest\View
     */
    public function getGasStationById(Request $request, Station $station)
    {
        return $this->responseBody->create(Response::HTTP_OK, [$station], []);
    }
}
