<?php

namespace App\Listener\Gas;

use App\Entity\Gas\Station;
use App\Entity\Review;
use App\Entity\User\User;
use App\Repository\Gas\PriceRepository;
use App\Repository\Gas\StationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class StationListener
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var PriceRepository */
    private $priceRepository;

    /** @var StationRepository */
    private $stationRepository;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(StationRepository $stationRepository, EntityManagerInterface $em, PriceRepository $priceRepository, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->stationRepository = $stationRepository;
        $this->priceRepository = $priceRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function postLoad(LifecycleEventArgs $event)
    {
        $entity = $event->getObject();
        if ($entity instanceof Station) {
            $ids = implode(',', array_map(function ($entry) {
                return $entry['id'];
            }, $entity->getLastPrices()));
            $entity->setLastPricesEntities($this->priceRepository->findGasPriceByIds($ids));
        }
    }

    public function onFlush(OnFlushEventArgs $onFlushEventArgs)
    {
        $em = $onFlushEventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Station) {
                foreach ($uow->getScheduledEntityInsertions() as $change) {
                    if ($change instanceof Review) {
                        if (is_null($entity->getGooglePlace()->getGoogleRating())) {
                            $this->setUpdateGoogleRatingWhenNull($entity, $change);
                        }
                        if (!is_null($entity->getGooglePlace()->getGoogleRating())) {
                            $this->setUpdateGoogleRating($entity, $change);
                        }
                        $em->persist($entity->getGooglePlace());
                        $uow->computeChangeSet($em->getClassMetadata(get_class($entity->getGooglePlace())), $entity->getGooglePlace());
                    }
                }
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof Review) {
                $station = $this->stationRepository->findGasStationByReviewId($entity->getId());
                if ($station instanceof Station) {
                    if (!is_null($station->getGooglePlace()->getGoogleRating())) {
                        $this->setRemoveGoogleRating($station, $entity);
                    }
                    $em->persist($station->getGooglePlace());
                    $uow->computeChangeSet($em->getClassMetadata(get_class($station->getGooglePlace())), $station->getGooglePlace());
                }
            }
        }
    }

    public function setUpdateGoogleRatingWhenNull(Station $entity, Review $change)
    {
        $entity->getGooglePlace()->setRating($change->getRating());
    }

    public function setUpdateGoogleRating(Station $entity, $change)
    {
        $entity->getGooglePlace()->setRating(($entity->getGooglePlace()->getGoogleRating() + $change->getRating())/2);
    }

    public function setRemoveGoogleRating(Station $entity, Review $review)
    {
        $reviewsRating = 0;
        $reviewsNumber = 0;

        foreach ($entity->getReviews() as $value) {
            if ($value->getCreatedBy() instanceof User) {
                $reviewsRating = $value->getRating() + $reviewsRating;
                $reviewsNumber++;
            }
        }

        $reviewsRating = $reviewsRating - $review->getRating();

        $entity->getGooglePlace()->setRating(round(($entity->getGooglePlace()->getGoogleRating() + $reviewsRating)/$reviewsNumber, 2));
    }

    public function preUpdate(PreUpdateEventArgs $event)
    {
        $entity = $event->getObject();
        if ($entity instanceof Station) {
            dump($entity);
            die;
        }
    }
}