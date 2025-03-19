<?php

namespace App\EventListener;

use App\Entity\ContactForm;
use App\Entity\TelegramGroup;
use App\Repository\TelegramGroupRepository;
use App\Services\Notification\Notifications\TelegramNotification;
use App\Services\Notification\NotificationService;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Psr\Log\LoggerInterface;

readonly class ContactFormListener
{
    /**
     * @param NotificationService $notificationService
     * @param TelegramGroupRepository $telegramGroupRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        private NotificationService     $notificationService,
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

        $group = $this->telegramGroupRepository->findByCode(TelegramGroup::CODE_GROUP_NOTIFY_CONTACT_FORM);

        $telegramNotification = new TelegramNotification();
        $telegramNotification
            ->setText($text)
            ->setGroup($group);

        $this->notificationService->send([$telegramNotification]);
    }
}
