<?php

namespace App\Entity\ActionLogEntry;

enum Type: string
{
    case EntryAdd = 'entry_create';
    case EntryLookUp = 'entry_look_up';
    case EntryRemove = 'entry_remove';
}
