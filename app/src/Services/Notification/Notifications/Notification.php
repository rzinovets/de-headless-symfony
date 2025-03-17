<?php

namespace App\Services\Notification\Notifications;

abstract class Notification implements NotificationInterface
{
    protected string $text;

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
