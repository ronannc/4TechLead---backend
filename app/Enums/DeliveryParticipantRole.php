<?php

namespace App\Enums;

enum DeliveryParticipantRole: string
{
    case Implementer = 'implementer';
    case CodeAuthor = 'code_author';
    case Reviewer = 'reviewer';
    case Qa = 'qa';
    case Actor = 'actor';
}
