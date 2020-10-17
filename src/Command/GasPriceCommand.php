<?php

namespace App\Command;

use App\Entity\Gas\Station;
use App\Exceptions\GasPriceCommandException;
use App\Repository\Gas\PriceRepository;
use App\Repository\Gas\ServiceRepository;
use App\Repository\Gas\StationRepository;
use App\Repository\Gas\TypeRepository;
use App\Util\DotEnv;
use App\Util\FileSystem;
use SimpleXMLElement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

    /** @var DotEnv */
    private $dotEnv;

    /** @var string */
    private $gasURL;

    public function __construct(StationRepository $stationRepository, TypeRepository $typeRepository, ServiceRepository $serviceRepository, PriceRepository $priceRepository, DotEnv $dotEnv)
    {
        parent::__construct(self::$defaultName);
        $this->stationRepository = $stationRepository;
        $this->typeRepository = $typeRepository;
        $this->serviceRepository = $serviceRepository;
        $this->priceRepository = $priceRepository;
        $this->dotEnv = $dotEnv;
        $this->gasURL = $this->dotEnv->load("GAS_URL");
    }

    protected function configure()
    {
        $this->setDescription('Creating Up To Date Prices. If A Price Is Created, The Gas Station Will Be Updated As Open.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stations = $this->stationRepository->findGasStationById();
        $types = $this->typeRepository->findGasTypeById();
        $services = $this->serviceRepository->findGasServiceById();
        $prices = $this->priceRepository->findMaxDatePricesGroupByStationAndType();

        $xmlPath = $this->download();

        $elements = simplexml_load_file($xmlPath);

        foreach ($elements as $element) {
            $stationId = (string)$element->attributes()->id;

            if (!isset($this->stations[$stationId])) {
            }
        }

        FileSystem::delete($xmlPath);

        return Command::SUCCESS;
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

    private function createStation(SimpleXMLElement $element, string $stationId): Station
    {

    }
}
