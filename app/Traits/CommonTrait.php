<?php
namespace App\Traits;

trait CommonTrait
{
    public static function generateId()
    {
        $lastRecord = static::query()->withTrashed()->orderByDesc('id')->first();
        if ($lastRecord) {
            $newId = $lastRecord->unique_id + 1;
        } else {
            $newId = config('constant.STATUSES_UNIQUE_ID');
        }
        return str_pad($newId, 5, '0', STR_PAD_LEFT);
    }
}
?>
