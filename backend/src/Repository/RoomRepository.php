<?php

namespace App\Repository;

use App\Entity\Room;
use App\Entity\Hotel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Room>
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    public function findFirstAmount(int $amount): array
    {
        return $this->createQueryBuilder('r')
            ->setMaxResults($amount)
            ->getQuery()
            ->getResult();
    }

    /**
     * Finds random rooms with available = true
     * 
     * @param int $amount The number of rooms to return
     * @return Room[] Returns an array of Room objects
     */
    public function findRandomAvailable(int $amount): array
    {
        $availableRooms = $this->createQueryBuilder('r')
            ->where('r.available = :available')
            ->setParameter('available', true)
            ->getQuery()
            ->getResult();
        
        // Shuffle the results in PHP rather than using SQL RAND()
        shuffle($availableRooms);
        
        // Return only the requested amount
        return array_slice($availableRooms, 0, $amount);
    }

    //    /**
    //     * @return Room[] Returns an array of Room objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Room
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
