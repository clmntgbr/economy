<?php

namespace App\MessageHandler\Gas;

use App\Entity\Gas\Station;
use App\Entity\Google\Place;
use App\Entity\Media;
use App\Entity\Review;
use App\Message\Gas\CreateGasStationGooglePlace;
use App\Repository\Gas\StationRepository;
use App\Repository\Google\PlaceRepository;
use App\Util\FileSystem;
use App\Util\Google\ApiPlace;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateGasStationGooglePlaceHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var ApiPlace */
    private $googlePlace;

    /** @var StationRepository */
    private $stationRepository;

    /** @var PlaceRepository */
    private $placeRepository;

    public function __construct(EntityManagerInterface $em, ApiPlace $googlePlace, StationRepository $stationRepository, PlaceRepository $placeRepository)
    {
        $this->em = $em;
        $this->googlePlace = $googlePlace;
        $this->stationRepository = $stationRepository;
        $this->placeRepository = $placeRepository;
    }

    public function __invoke(CreateGasStationGooglePlace $message)
    {
        try {
            if (!$this->em->isOpen()) {
                $this->em = $this->em->create($this->em->getConnection(), $this->em->getConfiguration());
            }

            $station = $this->stationRepository->findOneBy(['id' => $message->getStationId()]);

            if (!($station instanceof Station)) {
                return;
            }

            $station
                ->setSimilarText($message->getSimilarText())
                ->setDistanceMatch($message->getDistance())
                ->setNearbysearchForGooglePlace($message->getNearBy())
                ->setIsForced(false)
                ->setIsGoogled(true)
                ->setIsFormatted(false);

            $place = $this->placeRepository->findOneBy(['placeId' => $message->getPlaceId()]);

            if ($place instanceof Place) {
                $this->em->persist($station);
                $this->em->flush();
                return;
            }

            $details = $this->googlePlace->details($message->getPlaceId());

            if (isset($details['failed']) && false === $details['failed']) {
                $station->setDetailsForGooglePlace($details['response']);
                $this->em->persist($station);
                $this->em->flush();
                return;
            }

            $station
                ->updateAddress($details['address_components'] ?? null)
                ->setVicinity($details['formatted_address'] ?? null)
                ->setName($details['name'] ?? null)
                ->setLongitude($details['geometry']['location']['lng'] ?? null)
                ->setLatitude($details['geometry']['location']['lat'] ?? null)
                ->setGoogleIdForGooglePlace($details['id'] ?? null)
                ->setPlaceIdForGooglePlace($details['place_id'] ?? null)
                ->setBusinessStatusForGooglePlace($details['business_status'] ?? null)
                ->setIconForGooglePlace($details['icon'] ?? null)
                ->setPhoneNumberForGooglePlace($details['international_phone_number'] ?? null)
                ->setCompoundCodeForGooglePlace($details['plus_code']['compound_code'] ?? null)
                ->setGlobalCodeForGooglePlace($details['plus_code']['global_code'] ?? null)
                ->setGoogleRatingForGooglePlace($details['rating'] ?? null)
                ->setRatingForGooglePlace($details['rating'] ?? null)
                ->setReferenceForGooglePlace($details['reference'] ?? null)
                ->setOpeningHoursForGooglePlace($details['opening_hours']['weekday_text'] ?? null)
                ->setUserRatingsTotalForGooglePlace($details['user_ratings_total'] ?? null)
                ->setUrlForGooglePlace($details['url'] ?? null)
                ->setDetailsForGooglePlace($details)
                ->setWebsiteForGooglePlace($details['website'] ?? null)
                ->setIsFormatted(true);

            $this->getReviews($station, $details);
            $this->getPreview($station);

            $this->em->persist($station);
            $this->em->flush();

        } catch (\Exception $exception) {
            dump($station->getName(), $details);
            dump($exception->getMessage(), $station->getId());
            die;
        }
    }

    private function getReviews(Station $station, array $details)
    {
        if (!isset($details['reviews'])) {
            return;
        }

        foreach ($details['reviews'] as $detail) {
            $review = new Review(
                $detail['text'] ?? null,
                $detail['language'] ?? null,
                $detail['rating'] ?? null,
                $detail['time'] ?? null,
                $detail['author_name'] ?? null,
                $detail['author_url'] ?? null,
                $detail['profile_photo_url'] ?? null,
                null
            );
            $station->addReview($review);
        }
    }
    private function getPreview(Station $station)
    {
        foreach (Media::GAS_STATION_IMG as $key => $item) {
            if (false !== strpos(strtolower(FileSystem::stripAccents($station->getName())), $key)) {
                $media = new Media(Media::PUBLIC_GAS_STATION_IMG, $item, 'image/jpg','jpg',0);
                $station->setPreview($media);
                break(1);
            }
        }
    }
}