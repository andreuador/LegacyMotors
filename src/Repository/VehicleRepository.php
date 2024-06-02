<?php

namespace App\Repository;

use App\Entity\Vehicle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vehicle>
 *
 * @method Vehicle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vehicle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vehicle[]    findAll()
 * @method Vehicle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VehicleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

    public function findAllQuery(): Query {
        return $this->createQueryBuilder('v')
            ->andWhere('v.available = true')
            ->andWhere('v.vehicleOrder IS NULL')
            ->andWhere('v.isDeleted IS NULL OR v.isDeleted = 0')
            ->orderBy('v.id', 'ASC')
            ->getQuery();
    }

    public function findByTextQuery(string $value): Query {
        return $this->createQueryBuilder('v')
            ->join('v.brand', 'b')
            ->join('b.models', 'm') // Ajuste para la relación indirecta
            ->andWhere('v.available = true')
            ->andWhere('v.vehicleOrder IS NULL')
            ->andWhere('v.isDeleted IS NULL OR v.isDeleted = 0')
            ->andWhere('b.name LIKE :value OR m.name LIKE :value OR v.color LIKE :value OR v.transmission LIKE :value OR v.fuel LIKE :value OR v.year LIKE :value OR v.price LIKE :value')
            ->setParameter('value', '%' . $value . '%')
            ->orderBy('v.id', 'ASC')
            ->getQuery();
    }

    //    /**
    //     * @return Vehicle[] Returns an array of Vehicle objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Vehicle
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
