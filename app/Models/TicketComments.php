<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketComments extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['id','user_id','ticket_id','comment','comment_date','created_at','updated_at','deleted_at'];
    protected $appends = ['comment_time'];

    public function getCommentTimeAttribute(){
        if(auth()->user()){
            return $this->created_at->setTimezone(auth()->user()->timezone)->diffForHumans();
        }
        return $this->created_at->diffForHumans();
    }
    public function users(){
        return $this->belongsTo(User::class, 'user_id','id');
    }

}
