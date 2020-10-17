<?php

namespace App\MessageHandler\Gas;

use App\Command\Gas\GasPriceCommand;
use App\Entity\Gas\Service;
use App\Entity\Gas\Station;
use App\Entity\Gas\Type;
use App\Message\Gas\CreateGasService;
use App\Message\Gas\CreateGasStation;
use App\Message\Gas\CreateGasType;
use App\Repository\Gas\ServiceRepository;
use App\Repository\Gas\StationRepository;
use App\Util\FileSystem;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateGasTypeHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em, StationRepository $stationRepository)
    {
        $this->em = $em;
    }

    public function __invoke(CreateGasType $message)
    {
        if (!$this->em->isOpen()) {
            $this->em = $this->em->create(
                $this->em->getConnection(),
                $this->em->getConfiguration()
            );
        }

        $type = new Type($message->getId(), $message->getName());
        $this->em->persist($type);
        $this->em->flush();
    }
}