<?php

namespace App\MessageHandler\Gas;

use App\Entity\Gas\Station;
use App\Message\Gas\ClosedGasStation;
use App\Repository\Gas\StationRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ClosedGasStationHandler implements MessageHandlerInterface
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

    public function __invoke(ClosedGasStation $message)
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

        $station->setIsClosed(true);
        $station->setClosedAt(DateTime::createFromFormat('Y-m-d H:i:s', $message->getDate()));
        $this->em->persist($station);
        $this->em->flush();
    }
}