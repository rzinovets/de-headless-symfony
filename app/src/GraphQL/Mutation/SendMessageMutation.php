<?php

namespace App\GraphQL\Mutation;

use App\Entity\Message;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use App\Repository\ChatRepository;

class SendMessageMutation implements MutationInterface, AliasedInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private ChatRepository $chatRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function sendMessage(array $args): Message
    {
        $chat = $this->chatRepository->find($args['chatId']);
        $sender = $this->userRepository->find($args['senderId']);

        $message = new Message();
        $message->setChat($chat);
        $message->setSender($sender);
        $message->setContent($args['content']);

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $message;
    }

    public static function getAliases(): array
    {
        return [
            'sendMessage' => 'send_message',
        ];
    }
}