<?php

namespace App\Services\Notification\Notifications;

use App\Entity\TelegramGroup;
use Symfony\Component\HttpFoundation\File\File;

class TelegramNotification extends Notification
{
    private ?TelegramGroup $group = null;
    private ?File $attachment = null;

    public function setGroup(TelegramGroup $group): self
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @return TelegramGroup|null
     */
    public function getGroup(): ?TelegramGroup
    {
        return $this->group;
    }

    public function setAttachment(?File $attachment): self
    {
        $this->attachment = $attachment;

        return $this;
    }

    public function getAttachment(): ?File
    {
        return $this->attachment;
    }
}