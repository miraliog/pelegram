<?php

namespace Miraliog\Pelegram\Enums;

enum ParseMode: string
{
    case MARKDOWN = 'MarkdownV2';
    case MARKDOWN_LEGACY = 'Markdown';
    case HTML = 'HTML';
}
