<?php

namespace App\Repository;

use App\Entity\Etudiant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Etudiant>
 */
class EtudiantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etudiant::class);
    }

    //    /**
    //     * @return Etudiant[] Returns an array of Etudiant objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Etudiant
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

        public function compteEtudiant(): int
        {
            return $this->createQueryBuilder('e')
                ->select('count(e.id)')
                ->getQuery()
                ->getSingleScalarResult();
        }



        public function findAllSorted(string $sort, string $order): array
        {
            $qb = $this->createQueryBuilder('e');

            // Cas général : Tri sur les champs directs (ex: ENT_Nom)
            // On ajoute un 'e.' devant pour être sûr que Doctrine sache de quoi on parle
            return $qb->orderBy('e.' . $sort, $order)
                    ->getQuery()
                    ->getResult();
        }
}
