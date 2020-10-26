<?php

namespace App\MessageHandler\Gas;

use App\Entity\Gas\Station;
use App\Entity\Media;
use App\Entity\Review;
use App\Message\Gas\CreateGasStationGooglePlace;
use App\Repository\Gas\StationRepository;
use App\Util\FileSystem;
use App\Util\GooglePlace;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateGasStationGooglePlaceHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var GooglePlace */
    private $googlePlace;

    /** @var StationRepository */
    private $stationRepository;

    public function __construct(EntityManagerInterface $em, GooglePlace $googlePlace, StationRepository $stationRepository)
    {
        $this->em = $em;
        $this->googlePlace = $googlePlace;
        $this->stationRepository = $stationRepository;
    }

    public function __invoke(CreateGasStationGooglePlace $message)
    {
        if (!$this->em->isOpen()) {
            $this->em = $this->em->create(
                $this->em->getConnection(),
                $this->em->getConfiguration()
            );
        }

        $details = $this->googlePlace->details($message->getPlaceId());

        if (false === $details) {
            return;
        }

        $station = $this->stationRepository->findOneBy(['id' => $message->getStationId()]);

        if (!($station instanceof Station)) {
            return;
        }

        $station
            ->setSimilarText($message->getSimilarText())
            ->setDistanceMatch($message->getDistance())
            ->updateAddress($details['address_components'] ?? null)
            ->setVicinity($details['formatted_address'] ?? null)
            ->setName($details['name'] ?? null)
            ->setLongitude($details['geometry']['location']['lng'] ?? null)
            ->setLatitude($details['geometry']['location']['lat'] ?? null)
            ->setIsForced(false)
            ->setIsGoogled(true)
            ->setIsFormatted(true);

        $place = $station->getGooglePlace();

        $place
            ->setGoogleId($details['id'] ?? null)
            ->setPlaceId($details['place_id'] ?? null)
            ->setBusinessStatus($details['business_status'] ?? null)
            ->setIcon($details['icon'] ?? null)
            ->setPhoneNumber($details['international_phone_number'] ?? null)
            ->setCompoundCode($details['plus_code']['compound_code'] ?? null)
            ->setGlobalCode($details['plus_code']['global_code'] ?? null)
            ->setGoogleRating($details['rating'] ?? null)
            ->setReference($details['reference'] ?? null)
            ->setUserRatingsTotal($details['user_ratings_total'] ?? null)
            ->setUrl($details['url'] ?? null)
            ->setNearbysearch($message->getNearBy())
            ->setWebsite($details['website'] ?? null);

        $this->getReviews($station, $details);
        $this->getPreview($station);

        $this->em->persist($station);
        $this->em->persist($place);
        $this->em->flush();
    }

    private function getReviews(Station $station, array $details)
    {
        if (!isset($details['reviews'])) {
            return;
        }

        foreach ($details['reviews'] as $detail) {
            $review = new Review($detail['text'] ?? null, $detail['language'] ?? null, $detail['rating'] ?? null, DateTime::createFromFormat('U', $detail['time'] ?? true),null);
            $station->addReview($review);
        }
    }
    private function getPreview(Station $station)
    {
        foreach (Media::GAS_STATION_IMG as $key => $item) {
            if (false !== strpos(strtolower(FileSystem::stripAccents($station->getName())), $key)) {
                $media = new Media(Media::PUBLIC_GAS_STATION_IMG, $item, "image/jpg","jpg",0);
                $station->setPreview($media);
                break(1);
            }
        }
    }
}