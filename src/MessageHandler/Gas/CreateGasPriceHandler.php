<?php

namespace App\MessageHandler\Gas;

use App\Command\Gas\GasPriceCommand;
use App\Entity\Gas\Price;
use App\Entity\Gas\Service;
use App\Entity\Gas\Station;
use App\Entity\Gas\Type;
use App\Message\Gas\CreateGasPrice;
use App\Message\Gas\CreateGasService;
use App\Message\Gas\CreateGasStation;
use App\Message\Gas\CreateGasType;
use App\Repository\Gas\ServiceRepository;
use App\Repository\Gas\StationRepository;
use App\Repository\Gas\TypeRepository;
use App\Util\FileSystem;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateGasPriceHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var StationRepository */
    private $stationRepository;

    /** @var TypeRepository */
    private $typeRepository;

    public function __construct(EntityManagerInterface $em, StationRepository $stationRepository, TypeRepository $typeRepository)
    {
        $this->em = $em;
        $this->stationRepository = $stationRepository;
        $this->typeRepository = $typeRepository;
    }

    public function __invoke(CreateGasPrice $message)
    {
        if (!$this->em->isOpen()) {
            $this->em = $this->em->create(
                $this->em->getConnection(),
                $this->em->getConfiguration()
            );
        }

        $type = $this->typeRepository->findOneBy(['id' => $message->getTypeId()]);

        if (!($type instanceof Type)) {
            return;
        }

        $station = $this->stationRepository->findOneBy(['id' => $message->getStationId()]);

        if (!($station instanceof Station)) {
            return;
        }

        $price = new Price($type, $station, $message->getValue(), $message->getDate());

        $this->em->persist($price);
        $this->em->flush();
    }
}