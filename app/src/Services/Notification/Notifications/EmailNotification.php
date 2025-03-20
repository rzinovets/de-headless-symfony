<?php

namespace App\Services\Notification\Notifications;

use App\Entity\User;

class EmailNotification extends Notification
{
    /**
     * @var User|null
     */
    private ?User $account;

    /**
     * @var string
     */
    private string $title;

    /**
     * @param $account
     * @return $this
     */
    public function setAccount($account): self
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getAccount(): ?User
    {
        return $this->account;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}
