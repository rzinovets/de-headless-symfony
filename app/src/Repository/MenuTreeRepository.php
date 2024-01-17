<?php

namespace App\Repository;

use App\Entity\MenuTree;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MenuTree>
 *
 * @method MenuTree|null find($id, $lockMode = null, $lockVersion = null)
 * @method MenuTree|null findOneBy(array $criteria, array $orderBy = null)
 * @method MenuTree[]    findAll()
 * @method MenuTree[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuTreeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuTree::class);
    }

    public function add(MenuTree $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MenuTree $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
