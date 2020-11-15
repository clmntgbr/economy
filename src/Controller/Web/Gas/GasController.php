<?php

namespace App\Controller\Web\Gas;

use App\Entity\Gas\Station;
use App\Entity\Gas\Type;
use App\Entity\Review;
use App\Entity\User\User;
use App\Repository\AddressRepository;
use App\Repository\Gas\ServiceRepository;
use App\Repository\Gas\TypeRepository;
use App\Util\DotEnv;
use App\Util\Gas\StationUtil;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/web/gas")
 * @IsGranted("ROLE_USER")
 */
class GasController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var TypeRepository */
    private $typeRepository;

    /** @var ServiceRepository */
    private $serviceRepository;

    /** @var AddressRepository */
    private $addressRepository;

    /** @var StationUtil */
    private $stationUtil;

    /** @var DotEnv */
    private $dotEnv;

    public function __construct(ServiceRepository $serviceRepository, EntityManagerInterface $entityManager, AddressRepository $addressRepository, TypeRepository $typeRepository, StationUtil $stationUtil, DotEnv $dotEnv)
    {
        $this->entityManager = $entityManager;
        $this->typeRepository = $typeRepository;
        $this->addressRepository = $addressRepository;
        $this->serviceRepository = $serviceRepository;
        $this->stationUtil = $stationUtil;
        $this->dotEnv = $dotEnv;
    }

    /**
     * @Route("/stations", name="gas_stations")
     */
    public function gasStationsAction(Request $request)
    {
        return $this->render('gas/gas_stations.html.twig', [
            'KEY' => $this->dotEnv->load("KEY"),
            'gas_types' => $this->typeRepository->findAll(),
            'departments' => $this->stationUtil->getDepartments(),
            'gas_cities' => $this->addressRepository->findCityWithGasStation(),
            'gas_services' => $this->serviceRepository->findGasServiceById(),
        ]);
    }

    /**
     * @Route("/station/{id}", name="gas_station_id")
     * @ParamConverter("station", class="App\Entity\Gas\Station", options={"mapping": {"id": "id"}})
     */
    public function gasStationIdAction(Station $station)
    {
//        dump($station);
//        die;
        return $this->render('gas/gas_station_id.html.twig', [
            'station' => $station,
            'last_prices' => $this->stationUtil->getLastPrices($station),
            'gas_types' => $this->typeRepository->findAll(),
            'google_rating' => $this->stationUtil->getRating($station->getGooglePlace()->getRating(), 'massive', 25),
            'last_six_month_prices' => $this->stationUtil->getYearPrices($station, "LAST_SIX_MONTH"),
            'gas_years' => explode(",", $this->dotEnv->load('GAS_YEARS')),
            'KEY' => $this->dotEnv->load('KEY'),
        ]);
    }

    /**
     * @Route("/station/{id}/comment/add", name="gas_station_id_comment")
     * @ParamConverter("station", class="App\Entity\Gas\Station", options={"mapping": {"id": "id"}})
     */
    public function gasStationIdCommentAction(Station $station, Request $request)
    {
        $comment = $request->request->get("comment");

        if (is_null($comment)) {
            return $this->redirectToRoute('gas_station_id', ['id' => $station->getId()], 400);
        }

        if (!isset($comment['rating']) || !isset($comment['body']) || is_null($comment['rating']) || is_null($comment['body'])) {
            return $this->redirectToRoute('gas_station_id', ['id' => $station->getId()], 400);
        }

        $station->addReview(
            new Review($comment['body'], 'FR', $comment['rating'], (new DateTime())->format('U'), null, null, null, $this->getUser())
        );

        $this->entityManager->persist($station);
        $this->entityManager->flush();

        return $this->redirectToRoute('gas_station_id', ['id' => $station->getId()]);
    }

    /**
     * @Route("/station/{id}/comment/remove", name="gas_station_id_remove_comment")
     * @ParamConverter("review", class="App\Entity\Review", options={"mapping": {"id": "id"}})
     */
    public function gasStationIdRemoveCommentAction(Review $review, Request $request)
    {
        $user = $this->getUser();

        if ($user instanceof User && $user->getId() == $review->getCreatedBy()->getId()) {
            $this->entityManager->remove($review);
            $this->entityManager->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/type/{slug}", name="gas_type_id")
     * @ParamConverter("type", class="App\Entity\Gas\Type", options={"mapping": {"slug": "slug"}})
     */
    public function gasTypeIdAction(Type $type)
    {
        return $this->render('gas/gas_type_id.html.twig', [
            'type' => $type
        ]);
    }
}
