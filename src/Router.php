<?php

namespace Miraliog\Pelegram;

use Closure;
use ReflectionFunction;
use Miraliog\Pelegram\Types\Message;
use Miraliog\Pelegram\Types\CallbackQuery;
use Miraliog\Pelegram\Types\InlineQuery;
use Miraliog\Pelegram\Contracts\MiddlewareInterface;
use Miraliog\Pelegram\Exceptions\pelegramException;

class Router
{
    private array $commandHandlers          = [];
    private array $textHandlers             = [];
    private array $callbackExact            = [];
    private array $callbackPrefix           = [];
    private array $regexHandlers            = [];

    private ?Closure $onMessageHandler              = null;
    private ?Closure $onEditedMessageHandler        = null;
    private ?Closure $onChannelPostHandler          = null;
    private ?Closure $onCallbackQueryHandler        = null;
    private ?Closure $onInlineQueryHandler          = null;
    private ?Closure $onPreCheckoutQueryHandler     = null;
    private ?Closure $onSuccessfulPaymentHandler    = null;
    private ?Closure $onShippingQueryHandler        = null;
    private ?Closure $onPollHandler                 = null;
    private ?Closure $onPollAnswerHandler           = null;
    private ?Closure $onMyChatMemberHandler         = null;
    private ?Closure $onChatMemberHandler           = null;
    private ?Closure $onChatJoinRequestHandler      = null;
    private ?Closure $onChatBoostHandler            = null;
    private ?Closure $onMessageReactionHandler      = null;
    private ?Closure $onBusinessMessageHandler      = null;
    private ?Closure $onContactHandler              = null;
    private ?Closure $onLocationHandler             = null;
    private ?Closure $onPhotoHandler                = null;
    private ?Closure $onVideoHandler                = null;
    private ?Closure $onDocumentHandler             = null;
    private ?Closure $onVoiceHandler                = null;
    private ?Closure $onStickerHandler              = null;

    /** @var MiddlewareInterface[] */
    private array $middlewares  = [];
    private array $bypassTexts  = [];

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

    public function dispatch(Bot $bot, Update $update): void
    {
        $update->setBot($bot);

        try {
            $this->handle($bot, $update);
        } catch (pelegramException $e) {
            error_log("[pelegramException] {$e->getMessage()} (code: {$e->getErrorCode()})");
        } catch (\Throwable $e) {
            error_log("[Router] Unhandled exception: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}");
        }
    }

    private function handle(Bot $bot, Update $update): void
    {
        if ($update->isPreCheckoutQuery()) {
            $this->invoke($this->onPreCheckoutQueryHandler, $bot, $update);
            return;
        }
        if ($update->isSuccessfulPayment()) {
            $this->invoke($this->onSuccessfulPaymentHandler, $bot, $update);
            return;
        }
        if ($update->isShippingQuery()) {
            $this->invoke($this->onShippingQueryHandler, $bot, $update);
            return;
        }
        if ($update->isPoll()) {
            $this->invoke($this->onPollHandler, $bot, $update);
            return;
        }
        if ($update->isPollAnswer()) {
            $this->invoke($this->onPollAnswerHandler, $bot, $update);
            return;
        }
        if ($update->isMyChatMember()) {
            $this->invoke($this->onMyChatMemberHandler, $bot, $update);
            return;
        }
        if ($update->isChatMember()) {
            $this->invoke($this->onChatMemberHandler, $bot, $update);
            return;
        }
        if ($update->isChatJoinRequest()) {
            $this->invoke($this->onChatJoinRequestHandler, $bot, $update);
            return;
        }
        if ($update->isEditedMessage()) {
            $this->invoke($this->onEditedMessageHandler, $bot, $update);
            return;
        }

        if ($update->isChatBoost() || $update->isRemovedChatBoost()) {
            $this->invoke($this->onChatBoostHandler, $bot, $update);
            return;
        }
        if ($update->isMessageReaction() || $update->isMessageReactionCount()) {
            $this->invoke($this->onMessageReactionHandler, $bot, $update);
            return;
        }
        if ($update->isChannelPost() || $update->isEditedChannelPost()) {
            $this->invoke($this->onChannelPostHandler, $bot, $update);
            return;
        }
        if ($update->isBusinessMessage() || $update->isEditedBusinessMessage()) {
            $this->invoke($this->onBusinessMessageHandler, $bot, $update);
            return;
        }

        if ($update->isInlineQuery()) {
            $inlineQuery = new InlineQuery($update->raw['inline_query'], $bot);
            $this->invoke($this->onInlineQueryHandler, $bot, $update, $inlineQuery);
            return;
        }

        if ($update->isCallbackQuery()) {
            $this->dispatchCallback($bot, $update);
            return;
        }

        if (!$update->isMessage()) return;

        $message = new Message($update->raw['message'], $bot);
        $text    = $message->text();

        // bypass
        if ($text !== null && in_array($text, $this->bypassTexts, true)) {
            if (isset($this->textHandlers[$text])) {
                $this->invoke($this->textHandlers[$text], $bot, $update, $message);
            }
            return;
        }

        if (!$this->runMiddlewares($bot, $update)) return;

        // command
        if ($message->isCommand()) {
            [$command, $payload] = $message->commandParts();
            if (isset($this->commandHandlers[$command])) {
                $this->invoke($this->commandHandlers[$command], $bot, $update, $message, $payload);
                return;
            }
        }

        if ($message->isContact()  && $this->onContactHandler  !== null) {
            $this->invoke($this->onContactHandler,  $bot, $update, $message);
            return;
        }
        if ($message->isLocation() && $this->onLocationHandler !== null) {
            $this->invoke($this->onLocationHandler, $bot, $update, $message);
            return;
        }
        if ($message->isPhoto()    && $this->onPhotoHandler    !== null) {
            $this->invoke($this->onPhotoHandler,    $bot, $update, $message);
            return;
        }
        if ($message->isVideo()    && $this->onVideoHandler    !== null) {
            $this->invoke($this->onVideoHandler,    $bot, $update, $message);
            return;
        }
        if ($message->isDocument() && $this->onDocumentHandler !== null) {
            $this->invoke($this->onDocumentHandler, $bot, $update, $message);
            return;
        }
        if ($message->isVoice()    && $this->onVoiceHandler    !== null) {
            $this->invoke($this->onVoiceHandler,    $bot, $update, $message);
            return;
        }
        if ($message->isSticker()  && $this->onStickerHandler  !== null) {
            $this->invoke($this->onStickerHandler,  $bot, $update, $message);
            return;
        }

        if ($text !== null && isset($this->textHandlers[$text])) {
            $this->invoke($this->textHandlers[$text], $bot, $update, $message);
            return;
        }

        if ($text !== null) {
            foreach ($this->regexHandlers as ['pattern' => $pattern, 'handler' => $handler]) {
                if (preg_match($pattern, $text, $matches)) {
                    $this->invoke($handler, $bot, $update, $message, $matches);
                    return;
                }
            }
        }

        $this->invoke($this->onMessageHandler, $bot, $update, $message);
    }

    private function dispatchCallback(Bot $bot, Update $update): void
    {
        if (!$this->runMiddlewares($bot, $update)) return;

        $callback = new CallbackQuery($update->raw['callback_query'], $bot);
        $data     = $callback->data();

        if ($data === null) {
            $callback->answer();
            return;
        }

        if (isset($this->callbackExact[$data])) {
            $this->invoke($this->callbackExact[$data], $bot, $update, $callback);
            return;
        }

        foreach ($this->callbackPrefix as $prefix => $handler) {
            if (str_starts_with($data, $prefix)) {
                $this->invoke($handler, $bot, $update, $callback, substr($data, strlen($prefix)));
                return;
            }
        }

        if ($this->onCallbackQueryHandler !== null) {
            $this->invoke($this->onCallbackQueryHandler, $bot, $update, $callback);
            return;
        }

        $callback->answer();
    }

    private function invoke(
        ?callable $handler,
        Bot $bot,
        Update $update,
        Message|CallbackQuery|InlineQuery|null $typed = null,
        string|array|null $extra = null,
    ): void {
        if ($handler === null) return;

        try {
            $fn   = new ReflectionFunction(\Closure::fromCallable($handler));
            $args = [];

            foreach ($fn->getParameters() as $param) {
                $type   = $param->getType()?->getName();
                $args[] = match ($type) {
                    Message::class       => $typed instanceof Message       ? $typed : null,
                    CallbackQuery::class => $typed instanceof CallbackQuery ? $typed : null,
                    InlineQuery::class   => $typed instanceof InlineQuery   ? $typed : null,
                    Bot::class           => $bot,
                    Update::class        => $update,
                    'string'             => is_string($extra) ? $extra : null,
                    'array'              => is_array($extra)  ? $extra : null,
                    default              => null,
                };
            }

            $handler(...$args);
        } catch (\ReflectionException) {
            $handler($typed ?? $update);
        }
    }

    private function runMiddlewares(Bot $bot, Update $update): bool
    {
        if (empty($this->middlewares)) return true;

        $index       = 0;
        $middlewares = $this->middlewares;
        $next        = function () use ($bot, $update, &$index, $middlewares, &$next): bool {
            if ($index >= count($middlewares)) return true;
            return $middlewares[$index++]->handle($bot, $update, $next);
        };

        return $next();
    }
}
