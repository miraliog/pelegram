<?php

namespace Miraliog\Pelegram\Enums;

enum BackgroundFillType: string
{
    case SOLID = 'solid';
    case GRADIENT = 'gradient';
    case FREEFORM_GRADIENT = 'freeform_gradient';
}
