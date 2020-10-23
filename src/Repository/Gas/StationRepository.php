<?php

namespace App\Repository\Gas;

use App\Entity\Gas\Station;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Station|null find($id, $lockMode = null, $lockVersion = null)
 * @method Station|null findOneBy(array $criteria, array $orderBy = null)
 * @method Station[]    findAll()
 * @method Station[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Station::class);
    }

    public function findGasStationById()
    {
        $query = $this->createQueryBuilder('s')
            ->select('s.id')
            ->orderBy('s.id', 'ASC')
            ->indexBy('s', 's.id')
            ->getQuery();

        return $query->getResult();
    }

    public function findGasStationNotClosed()
    {
        $query = "SELECT p.station_id, MAX(p.date) as date, s.is_closed 
                  FROM gas_price p 
                  INNER JOIN gas_station s ON s.id = p.station_id
                  WHERE s.is_closed = false
                  GROUP BY p.station_id;" ;

        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $statement->execute();
        return $statement->fetchAllAssociative();
    }
}
