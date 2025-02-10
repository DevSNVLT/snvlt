<?php

namespace App\Repository\DocStats\Pages;

use App\Entity\DocStats\Pages\Pagebrh;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pagebrh>
 *
 * @method Pagebrh|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pagebrh|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pagebrh[]    findAll()
 * @method Pagebrh[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagebrhRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pagebrh::class);
    }

//    /**
//     * @return Pagebrh[] Returns an array of Pagebrh objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Pagebrh
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
