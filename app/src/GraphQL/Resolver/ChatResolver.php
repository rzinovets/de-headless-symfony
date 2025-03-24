<?php

namespace App\GraphQL\Resolver;

use App\Entity\Chat;
use App\Entity\Token;
use App\Entity\User;
use App\Repository\ChatRepository;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

readonly class ChatResolver implements QueryInterface, AliasedInterface
{
    public function __construct(
        private ChatRepository  $chatRepository,
        private TokenRepository $tokenRepository,
        private UserRepository  $userRepository
    ) {}

    /**
     * @throws AuthenticationException
     * @throws Exception
     */
    public function resolve(Argument $args): ?Chat
    {
        $token = $this->getTokenFromArguments($args);
        $user = $this->getAuthenticatedUser($token);

        return $this->findUserChat($user);
    }

    private function getTokenFromArguments(Argument $args): string
    {
        if (empty($args['token'])) {
            throw new AuthenticationException('Authentication token is required');
        }

        return $args['token'];
    }

    /**
     * @throws AuthenticationException
     */
    private function getAuthenticatedUser(string $token): User
    {
        $tokenEntity = $this->tokenRepository->findOneBy(['token' => $token]);

        if (!$tokenEntity instanceof Token) {
            throw new AuthenticationException('Invalid authentication token');
        }

        $user = $tokenEntity->getUser() ?? $this->userRepository->find($tokenEntity->getUser()->getId());

        if (!$user instanceof User) {
            throw new AuthenticationException('User not found');
        }

        return $user;
    }

    private function findUserChat(User $user): ?Chat
    {
        return $this->chatRepository->findOneBy([
            'user' => $user
        ]);
    }

    public static function getAliases(): array
    {
        return [
            'resolve' => 'Chat'
        ];
    }
}