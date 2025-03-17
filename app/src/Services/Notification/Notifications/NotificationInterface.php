<?php

namespace App\Services\Notification\Notifications;

interface NotificationInterface
{
    public function setText(string $text): self;
    public function getText(): string;
}
