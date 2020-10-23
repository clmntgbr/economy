<?php

namespace App\Command\Gas;

use App\Message\Gas\ClosedGasStation;
use App\Repository\Gas\PriceRepository;
use App\Repository\Gas\StationRepository;
use DateInterval;
use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class GasStationClosedCommand extends Command
{
    protected static $defaultName = 'app:gas-station-closed';

    const CLOSED_MONTH = 'P6M';

    /** @var StationRepository */
    private $stationRepository;

    /** @var PriceRepository */
    private $priceRepository;

    /** @var MessageBusInterface */
    private $messageBus;

    /** @var array */
    private $stations;

    public function __construct(
        StationRepository $stationRepository,
        PriceRepository $priceRepository,
        MessageBusInterface $messageBus
    ) {
        parent::__construct(self::$defaultName);
        $this->stationRepository = $stationRepository;
        $this->priceRepository = $priceRepository;
        $this->messageBus = $messageBus;
        $this->stations = [];
    }

    protected function configure()
    {
        $this->setDescription('Add a short description for your command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Finding Gas Stations ...');

        $this->stations = $this->stationRepository->findGasStationNotClosed();

        $date = ((new DateTime('now'))->sub(new DateInterval(self::CLOSED_MONTH)))->format('Y-m-d 00:00:00');

        $progressBar = new ProgressBar($output, count($this->stations));

        foreach ($this->stations as $station) {
            if ($station['date'] < $date) {
                $this->messageBus->dispatch(new ClosedGasStation($station['station_id'], $station['date']));
            }
            $progressBar->advance();
        }

        $progressBar->finish();

        return Command::SUCCESS;
    }
}
