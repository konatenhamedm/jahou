<?php

namespace App\Repository;

use App\Entity\LigneRetourFournisseur;
use App\Entity\RetourFournisseur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LigneRetourFournisseur>
 *
 * @method LigneRetourFournisseur|null find($id, $lockMode = null, $lockVersion = null)
 * @method LigneRetourFournisseur|null findOneBy(array $criteria, array $orderBy = null)
 * @method LigneRetourFournisseur[]    findAll()
 * @method LigneRetourFournisseur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LigneRetourFournisseurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LigneRetourFournisseur::class);
    }

    public function save(LigneRetourFournisseur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function add(LigneRetourFournisseur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function remove(LigneRetourFournisseur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return LigneRetourFournisseur[] Returns an array of LigneRetourFournisseur objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LigneRetourFournisseur
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
