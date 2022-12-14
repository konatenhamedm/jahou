<?php

namespace App\Repository;

use App\Entity\MouvementSortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MouvementSortie>
 *
 * @method MouvementSortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method MouvementSortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method MouvementSortie[]    findAll()
 * @method MouvementSortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MouvementSortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MouvementSortie::class);
    }

    public function save(MouvementSortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MouvementSortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getVente($value){
        return $this->createQueryBuilder('m')
            ->select('m.id')
            ->andWhere('m.vente = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getOneOrNullResult();
    }

//    /**
//     * @return MouvementSortie[] Returns an array of MouvementSortie objects
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

//    public function findOneBySomeField($value): ?MouvementSortie
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
