<?php

namespace App\MessageHandler\Gas;

use App\Entity\Gas\Price;
use App\Entity\Gas\Station;
use App\Entity\Gas\Type;
use App\Message\Gas\CreateGasPrice;
use App\Repository\Gas\StationRepository;
use App\Repository\Gas\TypeRepository;
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
            $this->em = $this->em->create($this->em->getConnection(), $this->em->getConfiguration());
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

        $this->updateLastPrices($station, $message, $price);

        $station->setIsClosed(false);
        $station->setClosedAt(null);

        $this->em->persist($station);
        $this->em->flush();
    }

    private function updateLastPrices(Station $station, CreateGasPrice $message, Price $price)
    {
        $prices = $station->getLastPrices();

        if (count($prices) <= 0) {
            $prices[$message->getTypeId()] = ['id' => $price->getId(), 'date' => $message->getDate(), 'type' => $message->getTypeId(), 'value' => $message->getValue()];
            $station->setLastPrices($prices);
            return;
        }

        foreach ($prices as $key => $value) {
            if (isset($prices[$message->getTypeId()]) && ($prices[$message->getTypeId()]['date'] < $message->getDate())) {
                $prices[$message->getTypeId()] = ['id' => $price->getId(), 'date' => $message->getDate(), 'type' => $message->getTypeId(), 'value' => $message->getValue()];
                continue;
            }

            if (!isset($prices[$message->getTypeId()])) {
                $prices[$message->getTypeId()] = ['id' => $price->getId(), 'date' => $message->getDate(), 'type' => $message->getTypeId(), 'value' => $message->getValue()];
                continue;
            }
        }

        $station->setLastPrices($prices);
    }
}