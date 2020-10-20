<?php

namespace App\MessageHandler\Gas;

use App\Entity\Gas\Type;
use App\Message\Gas\CreateGasType;
use App\Repository\Gas\StationRepository;
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