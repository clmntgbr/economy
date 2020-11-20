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

    public function findGasStationByIdForGooglePlace()
    {
        $query = 'SELECT p.id, p.is_closed, p.is_forced, p.is_googled, p.is_formatted, g.place_id, a.longitude, a.latitude, a.street, a.city
                  FROM gas_station p 
                  INNER JOIN google_place g ON g.id = p.google_place_id
                  INNER JOIN address a ON a.id = p.address_id                  
                  WHERE (p.is_closed IS FALSE OR p.is_forced IS TRUE) AND (p.is_googled IS FALSE OR p.is_forced IS TRUE) AND (p.is_formatted IS FALSE OR p.is_forced IS TRUE)  AND a.longitude IS NOT NULL AND a.latitude IS NOT NULL;';


        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $statement->execute();
        return $statement->fetchAllAssociative();
    }

    public function findGasStationByIds(string $ids)
    {
        $query = $this->createQueryBuilder('s')
            ->where('s.id IN (:ids)')
            ->setParameter('ids', explode(",", $ids))
            ->orderBy('s.id', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    public function findGasStationByReviewId(string $id): ?Station
    {
        $query = "SELECT p.station_id
                  FROM gas_station_reviews p                  
                  WHERE p.review_id = $id;";

        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $statement->execute();
        $reviewId = $statement->fetchAssociative();

        $query = $this->createQueryBuilder('s')
            ->where('s.id = :id')
            ->setParameter('id', $reviewId['station_id'] ?? null)
            ->orderBy('s.id', 'ASC')
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findGasStationMap(float $longitude, float $latitude, float $radius, int $max = 500, $filters = [])
    {
        $where = ' AND s.is_closed IS FALSE';

        if ((!empty($filters)) && isset($filters['departments'])) {
            $ids = implode(',', $filters['departments']);
            $where .= sprintf(' AND SUBSTRING(a.postal_code, 1, 2) IN (%s)', $ids);
            $radius = 1000000000000000;
            $max = 1000;
        }

        if ((!empty($filters)) && isset($filters['cities'])) {
            $ids = implode(',', $filters['cities']);
            $where .= sprintf(' AND a.postal_code IN (%s)', $ids);
            $radius = 1000000000000000;
            $max = 1000;
        }

        $query = "SELECT s.id as station_id, SUBSTRING(a.postal_code, 0, 2), (SQRT(POW(69.1 * (a.latitude - $latitude), 2) + POW(69.1 * ($longitude - a.longitude) * COS(a.latitude / 57.3), 2))*1000) as distance
                  FROM address a
                  INNER JOIN gas_station s ON s.address_id = a.id
                  WHERE a.longitude IS NOT NULL AND a.latitude IS NOT NULL $where
                  HAVING `distance` < $radius
                  ORDER BY `distance` ASC LIMIT $max;";

        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $statement->execute();
        $stationIds = $statement->fetchAllAssociative();

        $ids = implode(',', array_map(function ($entry) {
            return $entry['station_id'];
        }, $stationIds));

        return $this->findGasStationByIds($ids);
    }

    public function findGasStationNotClosed()
    {
        $query = "SELECT p.station_id, MAX(p.date) as date, s.is_closed 
                  FROM gas_price p 
                  INNER JOIN gas_station s ON s.id = p.station_id
                  WHERE s.is_closed = false
                  GROUP BY p.station_id;";

        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $statement->execute();
        return $statement->fetchAllAssociative();
    }

    public function findZeroPricesOnStation()
    {
        $query = "SELECT s.id as station_id, COUNT(p.id) AS count, s.is_closed, s.created_at AS date 
                  FROM gas_station s 
                  LEFT JOIN gas_price p ON s.id = p.station_id 
                  WHERE s.is_closed = 0 
                  GROUP BY s.id;";

        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $statement->execute();
        return $statement->fetchAllAssociative();

    }
}
