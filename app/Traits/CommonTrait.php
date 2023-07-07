<?php
namespace App\Traits;

trait CommonTrait
{
    public static function generateId($type = null,$isSoftDeleted = null)
    {
        if($isSoftDeleted == 1){
            $lastRecord = static::query()->withTrashed()->orderByDesc('id')->first();
        }else{
            $lastRecord = static::query()->orderByDesc('id')->first();
        }
        $length = 5;
        if ($lastRecord) {
            $newId = $lastRecord->unique_id + 1;
        } else {
            if($type == 'ticket'){
                $newId = config('constant.TICKET_UNIQUE_ID');
            }elseif ($type == 'contract'){
                $newId = config('constant.CONTRACT_UNIQUE_ID');
            }elseif ($type == 'invoice'){
                $newId = config('constant.INVOICE_ID');
                $length = 3;
            }else{
                $newId = config('constant.STATUSES_UNIQUE_ID');
            }
        }
        return str_pad($newId, $length, '0', STR_PAD_LEFT);
    }
}
?>
