<?php

namespace App\GraphQL\Mutation;

use App\Entity\Chat;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\ChatRepository;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use InvalidArgumentException;

readonly class SendMessageMutation implements MutationInterface, AliasedInterface
{
    public function __construct(
        private ChatRepository         $chatRepository,
        private TokenRepository        $tokenRepository,
        private UserRepository         $userRepository,
        private EntityManagerInterface $entityManager,
        private Security               $security
    ) {}

    public function sendMessage(array $args): Message
    {
        $author = $this->getAuthorizedUser($args['token']);
        $this->validateCurrentUser();
        $content = $args['content'];

        $chat = $this->getOrCreateChat($author);
        $message = $this->createMessage($chat, $author, $content);

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $message;
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

    private function getOrCreateChat(User $user): Chat
    {
        $chat = $this->chatRepository->findOneBy(['user' => $user]);

        if (!$chat) {
            $chat = new Chat();
            $chat->setUser($user);
            $chat->setAdmin($this->getAdminUser());

            $this->entityManager->persist($chat);
            $this->entityManager->flush();
        }

        return $chat;
    }

    private function getAdminUser(): User
    {
        $admin = $this->userRepository->findOneByRole('ROLE_ADMIN');

        if (!$admin) {
            throw new InvalidArgumentException('No admin available.');
        }

        return $admin;
    }

    private function createMessage(Chat $chat, User $sender, string $content): Message
    {
        $message = new Message();
        $message->setChat($chat);
        $message->setSender($sender);
        $message->setContent($content);

        return $message;
    }

    public static function getAliases(): array
    {
        return [
            'sendMessage' => 'send_message',
        ];
    }
}