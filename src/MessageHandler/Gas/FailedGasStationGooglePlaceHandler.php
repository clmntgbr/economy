<?php

namespace App\MessageHandler\Gas;

use App\Entity\Gas\Station;
use App\Message\Gas\FailedGasStationGooglePlace;
use App\Repository\Gas\StationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FailedGasStationGooglePlaceHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var StationRepository */
    private $stationRepository;

    public function __construct(EntityManagerInterface $em, StationRepository $stationRepository)
    {
        $this->em = $em;
        $this->stationRepository = $stationRepository;
    }

    public function __invoke(FailedGasStationGooglePlace $message)
    {
        if (!$this->em->isOpen()) {
            $this->em = $this->em->create(
                $this->em->getConnection(),
                $this->em->getConfiguration()
            );
        }

        $station = $this->stationRepository->findOneBy(['id' => $message->getStationId()]);

        if (!($station instanceof Station)) {
            return;
        }

        $station
            ->setIsGoogled($message->isGoogled())
            ->setIsForced($message->isForced());

        $this->em->persist($station);
        $this->em->flush();
    }
}