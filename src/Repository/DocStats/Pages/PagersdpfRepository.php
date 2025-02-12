<?php

namespace App\Repository\DocStats\Pages;

use App\Entity\DocStats\Pages\Pagersdpf;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pagersdpf>
 *
 * @method Pagersdpf|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pagersdpf|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pagersdpf[]    findAll()
 * @method Pagersdpf[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagersdpfRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pagersdpf::class);
    }

//    /**
//     * @return Pagersdpf[] Returns an array of Pagersdpf objects
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

//    public function findOneBySomeField($value): ?Pagersdpf
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
