<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Image>
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    /**
     * @return Image[] Returns an array of Image objects associated with a hotel
     */
    public function findByHotelId(int $hotelId): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.hotel = :hotelId')
            ->setParameter('hotelId', $hotelId)
            ->orderBy('i.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param array $hotelIds
     * @return array<int, Image[]> Returns a map of hotel IDs to arrays of Image objects
     */
    public function findByMultipleHotelIds(array $hotelIds): array
    {
        if (empty($hotelIds)) {
            return [];
        }

        $images = $this->createQueryBuilder('i')
            ->andWhere('i.hotel IN (:hotelIds)')
            ->setParameter('hotelIds', $hotelIds)
            ->orderBy('i.id', 'ASC')
            ->getQuery()
            ->getResult();

        // Group images by hotel ID
        $imagesByHotelId = [];
        foreach ($images as $image) {
            $hotelId = $image->getHotel()->getId();
            if (!isset($imagesByHotelId[$hotelId])) {
                $imagesByHotelId[$hotelId] = [];
            }
            $imagesByHotelId[$hotelId][] = $image;
        }

        return $imagesByHotelId;
    }

    /**
     * @param array $roomIds
     * @return array<int, Image[]> Returns a map of room IDs to arrays of Image objects
     */
    public function findByMultipleRoomIds(array $roomIds): array
    {
        if (empty($roomIds)) {
            return [];
        }

        $images = $this->createQueryBuilder('i')
            ->andWhere('i.room IN (:roomIds)')
            ->setParameter('roomIds', $roomIds)
            ->orderBy('i.id', 'ASC')
            ->getQuery()
            ->getResult();

        // Group images by room ID
        $imagesByRoomId = [];
        foreach ($images as $image) {
            $roomId = $image->getRoom()->getId();
            if (!isset($imagesByRoomId[$roomId])) {
                $imagesByRoomId[$roomId] = [];
            }
            $imagesByRoomId[$roomId][] = $image;
        }

        return $imagesByRoomId;
    }

    //    /**
    //     * @return Image[] Returns an array of Image objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Image
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
