<?php

namespace App\GraphQL\Mutation;

use App\Entity\DataObject;
use App\Entity\Token;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use App\Repository\AccountRepository;

class AuthorizationMutation implements MutationInterface, AliasedInterface
{
    /**
     * @var EntityManager
     */
    private EntityManager $em;
    /**
     * @var UserRepository
     */
    private UserRepository $accountRepository;

    /**
     * @param EntityManager $em
     * @param UserRepository $accountRepository
     */
    public function __construct(EntityManager $em, UserRepository $accountRepository)
    {
        $this->em = $em;
        $this->accountRepository = $accountRepository;
    }

    /**
     * @param array $args
     * @return Token
     * @throws ORMException
     * @throws Exception
     */
    public function authorization(array $args): Token
    {
        $recivedParameters = new DataObject($args);
        $token = new Token();

        $account = $this->accountRepository
            ->findBy(
                [
                    'email' => $recivedParameters->email,
                    'password' => $recivedParameters->password
                ]
            );

        if ($account) {
            $token->setToken(bin2hex(random_bytes(16)));
            $token->setExpiredTime(3600);
            $this->em->persist(($token));
            $this->em->flush();
        }
        return $token;
    }
    /**
     * @return string[]
     */
    public static function getAliases(): array
    {
        return [
            'authorization' => 'login_account'
        ];
    }
}
