<?php

namespace App\Controller\Web\Gas;

use App\Entity\Gas\Station;
use App\Entity\Gas\Type;
use App\Entity\Review;
use App\Entity\User\User;
use App\Repository\Gas\StationRepository;
use App\Repository\Gas\TypeRepository;
use App\Repository\Google\PlaceRepository;
use App\Repository\User\UserRepository;
use App\Util\DotEnv;
use App\Util\Gas\StationUtil;
use DateTime;
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

    /** @var UserRepository */
    private $userRepository;

    /** @var StationUtil */
    private $stationUtil;

    /** @var DotEnv */
    private $dotEnv;

    public function __construct(EntityManagerInterface $entityManager, TypeRepository $typeRepository, UserRepository $userRepository, StationUtil $stationUtil, DotEnv $dotEnv)
    {
        $this->entityManager = $entityManager;
        $this->typeRepository = $typeRepository;
        $this->userRepository = $userRepository;
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
    public function gasStationsAction(Request $request)
    {
        return $this->render('gas/gas_stations.html.twig', [
            'KEY' => $this->dotEnv->load("KEY"),
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
            'google_rating' => $this->stationUtil->getGoogleRating($station->getGooglePlace()->getGoogleRating(), 'massive', 25),
            'last_six_month_prices' => $this->stationUtil->getYearPrices($station, "LAST_SIX_MONTH"),
            'gas_years' => explode(",", $this->dotEnv->load('GAS_YEARS')),
            'KEY' => $this->dotEnv->load('KEY'),
        ]);
    }

    /**
     * @Route("/gas_station/{id}/comment", name="gas_station_id_comment")
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

        $user = $this->getFakeUser();

        $station->addReview(
            new Review($comment['body'], 'FR', $comment['rating'], (new DateTime())->format('U'), null, null, null, $user)
        );

        $this->entityManager->persist($station);
        $this->entityManager->flush();

        return $this->redirectToRoute('gas_station_id', ['id' => $station->getId()]);
    }

    /**
     * @Route("/gas_station/{id}/remove_comment", name="gas_station_id_remove_comment")
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
     * @Route("/gas_type/{slug}", name="gas_type_id")
     * @ParamConverter("type", class="App\Entity\Gas\Type", options={"mapping": {"slug": "slug"}})
     */
    public function gasTypeIdAction(Type $type)
    {
        return $this->render('gas/gas_type_id.html.twig', [
            'type' => $type
        ]);
    }

    private function getFakeUser()
    {
        return $this->userRepository->findOneBy(['email' => 'totogm@gmail.com']);
    }
}