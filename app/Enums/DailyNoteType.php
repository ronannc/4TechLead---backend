<?php

namespace App\Enums;

enum DailyNoteType: string
{
    case Impediment = 'impedimento';
    case Alignment = 'alinhamento';
    case Event = 'acontecimento';
}
