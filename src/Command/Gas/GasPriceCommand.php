<?php

namespace App\Command\Gas;

use App\Entity\Gas\Service;
use App\Entity\Gas\Station;
use App\Entity\Gas\Type;
use App\Exceptions\GasPriceCommandException;
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

class GasPriceCommand extends Command
{
    protected static $defaultName = 'app:gas-price';

    const PATH = "public/gas/";
    const FILENAME = "gas-price.zip";

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
        $max = 100;
        $i = 0;
        $this->stations = $this->stationRepository->findGasStationById();
        $this->types = $this->typeRepository->findGasTypeById();
        $this->services = $this->serviceRepository->findGasServiceByName();
        $this->prices = $this->priceRepository->findMaxDatePricesGroupByStationAndType();

        $xmlPath = $this->download();

        $elements = simplexml_load_file($xmlPath);

        $progressBar = new ProgressBar($output, count($elements));

        foreach ($elements as $element) {
            $stationId = (string)$element->attributes()->id;

            if (!isset($this->stations[$stationId])) {
                $this->createGasStation($element, $stationId);
            }

            $this->createGasPrices($element, $stationId);

            $progressBar->advance();

            $i++;

            if ($max == $i) {
                break(1);
            }
        }

        FileSystem::delete($xmlPath);

        $progressBar->finish();

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
//            $this->messageBus->dispatch(new CreateGasService($stationId, $item));
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

    /**
     * @return bool|string
     * @throws GasPriceCommandException
     */
    private function download()
    {
        FileSystem::delete(self::PATH, self::FILENAME);

        FileSystem::download($this->gasURL, self::FILENAME, self::PATH);

        if (false === FileSystem::exist(self::PATH, self::FILENAME)) {
            throw new GasPriceCommandException();
        }

        if (false === FileSystem::unzip(sprintf("%s%s", self::PATH, self::FILENAME), self::PATH)) {
            throw new GasPriceCommandException();
        }

        FileSystem::delete(self::PATH, self::FILENAME);

        if (false === $xmlPath = FileSystem::find(self::PATH, "%\.(xml)$%i")) {
            throw new GasPriceCommandException();
        }

        return $xmlPath;
    }
}
