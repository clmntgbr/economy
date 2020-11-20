<?php

namespace App\Command\Gas;

use App\Message\Gas\ClosedGasStation;
use App\Repository\Gas\PriceRepository;
use App\Repository\Gas\StationRepository;
use App\Util\Logger\Command as LoggerCommand;
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

    /** @var LoggerCommand */
    private $command;

    /** @var array */
    private $stations;

    /** @var array */
    private $logger;

    public function __construct(
        StationRepository $stationRepository,
        PriceRepository $priceRepository,
        MessageBusInterface $messageBus,
        LoggerCommand $command
    ) {
        parent::__construct(self::$defaultName);
        $this->stationRepository = $stationRepository;
        $this->priceRepository = $priceRepository;
        $this->messageBus = $messageBus;
        $this->command = $command;
        $this->stations = [];
        $this->logger = [
            'stations' => 0,
        ];
    }

    protected function configure()
    {
        $this->setDescription('Add a short description for your command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stationFound = [];

        $this->command->start();

        $io = new SymfonyStyle($input, $output);

        $io->title('Finding Gas Stations With Old Prices ...');

        $this->stations = $this->stationRepository->findGasStationNotClosed();

        $date = ((new DateTime('now'))->sub(new DateInterval(self::CLOSED_MONTH)))->format('Y-m-d 00:00:00');

        $progressBar = new ProgressBar($output, count($this->stations));

        foreach ($this->stations as $station) {
            if ($station['date'] < $date) {
                $stationFound[$station['station_id']] = $station;
                $this->messageBus->dispatch(new ClosedGasStation($station['station_id'], $station['date']));
                $this->logger['stations']++;
            }
            $progressBar->advance();
        }

        $progressBar->finish();

        $io->writeln('');
        $io->writeln('');

        $io->title('Finding Gas Stations With Zero Prices ...');

        $io->writeln('');

        $this->stations = $this->stationRepository->findZeroPricesOnStation();

        $progressBar = new ProgressBar($output, count($this->stations));

        foreach ($this->stations as $station) {
            if ('0' == $station['count'] && '0' == $station['is_closed'] && (!(isset($stationFound[$station['station_id']])))) {
                $this->messageBus->dispatch(new ClosedGasStation($station['station_id'], $station['date']));
                $this->logger['stations']++;
            }
            $progressBar->advance();
        }
        $progressBar->finish();

        $io->writeln('');

        $this->command->end(self::$defaultName, 'command', $this->logger);

        return Command::SUCCESS;
    }
}
