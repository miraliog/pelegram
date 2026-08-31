<?php

namespace Miraliog\Pelegram\Concerns\Update;

trait DetectsMessageType
{
    public function isSuccessfulPayment(): bool
    {
        return isset($this->raw['message']['successful_payment']);
    }
    public function isContact(): bool
    {
        return isset($this->raw['message']['contact']);
    }
    public function isLocation(): bool
    {
        return isset($this->raw['message']['location']);
    }
    public function isPhoto(): bool
    {
        return isset($this->raw['message']['photo']);
    }
    public function isVideo(): bool
    {
        return isset($this->raw['message']['video']);
    }
    public function isDocument(): bool
    {
        return isset($this->raw['message']['document']);
    }
    public function isAudio(): bool
    {
        return isset($this->raw['message']['audio']);
    }
    public function isVoice(): bool
    {
        return isset($this->raw['message']['voice']);
    }
    public function isSticker(): bool
    {
        return isset($this->raw['message']['sticker']);
    }
    public function isAnimation(): bool
    {
        return isset($this->raw['message']['animation']);
    }
    public function isVideoNote(): bool
    {
        return isset($this->raw['message']['video_note']);
    }

    public function isCommand(): bool
    {
        $text = $this->text();
        return $text !== null && str_starts_with($text, '/');
    }
}
