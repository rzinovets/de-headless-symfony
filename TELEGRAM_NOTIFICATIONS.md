# 📢 Telegram Notifications Setup

This document provides instructions on how to test and implement Telegram notifications in your project.

## ✨ How It Works

The application uses `NotificationService` to send messages to a configured Telegram group.

## 🛠 Example Code

Below is an example of how a Telegram notification is generated and sent:

```php
$text = $this->notificationService->generateTelegramText(
    'notification/telegram/chat_message.new.html.twig',
    []
);

$group = $this->telegramGroupRepository->findByCode('test');

$telegramNotification = new TelegramNotification();
$telegramNotification
    ->setText($text)
    ->setGroup($group);

$this->notificationService->send([$telegramNotification]);
```