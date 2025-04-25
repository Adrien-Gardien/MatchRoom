<?php

namespace App\Repository;

use App\Entity\Negotiation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Negotiation>
 */
class NegotiationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Negotiation::class);
    }

    /**
     * Trouve toutes les négociations pour les chambres appartenant aux hôtels du propriétaire
     */
    public function findNegotiationsForHotelOwner(User $owner): array
    {
        return $this->createQueryBuilder('n')
            ->join('n.roomId', 'r')
            ->join('r.hotelId', 'h')
            ->where('h.ownerId = :owner')
            ->setParameter('owner', $owner)
            ->orderBy('n.updatedAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Negotiation[] Returns an array of Negotiation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Negotiation
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
