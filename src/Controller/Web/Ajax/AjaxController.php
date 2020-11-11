<?php

namespace App\Controller\Web\Ajax;

use App\Entity\Gas\Station;
use App\Repository\Gas\PriceRepository;
use App\Repository\Gas\StationRepository;
use App\Repository\Gas\TypeRepository;
use App\Util\Gas\StationUtil;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @IsGranted("ROLE_USER")
 */
class AjaxController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var StationRepository */
    private $stationRepository;

    /** @var TypeRepository */
    private $typeRepository;

    /** @var PriceRepository */
    private $priceRepository;

    /** @var RouterInterface */
    private $router;

    /** @var SerializerInterface */
    private $serializer;

    /** @var StationUtil */
    private $stationUtil;

    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, StationRepository $stationRepository, SerializerInterface $serializer, TypeRepository $typeRepository, PriceRepository $priceRepository, StationUtil $stationUtil)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->stationRepository = $stationRepository;
        $this->typeRepository = $typeRepository;
        $this->priceRepository = $priceRepository;
        $this->serializer = $serializer;
        $this->stationUtil = $stationUtil;
    }

    /**
     * @Route("/ajax/gas_stations/map", name="ajax_gas_stations_map", methods={"GET"})
     */
    public function ajaxGasStationsMapAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse("This is not an AJAX request.", 400);
        }

        $longitude = $request->query->get('longitude');
        $latitude = $request->query->get('latitude');
        $radius = $request->query->get('radius');

        if (is_null($longitude) || is_null($latitude) || is_null($radius)) {
            return new JsonResponse("Parameters are missing.", 400);
        }

        return new JsonResponse($this->serializer->serialize($this->stationRepository->findGasStationMap($longitude, $latitude, $radius ), 'json', SerializationContext::create()->setGroups(["Ajax:GasStation"])), 200);
    }

    /**
     * @Route("/ajax/gas_station/id", name="ajax_gas_station_id", methods={"GET"})
     */
    public function ajaxGasStationIdAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse("This is not an AJAX request.", 400);
        }

        $stationId = $request->query->get('station_id');

        if (is_null($stationId)) {
            return new JsonResponse("Parameters are missing.", 400);
        }

        $station = $this->stationRepository->findOneBy(['id' => $stationId]);

        if (!($station instanceof Station)) {
            return new JsonResponse(false, 400);
        }

        return new JsonResponse($this->createGasStationContent($station), 200);
    }

    /**
     * @Route("/ajax/gas_prices", name="ajax_gas_prices_year", methods={"GET"})
     */
    public function ajaxGasSPricesByYearAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return new JsonResponse("This is not an AJAX request.", 400);
        }

        $stationId = $request->query->get('station_id');

        if (is_null($stationId)) {
            return new JsonResponse("Parameter `station_id` are missing.", 400);
        }

        $year = $request->query->get('year');

        if (is_null($year)) {
            return new JsonResponse("Parameters `year` are missing.", 400);
        }

        $station = $this->stationRepository->findOneBy(['id' => $stationId]);

        if (!($station instanceof Station)) {
            return new JsonResponse(false, 400);
        }

        return new JsonResponse($this->stationUtil->getYearPrices($station, $year), 200);
    }

    private function createGasStationContent(Station $station): array
    {
        $stationRoute = $this->router->generate("gas_station_id", ['id' => $station->getId()]);

        $types = $this->typeRepository->findBy([], ['id' => 'ASC']);

        $content = sprintf("<a href='%s' style='font-family:Raleway, sans-serif;z-index:1;margin-bottom:5px;position: relative;width: auto;height: 200px;display: block;background-position: center;background-size: cover;background-image: url(%s);'>", $stationRoute, '/asset/img/gas/station/total.jpg');
        if (!is_null($station->getPreview())) {
            $content = sprintf("<a href='%s' style='font-family:Raleway, sans-serif;z-index:1;margin-bottom:5px;position: relative;width: auto;height: 200px;display: block;background-position: center;background-size: cover;background-image: url(%s%s);'>", $stationRoute, $station->getPreview()->getPath(), $station->getPreview()->getName());
        }

        $cssIsClosed = 'station_is_not_closed';
        $htmlIsClosed = '';
        if($station->getIsClosed()) {
            $cssIsClosed = 'station_is_closed';
            $htmlIsClosed = "data-tooltip='Cette Station Essence est fermée.' data-position='top center' data-inverted=''";
        }

        $content .= "<p $htmlIsClosed style='font-family:Raleway, sans-serif;position: absolute;bottom: 0;color: #fff;font-weight: bold;font-size: 15px;display: block;background-color: rgba(28,29,30,0.75);width: 100%;padding: 10px;'>" . $station->getName() . "&nbsp;&nbsp;<i class='far fa-clock $cssIsClosed'></i></p>";

        if (!is_null($station->getGooglePlace()->getUrl())) {
            $content .= sprintf('<a href="%s" target="_blank" data-tooltip="Open in Google Map" data-position="right center" data-inverted="" style="font-family:Raleway, sans-serif;z-index: 2;position: absolute;top: 10px;left: 10px;color: #fff;"><i class="fas fa-external-link-alt"></i></a>', $station->getGooglePlace()->getUrl());
        }

        $content .= '</a>';

        $ids = implode(',', array_map(function ($entry) {
            return $entry['id'];
        }, $station->getLastPrices()));

        $prices = $this->priceRepository->findGasPricesBeforeByStationId($station->getId(), $ids);

        foreach ($types as $type) {
            $typeRoute = $this->router->generate("gas_type_id", ['slug' => $type->getSlug()]);
            foreach ($station->getLastPricesEntities() as $price) {
                if ($price->getType()->getId() == $type->getId()) {
                    if (isset($prices[$type->getId()])) {
                        $priceColor = "orange";
                        if ($prices[$type->getId()]['value'] > $price->getValue()) {
                            $priceColor = "green";
                        }
                        if ($prices[$type->getId()]['value'] < $price->getValue()) {
                            $priceColor = "red";
                        }
                    }
                    $content .= sprintf("<p style='font-size: 13px;font-family:Raleway, sans-serif;margin: 0;padding: 2px 8px;color:$priceColor'><a class='%s %s' href='%s' style='color:black;font-family: Raleway-Bold, sans-serif!important;'>%s </a>: <span style='font-family:Raleway-Bold, sans-serif;'>%s €</span>&nbsp;&nbsp;(%s %s)</p>", $station->getId(), $type->getId(), $typeRoute, $type->getName(), $price->getValue(), 'Dernière MAJ le', ($price->getDate())->format('d/m/Y'));
                }
            }
        }

        $services = '';
        foreach ($station->getServices() as $service) {
            $services .= sprintf("%s, ", $service->getName());
        }

        if ('' !== $services) {
            $content .= sprintf("<p style='font-size: 12px;font-family:Raleway, sans-serif;margin:5px 0;font-weight: 500;padding: 2px 10px;'><i>%s</i></p>", $services);
        }

        if (!is_null($station->getGooglePlace()->getGoogleRating())) {
            $content .= $this->stationUtil->getGoogleRating($station->getGooglePlace()->getGoogleRating());
        }

        $content .= sprintf("<a href='%s' style='font-family:Raleway-Bold, sans-serif;font-size: 15px;width: auto;border-radius: 0 0 8px 8px;text-align: center;display: block;margin-top: 10px;background-color: #4f9c49;color: #fff;padding: 13px 0;'>Accèder à la fiche</a>", $stationRoute, "%");

        return ['content' => $content, 'latitude' => $station->getAddress()->getLatitude(), 'longitude' => $station->getAddress()->getLongitude()];
    }
}
