<?php

namespace App\Repository\Gas;

use App\Entity\Gas\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Service|null find($id, $lockMode = null, $lockVersion = null)
 * @method Service|null findOneBy(array $criteria, array $orderBy = null)
 * @method Service[]    findAll()
 * @method Service[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    public function findGasServiceByName()
    {
        $query = $this->createQueryBuilder('s')
            ->select('s.name, s.id')
            ->orderBy('s.name', 'ASC')
            ->indexBy('s', 's.name')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return Service[]
     */
    public function findGasServiceById()
    {
        $query = $this->createQueryBuilder('s')
            ->select('s.name, s.id')
            ->orderBy('s.name', 'ASC')
            ->indexBy('s', 's.id')
            ->getQuery();

        return $query->getResult();
    }
}
