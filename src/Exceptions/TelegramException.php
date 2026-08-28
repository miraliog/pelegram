<?php

namespace miraliog\pelegram\Exceptions;

class pelegramException extends \RuntimeException
{
    public function __construct(
        string $message,
        private readonly int $errorCode = 0,
        private readonly ?string $description = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $errorCode, $previous);
    }

    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
