<?php

namespace App\Message;

readonly class TelegramNotificationMessage
{
    /**
     * @param string $text
     * @param string $groupCode
     */
    public function __construct(
        private string $text,
        private string $groupCode
    ) {}

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getGroupCode(): string
    {
        return $this->groupCode;
    }
}
