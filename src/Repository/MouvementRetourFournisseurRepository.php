<?php

namespace App\Repository;

use App\Entity\MouvementRetourFournisseur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MouvementRetourFournisseur>
 *
 * @method MouvementRetourFournisseur|null find($id, $lockMode = null, $lockVersion = null)
 * @method MouvementRetourFournisseur|null findOneBy(array $criteria, array $orderBy = null)
 * @method MouvementRetourFournisseur[]    findAll()
 * @method MouvementRetourFournisseur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MouvementRetourFournisseurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MouvementRetourFournisseur::class);
    }

    public function save(MouvementRetourFournisseur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MouvementRetourFournisseur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return MouvementRetourFournisseur[] Returns an array of MouvementRetourFournisseur objects
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

//    public function findOneBySomeField($value): ?MouvementRetourFournisseur
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
