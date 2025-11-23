<?php

namespace App\Enums;

enum LeadStatus: string
{
    case NEW = 'new';
    case FOLLOW_UP = 'follow_up';
    case SURVEY = 'survey';
    case CLOSED = 'closed';
    case LOST = 'lost';
}
