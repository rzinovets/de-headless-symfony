<?php

namespace App\GraphQL\Mutation;

use App\Entity\Token;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

final readonly class LoginAccountMutation implements MutationInterface, AliasedInterface
{
    public function __construct(
        private EntityManagerInterface      $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function loginAccount(array $input): Token
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $input['email']]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $input['password'])) {
            throw new BadCredentialsException('Invalid email or password.');
        }

        $token = (new Token())
            ->setUser($user)
            ->setToken(bin2hex(random_bytes(32)))
            ->setExpiredTime(time() + 3600);

        $this->em->persist($token);
        $this->em->flush();

        return $token;
    }

    public static function getAliases(): array
    {
        return [
            'loginAccount' => 'login_account',
        ];
    }
}
