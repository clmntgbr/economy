<?php

namespace App\Repository\Google;

use App\Entity\Google\Place;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Place|null find($id, $lockMode = null, $lockVersion = null)
 * @method Place|null findOneBy(array $criteria, array $orderBy = null)
 * @method Place[]    findAll()
 * @method Place[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Place::class);
    }

    public function findGasStationByPlaceId()
    {
        $query = $this->createQueryBuilder('s')
            ->select('s.placeId')
            ->orderBy('s.placeId', 'ASC')
            ->where('s.placeId IS NOT NULL')
            ->indexBy('s', 's.placeId')
            ->getQuery();

        return $query->getResult();
    }
}
