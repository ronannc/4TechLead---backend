<?php

namespace App\Enums;

enum SeniorityLevel: string
{
    case Intern = 'estagiario';
    case Junior = 'junior';
    case Mid = 'pleno';
    case Senior = 'senior';
    case Specialist = 'especialista';
}
