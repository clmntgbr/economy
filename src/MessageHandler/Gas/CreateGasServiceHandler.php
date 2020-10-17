<?php

namespace App\MessageHandler\Gas;

use App\Command\Gas\GasPriceCommand;
use App\Entity\Gas\Service;
use App\Entity\Gas\Station;
use App\Message\Gas\CreateGasService;
use App\Message\Gas\CreateGasStation;
use App\Repository\Gas\ServiceRepository;
use App\Repository\Gas\StationRepository;
use App\Util\FileSystem;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateGasServiceHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var StationRepository */
    private $stationRepository;

    /** @var ServiceRepository */
    private $serviceRepository;

    public function __construct(EntityManagerInterface $em, StationRepository $stationRepository, ServiceRepository $serviceRepository)
    {
        $this->em = $em;
        $this->stationRepository = $stationRepository;
        $this->serviceRepository = $serviceRepository;
    }

    public function __invoke(CreateGasService $message)
    {
        if (!$this->em->isOpen()) {
            $this->em = $this->em->create(
                $this->em->getConnection(),
                $this->em->getConfiguration()
            );
        }

        $service = $this->serviceRepository->findOneBy(['name' => $message->getName()]);

        if (!($service instanceof Service)) {
            $service = new Service($message->getName());
        }

        $station = $this->stationRepository->findOneBy(['id' => $message->getStationId()]);

        if (!($station instanceof Station)) {
            return;
        }

        $station->addService($service);
        $this->em->persist($station);
        $this->em->flush();
    }
}