<?php

namespace App\Util\Gas;

use App\Entity\Gas\Station;
use App\Repository\Gas\PriceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;

class StationUtil
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var PriceRepository */
    private $priceRepository;

    /** @var RouterInterface */
    private $router;

    public function __construct(EntityManagerInterface $entityManager, PriceRepository $priceRepository, RouterInterface $router)
    {
        $this->entityManager = $entityManager;
        $this->priceRepository = $priceRepository;
        $this->router = $router;
    }

    public function getGoogleRating($googleRating): string
    {
        if(is_null($googleRating)) {
            return '<i>there is no rating at this moment.</i>';
        }

        $value = (float)$googleRating;
        $integer = (int)$googleRating;

        $html = sprintf("<div style='margin-top: 10px;text-align: center;'><span style='font-weight: 500;font-size: 15px;top: -2px;position: relative;'>%s</span>&nbsp;&nbsp;<div class='ui huge star rating'>", $value);

        for ($i=1;$i<=$integer;$i++) {
            $html .= "<i class='icon active'></i>";
        }

        for ($i=$integer;$i<5;$i++) {
            $html .= "<i class='icon'></i>";
        }

        $html .= "</div></div>";

        return $html;
    }

    public function getYearPrices(Station $station, $year = null)
    {
        if(is_null($year)) {
            $year = (new \DateTime('now'))->format('Y');
        }

        $results = $this->priceRepository->findYearPrices($station->getId(), $year);

        $prices = [];

        foreach ($results as $price) {
            $prices[$price['slug']]['price'][] = $price;
            $prices[$price['slug']]['type_name'] = $price['name'];
            $prices[$price['slug']]['slug'] = $price['slug'];
            $prices[$price['slug']]['year'] = $year;
        }

        return $prices;
    }

    public function getLastPrices(Station $station)
    {
        $ids = implode(',', array_map(function ($entry) {
            return $entry['id'];
        }, $station->getLastPrices()));

        $prices = $this->priceRepository->findGasPricesBeforeByStationId($station->getId(), $ids);

        $values = [];

        foreach ($station->getLastPricesEntities() as $price) {
            if (isset($prices[$price->getType()->getId()])) {
                $values[$price->getType()->getId()] = [
                    'id' => $price->getId(),
                    'value' => $price->getValue(),
                    'date' => $price->getDate(),
                    'color' => 'orange',
                ];
                if ($prices[$price->getType()->getId()]['value'] > $price->getValue()) {
                    $values[$price->getType()->getId()]['color'] = 'green';
                }
                if ($prices[$price->getType()->getId()]['value'] < $price->getValue()) {
                    $values[$price->getType()->getId()]['color'] = 'red';
                }
            }
        }

        return $values;
    }
}