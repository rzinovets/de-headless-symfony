<?php

namespace App\EventListener;

use App\Entity\Message;
use App\Entity\TelegramGroup;
use App\Message\TelegramNotificationMessage;
use App\Services\Notification\NotificationService;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class MessageListener
{
    /**
     * @param NotificationService $notificationService
     * @param LoggerInterface $logger
     * @param MessageBusInterface $messageBus
     */
    public function __construct(
        private NotificationService $notificationService,
        private LoggerInterface $logger,
        private MessageBusInterface $messageBus
    ) {}

    /**
     * @param PrePersistEventArgs $args
     * @return void
     */
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Message) {
            return;
        }

        try {
            $this->handleNewMessage($entity);
        } catch (\Exception $exception) {
            $this->logger->error('Error in MessageListener: ' . $exception->getMessage(), [
                'exception' => $exception,
            ]);
        }
    }

    /**
     * @throws \Exception
     */
    private function handleNewMessage(Message $message): void
    {
        $sender = $message->getSender();

        if (in_array('ROLE_ADMIN', $sender->getRoles(), true)) {
            return;
        }

        $text = $this->notificationService->generateTelegramText(
            'notification/telegram/new-message.html.twig',
            [
                'chatId' => $message->getChat()->getId(),
                'sender' => $sender->getUsername(),
                'content' => $message->getContent(),
                'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
            ]
        );

        $group = TelegramGroup::CODE_GROUP_NOTIFY_SUPPORT;

        $this->messageBus->dispatch(new TelegramNotificationMessage($text, $group));
    }
}