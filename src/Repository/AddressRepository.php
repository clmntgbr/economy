<?php

namespace App\Repository;

use App\Entity\Address;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Address|null find($id, $lockMode = null, $lockVersion = null)
 * @method Address|null findOneBy(array $criteria, array $orderBy = null)
 * @method Address[]    findAll()
 * @method Address[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Address::class);
    }

    public function findCityWithGasStation()
    {
        $sql = "SELECT a.postal_code, LOWER(MAX(a.city)) as city, count(s.id) as gas_stations
                FROM gas_station s
                INNER JOIN address a ON a.id = s.address_id
                WHERE a.city IS NOT NULL AND a.city != ''
                GROUP BY a.postal_code
                ORDER BY LOWER(MAX(a.city))";

        $getConnection = $this->getEntityManager()->getConnection();
        $statement = $getConnection->prepare($sql);
        $statement->execute();
        return $statement->fetchAll();
    }
}
