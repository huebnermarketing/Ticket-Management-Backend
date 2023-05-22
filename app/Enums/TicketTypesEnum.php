<?php
namespace App\Enums;

class TicketTypesEnum
{
    const ADHOC = 'adhoc';
    const CONTRACT = 'contract';

    public static function values()
    {
        return [
            self::ADHOC,
            self::CONTRACT,
        ];
    }
}
?>
