<?php

namespace App\MessageHandler;

use App\Message\TelegramNotificationMessage;
use App\Repository\TelegramGroupRepository;
use App\Services\Notification\Notifications\TelegramNotification;
use App\Services\Notification\NotificationService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class TelegramNotificationHandler
{
    /**
     * @param NotificationService $notificationService
     * @param TelegramGroupRepository $telegramGroupRepository
     */
    public function __construct(
        private NotificationService $notificationService,
        private TelegramGroupRepository $telegramGroupRepository
    ) {}

    /**
     * @param TelegramNotificationMessage $message
     * @return void
     * @throws \Exception
     */
    public function __invoke(TelegramNotificationMessage $message): void
    {
        $group = $this->telegramGroupRepository->findByCode($message->getGroupCode());

        $telegramNotification = new TelegramNotification();
        $telegramNotification
            ->setText($message->getText())
            ->setGroup($group);

        $this->notificationService->send([$telegramNotification]);
    }
}
