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

    public function getGoogleRating($googleRating, string $ratingSize = 'huge', int $fontSize = 15): string
    {
        if(is_null($googleRating)) {
            return '<i>there is no rating at this moment.</i>';
        }

        $value = (float)$googleRating;
        $integer = (int)$googleRating;

        $html = sprintf("<div style='margin-top: 10px;text-align: center;'><span style='font-weight: bolder;font-size: %spx;top: -2px;position: relative;'>%s</span>&nbsp;&nbsp;<div class='ui %s star rating'>", $fontSize, $value, $ratingSize);

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

    public function getDepartments()
    {
        return [
            ["Ain","01"],
            ["Aisne","02"],
            ["Allier","03"],
            ["Alpes-de-Haute-Provence","04"],
            ["Hautes-alpes","05"],
            ["Alpes-maritimes","06"],
            ["Ardèche","07"],
            ["Ardennes","08"],
            ["Ariège","09"],
            ["Aube","10"],
            ["Aude","11"],
            ["Aveyron","12"],
            ["Bouches-du-Rhône","13"],
            ["Calvados","14"],
            ["Cantal","15"],
            ["Charente","16"],
            ["Charente-maritime","17"],
            ["Cher","18"],
            ["Corrèze","19"],
            ["Corse-du-sud","2A"],
            ["Haute-Corse","2B"],
            ["Côte-d'Or","21"],
            ["Côtes-d'Armor","22"],
            ["Creuse","23"],
            ["Dordogne","24"],
            ["Doubs","25"],
            ["Drôme","26"],
            ["Eure","27"],
            ["Eure-et-loir","28"],
            ["Finistère","29"],
            ["Gard","30"],
            ["Haute-garonne","31"],
            ["Gers","32"],
            ["Gironde","33"],
            ["Hérault","34"],
            ["Ille-et-vilaine","35"],
            ["Indre","36"],
            ["Indre-et-loire","37"],
            ["Isère","38"],
            ["Jura","39"],
            ["Landes","40"],
            ["Loir-et-cher","41"],
            ["Loire","42"],
            ["Haute-loire","43"],
            ["Loire-atlantique","44"],
            ["Loiret","45"],
            ["Lot","46"],
            ["Lot-et-garonne","47"],
            ["Lozère","48"],
            ["Maine-et-loire","49"],
            ["Manche","50"],
            ["Marne","51"],
            ["Haute-marne","52"],
            ["Mayenne","53"],
            ["Meurthe-et-moselle","54"],
            ["Meuse","55"],
            ["Morbihan","56"],
            ["Moselle","57"],
            ["Nièvre","58"],
            ["Nord","59"],
            ["Oise","60"],
            ["Orne","61"],
            ["Pas-de-calais","62"],
            ["Puy-de-dôme","63"],
            ["Pyrénées-atlantiques","64"],
            ["Hautes-Pyrénées","65"],
            ["Pyrénées-orientales","66"],
            ["Bas-rhin","67"],
            ["Haut-rhin","68"],
            ["Rhône","69"],
            ["Haute-saône","70"],
            ["Saône-et-loire","71"],
            ["Sarthe","72"],
            ["Savoie","73"],
            ["Haute-savoie","74"],
            ["Paris","75"],
            ["Seine-maritime","76"],
            ["Seine-et-marne","77"],
            ["Yvelines","78"],
            ["Deux-sèvres","79"],
            ["Somme","80"],
            ["Tarn","81"],
            ["Tarn-et-Garonne","82"],
            ["Var","83"],
            ["Vaucluse","84"],
            ["Vendée","85"],
            ["Vienne","86"],
            ["Haute-vienne","87"],
            ["Vosges","88"],
            ["Yonne","89"],
            ["Territoire de belfort","90"],
            ["Essonne","91"],
            ["Hauts-de-seine","92"],
            ["Seine-Saint-Denis","93"],
            ["Val-de-marne","94"],
            ["Val-d'Oise","95"],
            ["Guadeloupe","971"],
            ["Martinique","972"],
            ["Guyane","973"],
            ["La réunion","974"],
            ["Mayotte","976"],
        ] ;
    }
}