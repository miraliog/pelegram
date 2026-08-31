<?php

namespace Miraliog\Pelegram\Concerns\Actions;

use Miraliog\Pelegram\Exceptions\pelegramException;

trait Replyable
{
    public function reply(
        string $text,
        ?array $replyMarkup = null,
        string $parseMode = 'HTML',
        bool $disableWebPagePreview = false,
    ): array {
        $this->assertBotSet();

        return $this->bot->sendMessage(
            chatId: $this->chatId(),
            text: $text,
            replyMarkup: $replyMarkup,
            parseMode: $parseMode,
            disableWebPagePreview: $disableWebPagePreview,
            replyToMessageId: $this->messageId(),
        );
    }
}
