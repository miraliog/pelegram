<?php

namespace Miraliog\Pelegram\Concerns\Actions;

use Miraliog\Pelegram\Exceptions\pelegramException;

trait Answerable
{
    public function answer(
        string $text = '',
        bool $showAlert = false,
        ?string $url = null,
        int $cacheTime = 0,
        ?array $replyMarkup = null,
        string $parseMode = 'HTML',
    ): mixed {
        $this->assertBotSet();

        if ($this->isCallbackQuery()) {
            return $this->bot->answerCallbackQuery(
                callbackQueryId: $this->callbackQueryId(),
                text: $text !== '' ? $text : null,
                showAlert: $showAlert,
                url: $url,
                cacheTime: $cacheTime,
            );
        }

        return $this->bot->sendMessage(
            chatId: $this->chatId(),
            text: $text,
            replyMarkup: $replyMarkup,
            parseMode: $parseMode,
        );
    }

    private function assertBotSet(): void
    {
        if ($this->bot === null) {
            throw new pelegramException(
                'Bot is not set on Update. Call $update->setBot($bot) before using answer method!'
            );
        }
    }
}
