<?php

namespace App\MessageHandler\Gas;

use App\Command\Gas\GasPriceCommand;
use App\Entity\Gas\Service;
use App\Entity\Gas\Station;
use App\Message\Gas\CreateGasStation;
use App\Repository\Gas\StationRepository;
use App\Util\FileSystem;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateGasStationHandler implements MessageHandlerInterface
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

    public function __invoke(CreateGasStation $message)
    {
        if (!$this->em->isOpen()) {
            $this->em = $this->em->create(
                $this->em->getConnection(),
                $this->em->getConfiguration()
            );
        }

        $station = $this->stationRepository->findOneBy(['id' => $message->getStationId()]);

        if ($station instanceof Station) {
            return;
        }

        $station = new Station(
            $message->getStationId(),
            $message->getPop(),
            $message->getCp(),
            $message->getLongitude(),
            $message->getLatitude(),
            $message->getStreet(),
            $message->getCity(),
            $message->getCountry(),
            $message->getElement()
        );
        $this->em->persist($station);
        $this->em->flush();
    }
}