<?php

namespace App\GraphQL\Mutation;

use App\Entity\DataObject;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

class CreateAccountMutation implements MutationInterface, AliasedInterface
{
    /**
     * @var EntityManager
     */
    private EntityManager $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    /**
     * @param array $args
     * @return User
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createAccount(array $args): User {
        $newAccount = new User();
        $recivedParameters = new DataObject($args);

        if (!isset($recivedParameters->id)) {
            $newAccount->setEmail($recivedParameters->email);
            $newAccount->setPassword($recivedParameters->password);
            $this->em->persist($newAccount);
            $this->em->flush();
        }

        return $newAccount;
    }

    /**
     * @return string[]
     */
    public static function getAliases(): array {
        return [
            'createAccount' => 'create_account'
        ];
    }
}
