<?php

namespace App\Repository;

use App\Entity\LigneMouvement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LigneMouvement>
 *
 * @method LigneMouvement|null find($id, $lockMode = null, $lockVersion = null)
 * @method LigneMouvement|null findOneBy(array $criteria, array $orderBy = null)
 * @method LigneMouvement[]    findAll()
 * @method LigneMouvement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LigneMouvementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LigneMouvement::class);
    }

    public function save(LigneMouvement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LigneMouvement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getLigne($value){
        return $this->createQueryBuilder('m')
            ->select('m.id','a.designation','a.prixVente as montant','m.quantite','m.remise','a.prixVente*m.quantite - (a.prixVente*m.remise*m.quantite/100) as total')
            ->innerJoin('m.article','a')
            ->andWhere('m.mouvement = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }

    public function getMontant($value){
        return $this->createQueryBuilder('m')
            ->select('SUM(a.prixVente*m.quantite - (a.prixVente*m.remise*m.quantite/100))')
            ->innerJoin('m.article','a')
            ->andWhere('m.mouvement = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return LigneMouvement[] Returns an array of LigneMouvement objects
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

//    public function findOneBySomeField($value): ?LigneMouvement
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
