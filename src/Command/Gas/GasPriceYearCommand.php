<?php

namespace App\Command\Gas;

use App\Message\Gas\CreateGasPrice;
use App\Message\Gas\CreateGasService;
use App\Message\Gas\CreateGasStation;
use App\Message\Gas\CreateGasType;
use App\Repository\Gas\PriceRepository;
use App\Repository\Gas\ServiceRepository;
use App\Repository\Gas\StationRepository;
use App\Repository\Gas\TypeRepository;
use App\Util\DotEnv;
use App\Util\FileSystem;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class GasPriceYearCommand extends Command
{
    protected static $defaultName = 'app:gas-price-year';

    const PATH = 'public/gas/year/';
    const FILENAME = 'PrixCarburants_annuel_%s.zip';
    const START_YEAR = 2015;
    const END_YEAR = 2019;

    /** @var StationRepository */
    private $stationRepository;

    /** @var TypeRepository */
    private $typeRepository;

    /** @var ServiceRepository */
    private $serviceRepository;

    /** @var PriceRepository */
    private $priceRepository;

    /** @var MessageBusInterface */
    private $messageBus;

    /** @var DotEnv */
    private $dotEnv;

    /** @var string */
    private $gasURL;

    /** @var array */
    private $stations;

    /** @var array */
    private $prices;

    /** @var array */
    private $services;

    /** @var array */
    private $types;

    public function __construct(
        StationRepository $stationRepository,
        TypeRepository $typeRepository,
        ServiceRepository $serviceRepository,
        PriceRepository $priceRepository,
        DotEnv $dotEnv,
        MessageBusInterface $messageBus
    ) {
        parent::__construct(self::$defaultName);
        $this->stationRepository = $stationRepository;
        $this->typeRepository = $typeRepository;
        $this->serviceRepository = $serviceRepository;
        $this->priceRepository = $priceRepository;
        $this->dotEnv = $dotEnv;
        $this->messageBus = $messageBus;
        $this->gasURL = $this->dotEnv->load("GAS_URL");
        $this->stations = [];
        $this->types = [];
        $this->services = [];
        $this->prices = [];
    }

    protected function configure()
    {
        $this->setDescription('Creating Up To Date Prices. If A Price Is Created, The Gas Station Will Be Updated As Open.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->stations = $this->stationRepository->findGasStationById();
        $this->types = $this->typeRepository->findGasTypeById();
        $this->services = $this->serviceRepository->findGasServiceByName();

        for ($i = self::START_YEAR; $i <= self::END_YEAR; $i++) {
            if (false === FileSystem::exist(self::PATH, sprintf(self::FILENAME, $i))) {
                return Command::FAILURE;
            }

            if (false === FileSystem::unzip(sprintf("%s%s", self::PATH, sprintf(self::FILENAME, $i)), self::PATH)) {
                return Command::FAILURE;
            }

            if (false === $xmlPath = FileSystem::find(self::PATH, "%\.(xml)$%i")) {
                return Command::FAILURE;
            }

            $elements = simplexml_load_file($xmlPath);

            $progressBar = new ProgressBar($output, count($elements));

            foreach ($elements as $element) {
                $stationId = (string)$element->attributes()->id;

                if (!isset($this->stations[$stationId])) {
                    $this->createGasStation($element, $stationId);
                }

                $this->createGasPrices($element, $stationId);

                $progressBar->advance();
            }
        }
        return Command::SUCCESS;
    }

    private function createGasStation(SimpleXMLElement $element, string $stationId): void
    {
        $this->messageBus->dispatch(new CreateGasStation(
            $stationId,
            (string)$element->attributes()->pop,
            (string)$element->attributes()->cp,
            (string)$element->attributes()->longitude,
            (string)$element->attributes()->latitude,
            (string)$element->adresse,
            (string)$element->ville,
            "FRANCE",
            json_decode(str_replace("@", "", json_encode($element)), true)
        ));

        $this->createGasServices($stationId, $element);

        $this->prices[$stationId] = [];
        $this->stations[$stationId] = ["id" => $stationId];
    }

    private function createGasServices(string $stationId, SimpleXMLElement $element): void
    {
        foreach ((array)$element->services->service as $item) {
            $this->messageBus->dispatch(new CreateGasService($stationId, $item));
            if (!isset($this->services[$item])) {
                $this->services[$item] = $item;
            }
        }
    }

    private function createGasPrices(SimpleXMLElement $element, string $stationId): void
    {
        foreach ($element->prix as $item) {
            $typeId = (string)$item->attributes()->id;

            if (null === $typeId || "" === $typeId) {
                continue;
            }

            if (!isset($this->types[$typeId])) {
                $this->createGasTypes($item);
            }

            $typeId = $this->types[$typeId]['id'];

            $date = (string)$item->attributes()->maj;

            $date = str_replace("T", " ", substr($date, 0, 19));

            if (null === $date || "" === $date) {
                continue;
            }

            if ((false === isset($this->prices[$stationId][$typeId])) || ($date > $this->prices[$stationId][$typeId])) {
                $this->messageBus->dispatch(new CreateGasPrice($typeId, $stationId, $date, (string)$item->attributes()->valeur));
            }
        }
    }

    private function createGasTypes(SimpleXMLElement $element): void
    {
        $this->messageBus->dispatch(new CreateGasType((string)$element->attributes()->id, (string)$element->attributes()->nom));
        $this->types[(string)$element->attributes()->id] = ['id' => (string)$element->attributes()->id];
    }
}