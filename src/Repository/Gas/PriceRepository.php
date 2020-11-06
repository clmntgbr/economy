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
        if (is_null($ids) || $ids == "") {
            return [];
        }

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

    public function findYearPrices(int $stationId, string $year)
    {
        $where = sprintf("(p.date >= '%s' && p.date <= '%s')", sprintf('%s-01-01 00:00:00', $year), sprintf('%s-12-31 23:59:59', $year));
        if ("LAST_SIX_MONTH" == $year) {
            $date = (new \DateTime('now'))->sub(new \DateInterval('P6M'))->format('Y-m-d 00:00:00');
            $where = sprintf("p.date >= '%s'", $date);
        }

        $sql = sprintf("SELECT p.station_id, p.date, p.type_id, p.value, p.id, (p.date_timestamp*1000) as timestamp, t.name, t.slug
                                FROM gas_price p 
                                INNER JOIN gas_type t ON p.type_id = t.id
                                WHERE p.station_id = '%s' AND $where
                                ORDER BY p.type_id, p.date ASC;", $stationId);

        $getConnection = $this->getEntityManager()->getConnection();
        $statement = $getConnection->prepare($sql);
        $statement->execute();

        return $statement->fetchAll();
    }
}
