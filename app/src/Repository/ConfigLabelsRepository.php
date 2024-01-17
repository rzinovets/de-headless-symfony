<?php

namespace App\Repository;

use App\Entity\ConfigLabels;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConfigLabels>
 *
 * @method ConfigLabels|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConfigLabels|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConfigLabels[]    findAll()
 * @method ConfigLabels[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfigLabelsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConfigLabels::class);
    }

    public function add(ConfigLabels $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ConfigLabels $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
