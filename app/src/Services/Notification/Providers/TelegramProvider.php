<?php

declare(strict_types=1);

namespace App\Services\Notification\Providers;

use App\Entity\TelegramGroup;
use App\Services\Notification\Notifications\TelegramNotification;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;

class TelegramProvider implements ProviderInterface
{
    /**
     * @param BotApi $api
     * @param LoggerInterface $logger
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        private readonly BotApi $api,
        private readonly LoggerInterface $logger,
        private readonly UrlGeneratorInterface $urlGenerator
    ) {}

    /**
     * @param TelegramNotification $notification
     * @return void
     */
    public function send(TelegramNotification $notification): void
    {
        sleep(1);

        try {
            $group = $notification->getGroup();
            if (!$group instanceof TelegramGroup) {
                throw new \Exception('Notification does not have a group.');
            }

            $chatId = $group->getChatId();
            if (!is_string($chatId)) {
                throw new \Exception('Group does not have a valid chat ID.');
            }

            $this->api->sendMessage(
                $chatId,
                $notification->getText(),
                'HTML',
                true
            );
        } catch (InvalidArgumentException|Exception|\Exception $e) {
            $this->logger->error('Failed to send Telegram notification.', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
