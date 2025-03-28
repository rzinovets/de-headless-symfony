<?php

namespace App\Repository;

use App\Entity\Footer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Footer>
 *
 * @method Footer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Footer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Footer[]    findAll()
 * @method Footer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FooterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Footer::class);
    }

    public function save(Footer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Footer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
