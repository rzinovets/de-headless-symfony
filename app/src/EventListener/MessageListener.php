<?php

namespace App\EventListener;

use App\Entity\Message;
use App\Entity\TelegramGroup;
use App\Repository\TelegramGroupRepository;
use App\Services\Notification\Notifications\TelegramNotification;
use App\Services\Notification\NotificationService;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Psr\Log\LoggerInterface;

readonly class MessageListener
{
    /**
     * @param NotificationService $notificationService
     * @param TelegramGroupRepository $telegramGroupRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        private NotificationService $notificationService,
        private TelegramGroupRepository $telegramGroupRepository,
        private LoggerInterface $logger
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

        $group = $this->telegramGroupRepository->findByCode(TelegramGroup::CODE_GROUP_NOTIFY_SUPPORT);

        $telegramNotification = new TelegramNotification();
        $telegramNotification
            ->setText($text)
            ->setGroup($group);

        $this->notificationService->send([$telegramNotification]);
    }
}