<?php

namespace App\Repository;

use App\Entity\Hotel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Hotel>
 */
class HotelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hotel::class);
    }

    public function findByFilters(?string $city, ?string $country): array
    {
        $qb = $this->createQueryBuilder('h');

        if ($city) {
            $qb->andWhere('LOWER(h.city) LIKE :city')
                ->setParameter('city', '%' . strtolower($city) . '%');
        }

        if ($country) {
            $qb->andWhere('LOWER(h.country) LIKE :country')
                ->setParameter('country', '%' . strtolower($country) . '%');
        }

        return $qb->getQuery()->getResult();
    }


//    /**
//     * @return Hotel[] Returns an array of Hotel objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Hotel
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
