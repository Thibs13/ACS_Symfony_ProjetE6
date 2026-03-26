<?php

namespace App\Repository;

use App\Entity\Entreprise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Entreprise>
 */
class EntrepriseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entreprise::class);
    }

    //    /**
    //     * @return Entreprise[] Returns an array of Entreprise objects
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

    //    public function findOneBySomeField($value): ?Entreprise
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

        public function compteEntreprise(): int
        {
            return $this->createQueryBuilder('e')
                ->select('count(e.id)')
                ->getQuery()
                ->getSingleScalarResult();
        }

        public function findAllSorted(string $sort, string $order): array
        {
            $qb = $this->createQueryBuilder('e');

            // Cas spécifique : Tri par le NOM de la ville
            if ($sort === 'ville' || $sort === 'VIL_Nom') {
                return $qb->leftJoin('e.VIL_ID', 'v') // 'e.VIL_ID' est le nom de la propriété dans Entreprise.php
                        ->orderBy('v.VIL_Nom', $order) // 'VIL_Nom' est le nom de la propriété dans Ville.php
                        ->getQuery()
                        ->getResult();
            }

            // Cas général : Tri sur les champs directs (ex: ENT_Nom)
            // On ajoute un 'e.' devant pour être sûr que Doctrine sache de quoi on parle
            return $qb->orderBy('e.' . $sort, $order)
                    ->getQuery()
                    ->getResult();
        }

}
