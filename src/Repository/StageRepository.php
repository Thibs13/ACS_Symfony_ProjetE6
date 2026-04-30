<?php

namespace App\Repository;

use App\Entity\Stage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stage>
 */
class StageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stage::class);
    }

    //    /**
    //     * @return Stage[] Returns an array of Stage objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Stage
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

        public function compteStage(): int
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
    
        public function findThreeLatest(): array
        {
            return $this->createQueryBuilder('s')
                ->orderBy('s.id', 'DESC') // Du plus récent au plus ancien
                ->setMaxResults(3)        // On limite à 3 résultats
                ->getQuery()
                ->getResult();
        }

        public function findStagesByEnseignant(int $userId): array
        {
            return $this->createQueryBuilder('s')
                // On cherche l'ID de l'enseignant connecté dans les deux colonnes possibles
                ->where('s.EnseignantVisite = :id')
                ->orWhere('s.EnseignantSuivi = :id')
                ->setParameter('id', $userId) // Ici, $userId est l'ID de celui qui est devant l'écran
                ->getQuery()
                ->getResult();
        }

        public function findStagesByEnseignantSuivi(int $userId): array
        {
            return $this->createQueryBuilder('s')
                ->where('s.EnseignantSuivi = :id')
                ->setParameter('id', $userId)
                ->getQuery()
                ->getResult();
        }

        public function findStagesByEnseignantVisite(int $userId): array
        {
            return $this->createQueryBuilder('s')
                ->where('s.EnseignantVisite = :id')
                ->setParameter('id', $userId)
                ->getQuery()
                ->getResult();
        }
}
