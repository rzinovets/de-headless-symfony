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

## 🔧 Configuration Steps

1. Ensure that `NotificationService` and `TelegramGroupRepository` are properly set up.
2. Modify the `findByCode('test')` method to target the correct Telegram group.
3. Implement the logic in a relevant controller, such as `SecurityController`.

## 🔍 Where It’s Used

This logic is currently tested inside `SecurityController` under the `/login` route. Adjust it as needed for other use cases.

📌 _For further modifications, refer to the project's notification service implementation._