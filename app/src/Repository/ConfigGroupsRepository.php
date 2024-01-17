<?php

namespace App\Repository;

use App\Entity\ConfigGroups;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConfigGroups>
 *
 * @method ConfigGroups|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConfigGroups|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConfigGroups[]    findAll()
 * @method ConfigGroups[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfigGroupsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConfigGroups::class);
    }

    public function add(ConfigGroups $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ConfigGroups $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
