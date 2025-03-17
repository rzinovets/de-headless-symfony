<?php

namespace App\Services\Notification;

use App\Services\Notification\Notifications\NotificationInterface;
use App\Services\Notification\Notifications\TelegramNotification;
use App\Services\Notification\Providers\ProviderInterface;
use App\Services\Notification\Providers\TelegramProvider;
use Exception;
use Psr\Log\LoggerInterface;
use Twig\Environment;
use Twig\Error\Error;

readonly class NotificationService
{
    /**
     * @param TelegramProvider $telegramProvider
     * @param Environment $twig
     * @param LoggerInterface $logger
     */
    public function __construct(
        private TelegramProvider $telegramProvider,
        private Environment $twig,
        private LoggerInterface $logger
    ) {}

    /**
     * @param array $notifications
     * @return void
     */
    public function send(array $notifications): void
    {
        foreach ($notifications as $notification) {
            $this->logger->info('[NotificationService]: Sending notification', [
                'notification' => $notification,
            ]);

            try {
                $provider = $this->getProvider($notification);
                $provider->send($notification);
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }

    /**
     * @param string $templatePath
     * @param array $params
     * @return string
     * @throws Exception
     */
    public function generateTelegramText(string $templatePath, array $params): string
    {
        try {
            return $this->twig->render($templatePath, $params);
        } catch (Error $e) {
            $this->logger->error('Error generating Telegram notification text', [
                'error' => $e->getMessage(),
                'template_path' => $templatePath,
                'params' => $params,
            ]);

            throw new Exception('Error generating Telegram notification text.', 0, $e);
        }
    }

    /**
     * @param NotificationInterface $notification
     * @return ProviderInterface
     * @throws Exception
     */
    private function getProvider(NotificationInterface $notification): ProviderInterface
    {
        return match (true) {
            $notification instanceof TelegramNotification => $this->telegramProvider,
            default => throw new Exception('Unknown notification provider'),
        };
    }
}
