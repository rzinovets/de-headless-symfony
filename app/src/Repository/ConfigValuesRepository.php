<?php

namespace App\Repository;

use App\Entity\ConfigValues;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConfigValues>
 *
 * @method ConfigValues|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConfigValues|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConfigValues[]    findAll()
 * @method ConfigValues[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfigValuesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConfigValues::class);
    }

    public function add(ConfigValues $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ConfigValues $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
