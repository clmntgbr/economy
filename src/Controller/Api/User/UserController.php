<?php

namespace App\Controller\Api\User;

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
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/api", name="api_")
 */
class UserController extends AbstractFOSRestController
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
     * @Rest\Get(path="/user", name="user")
     * @Rest\View
     */
    public function getUserAction(Request $request, Security $security, StationRepository $stationRepository)
    {
        return $this->responseBody->create(Response::HTTP_OK, [$security->getUser()], []);
    }

    /**
     * @Rest\Post(path="/user/like/gas_station/{id}", name="user_like_gas_station")
     * @ParamConverter("station", class="App\Entity\Gas\Station", options={"mapping": {"id": "id"}})
     * @Rest\View
     */
    public function postUserLikeGasStationAction(Request $request, Security $security, Station $station)
    {
        $user = $security->getUser();

        $user->gasStationLike($station);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->responseBody->create(Response::HTTP_OK, [$security->getUser()], []);
    }
}
