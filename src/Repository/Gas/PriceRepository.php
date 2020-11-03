<?php

namespace App\Repository\Gas;

use App\Entity\Gas\Price;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Price|null find($id, $lockMode = null, $lockVersion = null)
 * @method Price|null findOneBy(array $criteria, array $orderBy = null)
 * @method Price[]    findAll()
 * @method Price[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Price::class);
    }

    public function findMaxDatePricesGroupByStationAndType()
    {
        $sql = "SELECT p.station_id, p.type_id, MAX(p.date) as date 
                FROM gas_price p 
                GROUP BY p.station_id, p.type_id 
                ORDER BY p.station_id ASC";

        $statement = $this->getEntityManager()->getConnection()->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAllAssociative();

        $array = [];

        foreach ($result as $item) {
            $array[$item['station_id']][$item['type_id']] = $item['date'];
        }

        return $array;
    }

    public function findGasPricesBeforeByStationId($stationId, $ids)
    {
        $sql = sprintf("SELECT MAX(id) as id
                FROM gas_price
                WHERE station_id = $stationId AND id not in (%s)
                GROUP BY type_id", $ids);

        $statement = $this->getEntityManager()->getConnection()->prepare($sql);
        $statement->execute();

        $ids = implode(',', array_map(function ($entry) {
            return $entry['id'];
        }, $statement->fetchAllAssociative()));

        $sql = sprintf("SELECT type_id, value 
                FROM gas_price
                WHERE id in (%s)", $ids);

        $statement = $this->getEntityManager()->getConnection()->prepare($sql);
        $statement->execute();

        return $statement->fetchAllAssociativeIndexed();
    }

    public function findGasPriceByIds(string $ids)
    {
        $query = $this->createQueryBuilder('s')
            ->where('s.id IN (:ids)')
            ->setParameter('ids', explode(",", $ids))
            ->orderBy('s.id', 'ASC')
            ->indexBy('s', 's.id')
            ->getQuery();

        return $query->getResult();
    }
}
