<?php

namespace App\MessageHandler;

use App\Message\ContactFormNotificationMessage;
use App\Repository\TelegramGroupRepository;
use App\Services\Notification\Notifications\TelegramNotification;
use App\Services\Notification\NotificationService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class ContactFormNotificationHandler
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
     * @param ContactFormNotificationMessage $message
     * @return void
     * @throws \Exception
     */
    public function __invoke(ContactFormNotificationMessage $message): void
    {
        $group = $this->telegramGroupRepository->findByCode($message->getGroupCode());

        $telegramNotification = new TelegramNotification();
        $telegramNotification
            ->setText($message->getText())
            ->setGroup($group);

        $this->notificationService->send([$telegramNotification]);
    }
}