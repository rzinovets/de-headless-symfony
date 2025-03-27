<?php

namespace App\EventListener;

use App\Entity\ContactForm;
use App\Entity\TelegramGroup;
use App\Message\ContactFormNotificationMessage;
use App\Services\Notification\NotificationService;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class ContactFormListener
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

        if (!$entity instanceof ContactForm) {
            return;
        }

        try {
            $this->handleContactFormCreation($entity);
        } catch (\Exception $exception) {
            $this->logger->error('Error in ContactFormListener: ' . $exception->getMessage(), [
                'exception' => $exception,
            ]);
        }
    }

    /**
     * @throws \Exception
     */
    private function handleContactFormCreation(ContactForm $contactForm): void
    {
        $text = $this->notificationService->generateTelegramText(
            'notification/telegram/contact-form.html.twig',
            [
                'name' => $contactForm->getName(),
                'email' => $contactForm->getEmail(),
                'subject' => $contactForm->getSubject(),
                'message' => $contactForm->getMessage(),
            ]
        );

        $groupCode = TelegramGroup::CODE_GROUP_NOTIFY_CONTACT_FORM;
        $this->messageBus->dispatch(new ContactFormNotificationMessage($text, $groupCode));
    }
}