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
        $getConnection = $this->getEntityManager()->getConnection();
        $statement = $getConnection->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll();

        $array = [];

        foreach ($result as $item) {
            $array[$item['station_id']][$item['type_id']] = $item['date'];
        }

        return $array;
    }
}
