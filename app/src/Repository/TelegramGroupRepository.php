<?php

namespace App\Repository;

use App\Entity\TelegramGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<TelegramGroup>
 */
class TelegramGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TelegramGroup::class);
    }

    /**
     * @throws Exception
     */
    public function findByCode(string $code): TelegramGroup
    {
        $group = $this->findOneBy(['code' => $code]);
        if (!$group instanceof TelegramGroup) {
            throw new Exception('Telegram chat not found. Code: ' . $code);
        }

        return $group;
    }
}
