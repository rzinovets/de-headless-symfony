<?php

namespace App\Services\Notification\Providers;

use App\Entity\User;
use App\Services\Notification\Notifications\EmailNotification;
use Exception;
use Psr\Log\LoggerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class EmailProvider implements ProviderInterface
{
    /**
     * @param Environment $environment
     * @param LoggerInterface $logger
     * @param string $template
     * @param string $emailFrom
     * @param string $from
     * @param bool $isActive
     */
    public function __construct(
        private Environment $environment,
        private LoggerInterface $logger,
        private string $template,
        private string $emailFrom,
        private bool $isActive = false
    ) {
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setTemplate(string $template): self
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @param EmailNotification $notification
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderTemplate(EmailNotification $notification): string
    {
        try {
            return $this->environment->render($this->getTemplate(), ['notification' => $notification]);
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            $this->logger->error('Template rendering error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * @param EmailNotification $notification
     * @return bool
     */
    public function send(EmailNotification $notification): bool
    {
        if (!$this->isActive) {
            return true;
        }

        try {
            $account = $notification->getAccount();
            if (!$account instanceof User) {
                throw new Exception('Account not specified');
            }

            if (!$account->getEmail()) {
                return false;
            }

            // TODO: Implement the logic for sending emails
//            $this->emailWrapper->send(
//                $this->emailFrom,
//                $account->getEmail(),
//                $notification->getTitle(),
//                $this->renderTemplate($notification),
//                $notification->getAttachments()
//            );

            return true;
        } catch (Exception $e) {
            $this->logger->error('Error sending Email notification. Message: ' . $e->getMessage());
            return false;
        }
    }
}
