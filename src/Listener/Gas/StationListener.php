<?php

namespace App\Listener\Gas;

use App\Entity\Gas\Station;
use App\Repository\Gas\PriceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;

class StationListener
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var PriceRepository */
    private $priceRepository;

    public function __construct(EntityManagerInterface $em, PriceRepository $priceRepository)
    {
        $this->em = $em;
        $this->priceRepository = $priceRepository;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof Station) {
            $ids = implode(', ', array_map(function ($entry) {
                return $entry['id'];
            }, $entity->getLastPrices()));

            $entity->setLastPrices($this->priceRepository->findGasPriceByIds($ids));
        }
    }
}