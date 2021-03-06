<?php

namespace App\Command\Gas;

use App\Message\Gas\CreateGasStationGooglePlace;
use App\Message\Gas\FailedGasStationGooglePlace;
use App\Repository\Gas\StationRepository;
use App\Repository\Google\PlaceRepository;
use App\Util\Google\ApiPlace;
use Cocur\Slugify\Slugify;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class GasGooglePlaceCommand extends Command
{
    protected static $defaultName = 'app:gas-google-place';

    /** @var StationRepository */
    private $stationRepository;

    /** @var PlaceRepository */
    private $placeRepository;

    /** @var MessageBusInterface */
    private $messageBus;

    /** @var ApiPlace */
    private $apiPlace;

    /** @var Slugify */
    private $slugify;

    /** @var array */
    private $stations;

    /** @var array */
    private $placeIds;

    public function __construct(
        StationRepository $stationRepository,
        MessageBusInterface $messageBus,
        ApiPlace $apiPlace,
        PlaceRepository $placeRepository
    ) {
        parent::__construct(self::$defaultName);
        $this->stationRepository = $stationRepository;
        $this->messageBus = $messageBus;
        $this->apiPlace = $apiPlace;
        $this->placeRepository = $placeRepository;
        $this->slugify = new Slugify();;
        $this->stations = [];
        $this->placeIds = [];
    }

    protected function configure()
    {
        $this->setDescription('Add a short description for your command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->stations = $this->stationRepository->findGasStationByIdForGooglePlace();
        $this->placeIds = $this->placeRepository->findGasStationByPlaceId();

        $progressBar = new ProgressBar($output, count($this->stations));

        foreach ($this->stations as $value) {

            if (strpos($value['id'], '75') !== 0 && strpos($value['id'], '94') !== 0) {
                $progressBar->advance();
                continue;
            }

            $interests = [];
            $nearBy = $this->apiPlace->nearbysearch($value['longitude'], $value['latitude'], 'gas_station');

            if ($nearBy === false) {
                $progressBar->advance();
                continue;
            }

            foreach ($nearBy as $result) {
                $distance = $this->apiPlace->getDistanceBetweenTwoCoordinates($value['longitude'], $value['latitude'], $result['geometry']['location']['lng'], $result['geometry']['location']['lat']);
                if (750 >= $distance && !(isset($this->placeIds[$result['place_id']]))) {
                    $interests[(string)$distance] = $result;
                }
            }

            if (0 > count($interests)) {
                $this->messageBus->dispatch(new FailedGasStationGooglePlace($value['id'], true, false, $nearBy));
                $progressBar->advance();
                continue;
            }

            ksort($interests);

            $similarText = [];

            foreach ($interests as $distance => $interest) {
                similar_text($this->slugify->slugify(sprintf('%s, %s', $value['street'], $value['city'])), $this->slugify->slugify($interest['vicinity']), $percent);
                if (85 <= $percent) {
                    $similarText[(string)$percent] = ['details' => $interest, 'distance' => $distance];
                }
            }

            if (0 >= count($similarText)) {
                $this->messageBus->dispatch(new FailedGasStationGooglePlace($value['id'], true, false, $interests));
                $progressBar->advance();
                continue;
            }

            ksort($similarText, SORT_NUMERIC);

            $this->messageBus->dispatch(new CreateGasStationGooglePlace(
                $value['id'],
                end($similarText)['details']['place_id'],
                (float)end($similarText)['distance'],
                (float)array_key_last($similarText),
                $interests
            ));

            $progressBar->advance();
        }

        $progressBar->finish();

        return Command::SUCCESS;
    }
}
