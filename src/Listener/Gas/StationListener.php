<?php

namespace App\Listener\Gas;

use App\Entity\Gas\Station;
use App\Entity\User\User;
use App\Repository\Gas\PriceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class StationListener
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var PriceRepository */
    private $priceRepository;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(EntityManagerInterface $em, PriceRepository $priceRepository, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->priceRepository = $priceRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof Station && $this->tokenStorage->getToken() && $this->tokenStorage->getToken()->getUser() instanceof User) {
            $ids = implode(',', array_map(function ($entry) {
                return $entry['id'];
            }, $entity->getLastPrices()));

            $entity->setLastPrices($this->priceRepository->findGasPriceByIds($ids));
        }
    }
}