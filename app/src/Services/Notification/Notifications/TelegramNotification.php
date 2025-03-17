<?php

namespace App\Services\Notification\Notifications;

use App\Entity\TelegramGroup;

class TelegramNotification extends Notification
{
    private ?TelegramGroup $group = null;

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
}
