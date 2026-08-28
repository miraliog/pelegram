<?php

namespace miraliog\pelegram;

use miraliog\pelegram\Contracts\MiddlewareInterface;
use miraliog\pelegram\Exceptions\pelegramException;

class Router
{
    // handler storage
    private array $commandHandlers   = [];
    private array $textHandlers      = [];
    private array $callbackExact     = [];
    private array $callbackPrefix    = [];
    private array $regexHandlers     = [];

    // update-type handlers
    private ?callable $onMessageHandler              = null;
    private ?callable $onEditedMessageHandler        = null;
    private ?callable $onChannelPostHandler          = null;
    private ?callable $onCallbackQueryHandler        = null;
    private ?callable $onInlineQueryHandler          = null;
    private ?callable $onPreCheckoutQueryHandler     = null;
    private ?callable $onSuccessfulPaymentHandler    = null;
    private ?callable $onShippingQueryHandler        = null;
    private ?callable $onPollHandler                 = null;
    private ?callable $onPollAnswerHandler           = null;
    private ?callable $onMyChatMemberHandler         = null;
    private ?callable $onChatMemberHandler           = null;
    private ?callable $onChatJoinRequestHandler      = null;
    private ?callable $onChatBoostHandler            = null;
    private ?callable $onMessageReactionHandler      = null;
    private ?callable $onBusinessMessageHandler      = null;
    private ?callable $onContactHandler              = null;
    private ?callable $onLocationHandler             = null;
    private ?callable $onPhotoHandler                = null;
    private ?callable $onVideoHandler                = null;
    private ?callable $onDocumentHandler             = null;
    private ?callable $onVoiceHandler                = null;
    private ?callable $onStickerHandler              = null;

    /** @var MiddlewareInterface[] */
    private array $middlewares = [];

    private array $bypassTexts = [];

    // ==================== Middleware ====================

    public function use(MiddlewareInterface $middleware): static
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function bypass(string ...$texts): static
    {
        foreach ($texts as $text) {
            $this->bypassTexts[] = $text;
        }
        return $this;
    }

    // ==================== Command handlers ====================

    public function onCommand(string $command, callable $handler): static
    {
        $this->commandHandlers[ltrim($command, '/')] = $handler;
        return $this;
    }

    public function onCommands(array $commands, callable $handler): static
    {
        foreach ($commands as $command) {
            $this->onCommand($command, $handler);
        }
        return $this;
    }

    // ==================== Text handlers ====================

    public function onText(string $text, callable $handler): static
    {
        $this->textHandlers[$text] = $handler;
        return $this;
    }

    public function onTexts(array $texts, callable $handler): static
    {
        foreach ($texts as $text) {
            $this->textHandlers[$text] = $handler;
        }
        return $this;
    }

    public function onPattern(string $pattern, callable $handler): static
    {
        $this->regexHandlers[] = ['pattern' => $pattern, 'handler' => $handler];
        return $this;
    }

    // ==================== Callback handlers ====================

    public function onCallback(string $data, callable $handler): static
    {
        $this->callbackExact[$data] = $handler;
        return $this;
    }

    public function onCallbackPrefix(string $prefix, callable $handler): static
    {
        $this->callbackPrefix[$prefix] = $handler;
        return $this;
    }

    // ==================== Update-type handlers ====================

    public function onMessage(callable $handler): static
    {
        $this->onMessageHandler = $handler;
        return $this;
    }

    public function onEditedMessage(callable $handler): static
    {
        $this->onEditedMessageHandler = $handler;
        return $this;
    }

    public function onChannelPost(callable $handler): static
    {
        $this->onChannelPostHandler = $handler;
        return $this;
    }

    public function onCallbackQuery(callable $handler): static
    {
        $this->onCallbackQueryHandler = $handler;
        return $this;
    }

    public function onInlineQuery(callable $handler): static
    {
        $this->onInlineQueryHandler = $handler;
        return $this;
    }

    public function onPreCheckoutQuery(callable $handler): static
    {
        $this->onPreCheckoutQueryHandler = $handler;
        return $this;
    }

    public function onSuccessfulPayment(callable $handler): static
    {
        $this->onSuccessfulPaymentHandler = $handler;
        return $this;
    }

    public function onShippingQuery(callable $handler): static
    {
        $this->onShippingQueryHandler = $handler;
        return $this;
    }

    public function onPoll(callable $handler): static
    {
        $this->onPollHandler = $handler;
        return $this;
    }

    public function onPollAnswer(callable $handler): static
    {
        $this->onPollAnswerHandler = $handler;
        return $this;
    }

    public function onMyChatMember(callable $handler): static
    {
        $this->onMyChatMemberHandler = $handler;
        return $this;
    }

    public function onChatMember(callable $handler): static
    {
        $this->onChatMemberHandler = $handler;
        return $this;
    }

    public function onChatJoinRequest(callable $handler): static
    {
        $this->onChatJoinRequestHandler = $handler;
        return $this;
    }

    public function onChatBoost(callable $handler): static
    {
        $this->onChatBoostHandler = $handler;
        return $this;
    }

    public function onMessageReaction(callable $handler): static
    {
        $this->onMessageReactionHandler = $handler;
        return $this;
    }

    public function onBusinessMessage(callable $handler): static
    {
        $this->onBusinessMessageHandler = $handler;
        return $this;
    }

    // ==================== Media message type handlers ====================

    public function onContact(callable $handler): static
    {
        $this->onContactHandler = $handler;
        return $this;
    }

    public function onLocation(callable $handler): static
    {
        $this->onLocationHandler = $handler;
        return $this;
    }

    public function onPhoto(callable $handler): static
    {
        $this->onPhotoHandler = $handler;
        return $this;
    }

    public function onVideo(callable $handler): static
    {
        $this->onVideoHandler = $handler;
        return $this;
    }

    public function onDocument(callable $handler): static
    {
        $this->onDocumentHandler = $handler;
        return $this;
    }

    public function onVoice(callable $handler): static
    {
        $this->onVoiceHandler = $handler;
        return $this;
    }

    public function onSticker(callable $handler): static
    {
        $this->onStickerHandler = $handler;
        return $this;
    }

    // ==================== Dispatch ====================

    public function dispatch(Bot $bot, Update $update): void
    {
        try {
            $this->handle($bot, $update);
        } catch (pelegramException $e) {
            error_log("[pelegramException] {$e->getMessage()} (code: {$e->getErrorCode()})");
        } catch (\Throwable $e) {
            error_log("[Router] Unhandled exception: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}");

            $userId = $update->userId();
            if ($userId !== null) {
                try {
                    $bot->sendMessage($userId, '⚠️ خطای غیرمنتظره‌ای رخ داد. لطفاً دوباره امتحان کن.');
                } catch (\Throwable) {
                }
            }
        }
    }

    private function handle(Bot $bot, Update $update): void
    {
        // ==================== Non-message update types ====================

        if ($update->isPreCheckoutQuery()) {
            $this->call($this->onPreCheckoutQueryHandler, $bot, $update);
            return;
        }

        if ($update->isSuccessfulPayment()) {
            $this->call($this->onSuccessfulPaymentHandler, $bot, $update);
            return;
        }

        if ($update->isShippingQuery()) {
            $this->call($this->onShippingQueryHandler, $bot, $update);
            return;
        }

        if ($update->isInlineQuery()) {
            $this->call($this->onInlineQueryHandler, $bot, $update);
            return;
        }

        if ($update->isPoll()) {
            $this->call($this->onPollHandler, $bot, $update);
            return;
        }

        if ($update->isPollAnswer()) {
            $this->call($this->onPollAnswerHandler, $bot, $update);
            return;
        }

        if ($update->isMyChatMember()) {
            $this->call($this->onMyChatMemberHandler, $bot, $update);
            return;
        }

        if ($update->isChatMember()) {
            $this->call($this->onChatMemberHandler, $bot, $update);
            return;
        }

        if ($update->isChatJoinRequest()) {
            $this->call($this->onChatJoinRequestHandler, $bot, $update);
            return;
        }

        if ($update->isChatBoost() || $update->isRemovedChatBoost()) {
            $this->call($this->onChatBoostHandler, $bot, $update);
            return;
        }

        if ($update->isMessageReaction() || $update->isMessageReactionCount()) {
            $this->call($this->onMessageReactionHandler, $bot, $update);
            return;
        }

        if ($update->isEditedMessage()) {
            $this->call($this->onEditedMessageHandler, $bot, $update);
            return;
        }

        if ($update->isChannelPost() || $update->isEditedChannelPost()) {
            $this->call($this->onChannelPostHandler, $bot, $update);
            return;
        }

        if ($update->isBusinessMessage() || $update->isEditedBusinessMessage()) {
            $this->call($this->onBusinessMessageHandler, $bot, $update);
            return;
        }

        // ==================== Callback Query ====================

        if ($update->isCallbackQuery()) {
            $this->dispatchCallback($bot, $update);
            return;
        }

        // ==================== Message ====================

        if (!$update->isMessage()) {
            return;
        }

        $text = $update->text();
        if ($text !== null && in_array($text, $this->bypassTexts, true)) {
            if (isset($this->textHandlers[$text])) {
                ($this->textHandlers[$text])($bot, $update);
            }
            return;
        }

        if (!$this->runMiddlewares($bot, $update)) {
            return;
        }

        if ($update->isCommand()) {
            [$command, $payload] = $update->commandParts();
            if (isset($this->commandHandlers[$command])) {
                ($this->commandHandlers[$command])($bot, $update, $payload);
            }
            return;
        }

        if ($update->isContact() && $this->onContactHandler !== null) {
            ($this->onContactHandler)($bot, $update);
            return;
        }

        if ($update->isLocation() && $this->onLocationHandler !== null) {
            ($this->onLocationHandler)($bot, $update);
            return;
        }

        if ($update->isPhoto() && $this->onPhotoHandler !== null) {
            ($this->onPhotoHandler)($bot, $update);
            return;
        }

        if ($update->isVideo() && $this->onVideoHandler !== null) {
            ($this->onVideoHandler)($bot, $update);
            return;
        }

        if ($update->isDocument() && $this->onDocumentHandler !== null) {
            ($this->onDocumentHandler)($bot, $update);
            return;
        }

        if ($update->isVoice() && $this->onVoiceHandler !== null) {
            ($this->onVoiceHandler)($bot, $update);
            return;
        }

        if ($update->isSticker() && $this->onStickerHandler !== null) {
            ($this->onStickerHandler)($bot, $update);
            return;
        }

        if ($text !== null && isset($this->textHandlers[$text])) {
            ($this->textHandlers[$text])($bot, $update);
            return;
        }

        if ($text !== null) {
            foreach ($this->regexHandlers as ['pattern' => $pattern, 'handler' => $handler]) {
                if (preg_match($pattern, $text, $matches)) {
                    $handler($bot, $update, $matches);
                    return;
                }
            }
        }

        $this->call($this->onMessageHandler, $bot, $update);
    }

    private function dispatchCallback(Bot $bot, Update $update): void
    {
        if (!$this->runMiddlewares($bot, $update)) {
            return;
        }

        $data = $update->callbackData();
        if ($data === null) {
            $bot->answerCallbackQuery($update->callbackQueryId() ?? '');
            return;
        }

        if (isset($this->callbackExact[$data])) {
            ($this->callbackExact[$data])($bot, $update);
            return;
        }

        foreach ($this->callbackPrefix as $prefix => $handler) {
            if (str_starts_with($data, $prefix)) {
                $param = substr($data, strlen($prefix));
                $handler($bot, $update, $param);
                return;
            }
        }

        if ($this->onCallbackQueryHandler !== null) {
            ($this->onCallbackQueryHandler)($bot, $update);
            return;
        }

        $bot->answerCallbackQuery($update->callbackQueryId() ?? '');
    }

    private function runMiddlewares(Bot $bot, Update $update): bool
    {
        if (empty($this->middlewares)) {
            return true;
        }

        $index   = 0;
        $middlewares = $this->middlewares;

        $next = function () use ($bot, $update, &$index, $middlewares, &$next): bool {
            if ($index >= count($middlewares)) {
                return true;
            }
            $middleware = $middlewares[$index++];
            return $middleware->handle($bot, $update, $next);
        };

        return $next();
    }

    private function call(?callable $handler, Bot $bot, Update $update): void
    {
        if ($handler !== null) {
            $handler($bot, $update);
        }
    }
}
