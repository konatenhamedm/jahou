<?php

namespace App\Repository;

use App\Entity\MouvementRetourCLient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MouvementRetourCLient>
 *
 * @method MouvementRetourCLient|null find($id, $lockMode = null, $lockVersion = null)
 * @method MouvementRetourCLient|null findOneBy(array $criteria, array $orderBy = null)
 * @method MouvementRetourCLient[]    findAll()
 * @method MouvementRetourCLient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MouvementRetourCLientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MouvementRetourCLient::class);
    }

    public function save(MouvementRetourCLient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MouvementRetourCLient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return MouvementRetourCLient[] Returns an array of MouvementRetourCLient objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MouvementRetourCLient
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
