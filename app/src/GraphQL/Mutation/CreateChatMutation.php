<?php

namespace App\GraphQL\Mutation;

use App\Entity\Chat;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateChatMutation implements MutationInterface, AliasedInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}

    public function createChat(array $args): Chat
    {
        $user = $this->userRepository->find($args['userId']);
        $admin = $this->userRepository->find($args['adminId']);

        if (!$user || !$admin) {
            throw new \InvalidArgumentException('User or Admin not found.');
        }

        if (!in_array('ROLE_ADMIN', $admin->getRoles())) {
            throw new \InvalidArgumentException('The specified user is not an admin.');
        }

        $chat = new Chat();
        $chat->setUser($user);
        $chat->setAdmin($admin);

        $this->entityManager->persist($chat);
        $this->entityManager->flush();

        return $chat;
    }

    public static function getAliases(): array
    {
        return [
            'createChat' => 'create_chat',
        ];
    }
}