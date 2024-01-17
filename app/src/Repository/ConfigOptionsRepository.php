<?php

namespace App\Repository;

use App\Entity\ConfigOptions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConfigOptions>
 *
 * @method ConfigOptions|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConfigOptions|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConfigOptions[]    findAll()
 * @method ConfigOptions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfigOptionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConfigOptions::class);
    }

    public function add(ConfigOptions $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ConfigOptions $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
