<?php

namespace Miraliog\Pelegram\Concerns\Accessors;

trait MediaAccessor
{
    public function photoMaxSizeFileId(): ?string
    {
        $photos = $this->raw['message']['photo'] ?? null;
        if (empty($photos)) return null;
        return end($photos)['file_id'] ?? null;
    }

    public function videoFileId(): ?string
    {
        return $this->raw['message']['video']['file_id'] ?? null;
    }
    public function documentFileId(): ?string
    {
        return $this->raw['message']['document']['file_id'] ?? null;
    }
    public function audioFileId(): ?string
    {
        return $this->raw['message']['audio']['file_id'] ?? null;
    }
    public function voiceFileId(): ?string
    {
        return $this->raw['message']['voice']['file_id'] ?? null;
    }
    public function stickerFileId(): ?string
    {
        return $this->raw['message']['sticker']['file_id'] ?? null;
    }
    public function animationFileId(): ?string
    {
        return $this->raw['message']['animation']['file_id'] ?? null;
    }
    public function videoNoteFileId(): ?string
    {
        return $this->raw['message']['video_note']['file_id'] ?? null;
    }
}
