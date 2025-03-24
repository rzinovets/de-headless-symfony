<?php

namespace App\GraphQL\Mutation;

use App\Entity\Chat;
use App\Entity\User;
use App\Repository\ChatRepository;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use InvalidArgumentException;

readonly class CreateChatMutation implements MutationInterface, AliasedInterface
{
    public function __construct(
        private UserRepository         $userRepository,
        private ChatRepository         $chatRepository,
        private TokenRepository        $tokenRepository,
        private EntityManagerInterface $entityManager,
        private Security               $security
    ) {}

    public function createChat(array $args): Chat
    {
        $author = $this->getAuthorizedUser($args['token']);
        $this->validateCurrentUser();

        if ($existingChat = $this->getExistingChat($author)) {
            return $existingChat;
        }

        $admin = $this->getAdminUser();
        $chat = $this->createNewChat($author, $admin);

        $this->entityManager->persist($chat);
        $this->entityManager->flush();

        return $chat;
    }

    private function getAuthorizedUser(string $token): User
    {
        $authorId = $this->tokenRepository->findOneBy(['token' => $token])?->getId();
        $author = $this->userRepository->find($authorId);

        if (!$author instanceof User) {
            throw new InvalidArgumentException('User must be logged in.');
        }

        return $author;
    }

    private function validateCurrentUser(): void
    {
        if (!$this->security->getUser()) {
            throw new InvalidArgumentException('User must be logged in.');
        }
    }

    private function getExistingChat(User $user): ?Chat
    {
        return $this->chatRepository->findOneBy(['user' => $user]);
    }

    private function getAdminUser(): User
    {
        $admin = $this->userRepository->findOneByRole('ROLE_ADMIN');

        if (!$admin) {
            throw new InvalidArgumentException('No admin available.');
        }

        return $admin;
    }

    private function createNewChat(User $user, User $admin): Chat
    {
        $chat = new Chat();
        $chat->setUser($user);
        $chat->setAdmin($admin);

        return $chat;
    }

    public static function getAliases(): array
    {
        return [
            'createChat' => 'create_chat',
        ];
    }
}